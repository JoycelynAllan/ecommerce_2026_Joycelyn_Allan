<?php

require_once "controllers/ProductController.php";

include 'views/layout/header.php';

$controller = new ProductController();

if (isset($_GET['cat']) && filter_var($_GET['cat'], FILTER_VALIDATE_INT)) {
    $products = $controller->getProductsByCategory((int)$_GET['cat']);
} elseif (isset($_GET['brand']) && filter_var($_GET['brand'], FILTER_VALIDATE_INT)) {
    $products = $controller->getProductsByBrand((int)$_GET['brand']);
} else {
    $products = $controller->getFeaturedProducts(6);
}

?>

<main>
    <h2>Welcome to Shoppn</h2>
    <p>Our e-commerce website is working.</p>

    <div class="cart-track">
        <div class="cart-runner">
            <svg viewBox="0 0 170 100" width="170" height="100" xmlns="http://www.w3.org/2000/svg">
                <!-- person -->
                <circle cx="40" cy="20" r="9" fill="#6cc5c0"/>
                <line x1="40" y1="29" x2="44" y2="60" stroke="#0F9B8E" stroke-width="7" stroke-linecap="round"/>
                <line x1="42" y1="38" x2="84" y2="47" stroke="#0F9B8E" stroke-width="5" stroke-linecap="round"/>
                <line class="leg leg1" x1="44" y1="60" x2="44" y2="90" stroke="#0c7d72" stroke-width="6" stroke-linecap="round"/>
                <line class="leg leg2" x1="44" y1="60" x2="44" y2="90" stroke="#6cc5c0" stroke-width="6" stroke-linecap="round"/>
                <!-- cart -->
                <line x1="84" y1="47" x2="92" y2="40" stroke="#0F9B8E" stroke-width="4" stroke-linecap="round"/>
                <path d="M90 40 L152 40 L144 72 L98 72 Z" fill="#b9e4e1" stroke="#0F9B8E" stroke-width="4" stroke-linejoin="round"/>
                <rect x="104" y="26" width="14" height="14" rx="2" fill="#6cc5c0"/>
                <rect x="122" y="30" width="16" height="10" rx="2" fill="#0F9B8E"/>
                <g class="wheel" style="transform-origin:106px 84px">
                    <circle cx="106" cy="84" r="8" fill="#fff" stroke="#0c7d72" stroke-width="3"/>
                    <line x1="106" y1="78" x2="106" y2="90" stroke="#0c7d72" stroke-width="2"/>
                </g>
                <g class="wheel" style="transform-origin:138px 84px">
                    <circle cx="138" cy="84" r="8" fill="#fff" stroke="#0c7d72" stroke-width="3"/>
                    <line x1="138" y1="78" x2="138" y2="90" stroke="#0c7d72" stroke-width="2"/>
                </g>
            </svg>
        </div>
    </div>

    <h3>Featured Products</h3>

    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <?php if (!empty($p['product_image'])): ?>
                        <img src="images/products/<?= htmlspecialchars($p['product_image']) ?>" alt="<?= htmlspecialchars($p['product_title']) ?>">
                    <?php else: ?>
                        <div class="no-image">No image</div>
                    <?php endif; ?>
                    <h4><?= htmlspecialchars($p['product_title']) ?></h4>
                    <p class="price">$<?= number_format($p['product_price'], 2) ?></p>
                    <a href="views/single_product.php?pro_id=<?= (int)$p['product_id'] ?>">Details</a>
                    <a href="actions/add_to_cart_action.php?add_cart=<?= (int)$p['product_id'] ?>">Add to Cart</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include 'views/layout/footer.php'; ?>