<?php
// Always include config first [cite: 550]
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CEMS Admin Dashboard</title>
    <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header class="hero">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1>CEMS Admin Dashboard</h1>
        </div>
    </header>

    <div class="admin-container">
        <?php 
            include('/admin/include/sidebar.php'); 

           // Using a placeholder menu for now since sidebar code wasn't provided in the text
        ?>
        <aside class="sidebar">
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="events/manage.php">Events</a></li>
                <li><a href="../index.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content" id="main-content">
            <h2>Welcome to the CEMS Admin Dashboard</h2>
            <p style="text-align:center;">Select a menu item to view or manage content.</p>
        </main>
    </div>

    <footer>
        <hr>
        <p>&copy; <?php echo date("Y"); ?> CEMS Admin Panel</p>
    </footer>

    <script>
        [cite_start]// Toggle submenu in sidebar visibility [cite: 594]
        document.querySelectorAll('.submenu-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.nextElementSibling.classList.toggle('show');
            });
        });
    </script>
</body>
</html>