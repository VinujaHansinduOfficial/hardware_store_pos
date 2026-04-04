<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Load dropdowns
$categories    = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();
$subcategories = $pdo->query("SELECT id, category_id, name FROM subcategories WHERE is_active = 1 ORDER BY name")->fetchAll();
$suppliers     = $pdo->query("SELECT id, name FROM suppliers WHERE is_active = 1 ORDER BY name")->fetchAll();

$errors = [];

if (is_post()) {
    $name          = trim($_POST['name'] ?? '');
    $sku           = trim($_POST['sku'] ?? '');
    $category_id   = (int)($_POST['category_id'] ?? 0) ?: null;
    $subcategory_id= (int)($_POST['subcategory_id'] ?? 0) ?: null;
    $supplier_id   = (int)($_POST['supplier_id'] ?? 0) ?: null;
    $cost_price    = (float)($_POST['cost_price'] ?? 0);
    $selling_price = (float)($_POST['selling_price'] ?? 0);
    $stock_qty     = (float)($_POST['stock_qty'] ?? 0);

    if ($name === '')  $errors[] = 'Product name is required.';
    if ($selling_price <= 0) $errors[] = 'Selling price must be greater than 0.';

    // Check SKU uniqueness
    if ($sku !== '') {
        $chk = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
        $chk->execute([$sku]);
        if ($chk->fetch()) $errors[] = 'SKU "' . e($sku) . '" is already in use.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO products (name, sku, category_id, subcategory_id, supplier_id, cost_price, selling_price, stock_qty, is_active)
             VALUES (:name, :sku, :category_id, :subcategory_id, :supplier_id, :cost_price, :selling_price, :stock_qty, 1)"
        );
        $stmt->execute([
            ':name'           => $name,
            ':sku'            => $sku ?: null,
            ':category_id'    => $category_id,
            ':subcategory_id' => $subcategory_id,
            ':supplier_id'    => $supplier_id,
            ':cost_price'     => $cost_price,
            ':selling_price'  => $selling_price,
            ':stock_qty'      => $stock_qty,
        ]);
        redirect('index.php');
    }
}

$pageTitle = page_title('Add Product');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
    <h3 style="margin:0;">Add Product</h3>
    <a href="index.php" class="btn btn-secondary btn-sm">← Back to Products</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger" style="margin-bottom:16px;">
    <?php foreach ($errors as $err): ?>
    <div>⚠ <?= e($err) ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post" style="display:grid;grid-template-columns:1fr 1fr;gap:16px 24px;">

            <!-- Product Name -->
            <div class="mb-3" style="grid-column:1/3;">
                <label class="form-label">Product Name <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" class="form-control"
                       value="<?= e(old('name')) ?>" required autofocus placeholder="e.g. PVC Pipe 1/2 inch">
            </div>

            <!-- SKU -->
            <div class="mb-3">
                <label class="form-label">SKU</label>
                <input type="text" name="sku" class="form-control"
                       value="<?= e(old('sku')) ?>" placeholder="Leave blank to auto-skip">
            </div>

            <!-- Category -->
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" id="categorySelect" onchange="filterSubcategories()">
                    <option value="">— None —</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (old('category_id') == $cat['id']) ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Subcategory -->
            <div class="mb-3">
                <label class="form-label">Subcategory</label>
                <select name="subcategory_id" class="form-select" id="subcategorySelect">
                    <option value="">— None —</option>
                    <?php foreach ($subcategories as $sub): ?>
                    <option value="<?= $sub['id'] ?>"
                            data-cat="<?= $sub['category_id'] ?>"
                            <?= (old('subcategory_id') == $sub['id']) ? 'selected' : '' ?>>
                        <?= e($sub['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Supplier -->
            <div class="mb-3">
                <label class="form-label">Supplier</label>
                <select name="supplier_id" class="form-select">
                    <option value="">— None —</option>
                    <?php foreach ($suppliers as $sup): ?>
                    <option value="<?= $sup['id'] ?>" <?= (old('supplier_id') == $sup['id']) ? 'selected' : '' ?>>
                        <?= e($sup['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Stock Qty -->
            <div class="mb-3">
                <label class="form-label">Opening Stock Qty</label>
                <input type="number" name="stock_qty" class="form-control"
                       value="<?= e(old('stock_qty', '0')) ?>" min="0" step="0.01">
            </div>

            <!-- Cost Price -->
            <div class="mb-3">
                <label class="form-label">Cost Price</label>
                <input type="number" name="cost_price" class="form-control"
                       value="<?= e(old('cost_price', '0.00')) ?>" min="0" step="0.01">
            </div>

            <!-- Selling Price -->
            <div class="mb-3">
                <label class="form-label">Selling Price <span style="color:var(--danger)">*</span></label>
                <input type="number" name="selling_price" class="form-control"
                       value="<?= e(old('selling_price', '0.00')) ?>" min="0.01" step="0.01" required>
            </div>

            <!-- Buttons -->
            <div style="grid-column:1/3;display:flex;gap:10px;padding-top:4px;">
                <button type="submit" class="btn btn-primary">✅ Save Product</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script>
function filterSubcategories() {
    const catId = document.getElementById('categorySelect').value;
    const opts  = document.querySelectorAll('#subcategorySelect option');
    opts.forEach(opt => {
        if (!opt.value) return; // keep "None"
        opt.style.display = (!catId || opt.dataset.cat === catId) ? '' : 'none';
    });
    // Reset if current selection no longer valid
    const sel = document.getElementById('subcategorySelect');
    if (sel.selectedOptions[0] && sel.selectedOptions[0].style.display === 'none') {
        sel.value = '';
    }
}
filterSubcategories();
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
