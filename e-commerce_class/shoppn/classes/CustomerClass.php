<?php

require_once __DIR__ . '/../core/db_class.php';

class CustomerClass extends Database {

    public function emailExists($email) {

        $stmt = $this->conn->prepare(
            'SELECT customer_email FROM customer WHERE customer_email = ?'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $exists = $result->num_rows > 0;

        $stmt->close();

        return $exists;
    }

    public function addCustomer($name, $email, $pass, $country, $city, $contact) {

        $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare(
            'INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('ssssss', $name, $email, $hashed_pass, $country, $city, $contact);

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }
    public function getCustomerByEmail($email) {

    $stmt = $this->conn->prepare('SELECT * FROM customer WHERE customer_email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
    }

    public function login($email, $pass) {

    $customer = $this->getCustomerByEmail($email);

    if ($customer && password_verify($pass, $customer['customer_pass'])) {
        return $customer;
    }

    return false;
    }

        public function getCustomerCount() {
        $result = $this->conn->query('SELECT COUNT(*) FROM customer WHERE user_role <> 1');

        return $result ? (int)$result->fetch_row()[0] : 0;
    }

    public function getAllCustomers($search = '') {
        $like = '%' . $search . '%';
        $stmt = $this->conn->prepare(
            'SELECT c.customer_id, c.customer_name, c.customer_email, c.customer_country, c.customer_city, c.customer_contact,
                    COUNT(o.order_id) AS order_count, COALESCE(SUM(o.total_amount), 0) AS total_spent
            FROM customer c
            LEFT JOIN orders o ON o.customer_id = c.customer_id
            WHERE c.user_role <> 1
              AND (c.customer_name LIKE ? OR c.customer_email LIKE ? OR c.customer_country LIKE ? OR c.customer_city LIKE ?)
            GROUP BY c.customer_id, c.customer_name, c.customer_email, c.customer_country, c.customer_city, c.customer_contact
            ORDER BY c.customer_id DESC'
        );
        $stmt->bind_param('ssss', $like, $like, $like, $like);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }
}

?>