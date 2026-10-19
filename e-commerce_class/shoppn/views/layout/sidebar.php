<?php
require_once __DIR__ . '/../../controllers/ProductController.php';

$sidebarController = new ProductController();
$sidebarCategories = $sidebarController->getAllCategories();
$sidebarBrands     = $sidebarController->getAllBrands();

$activeCat   = filter_var($_GET['cat'] ?? '', FILTER_VALIDATE_INT);
$activeBrand = filter_var($_GET['brand'] ?? '', FILTER_VALIDATE_INT);
?>

<aside class="sidebar">
    <h3>Categories</h3>
    <ul>
        <?php if (empty($sidebarCategories)): ?>
            <li>No categories yet</li>
        <?php else: ?>
            <?php foreach ($sidebarCategories as $c): ?>
                <li>
                    <a href="index.php?cat=<?= (int)$c['cat_id'] ?>"
                       class="<?= ($activeCat === (int)$c['cat_id']) ? 'active' : '' ?>">
                        <?= htmlspecialchars($c['cat_name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <h3>Brands</h3>
    <ul>
        <?php if (empty($sidebarBrands)): ?>
            <li>No brands yet</li>
        <?php else: ?>
            <?php foreach ($sidebarBrands as $b): ?>
                <li>
                    <a href="index.php?brand=<?= (int)$b['brand_id'] ?>"
                       class="<?= ($activeBrand === (int)$b['brand_id']) ? 'active' : '' ?>">
                        <?= htmlspecialchars($b['brand_name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <p><a href="index.php">Show all</a></p>
</aside>