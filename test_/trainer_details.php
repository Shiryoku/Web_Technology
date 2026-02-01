<?php
require_once 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: trainers.php");
    exit;
}

$user_id = $_GET['id'];

// Fetch trainer details
$stmt = $pdo->prepare("
    SELECT u.username, t.id as trainer_id, t.specialization, t.bio, t.image_url 
    FROM trainers t 
    JOIN users u ON t.user_id = u.id 
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$trainer = $stmt->fetch();

if (!$trainer) {
    echo "<div class='container' style='padding:4rem;'><h2 style='text-align:center;'>Trainer not found.</h2></div>";
    include 'includes/footer.php';
    exit;
}

// Fetch trainer's programmes
$stmt_prog = $pdo->prepare("SELECT * FROM programmes WHERE trainer_id = ?");
$stmt_prog->execute([$trainer['trainer_id']]);
$programmes = $stmt_prog->fetchAll();
?>

<div class="container" style="padding: 4rem 2rem;">
    <div
        style="background: var(--card-bg); border-radius: 16px; padding: 2rem; display: flex; flex-direction: column; gap: 2rem; border: 1px solid rgba(255,255,255,0.05);">
        <div style="display: flex; gap: 2rem; flex-wrap: wrap; align-items: center;">
            <img src="<?php echo htmlspecialchars($trainer['image_url']); ?>"
                alt="<?php echo htmlspecialchars($trainer['username']); ?>"
                style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 4px solid var(--accent);">
            <div>
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">
                    <?php echo htmlspecialchars($trainer['username']); ?>
                </h1>
                <p style="color: var(--primary); font-size: 1.2rem; margin-bottom: 1rem; font-weight: bold;">
                    <?php echo htmlspecialchars($trainer['specialization']); ?>
                </p>
                <div style="max-width: 600px; color: var(--text-muted);">
                    <?php echo nl2br(htmlspecialchars($trainer['bio'])); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Trainer's Programmes -->
    <h2 style="margin-top: 4rem; margin-bottom: 2rem; font-size: 2rem;">Programmes by
        <?php echo htmlspecialchars($trainer['username']); ?>
    </h2>

    <?php if (count($programmes) > 0): ?>
        <div class="grid">
            <?php foreach ($programmes as $prog): ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($prog['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($prog['title']); ?>">
                    <div class="card-content">
                        <h3>
                            <?php echo htmlspecialchars($prog['title']); ?>
                        </h3>
                        <p style="color: var(--text-muted); margin-bottom: 1rem;">
                            <?php echo htmlspecialchars($prog['difficulty']); ?> &bull;
                            <?php echo htmlspecialchars($prog['duration']); ?>
                        </p>
                        <a href="programme_details.php?id=<?php echo $prog['id']; ?>" class="btn btn-primary"
                            style="width: 100%; text-align: center;">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color: var(--text-muted);">No programmes listed yet.</p>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>