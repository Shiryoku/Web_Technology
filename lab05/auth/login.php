<?php
// Include global configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/cems/lab05/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Campus Event Management System (CEMS)</title>

  <!-- Styles -->
  <link rel="stylesheet" href="<?php echo BASE_PATH_CSS; ?>style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- ======= Hero Section ======= -->
  <header class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
      <img src="<?php echo BASE_PATH_IMG; ?>logo.jpg" alt="CEMS Logo" class="logo">
      <h1>Campus Event Management System</h1>
      <p>Organize, manage, and participate in campus events seamlessly</p>
    </div>
  </header>

  <!-- ======= Top Navigation ======= -->
  <?php include ROOT_PATH . '/include/topNav.php'; ?>  

  <main>
    <section class="section-content">	   			
      <h3>Login to Your Account</h3>
      <form action="<?php echo BASE_URL; ?>/auth/login_action.php" method="post">
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Login"><br><br>

        <p>
          <a href="<?php echo BASE_URL; ?>/auth/register.php">Don't have an account? Register here</a><br>
          <a href="<?php echo BASE_URL; ?>/auth/forgot_password.php">Forgot your password?</a>
        </p>
      </form>
    </section>
  </main>

  <footer>
    <hr>
    <p>&copy; Sam | BI12345678</p>
  </footer>
</body>
</html>
