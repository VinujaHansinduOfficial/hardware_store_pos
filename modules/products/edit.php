<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) { die('Product not found.'); }

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

    if ($name === '') $errors[] = 'Product name is required.';
    if ($selling_price <= 0) $errors[] = 'Selling price must be greater than 0.';

    // SKU uniqueness (exclude self)
    if ($sku !== '') {
        $chk = $pdo->prepare("SELECT id FROM products WHERE sku = ? AND id != ?");
        $chk->execute([$sku, $id]);
        if ($chk->fetch()) $errors[] = 'SKU "' . e($sku) . '" is already in use by another product.';
    }

    if (empty($errors)) {
        // Log price change if selling price changed
        if ((float)$row['selling_price'] !== $selling_price) {
            $pdo->prepare(
                "INSERT INTO product_price_history (product_id, old_price, new_price) VALUES (?, ?, ?)"
            )->execute([$id, $row['selling_price'], $selling_price]);
        }

        $update = $pdo->prepare(
            "UPDATE products SET name=:name, sku=:sku, category_id=:category_id,
             subcategory_id=:subcategory_id, supplier_id=:supplier_id,
             cost_price=:cost_price, selling_price=:selling_price, stock_qty=:stock_qty
             WHERE id=:id"
        );
        $update->execute([
            ':name'           => $name,
            ':sku'            => $sku ?: null,
            ':category_id'    => $category_id,
            ':subcategory_id' => $subcategory_id,
            ':supplier_id'    => $supplier_id,
            ':cost_price'     => $cost_price,
            ':selling_price'  => $selling_price,
            ':stock_qty'      => $stock_qty,
            ':id'             => $id,
        ]);
        redirect('index.php');
    }
    // On error, keep POST values
    $row = array_merge($row, $_POST);
}

$pageTitle = page_title('Edit Product');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
    <h3 style="margin:0;">Edit Product <span style="font-family:var(--font-mono);font-size:0.85rem;color:var(--muted);">#<?= $id ?></span></h3>
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
                       value="<?= e($row['name'] ?? '') ?>" required autofocus>
            </div>

            <!-- SKU -->
            <div class="mb-3">
                <label class="form-label">SKU</label>
                <input type="text" name="sku" class="form-control"
                       value="<?= e($row['sku'] ?? '') ?>">
            </div>

            <!-- Category -->
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" id="categorySelect" onchange="filterSubcategories()">
                    <option value="">— None —</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                            <?= ((int)($row['category_id'] ?? 0) === (int)$cat['id']) ? 'selected' : '' ?>>
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
                            <?= ((int)($row['subcategory_id'] ?? 0) === (int)$sub['id']) ? 'selected' : '' ?>>
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
                    <option value="<?= $sup['id'] ?>"
                            <?= ((int)($row['supplier_id'] ?? 0) === (int)$sup['id']) ? 'selected' : '' ?>>
                        <?= e($sup['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Stock Qty -->
            <div class="mb-3">
                <label class="form-label">Stock Qty</label>
                <input type="number" name="stock_qty" class="form-control"
                       value="<?= e($row['stock_qty'] ?? '0') ?>" min="0" step="0.01">
            </div>

            <!-- Cost Price -->
            <div class="mb-3">
                <label class="form-label">Cost Price</label>
                <input type="number" name="cost_price" class="form-control"
                       value="<?= e($row['cost_price'] ?? '0.00') ?>" min="0" step="0.01">
            </div>

            <!-- Selling Price -->
            <div class="mb-3">
                <label class="form-label">Selling Price <span style="color:var(--danger)">*</span></label>
                <input type="number" name="selling_price" class="form-control"
                       value="<?= e($row['selling_price'] ?? '0.00') ?>" min="0.01" step="0.01" required>
                <small style="color:var(--muted);font-size:0.75rem;">Price changes are logged automatically.</small>
            </div>

            <!-- Buttons -->
            <div style="grid-column:1/3;display:flex;gap:10px;padding-top:4px;">
                <button type="submit" class="btn btn-primary">💾 Update Product</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <a href="price_history.php?id=<?= $id ?>" class="btn btn-outline-secondary btn-sm" style="margin-left:auto;">📈 Price History</a>
            </div>

        </form>
    </div>
</div>

<script>
function filterSubcategories() {
    const catId = document.getElementById('categorySelect').value;
    const opts  = document.querySelectorAll('#subcategorySelect option');
    opts.forEach(opt => {
        if (!opt.value) return;
        opt.style.display = (!catId || opt.dataset.cat === catId) ? '' : 'none';
    });
    const sel = document.getElementById('subcategorySelect');
    if (sel.selectedOptions[0] && sel.selectedOptions[0].style.display === 'none') {
        sel.value = '';
    }
}
filterSubcategories();
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
