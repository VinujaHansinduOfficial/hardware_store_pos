<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Categories');
$rows = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Categories</h3>
    <a href="add.php" class="btn btn-primary">Add New</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead><tr><th>ID</th><th>name</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td><?= e($row['name'] ?? '') ?></td>
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
