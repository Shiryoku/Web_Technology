<?php
require_once 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    if (isset($pdo))
        $pdo = null;
    exit;
}

$user_id = $_SESSION['user_id'];
$view = isset($_GET['view']) ? $_GET['view'] : 'settings';
$message = isset($_GET['msg']) ? $_GET['msg'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $new_password = $_POST['new_password'];

    if (empty($full_name)) {
        $error = "Full Name cannot be empty.";
    } else {
        try {
            if (!empty($new_password)) {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, password = ? WHERE id = ?");
                $stmt->execute([$full_name, $new_password, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
                $stmt->execute([$full_name, $user_id]);
            }
            $message = "Profile updated successfully.";
            $_SESSION['user_name'] = $full_name; // Update session name
        } catch (PDOException $e) {
            $error = "Error updating profile: " . $e->getMessage();
        }
    }
}

// Fetch User Details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch Pending Payments (Student only)
$stmt_pending = $pdo->prepare("
    SELECT r.*, e.title, e.price, e.event_date 
    FROM registrations r 
    JOIN events e ON r.event_id = e.id 
    WHERE r.user_id = ? AND r.payment_status = 'pending'
    ORDER BY r.registration_date DESC
");
$stmt_pending->execute([$user_id]);
$pending_payments = $stmt_pending->fetchAll();

// Fetch My Events (Logic based on role)
$my_events = [];
if ($view == 'my_events') {
    if ($user['role'] == 'organizer') {
        // Fetch events created by organizer
        $stmt_events = $pdo->prepare("
            SELECT e.*, 
                   (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as registration_count 
            FROM events e 
            WHERE e.organizer_id = ? 
            ORDER BY e.event_date DESC
        ");
        $stmt_events->execute([$user_id]);
        $my_events = $stmt_events->fetchAll();
    } else {
        // Fetch registrations for student
        $stmt_events = $pdo->prepare("
            SELECT e.*, r.status as registration_status, r.payment_status, r.id as registration_id 
            FROM registrations r 
            JOIN events e ON r.event_id = e.id 
            WHERE r.user_id = ? 
            ORDER BY e.event_date DESC
        ");
        $stmt_events->execute([$user_id]);
        $my_events = $stmt_events->fetchAll();
    }
}

// Fetch Calendar Events
$calendar_events_json = '[]';
if ($view == 'calendar') {
    $calendar_events = [];
    if ($user['role'] == 'organizer') {
        $stmt = $pdo->prepare("SELECT id, title, event_date FROM events WHERE organizer_id = ?");
        $stmt->execute([$user_id]);
        $calendar_events = $stmt->fetchAll();
    } else {
        $stmt = $pdo->prepare("
            SELECT e.id, e.title, e.event_date 
            FROM registrations r 
            JOIN events e ON r.event_id = e.id 
            WHERE r.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $calendar_events = $stmt->fetchAll();
    }

    $calendar_events_json = json_encode(array_map(function ($e) use ($user) {
        $url = $user['role'] == 'organizer' ? 'organizer/event_attendees.php?id=' . $e['id'] : 'event_details.php?id=' . $e['id'];
        return [
            'title' => $e['title'],
            'start' => date('Y-m-d', strtotime($e['event_date'])),
            'url' => $url
        ];
    }, $calendar_events));
}
?>

<link rel="stylesheet" href="assets/css/profile.css">
<?php if ($view == 'calendar'): ?>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <style>
        .fc-event {
            cursor: pointer;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .fc-toolbar-title {
            font-size: 1.5rem !important;
        }

        .fc-button-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
    </style>
<?php endif; ?>

<div class="profile-container">
    <!-- Sidebar -->
    <div class="profile-sidebar">
        <div class="sidebar-header">Account</div>
        <a href="profile.php?view=settings"
            class="sidebar-item <?php echo $view == 'settings' ? 'active' : ''; ?>">Profile Settings</a>

        <div class="sidebar-separator"></div>
        <div class="sidebar-header">Event</div>
        <a href="profile.php?view=my_events" class="sidebar-item <?php echo $view == 'my_events' ? 'active' : ''; ?>">My
            Events</a>

        <a href="profile.php?view=calendar"
            class="sidebar-item <?php echo $view == 'calendar' ? 'active' : ''; ?>">Calendar</a>

        <?php if ($user['role'] == 'user'): ?>
            <a href="profile.php?view=payments"
                class="sidebar-item <?php echo $view == 'payments' ? 'active' : ''; ?>">Pending Payments</a>
        <?php endif; ?>

        <div class="sidebar-separator"></div>
        <a href="logout.php" class="sidebar-item" style="color: #dc2626;">Logout</a>
    </div>

    <!-- Content -->
    <div class="profile-content">
        <?php if ($view == 'settings'): ?>
            <div class="content-header">
                <h1 class="content-title">Profile Settings</h1>
            </div>

            <?php if ($message): ?>
                <div style="background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control"
                                value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>"
                                disabled style="background-color: #f3f4f6;">
                            <small class="text-muted">Email cannot be changed.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" name="new_password" class="form-control" placeholder="********">
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary btn-block">Update
                            Profile</button>
                    </form>
                </div>
            </div>

        <?php elseif ($view == 'payments' && $user['role'] == 'user'): ?>
            <div class="content-header">
                <h1 class="content-title">Pending Payments</h1>
            </div>

            <?php if (count($pending_payments) > 0): ?>
                <div class="flex flex-col gap-2" style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($pending_payments as $payment): ?>
                        <div
                            style="border: 1px solid var(--border-color); padding: 1rem; border-radius: var(--radius); background: white;">
                            <h4 style="font-weight: 600; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($payment['title']); ?>
                            </h4>
                            <p class="text-muted" style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                                <?php echo date('M d, Y', strtotime($payment['event_date'])); ?>
                            </p>
                            <div class="flex justify-between items-center">
                                <span
                                    style="font-weight: 600; color: var(--primary-color);">$<?php echo number_format($payment['price'], 2); ?></span>
                                <span class="badge" style="background: #fef3c7; color: #d97706;">Pending</span>
                            </div>
                            <a href="student/checkout.php?registration_id=<?php echo $payment['id']; ?>"
                                class="btn btn-primary btn-sm mt-2 w-full text-center" style="display: block;">Pay Now</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted">No pending payments found.</p>
                    </div>
                </div>
            <?php endif; ?>

        <?php elseif ($view == 'my_events'): ?>
            <div class="content-header">
                <h1 class="content-title">My Events</h1>
                <?php if ($user['role'] == 'organizer'): ?>
                    <a href="organizer/create_event.php" class="btn btn-primary btn-sm">Create New Event</a>
                <?php else: ?>
                    <a href="events.php" class="btn btn-primary btn-sm">Browse Events</a>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2"
                style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                <?php foreach ($my_events as $event): ?>
                    <div class="card">
                        <img src="<?php echo $event['image_path'] ? 'uploads/' . $event['image_path'] : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'; ?>"
                            alt="Event Image" class="card-image" style="height: 150px;">
                        <div class="card-body">
                            <?php if ($user['role'] == 'user'): ?>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="badge badge-primary"><?php echo ucfirst($event['registration_status']); ?></span>
                                    <?php if (isset($event['payment_status']) && $event['payment_status'] == 'paid'): ?>
                                        <a href="student/receipt.php?reg_id=<?php echo $event['registration_id']; ?>"
                                            class="text-primary" style="font-size: 0.9rem; text-decoration: underline;">View Receipt</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <h3 class="card-title" style="font-size: 1.1rem;"><?php echo htmlspecialchars($event['title']); ?>
                            </h3>
                            <p class="text-muted" style="font-size: 0.9rem;">
                                <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                            </p>

                            <?php if ($user['role'] == 'organizer'): ?>
                                Registrations: <?php echo $event['registration_count']; ?> / <?php echo $event['capacity']; ?>
                                <?php
                                $is_upcoming = strtotime($event['event_date']) >= time();
                                echo $is_upcoming
                                    ? '<span class="badge" style="background: #dcfce7; color: #166534; margin-left: 0.5rem;">Upcoming</span>'
                                    : '<span class="badge" style="background: #f3f4f6; color: #4b5563; margin-left: 0.5rem;">Ended</span>';
                                ?>
                                </p>
                                <div class="flex gap-2 mt-3">
                                    <a href="organizer/event_attendees.php?id=<?php echo $event['id']; ?>"
                                        class="btn btn-outline btn-sm" style="flex: 1;">Attendees</a>
                                    <a href="organizer/edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-outline btn-sm"
                                        style="flex: 1;">Edit</a>
                                </div>
                            <?php else: ?>
                                <?php
                                $has_passed = strtotime($event['event_date']) < time();
                                $feedback_stmt = $pdo->prepare("SELECT rating FROM event_feedback WHERE user_id = ? AND event_id = ?");
                                $feedback_stmt->execute([$user_id, $event['id']]);
                                $feedback = $feedback_stmt->fetch();
                                ?>
                                <div class="mt-3">
                                    <a href="event_details.php?id=<?php echo $event['id']; ?>"
                                        class="btn btn-outline btn-block btn-sm mb-2">View Details</a>

                                    <?php if ($has_passed): ?>
                                        <?php if ($feedback): ?>
                                            <button class="btn btn-secondary btn-block btn-sm" disabled
                                                style="opacity: 0.8; background-color: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb;">
                                                <span style="color: #fbbf24;">★</span> <?php echo $feedback['rating']; ?>/5 - Feedback Sent
                                            </button>
                                        <?php else: ?>
                                            <a href="student/feedback.php?event_id=<?php echo $event['id']; ?>"
                                                class="btn btn-primary btn-block btn-sm"
                                                style="background-color: #8b5cf6; border-color: #8b5cf6;">
                                                Give Feedback
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (count($my_events) == 0): ?>
                    <div class="card" style="grid-column: 1 / -1;">
                        <div class="card-body text-center">
                            <p class="text-muted">No events found.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($view == 'calendar'): ?>
            <div class="content-header">
                <h1 class="content-title">Calendar</h1>
            </div>
            <div class="card">
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var calendarEl = document.getElementById('calendar');
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,listWeek'
                        },
                        events: <?php echo $calendar_events_json; ?>,
                        eventClick: function (info) {
                            if (info.event.url) {
                                window.location.href = info.event.url;
                                info.jsEvent.preventDefault();
                            }
                        }
                    });
                    calendar.render();
                });
            </script>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>