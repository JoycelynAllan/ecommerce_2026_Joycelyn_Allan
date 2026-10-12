<?php
$img = !empty($product['product_image'])
    ? BASE_URL . '/images/products/' . rawurlencode($product['product_image']) : '';
?>
<div class="product-card">
    <?php if ($img): ?>
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($product['product_title']) ?>">
    <?php else: ?>
        <div class="no-image">No image</div>
    <?php endif; ?>
    <h4><?= htmlspecialchars($product['product_title']) ?></h4>
    <p class="price">GH₵ <?= number_format((float)$product['product_price'], 2) ?></p>
    <div class="card-actions">
        <a href="<?= BASE_URL ?>/views/single_product.php?pro_id=<?= (int)$product['product_id'] ?>">Details</a>
        <a href="<?= BASE_URL ?>/actions/add_to_cart_action.php?add_cart=<?= (int)$product['product_id'] ?>" class="btn">Add to Cart</a>
    </div>
</div>