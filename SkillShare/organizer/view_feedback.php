<?php
// Include database configuration
require_once '../config/db.php';
// Include header for layout
include '../includes/header.php';

// Authentication Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'organizer') {
    header("Location: ../login.php");
    exit;
}

// Parameter Check: Event ID is required
if (!isset($_GET['id'])) {
    header("Location: ../profile.php?view=my_events");
    exit;
}

$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Verify Ownership: Ensure event belongs to the organizer
$stmt = $pdo->prepare("SELECT title FROM events WHERE id = ? AND organizer_id = ?");
$stmt->execute([$event_id, $user_id]);
$event = $stmt->fetch();

if (!$event) {
    echo "<div class='container mt-4'><h2>Event not found or access denied.</h2></div>";
    include '../includes/footer.php';
    exit;
}

// Fetch Feedback: Get all feedback for this event with user details
$stmt = $pdo->prepare("
    SELECT f.*, u.full_name, u.email 
    FROM event_feedback f 
    JOIN users u ON f.user_id = u.id 
    WHERE f.event_id = ? 
    ORDER BY f.created_at DESC
");
$stmt->execute([$event_id]);
$feedbacks = $stmt->fetchAll();

// Calculate Average Rating: Compute the mean score
$avg_rating = 0;
if (count($feedbacks) > 0) {
    $total_rating = array_sum(array_column($feedbacks, 'rating'));
    $avg_rating = round($total_rating / count($feedbacks), 1);
}
?>

<!-- Main Container -->
<div class="container" style="margin-top: 2rem;">
    <div class="row">
        <div class="col-12 mb-4">
            <a href="../profile.php?view=my_events" class="text-muted">&larr; Back to My Events</a>
        </div>
    </div>

    <!-- Summary Card: Displays overall stats -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="mb-2">Event Feedback</h2>
                    <h4 class="text-muted"><?php echo htmlspecialchars($event['title']); ?></h4>
                </div>
                <div class="text-right">
                    <div class="display-4" style="font-size: 2.5rem; font-weight: bold; color: var(--primary-color);">
                        <?php echo $avg_rating; ?> <span style="font-size: 1rem; color: #6b7280;">/ 5</span>
                    </div>
                    <div class="text-muted"><?php echo count($feedbacks); ?> reviews</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback List Grid -->
    <?php if (count($feedbacks) > 0): ?>
        <div class="grid gap-3" style="display: grid; gap: 1rem;">
            <?php foreach ($feedbacks as $feedback): ?>
                <!-- Individual Feedback Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h5 style="margin: 0;"><?php echo htmlspecialchars($feedback['full_name']); ?></h5>
                                <small
                                    class="text-muted"><?php echo date('M d, Y h:i A', strtotime($feedback['created_at'])); ?></small>
                            </div>
                            <!-- Star Rating Display -->
                            <div style="color: #fbbf24; font-size: 1.2rem;">
                                <?php for ($i = 0; $i < $feedback['rating']; $i++)
                                    echo '★'; ?>
                                <?php for ($i = $feedback['rating']; $i < 5; $i++)
                                    echo '<span style="color: #e5e7eb;">★</span>'; ?>
                            </div>
                        </div>
                        <?php if ($feedback['comment']): ?>
                            <p class="mt-2" style="color: #374151; white-space: pre-line;">
                                <?php echo htmlspecialchars($feedback['comment']); ?>
                            </p>
                        <?php else: ?>
                            <p class="mt-2 text-muted" style="font-style: italic;">No comment provided.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">No feedback received for this event yet.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>