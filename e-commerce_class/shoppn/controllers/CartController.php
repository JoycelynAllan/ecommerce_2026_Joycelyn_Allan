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
}

?>