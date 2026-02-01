<?php
require_once __DIR__ . '/../../config.php'; // global config
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CEMS Create Event</title>
    <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include(ROOT_PATH . '/admin/include/sidebar.php'); ?>
        
        <main class="main-content" id="main-content">
            <h2>Create Event</h2>
            <form action="create_action.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label for="event_name">Event Name</label>
                    <input type="text" id="event_name" name="event_name" required />
                </div>
                <div>
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div>
                    <label for="category">Type</label>
                    <select id="category" name="category" required>
                        <option value="">-- Select Type --</option>
                        <option value="1">Workshop</option>
                        <option value="2">Seminar</option>
                        <option value="3">Competition</option>
                        <option value="4">Festival</option>
                        <option value="5">Sport</option>
                        <option value="6">Course</option>
                    </select>
                </div>
                <div>
                    <label for="venue">Venue</label>
                    <input type="text" id="venue" name="venue" required />
                </div>
                <div>
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required />
                </div>
                <div>
                    <label for="mode">Mode</label>
                    <select id="mode" name="mode" required>
                        <option value="">-- Select Mode --</option>
                        <option>Physical</option>
                        <option>Online</option>
                        <option>Hybrid</option>
                    </select>
                </div>
                <div>
                    <label for="remarks">Remarks / Notes</label>
                    <textarea id="remarks" name="remarks"></textarea>
                </div>
                <div>
                    <label for="poster">Event Poster (Image)</label>
                    <input type="file" id="poster" name="poster" accept="image/*" required />
                </div>
                <button type="submit">Create Event</button>
            </form>
        </main>
    </div>
</body>
</html>