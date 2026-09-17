<?php
// เปิดสิทธิ์ CORS ให้เว็บฝั่งผู้ใช้สามารถส่งและดึงข้อมูลข้ามมาได้
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/mailer.php';

$pdo = new PDO("mysql:host=localhost;dbname=smart_helpdesk;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$action = $_GET['action'] ?? '';

// 1. ดึง Master Data (ตึกและหมวดหมู่)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'meta') {
    $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
    $locs = $pdo->query("SELECT * FROM locations")->fetchAll();
    echo json_encode(['categories' => $cats, 'locations' => $locs]);
    exit;
}

// 2. ดึงรายการแจ้งซ่อมทั้งหมดสำหรับหน้าช่าง
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'tickets') {
    $sql = "SELECT t.*, c.name as category_name, CONCAT(l.building, ' - ', l.room) as location_name, u.name as user_name, u.email as user_email
            FROM tickets t
            JOIN categories c ON t.category_id = c.id
            JOIN locations l ON t.location_id = l.id
            JOIN users u ON t.user_id = u.id
            ORDER BY t.id DESC";
    echo json_encode($pdo->query($sql)->fetchAll());
    exit;
}

// 3. รับแจ้งซ่อมใหม่จากหน้าผู้ใช้ (User) + ส่งอีเมล
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $data = json_decode(file_get_contents('php://input'), true);

    $userName = !empty($data['user_name']) ? trim($data['user_name']) : 'ผู้แจ้งทั่วไป';
    $userEmail = !empty($data['user_email']) ? trim($data['user_email']) : '';

    $u_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $u_stmt->execute([$userEmail]);
    $userId = $u_stmt->fetchColumn();

    if (!$userId && !empty($userEmail)) {
        $ins_u = $pdo->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, 'User')");
        $ins_u->execute([$userName, $userEmail]);
        $userId = $pdo->lastInsertId();
    } elseif (!$userId) {
        $userId = 1;
    }

    $stmt = $pdo->prepare("INSERT INTO tickets (user_id, category_id, location_id, title, description, priority) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $userId,
        $data['category_id'],
        $data['location_id'],
        $data['title'],
        $data['description'],
        $data['priority']
    ]);
    $ticket_id = $pdo->lastInsertId();

    $loc_stmt = $pdo->prepare("SELECT CONCAT(building, ' - ', room) FROM locations WHERE id = ?");
    $loc_stmt->execute([$data['location_id']]);
    $location_name = $loc_stmt->fetchColumn() ?: 'ไม่ระบุสถานที่';

    $cat_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $cat_stmt->execute([$data['category_id']]);
    $category_name = $cat_stmt->fetchColumn() ?: 'ทั่วไป';

    $mail_body = build_new_ticket_email(
        $ticket_id,
        $data['title'],
        $category_name,
        $location_name,
        $data['priority'],
        $data['description']
    );

    if (!empty($userEmail)) {
        send_email_notification(
            $userEmail,
            $userName,
            "[เคสใหม่ #{$ticket_id}] {$data['title']}",
            $mail_body
        );
    }

    echo json_encode(['success' => true, 'id' => $ticket_id]);
    exit;
}

// 4. ช่างอัปเดตสถานะงาน (รับงาน / ปิดงาน)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_status') {
    $data = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
    $stmt->execute([$data['status'], $data['id']]);

    if ($data['status'] === 'Closed') {
        $u_stmt = $pdo->prepare("SELECT u.email, u.name, t.title FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
        $u_stmt->execute([$data['id']]);
        $ticket_info = $u_stmt->fetch();

        if ($ticket_info && !empty($ticket_info['email'])) {
            $mail_body = build_closed_ticket_email($data['id'], $ticket_info['title']);
            send_email_notification(
                $ticket_info['email'],
                $ticket_info['name'],
                "งานแจ้งซ่อม #{$data['id']} เสร็จสิ้นแล้ว",
                $mail_body
            );
        }
    }

    echo json_encode(['success' => true]);
    exit;
}