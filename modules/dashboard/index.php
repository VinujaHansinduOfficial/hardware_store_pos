<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Dashboard');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$productCount = get_count($pdo, 'products', 'is_active = 1');
$categoryCount = get_count($pdo, 'categories', 'is_active = 1');
$supplierCount = get_count($pdo, 'suppliers', 'is_active = 1');
$customerCount = get_count($pdo, 'customers', 'is_active = 1');
?>
<div class="row g-3">
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><h6>Products</h6><h2><?= $productCount ?></h2></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><h6>Categories</h6><h2><?= $categoryCount ?></h2></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><h6>Suppliers</h6><h2><?= $supplierCount ?></h2></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><h6>Customers</h6><h2><?= $customerCount ?></h2></div></div></div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h4>Quick Links</h4>
        <div class="quick-links">

    <a href="<?= BASE_URL ?>/modules/sales/pos.php" class="quick-card">
        <i>💰</i>
        <h6>Open POS</h6>
        <p>Start billing & sales</p>
    </a>

    <a href="<?= BASE_URL ?>/modules/products/index.php" class="quick-card">
        <i>📦</i>
        <h6>Products</h6>
        <p>Manage items</p>
    </a>

    <a href="<?= BASE_URL ?>/modules/purchases/purchase_order.php" class="quick-card">
        <i>🧾</i>
        <h6>Purchases</h6>
        <p>Order stock</p>
    </a>

    <a href="<?= BASE_URL ?>/modules/reports/daily_sales.php" class="quick-card">
        <i>📊</i>
        <h6>Reports</h6>
        <p>View analytics</p>
    </a>
  </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
