<?php
// submit_review.php
include 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['booking_id'])) {
    header("Location: my_bookings.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = $_GET['booking_id'];
$error = '';

// Check if valid booking and completed
$check = $conn->prepare("SELECT service_id, status FROM bookings WHERE booking_id = ? AND user_id = ?");
$check->bind_param("ii", $booking_id, $user_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows == 0) {
    die("Invalid booking.");
}

$booking = $res->fetch_assoc();
if ($booking['status'] !== 'Completed') {
    die("Can only review completed bookings.");
}

$service_id = $booking['service_id'];

// Check if already reviewed
$rev_check = $conn->prepare("SELECT review_id FROM reviews WHERE booking_id = ?");
$rev_check->bind_param("i", $booking_id);
$rev_check->execute();
if ($rev_check->get_result()->num_rows > 0) {
    die("You have already reviewed this service.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        $error = "Rating must be between 1 and 5.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, service_id, booking_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiis", $user_id, $service_id, $booking_id, $rating, $comment);

        if ($stmt->execute()) {
            header("Location: service_details.php?id=" . $service_id);
            exit();
        } else {
            $error = "Error submitting review.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Review - DSBMS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="card" style="max-width: 500px; margin: 0 auto;">
            <h2>Write a Review</h2>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Rating</label>
                    <select name="rating" class="form-control" required>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Poor</option>
                        <option value="1">1 - Terrible</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea name="comment" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn">Submit Review</button>
            </form>
        </div>
    </div>
    <?php $conn->close(); ?>
</body>

</html>