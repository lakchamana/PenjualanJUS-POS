<?php
// api/admin_users.php
// CRUD sederhana untuk users (dipanggil oleh public/admin_users.php)
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db.php'; // pastikan file ini menginisialisasi $pdo
if (session_status() === PHP_SESSION_NONE) session_start();

// simple auth guard
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$meId = (int)($_SESSION['user_id']);

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

try {
    if (!isset($pdo) || !$pdo) respond(['success' => false, 'error' => 'Database connection missing'], 500);

    // detect columns we may need (password column name / updated_at)
    $hasPasswordHash = false;
    $hasPassword = false;
    $hasUpdated = false;
    try {
        $c = $pdo->query("SHOW COLUMNS FROM users LIKE 'password_hash'")->fetch();
        if ($c) $hasPasswordHash = true;
        $c2 = $pdo->query("SHOW COLUMNS FROM users LIKE 'password'")->fetch();
        if ($c2) $hasPassword = true;
        $c3 = $pdo->query("SHOW COLUMNS FROM users LIKE 'updated_at'")->fetch();
        if ($c3) $hasUpdated = true;
    } catch (Exception $e) {
        // ignore; we'll fallback to common columns
    }

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // GET?id= -> single user
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int)$_GET['id'];
            $select = 'id, username, name, role, created_at';
            if ($hasUpdated) $select .= ', updated_at';
            // optionally include email if exists
            $maybeEmail = $pdo->query("SHOW COLUMNS FROM users LIKE 'email'")->fetch() ? ', email' : '';
            $select .= $maybeEmail;
            $stmt = $pdo->prepare("SELECT {$select} FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) respond(['success' => false, 'error' => 'User not found'], 404);
            respond(['success' => true, 'data' => $row]);
        }

        // list w/ search + pagination
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 25;
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $offset = ($page - 1) * $per_page;

        $where = '';
        $params = [];
        if ($q !== '') {
            $where = "WHERE username LIKE :q OR name LIKE :q OR role LIKE :q";
            $params[':q'] = '%' . $q . '%';
        }

        $countSql = "SELECT COUNT(*) FROM users $where";
        $cnt = $pdo->prepare($countSql);
        $cnt->execute($params);
        $total_rows = (int)$cnt->fetchColumn();

        $select = 'id, username, name, role, created_at';
        if ($hasUpdated) $select .= ', updated_at';
        $sql = "SELECT {$select} FROM users $where ORDER BY id DESC LIMIT :off, :lim";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k=>$v) $stmt->bindValue($k, $v, PDO::PARAM_STR);
        $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':lim', (int)$per_page, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_pages = $per_page ? (int)ceil($total_rows / $per_page) : 1;
        respond(['success' => true, 'data' => ['rows' => $rows, 'pagination' => ['page'=>$page,'per_page'=>$per_page,'total_rows'=>$total_rows,'total_pages'=>$total_pages]]]);
    }

    // read JSON payload if any
    $body = file_get_contents('php://input');
    $input = json_decode($body, true);

    if ($method === 'POST') {
        // create user
        if (!is_array($input)) respond(['success' => false, 'error' => 'Invalid JSON'], 400);
        $username = trim($input['username'] ?? '');
        $name = trim($input['name'] ?? '');
        $role = trim($input['role'] ?? '');
        $password = $input['password'] ?? '';

        if ($username === '' || $name === '' || $role === '') {
            respond(['success'=>false,'error'=>'username, name and role are required'], 400);
        }
        if ($password === '') {
            respond(['success'=>false,'error'=>'password is required for new user'], 400);
        }

        // check unique username
        $chk = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :u");
        $chk->execute([':u' => $username]);
        if ($chk->fetchColumn() > 0) {
            respond(['success'=>false,'error'=>'Username already exists'], 400);
        }

        // hash password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // choose which column to store password
        if ($hasPasswordHash) {
            $stmt = $pdo->prepare("INSERT INTO users (username, name, role, password_hash, created_at) VALUES (:username, :name, :role, :ph, NOW())");
            $stmt->execute([':username'=>$username,':name'=>$name,':role'=>$role,':ph'=>$hash]);
        } elseif ($hasPassword) {
            $stmt = $pdo->prepare("INSERT INTO users (username, name, role, `password`, created_at) VALUES (:username, :name, :role, :pw, NOW())");
            $stmt->execute([':username'=>$username,':name'=>$name,':role'=>$role,':pw'=>$hash]);
        } else {
            // fallback: try insert without password column (unlikely)
            $stmt = $pdo->prepare("INSERT INTO users (username, name, role, created_at) VALUES (:username, :name, :role, NOW())");
            $stmt->execute([':username'=>$username,':name'=>$name,':role'=>$role]);
        }

        $id = $pdo->lastInsertId();
        $sel = $pdo->prepare("SELECT id, username, name, role, created_at FROM users WHERE id = :id LIMIT 1");
        $sel->execute([':id'=>$id]);
        $row = $sel->fetch(PDO::FETCH_ASSOC);
        respond(['success'=>true,'data'=>$row], 201);
    }

    if ($method === 'PUT') {
        // update
        if (!is_array($input) || empty($input['id'])) respond(['success'=>false,'error'=>'id required'],400);
        $id = (int)$input['id'];

        // prevent demoting/deleting currently logged user via update? allow name/role change but not self-delete later
        $fields = [];
        $params = [':id'=>$id];

        if (array_key_exists('username',$input)) {
            $username = trim($input['username']);
            if ($username === '') respond(['success'=>false,'error'=>'username cannot be empty'],400);
            // if username changed, ensure unique
            $chk = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :u AND id <> :id");
            $chk->execute([':u'=>$username,':id'=>$id]);
            if ($chk->fetchColumn() > 0) respond(['success'=>false,'error'=>'Username already taken'],400);
            $fields[] = "username = :username"; $params[':username'] = $username;
        }
        if (array_key_exists('name',$input)) { $fields[] = "name = :name"; $params[':name'] = trim($input['name']); }
        if (array_key_exists('role',$input)) { $fields[] = "role = :role"; $params[':role'] = trim($input['role']); }
        if (array_key_exists('password',$input) && ($input['password'] !== null) && $input['password'] !== '') {
            $hash = password_hash($input['password'], PASSWORD_DEFAULT);
            if ($hasPasswordHash) { $fields[] = "password_hash = :ph"; $params[':ph'] = $hash; }
            elseif ($hasPassword) { $fields[] = "`password` = :pw"; $params[':pw'] = $hash; }
            else { /* ignore */ }
        }

        if (count($fields) === 0) respond(['success'=>false,'error'=>'Nothing to update'],400);

        if ($hasUpdated) $fields[] = "updated_at = NOW()";

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $sel = $pdo->prepare("SELECT id, username, name, role, created_at" . ($hasUpdated ? ", updated_at" : "") . " FROM users WHERE id = :id LIMIT 1");
        $sel->execute([':id'=>$id]);
        $row = $sel->fetch(PDO::FETCH_ASSOC);
        respond(['success'=>true,'data'=>$row]);
    }

    if ($method === 'DELETE') {
        // payload JSON must include id
        if (!is_array($input) || empty($input['id']) || !is_numeric($input['id'])) respond(['success'=>false,'error'=>'id required'],400);
        $id = (int)$input['id'];

        // prevent deleting yourself
        if ($id === $meId) respond(['success'=>false,'error'=>"You cannot delete your own account"],400);

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id'=>$id]);
        respond(['success'=>true]);
    }

    respond(['success'=>false,'error'=>'Method not allowed'],405);

} catch (PDOException $ex) {
    respond(['success'=>false,'error'=>'Internal error','detail'=>$ex->getMessage()],500);
} catch (Exception $e) {
    respond(['success'=>false,'error'=>'Internal error','detail'=>$e->getMessage()],500);
}
