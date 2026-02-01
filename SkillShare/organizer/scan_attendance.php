<?php
// Include database configuration
require_once '../config/db.php';
// Include header for layout
include '../includes/header.php';

// Authentication Check: Ensure user is logged in as 'organizer'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../login.php");
    exit;
}
?>

<!-- Main Container -->
<div class="container" style="margin-top: 2rem; max-width: 600px;">
    <!-- Scanner Card -->
    <div class="card">
        <div class="card-body text-center">
            <h2 class="mb-4">Scan Attendance</h2>
            <p class="text-muted mb-4">Scan the student's QR code to check them in.</p>

            <!-- QR Code Reader Container -->
            <div id="reader" style="width: 100%; min-height: 300px; background: #f3f4f6; border-radius: var(--radius);">
            </div>

            <!-- Result Display Area -->
            <div id="result" class="mt-4" style="display: none;">
                <div id="result-message" style="padding: 1rem; border-radius: var(--radius);"></div>
            </div>

            <a href="../profile.php?view=my_events" class="btn btn-outline mt-4">Back to Dashboard</a>
        </div>
    </div>
</div>

<!-- HTML5-QRCode Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // Handle the scanned code as you like, for example:
        console.log(`Code matched = ${decodedText}`, decodedResult);

        // Stop scanning temporarily
        html5QrcodeScanner.clear();

        // Send to backend
        fetch('checkin_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ token: decodedText }),
        })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('result');
                const messageDiv = document.getElementById('result-message');
                resultDiv.style.display = 'block';

                if (data.success) {
                    messageDiv.style.backgroundColor = '#dcfce7';
                    messageDiv.style.color = '#166534';
                    messageDiv.innerHTML = `<strong>Success!</strong> ${data.message}`;
                } else {
                    messageDiv.style.backgroundColor = '#fee2e2';
                    messageDiv.style.color = '#991b1b';
                    messageDiv.innerHTML = `<strong>Error!</strong> ${data.message}`;
                }

                // Restart scanner after 3 seconds
                setTimeout(() => {
                    resultDiv.style.display = 'none';
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                }, 3000);
            })
            .catch((error) => {
                console.error('Error:', error);
                alert("An error occurred. Please try again.");
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            });
    }

    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning.
        // for example:
        // console.warn(`Code scan error = ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: { width: 250, height: 250 } },
        /* verbose= */ false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>

<?php include '../includes/footer.php'; ?>