<?php

require "../core/core.php";
require_once "../controllers/CartController.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/cart.php');
    exit;
}

$ids = $_POST['remove'] ?? [];

if (!is_array($ids) || empty($ids)) {
    $_SESSION['cart_error'] = 'Select at least one item to remove.';
    redirect('../views/cart.php');
    exit;
}

$cartController = new CartController();
$ip = get_ip();
$removed = 0;

foreach ($ids as $rawId) {
    $id = filter_var($rawId, FILTER_VALIDATE_INT);

    if ($id && $id > 0 && $cartController->removeFromCart($id, $ip)) {
        $removed++;
    }
}

if ($removed > 0) {
    $_SESSION['cart_success'] = $removed . ' item(s) removed from your cart.';
} else {
    $_SESSION['cart_error'] = 'Could not remove the selected items.';
}

redirect('../views/cart.php');
exit;