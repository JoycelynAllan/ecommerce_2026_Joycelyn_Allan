<?php

require "../core/core.php";
require_once "../core/paystack_config.php";
require_once "../controllers/CartController.php";

require_login();

$cartController = new CartController();
$ip    = get_ip();
$items = $cartController->getCartItems($ip);
$total = $cartController->getCartTotal($ip);

if (empty($items)) {
    $_SESSION['cart_error'] = 'Your cart is empty.';
    redirect('cart.php');
    exit;
}

$amount = (int)round($total * 100);
$email  = $_SESSION['customer_email'] ?? '';

include 'layout/header.php';
?>

<main>
    <h2>Payment</h2>

    <div class="checkout-layout">
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_title']) ?></td>
                            <td><?= (int)$item['qty'] ?></td>
                            <td>$<?= number_format($item['product_price'], 2) ?></td>
                            <td>$<?= number_format($item['qty'] * $item['product_price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="cart-subtotal">
                        <td colspan="3">Total</td>
                        <td>$<?= number_format($total, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <aside class="summary-card">
            <h3>Pay with Paystack</h3>
            <div class="summary-row"><span>Paying as</span><span><?= htmlspecialchars($email) ?></span></div>
            <div class="summary-row summary-total"><span>Amount</span><span>$<?= number_format($total, 2) ?></span></div>
            <button type="button" id="pay-btn" onclick="payWithPaystack()">Pay Now</button>
            <a class="btn-link" href="checkout.php">Back to Checkout</a>
        </aside>
    </div>
</main>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
    function payWithPaystack() {
        var handler = PaystackPop.setup({
            key: <?= json_encode(PAYSTACK_PUBLIC_KEY) ?>,
            email: <?= json_encode($email) ?>,
            amount: <?= (int)$amount ?>,
            currency: <?= json_encode(PAYSTACK_CURRENCY) ?>,
            callback: function (response) {
                window.location = <?= json_encode($base . '/actions/process_payment_action.php?ref=') ?> + encodeURIComponent(response.reference);
            },
            onClose: function () {
                alert('Payment cancelled.');
            }
        });

        handler.openIframe();
    }
</script>

<?php include 'layout/footer.php'; ?>