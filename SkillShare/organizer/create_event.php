<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'organizer') {
    header("Location: ../login.php");
    if (isset($pdo))
        $pdo = null;
    exit;
}

$error = '';
$success = '';

// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $event_date = $_POST['event_date'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];
    $category_id = $_POST['category_id'];
    $event_type = $_POST['event_type'];

    // Handle Image Upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $new_filename;
        } else {
            $error = "Failed to upload image.";
        }
    }

    // Handle Material Upload
    $material_path = null;
    if (isset($_FILES['material']) && $_FILES['material']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_extension = pathinfo($_FILES["material"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_material.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["material"]["tmp_name"], $target_file)) {
            $material_path = $new_filename;
        } else {
            $error = "Failed to upload material.";
        }
    }

    if (!$error) {
        $stmt = $pdo->prepare("INSERT INTO events (organizer_id, category_id, title, description, location, event_date, price, capacity, image_path, event_type, material_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $category_id, $title, $description, $location, $event_date, $price, $capacity, $image_path, $event_type, $material_path])) {
            $success = "Workshop created successfully!";
        } else {
            $error = "Failed to create workshop.";
        }
    }
}
?>

<div class="container" style="max-width: 800px; margin-top: 2rem;">
    <div class="card">
        <div class="card-body">
            <h2 class="mb-4">Create New Workshop</h2>

            <?php if ($error): ?>
                <div
                    style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div
                    style="background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $success; ?> <a href="../profile.php?view=my_events" style="text-decoration: underline;">Go
                        to
                        Dashboard</a>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Workshop Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Workshop Type</label>
                    <select name="event_type" class="form-control" required onchange="updateLocationLabel(this.value)">
                        <option value="physical">Physical Event</option>
                        <option value="online">Online Event</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5" required></textarea>
                </div>

                <div class="grid grid-cols-2" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Date & Time</label>
                        <input type="datetime-local" name="event_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" id="locationLabel">Location</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>
                </div>

                <div class="grid grid-cols-2" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Price ($)</label>
                        <input type="number" name="price" step="0.01" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="capacity" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Workshop Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label class="form-label">Workshop Material (Optional)</label>
                    <input type="file" name="material" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip">
                    <small class="text-muted">Upload course materials for students (PDF, DOC, ZIP, etc.)</small>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Create Workshop</button>
            </form>
        </div>
    </div>
</div>

<script>
    function updateLocationLabel(type) {
        const label = document.getElementById('locationLabel');
        if (type === 'online') {
            label.textContent = 'Meeting Link / Platform';
        } else {
            label.textContent = 'Location';
        }
    }
</script>

<?php include '../includes/footer.php'; ?>