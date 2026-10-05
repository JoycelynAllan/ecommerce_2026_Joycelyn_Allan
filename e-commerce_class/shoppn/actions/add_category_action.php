<?php

require "../core/core.php";
require_once "../controllers/ProductController.php";

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/admin/category.php');
}

$cat_name = trim(strip_tags($_POST['cat_name'] ?? ''));

if (empty($cat_name)) {
    $_SESSION['error'] = 'Category name cannot be empty.';
    redirect('../views/admin/category.php');
}

$controller = new ProductController();
$success = $controller->addCategory($cat_name);

if ($success) {
    $_SESSION['success'] = 'Category added.';
} else {
    $_SESSION['error'] = 'Something went wrong. Please try again.';
}

redirect('../views/admin/category.php');

?>