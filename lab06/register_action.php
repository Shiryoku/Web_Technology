<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab06";

// Connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user input and sanitize output potentially (though inputs are bound)
$email = $_POST['email'];
$password = $_POST['password'];

// Secure Password Hashing
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepared Statement to prevent SQL Injection
$stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $hashed_password);

if ($stmt->execute()) {
    echo "New record created successfully<br>";
    // XSS Prevention for output
    echo "Registered email: " . htmlspecialchars($email) . "<br>";
    echo "<a href='login.html'>Go to Login</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>