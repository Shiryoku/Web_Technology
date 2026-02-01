<?php
// Include database configuration
require_once '../config/db.php';
// Include header file for layout and navigation
include '../includes/header.php';

// Authentication Check: Ensure the user is logged in and has the 'organizer' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'organizer') {
    header("Location: ../login.php");
    exit;
}

// Parameter Check: specific event ID is required to view attendees
if (!isset($_GET['id'])) {
    header("Location: ../profile.php?view=my_events");
    exit;
}

$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Check Ownership: Verify that the current event belongs to the logged-in organizer
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND organizer_id = ?");
$stmt->execute([$event_id, $user_id]);
$event = $stmt->fetch();

// If event is not found or doesn't belong to the user, deny access
if (!$event) {
    echo "<div class='container mt-4'><h2>Event not found or access denied.</h2></div>";
    include '../includes/footer.php';
    exit;
}

// Handle Attendance Update: Process form submission to update a student's attendance status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_attendance'])) {
    $reg_id = $_POST['registration_id'];
    $status = $_POST['status'];

    $update_stmt = $pdo->prepare("UPDATE registrations SET attendance_status = ? WHERE id = ?");
    $update_stmt->execute([$status, $reg_id]);
}

// Fetch Attendees: Retrieve all registrations for this specific event
// Joins with the users table to get student details (name, email)
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name, u.email 
    FROM registrations r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.event_id = ? 
    ORDER BY u.full_name ASC
");
$stmt->execute([$event_id]);
$attendees = $stmt->fetchAll();
?>

<div class="container" style="margin-top: 2rem;">
    <div class="flex justify-between items-center mb-4">
        <div>
            <a href="../profile.php?view=my_events" class="text-muted" style="font-size: 0.9rem;">&larr; Back to
                Dashboard</a>
            <h1 class="mt-2">Attendees: <?php echo htmlspecialchars($event['title']); ?></h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="scan_attendance.php" class="btn btn-primary btn-sm">Scan Attendance</a>
            <a href="view_feedback.php?id=<?php echo $event_id; ?>" class="btn btn-outline btn-sm">View Feedback</a>
            <span class="text-muted">Total: <?php echo count($attendees); ?></span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (count($attendees) > 0): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <th style="text-align: left; padding: 1rem;">Student Name</th>
                            <th style="text-align: left; padding: 1rem;">Email</th>
                            <th style="text-align: left; padding: 1rem;">Registration Date</th>
                            <th style="text-align: left; padding: 1rem;">Payment Status</th>
                            <th style="text-align: left; padding: 1rem;">Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendees as $attendee): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem; font-weight: 500;">
                                    <?php echo htmlspecialchars($attendee['full_name']); ?>
                                </td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($attendee['email']); ?></td>
                                <td style="padding: 1rem;">
                                    <?php echo date('M d, Y', strtotime($attendee['registration_date'])); ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <span
                                        class="badge <?php echo $attendee['payment_status'] == 'paid' ? 'badge-primary' : ''; ?>"
                                        style="<?php echo $attendee['payment_status'] != 'paid' ? 'background: #fee2e2; color: #991b1b;' : ''; ?>">
                                        <?php echo ucfirst($attendee['payment_status']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <form method="POST" style="display: flex; gap: 0.5rem;">
                                        <input type="hidden" name="registration_id" value="<?php echo $attendee['id']; ?>">
                                        <input type="hidden" name="update_attendance" value="1">

                                        <select name="status" onchange="this.form.submit()" class="form-control"
                                            style="padding: 0.25rem 0.5rem; width: auto; font-size: 0.9rem;">
                                            <option value="pending" <?php echo $attendee['attendance_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="checked_in" <?php echo $attendee['attendance_status'] == 'checked_in' ? 'selected' : ''; ?>>Present</option>
                                            <option value="absent" <?php echo $attendee['attendance_status'] == 'absent' ? 'selected' : ''; ?>>Absent</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-muted py-4">No students registered yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>