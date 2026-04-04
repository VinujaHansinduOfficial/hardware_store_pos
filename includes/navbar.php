<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>/index.php"><?= APP_NAME ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="mainNav" class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/modules/dashboard/index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/modules/categories/index.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/modules/subcategories/index.php">Subcategories</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/modules/products/index.php">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/modules/suppliers/index.php">Suppliers</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/modules/customers/index.php">Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/modules/sales/pos.php">POS</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/modules/purchases/history.php">Purchases</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/modules/reports/daily_sales.php">Reports</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-4">
