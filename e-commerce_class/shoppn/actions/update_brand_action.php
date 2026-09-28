<?php

require "../core/core.php";
require_once "../controllers/CustomerController.php";




require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/admin/brand.php');
}

$brand_id   = filter_var($_POST['brand_id'] ?? '', FILTER_VALIDATE_INT);
$brand_name = trim(strip_tags($_POST['brand_name'] ?? ''));

if (!$brand_id || $brand_id <= 0) {
    $_SESSION['error'] = 'Invalid brand.';
    redirect('../views/admin/brand.php');
}

if (empty($brand_name)) {
    $_SESSION['error'] = 'Brand name cannot be empty.';
    redirect('../views/admin/brand.php');
}

$controller = new ProductController();
$success = $controller->updateBrand($brand_id, $brand_name);

if ($success) {
    $_SESSION['success'] = 'Brand updated.';
} else {
    $_SESSION['error'] = 'Something went wrong. Please try again.';
}

redirect('../views/admin/brand.php');

?>