<?php
// admin/manage_bookings.php
include 'header.php';
include '../config.php';

// Handle Status Update
if (isset($_GET['confirm'])) {
    $id = $_GET['confirm'];
    $conn->query("UPDATE bookings SET status='Confirmed' WHERE booking_id=$id");
}
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    $conn->query("UPDATE bookings SET status='Completed' WHERE booking_id=$id");
}

$sql = "SELECT b.*, u.full_name, s.service_name 
        FROM bookings b 
        JOIN users u ON b.user_id = u.user_id 
        JOIN services s ON b.service_id = s.service_id 
        ORDER BY b.created_at DESC";
$result = $conn->query($sql);
?>

<h2>Manage Bookings</h2>
<div class="card mt-4" style="padding: 0;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Service</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['booking_id']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['service_name']; ?></td>
                    <td><?php echo $row['booking_date']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <?php if ($row['status'] == 'Pending'): ?>
                            <a href="manage_bookings.php?confirm=<?php echo $row['booking_id']; ?>" class="btn">Confirm</a>
                        <?php elseif ($row['status'] == 'Confirmed'): ?>
                            <a href="manage_bookings.php?complete=<?php echo $row['booking_id']; ?>"
                                class="btn btn-success">Complete</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
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