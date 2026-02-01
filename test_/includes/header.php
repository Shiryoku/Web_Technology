<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IronForge Gym</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <header>
        <div class="container">
            <nav>
                <a href="index.php" class="logo">IronForge</a>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="programmes.php">Programmes</a></li>
                    <li><a href="trainers.php">Trainers</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="#" class="btn btn-primary" style="padding: 0.5rem 1rem;">Profile</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php" class="btn btn-accent" style="padding: 0.5rem 1rem;">Join Now</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>