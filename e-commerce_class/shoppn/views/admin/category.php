<?php

require "../../core/core.php";
require_once "../../controllers/ProductController.php";

require_admin();

$controller = new ProductController();

$editId   = filter_var($_GET['edit_id'] ?? '', FILTER_VALIDATE_INT);
$category = false;

if ($editId && $editId > 0) {
    $category = $controller->getCategoryById($editId);

    if (!$category) {
        $_SESSION['error'] = 'That category could not be found.';
        redirect('category.php');
        exit;
    }
}

$categories = $controller->getAllCategories();
$isEdit     = (bool)$category;

include '../layout/header.php';
?>

<main>
    <div class="page-title-row">
        <h2>Manage Categories</h2>
        <span class="count-pill"><?= count($categories) ?> categories</span>
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
            <h3><?= $isEdit ? 'Edit Category' : 'Add Category' ?></h3>

            <form id="category-form" method="POST" action="../../actions/<?= $isEdit ? 'update_category_action.php' : 'add_category_action.php' ?>">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="cat_id" value="<?= (int)$category['cat_id'] ?>">
                <?php endif; ?>

                <label for="cat_name">Category Name</label>
                <input type="text" name="cat_name" id="cat_name" maxlength="100"
                       value="<?= $isEdit ? htmlspecialchars($category['cat_name']) : '' ?>">

                <p class="error-text" id="category-error"></p>

                <div class="form-buttons">
                    <button type="submit"><?= $isEdit ? 'Update Category' : 'Add Category' ?></button>
                    <?php if ($isEdit): ?>
                        <a class="btn-link" href="category.php">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="admin-panel">
            <h3>Existing Categories</h3>

            <?php if (empty($categories)): ?>
                <p class="muted">No categories yet. Add one using the form.</p>
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
                            <?php foreach ($categories as $c): ?>
                                <tr class="<?= ($isEdit && (int)$category['cat_id'] === (int)$c['cat_id']) ? 'row-active' : '' ?>">
                                    <td><?= (int)$c['cat_id'] ?></td>
                                    <td><strong><?= htmlspecialchars($c['cat_name']) ?></strong></td>
                                    <td><a class="btn-small-link" href="category.php?edit_id=<?= (int)$c['cat_id'] ?>">Edit</a></td>
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
    document.getElementById('category-form').addEventListener('submit', function (e) {
        var name = document.getElementById('cat_name').value.trim();
        var error = '';

        if (name.length < 2) {
            error = 'Please enter a category name of at least 2 characters.';
        }

        document.getElementById('category-error').textContent = error;

        if (error) {
            e.preventDefault();
        }
    });
</script>

<?php include '../layout/footer.php'; ?>