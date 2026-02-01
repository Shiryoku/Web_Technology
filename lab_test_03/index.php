<?php
// index.php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Digital Service Booking</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .hero {
            background-color: var(--primary-color);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: 0 0 50% 50% / 20px;
        }

        .hero h1 {
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }

        .hero p {
            margin-bottom: 2rem;
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .hero .btn {
            background-color: white;
            color: var(--primary-color);
            font-weight: bold;
        }

        .hero .btn:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <header class="hero">
        <div class="container">
            <h1>Welcome to DSBMS</h1>
            <p>Your one-stop solution for booking premium digital and home services.</p>
            <a href="services.php" class="btn">Browse Services</a>
        </div>
    </header>

    <div class="container mt-4">
        <h2 class="text-center mb-4">Why Choose Us?</h2>
        <div class="services-grid">
            <div class="card text-center">
                <h3>Trusted Professionals</h3>
                <p>All our service providers are verified and expert in their fields.</p>
            </div>
            <div class="card text-center">
                <h3>Easy Booking</h3>
                <p>Book your desired service in just a few clicks.</p>
            </div>
            <div class="card text-center">
                <h3>Secure Payment</h3>
                <p>Pay securely after service completion or online.</p>
            </div>
        </div>

        <h2 class="text-center mt-4 mb-4">Popular Services</h2>
        <div class="services-grid">
            <?php
            // Fetch 3 random services for display
            $sql = "SELECT * FROM services LIMIT 3";
            $result = $conn->query($sql);

            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    ?>
                    <div class="service-card">
                        <!-- Placeholder image if no image path, or use image path -->
                        <div class="service-img"
                            style="background-image: url('uploads/<?php echo $row['image_path']; ?>'); background-size: cover; background-position: center;">
                        </div>
                        <div class="service-content">
                            <h3><?php echo htmlspecialchars($row['service_name']); ?></h3>
                            <p class="service-price">RM <?php echo number_format($row['price'], 2); ?></p>
                            <p><?php echo substr(htmlspecialchars($row['description']), 0, 80); ?>...</p>
                            <a href="service_details.php?id=<?php echo $row['service_id']; ?>" class="btn mt-4">View Details</a>
                        </div>
                    </div>
                    <?php
                endwhile;
            else:
                echo "<p>No services available properly.</p>";
            endif;
            ?>
        </div>
        <div class="text-center mt-4">
            <a href="services.php" class="btn">View All Services</a>
        </div>
    </div>

    <footer style="background: #1f2937; color: white; padding: 2rem; text-align: center; margin-top: 4rem;">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Digital Service Booking & Management System. All rights reserved.</p>
        </div>
    </footer>
    <?php if (isset($conn))
        $conn->close(); ?>
</body>

</html>