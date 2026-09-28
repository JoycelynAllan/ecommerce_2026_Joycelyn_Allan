<?php

session_start();

date_default_timezone_set('Africa/Accra');

require_once "db_class.php";

function get_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    return $_SERVER['REMOTE_ADDR'];
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function is_logged_in() {
    return isset($_SESSION['customer_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
}

function require_login() {
    if (!is_logged_in()) {
        redirect('/~joycelyn.allan/e-commerce_class/shoppn/views/login.php');
    }
}

function require_admin() {
    if (!is_admin()) {
        $_SESSION['error'] = 'You do not have permission to access that page.';
        redirect('/~joycelyn.allan/e-commerce_class/shoppn/index.php');
    }
}

?>