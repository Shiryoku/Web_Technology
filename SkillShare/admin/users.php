<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    if (isset($pdo))
        $pdo = null;
    exit;
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="container" style="margin-top: 2rem;">
    <div class="flex justify-between items-center mb-4">
        <h1>Manage Users</h1>
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
                        <th style="text-align: left; padding: 1rem;">Full Name</th>
                        <th style="text-align: left; padding: 1rem;">Email</th>
                        <th style="text-align: left; padding: 1rem;">Role</th>
                        <th style="text-align: left; padding: 1rem;">Created At</th>
                        <th style="text-align: left; padding: 1rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;">#
                                <?php echo $user['id']; ?>
                            </td>
                            <td style="padding: 1rem; font-weight: 500;">
                                <?php echo htmlspecialchars($user['full_name']); ?>
                            </td>
                            <td style="padding: 1rem;">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="badge badge-secondary">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td style="padding: 1rem;">
                                <div class="flex gap-2">
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>"
                                        class="btn btn-outline btn-sm">Edit</a>
                                    <?php if ($user['role'] !== 'admin'): ?>
                                        <form method="POST" action="delete_user.php"
                                            onsubmit="return confirm('Are you sure you want to delete this user?');"
                                            style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-sm"
                                                style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">Delete</button>
                                        </form>
                                    <?php endif; ?>
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