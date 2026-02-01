<?php
require_once '../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$transaction_id = isset($_GET['tid']) ? htmlspecialchars($_GET['tid']) : '';
$amount = isset($_GET['amount']) ? htmlspecialchars($_GET['amount']) : '';

$registration_id = null;
if ($transaction_id) {
    $stmt = $pdo->prepare("SELECT registration_id FROM payments WHERE transaction_id = ?");
    $stmt->execute([$transaction_id]);
    $payment = $stmt->fetch();
    if ($payment) {
        $registration_id = $payment['registration_id'];
    }
}

include '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center shadow">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="green"
                            class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                    </div>
                    <h2 class="card-title text-success mb-3">Payment Successful!</h2>
                    <p class="lead">Thank you for your payment. Your registration is now confirmed.</p>

                    <?php if ($transaction_id): ?>
                        <div class="alert alert-light border d-inline-block text-left mt-3">
                            <p class="mb-1"><strong>Transaction ID:</strong> <?php echo $transaction_id; ?></p>
                            <?php if ($amount): ?>
                                <p class="mb-0"><strong>Amount Paid:</strong> $<?php echo number_format((float) $amount, 2); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <a href="../profile.php?view=my_events" class="btn btn-primary mr-2">Go to My Events</a>
                        <?php if ($registration_id): ?>
                            <a href="receipt.php?reg_id=<?php echo $registration_id; ?>"
                                class="btn btn-outline-secondary">View My
                                Receipt</a>
                        <?php else: ?>
                            <a href="../events.php" class="btn btn-outline-secondary">Browse More Workshops</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>