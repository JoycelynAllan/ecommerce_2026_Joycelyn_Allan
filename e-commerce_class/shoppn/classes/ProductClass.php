<?php

require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database {

    public $lastError = '';

    private function runWrite($sql, $types, ...$params) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $success = $stmt->execute();
            $stmt->close();

            return $success;
        } catch (mysqli_sql_exception $e) {
            $this->lastError = $e->getMessage();
            error_log($e->getMessage());

            return false;
        }
    }

    public function addBrand($name) {
        return $this->runWrite(
            'INSERT INTO brands (brand_name) VALUES (?)',
            's',
            $name
        );
    }

    public function getAllBrands() {
        $result = $this->conn->query(
            'SELECT * FROM brands ORDER BY brand_name ASC'
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getBrandById($id) {
        $stmt = $this->conn->prepare(
            'SELECT * FROM brands WHERE brand_id = ?'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $brand = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $brand ? $brand : false;
    }

    public function updateBrand($id, $name) {
        return $this->runWrite(
            'UPDATE brands SET brand_name = ? WHERE brand_id = ?',
            'si',
            $name,
            $id
        );
    }

    public function addCategory($name) {
        return $this->runWrite(
            'INSERT INTO categories (cat_name) VALUES (?)',
            's',
            $name
        );
    }

    public function getAllCategories() {
        $result = $this->conn->query(
            'SELECT * FROM categories ORDER BY cat_name ASC'
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getCategoryById($id) {
        $stmt = $this->conn->prepare(
            'SELECT * FROM categories WHERE cat_id = ?'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ? $row : false;
    }

    public function updateCategory($id, $name) {
        return $this->runWrite(
            'UPDATE categories SET cat_name = ? WHERE cat_id = ?',
            'si',
            $name,
            $id
        );
    }

    public function addProduct($cat, $brand, $title, $price, $desc, $image, $keywords) {
        return $this->runWrite(
            'INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
            VALUES (?, ?, ?, ?, ?, ?, ?)',
            'iisdsss',
            $cat, $brand, $title, $price, $desc, $image, $keywords
        );
    }

    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords) {
        return $this->runWrite(
            'UPDATE products SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?,
            product_desc = ?, product_image = ?, product_keywords = ? WHERE product_id = ?',
            'iisdsssi',
            $cat, $brand, $title, $price, $desc, $image, $keywords, $id
        );
    }

    public function getProductById($id) {
        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            WHERE p.product_id = ?'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ? $row : false;
    }

    public function getAllProducts() {
        $result = $this->conn->query(
            'SELECT p.product_id, p.product_title, p.product_price, p.product_image, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            ORDER BY p.product_id DESC'
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getFeaturedProducts($limit = 6) {
        $stmt = $this->conn->prepare('SELECT * FROM products ORDER BY RAND() LIMIT ?');
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function getProductsByCategory($cat_id) {
        $stmt = $this->conn->prepare('SELECT * FROM products WHERE product_cat = ?');
        $stmt->bind_param('i', $cat_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function getProductsByBrand($brand_id) {
        $stmt = $this->conn->prepare('SELECT * FROM products WHERE product_brand = ?');
        $stmt->bind_param('i', $brand_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function searchProducts($query) {
        $like = '%' . $query . '%';
        $stmt = $this->conn->prepare('SELECT * FROM products WHERE product_title LIKE ? OR product_keywords LIKE ?');
        $stmt->bind_param('ss', $like, $like);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }
}

?>