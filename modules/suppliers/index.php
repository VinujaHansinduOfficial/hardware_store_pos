<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Suppliers');
$rows = $pdo->query("SELECT * FROM suppliers ORDER BY id DESC")->fetchAll();
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<div class="suppliers-topbar">
    <h3 class="suppliers-page-title mb-0">Suppliers</h3>
    <a href="add.php" class="btn btn-primary">Add New</a>
</div>

<div class="card suppliers-card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped suppliers-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Outstanding Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                <?php
                    $balance = (float)($row['outstanding_balance'] ?? 0);
                    $balanceClass = 'balance-low';
                    if ($balance > 50000) {
                        $balanceClass = 'balance-high';
                    } elseif ($balance > 10000) {
                        $balanceClass = 'balance-medium';
                    }
                ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td class="supplier-name"><?= e($row['name'] ?? '') ?></td>
                    <td><?= e($row['phone'] ?? '') ?></td>
                    <td class="supplier-email"><?= e($row['email'] ?? '') ?></td>
                    <td class="supplier-address"><?= e($row['address'] ?? '') ?></td>
                    <td>
                        <span class="balance-box <?= $balanceClass ?>">
                            Rs. <?= number_format($balance, 2) ?>
                        </span>
                    </td>
                    <td><?= active_badge($row['is_active'] ?? 1) ?></td>
                    <td>
                        <div class="suppliers-actions">
                            <a class="btn btn-sm btn-warning" href="edit.php?id=<?= (int)$row['id'] ?>">Edit</a>
                            <a class="btn btn-sm btn-secondary" href="deactivate.php?id=<?= (int)$row['id'] ?>">Toggle</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
