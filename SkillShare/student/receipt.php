<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['reg_id'])) {
    header("Location: ../profile.php");
    exit;
}

$reg_id = $_GET['reg_id'];
$user_id = $_SESSION['user_id'];

// Fetch registration, event, and payment details
$stmt = $pdo->prepare("
    SELECT r.*, e.title, e.event_date, e.location, e.image_path, p.amount, p.payment_date, p.transaction_id
    FROM registrations r 
    JOIN events e ON r.event_id = e.id 
    LEFT JOIN payments p ON r.id = p.registration_id
    WHERE r.id = ? AND r.user_id = ?
");
$stmt->execute([$reg_id, $user_id]);
$data = $stmt->fetch();

if (!$data) {
    echo "<div class='container mt-4'><h2>Receipt not found</h2></div>";
    include '../includes/footer.php';
    exit;
}
?>

<div class="container" style="margin-top: 2rem; max-width: 600px;">
    <div class="card">
        <div class="card-body text-center">
            <h2 class="mb-4">Payment Receipt</h2>

            <div class="mb-4">
                <img src="<?php echo $data['image_path'] ? '../uploads/' . $data['image_path'] : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'; ?>"
                    alt="Event"
                    style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; margin-bottom: 1rem;">
                <h3><?php echo htmlspecialchars($data['title']); ?></h3>
                <p class="text-muted"><?php echo date('M d, Y h:i A', strtotime($data['event_date'])); ?></p>
            </div>

            <div class="text-left mb-4"
                style="text-align: left; background: #f9fafb; padding: 1.5rem; border-radius: var(--radius);">
                <div class="flex justify-between mb-2">
                    <span class="text-muted">Transaction ID</span>
                    <span style="font-family: monospace;"><?php echo $data['transaction_id']; ?></span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-muted">Date</span>
                    <span><?php echo date('M d, Y h:i A', strtotime($data['payment_date'])); ?></span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-muted">Amount Paid</span>
                    <span style="font-weight: 600;">$<?php echo number_format($data['amount'], 2); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted">Status</span>
                    <span class="badge badge-success">Paid</span>
                </div>
            </div>

            <?php if (!empty($data['checkin_token'])): ?>
                <div class="mb-4">
                    <h3>Event Check-in</h3>
                    <p class="text-muted">Show this QR code to the organizer for attendance.</p>
                    <div id="qrcode" style="display: flex; justify-content: center; margin: 1rem 0;"></div>
                </div>
            <?php endif; ?>



            <a href="../profile.php" class="btn btn-outline">Back to Profile</a>
        </div>
    </div>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    <?php if (!empty($data['checkin_token'])): ?>
        new QRCode(document.getElementById("qrcode"), {
            text: "<?php echo $data['checkin_token']; ?>",
            width: 128,
            height: 128,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    <?php endif; ?>
</script>



<?php include '../includes/footer.php'; ?>