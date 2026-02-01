<?php
require_once 'config/db.php';
include 'includes/header.php';

// Fetch programmes with trainer name
$stmt = $pdo->prepare("
    SELECT p.*, u.username as trainer_name 
    FROM programmes p 
    LEFT JOIN trainers t ON p.trainer_id = t.id 
    LEFT JOIN users u ON t.user_id = u.id
");
$stmt->execute();
$programmes = $stmt->fetchAll();
?>

<div class="container" style="padding: 4rem 2rem;">
    <h1 style="text-align: center; margin-bottom: 3rem; font-size: 3rem; color: var(--text-main);">Elite <span
            style="color: var(--primary);">Programmes</span></h1>

    <div class="grid">
        <?php foreach ($programmes as $prog): ?>
            <div class="card">
                <img src="<?php echo !empty($prog['image_url']) ? htmlspecialchars($prog['image_url']) : 'assets/images/default.jpg'; ?>"
                    alt="<?php echo htmlspecialchars($prog['title']); ?>">
                <div class="card-content">
                    <div class="card-meta">
                        <span
                            style="background: rgba(34, 197, 94, 0.1); color: var(--accent); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">
                            <?php echo htmlspecialchars($prog['difficulty']); ?>
                        </span>
                        <span>
                            <?php echo htmlspecialchars($prog['duration']); ?>
                        </span>
                    </div>
                    <h3>
                        <?php echo htmlspecialchars($prog['title']); ?>
                    </h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">
                        Trainer: <span style="color: #fff;">
                            <?php echo htmlspecialchars($prog['trainer_name'] ?? 'TBD'); ?>
                        </span>
                    </p>
                    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                        <?php echo htmlspecialchars(substr($prog['description'], 0, 80)) . '...'; ?>
                    </p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.25rem; font-weight: bold; color: var(--text-main);">
                            $
                            <?php echo htmlspecialchars($prog['price']); ?>
                        </span>
                        <a href="programme_details.php?id=<?php echo $prog['id']; ?>" class="btn btn-accent">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>