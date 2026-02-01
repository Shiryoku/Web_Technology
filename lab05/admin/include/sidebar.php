<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config.php';
}
?>
<aside class="sidebar" id="sidebar">
  <nav class="menu">
    <a href="<?= BASE_URL ?>/admin/index.php" class="menu-item active">Dashboard</a>
    <a href="<?= BASE_URL ?>/admin/users/users.php" class="menu-item">Users</a>

    <div class="submenu">
      <button class="submenu-toggle">Events ▾</button>
      <div class="submenu-content">
        <a href="<?= BASE_URL ?>/admin/events/manage.php">Manage</a>
        <a href="<?= BASE_URL ?>/admin/events/create.php">Create</a>
      </div>
    </div>

    <a href="<?= BASE_URL ?>/auth/logout.php" class="menu-item">Logout</a>
  </nav>
</aside>
