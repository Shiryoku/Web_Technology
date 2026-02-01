<?php
include 'db.php';

$error = "";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        header("Location: index.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
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
        $stmt = $conn->prepare("UPDATE students SET name=?, email=?, course=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $course, $id);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php");
            exit();
        } else {
            $error = "Error updating record: " . $stmt->error;
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
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Edit Student</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo isset($row['id']) ? $row['id'] : $_POST['id']; ?>">

            <label for="name">Name:</label>
            <input type="text" id="name" name="name"
                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : (isset($row['name']) ? $row['name'] : ''); ?>"
                required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email"
                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($row['email']) ? $row['email'] : ''); ?>"
                required>

            <label for="course">Course:</label>
            <input type="text" id="course" name="course"
                value="<?php echo isset($_POST['course']) ? htmlspecialchars($_POST['course']) : (isset($row['course']) ? $row['course'] : ''); ?>"
                required>

            <button type="submit" class="btn btn-warning"
                onclick="return confirm('Are you sure you want to update this student?');">Update Student</button>
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