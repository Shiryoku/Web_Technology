<?php
// Detect the request method
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo "<p><b>Form submitted using GET method.</b></p>";
    $email = $_GET['email'] ?? '';
    $password = $_GET['password'] ?? '';
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<p><b>Form submitted using POST method.</b></p>";
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
}

echo "<p>Email: " . htmlspecialchars($email) . "</p>";
echo "<p>Password: " . htmlspecialchars($password) . "</p>";
?>