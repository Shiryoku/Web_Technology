<?php
// specific logic to highlight the active menu item automatically
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">
    <div class="nav-container">
      <a href="index.php" class="brand">CEMS</a>
      
      <div class="menu-icon" id="menu-icon">
        <i class="fas fa-bars"></i> 
      </div>
      
      <ul class="nav-links" id="nav-links">
        <li>
            <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Home</a>
        </li>
        <li>
            <a href="register.php" class="<?= ($current_page == 'register.php') ? 'active' : '' ?>">Register</a>
        </li>
        <li>
            <a href="login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Login</a>
        </li>
      </ul>
    </div>
</nav>