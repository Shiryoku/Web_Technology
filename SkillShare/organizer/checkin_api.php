<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$token = isset($input['token']) ? $input['token'] : '';

if (empty($token)) {
    echo json_encode(['success' => false, 'message' => 'Invalid token']);
    exit;
}

try {
    // Check if token exists and get details
    $stmt = $pdo->prepare("
        SELECT r.*, u.full_name, e.title as event_title, e.organizer_id 
        FROM registrations r 
        JOIN users u ON r.user_id = u.id 
        JOIN events e ON r.event_id = e.id 
        WHERE r.checkin_token = ?
    ");
    $stmt->execute([$token]);
    $registration = $stmt->fetch();

    if (!$registration) {
        echo json_encode(['success' => false, 'message' => 'Invalid QR Code']);
        exit;
    }

    // Verify organizer owns the event
    if ($registration['organizer_id'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'This ticket is for another event']);
        exit;
    }

    if ($registration['attendance_status'] == 'checked_in') {
        echo json_encode(['success' => false, 'message' => 'Student already checked in: ' . $registration['full_name']]);
        exit;
    }

    // Update status
    $updateStmt = $pdo->prepare("UPDATE registrations SET attendance_status = 'checked_in' WHERE id = ?");
    $updateStmt->execute([$registration['id']]);

    echo json_encode([
        'success' => true,
        'message' => 'Checked in: ' . $registration['full_name'] . ' for ' . $registration['event_title']
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>