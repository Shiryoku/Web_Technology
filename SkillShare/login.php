<?php
require_once 'config/db.php';
include 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'organizer') {
                header("Location: index.php");
            } elseif ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            if (isset($pdo))
                $pdo = null;
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<div class="container" style="max-width: 400px; margin-top: 4rem;">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Welcome Back</h2>

            <?php if ($error): ?>
                <div
                    style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <p class="text-center mt-4 text-muted">
                Don't have an account? <a href="register.php" style="color: var(--primary-color);">Register</a><br>
                <a href="forgot_password.php" style="color: var(--primary-color); font-size: 0.9rem;">Forgot
                    Password?</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>