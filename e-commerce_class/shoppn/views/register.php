<?php

require "../core/core.php";

include 'layout/header.php';

?>

<h2>Create an Account</h2>

<?php if (!empty($_SESSION['error'])): ?>
    <p style="color:red;">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form id="register-form" action="../actions/register_action.php" method="POST">

    <label>Full Name</label><br>
    <input type="text" name="name" id="name"><br>
    <span class="error" id="name-error" style="color:red;"></span><br>

    <label>Email</label><br>
    <input type="email" name="email" id="email"><br>
    <span class="error" id="email-error" style="color:red;"></span><br>

    <label>Password</label><br>
    <input type="password" name="pass" id="pass"><br>
    <span class="error" id="pass-error" style="color:red;"></span><br>

    <label>Country</label><br>
    <select name="country" id="country">
        <option value="Ghana">Ghana</option>
        <option value="Nigeria">Nigeria</option>
        <option value="Other">Other</option>
    </select><br><br>

    <label>City</label><br>
    <input type="text" name="city" id="city"><br><br>

    <label>Contact Number</label><br>
    <input type="text" name="contact" id="contact"><br>
    <span class="error" id="contact-error" style="color:red;"></span><br>

    <button type="submit" id="submit-btn">Register</button>

</form>

<p>Already have an account? <a href="login.php">Login here</a></p>

<script src="../js/validate.js"></script>

<?php include 'layout/footer.php'; ?>