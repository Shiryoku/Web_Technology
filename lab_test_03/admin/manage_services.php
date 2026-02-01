<?php
// admin/manage_services.php
include '../config.php';
include 'header.php';

// Handle Add Service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_service'])) {
    $id = $_POST['service_id'];
    $name = $_POST['service_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];

    $image = "default.jpg";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir))
            mkdir($target_dir);
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image = basename($_FILES["image"]["name"]);
    }

    $stmt = $conn->prepare("INSERT INTO services (service_id, service_name, description, price, duration_minutes, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdis", $id, $name, $desc, $price, $duration, $image);

    if ($stmt->execute()) {
        header("Location: manage_services.php"); // Redirect to clear POST
        exit();
    } else {
        echo "<script>alert('Error: Service ID " . $id . " might already exist.');</script>";
    }
}

// Handle Update Service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_service'])) {
    $id = $_POST['service_id']; // This is read-only in form, key for update
    $name = $_POST['service_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $old_image = $_POST['old_image'];

    $image = $old_image; // Default to old image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir))
            mkdir($target_dir);
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image = basename($_FILES["image"]["name"]);
    }

    $stmt = $conn->prepare("UPDATE services SET service_name=?, description=?, price=?, duration_minutes=?, image_path=? WHERE service_id=?");
    $stmt->bind_param("ssdisi", $name, $desc, $price, $duration, $image, $id);

    if ($stmt->execute()) {
        header("Location: manage_services.php");
        exit();
    } else {
        echo "<script>alert('Error updating service.');</script>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM services WHERE service_id=$id");
}

// Fetch Service Data for Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_res = $conn->query("SELECT * FROM services WHERE service_id=$edit_id");
    if ($edit_res->num_rows > 0) {
        $edit_data = $edit_res->fetch_assoc();
    }
}

$result = $conn->query("SELECT * FROM services");
if (!$result) {
    die("Error retrieving services: " . $conn->error);
}
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Manage Services</h2>
    <?php if (!$edit_data): ?>
        <button onclick="document.getElementById('addForm').style.display='block'" class="btn">Add New Service</button>
    <?php endif; ?>
</div>

<!-- Add/Edit Form -->
<div id="addForm" class="card mt-4" style="<?php echo $edit_data ? 'display:block;' : 'display:none;'; ?>">
    <h3><?php echo $edit_data ? 'Edit Service' : 'Add Service'; ?></h3>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($edit_data): ?>
            <input type="hidden" name="update_service" value="1">
            <input type="hidden" name="old_image" value="<?php echo $edit_data['image_path']; ?>">
        <?php else: ?>
            <input type="hidden" name="add_service" value="1">
        <?php endif; ?>

        <div class="form-group">
            <label>Service ID <?php echo $edit_data ? '(Cannot Change)' : '(Manual)'; ?></label>
            <input type="number" name="service_id" class="form-control" required placeholder="e.g. 101"
                value="<?php echo $edit_data ? $edit_data['service_id'] : ''; ?>" <?php echo $edit_data ? 'readonly style="background-color: #e5e7eb;"' : ''; ?>>
        </div>

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="service_name" class="form-control" required
                value="<?php echo $edit_data ? htmlspecialchars($edit_data['service_name']) : ''; ?>">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control"
                required><?php echo $edit_data ? htmlspecialchars($edit_data['description']) : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label>Price (RM)</label>
            <input type="number" step="0.01" name="price" class="form-control" required
                value="<?php echo $edit_data ? $edit_data['price'] : ''; ?>">
        </div>
        <div class="form-group">
            <label>Duration (mins)</label>
            <input type="number" name="duration" class="form-control" required
                value="<?php echo $edit_data ? $edit_data['duration_minutes'] : ''; ?>">
        </div>

        <div class="form-group">
            <label>Upload Image <?php echo $edit_data ? '(Leave empty to keep current)' : ''; ?></label>
            <input type="file" name="image" class="form-control">
            <?php if ($edit_data && $edit_data['image_path']): ?>
                <small>Current: <?php echo $edit_data['image_path']; ?></small>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn"><?php echo $edit_data ? 'Update Service' : 'Save Service'; ?></button>
        <?php if ($edit_data): ?>
            <a href="manage_services.php" class="btn btn-danger">Cancel Edit</a>
        <?php else: ?>
            <button type="button" class="btn btn-danger"
                onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
        <?php endif; ?>
    </form>
</div>

<div class="card mt-4" style="padding: 0;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['service_id']; ?></td>
                    <td><img src="../uploads/<?php echo $row['image_path']; ?>" width="50"></td>
                    <td><?php echo $row['service_name']; ?></td>
                    <td>RM <?php echo $row['price']; ?></td>
                    <td>
                        <a href="manage_services.php?edit=<?php echo $row['service_id']; ?>" class="btn"
                            style="padding: 0.25rem 0.5rem; background-color: #eab308;">Edit</a>
                        <a href="manage_services.php?delete=<?php echo $row['service_id']; ?>" class="btn btn-danger"
                            style="padding: 0.25rem 0.5rem;" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>
<?php $conn->close(); ?>
</body>

</html>