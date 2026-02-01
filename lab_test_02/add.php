<?php
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);

    // Server-side Validation
    if (empty($name) || empty($email) || empty($course)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        // Use Prepared Statements for Security
        $stmt = $conn->prepare("INSERT INTO students (name, email, course) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $course);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Add Student</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email"
                value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

            <label for="course">Course:</label>
            <input type="text" id="course" name="course"
                value="<?php echo isset($course) ? htmlspecialchars($course) : ''; ?>" required>

            <button type="submit" class="btn btn-success">Add Student</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <footer id="myfooter">
        <hr>
        <p>&copy; <span id="y"></span> (WAN AHMAD NURULLAH | BI23110062 ) 2025 CEMS. All rights reserved.</p>
    </footer>
    <script>
        document.getElementById('y').textContent = new Date().getFullYear();
    </script>
</body>

</html>