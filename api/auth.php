<?php
// api/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/db.php'; // sesuaikan jika path berbeda
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? null;

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = json_decode(file_get_contents('php://input'), true);
    if (!($in['email'] ?? false) || !($in['password'] ?? false)) {
        http_response_code(400);
        echo json_encode(['error' => 'missing_fields']);
        exit;
    }
    $email = filter_var($in['email'], FILTER_VALIDATE_EMAIL);
    $username = $in['username'] ?? ($email ? strtok($email, '@') : null);
    $name = $in['name'] ?? null;
    $password = $in['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, username, password_hash, name, role, created_at) VALUES (?, ?, ?, ?, 'CASHIER', NOW())");
        $stmt->execute([$email, $username, $hash, $name]);
        echo json_encode(['success' => true, 'user_id' => $pdo->lastInsertId()]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'register_failed', 'detail' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = json_decode(file_get_contents('php://input'), true);
    if (!($in['email'] ?? false) || !($in['password'] ?? false)) {
        http_response_code(400);
        echo json_encode(['error' => 'missing_fields']);
        exit;
    }

    // find by email or username
    $stmt = $pdo->prepare("SELECT id, email, password_hash, name, role, username FROM users WHERE email = ? OR username = ? LIMIT 1");
    $stmt->execute([$in['email'], $in['email']]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($u && password_verify($in['password'], $u['password_hash'])) {
        // regenerate session id
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['user_role'] = isset($u['role']) ? strtoupper($u['role']) : null;
        $_SESSION['user_name'] = $u['name'] ?? $u['username'] ?? '';

        // decide redirect:
        // ADMIN -> admin_dashboard.php
        // LEADER -> index.php (rekap accessible there)
        // others -> index.php
        $role = $_SESSION['user_role'] ?? '';
        if ($role === 'ADMIN') {
            $redirect = 'admin_dashboard.php';
        } else {
            // LEADER or any other role goes to main index (with rekap for leader)
            $redirect = 'index.php';
        }

        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $u['id'],
                'email' => $u['email'],
                'role' => $u['role'],
                'name' => $u['name']
            ],
            'redirect' => $redirect
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'invalid_credentials']);
    }
    exit;
}

// logout handler
if ($action === 'logout') {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    $isAjax = (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    );
    $acceptsJson = (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

    if ($isAjax || $acceptsJson) {
        echo json_encode(['success' => true]);
        exit;
    }

    header('Location: ../public/login.php');
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'invalid_action']);
