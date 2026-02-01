<?php
// admin/manage_users.php
include 'header.php';
include '../config.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE user_id=$id");
}

$result = $conn->query("SELECT * FROM users");
?>

<h2>Manage Users</h2>
<div class="card mt-4" style="padding: 0;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td>
                        <a href="manage_users.php?delete=<?php echo $row['user_id']; ?>" class="btn btn-danger"
                            style="padding: 0.25rem 0.5rem;" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>
<?php $conn->close(); ?>
</body>

</html>