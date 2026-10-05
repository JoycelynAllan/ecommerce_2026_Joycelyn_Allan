<?php

require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database {

    public function addBrand($name) {

        $stmt = $this->conn->prepare(
            'INSERT INTO brands (brand_name) VALUES (?)'
        );
        $stmt->bind_param('s', $name);

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    public function getAllBrands() {

        $result = $this->conn->query(
            'SELECT * FROM brands ORDER BY brand_name ASC'
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getBrandById($id) {

        $stmt = $this->conn->prepare(
            'SELECT * FROM brands WHERE brand_id = ?'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $brand = $result->fetch_assoc();

        $stmt->close();

        return $brand;
    }

    public function updateBrand($id, $name) {

        $stmt = $this->conn->prepare(
            'UPDATE brands SET brand_name = ? WHERE brand_id = ?'
        );
        $stmt->bind_param('si', $name, $id);

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    public function addCategory($name) {

        $stmt = $this->conn->prepare(
            'INSERT INTO categories (cat_name) VALUES (?)'
        );
        $stmt->bind_param('s', $name);

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    public function getAllCategories() {

        $result = $this->conn->query(
            'SELECT * FROM categories ORDER BY cat_name ASC'
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCategoryById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? $row : false;
    }

    public function updateCategory($id, $name) {
        $stmt = $this->conn->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ?");
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }

    public function addProduct($cat, $brand, $title, $price, $desc, $image, $keywords) {
        $stmt = $this->conn->prepare(
            "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iisdsss", $cat, $brand, $title, $price, $desc, $image, $keywords);
        return $stmt->execute();
    }

    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords) {
        $stmt = $this->conn->prepare(
            "UPDATE products SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?,
            product_desc = ?, product_image = ?, product_keywords = ? WHERE product_id = ?"
        );
        $stmt->bind_param("iisdsssi", $cat, $brand, $title, $price, $desc, $image, $keywords, $id);
        return $stmt->execute();
    }

    public function getProductById($id) {
        $stmt = $this->conn->prepare(
            "SELECT p.*, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            WHERE p.product_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? $row : false;
    }

    public function getAllProducts() {
        $result = $this->conn->query(
            "SELECT p.product_id, p.product_title, p.product_price, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            ORDER BY p.product_id DESC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getFeaturedProducts($limit = 6) {
        $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY RAND() LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductsByCategory($cat_id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE product_cat = ?");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductsByBrand($brand_id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE product_brand = ?");
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function searchProducts($query) {
        $like = '%' . $query . '%';
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE product_title LIKE ? OR product_keywords LIKE ?");
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

?>
