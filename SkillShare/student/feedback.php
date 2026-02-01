<?php
// Include database configuration
require_once '../config/db.php';
// Include header for layout
include '../includes/header.php';

// Authentication Check: Ensure user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

// Parameter Check: Event ID is required
if (!isset($_GET['event_id'])) {
    header("Location: ../profile.php?view=my_events");
    exit;
}

$event_id = $_GET['event_id'];
$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Check Attendance: Verify that the user actually attended (confirmed registration) the event
$stmt = $pdo->prepare("SELECT * FROM registrations WHERE user_id = ? AND event_id = ? AND status = 'confirmed'");
$stmt->execute([$user_id, $event_id]);
if ($stmt->rowCount() == 0) {
    header("Location: ../profile.php?view=my_events");
    exit;
}

// Check Existing Feedback: Prevent duplicate submissions
$stmt = $pdo->prepare("SELECT * FROM event_feedback WHERE user_id = ? AND event_id = ?");
$stmt->execute([$user_id, $event_id]);
if ($stmt->rowCount() > 0) {
    $message = "You have already submitted feedback for this event.";
}

// Fetch Event Details: Get title for display
$stmt = $pdo->prepare("SELECT title FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    // Validation
    if (empty($rating) || $rating < 1 || $rating > 5) {
        $error = "Please provide a valid rating (1-5 stars).";
    } else {
        try {
            // Insert Feedback into Database
            $stmt = $pdo->prepare("INSERT INTO event_feedback (event_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$event_id, $user_id, $rating, $comment]);
            $message = "Thank you for your feedback!";
        } catch (PDOException $e) {
            $error = "Error submitting feedback: " . $e->getMessage();
        }
    }
}
?>

<!-- Main Container -->
<div class="container" style="max-width: 600px; margin-top: 2rem;">
    <!-- Feedback Card -->
    <div class="card">
        <div class="card-body">
            <h2 class="mb-4">Give Feedback</h2>
            <h4 class="text-muted mb-4"><?php echo htmlspecialchars($event['title']); ?></h4>

            <!-- Success Message Alert -->
            <?php if ($message): ?>
                <div class="alert alert-success"
                    style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $message; ?>
                </div>
                <a href="../profile.php?view=my_events" class="btn btn-secondary btn-block">Back to My Events</a>
            <?php else: ?>
                <!-- Error Message Alert -->
                <?php if ($error): ?>
                    <div class="alert alert-danger"
                        style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Feedback Form -->
                <form method="POST">
                    <!-- Rating Section (Stars) -->
                    <div class="form-group mb-4">
                        <label class="form-label">Rating</label>
                        <div class="rating-input"
                            style="display: flex; gap: 0.5rem; flex-direction: row-reverse; justify-content: flex-end;">
                            <input type="radio" name="rating" value="5" id="star5" style="display: none;"><label for="star5"
                                style="cursor: pointer; font-size: 2rem; color: #ccc;">★</label>
                            <input type="radio" name="rating" value="4" id="star4" style="display: none;"><label for="star4"
                                style="cursor: pointer; font-size: 2rem; color: #ccc;">★</label>
                            <input type="radio" name="rating" value="3" id="star3" style="display: none;"><label for="star3"
                                style="cursor: pointer; font-size: 2rem; color: #ccc;">★</label>
                            <input type="radio" name="rating" value="2" id="star2" style="display: none;"><label for="star2"
                                style="cursor: pointer; font-size: 2rem; color: #ccc;">★</label>
                            <input type="radio" name="rating" value="1" id="star1" style="display: none;"><label for="star1"
                                style="cursor: pointer; font-size: 2rem; color: #ccc;">★</label>
                        </div>
                        <!-- Star Hover Effects CSS -->
                        <style>
                            .rating-input input:checked~label,
                            .rating-input label:hover,
                            .rating-input label:hover~label {
                                color: #fbbf24 !important;
                            }
                        </style>
                    </div>

                    <!-- Comment Section -->
                    <div class="form-group mb-4">
                        <label class="form-label">Comment (Optional)</label>
                        <textarea name="comment" class="form-control" rows="4"
                            placeholder="Share your experience..."></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" name="submit_feedback" class="btn btn-primary" style="flex: 1;">Submit
                            Feedback</button>
                        <a href="../profile.php?view=my_events" class="btn btn-outline" style="flex: 1;">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>