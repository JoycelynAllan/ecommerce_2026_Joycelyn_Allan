<?php

require "../core/core.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shoppn</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-page">

<div class="login-card">

    <a class="back" href="../index.php">&larr;</a>

    <h2>Welcome Back!</h2>

    <p class="subtitle">Login to continue shopping</p>

    <?php if (($_GET['message'] ?? '') === 'login_required'): ?>
        <p class="error-text">
            Please sign in to add products to your cart.
        </p>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <p style="color:green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="../actions/login_action.php" method="POST">
        <?php if (!empty($_GET['redirect'])): ?>
            <input
                type="hidden"
                name="redirect"
                value="<?= htmlspecialchars($_GET['redirect'], ENT_QUOTES, 'UTF-8') ?>"
            >
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" aria-label="Email" required>

        <div class="pass-wrap">
            <input type="password" name="pass" id="pass" placeholder="Password" aria-label="Password" required>
            <button type="button" class="toggle-pass">Show</button>
        </div>

        <button type="submit">Login</button>

    </form>

    <p class="register-link">
        Don't have an account? <a href="register.php">Sign up</a>
    </p>

</div>

<script>
    document.querySelector('.toggle-pass').addEventListener('click', function () {
        var pass = document.getElementById('pass');

        if (pass.type === 'password') {
            pass.type = 'text';
            this.textContent = 'Hide';
        } else {
            pass.type = 'password';
            this.textContent = 'Show';
        }
    });
</script>

</body>
</html>