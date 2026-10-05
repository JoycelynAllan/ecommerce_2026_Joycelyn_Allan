<?php

require "../../core/core.php";
require_once "../../controllers/ProductController.php";

require_admin();

include '../layout/header.php';

$controller = new ProductController();
$categories = $controller->getAllCategories();
$brands     = $controller->getAllBrands();
$products   = $controller->getAllProducts();

$editing = false;
if (isset($_GET['edit_id'])) {
    $edit_id = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT);
    if ($edit_id && $edit_id > 0) {
        $editing = $controller->getProductById($edit_id);
    }
}

$base = "/~joycelyn.allan/e-commerce_class/shoppn";

?>

<h2>Manage Products</h2>

<?php if (!empty($_SESSION['success'])): ?>
    <p style="color:green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <p style="color:red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<h3><?= $editing ? 'Edit Product' : 'Add Product' ?></h3>

<form id="product-form"
      action="<?= $base ?>/actions/<?= $editing ? 'update_product_action.php' : 'add_product_action.php' ?>"
      method="POST" enctype="multipart/form-data">

    <?php if ($editing): ?>
        <input type="hidden" name="product_id" value="<?= (int)$editing['product_id'] ?>">
        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editing['product_image']) ?>">
    <?php endif; ?>

    <label>Product Title</label><br>
    <input type="text" name="product_title" value="<?= $editing ? htmlspecialchars($editing['product_title']) : '' ?>" required><br><br>

    <label>Price</label><br>
    <input type="text" name="product_price" value="<?= $editing ? htmlspecialchars($editing['product_price']) : '' ?>" required><br><br>

    <label>Category</label><br>
    <select name="product_cat" required>
        <option value="">Select category</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['cat_id'] ?>"
                <?= ($editing && $editing['product_cat'] == $c['cat_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['cat_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Brand</label><br>
    <select name="product_brand" required>
        <option value="">Select brand</option>
        <?php foreach ($brands as $b): ?>
            <option value="<?= (int)$b['brand_id'] ?>"
                <?= ($editing && $editing['product_brand'] == $b['brand_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['brand_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Description</label><br>
    <input type="text" name="product_desc" value="<?= $editing ? htmlspecialchars($editing['product_desc']) : '' ?>"><br><br>

    <label>Keywords</label><br>
    <input type="text" name="product_keywords" value="<?= $editing ? htmlspecialchars($editing['product_keywords']) : '' ?>"><br><br>

    <label>Image</label><br>
    <?php if ($editing && !empty($editing['product_image'])): ?>
        <img src="<?= $base ?>/images/products/<?= htmlspecialchars($editing['product_image']) ?>" alt="Current image" width="100"><br>
    <?php endif; ?>
    <input type="file" name="product_image" id="product_image" accept="image/*"><br>
    <span style="color:#e03131;font-size:0.8rem;" id="image-error"></span><br>

    <button type="submit"><?= $editing ? 'Update Product' : 'Add Product' ?></button>
    <?php if ($editing): ?><a href="product.php">Cancel</a><?php endif; ?>
</form>

<hr>

<h3>Existing Products</h3>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Price</th>
        <th>Category</th>
        <th>Brand</th>
        <th>Action</th>
    </tr>
    <?php if (empty($products)): ?>
        <tr><td colspan="6">No products yet.</td></tr>
    <?php else: ?>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= (int)$p['product_id'] ?></td>
                <td><?= htmlspecialchars($p['product_title']) ?></td>
                <td><?= htmlspecialchars($p['product_price']) ?></td>
                <td><?= htmlspecialchars($p['cat_name']) ?></td>
                <td><?= htmlspecialchars($p['brand_name']) ?></td>
                <td><a href="product.php?edit_id=<?= (int)$p['product_id'] ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>

<script>
document.getElementById('product-form').addEventListener('submit', function (e) {
    var file = document.getElementById('product_image').files[0];
    var err = document.getElementById('image-error');
    err.textContent = '';
    if (file) {
        var okTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (okTypes.indexOf(file.type) === -1) {
            err.textContent = 'Image must be JPG, PNG, GIF or WEBP.';
            e.preventDefault();
        } else if (file.size > 2 * 1024 * 1024) {
            err.textContent = 'Image must be under 2MB.';
            e.preventDefault();
        }
    }
});
</script>

<?php include '../layout/footer.php'; ?>