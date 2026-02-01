<?php
// Include database configuration
require_once 'config/db.php';
// Include header for layout
include 'includes/header.php';

$error = '';
$success = '';

// Handle Registration Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input to remove whitespace
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'user'; // Default to 'user' if not set

    // Validate Input: Check if fields are empty
    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check Duplicate Email: Prevent multiple accounts with the same email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            // Insert New User into Database
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$full_name, $email, $password, $role])) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<div class="container" style="max-width: 500px; margin-top: 4rem;">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Create an Account</h2>

            <?php if ($error): ?>
                <div
                    style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div
                    style="background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $success; ?> <a href="login.php" style="text-decoration: underline;">Login here</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">I want to...</label>
                    <select name="role" class="form-control">
                        <option value="user">Join Events</option>
                        <option value="organizer">Organize Events</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
            <p class="text-center mt-4 text-muted">
                Already have an account? <a href="login.php" style="color: var(--primary-color);">Login</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>