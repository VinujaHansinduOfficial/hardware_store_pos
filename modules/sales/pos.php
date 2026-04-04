<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

$products = $pdo->query("SELECT id, name, selling_price, stock_qty FROM products WHERE is_active = 1 ORDER BY name")->fetchAll();
$pageTitle = page_title('POS');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Sales POS</h3>
<div class="pos-grid">
    <div class="card">
        <div class="card-body">
            <h5>Products</h5>
            <table class="table table-bordered">
                <thead><tr><th>Name</th><th>Price</th><th>Stock</th></tr></thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= e($product['name']) ?></td>
                        <td><?= e($product['selling_price']) ?></td>
                        <td><?= e($product['stock_qty']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5>Billing</h5>
            <form method="post" action="../../ajax/sales_ajax.php">
                <div class="mb-3">
                    <label class="form-label">Customer ID</label>
                    <input type="number" name="customer_id" class="form-control" value="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select class="form-select" name="payment_method">
                        <option>Cash</option>
                        <option>Card</option>
                        <option>Bank Transfer</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Discount</label>
                    <input type="number" step="0.01" class="form-control" name="bill_discount" value="0">
                </div>
                <button class="btn btn-primary">Save Sale</button>
                <a class="btn btn-outline-secondary" href="suspended_bills.php">Suspended Bills</a>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
