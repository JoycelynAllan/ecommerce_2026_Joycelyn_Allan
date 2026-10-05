<?php

require "../core/core.php";
require_once "../controllers/CustomerController.php";


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/login.php');
}

$email = trim(strip_tags($_POST['email'] ?? ''));
$pass  = $_POST['pass'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($pass)) {
    $_SESSION['error'] = 'Please enter a valid email and password.';
    redirect('../views/login.php');
}

$controller = new CustomerController();
$result = $controller->login($email, $pass);

if ($result['success']) {

    $customer = $result['customer'];

    $_SESSION['customer_id']    = $customer['customer_id'];
    $_SESSION['customer_name']  = $customer['customer_name'];
    $_SESSION['customer_email'] = $customer['customer_email'];
    $_SESSION['user_role']      = $customer['user_role'];

    redirect('../index.php');

} else {
    $_SESSION['error'] = $result['error'];
    redirect('../views/login.php');
}

?>