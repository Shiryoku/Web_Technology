<?php
require_once 'config/db.php';
include 'includes/header.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login Success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<div class="auth-container">
    <h2 style="text-align: center; margin-bottom: 2rem;">Welcome Back</h2>

    <?php if ($error): ?>
        <div
            style="background: rgba(220, 38, 38, 0.2); border: 1px solid #dc2626; color: #fca5a5; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
    </form>
    <p style="text-align: center; margin-top: 1rem; color: var(--text-muted);">
        Don't have an account? <a href="register.php" style="color: var(--primary);">Register</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>