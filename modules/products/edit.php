<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    die('Record not found');
}

if (is_post()) {
    $update = $pdo->prepare("UPDATE products SET name = :name, sku = :sku, category_id = :category_id, subcategory_id = :subcategory_id, supplier_id = :supplier_id, cost_price = :cost_price, selling_price = :selling_price, stock_qty = :stock_qty WHERE id = :id");
    $update->execute([
        ':name' => $_POST['name'],
        ':sku' => $_POST['sku'],
        ':category_id' => $_POST['category_id'],
        ':subcategory_id' => $_POST['subcategory_id'],
        ':supplier_id' => $_POST['supplier_id'],
        ':cost_price' => $_POST['cost_price'],
        ':selling_price' => $_POST['selling_price'],
        ':stock_qty' => $_POST['stock_qty'],
        ':id' => $id
    ]);
    redirect('index.php');
}

$pageTitle = page_title('Edit Product');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Edit Product</h3>
<div class="card"><div class="card-body">
<form method="post">

        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control" value="<?= e($row['name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">SKU</label>
            <input type="text" name="sku" class="form-control" value="<?= e($row['sku'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Category ID</label>
            <input type="text" name="category_id" class="form-control" value="<?= e($row['category_id'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Subcategory ID</label>
            <input type="text" name="subcategory_id" class="form-control" value="<?= e($row['subcategory_id'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Supplier ID</label>
            <input type="text" name="supplier_id" class="form-control" value="<?= e($row['supplier_id'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Cost Price</label>
            <input type="text" name="cost_price" class="form-control" value="<?= e($row['cost_price'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Selling Price</label>
            <input type="text" name="selling_price" class="form-control" value="<?= e($row['selling_price'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Stock Qty</label>
            <input type="text" name="stock_qty" class="form-control" value="<?= e($row['stock_qty'] ?? '') ?>" required>
        </div>
    <button class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Back</a>
</form>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
