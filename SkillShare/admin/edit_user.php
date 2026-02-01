<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = $_GET['id'];
$error = '';
$success = '';

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (empty($full_name) || empty($email)) {
        $error = "Name and Email are required.";
    } else {
        $stmt_update = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE id = ?");
        if ($stmt_update->execute([$full_name, $email, $role, $user_id])) {
            $success = "User updated successfully.";
            // Refresh user data
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        } else {
            $error = "Failed to update user.";
        }
    }
}
?>

<div class="container" style="margin-top: 2rem; max-width: 600px;">
    <div class="card">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2>Edit User</h2>
                <a href="users.php" class="btn btn-outline">Back</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"
                    style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"
                    style="background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control"
                        value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                        value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control">
                        <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>Student (User)
                        </option>
                        <option value="organizer" <?php echo $user['role'] == 'organizer' ? 'selected' : ''; ?>>Organizer
                        </option>
                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update User</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>