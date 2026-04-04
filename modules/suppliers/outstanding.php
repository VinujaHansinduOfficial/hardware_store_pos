<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Suppliers Outstanding');
$rows = $pdo->query("SELECT id, name, outstanding_balance FROM suppliers WHERE is_active = 1 ORDER BY outstanding_balance DESC")->fetchAll();
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Suppliers Outstanding</h3>
<div class="card"><div class="card-body table-responsive">
<table class="table table-bordered">
<thead><tr><th>ID</th><th>Name</th><th>Outstanding Balance</th></tr></thead>
<tbody><?php foreach($rows as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['name']) ?></td><td><?= e($row['outstanding_balance']) ?></td></tr><?php endforeach; ?></tbody>
</table>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
