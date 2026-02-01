<?php
// my_bookings.php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = '';

// Handle Cancellation
if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];
    // Verify ownership and status
    $check = $conn->prepare("SELECT status FROM bookings WHERE booking_id = ? AND user_id = ?");
    $check->bind_param("ii", $cancel_id, $user_id);
    $check->execute();
    $res = $check->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if ($row['status'] == 'Pending') {
            $upd = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ?");
            $upd->bind_param("i", $cancel_id);
            $upd->execute();
            $msg = "<div class='alert alert-success'>Booking cancelled successfully.</div>";
        } else {
            $msg = "<div class='alert alert-error'>Cannot cancel this booking.</div>";
        }
    }
}

// Fetch Bookings
$sql = "SELECT b.*, s.service_name 
        FROM bookings b 
        JOIN services s ON b.service_id = s.service_id 
        WHERE b.user_id = ? 
        ORDER BY b.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - DSBMS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>My Bookings</h2>
        <?php echo $msg; ?>

        <div class="card" style="padding: 0; overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['booking_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                                <td><?php echo $row['booking_date']; ?></td>
                                <td><?php echo date('h:i A', strtotime($row['booking_time'])); ?></td>
                                <td>
                                    <?php
                                    $statusColor = 'black';
                                    if ($row['status'] == 'Pending')
                                        $statusColor = '#d97706'; // orange
                                    if ($row['status'] == 'Confirmed')
                                        $statusColor = '#2563eb'; // blue
                                    if ($row['status'] == 'Completed')
                                        $statusColor = '#16a34a'; // green
                                    if ($row['status'] == 'Cancelled')
                                        $statusColor = '#dc2626'; // red
                                    ?>
                                    <span style="font-weight: bold; color: <?php echo $statusColor; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'Pending'): ?>
                                        <a href="update_booking.php?id=<?php echo $row['booking_id']; ?>" class="btn"
                                            style="padding: 0.25rem 0.5rem; font-size: 0.8rem; background-color: #2563eb; margin-right: 0.5rem;">Update</a>
                                        <a href="my_bookings.php?cancel_id=<?php echo $row['booking_id']; ?>" class="btn btn-danger"
                                            style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"
                                            onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">Cancel</a>
                                    <?php elseif ($row['status'] == 'Completed'): ?>
                                        <!-- Check if reviewed? For simplicity, we just link to review page -->
                                        <a href="submit_review.php?booking_id=<?php echo $row['booking_id']; ?>" class="btn"
                                            style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Review</a>
                                    <?php else: ?>
                                        <span style="color: grey;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No bookings found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php $conn->close(); ?>
</body>

</html>