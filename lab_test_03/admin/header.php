<?php
// admin/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - DSBMS</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-nav {
            background-color: #111827;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-nav a {
            color: #e5e7eb;
            text-decoration: none;
            margin-left: 1.5rem;
            transition: color 0.3s;
        }

        .admin-nav a:hover {
            color: white;
        }

        .admin-nav .logo {
            font-size: 1.25rem;
            font-weight: bold;
            margin-left: 0;
            color: white;
        }
    </style>
</head>

<body>
    <nav class="admin-nav">
        <a href="dashboard.php" class="logo">DSBMS Admin</a>
        <div>
            <span style="color: #9ca3af; margin-right: 1rem; font-size: 0.9rem;">Welcome, Admin</span>
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_services.php">Services</a>
            <a href="manage_bookings.php">Bookings</a>
            <a href="manage_reviews.php">Reviews</a>
            <a href="manage_users.php">Users</a>
            <a href="logout.php" style="color: #f87171;">Logout</a>
        </div>
    </nav>
    <div class="container mt-4">