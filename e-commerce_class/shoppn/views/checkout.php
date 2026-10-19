<?php

require "../core/core.php";
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

include 'layout/header.php';
?>

<main>
    <h2>Checkout</h2>

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
                            <td>
                                <div class="cart-product">
                                    <?php if (!empty($item['product_image'])): ?>
                                        <img class="cart-thumb" src="../images/products/<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_title']) ?>">
                                    <?php else: ?>
                                        <div class="cart-thumb"></div>
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($item['product_title']) ?></span>
                                </div>
                            </td>
                            <td><?= (int)$item['qty'] ?></td>
                            <td>$<?= number_format($item['product_price'], 2) ?></td>
                            <td>$<?= number_format($item['qty'] * $item['product_price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <aside class="summary-card">
            <h3>Order Summary</h3>
            <div class="summary-row"><span>Items</span><span><?= count($items) ?></span></div>
            <div class="summary-row"><span>Name</span><span><?= htmlspecialchars($_SESSION['customer_name'] ?? '') ?></span></div>
            <div class="summary-row"><span>Email</span><span><?= htmlspecialchars($_SESSION['customer_email'] ?? '') ?></span></div>
            <div class="summary-row summary-total"><span>Total</span><span>$<?= number_format($total, 2) ?></span></div>
            <a class="btn-link" href="payment.php">Proceed to Payment</a>
            <a class="btn-link" href="cart.php">Back to Cart</a>
        </aside>
    </div>
</main>

<?php include 'layout/footer.php'; ?>