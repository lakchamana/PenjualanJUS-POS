<?php
// api/rekap.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/db.php'; // pastikan path benar dan $pdo tersedia

// helper
function respond($ok, $data = null, $err = null) {
    $out = ['success' => $ok];
    if ($ok && $data !== null) $out['data'] = $data;
    if (!$ok && $err !== null) $out['error'] = $err;
    echo json_encode($out);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

// safe date inputs (YYYY-MM-DD)
$start = isset($_GET['start']) ? trim($_GET['start']) : (isset($_POST['start']) ? trim($_POST['start']) : null);
$end = isset($_GET['end']) ? trim($_GET['end']) : (isset($_POST['end']) ? trim($_POST['end']) : null);

// small validator: default to today range last 7 days if not provided
if (!$start || !$end) {
    $end = date('Y-m-d');
    $d = new DateTime();
    $d->modify('-6 days');
    $start = $d->format('Y-m-d');
}

try {
    switch ($action) {
        /* -------------------- list_cashiers -------------------- */
        case 'list_cashiers': {
            $stmt = $pdo->prepare("SELECT id, name FROM users ORDER BY name");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            respond(true, ['data' => $rows]);
        } break;

        /* -------------------- list_categories -------------------- */
        case 'list_categories': {
            $stmt = $pdo->prepare("SELECT id, name FROM categories ORDER BY name");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            respond(true, ['data' => $rows]);
        } break;

        /* -------------------- summary -------------------- */
        case 'summary': {
            $sql = "SELECT 
                      COALESCE(SUM(o.total),0) AS total_revenue,
                      COUNT(o.id) AS total_orders,
                      COALESCE(SUM( (SELECT COALESCE(SUM(qty),0) FROM order_items oi WHERE oi.order_id = o.id )),0) AS total_items,
                      COALESCE(AVG(o.total),0) AS avg_order
                    FROM orders o
                    WHERE o.status = 'FINISHED' 
                      AND DATE(o.created_at) BETWEEN :start AND :end";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':start' => $start, ':end' => $end]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            respond(true, $data);
        } break;

        /* -------------------- daily -------------------- */
        case 'daily': {
            $sql = "SELECT DATE(o.created_at) AS day, COALESCE(SUM(o.total),0) AS revenue, COUNT(o.id) AS orders_count
                    FROM orders o
                    WHERE o.status = 'FINISHED'
                      AND DATE(o.created_at) BETWEEN :start AND :end
                    GROUP BY DATE(o.created_at)
                    ORDER BY DATE(o.created_at) ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':start' => $start, ':end' => $end]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            respond(true, ['data' => $rows]);
        } break;

        /* -------------------- group (by category) -------------------- */
        case 'group': {
            $type = isset($_GET['type']) ? $_GET['type'] : (isset($_POST['type']) ? $_POST['type'] : 'category');
            if ($type === 'category') {
                $sql = "SELECT c.id as category_id, c.name as label, 
                           COALESCE(SUM(oi.qty * oi.price),0) AS revenue,
                           COALESCE(SUM(oi.qty),0) AS items
                        FROM order_items oi
                        JOIN orders o ON o.id = oi.order_id AND o.status='FINISHED' AND DATE(o.created_at) BETWEEN :start AND :end
                        JOIN menus m ON m.id = oi.menu_id
                        JOIN categories c ON c.id = m.category_id
                        GROUP BY c.id, c.name
                        ORDER BY revenue DESC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':start' => $start, ':end' => $end]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                respond(true, ['data' => $rows]);
            } else {
                respond(false, null, 'Unsupported group type');
            }
        } break;

        /* -------------------- methods -------------------- */
        case 'methods': {
            $sql = "SELECT p.method, COUNT(DISTINCT p.order_id) AS cnt, COALESCE(SUM(p.amount),0) AS total_amount
                    FROM payments p
                    JOIN orders o ON o.id = p.order_id
                    WHERE DATE(o.created_at) BETWEEN :start AND :end
                    GROUP BY p.method";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':start' => $start, ':end' => $end]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            respond(true, ['data' => $rows]);
        } break;

        /* -------------------- orders (paged + filters) -------------------- */
        case 'orders': {
            // basic filters
            $q = isset($_GET['q']) ? trim($_GET['q']) : (isset($_POST['q']) ? trim($_POST['q']) : '');
            $cashier_id = isset($_GET['cashier_id']) ? trim($_GET['cashier_id']) : (isset($_POST['cashier_id']) ? trim($_POST['cashier_id']) : '');
            $payment_method = isset($_GET['payment_method']) ? trim($_GET['payment_method']) : (isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '');
            $category_id = isset($_GET['category_id']) ? trim($_GET['category_id']) : (isset($_POST['category_id']) ? trim($_POST['category_id']) : '');

            $page = max(1, intval(isset($_GET['page']) ? $_GET['page'] : (isset($_POST['page']) ? $_POST['page'] : 1)));
            $per_page = max(1, intval(isset($_GET['per_page']) ? $_GET['per_page'] : (isset($_POST['per_page']) ? $_POST['per_page'] : 10)));
            $offset = ($page - 1) * $per_page;

            // build where + params
            $where = "o.status = 'FINISHED' AND DATE(o.created_at) BETWEEN :start AND :end";
            $params = [':start' => $start, ':end' => $end];

            if ($cashier_id !== '') {
                $where .= " AND o.user_id = :cashier_id";
                $params[':cashier_id'] = $cashier_id;
            }
            if ($payment_method !== '') {
                // require that this order has at least one payment of method = ?
                $where .= " AND EXISTS (SELECT 1 FROM payments p2 WHERE p2.order_id = o.id AND p2.method = :pm)";
                $params[':pm'] = $payment_method;
            }
            if ($category_id !== '') {
                // require that an item in this order belongs to menus.category_id = :category_id
                $where .= " AND EXISTS (SELECT 1 FROM order_items oi2 JOIN menus m2 ON m2.id = oi2.menu_id WHERE oi2.order_id = o.id AND m2.category_id = :catid)";
                $params[':catid'] = $category_id;
            }
            if ($q !== '') {
                // search in order_no (if exists), member name, cashier name, or order id
                $where .= " AND ( (o.order_no IS NOT NULL AND o.order_no LIKE :q) OR CAST(o.id AS CHAR) LIKE :q OR EXISTS (SELECT 1 FROM members mm WHERE mm.id = o.member_id AND mm.name LIKE :q) OR EXISTS (SELECT 1 FROM users uu WHERE uu.id = o.user_id AND uu.name LIKE :q) )";
                $params[':q'] = "%{$q}%";
            }

            // total rows (count distinct orders)
            $countSql = "SELECT COUNT(DISTINCT o.id) as total_rows FROM orders o WHERE {$where}";
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute($params);
            $total_rows = (int)$countStmt->fetchColumn();

            // pagination calc
            $total_pages = max(1, (int)ceil($total_rows / $per_page));

            // main selection: latest payment method using derived subquery
            $sql = "SELECT 
                        o.id AS order_id,
                        COALESCE(o.order_no, CONCAT('#', o.id)) AS order_no,
                        o.created_at,
                        u.name AS cashier,
                        m.name AS member_name,
                        o.visit_type,
                        o.total,
                        (SELECT COALESCE(SUM(qty),0) FROM order_items it WHERE it.order_id = o.id) AS items_count,
                        pm.method AS payment_method,
                        (CASE WHEN EXISTS(SELECT 1 FROM payments px WHERE px.order_id = o.id) THEN 1 ELSE 0 END) AS has_payment,
                        (SELECT CONCAT('receipts/receipt_', o.id, '.html')) AS receipt_url
                    FROM orders o
                    LEFT JOIN users u ON u.id = o.user_id
                    LEFT JOIN members m ON m.id = o.member_id
                    LEFT JOIN (
                        SELECT p1.order_id, p1.method
                        FROM payments p1
                        JOIN (SELECT order_id, MAX(id) AS maxid FROM payments GROUP BY order_id) lm ON lm.order_id = p1.order_id AND lm.maxid = p1.id
                    ) pm ON pm.order_id = o.id
                    WHERE {$where}
                    ORDER BY o.created_at DESC
                    LIMIT :offset, :per_page";
            // bind offset/per_page separately (must be integers)
            $stmt = $pdo->prepare($sql);
            // bind params
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v, PDO::PARAM_STR);
            }
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->bindValue(':per_page', (int)$per_page, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // respond
            $payload = [
                'rows' => $rows,
                'pagination' => [
                    'page' => $page,
                    'per_page' => $per_page,
                    'total_rows' => $total_rows,
                    'total_pages' => $total_pages
                ]
            ];
            respond(true, $payload);
        } break;

        default:
            respond(false, null, 'Unknown action');
    }
} catch (Exception $e) {
    // return error (avoid leaking sensitive details in production)
    http_response_code(500);
    respond(false, null, $e->getMessage());
}
