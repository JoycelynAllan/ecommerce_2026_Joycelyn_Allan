<?php

require "../core/core.php";

require_login();

if (is_admin()) {
    $_SESSION['cart_error'] = 'Administrators do not have a cart.';
    redirect('../index.php');
    exit;
}

require_once "../controllers/CartController.php";

$cartController = new CartController();
$ip = get_ip();
$items = $cartController->getCartItems($ip);
$subtotal = $cartController->getCartTotal($ip);

include 'layout/header.php';
?>

<main>
    <div class="page-title-row">
        <h2>Your Cart</h2>
        <?php if (!empty($items)): ?>
            <span class="count-pill"><?= count($items) ?> item(s)</span>
        <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
        <div class="empty-state">
            <h3>Your cart is empty</h3>
            <p>Looks like you have not added anything yet.</p>
            <a class="btn-link" href="../index.php">Continue Shopping</a>
        </div>
    <?php else: ?>

        <form id="remove-form" method="POST" action="../actions/remove_from_cart_action.php" hidden></form>
        <form id="update-form" method="POST" action="../actions/update_qty_action.php" hidden></form>

        <div class="cart-layout">
            <section class="cart-list">
                <div class="cart-toolbar">
                    <label class="check">
                        <input type="checkbox" id="select-all">
                        Select all
                    </label>
                    <div class="toolbar-buttons">
                        <button type="submit" form="remove-form" class="btn-small btn-danger-outline">Remove selected</button>
                        <button type="submit" form="update-form" class="btn-small">Update quantities</button>
                    </div>
                </div>

                <?php foreach ($items as $item): ?>
                    <article class="cart-item">
                        <input type="checkbox" form="remove-form" name="remove[]" value="<?= (int)$item['p_id'] ?>" aria-label="Select <?= htmlspecialchars($item['product_title']) ?>">

                        <a class="cart-img" href="single_product.php?pro_id=<?= (int)$item['p_id'] ?>">
                            <?php if (!empty($item['product_image'])): ?>
                                <img src="../images/products/<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_title']) ?>">
                            <?php endif; ?>
                        </a>

                        <div class="cart-info">
                            <a class="cart-name" href="single_product.php?pro_id=<?= (int)$item['p_id'] ?>"><?= htmlspecialchars($item['product_title']) ?></a>
                            <span class="cart-unit">$<?= number_format($item['product_price'], 2) ?> each</span>
                        </div>

                        <div class="cart-qtybox">
                            <span>Qty</span>
                            <input class="cart-qty" type="text" inputmode="numeric" form="update-form" name="qty[]" value="<?= (int)$item['qty'] ?>" aria-label="Quantity">
                            <input type="hidden" form="update-form" name="update[]" value="<?= (int)$item['p_id'] ?>">
                        </div>

                        <div class="cart-line">$<?= number_format($item['qty'] * $item['product_price'], 2) ?></div>
                    </article>
                <?php endforeach; ?>
            </section>

            <aside class="summary-card">
                <h3>Order Summary</h3>
                <div class="summary-row"><span>Items</span><span><?= count($items) ?></span></div>
                <div class="summary-row"><span>Subtotal</span><span>$<?= number_format($subtotal, 2) ?></span></div>
                <div class="summary-row summary-total"><span>Total</span><span>$<?= number_format($subtotal, 2) ?></span></div>
                <a class="btn-link" href="checkout.php">Checkout</a>
                <a class="btn-outline-block" href="../index.php">Continue Shopping</a>
            </aside>
        </div>

        <script>
            document.getElementById('select-all').addEventListener('change', function () {
                var boxes = document.querySelectorAll('input[name="remove[]"]');

                for (var i = 0; i < boxes.length; i++) {
                    boxes[i].checked = this.checked;
                }
            });
        </script>

    <?php endif; ?>
</main>

<?php include 'layout/footer.php'; ?>