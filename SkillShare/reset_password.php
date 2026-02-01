<?php
require_once 'config/db.php';
include 'includes/header.php';

$error = '';
$success = '';
$email = $_GET['email'] ?? '';

if (empty($email)) {
    header("Location: forgot_password.php");
    exit;
}

// Check if user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: forgot_password.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($password)) {
        $error = "Please enter a new password.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires_at = NULL WHERE id = ?");
        if ($update->execute([$password, $user['id']])) {
            $success = "Your password has been reset successfully! You can now login.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<div class="container" style="max-width: 400px; margin-top: 4rem;">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Set New Password</h2>
            <p class="text-center text-muted mb-4">Resetting password for:
                <strong><?php echo htmlspecialchars($email); ?></strong>
            </p>

            <?php if ($error): ?>
                <div
                    style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div
                    style="background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $success; ?> <br><a href="login.php" style="text-decoration: underline;">Login here</a>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>