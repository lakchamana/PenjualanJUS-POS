<?php
// api/admin_members.php
// JSON API untuk CRUD members (GET list/single, POST create, PUT update, DELETE delete)
// Catatan: simpan file ini di folder api/ dan pastikan includes/db.php menginisialisasi $pdo (PDO).

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/db.php'; // pastikan file ini menyediakan $pdo

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// auth minimal (sesuaikan jika perlu: hanya ADMIN boleh akses)
if (empty($_SESSION['user_id'])) {
    respond(['success' => false, 'error' => 'Unauthorized'], 401);
}

if (!isset($pdo) || !$pdo) {
    respond(['success' => false, 'error' => 'Database connection missing'], 500);
}

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // helper: cek kolom (agar kompatibel bila tabel tidak punya updated_at)
    function hasColumn($pdo, $table, $col) {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE :col");
        $stmt->execute([':col' => $col]);
        return (bool) $stmt->fetch();
    }
    $hasUpdatedAt = hasColumn($pdo, 'members', 'updated_at');

    if ($method === 'GET') {
        // ambil single record jika id diberikan
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int) $_GET['id'];
            $cols = 'id, code, name, phone, created_at' . ($hasUpdatedAt ? ', updated_at' : '');
            $stmt = $pdo->prepare("SELECT $cols FROM members WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return respond(['success' => false, 'error' => 'Not found'], 404);
            return respond(['success' => true, 'data' => $row]);
        }

        // list + pagination + search
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 25;
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $offset = ($page - 1) * $per_page;

        $where = '';
        $params = [];
        if ($q !== '') {
            // gunakan named parameter :q untuk tiga kolom
            $where = "WHERE code LIKE :q OR name LIKE :q OR phone LIKE :q";
            $params[':q'] = '%' . $q . '%';
        }

        // count total rows (safe karena $where hanya mengandung placeholders)
        $countSql = "SELECT COUNT(*) as cnt FROM members $where";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total_rows = (int) $countStmt->fetchColumn();

        // SELECT rows — PERBAIKAN: jangan bind LIMIT; cast ke int dan interpolasi
        $cols = 'id, code, name, phone, created_at' . ($hasUpdatedAt ? ', updated_at' : '');
        // sanitize integers (casting memastikan aman)
        $limitClause = "LIMIT " . intval($offset) . ", " . intval($per_page);
        $sql = "SELECT $cols FROM members $where ORDER BY id DESC $limitClause";

        $stmt = $pdo->prepare($sql);
        // hanya bind :q jika ada
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pagination = [
            'page' => $page,
            'per_page' => $per_page,
            'total_rows' => $total_rows,
            'total_pages' => $per_page ? (int) ceil($total_rows / $per_page) : 1
        ];

        respond(['success' => true, 'data' => ['rows' => $rows, 'pagination' => $pagination]]);
    }

    // baca JSON body untuk POST/PUT/DELETE
    $input = json_decode(file_get_contents('php://input'), true);

    if ($method === 'POST') {
        $code = isset($input['code']) ? trim($input['code']) : '';
        $name = isset($input['name']) ? trim($input['name']) : '';
        $phone = isset($input['phone']) ? trim($input['phone']) : '';

        if ($name === '') respond(['success' => false, 'error' => 'Field name required'], 400);

        $stmt = $pdo->prepare("INSERT INTO members (code, name, phone, created_at) VALUES (:code, :name, :phone, NOW())");
        $stmt->execute([
            ':code' => $code !== '' ? $code : null,
            ':name' => $name,
            ':phone' => $phone !== '' ? $phone : null
        ]);
        $id = $pdo->lastInsertId();

        $cols = 'id, code, name, phone, created_at' . ($hasUpdatedAt ? ', updated_at' : '');
        $r = $pdo->prepare("SELECT $cols FROM members WHERE id = :id LIMIT 1");
        $r->execute([':id' => $id]);
        $row = $r->fetch(PDO::FETCH_ASSOC);
        respond(['success' => true, 'data' => $row], 201);
    }

    if ($method === 'PUT') {
        if (!isset($input['id']) || !is_numeric($input['id'])) respond(['success' => false, 'error' => 'id wajib untuk update'], 400);
        $id = (int)$input['id'];
        $fields = [];
        $params = [':id' => $id];
        if (isset($input['code'])) { $fields[] = "code = :code"; $params[':code'] = trim($input['code']); }
        if (isset($input['name'])) { $fields[] = "name = :name"; $params[':name'] = trim($input['name']); }
        if (isset($input['phone'])) { $fields[] = "phone = :phone"; $params[':phone'] = trim($input['phone']); }
        if (count($fields) === 0) respond(['success' => false, 'error' => 'Tidak ada field untuk diupdate'], 400);

        $sql = "UPDATE members SET " . implode(', ', $fields) . ($hasUpdatedAt ? ", updated_at = NOW()" : "") . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $cols = 'id, code, name, phone, created_at' . ($hasUpdatedAt ? ', updated_at' : '');
        $r = $pdo->prepare("SELECT $cols FROM members WHERE id = :id LIMIT 1");
        $r->execute([':id' => $id]);
        $row = $r->fetch(PDO::FETCH_ASSOC);
        respond(['success' => true, 'data' => $row]);
    }

    if ($method === 'DELETE') {
        if (!isset($input['id']) || !is_numeric($input['id'])) respond(['success' => false, 'error' => 'id wajib untuk hapus'], 400);
        $id = (int)$input['id'];
        $stmt = $pdo->prepare("DELETE FROM members WHERE id = :id");
        $stmt->execute([':id' => $id]);
        respond(['success' => true]);
    }

    respond(['success' => false, 'error' => 'Method not allowed'], 405);

} catch (PDOException $ex) {
    respond(['success' => false, 'error' => 'Internal error', 'detail' => $ex->getMessage()], 500);
} catch (Exception $ex) {
    respond(['success' => false, 'error' => 'Internal error', 'detail' => $ex->getMessage()], 500);
}
