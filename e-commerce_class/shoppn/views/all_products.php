<?php

require "../core/core.php";
require_once "../controllers/ProductController.php";

$controller = new ProductController();
$products = $controller->getAllProducts();

include 'layout/header.php';
?>

<main>
    <h2>All Products</h2>

    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <p><?= count($products) ?> product(s)</p>
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
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include 'layout/footer.php'; ?>