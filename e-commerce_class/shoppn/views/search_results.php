<?php

require "../core/core.php";
require_once "../controllers/ProductController.php";

$query = trim(strip_tags($_GET['user_query'] ?? ''));

$controller = new ProductController();
$products = ($query !== '') ? $controller->searchProducts($query) : [];

include 'layout/header.php';
?>

<main>
    <h2>Search Results</h2>

    <?php if ($query === ''): ?>
        <p>Type something in the search box to find products.</p>
    <?php elseif (empty($products)): ?>
        <p>No products found for "<?= htmlspecialchars($query) ?>". Try a different word, or browse <a href="all_products.php">all products</a>.</p>
    <?php else: ?>
        <p><?= count($products) ?> result(s) for "<?= htmlspecialchars($query) ?>"</p>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <?php if (!empty($p['product_image'])): ?>
                        <img src="../images/products/<?= htmlspecialchars($p['product_image']) ?>" alt="<?= htmlspecialchars($p['product_title']) ?>">
                    <?php else: ?>
                        <div class="no-image">No image</div>
                    <?php endif; ?>
                    <h4><?= htmlspecialchars($p['product_title']) ?></h4>
                    <p class="price">$<?= number_format($p['product_price'], 2) ?></p>
                    <a href="single_product.php?pro_id=<?= (int)$p['product_id'] ?>">Details</a>
                    <a href="../actions/add_to_cart_action.php?add_cart=<?= (int)$p['product_id'] ?>">Add to Cart</a>
                    <?php if (is_admin()): ?>
                        <a href="admin/product.php?edit_id=<?= (int)$p['product_id'] ?>">Edit</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include 'layout/footer.php'; ?>