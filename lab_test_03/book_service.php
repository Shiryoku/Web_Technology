<?php
// book_service.php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: services.php");
    exit();
}

$service_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch Service Details
$stmt = $conn->prepare("SELECT * FROM services WHERE service_id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$service = $stmt->get_result()->fetch_assoc();

if (!$service) {
    die("Service not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['booking_date'];
    $time = $_POST['booking_time'];

    if (empty($date) || empty($time)) {
        $error = "Please select date and time.";
    } else {
        // Simple check if already booked (optional, but good)
        // For this lab, we might just insert.
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, service_id, booking_date, booking_time, status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("iiss", $user_id, $service_id, $date, $time);

        if ($stmt->execute()) {
            header("Location: my_bookings.php");
            exit();
        } else {
            $error = "Error creating booking: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service - DSBMS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h2>Confirm Booking</h2>
            <div style="margin: 1rem 0; padding: 1rem; background: #f3f4f6; border-radius: 4px;">
                <h3><?php echo htmlspecialchars($service['service_name']); ?></h3>
                <p>Price: RM <?php echo number_format($service['price'], 2); ?></p>
                <p>Duration: <?php echo $service['duration_minutes']; ?> mins</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Select Date</label>
                    <input type="date" name="booking_date" class="form-control" required
                        min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Select Time</label>
                    <input type="time" name="booking_time" class="form-control" required>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn" style="flex: 1;">Confirm Booking</button>
                    <a href="service_details.php?id=<?php echo $service_id; ?>" class="btn"
                        style="background-color: #9ca3af; text-align: center; flex: 1;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <?php $conn->close(); ?>
</body>

</html>