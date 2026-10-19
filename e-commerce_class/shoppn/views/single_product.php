<?php

require "../core/core.php";
require_once "../controllers/ProductController.php";

$id = filter_var($_GET['pro_id'] ?? '', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    redirect('../index.php');
    exit;
}

$controller = new ProductController();
$product = $controller->getProductById($id);

if (!$product) {
    redirect('../index.php');
    exit;
}

$related = [];

foreach ($controller->getProductsByCategory((int)$product['product_cat']) as $candidate) {
    if ((int)$candidate['product_id'] !== (int)$product['product_id']) {
        $related[] = $candidate;
    }

    if (count($related) === 4) {
        break;
    }
}

$keywords = array_filter(array_map('trim', explode(',', (string)$product['product_keywords'])));

include 'layout/header.php';
?>

<main>
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= $base ?>/index.php">Home</a>
        <span>/</span>
        <a href="<?= $base ?>/index.php?cat=<?= (int)$product['product_cat'] ?>"><?= htmlspecialchars($product['cat_name']) ?></a>
        <span>/</span>
        <strong><?= htmlspecialchars($product['product_title']) ?></strong>
    </nav>

    <section class="pd">
        <div class="pd-media">
            <?php if (!empty($product['product_image'])): ?>
                <img src="<?= $base ?>/images/products/<?= htmlspecialchars($product['product_image']) ?>"
                     alt="<?= htmlspecialchars($product['product_title']) ?>"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="no-image" style="display:none;">Image unavailable</div>
            <?php else: ?>
                <div class="no-image">No image</div>
            <?php endif; ?>
        </div>

        <div class="pd-info">
            <a class="pd-tag" href="<?= $base ?>/index.php?cat=<?= (int)$product['product_cat'] ?>"><?= htmlspecialchars($product['cat_name']) ?></a>
            <h1 class="pd-title"><?= htmlspecialchars($product['product_title']) ?></h1>
            <p class="pd-brand">Brand: <a href="<?= $base ?>/index.php?brand=<?= (int)$product['product_brand'] ?>"><?= htmlspecialchars($product['brand_name']) ?></a></p>
            <p class="pd-price">$<?= number_format($product['product_price'], 2) ?></p>

            <?php if (!empty($product['product_desc'])): ?>
                <p class="pd-desc"><?= nl2br(htmlspecialchars($product['product_desc'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($keywords)): ?>
                <div class="pd-tags">
                    <?php foreach ($keywords as $word): ?>
                        <span><?= htmlspecialchars($word) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="pd-actions">
                <?php if (is_admin()): ?>
                    <a class="btn-add" href="<?= $base ?>/views/admin/product.php?edit_id=<?= (int)$product['product_id'] ?>">Edit Product</a>
                <?php else: ?>
                    <a class="btn-add" href="<?= $base ?>/actions/add_to_cart_action.php?add_cart=<?= (int)$product['product_id'] ?>">Add to Cart</a>
                <?php endif; ?>
                <a class="btn-outline" href="<?= $base ?>/index.php">Back to shop</a>
            </div>
        </div>
    </section>

    <?php if (!empty($related)): ?>
        <h3>You may also like</h3>
        <div class="product-grid">
            <?php foreach ($related as $p): ?>
                <article class="product-card">
                    <a class="card-media" href="<?= $base ?>/views/single_product.php?pro_id=<?= (int)$p['product_id'] ?>">
                        <?php if (!empty($p['product_image'])): ?>
                            <img src="<?= $base ?>/images/products/<?= htmlspecialchars($p['product_image']) ?>" alt="<?= htmlspecialchars($p['product_title']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="no-image">No image</div>
                        <?php endif; ?>
                    </a>

                    <div class="card-body">
                        <?php if (!empty($p['cat_name'])): ?>
                            <span class="card-tag"><?= htmlspecialchars($p['cat_name']) ?></span>
                        <?php endif; ?>
                        <h4><a class="card-title" href="<?= $base ?>/views/single_product.php?pro_id=<?= (int)$p['product_id'] ?>"><?= htmlspecialchars($p['product_title']) ?></a></h4>
                        <p class="price">$<?= number_format($p['product_price'], 2) ?></p>
                    </div>

                    <div class="card-actions">
                        <?php if (is_admin()): ?>
                            <a class="btn-add" href="<?= $base ?>/views/admin/product.php?edit_id=<?= (int)$p['product_id'] ?>">Edit</a>
                        <?php else: ?>
                            <a class="btn-add" href="<?= $base ?>/actions/add_to_cart_action.php?add_cart=<?= (int)$p['product_id'] ?>">Add to Cart</a>
                        <?php endif; ?>
                        <a class="btn-outline" href="<?= $base ?>/views/single_product.php?pro_id=<?= (int)$p['product_id'] ?>">Details</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include 'layout/footer.php'; ?>