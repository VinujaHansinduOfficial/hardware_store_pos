<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Customers Payments');
$table = 'customer_payments';
$rows = $pdo->query("SELECT * FROM $table ORDER BY payment_date DESC")->fetchAll();
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Customers Payments</h3>
<div class="card"><div class="card-body table-responsive">
<table class="table table-bordered">
<thead><tr><th>ID</th><th>Customer ID</th><th>Amount</th><th>Method</th><th>Date</th><th>Notes</th></tr></thead>
<tbody><?php foreach($rows as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= (int)$row['customer_id'] ?></td><td><?= e($row['amount']) ?></td><td><?= e($row['payment_method']) ?></td><td><?= e($row['payment_date']) ?></td><td><?= e($row['notes']) ?></td></tr><?php endforeach; ?></tbody>
</table>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
