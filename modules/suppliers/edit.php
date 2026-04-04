<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    die('Record not found');
}

if (is_post()) {
    $update = $pdo->prepare("UPDATE suppliers SET name = :name, phone = :phone, email = :email, address = :address, outstanding_balance = :outstanding_balance WHERE id = :id");
    $update->execute([
        ':name' => $_POST['name'],
        ':phone' => $_POST['phone'],
        ':email' => $_POST['email'],
        ':address' => $_POST['address'],
        ':outstanding_balance' => $_POST['outstanding_balance'],
        ':id' => $id
    ]);
    redirect('index.php');
}

$pageTitle = page_title('Edit Supplier');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Edit Supplier</h3>
<div class="card"><div class="card-body">
<form method="post">

        <div class="mb-3">
            <label class="form-label">Supplier Name</label>
            <input type="text" name="name" class="form-control" value="<?= e($row['name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= e($row['phone'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="text" name="email" class="form-control" value="<?= e($row['email'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?= e($row['address'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Outstanding Balance</label>
            <input type="text" name="outstanding_balance" class="form-control" value="<?= e($row['outstanding_balance'] ?? '') ?>" required>
        </div>
    <button class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Back</a>
</form>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
