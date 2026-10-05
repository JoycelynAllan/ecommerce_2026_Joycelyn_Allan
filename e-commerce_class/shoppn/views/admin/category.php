<?php

require "../../core/core.php";
require_once "../../controllers/ProductController.php";

require_admin();

include '../layout/header.php';

$controller = new ProductController();
$categories = $controller->getAllCategories();

$editing = false;
if (isset($_GET['edit_id'])) {
    $edit_id = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT);
    if ($edit_id && $edit_id > 0) {
        $editing = $controller->getCategoryById($edit_id);
    }
}

?>

<h2>Manage Categories</h2>

<?php if (!empty($_SESSION['success'])): ?>
    <p style="color:green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <p style="color:red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if ($editing): ?>

    <h3>Edit Category</h3>
    <form action="/~joycelyn.allan/e-commerce_class/shoppn/actions/update_category_action.php" method="POST">
        <input type="hidden" name="cat_id" value="<?= (int)$editing['cat_id'] ?>">
        <label>Category Name</label><br>
        <input type="text" name="cat_name" value="<?= htmlspecialchars($editing['cat_name']) ?>"><br><br>
        <button type="submit">Update Category</button>
        <a href="category.php">Cancel</a>
    </form>

<?php else: ?>

    <form action="/~joycelyn.allan/e-commerce_class/shoppn/actions/add_category_action.php" method="POST">
        <label>Category Name</label><br>
        <input type="text" name="cat_name"><br><br>
        <button type="submit">Add Category</button>
    </form>

<?php endif; ?>

<hr>

<h3>Existing Categories</h3>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Action</th>
    </tr>

    <?php if (empty($categories)): ?>
        <tr>
            <td colspan="3">No categories yet.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= htmlspecialchars($cat['cat_id']) ?></td>
                <td><?= htmlspecialchars($cat['cat_name']) ?></td>
                <td><a href="category.php?edit_id=<?= (int)$cat['cat_id'] ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

</table>

<?php include '../layout/footer.php'; ?>