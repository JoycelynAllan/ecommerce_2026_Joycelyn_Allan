<?php

require "../core/core.php";
require_once "../controllers/CartController.php";

require_login();

$orderId = filter_var($_GET['order_id'] ?? '', FILTER_VALIDATE_INT);

if (!$orderId || $orderId < 1) {
    redirect('../index.php');
    exit;
}

$cartController = new CartController();
$result = $cartController->getOrderById($orderId, is_admin() ? null : (int)$_SESSION['customer_id']);

if (!$result) {
    redirect('../index.php');
    exit;
}

$order = $result['order'];
$items = $result['items'];

include 'layout/header.php';
?>

<main>
    <div class="status-card">
        <div class="status-icon ok">&#10003;</div>
        <h2>Payment Successful</h2>
        <p>Thank you, <?= htmlspecialchars($order['customer_name']) ?>. Your order has been recorded.</p>
    </div>

    <h3>Invoice</h3>

    <dl class="invoice-meta">
        <div>
            <dt>Invoice number</dt>
            <dd>#<?= (int)$order['invoice_no'] ?></dd>
        </div>
        <div>
            <dt>Order number</dt>
            <dd><?= (int)$order['order_id'] ?></dd>
        </div>
        <div>
            <dt>Date</dt>
            <dd><?= htmlspecialchars(date('j F Y, g:i A', strtotime($order['order_date']))) ?></dd>
        </div>
        <div>
            <dt>Status</dt>
            <dd><?= htmlspecialchars(ucfirst($order['order_status'])) ?></dd>
        </div>
    </dl>

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
                <td colspan="3">Total paid</td>
                <td>$<?= number_format($order['total_amount'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="cart-actions no-print">
        <button type="button" onclick="window.print()">Print Invoice</button>
        <a class="btn-link" href="../index.php">Continue Shopping</a>
        <a class="btn-link" href="account/my_account.php">My Account</a>
    </div>
</main>

<?php include 'layout/footer.php'; ?>