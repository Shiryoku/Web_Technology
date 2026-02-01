<?php
require_once 'config/db.php';
include 'includes/header.php';

// Fetch trainers with user info
$stmt = $pdo->prepare("
    SELECT u.username, t.specialization, t.bio, t.image_url 
    FROM trainers t 
    JOIN users u ON t.user_id = u.id
");
$stmt->execute();
$trainers = $stmt->fetchAll();
?>

<div class="container" style="padding: 4rem 2rem;">
    <h1 style="text-align: center; margin-bottom: 3rem; font-size: 3rem; color: var(--text-main);">Meet Our <span
            style="color: var(--accent);">Champions</span></h1>

    <div class="grid">
        <?php foreach ($trainers as $trainer): ?>
            <div class="card">
                <img src="<?php echo !empty($trainer['image_url']) ? htmlspecialchars($trainer['image_url']) : 'assets/images/default.jpg'; ?>"
                    alt="<?php echo htmlspecialchars($trainer['username']); ?>">
                <div class="card-content">
                    <div class="card-meta">
                        <span style="color: var(--primary); font-weight: bold;">
                            <?php echo htmlspecialchars($trainer['specialization']); ?>
                        </span>
                    </div>
                    <h3>
                        <?php echo htmlspecialchars($trainer['username']); ?>
                    </h3>
                    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                        <?php echo htmlspecialchars(substr($trainer['bio'], 0, 100)) . '...'; ?>
                    </p>
                    <a href="trainer_details.php?id=<?php echo $trainer['user_id']; // Using user_id for simplicity or join id ?>" class="btn btn-primary" style="width: 100%; text-align: center;">View Profile</a>
                    <!-- Note: Profile view implementation is out of scope for this simple listing, or can be added later -->
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>