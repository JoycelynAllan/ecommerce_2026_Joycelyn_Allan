<?php

require "../../core/core.php";
require_once "../../controllers/ProductController.php";

require_admin();

$controller = new ProductController();

$editId = filter_var($_GET['edit_id'] ?? '', FILTER_VALIDATE_INT);
$brand  = false;

if ($editId && $editId > 0) {
    $brand = $controller->getBrandById($editId);

    if (!$brand) {
        $_SESSION['error'] = 'That brand could not be found.';
        redirect('brand.php');
        exit;
    }
}

$brands = $controller->getAllBrands();
$isEdit = (bool)$brand;

include '../layout/header.php';
?>

<main>
    <div class="page-title-row">
        <h2>Manage Brands</h2>
        <span class="count-pill"><?= count($brands) ?> brands</span>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <p style="color:green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="admin-split admin-split-narrow">
        <section class="admin-panel admin-form-panel">
            <h3><?= $isEdit ? 'Edit Brand' : 'Add Brand' ?></h3>

            <form id="brand-form" method="POST" action="../../actions/<?= $isEdit ? 'update_brand_action.php' : 'add_brand_action.php' ?>">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="brand_id" value="<?= (int)$brand['brand_id'] ?>">
                <?php endif; ?>

                <label for="brand_name">Brand Name</label>
                <input type="text" name="brand_name" id="brand_name" maxlength="100"
                       value="<?= $isEdit ? htmlspecialchars($brand['brand_name']) : '' ?>">

                <p class="error-text" id="brand-error"></p>

                <div class="form-buttons">
                    <button type="submit"><?= $isEdit ? 'Update Brand' : 'Add Brand' ?></button>
                    <?php if ($isEdit): ?>
                        <a class="btn-link" href="brand.php">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="admin-panel">
            <h3>Existing Brands</h3>

            <?php if (empty($brands)): ?>
                <p class="muted">No brands yet. Add one using the form.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($brands as $b): ?>
                                <tr class="<?= ($isEdit && (int)$brand['brand_id'] === (int)$b['brand_id']) ? 'row-active' : '' ?>">
                                    <td><?= (int)$b['brand_id'] ?></td>
                                    <td><strong><?= htmlspecialchars($b['brand_name']) ?></strong></td>
                                    <td><a class="btn-small-link" href="brand.php?edit_id=<?= (int)$b['brand_id'] ?>">Edit</a></td>
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
    document.getElementById('brand-form').addEventListener('submit', function (e) {
        var name = document.getElementById('brand_name').value.trim();
        var error = '';

        if (name.length < 2) {
            error = 'Please enter a brand name of at least 2 characters.';
        }

        document.getElementById('brand-error').textContent = error;

        if (error) {
            e.preventDefault();
        }
    });
</script>

<?php include '../layout/footer.php'; ?>