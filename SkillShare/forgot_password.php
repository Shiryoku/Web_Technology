<?php
require_once 'config/db.php';
include 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            header("Location: reset_password.php?email=" . urlencode($email));
            exit;
        } else {
            $error = "Email address not found.";
        }
    }
}
?>

<div class="container" style="max-width: 400px; margin-top: 4rem;">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Forgot Password</h2>
            <p class="text-center text-muted mb-4">Enter your email to reset your password.</p>

            <?php if ($error): ?>
                <div
                    style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your registered email"
                        required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Next</button>
            </form>

            <p class="text-center mt-4">
                <a href="login.php" style="color: var(--primary-color);">Back to Login</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>