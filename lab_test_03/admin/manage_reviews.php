<?php
// admin/manage_reviews.php
include 'header.php';
include '../config.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM reviews WHERE review_id=$id");
}

$sql = "SELECT r.*, u.full_name, s.service_name 
        FROM reviews r 
        JOIN users u ON r.user_id = u.user_id 
        JOIN services s ON r.service_id = s.service_id";
$result = $conn->query($sql);
?>

<h2>Manage Reviews</h2>
<div class="card mt-4" style="padding: 0;">
    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th>User</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['service_name']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['rating']; ?>/5</td>
                    <td><?php echo $row['comment']; ?></td>
                    <td>
                        <a href="manage_reviews.php?delete=<?php echo $row['review_id']; ?>" class="btn btn-danger"
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