<?php
// update_booking.php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: my_bookings.php");
    exit();
}

$booking_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$error = '';

// Fetch Booking Details & Verify Ownership/Status
$stmt = $conn->prepare("SELECT b.*, s.service_name, s.duration_minutes 
                        FROM bookings b 
                        JOIN services s ON b.service_id = s.service_id 
                        WHERE b.booking_id = ? AND b.user_id = ? AND b.status = 'Pending'");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Error</title><link rel='stylesheet' href='style.css'></head><body>";
    include 'navbar.php';
    echo "<div class='container mt-4'><div class='alert alert-error'>Booking not found or cannot be updated (only Pending bookings can be updated).</div><a href='my_bookings.php' class='btn'>Back to My Bookings</a></div></body></html>";
    exit();
}

$booking = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['booking_date'];
    $time = $_POST['booking_time'];

    if (empty($date) || empty($time)) {
        $error = "Please select date and time.";
    } else {
        $update_stmt = $conn->prepare("UPDATE bookings SET booking_date = ?, booking_time = ? WHERE booking_id = ?");
        $update_stmt->bind_param("ssi", $date, $time, $booking_id);

        if ($update_stmt->execute()) {
            header("Location: my_bookings.php");
            exit();
        } else {
            $error = "Error updating booking: " . $update_stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking - DSBMS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h2>Update Booking #<?php echo $booking_id; ?></h2>
            <div style="margin: 1rem 0; padding: 1rem; background: #f3f4f6; border-radius: 4px;">
                <h3><?php echo htmlspecialchars($booking['service_name']); ?></h3>
                <p>Duration: <?php echo $booking['duration_minutes']; ?> mins</p>
                <p>Current:
                    <?php echo $booking['booking_date'] . ' at ' . date('h:i A', strtotime($booking['booking_time'])); ?>
                </p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to update this booking?');">
                <div class="form-group">
                    <label>Select New Date</label>
                    <input type="date" name="booking_date" class="form-control" required
                        min="<?php echo date('Y-m-d'); ?>" value="<?php echo $booking['booking_date']; ?>">
                </div>
                <div class="form-group">
                    <label>Select New Time</label>
                    <input type="time" name="booking_time" class="form-control" required
                        value="<?php echo $booking['booking_time']; ?>">
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn" style="flex: 1;">Update Booking</button>
                    <a href="my_bookings.php" class="btn"
                        style="background-color: #9ca3af; text-align: center; flex: 1;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <?php $conn->close(); ?>
</body>

</html>