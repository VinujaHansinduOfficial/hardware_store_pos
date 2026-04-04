<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM subcategories WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    die('Record not found');
}

if (is_post()) {
    $update = $pdo->prepare("UPDATE subcategories SET category_id = :category_id, name = :name WHERE id = :id");
    $update->execute([
        ':category_id' => $_POST['category_id'],
        ':name' => $_POST['name'],
        ':id' => $id
    ]);
    redirect('index.php');
}

$pageTitle = page_title('Edit Subcategorie');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Edit Subcategorie</h3>
<div class="card"><div class="card-body">
<form method="post">

        <div class="mb-3">
            <label class="form-label">Category ID</label>
            <input type="text" name="category_id" class="form-control" value="<?= e($row['category_id'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Subcategory Name</label>
            <input type="text" name="name" class="form-control" value="<?= e($row['name'] ?? '') ?>" required>
        </div>
    <button class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Back</a>
</form>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
