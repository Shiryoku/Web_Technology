<?php
// navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
    <a href="index.php" class="logo">DSBMS</a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="services.php">Services</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="my_bookings.php">My Bookings</a></li>
            <li style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="color: #6b7280; font-size: 0.9rem;">Welcome,
                    <b><?php echo htmlspecialchars($_SESSION['user_name']); ?></b></span>
            </li>
            <li><a href="logout.php" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Logout</a></li>
        <?php else: ?>
            <li><a href="admin/login.php" style="color: #4b5563;">Admin</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Sign Up</a></li>
        <?php endif; ?>
    </ul>
</nav>