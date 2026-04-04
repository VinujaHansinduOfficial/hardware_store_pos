<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Sales History');

// ── Filters ──────────────────────────────────────────────
$search     = trim($_GET['search']    ?? '');
$status     = $_GET['status']         ?? '';
$payment    = $_GET['payment']        ?? '';
$date_from  = $_GET['date_from']      ?? '';
$date_to    = $_GET['date_to']        ?? '';

// ── Query ─────────────────────────────────────────────────
$where  = ['1=1'];
$params = [];

if ($search !== '') {
    $where[]  = '(s.id LIKE ? OR c.name LIKE ? OR c.phone LIKE ?)';
    $like     = '%' . $search . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($status !== '') {
    $where[]  = 's.status = ?';
    $params[] = $status;
}
if ($payment !== '') {
    $where[]  = 's.payment_method = ?';
    $params[] = $payment;
}
if ($date_from !== '') {
    $where[]  = 'DATE(s.sale_date) >= ?';
    $params[] = $date_from;
}
if ($date_to !== '') {
    $where[]  = 'DATE(s.sale_date) <= ?';
    $params[] = $date_to;
}

$whereSQL = implode(' AND ', $where);

$sales = $pdo->prepare("
    SELECT s.*,
           c.name  AS customer_name,
           c.phone AS customer_phone,
           (SELECT COUNT(*) FROM sale_items si WHERE si.sale_id = s.id) AS item_count
    FROM   sales s
    LEFT JOIN customers c ON c.id = s.customer_id
    WHERE  {$whereSQL}
    ORDER  BY s.sale_date DESC
    LIMIT  500
");
$sales->execute($params);
$sales = $sales->fetchAll();

// ── Summary totals for filtered results ───────────────────
$total_sales   = count($sales);
$total_revenue = array_sum(array_column($sales, 'net_amount'));
$total_disc    = array_sum(array_column($sales, 'bill_discount'));

// ── Distinct payment methods for filter dropdown ──────────
$methods = $pdo->query("SELECT DISTINCT payment_method FROM sales ORDER BY payment_method")->fetchAll(PDO::FETCH_COLUMN);

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<style>
.sh-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.sh-title h3 { margin: 0; font-family: var(--font-display); font-size: 1.5rem; }
.sh-title p  { margin: 4px 0 0; color: var(--muted); font-size: 0.85rem; }

.sh-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.sh-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 14px 18px;
    box-shadow: var(--shadow-sm);
}
.sh-card-label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 4px;
}
.sh-card-value {
    font-family: var(--font-display);
    font-size: 1.45rem;
    font-weight: 700;
    color: var(--dark);
}
.sh-card-value.accent { color: var(--primary); }
.sh-card-value.disc   { color: var(--danger);  }

.sh-filters {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 14px 16px;
    margin-bottom: 16px;
    box-shadow: var(--shadow-sm);
}
.sh-filters form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: flex-end;
}
.sh-filters .form-group { display: flex; flex-direction: column; gap: 4px; }
.sh-filters label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--muted); }
.sh-filters input,
.sh-filters select {
    height: 36px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0 10px;
    font-family: var(--font-body);
    font-size: 0.85rem;
    background: var(--bg);
    color: var(--dark);
    outline: none;
    transition: border-color var(--trans);
}
.sh-filters input:focus,
.sh-filters select:focus { border-color: var(--primary); background: #fff; }
.sh-filters input[name="search"] { min-width: 200px; }
.sh-filters .filter-actions { display: flex; gap: 8px; }
.sh-filters .btn-filter {
    height: 36px;
    padding: 0 16px;
    border-radius: var(--radius-sm);
    border: none;
    font-family: var(--font-body);
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all var(--trans);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.sh-filters .btn-filter.primary  { background: var(--primary); color: #fff; }
.sh-filters .btn-filter.primary:hover { background: var(--primary-dark); }
.sh-filters .btn-filter.reset    { background: var(--bg-2); color: var(--dark); border: 1.5px solid var(--border); }
.sh-filters .btn-filter.reset:hover { border-color: var(--muted); }

.sh-table-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.sh-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.88rem;
}
.sh-table thead th {
    background: var(--bg-2);
    padding: 10px 14px;
    text-align: left;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--muted);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
.sh-table tbody tr { border-bottom: 1px solid var(--border); transition: background var(--trans); }
.sh-table tbody tr:last-child { border-bottom: none; }
.sh-table tbody tr:hover { background: var(--primary-light); }
.sh-table tbody td { padding: 10px 14px; vertical-align: middle; }

.sale-id { font-family: var(--font-mono); font-size: 0.82rem; font-weight: 700; color: var(--primary); }
.customer-name { font-weight: 600; }
.customer-phone { font-size: 0.78rem; color: var(--muted); margin-top: 1px; }
.amount-cell { font-family: var(--font-mono); font-weight: 700; text-align: right; }
.amount-muted { font-size: 0.78rem; color: var(--muted); text-align: right; font-family: var(--font-mono); }
.sale-date-main { font-weight: 600; }
.sale-date-time { font-size: 0.78rem; color: var(--muted); }

.status-pill {
    display: inline-block;
    padding: 2px 9px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.status-completed   { background: #d1fae5; color: #065f46; }
.status-outstanding { background: #fef3c7; color: #92400e; }
.status-refunded    { background: #fee2e2; color: #991b1b; }
.status-voided      { background: #f3f4f6; color: #6b7280; }

.pay-badge {
    display: inline-block;
    padding: 2px 9px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    background: var(--bg-2);
    color: var(--muted);
    border: 1px solid var(--border);
}

.sh-actions { display: flex; gap: 6px; align-items: center; }
.sh-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 11px;
    border-radius: var(--radius-sm);
    border: 1.5px solid var(--border);
    background: var(--surface);
    font-family: var(--font-body);
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--dark);
    text-decoration: none;
    cursor: pointer;
    transition: all var(--trans);
    white-space: nowrap;
}
.sh-btn:hover { border-color: var(--primary); background: var(--primary-light); color: var(--dark); }
.sh-btn.print-btn { background: var(--dark); border-color: var(--dark); color: #fff; }
.sh-btn.print-btn:hover { background: #333; color: #fff; }

.sh-empty { text-align: center; padding: 60px 20px; color: var(--muted); }
.sh-empty .sh-empty-icon { font-size: 2.5rem; margin-bottom: 10px; }
.sh-empty p { margin: 0; font-size: 0.95rem; }

.sh-results-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 14px;
    background: var(--bg-2);
    border-bottom: 1px solid var(--border);
    font-size: 0.78rem;
    color: var(--muted);
    font-weight: 600;
}

@media (max-width: 900px) { .sh-summary { grid-template-columns: 1fr 1fr; } }
@media (max-width: 600px) { .sh-summary { grid-template-columns: 1fr; } .sh-filters form { flex-direction: column; } }
</style>

<!-- Page header -->
<div class="sh-header">
    <div class="sh-title">
        <h3>Sales History</h3>
        <p>Browse, search, and print receipts for all recorded sales</p>
    </div>
</div>

<!-- Summary cards -->
<div class="sh-summary">
    <div class="sh-card">
        <div class="sh-card-label">Total Sales</div>
        <div class="sh-card-value"><?= number_format($total_sales) ?></div>
    </div>
    <div class="sh-card">
        <div class="sh-card-label">Total Revenue</div>
        <div class="sh-card-value accent"><?= CURRENCY ?> <?= number_format($total_revenue, 2) ?></div>
    </div>
    <div class="sh-card">
        <div class="sh-card-label">Total Discounts Given</div>
        <div class="sh-card-value disc"><?= CURRENCY ?> <?= number_format($total_disc, 2) ?></div>
    </div>
</div>

<!-- Filters -->
<div class="sh-filters">
    <form method="get">
        <div class="form-group">
            <label>Search</label>
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Sale ID, customer name or phone…">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="completed"   <?= $status === 'completed'   ? 'selected' : '' ?>>Completed</option>
                <option value="outstanding" <?= $status === 'outstanding' ? 'selected' : '' ?>>Outstanding</option>
                <option value="refunded"    <?= $status === 'refunded'    ? 'selected' : '' ?>>Refunded</option>
                <option value="voided"      <?= $status === 'voided'      ? 'selected' : '' ?>>Voided</option>
            </select>
        </div>
        <div class="form-group">
            <label>Payment</label>
            <select name="payment">
                <option value="">All</option>
                <?php foreach ($methods as $m): ?>
                <option value="<?= e($m) ?>" <?= $payment === $m ? 'selected' : '' ?>><?= e($m) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>From</label>
            <input type="date" name="date_from" value="<?= e($date_from) ?>">
        </div>
        <div class="form-group">
            <label>To</label>
            <input type="date" name="date_to" value="<?= e($date_to) ?>">
        </div>
        <div class="form-group filter-actions">
            <button type="submit" class="btn-filter primary">🔍 Filter</button>
            <a href="history.php" class="btn-filter reset">✕ Reset</a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="sh-table-wrap">
    <div class="sh-results-bar">
        <span>Showing <strong><?= number_format(count($sales)) ?></strong> sale<?= count($sales) != 1 ? 's' : '' ?><?= ($search || $status || $payment || $date_from || $date_to) ? ' (filtered)' : '' ?></span>
        <?php if (count($sales) === 500): ?>
        <span style="color:var(--warning);">⚠ Results capped at 500 — use filters to narrow down.</span>
        <?php endif; ?>
    </div>

    <?php if (empty($sales)): ?>
    <div class="sh-empty">
        <div class="sh-empty-icon">🧾</div>
        <p>No sales found<?= ($search || $status || $payment || $date_from || $date_to) ? ' matching your filters' : '' ?>.</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="sh-table">
            <thead>
                <tr>
                    <th>Sale #</th>
                    <th>Date &amp; Time</th>
                    <th>Customer</th>
                    <th style="text-align:center;">Items</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th style="text-align:right;">Discount</th>
                    <th style="text-align:right;">Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                <tr>
                    <td>
                        <span class="sale-id">#<?= str_pad($sale['id'], 5, '0', STR_PAD_LEFT) ?></span>
                    </td>
                    <td>
                        <div class="sale-date-main"><?= date('d M Y', strtotime($sale['sale_date'])) ?></div>
                        <div class="sale-date-time"><?= date('H:i A', strtotime($sale['sale_date'])) ?></div>
                    </td>
                    <td>
                        <?php if ($sale['customer_name']): ?>
                            <div class="customer-name"><?= e($sale['customer_name']) ?></div>
                            <?php if ($sale['customer_phone']): ?>
                            <div class="customer-phone"><?= e($sale['customer_phone']) ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color:var(--muted);font-size:0.83rem;">Walk-in</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;"><?= (int)$sale['item_count'] ?></td>
                    <td><span class="pay-badge"><?= e($sale['payment_method']) ?></span></td>
                    <td>
                        <span class="status-pill status-<?= e($sale['status']) ?>">
                            <?= ucfirst(e($sale['status'])) ?>
                        </span>
                    </td>
                    <td class="amount-muted">
                        <?= $sale['bill_discount'] > 0
                            ? '− ' . CURRENCY . ' ' . number_format($sale['bill_discount'], 2)
                            : '—' ?>
                    </td>
                    <td class="amount-cell"><?= CURRENCY ?> <?= number_format($sale['net_amount'], 2) ?></td>
                    <td>
                        <div class="sh-actions">
                            <a class="sh-btn"
                               href="<?= BASE_URL ?>/modules/sales/receipt.php?sale_id=<?= (int)$sale['id'] ?>">
                                🧾 View
                            </a>
                            <a class="sh-btn print-btn"
                               href="<?= BASE_URL ?>/print/receipt_80mm.php?sale_id=<?= (int)$sale['id'] ?>"
                               target="_blank">
                                🖨️ Print
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
