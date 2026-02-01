<?php
require_once 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: programmes.php");
    exit;
}

$prog_id = $_GET['id'];

// Fetch programme details with trainer info
$stmt = $pdo->prepare("
    SELECT p.*, u.username as trainer_name, u.id as trainer_user_id
    FROM programmes p 
    LEFT JOIN trainers t ON p.trainer_id = t.id 
    LEFT JOIN users u ON t.user_id = u.id
    WHERE p.id = ?
");
$stmt->execute([$prog_id]);
$programme = $stmt->fetch();

if (!$programme) {
    echo "<div class='container' style='padding:4rem;'><h2 style='text-align:center;'>Programme not found.</h2></div>";
    include 'includes/footer.php';
    exit;
}
?>

<div class="container" style="padding: 4rem 2rem;">
    <div
        style="background: var(--card-bg); border-radius: 16px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
        <img src="<?php echo htmlspecialchars($programme['image_url']); ?>"
            alt="<?php echo htmlspecialchars($programme['title']); ?>"
            style="width: 100%; height: 400px; object-fit: cover;">

        <div style="padding: 2rem;">
            <div
                style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; margin-bottom: 2rem;">
                <div>
                    <h1 style="font-size: 3rem; margin-bottom: 0.5rem;">
                        <?php echo htmlspecialchars($programme['title']); ?>
                    </h1>
                    <p style="font-size: 1.2rem; color: var(--text-muted);">
                        Train with <a href="trainer_details.php?id=<?php echo $programme['trainer_user_id']; ?>"
                            style="color: var(--primary); text-decoration: underline;">
                            <?php echo htmlspecialchars($programme['trainer_name']); ?>
                        </a>
                    </p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 2.5rem; font-weight: bold; color: var(--accent);">$
                        <?php echo htmlspecialchars($programme['price']); ?>
                    </div>
                    <span
                        style="background: rgba(34, 197, 94, 0.1); color: var(--accent); padding: 0.25rem 0.75rem; border-radius: 4px;">
                        <?php echo htmlspecialchars($programme['difficulty']); ?>
                    </span>
                    <span style="margin-left: 0.5rem; color: var(--text-muted);">
                        <?php echo htmlspecialchars($programme['duration']); ?>
                    </span>
                </div>
            </div>

            <div style="margin-bottom: 3rem; font-size: 1.1rem; line-height: 1.8; color: var(--text-muted);">
                <?php echo nl2br(htmlspecialchars($programme['description'])); ?>
            </div>

            <button class="btn btn-primary" style="font-size: 1.25rem; padding: 1rem 3rem;">Enroll Now</button>
            <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-muted);">* Enrolling links to payment
                gateway (Mockup)</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>