<?php
include 'db.php';

$sql = "SELECT * FROM students";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1> Student Management List </h1>
        <table>
            <thead>
                <tr>
                    <td colspan="5" style="text-align: right; border: none; background: none;">
                        <a href="add.php" class="btn btn-primary">Add Student</a>
                    </td>
                </tr>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["course"] . "</td>";
                        echo "<td>
                                <a href='edit.php?id=" . $row["id"] . "' class='btn btn-warning'>Edit</a>
                                <a href='delete.php?id=" . $row["id"] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No students found</td></tr>";
                }
                ?>
            </tbody>
        </table>
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
<?php
$conn->close();
?>