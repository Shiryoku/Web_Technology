<?php
// admin/dashboard.php
include 'header.php';
include '../config.php';

$user_count = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$service_count = $conn->query("SELECT COUNT(*) as c FROM services")->fetch_assoc()['c'];
$booking_count = $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];
$review_count = $conn->query("SELECT COUNT(*) as c FROM reviews")->fetch_assoc()['c'];
?>

<h2>Dashboard</h2>
<div class="services-grid mt-4">
    <div class="card text-center" style="border-left: 5px solid #2563eb;">
        <h3>Services</h3>
        <p style="font-size: 2rem; font-weight: bold; color: #2563eb;"><?php echo $service_count; ?></p>
        <a href="manage_services.php">Manage</a>
    </div>
    <div class="card text-center" style="border-left: 5px solid #16a34a;">
        <h3>Bookings</h3>
        <p style="font-size: 2rem; font-weight: bold; color: #16a34a;"><?php echo $booking_count; ?></p>
        <a href="manage_bookings.php">Manage</a>
    </div>
    <div class="card text-center" style="border-left: 5px solid #f59e0b;">
        <h3>Users</h3>
        <p style="font-size: 2rem; font-weight: bold; color: #f59e0b;"><?php echo $user_count; ?></p>
        <a href="manage_users.php">Manage</a>
    </div>
    <div class="card text-center" style="border-left: 5px solid #dc2626;">
        <h3>Reviews</h3>
        <p style="font-size: 2rem; font-weight: bold; color: #dc2626;"><?php echo $review_count; ?></p>
        <a href="manage_reviews.php">Manage</a>
    </div>
</div>

</div>
<?php $conn->close(); ?>
</body>

</html>