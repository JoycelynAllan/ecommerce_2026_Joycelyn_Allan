<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Shoppn</title>

    <link rel="stylesheet" href="/~joycelyn.allan/e-commerce_class/shoppn/css/style.css">


</head>

<body>

<header>

    <h1>Shoppn</h1>

    <nav>

        <a href="/~joycelyn.allan/e-commerce_class/shoppn/index.php">Home</a>


        <?php if (is_logged_in()): ?>

            <span>Welcome <?= htmlspecialchars($_SESSION['customer_name'] ?? '') ?></span>

            <?php if (is_admin()): ?>
                <a href="/~joycelyn.allan/e-commerce_class/shoppn/views/admin/brand.php">Brands</a>
                <a href="/~joycelyn.allan/e-commerce_class/shoppn/views/admin/category.php">Categories</a>
            <?php endif; ?>

            <a href="/~joycelyn.allan/e-commerce_class/shoppn/views/account/my_account.php">My Account</a>
            <a href="/~joycelyn.allan/e-commerce_class/shoppn/logout.php">Logout</a>

        <?php else: ?>

            <a href="/~joycelyn.allan/e-commerce_class/shoppn/views/register.php">Register</a>
            <a href="/~joycelyn.allan/e-commerce_class/shoppn/views/login.php">Login</a>

        <?php endif; ?>

    </nav>

</header>

<main>