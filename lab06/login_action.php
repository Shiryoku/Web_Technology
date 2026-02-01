<?php
session_start(); // Start URL session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab06";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

// Prepared Statement for SQL Injection prevention
$stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Verify Hashed Password
    if (password_verify($password, $row['password'])) {
        // Prevent Session Hijacking
        session_regenerate_id(true);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['email'] = $row['email'];

        echo "<h2 style='color: green;'>Login Successful!</h1>";
        // XSS Prevention
        echo "<p>Welcome back, " . htmlspecialchars($row['email']) . "</p>";
    } else {
        echo "<h2 style='color: red;'>Login Failed!</h1>";
        echo "<p>Invalid email or password.</p>";
    }
} else {
    echo "<h2 style='color: red;'>Login Failed!</h1>";
    echo "<p>Invalid email or password.</p>";
}

$stmt->close();
$conn->close();
?>