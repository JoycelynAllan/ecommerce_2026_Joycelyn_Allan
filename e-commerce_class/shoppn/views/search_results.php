<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$user_query = substr(trim(strip_tags($_GET['user_query'] ?? '')), 0, 100);
$products   = (new ProductController())->searchProducts($user_query);

include __DIR__ . '/layout/header.php';
?>
<!-- same shop-layout + sidebar + product-grid loop as home.php,
     heading: Search results for "<?= htmlspecialchars($user_query) ?>" -->