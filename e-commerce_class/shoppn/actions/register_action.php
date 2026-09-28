<?php

require "../core/core.php";
require_once "../controllers/CustomerController.php";




if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/register.php');
}

$name    = trim(strip_tags($_POST['name'] ?? ''));
$email   = trim(strip_tags($_POST['email'] ?? ''));
$pass    = $_POST['pass'] ?? '';
$country = trim(strip_tags($_POST['country'] ?? ''));
$city    = trim(strip_tags($_POST['city'] ?? ''));
$contact = trim(strip_tags($_POST['contact'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Invalid email address.';
    redirect('../views/register.php');
}

if (strlen($email) > 50) {
    $_SESSION['error'] = 'Email is too long.';
    redirect('../views/register.php');
}

if (strlen($name) < 2) {
    $_SESSION['error'] = 'Please enter your full name.';
    redirect('../views/register.php');
}

if (strlen($pass) < 8) {
    $_SESSION['error'] = 'Password must be at least 8 characters.';
    redirect('../views/register.php');
}

if (empty($country) || empty($city) || empty($contact)) {
    $_SESSION['error'] = 'All fields are required.';
    redirect('../views/register.php');
}

$controller = new CustomerController();

$result = $controller->register([
    'name'    => $name,
    'email'   => $email,
    'pass'    => $pass,
    'country' => $country,
    'city'    => $city,
    'contact' => $contact
]);

if ($result['success']) {
    redirect('../views/account/my_account.php');
} else {
    $_SESSION['error'] = $result['error'];
    redirect('../views/register.php');
}

?>