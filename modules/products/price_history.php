<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Product Price History');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
$rows = $pdo->query("SELECT * FROM product_price_history ORDER BY changed_at DESC")->fetchAll();
?>
<h3>Product Price History</h3>
<div class="card"><div class="card-body table-responsive">
<table class="table table-bordered">
    <thead><tr><th>ID</th><th>Product ID</th><th>Old Price</th><th>New Price</th><th>Changed At</th></tr></thead>
    <tbody>
        <?php foreach($rows as $row): ?>
        <tr>
            <td><?= (int)$row['id'] ?></td>
            <td><?= (int)$row['product_id'] ?></td>
            <td><?= e($row['old_price']) ?></td>
            <td><?= e($row['new_price']) ?></td>
            <td><?= e($row['changed_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
