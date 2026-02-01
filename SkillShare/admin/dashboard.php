<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    if (isset($pdo))
        $pdo = null;
    exit;
}

// Fetch Statistics
$stmt_users = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt_users->fetchColumn();

$stmt_events = $pdo->query("SELECT COUNT(*) FROM events");
$total_events = $stmt_events->fetchColumn();

$stmt_registrations = $pdo->query("SELECT COUNT(*) FROM registrations");
$total_registrations = $stmt_registrations->fetchColumn();
?>

<div class="container" style="margin-top: 2rem;">
    <div class="flex justify-between items-center mb-4">
        <h1>Admin Dashboard</h1>
        <a href="../logout.php" class="btn btn-outline" style="color: #ef4444; border-color: #ef4444;">Sign Out</a>
    </div>

    <div class="grid grid-cols-3" style="gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="display-4" style="color: var(--primary-color);">
                    <?php echo $total_users; ?>
                </h3>
                <p class="text-muted">Total Users</p>
                <a href="users.php" class="btn btn-outline btn-sm mt-2">Manage Users</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <h3 class="display-4" style="color: var(--primary-color);">
                    <?php echo $total_events; ?>
                </h3>
                <p class="text-muted">Total Workshops</p>
                <a href="events.php" class="btn btn-outline btn-sm mt-2">Manage Workshops</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <h3 class="display-4" style="color: var(--primary-color);">
                    <?php echo $total_registrations; ?>
                </h3>
                <p class="text-muted">Total Registrations</p>
            </div>
        </div>
    </div>


    <!-- Statistics Chart Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title mb-4">Platform Growth (Last 7 Days)</h3>
                    <canvas id="growthChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Prepare data for the chart
    $dates = [];
    $user_counts = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dates[] = date('M d', strtotime($date));

        // Users count for this date
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) = ?");
        $stmt->execute([$date]);
        $user_counts[] = $stmt->fetchColumn();
    }
    ?>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('growthChart').getContext('2d');
        const growthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode($user_counts); ?>,
                    borderColor: 'rgb(79, 70, 229)', // Primary color
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
</div>

<?php include '../includes/footer.php'; ?>