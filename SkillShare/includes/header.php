<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($base_path)) {
    // Determine base path based on directory depth (organizer, student, or admin) to ensure relative links work
    $base_path = (stripos($_SERVER['SCRIPT_NAME'], '/organizer/') !== false || stripos($_SERVER['SCRIPT_NAME'], '/student/') !== false || stripos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '../' : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillShare - Community Workshop Management</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <nav class="navbar">
        <div class="container nav-content">
            <a href="<?php echo $base_path; ?>index.php" class="logo">
                <i data-lucide="share-2"></i>
                SkillShare
            </a>

            <button class="mobile-menu-btn" id="mobile-menu-btn">
                <i data-lucide="menu"></i>
            </button>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
                    const navLinks = document.querySelector('.nav-links'); if (mobileMenuBtn) {
                        mobileMenuBtn.addEventListener('click', function () {
                            navLinks.classList.toggle('active');
                            const icon = navLinks.classList.contains('active') ? 'x' : 'menu';
                            const iconElement = mobileMenuBtn.querySelector('i');
                            if (iconElement) {
                                iconElement.setAttribute('data-lucide', icon);
                                lucide.createIcons();
                            }
                        });
                    }
                });
            </script>
            <div class="nav-links">
                <a href="<?php echo $base_path; ?>index.php" class="nav-link">Home</a>
                <a href="<?php echo $base_path; ?>events.php" class="nav-link">Browse Workshops</a>

                <?php if (isset($_SESSION['user_id'])):
                    require_once __DIR__ . '/notification_helper.php';
                    // Ensure $pdo is available, might need to require db.php if not already included in the page
                    if (!isset($pdo)) {
                        require_once __DIR__ . '/../config/db.php';
                    }
                    $unread_count = getUnreadCount($pdo, $_SESSION['user_id']);
                    ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="<?php echo $base_path; ?>admin/dashboard.php" class="nav-link"
                            style="color: var(--primary-color); font-weight: 600;">Admin Dashboard</a>
                    <?php else: ?>
                        <a href="<?php echo $base_path; ?>profile.php" class="nav-link">Dashboard</a>
                    <?php endif; ?>

                    <div class="dropdown" style="margin-left: 1rem;">
                        <a href="<?php echo $base_path; ?>notifications.php" class="nav-link"
                            style="position: relative; display: inline-flex; align-items: center;">
                            <span class="notification-icon-wrapper"><i data-lucide="bell"
                                    style="width: 20px; height: 20px;"></i></span>
                            <span class="notification-text">Notifications</span>
                            <?php if ($unread_count > 0): ?>
                                <span
                                    style="position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 10px; min-width: 18px; text-align: center;">
                                    <?php echo $unread_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-content">
                            <div class="dropdown-header">
                                <span>Notifications</span>
                                <?php if ($unread_count > 0): ?>
                                    <span class="badge badge-primary"><?php echo $unread_count; ?> New</span>
                                <?php endif; ?>
                            </div>
                            <?php
                            $recent_notifications = getAllNotifications($pdo, $_SESSION['user_id'], 5);
                            if (count($recent_notifications) > 0):
                                foreach ($recent_notifications as $notif):
                                    ?>
                                    <a href="<?php echo $base_path; ?>notifications.php"
                                        class="dropdown-item notification-item <?php echo !$notif['is_read'] ? 'notification-unread' : ''; ?>">
                                        <?php echo htmlspecialchars($notif['message']); ?>
                                        <span
                                            class="notification-time"><?php echo date('M d, H:i', strtotime($notif['created_at'])); ?></span>
                                    </a>
                                    <?php
                                endforeach;
                            else:
                                ?>
                                <div class="dropdown-item text-center text-muted">No notifications</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>login.php" class="nav-link">Login</a>
                <a href="<?php echo $base_path; ?>register.php" class="btn btn-primary">Get Started</a>
            <?php endif; ?>
        </div>
        </div>
    </nav>
    <main>