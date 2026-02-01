<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>CEMS — Login</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <header class="hero">
        <div class="overlay"></div>
        <div class="hero-content">
            <img src="img/logo.jpg" alt="CEMS Logo" class="logo" />
            <h1>Campus Event Management System</h1>
            <p>Login to your account to start managing and participating in campus events.</p>
        </div>
    </header>

    <?php include 'include/topNav.php'; ?>

    <main>
        <section class="form-page">
            <h3>Login</h3>
            <form class="form-card" action="login_action.php" method="post">
                <div class="field">
                    <label for="email">Email</label><br>
                    <input id="email" name="email" type="email" placeholder="you@example.edu" required />
                </div>
                <div class="field">
                    <label for="password">Password</label><br>
                    <input id="password" name="password" type="password" placeholder="Your password" required />
                </div>
                <div class="actions">
                    <button type="submit">Login</button>
                    <button type="reset">Reset</button>
                </div>
                <p class="muted">
                    New here? <a href="register.php">Create an account</a> ·
                    <a href="forgot_password.html">Forgot password?</a>
                </p>
            </form>
        </section>
    </main>

<footer id="myfooter">
    <hr>
    <p>&copy;  <span id="y"></span>(WAN AHMAD NURULLAH | BI23110062 ) 2025 CEMS. All rights reserved.</p>
</footer>
<script> 
    document.getElementById('y').textContent = new Date().getFullYear();
    // Toggle mobile menu logic
    const menuIcon = document.getElementById('menu-icon');
    const navLinks = document.getElementById('nav-links');
    if(menuIcon) {
        menuIcon.onclick = () => navLinks.classList.toggle('active');
    }
</script>
</body>
</html>