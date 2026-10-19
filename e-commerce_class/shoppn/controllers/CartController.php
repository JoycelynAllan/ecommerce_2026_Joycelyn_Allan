<?php

require_once __DIR__ . '/../classes/CartClass.php';

class CartController {

    private $cartModel;

    public function __construct() {
        $this->cartModel = new CartClass();
    }

    public function isInCart($product_id, $ip) {
        return $this->cartModel->isInCart($product_id, $ip);
    }

    public function addToCart($product_id, $qty, $ip, $customer_id = null) {
        return $this->cartModel->addToCart($product_id, $qty, $ip, $customer_id);
    }

    public function getCartItems($ip) {
        return $this->cartModel->getCartItems($ip);
    }

    public function getCartCount($ip) {
        return $this->cartModel->getCartCount($ip);
    }

    public function getCartTotal($ip) {
        return $this->cartModel->getCartTotal($ip);
    }

    public function removeFromCart($product_id, $ip) {
        return $this->cartModel->removeFromCart($product_id, $ip);
    }

    public function updateQty($product_id, $new_qty, $ip) {
        return $this->cartModel->updateQty($product_id, $new_qty, $ip);
    }

    public function processPayment($customer_id, $ip, $reference, $amount) {
        return $this->cartModel->processPayment($customer_id, $ip, $reference, $amount);
    }

    public function getOrderById($order_id, $customer_id = null) {
        return $this->cartModel->getOrderById($order_id, $customer_id);
    }

    public function getAllOrders($limit = 200) {
        return $this->cartModel->getAllOrders($limit);
    }

    public function getOrderCount() {
        return $this->cartModel->getOrderCount();
    }

    public function getRevenueTotal() {
        return $this->cartModel->getRevenueTotal();
    }

    public function getUnreadNotificationCount() {
        return $this->cartModel->getUnreadNotificationCount();
    }

    public function getLatestNotificationId() {
        return $this->cartModel->getLatestNotificationId();
    }

    public function getRecentNotifications($limit = 5) {
        return $this->cartModel->getRecentNotifications($limit);
    }

    public function getNotificationsAfter($after) {
        return $this->cartModel->getNotificationsAfter($after);
    }

    public function markNotificationsRead() {
        return $this->cartModel->markNotificationsRead();
    }
}

?>