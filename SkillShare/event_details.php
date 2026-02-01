<?php
require_once 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: events.php");
    if (isset($pdo))
        $pdo = null;
    exit;
}

$event_id = $_GET['id'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch event details
$stmt = $pdo->prepare("
    SELECT e.*, c.name as category_name, u.full_name as organizer_name 
    FROM events e 
    JOIN categories c ON e.category_id = c.id 
    JOIN users u ON e.organizer_id = u.id 
    WHERE e.id = ?
");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    echo "<div class='container mt-4'><h2>Event not found</h2></div>";
    include 'includes/footer.php';
    exit;
}

// Check registration status
$is_registered = false;
$payment_status = null;
$registration_id = null;

if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM registrations WHERE user_id = ? AND event_id = ?");
    $stmt->execute([$user_id, $event_id]);
    if ($stmt->rowCount() > 0) {
        $reg_data = $stmt->fetch();
        $is_registered = true;
        $payment_status = $reg_data['payment_status'];
        $registration_id = $reg_data['id'];
    }
}

// Handle Registration
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    if (!$user_id) {
        header("Location: login.php");
        if (isset($pdo))
            $pdo = null;
        exit;
    }

    if (!$is_registered) {
        // Check capacity
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ? AND status = 'confirmed'");
        $stmt->execute([$event_id]);
        $current_attendees = $stmt->fetchColumn();

        if ($current_attendees < $event['capacity']) {
            $stmt = $pdo->prepare("INSERT INTO registrations (user_id, event_id, status, payment_status) VALUES (?, ?, 'confirmed', 'pending')");
            if ($stmt->execute([$user_id, $event_id])) {
                $new_reg_id = $pdo->lastInsertId();
                header("Location: student/checkout.php?reg_id=" . $new_reg_id);
                if (isset($pdo))
                    $pdo = null;
                exit;
            } else {
                $message = "Registration failed.";
            }
        } else {
            // Waitlist logic could go here
            $message = "Event is full.";
        }
    }
}
?>

<div class="container" style="margin-top: 2rem;">
    <div class="card">
        <img src="<?php echo $event['image_path'] ? 'uploads/' . $event['image_path'] : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'; ?>"
            alt="Event Image" style="width: 100%; height: 400px; object-fit: cover;">

        <div class="card-body">
            <div class="flex justify-between items-start">
                <div>
                    <span
                        class="badge badge-primary mb-2"><?php echo htmlspecialchars($event['category_name']); ?></span>
                    <h1 class="mb-2"><?php echo htmlspecialchars($event['title']); ?></h1>
                    <p class="text-muted">Organized by <?php echo htmlspecialchars($event['organizer_name']); ?></p>
                </div>
                <div class="text-right">
                    <h2 style="color: var(--primary-color);">$<?php echo number_format($event['price'], 2); ?></h2>
                </div>
            </div>

            <hr style="margin: 2rem 0; border: 0; border-top: 1px solid var(--border-color);">

            <div class="grid grid-cols-2" style="grid-template-columns: 2fr 1fr; gap: 3rem;">
                <div>
                    <h3>About this Event</h3>
                    <p class="mt-4" style="white-space: pre-line; color: var(--text-muted);">
                        <?php echo htmlspecialchars($event['description']); ?>
                    </p>
                </div>

                <div>
                    <div class="card" style="background: var(--background-color); border: none;">
                        <div class="card-body">
                            <h3 class="mb-4">Event Details</h3>

                            <div class="mb-4">
                                <label class="form-label text-muted">Date & Time</label>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="calendar"></i>
                                    <span><?php echo date('l, F j, Y', strtotime($event['event_date'])); ?></span>
                                </div>
                                <div class="flex items-center gap-2 mt-1" style="margin-left: 1.5rem;">
                                    <i data-lucide="clock"></i>
                                    <span><?php echo date('h:i A', strtotime($event['event_date'])); ?></span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted">Event Type</label>
                                <div class="flex items-center gap-2">
                                    <i
                                        data-lucide="<?php echo $event['event_type'] == 'online' ? 'video' : 'map-pin'; ?>"></i>
                                    <span
                                        style="text-transform: capitalize;"><?php echo htmlspecialchars($event['event_type']); ?></span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label
                                    class="form-label text-muted"><?php echo $event['event_type'] == 'online' ? 'Meeting Link / Platform' : 'Location'; ?></label>
                                <div class="flex items-center gap-2">
                                    <i
                                        data-lucide="<?php echo $event['event_type'] == 'online' ? 'link' : 'map-pin'; ?>"></i>
                                    <?php if ($event['event_type'] == 'online' && filter_var($event['location'], FILTER_VALIDATE_URL)): ?>
                                        <a href="<?php echo htmlspecialchars($event['location']); ?>" target="_blank"
                                            style="color: var(--primary-color); text-decoration: underline;">
                                            <?php echo htmlspecialchars($event['location']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span><?php echo htmlspecialchars($event['location']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted">Capacity</label>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="users"></i>
                                    <span><?php echo $event['capacity']; ?> seats</span>
                                </div>
                            </div>

                            <?php if ($message): ?>
                                <div
                                    style="background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                                    <?php echo $message; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($is_registered): ?>
                                <?php if ($payment_status == 'pending'): ?>
                                    <a href="student/checkout.php?reg_id=<?php echo $registration_id; ?>"
                                        class="btn btn-primary btn-block"
                                        style="background-color: #d97706; border-color: #d97706;">
                                        Complete Payment
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-primary btn-block" disabled
                                        style="opacity: 0.7; cursor: not-allowed;">
                                        Already Registered
                                    </button>

                                    <?php if ($payment_status == 'paid' && !empty($event['material_path'])): ?>
                                        <div class="mt-3">
                                            <a href="uploads/<?php echo $event['material_path']; ?>"
                                                class="btn btn-outline-primary btn-block" download>
                                                <i data-lucide="download" style="width: 16px; height: 16px; margin-right: 5px;"></i>
                                                Download Workshop Material
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <form method="POST">
                                    <button type="submit" name="register" class="btn btn-primary btn-block">
                                        Register Now
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>