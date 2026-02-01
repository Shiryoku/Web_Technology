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

// Parameter Check: specific event ID is required to edit
if (!isset($_GET['id'])) {
    header("Location: ../profile.php?view=my_events");
    exit;
}

$event_id = $_GET['id'];
$organizer_id = $_SESSION['user_id'];

// Check Ownership: Verify that the workshop exists and belongs to the currently logged-in organizer
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND organizer_id = ?");
$stmt->execute([$event_id, $organizer_id]);
$event = $stmt->fetch();

// If event does not exist or access is denied
if (!$event) {
    echo "<div class='container' style='margin-top: 2rem;'><div class='alert alert-danger'>Workshop not found or you don't have permission to edit it.</div></div>";
    include '../includes/footer.php';
    exit;
}

$error = '';
$success = '';

// Fetch Categories: Retrieve list of categories for the dropdown menu
$stmt_cat = $pdo->query("SELECT * FROM categories");
$categories = $stmt_cat->fetchAll();

// Handle Form Submission: Process the update request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve form inputs
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $event_date = $_POST['event_date'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];
    $category_id = $_POST['category_id'];
    $event_type = $_POST['event_type'];

    // Handle Image Upload Logic
    $image_path = $event['image_path']; // Default to existing image if no new one is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $new_filename;
        } else {
            $error = "Failed to upload image.";
        }
    }

    // Handle Material Upload Logic
    $material_path = $event['material_path']; // Default to existing material
    if (isset($_FILES['material']) && $_FILES['material']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_extension = pathinfo($_FILES["material"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_material.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        // Move uploaded material
        if (move_uploaded_file($_FILES["material"]["tmp_name"], $target_file)) {
            $material_path = $new_filename;
        } else {
            $error = "Failed to upload material.";
        }
    }

    // Proceed if no errors
    if (!$error) {
        // Update Event Database Record
        $stmt_update = $pdo->prepare("UPDATE events SET category_id = ?, title = ?, description = ?, location = ?, event_date = ?, price = ?, capacity = ?, image_path = ?, event_type = ?, material_path = ? WHERE id = ? AND organizer_id = ?");
        if ($stmt_update->execute([$category_id, $title, $description, $location, $event_date, $price, $capacity, $image_path, $event_type, $material_path, $event_id, $organizer_id])) {
            $success = "Workshop updated successfully!";

            // Notification Logic: Inform registered students about the update
            require_once '../includes/notification_helper.php';

            // Fetch all registered students for this event
            $stmt_students = $pdo->prepare("SELECT user_id FROM registrations WHERE event_id = ?");
            $stmt_students->execute([$event_id]);
            $students = $stmt_students->fetchAll();

            $notif_message = "Update: The workshop '$title' has been updated by the organizer.";
            foreach ($students as $student) {
                createNotification($pdo, $student['user_id'], 'event_update', $notif_message, $event_id);
            }

            // Refresh event data to show updated values in the form
            $stmt->execute([$event_id, $organizer_id]);
            $event = $stmt->fetch();
        } else {
            $error = "Failed to update workshop.";
        }
    }
}
?>

<!-- Main Container -->
<div class="container" style="max-width: 800px; margin-top: 2rem;">
    <!-- Edit Workshop Form Card -->
    <div class="card">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2 style="margin: 0;">Edit Workshop</h2>
                <a href="../profile.php?view=my_events" class="btn btn-outline">Back to Dashboard</a>
            </div>

            <!-- Error Notification -->
            <?php if ($error): ?>
                <div
                    style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Success Notification -->
            <?php if ($success): ?>
                <div
                    style="background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <!-- Title Input -->
                <div class="form-group">
                    <label class="form-label">Workshop Title</label>
                    <input type="text" name="title" class="form-control"
                        value="<?php echo htmlspecialchars($event['title']); ?>" required>
                </div>

                <!-- Category Dropdown -->
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

                <!-- Workshop Type & Location Switcher -->
                <div class="form-group">
                    <label class="form-label">Workshop Type</label>
                    <select name="event_type" class="form-control" required onchange="updateLocationLabel(this.value)">
                        <option value="physical" <?php echo $event['event_type'] == 'physical' ? 'selected' : ''; ?>>
                            Physical Event</option>
                        <option value="online" <?php echo $event['event_type'] == 'online' ? 'selected' : ''; ?>>Online
                            Event</option>
                    </select>
                </div>

                <!-- Description Text Area -->
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5"
                        required><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>

                <!-- Date & Location Grid -->
                <div class="grid grid-cols-2" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Date & Time</label>
                        <input type="datetime-local" name="event_date" class="form-control"
                            value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_date'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"
                            id="locationLabel"><?php echo $event['event_type'] == 'online' ? 'Meeting Link / Platform' : 'Location'; ?></label>
                        <input type="text" name="location" class="form-control"
                            value="<?php echo htmlspecialchars($event['location']); ?>" required>
                    </div>
                </div>

                <!-- Price & Capacity Grid -->
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

                <!-- Image Upload Section -->
                <div class="form-group">
                    <label class="form-label">Workshop Image</label>
                    <?php if ($event['image_path']): ?>
                        <div class="mb-2">
                            <img src="../uploads/<?php echo $event['image_path']; ?>" alt="Current Image"
                                style="max-height: 100px; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Leave empty to keep current image</small>
                </div>

                <!-- Material Upload Section -->
                <div class="form-group">
                    <label class="form-label">Workshop Material</label>
                    <?php if ($event['material_path']): ?>
                        <div class="mb-2">
                            <a href="../uploads/<?php echo $event['material_path']; ?>" target="_blank"
                                class="text-primary">
                                <i data-lucide="file" style="width: 16px; height: 16px; display: inline;"></i>
                                View Current Material
                            </a>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="material" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip">
                    <small class="text-muted">Leave empty to keep current material. Uploading new will replace
                        old.</small>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Update Workshop</button>
            </form>

            <!-- Dangerous Action: Delete Workshop -->
            <div style="margin-top: 1.5rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                <form method="POST" action="delete_event.php"
                    onsubmit="return confirm('Are you sure you want to delete this workshop? This action cannot be undone and will remove all registrations.');">
                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                    <button type="submit" class="btn btn-block"
                        style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                        Delete Workshop
                    </button>
                </form>
            </div>
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