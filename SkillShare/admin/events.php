<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all events
$stmt = $pdo->query("SELECT e.*, u.full_name as organizer_name FROM events e JOIN users u ON e.organizer_id = u.id ORDER BY e.created_at DESC");
$events = $stmt->fetchAll();
?>

<div class="container" style="margin-top: 2rem;">
    <div class="flex justify-between items-center mb-4">
        <h1>Manage Workshops</h1>
        <a href="dashboard.php" class="btn btn-outline">&larr; Back to Dashboard</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div style="background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <th style="text-align: left; padding: 1rem;">ID</th>
                        <th style="text-align: left; padding: 1rem;">Title</th>
                        <th style="text-align: left; padding: 1rem;">Organizer</th>
                        <th style="text-align: left; padding: 1rem;">Date</th>
                        <th style="text-align: left; padding: 1rem;">Price</th>
                        <th style="text-align: left; padding: 1rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;">#
                                <?php echo $event['id']; ?>
                            </td>
                            <td style="padding: 1rem; font-weight: 500;">
                                <?php echo htmlspecialchars($event['title']); ?>
                            </td>
                            <td style="padding: 1rem;">
                                <?php echo htmlspecialchars($event['organizer_name']); ?>
                            </td>
                            <td style="padding: 1rem;">
                                <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                            </td>
                            <td style="padding: 1rem;">
                                $
                                <?php echo number_format($event['price'], 2); ?>
                            </td>
                            <td style="padding: 1rem;">
                                <div class="flex gap-2">
                                    <a href="edit_event.php?id=<?php echo $event['id']; ?>"
                                        class="btn btn-outline btn-sm">Edit</a>
                                    <form method="POST" action="delete_event.php"
                                        onsubmit="return confirm('Are you sure you want to delete this workshop?');"
                                        style="display: inline;">
                                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                        <button type="submit" class="btn btn-sm"
                                            style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>