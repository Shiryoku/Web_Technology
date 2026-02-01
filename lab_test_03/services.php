<?php
// services.php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - DSBMS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1 class="text-center mb-4" style="margin-top: 2rem;">Our Services</h1>

        <div class="services-grid">
            <?php
            $sql = "SELECT * FROM services";
            $result = $conn->query($sql);

            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    ?>
                    <div class="service-card">
                        <div class="service-img"
                            style="background-image: url('uploads/<?php echo $row['image_path']; ?>'); background-size: cover; background-position: center;">
                            <?php if (empty($row['image_path'])): ?>
                                <div
                                    style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#9ca3af;">
                                    No Image</div>
                            <?php endif; ?>
                        </div>
                        <div class="service-content">
                            <h3><?php echo htmlspecialchars($row['service_name']); ?></h3>
                            <p class="service-price">RM <?php echo number_format($row['price'], 2); ?></p>
                            <p style="color: #4b5563; font-size: 0.9rem; margin-bottom: 1rem;">
                                Duration: <?php echo $row['duration_minutes']; ?> mins
                            </p>
                            <p style="margin-bottom: 1rem;">
                                <?php echo substr(htmlspecialchars($row['description']), 0, 100); ?>...
                            </p>
                            <a href="service_details.php?id=<?php echo $row['service_id']; ?>" class="btn"
                                style="width: 100%; text-align: center;">View Details</a>
                        </div>
                    </div>
                    <?php
                endwhile;
            else:
                echo "<p>No services found.</p>";
            endif;
            ?>
        </div>
    </div>
    <?php $conn->close(); ?>
</body>

</html>