<?php
require_once 'config/db.php';
include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password, $role])) {
                $success = "Registration successful! You can now <a href='login.php' style='color:var(--accent)'>Login</a>.";

                // If trainer, insert blank profile
                if ($role === 'trainer') {
                    $user_id = $pdo->lastInsertId();
                    $stmt_trainer = $pdo->prepare("INSERT INTO trainers (user_id) VALUES (?)");
                    $stmt_trainer->execute([$user_id]);
                }

            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Username or Email already exists.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<div class="auth-container">
    <h2 style="text-align: center; margin-bottom: 2rem;">Create Account</h2>

    <?php if ($error): ?>
        <div
            style="background: rgba(220, 38, 38, 0.2); border: 1px solid #dc2626; color: #fca5a5; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div
            style="background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #86efac; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>I am a:</label>
            <select name="role" class="form-control">
                <option value="user">Member (User)</option>
                <option value="trainer">Trainer</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
    </form>
    <p style="text-align: center; margin-top: 1rem; color: var(--text-muted);">
        Already have an account? <a href="login.php" style="color: var(--primary);">Login</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>