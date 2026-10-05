<?php

require "../core/core.php";
require_once "../controllers/CustomerController.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shoppn</title>
    <link rel="stylesheet" href="../css/style.css">
</head>


<body>

<div class="login-page">
    <div class="login-card">

        <a href="../index.php" class="back">&larr;</a>
        <h2>Welcome Back!</h2>
        <p class="subtitle">Login to continue shopping</p>

        <?php if (!empty($_SESSION['success'])): ?>
            <p style="color:green;">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form action="../actions/login_action.php" method="POST">

            <label>Email</label><br>
            <input type="email" name="email" placeholder="Enter your email"><br><br>

            <div class="pass-wrap">
                <input type="password" name="pass" id="pass" placeholder="Enter your password" required>
                <button type="button" class="toggle-pass">Show</button>
            </div>

            <button type="submit">Login</button>

        </form>

        <p class="register-link">
            Don't have an account? <a href="register.php">Sign up</a>
        </p>

    </div>
</div>

<script src="../js/validate.js"></script>

</body>
</html>