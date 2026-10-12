<?php

require "../../core/core.php";
require_once "../../controllers/CartController.php";
require_once "../../controllers/ProductController.php";

require_login();

$customer = null;
$customerControllerFile = __DIR__ . '/../../controllers/CustomerController.php';

if (file_exists($customerControllerFile)) {
    require_once $customerControllerFile;

    try {
        $customerController = new CustomerController();
        $customer = $customerController->getCustomerById((int)$_SESSION['customer_id']);
    } catch (Throwable $e) {
        $customer = null;
    }
}

$basicOnly = false;

if (!$customer) {
    $basicOnly = true;
    $customer = [
        'customer_name'    => $_SESSION['customer_name'] ?? '',
        'customer_email'   => $_SESSION['customer_email'] ?? '',
        'customer_country' => '',
        'customer_city'    => '',
        'customer_contact' => '',
        'customer_image'   => ''
    ];
}

$isAdminUser = is_admin();
$name        = trim($customer['customer_name']);
$initial     = strtoupper(substr($name, 0, 1));
$nameParts   = explode(' ', $name);
$firstName   = $nameParts[0];

$cartController = new CartController();
$cartIp         = get_ip();
$cartCount      = $cartController->getCartCount($cartIp);
$cartTotal      = $cartController->getCartTotal($cartIp);

$productCount  = 0;
$categoryCount = 0;
$brandCount    = 0;

if ($isAdminUser) {
    $productController = new ProductController();
    $productCount  = count($productController->getAllProducts());
    $categoryCount = count($productController->getAllCategories());
    $brandCount    = count($productController->getAllBrands());
}

include '../layout/header.php';
?>

<main>
    <h2>My Account</h2>

    <div class="account-card">
        <div class="account-head">
            <?php if (!empty($customer['customer_image'])): ?>
                <img class="account-avatar" src="../../images/customers/<?= htmlspecialchars($customer['customer_image']) ?>" alt="Profile picture">
            <?php else: ?>
                <div class="account-avatar"><?= htmlspecialchars($initial) ?></div>
            <?php endif; ?>

            <div>
                <h3>Welcome back, <?= htmlspecialchars($firstName) ?></h3>
                <span class="role-badge"><?= $isAdminUser ? 'Administrator' : 'Customer' ?></span>
            </div>
        </div>

        <dl class="account-details">
            <div>
                <dt>Full name</dt>
                <dd><?= htmlspecialchars($name) ?></dd>
            </div>
            <div>
                <dt>Email</dt>
                <dd><?= htmlspecialchars($customer['customer_email']) ?></dd>
            </div>
            <div>
                <dt>Contact</dt>
                <dd><?= $customer['customer_contact'] !== '' ? htmlspecialchars($customer['customer_contact']) : 'Not provided' ?></dd>
            </div>
            <div>
                <dt>Location</dt>
                <dd>
                    <?php
                    $location = trim($customer['customer_city'] . ', ' . $customer['customer_country'], ', ');
                    echo $location !== '' ? htmlspecialchars($location) : 'Not provided';
                    ?>
                </dd>
            </div>
        </dl>

        <?php if ($basicOnly): ?>
            <p class="account-note">Showing basic details only.</p>
        <?php endif; ?>
    </div>

    <?php if ($isAdminUser): ?>
        <h3>Admin Dashboard</h3>
        <div class="dash-grid">
            <div class="dash-card">
                <span class="dash-label">Products</span>
                <span class="dash-stat"><?= (int)$productCount ?></span>
                <div class="dash-links">
                    <a class="btn-link" href="<?= $base ?>/views/admin/product.php">Manage</a>
                </div>
            </div>
            <div class="dash-card">
                <span class="dash-label">Categories</span>
                <span class="dash-stat"><?= (int)$categoryCount ?></span>
                <div class="dash-links">
                    <a class="btn-link" href="<?= $base ?>/views/admin/category.php">Manage</a>
                </div>
            </div>
            <div class="dash-card">
                <span class="dash-label">Brands</span>
                <span class="dash-stat"><?= (int)$brandCount ?></span>
                <div class="dash-links">
                    <a class="btn-link" href="<?= $base ?>/views/admin/brand.php">Manage</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <h3>My Shopping</h3>
    <div class="dash-grid">
        <div class="dash-card">
            <span class="dash-label">Items in cart</span>
            <span class="dash-stat"><?= (int)$cartCount ?></span>
            <span class="dash-sub">Total $<?= number_format($cartTotal, 2) ?></span>
            <div class="dash-links">
                <a class="btn-link" href="<?= $base ?>/views/cart.php">View Cart</a>
            </div>
        </div>
        <div class="dash-card">
            <span class="dash-label">Keep browsing</span>
            <span class="dash-sub">Find something new in the shop.</span>
            <div class="dash-links">
                <a class="btn-link" href="<?= $base ?>/index.php">Continue Shopping</a>
                <a class="btn-link" href="<?= $base ?>/views/all_products.php">All Products</a>
            </div>
        </div>
    </div>

    <div class="account-actions">
        <a class="btn-link" href="<?= $base ?>/logout.php">Logout</a>
    </div>
</main>

<?php include '../layout/footer.php'; ?>