<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'get-unread') {
    $notifications = getUnreadNotifications();
    echo json_encode([
        'success' => true,
        'count' => count($notifications),
        'notifications' => $notifications
    ]);
    exit;
}

if ($action === 'get-all') {
    $notifications = getAllNotifications();
    echo json_encode([
        'success' => true,
        'notifications' => $notifications
    ]);
    exit;
}


if ($action === 'mark-read') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $db = db();
        $db->update('notifications', ['is_read' => 1], 'id = ?', [$id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

if ($action === 'mark-all-read') {
    $user = getCurrentUser();
    $db = db();
    $db->update('notifications', ['is_read' => 1], '(user_id = ? OR role = ?)', [$user['id'], $user['role']]);
    echo json_encode(['success' => true]);
    exit;
}
