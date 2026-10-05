<?php


require "../../core/core.php";
require_once "../../controllers/ProductController.php";
require_admin();

include '../layout/header.php';

$controller = new ProductController();
$brands = $controller->getAllBrands();

$edit_brand = null;

if (isset($_GET['edit_id'])) {
    $edit_id = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT);
    if ($edit_id) {
        $edit_brand = $controller->getBrandById($edit_id);
    }
}

?>

<h2><?= $edit_brand ? 'Edit Brand' : 'Add Brand' ?></h2>

<?php if (!empty($_SESSION['success'])): ?>
    <p style="color:green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <p style="color:red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if ($edit_brand): ?>

    <form action="../../actions/update_brand_action.php" method="POST">
        <input type="hidden" name="brand_id" value="<?= htmlspecialchars($edit_brand['brand_id']) ?>">

        <label>Brand Name</label><br>
        <input type="text" name="brand_name" value="<?= htmlspecialchars($edit_brand['brand_name']) ?>"><br><br>

        <button type="submit">Update Brand</button>
        <a href="brand.php">Cancel</a>
    </form>

<?php else: ?>

    <form action="../../actions/add_brand_action.php" method="POST">
        <label>Brand Name</label><br>
        <input type="text" name="brand_name"><br><br>
        <button type="submit">Add Brand</button>
    </form>

<?php endif; ?>

<hr>

<h3>Existing Brands</h3>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Action</th>
    </tr>

    <?php if (empty($brands)): ?>
        <tr>
            <td colspan="3">No brands yet.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($brands as $brand): ?>
            <tr>
                <td><?= htmlspecialchars($brand['brand_id']) ?></td>
                <td><?= htmlspecialchars($brand['brand_name']) ?></td>
                <td><a href="brand.php?edit_id=<?= $brand['brand_id'] ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

</table>

<?php include '../layout/footer.php'; ?>