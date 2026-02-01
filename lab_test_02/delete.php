<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Use Prepared Statements for Security
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
        $stmt->close();
        $conn->close();
    }
} else {
    $conn->close();
    header("Location: index.php");
    exit();
}
?>