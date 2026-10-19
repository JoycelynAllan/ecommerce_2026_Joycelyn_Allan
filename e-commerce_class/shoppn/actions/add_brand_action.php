<?php

require "../core/core.php";
require_once "../controllers/ProductController.php";

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/admin/brand.php');
}

$brand_name = trim(strip_tags($_POST['brand_name'] ?? ''));

if (empty($brand_name)) {
    $_SESSION['error'] = 'Brand name cannot be empty.';
    redirect('../views/admin/brand.php');
}

$controller = new ProductController();
$success = $controller->addBrand($brand_name);

if ($success) {
    $_SESSION['success'] = 'Brand added.';
} else {
    $_SESSION['error'] = 'Something went wrong. Please try again.';
}

redirect('../views/admin/brand.php');

?>