<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Products');
$rows = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Products</h3>
    <a href="add.php" class="btn btn-primary">Add New</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead><tr><th>ID</th><th>name</th><th>sku</th><th>category_id</th><th>subcategory_id</th><th>supplier_id</th><th>cost_price</th><th>selling_price</th><th>stock_qty</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td><?= e($row['name'] ?? '') ?></td><td><?= e($row['sku'] ?? '') ?></td><td><?= e($row['category_id'] ?? '') ?></td><td><?= e($row['subcategory_id'] ?? '') ?></td><td><?= e($row['supplier_id'] ?? '') ?></td><td><?= e($row['cost_price'] ?? '') ?></td><td><?= e($row['selling_price'] ?? '') ?></td><td><?= e($row['stock_qty'] ?? '') ?></td>
                    <td><?= active_badge($row['is_active'] ?? 1) ?></td>
                    <td class="module-actions">
                        <a class="btn btn-sm btn-warning" href="edit.php?id=<?= (int)$row['id'] ?>">Edit</a>
                        <a class="btn btn-sm btn-secondary" href="deactivate.php?id=<?= (int)$row['id'] ?>">Toggle</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
