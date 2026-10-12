<?php

require "../core/core.php";
require_once "../controllers/CartController.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/cart.php');
    exit;
}

$ids  = $_POST['update'] ?? [];
$qtys = $_POST['qty'] ?? [];

if (!is_array($ids) || !is_array($qtys) || empty($ids)) {
    $_SESSION['cart_error'] = 'Nothing to update.';
    redirect('../views/cart.php');
    exit;
}

$cartController = new CartController();
$ip = get_ip();
$updated = 0;
$invalid = 0;

foreach ($ids as $i => $rawId) {
    $id  = filter_var($rawId, FILTER_VALIDATE_INT);
    $qty = filter_var($qtys[$i] ?? '', FILTER_VALIDATE_INT);

    if (!$id || $id < 1 || !$qty || $qty < 1 || $qty > 99) {
        $invalid++;
        continue;
    }

    if ($cartController->updateQty($id, $qty, $ip)) {
        $updated++;
    }
}

if ($invalid > 0) {
    $_SESSION['cart_error'] = 'Quantities must be whole numbers from 1 to 99.';
}

if ($updated > 0) {
    $_SESSION['cart_success'] = 'Cart updated.';
}

redirect('../views/cart.php');
exit;