<?php

require "../core/core.php";

require_login();

$message = $_SESSION['payment_error'] ?? 'Your payment could not be completed.';
unset($_SESSION['payment_error']);

include 'layout/header.php';
?>

<main>
    <div class="status-card">
        <div class="status-icon fail">&times;</div>
        <h2>Payment Failed</h2>
        <p><?= htmlspecialchars($message) ?></p>
        <div class="cart-actions" style="justify-content:center;">
            <a class="btn-link" href="payment.php">Try Again</a>
            <a class="btn-link" href="cart.php">Back to Cart</a>
        </div>
    </div>
</main>

<?php include 'layout/footer.php'; ?>