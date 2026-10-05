<?php

require "../core/core.php";
require_once "../controllers/ProductController.php";

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/admin/product.php');
    exit;
}

$cat      = filter_var($_POST['product_cat'] ?? '', FILTER_VALIDATE_INT);
$brand    = filter_var($_POST['product_brand'] ?? '', FILTER_VALIDATE_INT);
$title    = trim(strip_tags($_POST['product_title'] ?? ''));
$price    = filter_var($_POST['product_price'] ?? '', FILTER_VALIDATE_FLOAT);
$desc     = trim(strip_tags($_POST['product_desc'] ?? ''));
$keywords = trim(strip_tags($_POST['product_keywords'] ?? ''));

if (!$cat || !$brand || $title === '' || strlen($title) > 200 || $price === false || $price <= 0) {
    $_SESSION['error'] = 'Please fill in title, price, category and brand correctly.';
    redirect('../views/admin/product.php');
    exit;
}

$filename = '';
if (!empty($_FILES['product_image']['name'])) {
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES['product_image']['tmp_name']);
    finfo_close($finfo);

    if ($_FILES['product_image']['error'] !== UPLOAD_ERR_OK
        || !in_array($mime, $allowed)
        || $_FILES['product_image']['size'] > 2 * 1024 * 1024) {
        $_SESSION['error'] = 'Image must be JPG, PNG, GIF or WEBP and under 2MB.';
        redirect('../views/admin/product.php');
        exit;
    }

    $filename = uniqid() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', basename($_FILES['product_image']['name']));
    move_uploaded_file($_FILES['product_image']['tmp_name'], '../images/products/' . $filename);
}

$controller = new ProductController();
$result = $controller->addProduct($cat, $brand, $title, $price, $desc, $filename, $keywords);

if ($result['success']) {
    $_SESSION['success'] = 'Product added.';
} else {
    $_SESSION['error'] = $result['error'];
}

redirect('../views/admin/product.php');
exit;