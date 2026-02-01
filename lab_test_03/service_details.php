<?php
// service_details.php
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: services.php");
    exit();
}

$service_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM services WHERE service_id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Service not found.";
    exit();
}

$service = $result->fetch_assoc();

// Fetch Reviews
$review_sql = "SELECT r.*, u.full_name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.service_id = ? ORDER BY r.created_at DESC";
$review_stmt = $conn->prepare($review_sql);
$review_stmt->bind_param("i", $service_id);
$review_stmt->execute();
$reviews_result = $review_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($service['service_name']); ?> - DSBMS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="card">
            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px;">
                    <div
                        style="width: 100%; height: 300px; background-color: #e5e7eb; border-radius: 8px; background-image: url('uploads/<?php echo $service['image_path']; ?>'); background-size: cover; background-position: center;">
                    </div>
                </div>
                <div style="flex: 1; min-width: 300px;">
                    <h1 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($service['service_name']); ?></h1>
                    <p class="service-price" style="font-size: 1.5rem;">
                        RM <?php echo number_format($service['price'], 2); ?></p>
                    <p style="margin-bottom: 1rem; color: #6b7280;">Duration:
                        <?php echo $service['duration_minutes']; ?> minutes
                    </p>

                    <div style="margin-bottom: 2rem;">
                        <h3>Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                    </div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="book_service.php?id=<?php echo $service['service_id']; ?>" class="btn"
                            style="padding: 1rem 2rem; font-size: 1.1rem;">Book This Service</a>
                    <?php else: ?>
                        <div class="alert alert-error">Please <a href="login.php">login</a> to book this service.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Reviews & Ratings</h3>
            <hr style="margin: 1rem 0; border: 0; border-top: 1px solid #e5e7eb;">

            <?php if ($reviews_result->num_rows > 0): ?>
                <?php while ($review = $reviews_result->fetch_assoc()): ?>
                    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                            <span style="color: #f59e0b;">
                                <?php echo str_repeat("★", $review['rating']); ?>
                                <?php echo str_repeat("☆", 5 - $review['rating']); ?>
                            </span>
                        </div>
                        <p style="margin-top: 0.5rem;"><?php echo htmlspecialchars($review['comment']); ?></p>
                        <small style="color: #9ca3af;"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No reviews yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php $conn->close(); ?>
</body>

</html>