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

<!-- Inline CSS for Dashboard -->
<style>
/* =========================
   DASHBOARD STYLE
========================= */
body {
  font-family: 'Segoe UI', Tahoma, sans-serif;
  background-color: #f4f6f9;
  margin: 0;
  color: #333;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h2 {
  font-size: 24px;
  font-weight: 600;
}

.page-header p {
  color: #6b7280;
  font-size: 14px;
}

.row.g-3 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.card-stat {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  transition: 0.3s;
}

.card-stat:hover {
  transform: translateY(-5px);
}

.card-stat h6 {
  font-size: 14px;
  color: #6b7280;
}

.card-stat h2 {
  font-size: 26px;
  margin-top: 5px;
}

.card.mt-4 {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.quick-links {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 15px;
  margin-top: 15px;
}

.quick-card {
  background: #f9fafb;
  padding: 15px;
  border-radius: 10px;
  text-decoration: none;
  color: #111;
  border: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  transition: 0.3s;
}

.quick-card:hover {
  background: #3b82f6;
  color: #fff;
}

.quick-card .icon, .quick-card i {
  font-size: 24px;
  margin-bottom: 8px;
}

.quick-card h6 {
  margin-bottom: 5px;
}

.quick-card p {
  font-size: 13px;
  color: #6b7280;
}

.quick-card:hover p {
  color: #e0e7ff;
}
</style>

<!-- Dashboard Content -->
<div class="page-header">
    <h2>Dashboard Overview</h2>
    <p>Welcome back! Here's what's happening in your store.</p>
</div>

<div class="row g-3">
    <div class="card-stat blue">
        <h6>Products</h6>
        <h2><?= $productCount ?></h2>
    </div>
    <div class="card-stat green">
        <h6>Categories</h6>
        <h2><?= $categoryCount ?></h2>
    </div>
    <div class="card-stat orange">
        <h6>Suppliers</h6>
        <h2><?= $supplierCount ?></h2>
    </div>
    <div class="card-stat red">
        <h6>Customers</h6>
        <h2><?= $customerCount ?></h2>
    </div>
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