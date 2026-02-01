<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    if (isset($pdo))
        $pdo = null;
    exit;
}

if (!isset($_GET['reg_id'])) {
    header("Location: ../events.php");
    if (isset($pdo))
        $pdo = null;
    exit;
}

$reg_id = $_GET['reg_id'];
$user_id = $_SESSION['user_id'];

// Fetch registration and event details
$stmt = $pdo->prepare("
    SELECT r.*, e.title, e.price, e.event_date, e.location, e.image_path 
    FROM registrations r 
    JOIN events e ON r.event_id = e.id 
    WHERE r.id = ? AND r.user_id = ?
");
$stmt->execute([$reg_id, $user_id]);
$registration = $stmt->fetch();

if (!$registration) {
    echo "<div class='container mt-4'><h2>Registration not found</h2></div>";
    include '../includes/footer.php';
    exit;
}

if ($registration['payment_status'] == 'paid') {
    echo "<div class='container mt-4'>
            <div class='card'>
                <div class='card-body text-center'>
                    <h2 class='text-success'>Payment Already Completed</h2>
                    <p>You have already paid for this event.</p>
                    <a href='../profile.php' class='btn btn-primary'>Go to Profile</a>
                </div>
            </div>
          </div>";
    include '../includes/footer.php';
    exit;
}
?>

<div class="container" style="margin-top: 2rem; max-width: 800px;">
    <h1 class="mb-4">Checkout</h1>

    <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Order Summary -->
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-4">Order Summary</h3>
                <div class="flex gap-4 mb-4">
                    <img src="<?php echo $registration['image_path'] ? '../uploads/' . $registration['image_path'] : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'; ?>"
                        alt="Event" style="width: 80px; height: 80px; object-fit: cover; border-radius: var(--radius);">
                    <div>
                        <h4 style="font-size: 1.1rem; margin-bottom: 0.25rem;">
                            <?php echo htmlspecialchars($registration['title']); ?>
                        </h4>
                        <p class="text-muted" style="font-size: 0.9rem;">
                            <?php echo date('M d, Y', strtotime($registration['event_date'])); ?>
                        </p>
                    </div>
                </div>

                <hr style="margin: 1.5rem 0; border-top: 1px solid var(--border-color);">

                <div class="flex justify-between items-center mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>$<?php echo number_format($registration['price'], 2); ?></span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-muted">Processing Fee</span>
                    <span>$0.00</span>
                </div>
                <div class="flex justify-between items-center" style="font-size: 1.25rem; font-weight: 700;">
                    <span>Total</span>
                    <span
                        style="color: var(--primary-color);">$<?php echo number_format($registration['price'], 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-4">Payment Method</h3>

                <form action="process_payment.php" method="POST">
                    <input type="hidden" name="reg_id" value="<?php echo $reg_id; ?>">
                    <input type="hidden" name="amount" value="<?php echo $registration['price']; ?>">

                    <div class="form-group">
                        <label class="category-link mb-2"
                            style="cursor: pointer; border: 1px solid var(--border-color);">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_method" value="ewallet" checked>
                                <div>
                                    <div style="font-weight: 600;">E-Wallet</div>
                                    <div class="text-muted" style="font-size: 0.85rem;">Touch 'n Go, GrabPay, Boost
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="category-link" style="cursor: pointer; border: 1px solid var(--border-color);">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_method" value="fpx">
                                <div>
                                    <div style="font-weight: 600;">FPX Online Banking</div>
                                    <div class="text-muted" style="font-size: 0.85rem;">Maybank2u, CIMB Clicks, etc.
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-4" style="padding: 1rem;">
                        Pay $<?php echo number_format($registration['price'], 2); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>