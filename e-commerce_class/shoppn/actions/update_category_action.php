<?php

require "../core/core.php";
require_once "../controllers/ProductController.php";

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/admin/category.php');
    exit;
}

$id   = filter_var($_POST['cat_id'] ?? '', FILTER_VALIDATE_INT);
$name = trim(strip_tags($_POST['cat_name'] ?? ''));

if (!$id || $id < 1) {
    $_SESSION['error'] = 'Invalid category.';
    redirect('../views/admin/category.php');
    exit;
}

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Category name is required (max 100 characters).';
    redirect('../views/admin/category.php?edit_id=' . $id);
    exit;
}

$controller = new ProductController();
$result = $controller->updateCategory($id, $name);

if ($result['success']) {
    $_SESSION['success'] = 'Category updated.';
} else {
    $_SESSION['error'] = $result['error'];
}

redirect('../views/admin/category.php');
exit;