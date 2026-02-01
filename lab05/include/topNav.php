<?php include_once __DIR__ . '/../config.php'; ?>
<nav class="navbar">
    <div class="nav-container">
      <a href="#" class="brand">CEMS</a>
      <div class="menu-icon" id="menu-icon">
        <i class="fas fa-bars"></i>
      </div>
      <ul class="nav-links" id="nav-links">
        <li><a href="<?php echo BASE_URL; ?>/index.php" class="active">Home</a></li>
        <li><a href="<?php echo BASE_URL; ?>/auth/register.php">Register</a></li>
        <li><a href="<?php echo BASE_URL; ?>/auth/login.php">Login</a></li>
      </ul>
    </div>
  </nav>