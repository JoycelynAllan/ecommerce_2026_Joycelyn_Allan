<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$pro_id = filter_input(INPUT_GET, 'pro_id', FILTER_VALIDATE_INT);
if (!$pro_id || $pro_id < 1) redirect(BASE_URL . '/index.php');

$product = (new ProductController())->getProductById($pro_id);
if (!$product) {
    $_SESSION['error'] = 'That product does not exist.';
    redirect(BASE_URL . '/index.php');
}
include __DIR__ . '/layout/header.php';
?>
