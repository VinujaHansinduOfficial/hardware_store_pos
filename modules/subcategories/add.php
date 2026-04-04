<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (is_post()) {
    $stmt = $pdo->prepare("INSERT INTO subcategories (category_id, name, is_active) VALUES (:category_id, :name, 1)");
    $stmt->execute([
        ':category_id' => $_POST['category_id'],
        ':name' => $_POST['name'],
    ]);
    redirect('index.php');
}

$pageTitle = page_title('Add Subcategorie');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Add Subcategorie</h3>
<div class="card"><div class="card-body">
<form method="post">

        <div class="mb-3">
            <label class="form-label">Category ID</label>
            <input type="text" name="category_id" class="form-control" value="<?= e(old('category_id')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Subcategory Name</label>
            <input type="text" name="name" class="form-control" value="<?= e(old('name')) ?>" required>
        </div>
    <button class="btn btn-primary">Save</button>
    <a href="index.php" class="btn btn-secondary">Back</a>
</form>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
