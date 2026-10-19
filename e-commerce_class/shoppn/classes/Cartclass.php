<?php

require_once __DIR__ . '/../core/db_class.php';

class CartClass extends Database {

    public function isInCart($product_id, $ip) {
        $stmt = $this->conn->prepare(
            'SELECT p_id FROM cart WHERE p_id = ? AND ip_add = ?'
        );
        $stmt->bind_param('is', $product_id, $ip);
        $stmt->execute();
        $found = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        return $found;
    }

    public function addToCart($product_id, $qty, $ip, $customer_id = null) {
        $stmt = $this->conn->prepare(
            'INSERT INTO cart (p_id, ip_add, qty) VALUES (?, ?, ?)'
        );
        $stmt->bind_param('isi', $product_id, $ip, $qty);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function getCartItems($ip) {
        $stmt = $this->conn->prepare(
            'SELECT cart.*, products.product_title, products.product_price, products.product_image
            FROM cart
            JOIN products ON cart.p_id = products.product_id
            WHERE cart.ip_add = ?'
        );
        $stmt->bind_param('s', $ip);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $items;
    }

    public function getCartCount($ip) {
        $stmt = $this->conn->prepare(
            'SELECT COUNT(*) AS total FROM cart WHERE ip_add = ?'
        );
        $stmt->bind_param('s', $ip);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int)$row['total'];
    }

    public function getCartTotal($ip) {
        $total = 0.0;

        foreach ($this->getCartItems($ip) as $item) {
            $total += $item['qty'] * $item['product_price'];
        }

        return (float)$total;
    }

    public function removeFromCart($product_id, $ip) {
        $stmt = $this->conn->prepare(
            'DELETE FROM cart WHERE p_id = ? AND ip_add = ?'
        );
        $stmt->bind_param('is', $product_id, $ip);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function updateQty($product_id, $new_qty, $ip) {
        if ((int)$new_qty < 1) {
            return false;
        }

        $stmt = $this->conn->prepare(
            'UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ?'
        );
        $stmt->bind_param('iis', $new_qty, $product_id, $ip);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    private function invoiceExists($invoice) {
        $stmt = $this->conn->prepare(
            'SELECT order_id FROM orders WHERE invoice_no = ?'
        );
        $stmt->bind_param('i', $invoice);
        $stmt->execute();
        $found = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        return $found;
    }

    private function recordPaymentNotification($order_id, $customer_id, $invoice, $amount) {
        try {
            $stmt = $this->conn->prepare('SELECT customer_name FROM customer WHERE customer_id = ?');
            $stmt->bind_param('i', $customer_id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $name    = $row ? $row['customer_name'] : 'A customer';
            $message = $name . ' paid $' . number_format($amount, 2) . ' (Invoice #' . $invoice . ')';

            $stmt = $this->conn->prepare('INSERT INTO notifications (message, order_id) VALUES (?, ?)');
            $stmt->bind_param('si', $message, $order_id);
            $stmt->execute();
            $stmt->close();
        } catch (Throwable $e) {
            error_log($e->getMessage());
        }
    }

    public function processPayment($customer_id, $ip, $reference, $amount) {
        try {
            $this->conn->begin_transaction();

            do {
                $invoice = mt_rand(100000, 999999);
            } while ($this->invoiceExists($invoice));

            $stmt = $this->conn->prepare(
                "INSERT INTO orders (customer_id, invoice_no, order_date, order_status, total_amount, payment_reference)
                VALUES (?, ?, NOW(), 'paid', ?, ?)"
            );
            $stmt->bind_param('iids', $customer_id, $invoice, $amount, $reference);

            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }

            $order_id = $this->conn->insert_id;
            $stmt->close();

            $stmt = $this->conn->prepare(
                'INSERT INTO orderdetails (order_id, product_id, qty)
                SELECT ?, p_id, qty FROM cart WHERE ip_add = ?'
            );
            $stmt->bind_param('is', $order_id, $ip);

            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }

            $stmt->close();

            $stmt = $this->conn->prepare('DELETE FROM cart WHERE ip_add = ?');
            $stmt->bind_param('s', $ip);

            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }

            $stmt->close();

            $this->conn->commit();

            $this->recordPaymentNotification($order_id, $customer_id, $invoice, $amount);

            return $order_id;
        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log($e->getMessage());

            return false;
        }
    }

    public function getOrderById($order_id, $customer_id = null) {
        if ($customer_id === null) {
            $stmt = $this->conn->prepare(
                'SELECT o.*, c.customer_name, c.customer_email
                FROM orders o
                JOIN customer c ON o.customer_id = c.customer_id
                WHERE o.order_id = ?'
            );
            $stmt->bind_param('i', $order_id);
        } else {
            $stmt = $this->conn->prepare(
                'SELECT o.*, c.customer_name, c.customer_email
                FROM orders o
                JOIN customer c ON o.customer_id = c.customer_id
                WHERE o.order_id = ? AND o.customer_id = ?'
            );
            $stmt->bind_param('ii', $order_id, $customer_id);
        }

        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$order) {
            return false;
        }

        $stmt = $this->conn->prepare(
            'SELECT od.qty, p.product_title, p.product_price, p.product_image
            FROM orderdetails od
            JOIN products p ON od.product_id = p.product_id
            WHERE od.order_id = ?'
        );
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return ['order' => $order, 'items' => $items];
    }

    private function fetchRowsSafe($sql, $types = '', $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);

            if ($types !== '') {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $rows;
        } catch (Throwable $e) {
            error_log($e->getMessage());

            return [];
        }
    }

    private function fetchScalarSafe($sql) {
        try {
            $result = $this->conn->query($sql);
            $row = $result ? $result->fetch_row() : null;

            return $row ? $row[0] : 0;
        } catch (Throwable $e) {
            error_log($e->getMessage());

            return 0;
        }
    }

    public function getAllOrders($limit = 200) {
        return $this->fetchRowsSafe(
            'SELECT o.order_id, o.invoice_no, o.order_date, o.order_status, o.total_amount, c.customer_name, c.customer_email
            FROM orders o
            JOIN customer c ON o.customer_id = c.customer_id
            ORDER BY o.order_id DESC
            LIMIT ?',
            'i',
            [$limit]
        );
    }

    public function getOrderCount() {
        return (int)$this->fetchScalarSafe('SELECT COUNT(*) FROM orders');
    }

    public function getRevenueTotal() {
        return (float)$this->fetchScalarSafe('SELECT COALESCE(SUM(total_amount), 0) FROM orders');
    }

    public function getUnreadNotificationCount() {
        return (int)$this->fetchScalarSafe('SELECT COUNT(*) FROM notifications WHERE is_read = 0');
    }

    public function getLatestNotificationId() {
        return (int)$this->fetchScalarSafe('SELECT COALESCE(MAX(notification_id), 0) FROM notifications');
    }

    public function getRecentNotifications($limit = 5) {
        return $this->fetchRowsSafe(
            'SELECT notification_id, message, order_id, is_read, created_at
            FROM notifications
            ORDER BY notification_id DESC
            LIMIT ?',
            'i',
            [$limit]
        );
    }

    public function getNotificationsAfter($after) {
        return $this->fetchRowsSafe(
            'SELECT notification_id, message, order_id, is_read, created_at
            FROM notifications
            WHERE notification_id > ?
            ORDER BY notification_id ASC
            LIMIT 20',
            'i',
            [$after]
        );
    }

    public function markNotificationsRead() {
        try {
            return (bool)$this->conn->query('UPDATE notifications SET is_read = 1 WHERE is_read = 0');
        } catch (Throwable $e) {
            error_log($e->getMessage());

            return false;
        }
    }
}

?>