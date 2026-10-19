<?php

require "../../core/core.php";
require_once "../../controllers/ProductController.php";

require_admin();

$controller = new ProductController();

$editId  = filter_var($_GET['edit_id'] ?? '', FILTER_VALIDATE_INT);
$product = false;

if ($editId && $editId > 0) {
    $product = $controller->getProductById($editId);

    if (!$product) {
        $_SESSION['error'] = 'That product could not be found.';
        redirect('product.php');
        exit;
    }
}

$categories = $controller->getAllCategories();
$brands     = $controller->getAllBrands();
$products   = $controller->getAllProducts();

$isEdit = (bool)$product;

include '../layout/header.php';
?>

<main>
    <div class="page-title-row">
        <h2>Manage Products</h2>
        <span class="count-pill"><?= count($products) ?> products</span>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <p style="color:green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="admin-split">
        <section class="admin-panel admin-form-panel">
            <h3><?= $isEdit ? 'Edit Product' : 'Add Product' ?></h3>

            <form id="product-form" method="POST" enctype="multipart/form-data"
                  action="../../actions/<?= $isEdit ? 'update_product_action.php' : 'add_product_action.php' ?>">

                <?php if ($isEdit): ?>
                    <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
                    <input type="hidden" name="existing_image" value="<?= htmlspecialchars($product['product_image'] ?? '') ?>">
                <?php endif; ?>

                <div class="form-grid">
                    <div class="field-full">
                        <label for="product_title">Product Title</label>
                        <input type="text" name="product_title" id="product_title" maxlength="200"
                               value="<?= $isEdit ? htmlspecialchars($product['product_title']) : '' ?>">
                    </div>

                    <div>
                        <label for="product_price">Price</label>
                        <input type="text" name="product_price" id="product_price" inputmode="decimal"
                               value="<?= $isEdit ? htmlspecialchars($product['product_price']) : '' ?>">
                    </div>

                    <div>
                        <label for="product_cat">Category</label>
                        <select name="product_cat" id="product_cat">
                            <option value="">Select</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int)$c['cat_id'] ?>" <?= ($isEdit && (int)$product['product_cat'] === (int)$c['cat_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['cat_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field-full">
                        <label for="product_brand">Brand</label>
                        <select name="product_brand" id="product_brand">
                            <option value="">Select</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= (int)$b['brand_id'] ?>" <?= ($isEdit && (int)$product['product_brand'] === (int)$b['brand_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['brand_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field-full">
                        <label for="product_desc">Description</label>
                        <textarea name="product_desc" id="product_desc" rows="4" maxlength="2000"><?= $isEdit ? htmlspecialchars($product['product_desc']) : '' ?></textarea>
                    </div>

                    <div class="field-full">
                        <label for="product_keywords">Keywords</label>
                        <input type="text" name="product_keywords" id="product_keywords" maxlength="255" placeholder="comma, separated, words"
                               value="<?= $isEdit ? htmlspecialchars($product['product_keywords']) : '' ?>">
                    </div>

                    <div class="field-full">
                        <label for="product_image">Product Image</label>

                        <?php if ($isEdit && !empty($product['product_image'])): ?>
                            <img class="current-image" src="<?= $base ?>/images/products/<?= htmlspecialchars($product['product_image']) ?>" alt="Current product image">
                        <?php endif; ?>

                        <input type="file" name="product_image" id="product_image" accept="image/jpeg,image/png,image/gif,image/webp">
                    </div>
                </div>

                <p class="error-text" id="product-error"></p>

                <div class="form-buttons">
                    <button type="submit"><?= $isEdit ? 'Update Product' : 'Add Product' ?></button>
                    <?php if ($isEdit): ?>
                        <a class="btn-link" href="product.php">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="admin-panel">
            <h3>Existing Products</h3>

            <?php if (empty($products)): ?>
                <p class="muted">No products yet. Add your first one using the form.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
                                <tr class="<?= ($isEdit && (int)$product['product_id'] === (int)$p['product_id']) ? 'row-active' : '' ?>">
                                    <td>
                                        <div class="cell-product">
                                            <?php if (!empty($p['product_image'])): ?>
                                                <img class="admin-thumb" src="<?= $base ?>/images/products/<?= htmlspecialchars($p['product_image']) ?>" alt="">
                                            <?php else: ?>
                                                <div class="admin-thumb"></div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= htmlspecialchars($p['product_title']) ?></strong>
                                                <small><?= htmlspecialchars($p['cat_name']) ?> &middot; <?= htmlspecialchars($p['brand_name']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>$<?= number_format($p['product_price'], 2) ?></td>
                                    <td><a class="btn-small-link" href="product.php?edit_id=<?= (int)$p['product_id'] ?>">Edit</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<script>
    document.getElementById('product-form').addEventListener('submit', function (e) {
        var title = document.getElementById('product_title').value.trim();
        var price = parseFloat(document.getElementById('product_price').value);
        var cat = document.getElementById('product_cat').value;
        var brand = document.getElementById('product_brand').value;
        var file = document.getElementById('product_image').files[0];
        var allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        var error = '';

        if (title.length < 2) {
            error = 'Please enter a product title.';
        } else if (isNaN(price) || price <= 0) {
            error = 'Please enter a valid price greater than 0.';
        } else if (!cat) {
            error = 'Please select a category.';
        } else if (!brand) {
            error = 'Please select a brand.';
        } else if (file && allowed.indexOf(file.type) === -1) {
            error = 'Image must be JPG, PNG, GIF or WEBP.';
        } else if (file && file.size > 2 * 1024 * 1024) {
            error = 'Image must be under 2MB.';
        }

        document.getElementById('product-error').textContent = error;

        if (error) {
            e.preventDefault();
        }
    });
</script>

<?php include '../layout/footer.php'; ?>