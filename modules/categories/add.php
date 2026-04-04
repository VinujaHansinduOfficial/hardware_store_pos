<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (is_post()) {
    $stmt = $pdo->prepare("INSERT INTO categories (name, is_active) VALUES (:name, 1)");
    $stmt->execute([
        ':name' => $_POST['name'],
    ]);
    redirect('index.php');
}

$pageTitle = page_title('Add Categorie');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Add Categorie</h3>
<div class="card"><div class="card-body">
<form method="post">

        <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" name="name" class="form-control" value="<?= e(old('name')) ?>" required>
        </div>
    <button class="btn btn-primary">Save</button>
    <a href="index.php" class="btn btn-secondary">Back</a>
</form>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
