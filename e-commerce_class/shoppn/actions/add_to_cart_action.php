<?php

require "../core/core.php";
require_once "../controllers/CartController.php";
require_once "../controllers/ProductController.php";

$back = '../index.php';

if (!empty($_SERVER['HTTP_REFERER'])) {
    $refHost  = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    $thisHost = parse_url('//' . $_SERVER['HTTP_HOST'], PHP_URL_HOST);

    if ($refHost && $refHost === $thisHost) {
        $back = $_SERVER['HTTP_REFERER'];
    }
}

$id = filter_var($_GET['add_cart'] ?? '', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $_SESSION['cart_error'] = 'Invalid product.';
    redirect($back);
    exit;
}

$productController = new ProductController();

if (!$productController->getProductById($id)) {
    $_SESSION['cart_error'] = 'That product could not be found.';
    redirect($back);
    exit;
}

$cartController = new CartController();
$ip = get_ip();

if ($cartController->isInCart($id, $ip)) {
    $_SESSION['cart_error'] = 'This item is already in your cart. Go to cart to update quantity.';
    redirect($back);
    exit;
}

$customerId = $_SESSION['customer_id'] ?? null;

if ($cartController->addToCart($id, 1, $ip, $customerId)) {
    $_SESSION['cart_success'] = 'Item added to your cart.';
} else {
    $_SESSION['cart_error'] = 'Could not add the item to your cart.';
}

redirect($back);
exit;