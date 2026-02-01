<?php
// Include database configuration
require_once 'config/db.php';
// Include header for layout
include 'includes/header.php';

// Fetch Featured Events:
// Select the latest 3 upcoming events (where event_date is today or in the future)
$stmt = $pdo->query("SELECT * FROM events WHERE event_date >= NOW() ORDER BY created_at DESC LIMIT 3");
$featured_events = $stmt->fetchAll();
?>

<div class="hero">
    <div class="container">
        <h1>Discover & Join Amazing Workshops</h1>
        <p>SkillShare is the best place to find workshops, charity drives, and community activities happening near you.
        </p>
        <a href="events.php" class="btn btn-primary" style="background: white; color: var(--primary-color);">Browse All
            Workshops</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'organizer'): ?>
            <a href="organizer/create_event.php" class="btn btn-outline"
                style="color: white; border-color: white; margin-left: 0.5rem; margin-top: 5px;">
                Create Workshop
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <h2 class="mb-4">Featured Workshops</h2>
    <div class="grid grid-cols-3">
        <?php foreach ($featured_events as $event): ?>
            <div class="card">
                <img src="<?php echo $event['image_path'] ? 'uploads/' . $event['image_path'] : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'; ?>"
                    alt="Event Image" class="card-image">
                <div class="card-body">
                    <div class="flex justify-between items-center mb-2">
                        <span
                            class="badge badge-primary"><?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                        <span class="text-muted"
                            style="font-size: 0.875rem;">$<?php echo number_format($event['price'], 2); ?></span>
                    </div>
                    <h3 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                    <p class="card-text"><?php echo substr(htmlspecialchars($event['description']), 0, 100); ?>...</p>
                    <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-outline btn-block">View
                        Details</a>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (count($featured_events) == 0): ?>
            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                <p class="text-muted">No workshops found. Be the first to create one!</p>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'organizer'): ?>
                    <a href="organizer/create_event.php" class="btn btn-primary mt-2">Create Workshop</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>