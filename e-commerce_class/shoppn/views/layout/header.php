<?php
require_once __DIR__ . '/../../controllers/CartController.php';

$base        = '/~joycelyn.allan/e-commerce_class/shoppn';
$isAdminUser = is_admin();
$isCustomer  = is_logged_in() && !$isAdminUser;

$headerCount = 0;
$headerTotal = 0;

if ($isCustomer) {
    $headerCart  = new CartController();
    $headerIp    = get_ip();
    $headerCount = $headerCart->getCartCount($headerIp);
    $headerTotal = $headerCart->getCartTotal($headerIp);
}

$unreadCount   = 0;
$recentAlerts  = [];
$latestAlertId = 0;

if ($isAdminUser) {
    $adminHeader   = new CartController();
    $unreadCount   = $adminHeader->getUnreadNotificationCount();
    $recentAlerts  = $adminHeader->getRecentNotifications(5);
    $latestAlertId = $adminHeader->getLatestNotificationId();
}

$firstName = '';

if (is_logged_in()) {
    $parts     = explode(' ', trim($_SESSION['customer_name'] ?? ''));
    $firstName = $parts[0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoppn</title>
    <link rel="stylesheet" href="<?= $base ?>/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">

        <a class="logo" href="<?= $base ?>/index.php">Shoppn</a>

        <nav class="main-links">
            <a href="<?= $base ?>/index.php">Home</a>
            <a href="<?= $base ?>/views/all_products.php">All Products</a>
        </nav>

        <form class="search-form" action="<?= $base ?>/views/search_results.php" method="GET">
            <input type="text" name="user_query" placeholder="Search products, keywords and more" aria-label="Search products">
            <button type="submit">Search</button>
        </form>

        <div class="header-actions">

            <?php if ($isAdminUser): ?>
                <div class="menu">
                    <button type="button" class="menu-trigger">Admin</button>
                    <div class="menu-panel">
                        <a href="<?= $base ?>/views/admin/product.php">Manage Products</a>
                        <a href="<?= $base ?>/views/admin/category.php">Manage Categories</a>
                        <a href="<?= $base ?>/views/admin/brand.php">Manage Brands</a>
                        <a href="<?= $base ?>/views/account/my_account.php?tab=customers">Customers</a>
                        <a href="<?= $base ?>/views/account/my_account.php?tab=orders">Orders</a>
                    </div>
                </div>

                <div class="menu">
                    <button type="button" class="menu-trigger bell-trigger" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.7 21a2 2 0 0 1-3.4 0"></path>
                        </svg>
                        <span class="bell-badge" id="bell-badge" <?= $unreadCount > 0 ? '' : 'hidden' ?>><?= (int)$unreadCount ?></span>
                    </button>
                    <div class="menu-panel notif-panel">
                        <div class="notif-title">Payment alerts</div>
                        <?php if (empty($recentAlerts)): ?>
                            <div class="notif-empty">No alerts yet.</div>
                        <?php else: ?>
                            <?php foreach ($recentAlerts as $alert): ?>
                                <a class="notif-item <?= empty($alert['is_read']) ? 'unread' : '' ?>"
                                   href="<?= $base ?>/views/account/my_account.php?tab=orders<?= !empty($alert['order_id']) ? '&view=' . (int)$alert['order_id'] : '' ?>">
                                    <?= htmlspecialchars($alert['message']) ?>
                                    <small><?= htmlspecialchars(date('j M, g:i A', strtotime($alert['created_at']))) ?></small>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <a class="notif-all" href="<?= $base ?>/views/account/my_account.php?tab=orders">View all orders</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (is_logged_in()): ?>
                <div class="menu">
                    <button type="button" class="menu-trigger">Hi, <?= htmlspecialchars($firstName) ?></button>
                    <div class="menu-panel">
                        <a href="<?= $base ?>/views/account/my_account.php"><?= $isAdminUser ? 'Dashboard' : 'My Account' ?></a>
                        <a href="<?= $base ?>/logout.php">Logout</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($isCustomer): ?>
                <a class="cart-link" href="<?= $base ?>/views/cart.php"
                   title="Shopping Cart - Total Items: <?= (int)$headerCount ?> | Total Price: $<?= number_format($headerTotal, 2) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="9" cy="20" r="1.5"></circle>
                        <circle cx="18" cy="20" r="1.5"></circle>
                        <path d="M2 3h3l2.6 12.4a2 2 0 0 0 2 1.6h8.2a2 2 0 0 0 2-1.5L21.5 8H6"></path>
                    </svg>
                    <span class="cart-badge"><?= (int)$headerCount ?></span>
                    <span class="cart-total">$<?= number_format($headerTotal, 2) ?></span>
                </a>
            <?php endif; ?>

            <?php if (!is_logged_in()): ?>
                <a class="header-login" href="<?= $base ?>/views/login.php">Login</a>
                <a class="header-register" href="<?= $base ?>/views/register.php">Register</a>
            <?php endif; ?>

        </div>
    </div>
</header>

<?php if ($isAdminUser): ?>
    <div class="toast-wrap" id="toast-wrap"></div>
    <script>
        (function () {
            var lastId = <?= (int)$latestAlertId ?>;
            var feedUrl = <?= json_encode($base . '/views/account/my_account.php') ?>;
            var ordersUrl = <?= json_encode($base . '/views/account/my_account.php?tab=orders') ?>;
            var wrap = document.getElementById('toast-wrap');
            var badge = document.getElementById('bell-badge');

            function setBadge(count) {
                if (!badge) {
                    return;
                }

                badge.textContent = count;
                badge.hidden = !(count > 0);
            }

            function showToast(item) {
                var toast = document.createElement('div');
                toast.className = 'toast';

                var link = document.createElement('a');
                link.href = ordersUrl + (item.order_id ? '&view=' + encodeURIComponent(item.order_id) : '');
                link.textContent = item.message;

                var close = document.createElement('button');
                close.type = 'button';
                close.className = 'toast-close';
                close.setAttribute('aria-label', 'Dismiss');
                close.textContent = '\u00d7';
                close.addEventListener('click', function () {
                    toast.remove();
                });

                toast.appendChild(link);
                toast.appendChild(close);
                wrap.appendChild(toast);

                setTimeout(function () {
                    toast.remove();
                }, 10000);
            }

            function poll() {
                fetch(feedUrl + '?feed=1&after=' + lastId, { credentials: 'same-origin' })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (!data || !data.items) {
                            return;
                        }

                        data.items.forEach(function (item) {
                            showToast(item);
                            lastId = Math.max(lastId, parseInt(item.notification_id, 10));
                        });

                        setBadge(data.unread);
                    })
                    .catch(function () {});
            }

            setInterval(poll, 15000);
        })();
    </script>
<?php endif; ?>

<?php if (!empty($_SESSION['cart_success'])): ?>
    <div class="flash"><p style="color:green;"><?= htmlspecialchars($_SESSION['cart_success']) ?></p></div>
    <?php unset($_SESSION['cart_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['cart_error'])): ?>
    <div class="flash"><p style="color:red;"><?= htmlspecialchars($_SESSION['cart_error']) ?></p></div>
    <?php unset($_SESSION['cart_error']); ?>
<?php endif; ?>