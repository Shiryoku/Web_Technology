<?php
// admin/login.php
include '../config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter username and password.";
    } else {
        $stmt = $conn->prepare("SELECT admin_id, password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            if ($password === $admin['password']) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Admin not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - DSBMS</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body style="background-color: #1f2937; display: flex; align-items: center; justify-content: center; height: 100vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <h2 class="text-center mb-4">Admin Login</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Login</button>
        </form>
        <p class="text-center mt-4">
            <a href="register.php" style="font-size: 0.9rem;">Register New Admin</a> <br>
            <a href="../index.php" style="font-size: 0.9rem; color: #6b7280;">Back to Home</a>
        </p>
    </div>
    <?php $conn->close(); ?>
</body>

</html>