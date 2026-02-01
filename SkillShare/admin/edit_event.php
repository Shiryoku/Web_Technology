<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: events.php");
    exit;
}

$event_id = $_GET['id'];
$error = '';
$success = '';

// Fetch event details
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    echo "Event not found.";
    exit;
}

// Fetch categories
$stmt_cat = $pdo->query("SELECT * FROM categories");
$categories = $stmt_cat->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $event_date = $_POST['event_date'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];
    $category_id = $_POST['category_id'];

    if (empty($title) || empty($description)) {
        $error = "Title and Description are required.";
    } else {
        $stmt_update = $pdo->prepare("UPDATE events SET category_id = ?, title = ?, description = ?, location = ?, event_date = ?, price = ?, capacity = ? WHERE id = ?");
        if ($stmt_update->execute([$category_id, $title, $description, $location, $event_date, $price, $capacity, $event_id])) {
            $success = "Event updated successfully.";
            // Refresh
            $stmt->execute([$event_id]);
            $event = $stmt->fetch();
        } else {
            $error = "Failed to update event.";
        }
    }
}
?>

<div class="container" style="max-width: 800px; margin-top: 2rem;">
    <div class="card">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2>Edit Workshop (Admin)</h2>
                <a href="events.php" class="btn btn-outline">Back</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"
                    style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"
                    style="background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control"
                        value="<?php echo htmlspecialchars($event['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $event['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5"
                        required><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>
                <div class="grid grid-cols-2" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Date & Time</label>
                        <input type="datetime-local" name="event_date" class="form-control"
                            value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_date'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control"
                            value="<?php echo htmlspecialchars($event['location']); ?>" required>
                    </div>
                </div>
                <div class="grid grid-cols-2" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Price ($)</label>
                        <input type="number" name="price" step="0.01" class="form-control"
                            value="<?php echo $event['price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="capacity" class="form-control"
                            value="<?php echo $event['capacity']; ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Update Workshop</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>