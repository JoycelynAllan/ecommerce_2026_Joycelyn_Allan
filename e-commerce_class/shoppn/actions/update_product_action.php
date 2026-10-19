<?php

require "../core/core.php";
require_once "../controllers/ProductController.php";

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/admin/product.php');
    exit;
}

$id       = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT);
$cat      = filter_var($_POST['product_cat'] ?? '', FILTER_VALIDATE_INT);
$brand    = filter_var($_POST['product_brand'] ?? '', FILTER_VALIDATE_INT);
$title    = trim(strip_tags($_POST['product_title'] ?? ''));
$price    = filter_var($_POST['product_price'] ?? '', FILTER_VALIDATE_FLOAT);
$desc     = trim(strip_tags($_POST['product_desc'] ?? ''));
$keywords = trim(strip_tags($_POST['product_keywords'] ?? ''));

if (!$id || !$cat || !$brand || $title === '' || strlen($title) > 200 || $price === false || $price <= 0) {
    $_SESSION['error'] = 'Please fill in title, price, category and brand correctly.';
    redirect('../views/admin/product.php' . ($id ? '?edit_id=' . $id : ''));
    exit;
}

if (strlen($desc) > 2000 || strlen($keywords) > 255) {
    $_SESSION['error'] = 'Description must be under 2000 characters and keywords under 255.';
    redirect('../views/admin/product.php?edit_id=' . $id);
    exit;
}

$controller = new ProductController();

$existing = $controller->getProductById($id);
$filename = $existing ? (string)$existing['product_image'] : basename($_POST['existing_image'] ?? '');
$oldFilename = $filename;

if (!empty($_FILES['product_image']['name'])) {
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    if ($_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'The image could not be uploaded. Please try again with a smaller file.';
        redirect('../views/admin/product.php?edit_id=' . $id);
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES['product_image']['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowed[$mime]) || $_FILES['product_image']['size'] > 2 * 1024 * 1024) {
        $_SESSION['error'] = 'Image must be JPG, PNG, GIF or WEBP and under 2MB.';
        redirect('../views/admin/product.php?edit_id=' . $id);
        exit;
    }

    $uploadDir = __DIR__ . '/../images/products/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $baseName = pathinfo($_FILES['product_image']['name'], PATHINFO_FILENAME);
    $baseName = preg_replace('/[^A-Za-z0-9_-]/', '_', $baseName);
    $baseName = substr($baseName, 0, 40);
    $filename = uniqid() . '_' . $baseName . '.' . $allowed[$mime];

    if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadDir . $filename)) {
        $_SESSION['error'] = 'The image could not be saved on the server. Check that images/products is writable.';
        redirect('../views/admin/product.php?edit_id=' . $id);
        exit;
    }
}

$result = $controller->updateProduct($id, $cat, $brand, $title, $price, $desc, $filename, $keywords);

if ($result['success']) {
    if ($oldFilename !== '' && $oldFilename !== $filename) {
        $oldPath = __DIR__ . '/../images/products/' . basename($oldFilename);

        if (is_file($oldPath)) {
            unlink($oldPath);
        }
    }

    $_SESSION['success'] = 'Product updated.';
} else {
    $_SESSION['error'] = $result['error'];
}

redirect('../views/admin/product.php');
exit;