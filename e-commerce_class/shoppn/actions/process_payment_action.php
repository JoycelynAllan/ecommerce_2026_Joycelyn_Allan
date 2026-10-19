<?php

require "../core/core.php";
require_once "../core/paystack_config.php";
require_once "../controllers/CartController.php";

require_login();

function payment_failed($message) {
    $_SESSION['payment_error'] = $message;
    redirect('../views/payment_failed.php');
    exit;
}

$reference = $_GET['ref'] ?? '';

if (!preg_match('/^[A-Za-z0-9._-]{6,100}$/', $reference)) {
    payment_failed('The payment reference was missing or invalid.');
}

$cartController = new CartController();
$ip    = get_ip();
$total = $cartController->getCartTotal($ip);

if ($total <= 0) {
    payment_failed('Your cart is empty, so there is nothing to pay for.');
}

$expectedAmount = (int)round($total * 100);

$ch = curl_init('https://api.paystack.co/transaction/verify/' . rawurlencode($reference));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
        'Cache-Control: no-cache'
    ]
]);

$response  = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false || $curlError !== '') {
    payment_failed('We could not reach the payment provider to confirm your payment. Please try again.');
}

$result = json_decode($response, true);

if (!is_array($result) || empty($result['status']) || empty($result['data'])) {
    payment_failed('The payment could not be verified.');
}

$data = $result['data'];

if (($data['status'] ?? '') !== 'success') {
    payment_failed('The payment was not successful.');
}

if ((int)($data['amount'] ?? 0) !== $expectedAmount) {
    payment_failed('The amount paid does not match your cart total.');
}

if (($data['currency'] ?? '') !== PAYSTACK_CURRENCY) {
    payment_failed('The payment currency does not match.');
}

$sessionEmail = strtolower($_SESSION['customer_email'] ?? '');
$paidEmail    = strtolower($data['customer']['email'] ?? '');

if ($sessionEmail !== '' && $paidEmail !== '' && $sessionEmail !== $paidEmail) {
    payment_failed('The payment does not belong to this account.');
}

$orderId = $cartController->processPayment((int)$_SESSION['customer_id'], $ip, $reference, $total);

if (!$orderId) {
    payment_failed('Your payment was received, but the order could not be saved. Please contact support with reference ' . $reference . '.');
}

redirect('../views/payment_success.php?order_id=' . (int)$orderId);
exit;