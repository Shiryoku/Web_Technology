<?php
require_once 'config/db.php';
include 'includes/header.php';
require_once 'includes/notification_helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Mark all as read if requested
if (isset($_GET['mark_all_read'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$user_id]);
    header("Location: notifications.php");
    exit;
}

$notifications = getAllNotifications($pdo, $user_id, 50);
?>

<div class="container" style="margin-top: 2rem; max-width: 800px;">
    <div class="flex justify-between items-center mb-4">
        <h1>Notifications</h1>
        <?php if (count($notifications) > 0): ?>
            <a href="notifications.php?mark_all_read=1" class="btn btn-outline btn-sm">Mark all as read</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0;">
            <?php if (count($notifications) > 0): ?>
                <div class="list-group">
                    <?php foreach ($notifications as $notif): ?>
                        <div
                            style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: <?php echo $notif['is_read'] ? 'white' : '#f0f9ff'; ?>;">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p style="margin: 0; font-weight: <?php echo $notif['is_read'] ? 'normal' : '600'; ?>;">
                                        <?php echo htmlspecialchars($notif['message']); ?>
                                    </p>
                                    <small class="text-muted">
                                        <?php echo date('M d, Y H:i', strtotime($notif['created_at'])); ?>
                                    </small>
                                </div>
                                <?php if (!$notif['is_read']): ?>
                                    <span
                                        style="width: 10px; height: 10px; background: var(--primary-color); border-radius: 50%; display: inline-block;"></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="padding: 2rem; text-center;">
                    <p class="text-muted">No notifications yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>