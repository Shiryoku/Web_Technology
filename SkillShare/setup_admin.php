<?php
require_once 'config/db.php';

$email = 'admin@skillshare.com';
$password = 'admin123';
$full_name = 'System Admin';
$role = 'admin';

// Check if admin already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {
    echo "Admin user already exists.";
} else {
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$full_name, $email, $password, $role])) {
        echo "Admin user created successfully.<br>";
        echo "Email: $email<br>";
        echo "Password: $password<br>";
        echo "Please delete this file after setup.";
    } else {
        echo "Error creating admin user.";
    }
}
?>