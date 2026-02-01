<?php
session_start();
require_once 'config.php'; // Ensure this points to your Lab 05 config.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Prepare SQL to find the user by email
    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // 2. Verify Password (Simple comparison since Lab 04 didn't use hashing)
        // In a real app, use: if (password_verify($password, $user['password'])) {
        if ($password === $user['password']) {
            
            // 3. Login Success: Store data in Session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // 4. Redirect based on Role
            if ($user['role'] === 'Admin') {
                // Go to Admin Panel
                header("Location: admin/index.php"); 
            } else {
                // Go to Main Home Page
                header("Location: index.php");
            }
            exit();

        } else {
            // Password incorrect
            echo "<script>alert('Invalid Password!'); window.location.href='login.php';</script>";
        }
    } else {
        // User not found
        echo "<script>alert('User not found!'); window.location.href='login.php';</script>";
    }
    
    $stmt->close();
    $conn->close();
}
?>