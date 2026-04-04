<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    die('Record not found');
}

if (is_post()) {
    $update = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
    $update->execute([
        ':name' => $_POST['name'],
        ':id' => $id
    ]);
    redirect('index.php');
}

$pageTitle = page_title('Edit Categorie');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Edit Categorie</h3>
<div class="card"><div class="card-body">
<form method="post">

        <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" name="name" class="form-control" value="<?= e($row['name'] ?? '') ?>" required>
        </div>
    <button class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Back</a>
</form>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
