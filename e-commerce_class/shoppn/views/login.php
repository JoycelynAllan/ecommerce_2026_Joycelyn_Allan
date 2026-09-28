<?php


require "../core/core.php";
require_once "../controllers/CustomerController.php";

include 'layout/header.php';


?>

<h2>Login</h2>

<?php if (!empty($_SESSION['error'])): ?>
    <p style="color:red;">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form action="../actions/login_action.php" method="POST">

    <label>Email</label><br>
    <input type="email" name="email"><br><br>

    <label>Password</label><br>
    <input type="password" name="pass"><br><br>

    <button type="submit">Login</button>

</form>

<p>Don't have an account? <a href="register.php">Register here</a></p>

<?php include 'layout/footer.php'; ?>