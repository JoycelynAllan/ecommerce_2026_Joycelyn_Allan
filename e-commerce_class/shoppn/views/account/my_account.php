<?php

require "../../core/core.php";
require_once "../../controllers/CartController.php";
require_once "../../controllers/ProductController.php";

if (isset($_GET['feed'])) {
    header('Content-Type: application/json');

    if (!is_admin()) {
        http_response_code(403);
        echo json_encode(['items' => [], 'unread' => 0]);
        exit;
    }

    $after = filter_var($_GET['after'] ?? 0, FILTER_VALIDATE_INT);
    $after = ($after && $after > 0) ? $after : 0;

    $feedController = new CartController();

    echo json_encode([
        'items'  => $feedController->getNotificationsAfter($after),
        'unread' => $feedController->getUnreadNotificationCount()
    ]);
    exit;
}

require_login();

$customer           = null;
$customerController = null;
$customerFile       = __DIR__ . '/../../controllers/CustomerController.php';

if (file_exists($customerFile)) {
    require_once $customerFile;

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

$tab = $_GET['tab'] ?? 'overview';

if (!in_array($tab, ['overview', 'customers', 'orders'], true) || !$isAdminUser) {
    $tab = 'overview';
}

$cartController = new CartController();

$cartCount     = 0;
$cartTotal     = 0;
$productCount  = 0;
$categoryCount = 0;
$brandCount    = 0;
$customerCount = 0;
$orderCount    = 0;
$revenue       = 0;
$recentOrders  = [];
$allOrders     = [];
$customers     = [];
$search        = '';
$detail        = false;
$customersReady = true;

if ($isAdminUser) {
    $productController = new ProductController();

    $orderCount   = $cartController->getOrderCount();
    $revenue      = $cartController->getRevenueTotal();

    if ($tab === 'overview') {
        $productCount  = count($productController->getAllProducts());
        $categoryCount = count($productController->getAllCategories());
        $brandCount    = count($productController->getAllBrands());
        $recentOrders  = $cartController->getAllOrders(5);
    }

    if ($customerController && method_exists($customerController, 'getCustomerCount')) {
        $customerCount = $customerController->getCustomerCount();
    }

    if ($tab === 'customers') {
        $search = trim(strip_tags($_GET['q'] ?? ''));

        if ($customerController && method_exists($customerController, 'getAllCustomers')) {
            $customers = $customerController->getAllCustomers($search);
        } else {
            $customersReady = false;
        }
    }

    if ($tab === 'orders') {
        $cartController->markNotificationsRead();

        $viewId = filter_var($_GET['view'] ?? '', FILTER_VALIDATE_INT);
        $detail = ($viewId && $viewId > 0) ? $cartController->getOrderById($viewId) : false;
        $allOrders = $cartController->getAllOrders();
    }
} else {
    $cartIp    = get_ip();
    $cartCount = $cartController->getCartCount($cartIp);
    $cartTotal = $cartController->getCartTotal($cartIp);
}

include '../layout/header.php';
?>

<main>
    <h2><?= $isAdminUser ? 'Admin Dashboard' : 'My Account' ?></h2>

    <?php if ($isAdminUser): ?>
        <nav class="tabs" aria-label="Dashboard sections">
            <a class="<?= $tab === 'overview' ? 'active' : '' ?>" href="my_account.php">Overview</a>
            <a class="<?= $tab === 'customers' ? 'active' : '' ?>" href="my_account.php?tab=customers">Customers</a>
            <a class="<?= $tab === 'orders' ? 'active' : '' ?>" href="my_account.php?tab=orders">Orders</a>
        </nav>
    <?php endif; ?>

    <?php if ($tab === 'overview'): ?>

        <div class="account-card">
            <div class="account-head">
                <?php if (!empty($customer['customer_image'])): ?>
                    <img class="account-avatar" src="<?= $base ?>/images/customers/<?= htmlspecialchars($customer['customer_image']) ?>" alt="Profile picture">
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
            <h3>Store Overview</h3>
            <div class="dash-grid">
                <div class="dash-card">
                    <span class="dash-label">Revenue</span>
                    <span class="dash-stat">$<?= number_format($revenue, 2) ?></span>
                    <div class="dash-links"><a class="btn-link" href="my_account.php?tab=orders">View orders</a></div>
                </div>
                <div class="dash-card">
                    <span class="dash-label">Orders</span>
                    <span class="dash-stat"><?= (int)$orderCount ?></span>
                    <div class="dash-links"><a class="btn-link" href="my_account.php?tab=orders">Manage</a></div>
                </div>
                <div class="dash-card">
                    <span class="dash-label">Customers</span>
                    <span class="dash-stat"><?= (int)$customerCount ?></span>
                    <div class="dash-links"><a class="btn-link" href="my_account.php?tab=customers">View all</a></div>
                </div>
                <div class="dash-card">
                    <span class="dash-label">Products</span>
                    <span class="dash-stat"><?= (int)$productCount ?></span>
                    <div class="dash-links"><a class="btn-link" href="<?= $base ?>/views/admin/product.php">Manage</a></div>
                </div>
                <div class="dash-card">
                    <span class="dash-label">Categories</span>
                    <span class="dash-stat"><?= (int)$categoryCount ?></span>
                    <div class="dash-links"><a class="btn-link" href="<?= $base ?>/views/admin/category.php">Manage</a></div>
                </div>
                <div class="dash-card">
                    <span class="dash-label">Brands</span>
                    <span class="dash-stat"><?= (int)$brandCount ?></span>
                    <div class="dash-links"><a class="btn-link" href="<?= $base ?>/views/admin/brand.php">Manage</a></div>
                </div>
            </div>

            <h3>Recent Payments</h3>
            <?php if (empty($recentOrders)): ?>
                <p class="muted">No payments yet. You will get an alert in the bell menu when a customer pays.</p>
            <?php else: ?>
                <div class="admin-panel">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $o): ?>
                                    <tr>
                                        <td><strong>#<?= (int)$o['invoice_no'] ?></strong></td>
                                        <td><?= htmlspecialchars($o['customer_name']) ?></td>
                                        <td><?= htmlspecialchars(date('j M Y, g:i A', strtotime($o['order_date']))) ?></td>
                                        <td><strong>$<?= number_format($o['total_amount'], 2) ?></strong></td>
                                        <td><a class="btn-small-link" href="my_account.php?tab=orders&view=<?= (int)$o['order_id'] ?>">View</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
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
        <?php endif; ?>

        <div class="account-actions">
            <a class="btn-link" href="<?= $base ?>/logout.php">Logout</a>
        </div>

    <?php elseif ($tab === 'customers'): ?>

        <div class="page-title-row">
            <h3>Customers</h3>
            <span class="count-pill"><?= count($customers) ?> <?= $search !== '' ? 'found' : 'registered' ?></span>
        </div>

        <form class="toolbar-form" method="GET" action="my_account.php">
            <input type="hidden" name="tab" value="customers">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name, email, city or country" aria-label="Search customers">
            <button type="submit">Search</button>
            <?php if ($search !== ''): ?>
                <a class="btn-link" href="my_account.php?tab=customers">Clear</a>
            <?php endif; ?>
        </form>

        <?php if (!$customersReady): ?>
            <div class="empty-state">
                <h3>Customer list not available yet</h3>
                <p>Add the getAllCustomers method to CustomerClass and CustomerController to enable this page.</p>
            </div>
        <?php elseif (empty($customers)): ?>
            <div class="empty-state">
                <h3>No customers found</h3>
                <p><?= $search !== '' ? 'Nothing matched your search.' : 'Customers will appear here once they register.' ?></p>
            </div>
        <?php else: ?>
            <div class="admin-panel">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Orders</th>
                                <th>Total spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $c): ?>
                                <?php $cLocation = trim(($c['customer_city'] ?? '') . ', ' . ($c['customer_country'] ?? ''), ', '); ?>
                                <tr>
                                    <td>
                                        <div class="cell-product">
                                            <div class="avatar-sm"><?= htmlspecialchars(strtoupper(substr($c['customer_name'], 0, 1))) ?></div>
                                            <div>
                                                <strong><?= htmlspecialchars($c['customer_name']) ?></strong>
                                                <small><?= htmlspecialchars($c['customer_email']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= !empty($c['customer_contact']) ? htmlspecialchars($c['customer_contact']) : '-' ?></td>
                                    <td><?= $cLocation !== '' ? htmlspecialchars($cLocation) : '-' ?></td>
                                    <td><?= (int)$c['order_count'] ?></td>
                                    <td><strong>$<?= number_format($c['total_spent'], 2) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>

        <div class="page-title-row">
            <h3>Orders</h3>
            <span class="count-pill"><?= count($allOrders) ?> orders</span>
        </div>

        <div class="dash-grid">
            <div class="dash-card">
                <span class="dash-label">Total orders</span>
                <span class="dash-stat"><?= (int)$orderCount ?></span>
            </div>
            <div class="dash-card">
                <span class="dash-label">Revenue</span>
                <span class="dash-stat">$<?= number_format($revenue, 2) ?></span>
            </div>
        </div>

        <?php if ($detail): ?>
            <?php $order = $detail['order']; ?>
            <div class="admin-panel order-detail">
                <div class="page-title-row">
                    <h3>Invoice #<?= (int)$order['invoice_no'] ?></h3>
                    <a class="btn-small-link" href="my_account.php?tab=orders">Close</a>
                </div>

                <dl class="invoice-meta">
                    <div>
                        <dt>Customer</dt>
                        <dd><?= htmlspecialchars($order['customer_name']) ?></dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd><?= htmlspecialchars($order['customer_email']) ?></dd>
                    </div>
                    <div>
                        <dt>Date</dt>
                        <dd><?= htmlspecialchars(date('j M Y, g:i A', strtotime($order['order_date']))) ?></dd>
                    </div>
                    <div>
                        <dt>Status</dt>
                        <dd><span class="badge badge-paid"><?= htmlspecialchars(ucfirst($order['order_status'])) ?></span></dd>
                    </div>
                </dl>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail['items'] as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_title']) ?></td>
                                    <td><?= (int)$item['qty'] ?></td>
                                    <td>$<?= number_format($item['product_price'], 2) ?></td>
                                    <td>$<?= number_format($item['qty'] * $item['product_price'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="cart-subtotal">
                                <td colspan="3">Total paid</td>
                                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($allOrders)): ?>
            <div class="empty-state">
                <h3>No orders yet</h3>
                <p>Paid orders will appear here, and you will get an alert the moment a customer pays.</p>
            </div>
        <?php else: ?>
            <div class="admin-panel">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allOrders as $o): ?>
                                <tr class="<?= ($detail && (int)$detail['order']['order_id'] === (int)$o['order_id']) ? 'row-active' : '' ?>">
                                    <td><strong>#<?= (int)$o['invoice_no'] ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($o['customer_name']) ?>
                                        <small class="block-small"><?= htmlspecialchars($o['customer_email']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars(date('j M Y, g:i A', strtotime($o['order_date']))) ?></td>
                                    <td><strong>$<?= number_format($o['total_amount'], 2) ?></strong></td>
                                    <td><span class="badge badge-paid"><?= htmlspecialchars(ucfirst($o['order_status'])) ?></span></td>
                                    <td><a class="btn-small-link" href="my_account.php?tab=orders&view=<?= (int)$o['order_id'] ?>">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</main>

<?php include '../layout/footer.php'; ?>