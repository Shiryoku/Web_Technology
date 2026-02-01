<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Delete associated registrations and feedback first (or rely on foreign key cascade if set)
    // Assuming manual cleanup for safety or if cascade is not set
    $pdo->prepare("DELETE FROM registrations WHERE event_id = ?")->execute([$event_id]);
    $pdo->prepare("DELETE FROM event_feedback WHERE event_id = ?")->execute([$event_id]);

    // Delete event
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    if ($stmt->execute([$event_id])) {
        if (isset($pdo))
            $pdo = null;
        header("Location: events.php?msg=Workshop deleted successfully");
    } else {
        die("Failed to delete event.");
    }
}
?>