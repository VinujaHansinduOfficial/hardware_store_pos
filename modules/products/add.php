<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (is_post()) {
    $stmt = $pdo->prepare("INSERT INTO products (name, sku, category_id, subcategory_id, supplier_id, cost_price, selling_price, stock_qty, is_active) VALUES (:name, :sku, :category_id, :subcategory_id, :supplier_id, :cost_price, :selling_price, :stock_qty, 1)");
    $stmt->execute([
        ':name' => $_POST['name'],
        ':sku' => $_POST['sku'],
        ':category_id' => $_POST['category_id'],
        ':subcategory_id' => $_POST['subcategory_id'],
        ':supplier_id' => $_POST['supplier_id'],
        ':cost_price' => $_POST['cost_price'],
        ':selling_price' => $_POST['selling_price'],
        ':stock_qty' => $_POST['stock_qty'],
    ]);
    redirect('index.php');
}

$pageTitle = page_title('Add Product');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Add Product</h3>
<div class="card"><div class="card-body">
<form method="post">

        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control" value="<?= e(old('name')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">SKU</label>
            <input type="text" name="sku" class="form-control" value="<?= e(old('sku')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Category ID</label>
            <input type="text" name="category_id" class="form-control" value="<?= e(old('category_id')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Subcategory ID</label>
            <input type="text" name="subcategory_id" class="form-control" value="<?= e(old('subcategory_id')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Supplier ID</label>
            <input type="text" name="supplier_id" class="form-control" value="<?= e(old('supplier_id')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Cost Price</label>
            <input type="text" name="cost_price" class="form-control" value="<?= e(old('cost_price')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Selling Price</label>
            <input type="text" name="selling_price" class="form-control" value="<?= e(old('selling_price')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Stock Qty</label>
            <input type="text" name="stock_qty" class="form-control" value="<?= e(old('stock_qty')) ?>" required>
        </div>
    <button class="btn btn-primary">Save</button>
    <a href="index.php" class="btn btn-secondary">Back</a>
</form>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
