<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Prevent deleting self
    if ($user_id == $_SESSION['user_id']) {
        die("Cannot delete yourself.");
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$user_id])) {
        if (isset($pdo))
            $pdo = null;
        header("Location: users.php?msg=User deleted successfully");
    } else {
        die("Failed to delete user.");
    }
}
?>