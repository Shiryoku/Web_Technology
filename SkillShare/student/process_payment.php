<?php
// Include database configuration
require_once '../config/db.php';
session_start();

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../events.php");
    if (isset($pdo))
        $pdo = null;
    exit;
}

// Authentication Check: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    if (isset($pdo))
        $pdo = null;
    exit;
}

$reg_id = $_POST['reg_id'];
$amount = $_POST['amount'];
$payment_method = $_POST['payment_method'];
$user_id = $_SESSION['user_id'];

// Verify Registration: Ensure the registration record belongs to the logged-in user
$stmt = $pdo->prepare("SELECT * FROM registrations WHERE id = ? AND user_id = ?");
$stmt->execute([$reg_id, $user_id]);
$registration = $stmt->fetch();

if (!$registration) {
    die("Invalid registration.");
}

try {
    // Start Database Transaction: Ensures atomicity (all or nothing)
    $pdo->beginTransaction();

    // 1. Update Registration Status and Generate Check-in Token
    // The checkin_token is used for QR code generation later
    $checkin_token = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare("UPDATE registrations SET payment_status = 'paid', checkin_token = ? WHERE id = ?");
    $stmt->execute([$checkin_token, $reg_id]);

    // 2. Record Payment Logic
    // Create a new record in the payments table
    $stmt = $pdo->prepare("INSERT INTO payments (registration_id, amount, payment_method, transaction_id) VALUES (?, ?, ?, ?)");
    $transaction_id = 'TXN-' . strtoupper(uniqid()); // Generate a unique transaction ID
    $stmt->execute([$reg_id, $amount, $payment_method, $transaction_id]);

    // Commit Transaction: Save all changes if everything succeeded
    $pdo->commit();

    // Notification Logic: Notify the organizer about the new paid registration
    require_once '../includes/notification_helper.php';

    // Fetch organizer ID and event title
    $stmt_org = $pdo->prepare("SELECT organizer_id, title FROM events WHERE id = ?");
    $stmt_org->execute([$registration['event_id']]);
    $event_data = $stmt_org->fetch();

    if ($event_data) {
        $message = "New registration for your workshop: " . $event_data['title'];
        createNotification($pdo, $event_data['organizer_id'], 'registration', $message, $registration['event_id']);
    }

    // Redirect to success page with transaction details
    header("Location: payment_success.php?tid=" . $transaction_id . "&amount=" . $amount);
    if (isset($pdo))
        $pdo = null;
    exit;

} catch (Exception $e) {
    // Rollback Transaction: Undo changes if an error occurred to maintain data integrity
    $pdo->rollBack();
    die("Payment failed: " . $e->getMessage());
}
?>