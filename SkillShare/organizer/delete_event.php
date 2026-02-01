<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'organizer') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $organizer_id = $_SESSION['user_id'];

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM events WHERE id = ? AND organizer_id = ?");
    $stmt->execute([$event_id, $organizer_id]);
    $event = $stmt->fetch();

    if ($event) {
        try {
            $pdo->beginTransaction();

            // Delete associated registrations (just to be safe, though ON DELETE CASCADE might handle it)
            $stmt_reg = $pdo->prepare("DELETE FROM registrations WHERE event_id = ?");
            $stmt_reg->execute([$event_id]);

            // Feedback table likely has foreign key constraint, but if not we should delete it too.
            // Assuming table schema has ON DELETE CASCADE from previous context, but executing just in case doesn't hurt if we catch errors.
            // Better to rely on foreign keys if set, but explicit delete is safer for application logic consistency if schema varies.
            // We will trust the main delete to trigger cascades or fail if constraints prevent it without cascade.

            // Delete the event
            $stmt_del = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $stmt_del->execute([$event_id]);

            $pdo->commit();

            // Redirect with success message
            header("Location: ../profile.php?view=my_events&msg=Workshop deleted successfully");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            // Redirect with error
            header("Location: edit_event.php?id=$event_id&error=Failed to delete workshop: " . urlencode($e->getMessage()));
            exit;
        }
    } else {
        header("Location: ../profile.php?view=my_events&error=Workshop not found or access denied");
        exit;
    }
} else {
    header("Location: ../profile.php?view=my_events");
    exit;
}
