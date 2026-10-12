<?php

require "../core/core.php";
require_once "../controllers/CartController.php";

$cartController = new CartController();
$ip = get_ip();
$items = $cartController->getCartItems($ip);
$subtotal = $cartController->getCartTotal($ip);

include 'layout/header.php';
?>

<main>
    <h2>Your Cart</h2>

    <?php if (empty($items)): ?>
        <p>Your cart is empty.</p>
        <a class="btn-link" href="../index.php">Continue Shopping</a>
    <?php else: ?>

        <form id="remove-form" method="POST" action="../actions/remove_from_cart_action.php" hidden></form>
        <form id="update-form" method="POST" action="../actions/update_qty_action.php" hidden></form>

        <table>
            <thead>
                <tr>
                    <th>Remove</th>
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
                            <input type="checkbox" form="remove-form" name="remove[]" value="<?= (int)$item['p_id'] ?>">
                        </td>
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
                        <td>
                            <input class="cart-qty" type="text" form="update-form" name="qty[]" value="<?= (int)$item['qty'] ?>">
                            <input type="hidden" form="update-form" name="update[]" value="<?= (int)$item['p_id'] ?>">
                        </td>
                        <td>$<?= number_format($item['product_price'], 2) ?></td>
                        <td>$<?= number_format($item['qty'] * $item['product_price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="cart-subtotal">
                    <td colspan="4">Subtotal</td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="cart-actions">
            <button type="submit" form="remove-form">Remove from Cart</button>
            <button type="submit" form="update-form">Update Quantity</button>
            <a class="btn-link" href="../index.php">Continue Shopping</a>
            <a class="btn-link" href="checkout.php">Checkout</a>
        </div>

    <?php endif; ?>
</main>

<?php include 'layout/footer.php'; ?>