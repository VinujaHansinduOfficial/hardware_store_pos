<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (is_post()) {
    $stmt = $pdo->prepare("INSERT INTO suppliers (name, phone, email, address, outstanding_balance, is_active) VALUES (:name, :phone, :email, :address, :outstanding_balance, 1)");
    $stmt->execute([
        ':name' => $_POST['name'],
        ':phone' => $_POST['phone'],
        ':email' => $_POST['email'],
        ':address' => $_POST['address'],
        ':outstanding_balance' => $_POST['outstanding_balance'],
    ]);
    redirect('index.php');
}

$pageTitle = page_title('Add Supplier');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3 class="supplier-form-title">Add Supplier</h3>
<div class="card supplier-form-card">
    <div class="card-body">
        <form method="post">

        <div class="mb-3">
            <label class="form-label">Supplier Name</label>
            <input type="text" name="name" class="form-control" value="<?= e(old('name')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= e(old('phone')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="text" name="email" class="form-control" value="<?= e(old('email')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?= e(old('address')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Outstanding Balance</label>
            <input type="text" name="outstanding_balance" class="form-control" value="<?= e(old('outstanding_balance')) ?>" required>
        </div>
        <div class="supplier-form-actions">
                    <button class="btn btn-primary">Save</button>
                    <a href="index.php" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
