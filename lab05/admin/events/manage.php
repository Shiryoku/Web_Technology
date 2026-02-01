<?php
require_once __DIR__ . '/../../config.php'; //global config

// --- Fetch all events and category name ---
$sql = "
    SELECT 
        e.*, 
        c.categoryName
    FROM 
        events AS e
    INNER JOIN 
        event_category AS c 
        ON e.category_id = c.category_id
    ORDER BY 
        e.event_date DESC
    ";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CEMS - Create Event</title>

  <!-- Use BASE_PATH_CSS constant -->
  <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- ======= Hero Section ======= -->
  <header class="hero">
    <div class="overlay"></div>
    <div class="hero-content">      
      <h1>CEMS - Admin Dashboard</h1>
    </div>
  </header>
  
  <div class="admin-container">
    <!-- Include Sidebar -->
    <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>
    
    <!-- Main Content -->
    <main class="main-content" id="main-content">
        <h2>Manage Events</h2>

            <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Category</th>
                    <th>Venue</th>
                    <th>Date</th>
                    <th>Mode</th>
                    <th>Poster</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                    <td data-label="ID"><?= $row['event_id'] ?></td>
                    <td data-label="Event Name"><?= htmlspecialchars($row['event_name']) ?></td>
                    <td data-label="Category"><?= $row['categoryName'] ?></td>
                    <td data-label="Venue"><?= htmlspecialchars($row['venue']) ?></td>
                    <td data-label="Date"><?= $row['event_date'] ?></td>
                    <td data-label="Mode"><?= $row['mode'] ?></td>
                    <td data-label="Poster">
                        <?php if (!empty($row['poster_path'])): ?>
                        <img src="<?= BASE_PATH_UPLOADS . htmlspecialchars($row['poster_path']) ?>" alt="Poster">
                        <?php else: ?>
                        N/A
                        <?php endif; ?>
                    </td>
                    <td data-label="Remarks"><?= htmlspecialchars($row['remarks']) ?></td>
                    <td data-label="Actions">
                        <a href="edit.php?id=<?= $row['event_id'] ?>" class="action-btn edit">Edit</a>
                        <a href="delete.php?id=<?= $row['event_id'] ?>" class="action-btn delete" onclick="return confirm('Are you sure to delete this event?')">Delete</a>
                    </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align:center;">No events found.</p>
            <?php endif; ?>

            <p style="text-align:center; margin-top:1.5rem;">
            <a href="create.php" class="action-btn edit">➕ Create New Event</a>
            </p>

    </main>
  </div>

  <footer>
    <hr>
    <p>&copy; Sam | BI12345678</p>
  </footer>

  <script>
    // Toggle submenu in sidebar visibility
    document.querySelectorAll('.submenu-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        btn.nextElementSibling.classList.toggle('show');
      });
    });
  </script>
</body>
</html>
