<?php 
session_start();
// 1. Include the database configuration from Lab 05
require_once 'config.php'; 

// 2. Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get data from form
    $category = $_POST['category'] ?? 'Public'; 
    $name = trim($_POST['name']); 
    $email = trim($_POST['email']); 
    $phone = trim($_POST['phone']); 
    $password = $_POST['password']; 
    // Events are not in the users table, so we handle them differently or ignore for now
    
    // 3. Simple Validation
    if (empty($name) || empty($email) || empty($password)) {
        die("Error: Name, Email, and Password are required.");
    }

    // 4. Check if email already exists
    $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='register.php';</script>";
        exit();
    }
    $checkStmt->close();

    // 5. Insert into Database
    // Note: In a real app, use password_hash($password, PASSWORD_DEFAULT)
    $sql = "INSERT INTO users (category, name, email, phone, password, role) VALUES (?, ?, ?, ?, ?, 'User')";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sssss", $category, $name, $email, $phone, $password);
        
        if ($stmt->execute()) {
            // Success!
            echo "<script>alert('Registration Successful! Please Login.'); window.location.href='login.php';</script>";
        } else {
            // Database Error
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Prepare failed: " . $conn->error;
    }
    
    $conn->close();
}
?>