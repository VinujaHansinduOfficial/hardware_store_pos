<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Daily Sales Summary');

// ── Selected date (default: today) ───────────────────────
$date = $_GET['date'] ?? date('Y-m-d');
$date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date : date('Y-m-d');
$display_date = date('l, d F Y', strtotime($date));

// ── Sales for the day ─────────────────────────────────────
$sales = $pdo->prepare("
    SELECT s.*,
           c.name  AS customer_name,
           c.phone AS customer_phone,
           (SELECT COUNT(*) FROM sale_items si WHERE si.sale_id = s.id) AS item_count
    FROM   sales s
    LEFT JOIN customers c ON c.id = s.customer_id
    WHERE  DATE(s.sale_date) = ?
    ORDER  BY s.sale_date ASC
$sales->execute([$date]);
");
$sales = $sales->fetchAll();

// ── Aggregate totals ──────────────────────────────────────
$total_transactions = count($sales);
$total_revenue      = array_sum(array_column($sales, 'net_amount'));
$total_gross        = array_sum(array_column($sales, 'total_amount'));
$total_discounts    = array_sum(array_column($sales, 'bill_discount'));
$outstanding_amt    = array_sum(array_column(
    array_filter($sales, fn($s) => $s['status'] === 'outstanding'),
    'net_amount'
));

// Payment method breakdown
$by_payment = [];
foreach ($sales as $s) {
    $m = $s['payment_method'];
    if (!isset($by_payment[$m])) $by_payment[$m] = ['count' => 0, 'amount' => 0];
    $by_payment[$m]['count']++;
    $by_payment[$m]['amount'] += $s['net_amount'];
}
arsort($by_payment);

// Hourly breakdown (for mini chart)
$by_hour = array_fill(0, 24, 0);
foreach ($sales as $s) {
    $h = (int)date('G', strtotime($s['sale_date']));
    $by_hour[$h] += $s['net_amount'];
}
$peak_hour    = array_search(max($by_hour), $by_hour);
$peak_revenue = max($by_hour);

// Top products sold today
$top_products = $pdo->prepare("
    SELECT p.name, p.sku,
           SUM(si.qty)        AS total_qty,
           SUM(si.line_total) AS total_revenue
    FROM   sale_items si
    JOIN   products p   ON p.id = si.product_id
    JOIN   sales s      ON s.id = si.sale_id
    WHERE  DATE(s.sale_date) = ?
    GROUP  BY p.id
    ORDER  BY total_revenue DESC
    LIMIT  8
");
$top_products->execute([$date]);
$top_products = $top_products->fetchAll();

// Item discounts total
$item_disc = $pdo->prepare("
    SELECT COALESCE(SUM(si.item_discount), 0) AS total
    FROM   sale_items si
    JOIN   sales s ON s.id = si.sale_id
    WHERE  DATE(s.sale_date) = ?
");
$item_disc->execute([$date]);
$total_item_disc = (float)$item_disc->fetch()['total'];

// Prev/next day links
$prev_date = date('Y-m-d', strtotime($date . ' -1 day'));
$next_date = date('Y-m-d', strtotime($date . ' +1 day'));
$is_today  = $date === date('Y-m-d');

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<style>
/* ── Page chrome ── */
.ds-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.ds-title h3 { margin: 0; font-family: var(--font-display); font-size: 1.5rem; }
.ds-title p  { margin: 4px 0 0; color: var(--muted); font-size: 0.85rem; }

/* Date navigator */
.ds-date-nav {
    display: flex;
    align-items: center;
    gap: 8px;
}
.ds-nav-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: var(--radius-sm);
    border: 1.5px solid var(--border);
    background: var(--surface);
    color: var(--dark);
    text-decoration: none;
    font-size: 1rem;
    transition: all var(--trans);
}
.ds-nav-btn:hover { border-color: var(--primary); background: var(--primary-light); color: var(--dark); }
.ds-nav-btn.disabled { opacity: 0.35; pointer-events: none; }

.ds-date-form { display: flex; align-items: center; gap: 6px; }
.ds-date-input {
    height: 34px;
    padding: 0 10px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 0.85rem;
    background: var(--bg);
    color: var(--dark);
    outline: none;
    transition: border-color var(--trans);
}
.ds-date-input:focus { border-color: var(--primary); background: #fff; }
.ds-go-btn {
    height: 34px;
    padding: 0 14px;
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-weight: 700;
    font-size: 0.82rem;
    cursor: pointer;
    transition: background var(--trans);
}
.ds-go-btn:hover { background: var(--primary-dark); }

/* ── KPI cards ── */
.ds-kpi {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.ds-kpi-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 18px;
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
}
.ds-kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--kpi-color, var(--border));
}
.ds-kpi-card.accent { --kpi-color: var(--primary); }
.ds-kpi-card.success { --kpi-color: var(--success); }
.ds-kpi-card.warning { --kpi-color: var(--warning); }
.ds-kpi-card.danger  { --kpi-color: var(--danger);  }

.kpi-label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 6px;
}
.kpi-value {
    font-family: var(--font-display);
    font-size: 1.55rem;
    font-weight: 700;
    color: var(--dark);
    line-height: 1.1;
}
.kpi-value.accent  { color: var(--primary); }
.kpi-value.success { color: var(--success); }
.kpi-value.danger  { color: var(--danger);  }
.kpi-sub {
    font-size: 0.75rem;
    color: var(--muted);
    margin-top: 4px;
}

/* ── Two-column layout ── */
.ds-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 16px;
    margin-bottom: 20px;
    align-items: start;
}

/* ── Panel card ── */
.ds-panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 16px;
}
.ds-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: var(--bg-2);
    border-bottom: 1px solid var(--border);
}
.ds-panel-title {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    color: var(--muted);
}
.ds-panel-body { padding: 16px; }

/* ── Hourly bar chart ── */
.hour-chart {
    display: flex;
    align-items: flex-end;
    gap: 3px;
    height: 64px;
    padding: 0 2px;
}
.hour-bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px; }
.hour-bar {
    width: 100%;
    background: var(--primary);
    border-radius: 2px 2px 0 0;
    opacity: 0.85;
    min-height: 2px;
    transition: opacity var(--trans);
}
.hour-bar:hover { opacity: 1; }
.hour-bar.peak { background: var(--primary-dark); opacity: 1; }
.hour-bar.zero { background: var(--bg-2); opacity: 1; }
.hour-label { font-size: 7px; color: var(--muted); font-family: var(--font-mono); }

/* ── Payment breakdown ── */
.pay-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid var(--border);
}
.pay-row:last-child { border-bottom: none; }
.pay-name { font-weight: 700; font-size: 0.85rem; flex: 1; }
.pay-count { font-size: 0.75rem; color: var(--muted); }
.pay-amount { font-family: var(--font-mono); font-weight: 700; font-size: 0.88rem; text-align: right; }
.pay-bar-track { width: 100%; height: 4px; background: var(--bg-2); border-radius: 2px; margin-top: 4px; }
.pay-bar-fill  { height: 4px; background: var(--primary); border-radius: 2px; }

/* ── Top products ── */
.prod-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid var(--border);
}
.prod-row:last-child { border-bottom: none; }
.prod-rank {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--bg-2);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.68rem;
    font-weight: 700;
    color: var(--muted);
    flex-shrink: 0;
}
.prod-rank.gold   { background: #fef3c7; border-color: #f59e0b; color: #92400e; }
.prod-rank.silver { background: #f1f5f9; border-color: #94a3b8; color: #475569; }
.prod-rank.bronze { background: #fdf4e8; border-color: #d97706; color: #7c4a0a; }
.prod-info { flex: 1; min-width: 0; }
.prod-name { font-weight: 600; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.prod-sku  { font-size: 0.72rem; color: var(--muted); font-family: var(--font-mono); }
.prod-qty  { font-size: 0.78rem; color: var(--muted); text-align: right; white-space: nowrap; }
.prod-rev  { font-family: var(--font-mono); font-weight: 700; font-size: 0.85rem; text-align: right; white-space: nowrap; }

/* ── Transactions table ── */
.ds-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.88rem;
}
.ds-table thead th {
    background: var(--bg-2);
    padding: 9px 14px;
    text-align: left;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--muted);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
.ds-table tbody tr { border-bottom: 1px solid var(--border); transition: background var(--trans); }
.ds-table tbody tr:last-child { border-bottom: none; }
.ds-table tbody tr:hover { background: var(--primary-light); }
.ds-table tbody td { padding: 9px 14px; vertical-align: middle; }

.sale-id { font-family: var(--font-mono); font-weight: 700; color: var(--primary); font-size: 0.82rem; }
.sale-time { font-family: var(--font-mono); font-size: 0.82rem; color: var(--muted); }
.cust-name { font-weight: 600; font-size: 0.85rem; }
.cust-phone { font-size: 0.75rem; color: var(--muted); }
.amount-cell { font-family: var(--font-mono); font-weight: 700; text-align: right; }
.disc-cell { font-family: var(--font-mono); font-size: 0.8rem; color: var(--muted); text-align: right; }

.status-pill {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.68rem;
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
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.68rem;
    font-weight: 700;
    background: var(--bg-2);
    color: var(--muted);
    border: 1px solid var(--border);
}

.act-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: var(--radius-sm);
    border: 1.5px solid var(--border);
    background: var(--surface);
    font-family: var(--font-body);
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--dark);
    text-decoration: none;
    transition: all var(--trans);
    white-space: nowrap;
}
.act-btn:hover { border-color: var(--primary); background: var(--primary-light); color: var(--dark); }
.act-btn.print { background: var(--dark); border-color: var(--dark); color: #fff; }
.act-btn.print:hover { background: #333; color: #fff; }

/* Empty state */
.ds-empty {
    text-align: center;
    padding: 60px 20px;
    color: var(--muted);
}
.ds-empty-icon { font-size: 2.8rem; margin-bottom: 12px; }
.ds-empty p { margin: 0; font-size: 0.95rem; }

/* Total row */
.ds-table tfoot td {
    padding: 10px 14px;
    font-weight: 700;
    background: var(--bg-2);
    border-top: 2px solid var(--border);
    font-size: 0.88rem;
}

@media (max-width: 1000px) {
    .ds-kpi  { grid-template-columns: repeat(2, 1fr); }
    .ds-grid { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .ds-kpi { grid-template-columns: 1fr 1fr; }
}
</style>

<!-- ── Page header ── -->
<div class="ds-header">
    <div class="ds-title">
        <h3>Daily Sales Summary</h3>
        <p><?= $display_date ?></p>
    </div>
    <div class="ds-date-nav">
        <a href="?date=<?= $prev_date ?>" class="ds-nav-btn" title="Previous day">‹</a>
        <form method="get" class="ds-date-form">
            <input type="date" name="date" value="<?= e($date) ?>" class="ds-date-input">
            <button type="submit" class="ds-go-btn">Go</button>
        </form>
        <a href="?date=<?= $next_date ?>" class="ds-nav-btn <?= $is_today ? 'disabled' : '' ?>" title="Next day">›</a>
        <?php if (!$is_today): ?>
        <a href="daily_summary.php" class="ds-nav-btn" title="Jump to today" style="font-size:0.7rem;font-weight:700;width:auto;padding:0 10px;">Today</a>
        <?php endif; ?>
    </div>
</div>

<!-- ── KPI row ── -->
<div class="ds-kpi">
    <div class="ds-kpi-card accent">
        <div class="kpi-label">Net Revenue</div>
        <div class="kpi-value accent"><?= CURRENCY ?> <?= number_format($total_revenue, 2) ?></div>
        <div class="kpi-sub">Gross <?= CURRENCY ?> <?= number_format($total_gross, 2) ?></div>
    </div>
    <div class="ds-kpi-card success">
        <div class="kpi-label">Transactions</div>
        <div class="kpi-value"><?= $total_transactions ?></div>
        <div class="kpi-sub">
            <?= $total_transactions > 0
                ? 'Avg ' . CURRENCY . ' ' . number_format($total_revenue / $total_transactions, 2) . ' / sale'
                : 'No sales yet' ?>
        </div>
    </div>
    <div class="ds-kpi-card warning">
        <div class="kpi-label">Discounts Given</div>
        <div class="kpi-value"><?= CURRENCY ?> <?= number_format($total_discounts + $total_item_disc, 2) ?></div>
        <div class="kpi-sub">Bill <?= CURRENCY ?> <?= number_format($total_discounts, 2) ?> · Item <?= CURRENCY ?> <?= number_format($total_item_disc, 2) ?></div>
    </div>
    <div class="ds-kpi-card danger">
        <div class="kpi-label">Outstanding</div>
        <div class="kpi-value <?= $outstanding_amt > 0 ? 'danger' : '' ?>">
            <?= CURRENCY ?> <?= number_format($outstanding_amt, 2) ?>
        </div>
        <div class="kpi-sub">
            <?= count(array_filter($sales, fn($s) => $s['status'] === 'outstanding')) ?> pending sale<?= count(array_filter($sales, fn($s) => $s['status'] === 'outstanding')) != 1 ? 's' : '' ?>
        </div>
    </div>
</div>

<?php if (empty($sales)): ?>
<!-- ── Empty state ── -->
<div class="ds-panel">
    <div class="ds-empty">
        <div class="ds-empty-icon">📭</div>
        <p>No sales recorded on <?= $display_date ?>.</p>
    </div>
</div>

<?php else: ?>
<!-- ── Charts + Breakdown ── -->
<div class="ds-grid">

    <!-- LEFT: Hourly activity -->
    <div class="ds-panel">
        <div class="ds-panel-head">
            <span class="ds-panel-title">Sales Activity by Hour</span>
            <?php if ($peak_revenue > 0): ?>
            <span style="font-size:0.75rem;color:var(--muted);">
                Peak: <?= str_pad($peak_hour, 2, '0', STR_PAD_LEFT) ?>:00 — <?= CURRENCY ?> <?= number_format($peak_revenue, 2) ?>
            </span>
            <?php endif; ?>
        </div>
        <div class="ds-panel-body">
            <?php
            $max_hour = max($by_hour) ?: 1;
            ?>
            <div class="hour-chart">
                <?php for ($h = 0; $h < 24; $h++): ?>
                <?php
                    $pct    = round(($by_hour[$h] / $max_hour) * 60);
                    $cls    = $by_hour[$h] === 0 ? 'zero' : ($h === $peak_hour ? 'peak' : '');
                    $height = max($by_hour[$h] > 0 ? 4 : 2, $pct);
                    $tip    = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00 — ' . CURRENCY . ' ' . number_format($by_hour[$h], 2);
                ?>
                <div class="hour-bar-wrap" title="<?= $tip ?>">
                    <div class="hour-bar <?= $cls ?>" style="height:<?= $height ?>px;"></div>
                    <div class="hour-label"><?= $h % 6 === 0 ? str_pad($h, 2, '0', STR_PAD_LEFT) : '' ?></div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- RIGHT: Payment breakdown -->
    <div class="ds-panel" style="margin-bottom:0;">
        <div class="ds-panel-head">
            <span class="ds-panel-title">By Payment Method</span>
        </div>
        <div class="ds-panel-body" style="padding:10px 16px;">
            <?php foreach ($by_payment as $method => $data): ?>
            <?php $pct = $total_revenue > 0 ? round(($data['amount'] / $total_revenue) * 100) : 0; ?>
            <div class="pay-row">
                <div style="flex:1;">
                    <div style="display:flex;justify-content:space-between;align-items:baseline;gap:8px;">
                        <span class="pay-name"><?= e($method) ?></span>
                        <span class="pay-count"><?= $data['count'] ?> sale<?= $data['count'] != 1 ? 's' : '' ?></span>
                    </div>
                    <div class="pay-bar-track"><div class="pay-bar-fill" style="width:<?= $pct ?>%;"></div></div>
                    <div style="display:flex;justify-content:space-between;margin-top:2px;">
                        <span style="font-size:0.72rem;color:var(--muted);"><?= $pct ?>%</span>
                        <span class="pay-amount"><?= CURRENCY ?> <?= number_format($data['amount'], 2) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ── Top Products + Transactions ── -->
<div class="ds-grid">

    <!-- Transactions table -->
    <div class="ds-panel" style="margin-bottom:0;">
        <div class="ds-panel-head">
            <span class="ds-panel-title">All Transactions (<?= $total_transactions ?>)</span>
            <a href="<?= BASE_URL ?>/modules/purchases/history.php?date_from=<?= $date ?>&date_to=<?= $date ?>"
               style="font-size:0.75rem;color:var(--primary);font-weight:700;text-decoration:none;">
                View in History →
            </a>
        </div>
        <div class="table-responsive">
            <table class="ds-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Sale #</th>
                        <th>Customer</th>
                        <th style="text-align:center;">Items</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th style="text-align:right;">Discount</th>
                        <th style="text-align:right;">Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $s): ?>
                    <tr>
                        <td class="sale-time"><?= date('H:i', strtotime($s['sale_date'])) ?></td>
                        <td><span class="sale-id">#<?= str_pad($s['id'], 5, '0', STR_PAD_LEFT) ?></span></td>
                        <td>
                            <?php if ($s['customer_name']): ?>
                                <div class="cust-name"><?= e($s['customer_name']) ?></div>
                                <?php if ($s['customer_phone']): ?>
                                <div class="cust-phone"><?= e($s['customer_phone']) ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color:var(--muted);font-size:0.8rem;">Walk-in</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;"><?= (int)$s['item_count'] ?></td>
                        <td><span class="pay-badge"><?= e($s['payment_method']) ?></span></td>
                        <td>
                            <span class="status-pill status-<?= e($s['status']) ?>">
                                <?= ucfirst(e($s['status'])) ?>
                            </span>
                        </td>
                        <td class="disc-cell">
                            <?= $s['bill_discount'] > 0 ? '− ' . number_format($s['bill_discount'], 2) : '—' ?>
                        </td>
                        <td class="amount-cell"><?= CURRENCY ?> <?= number_format($s['net_amount'], 2) ?></td>
                        <td>
                            <div style="display:flex;gap:5px;">
                                <a class="act-btn" href="<?= BASE_URL ?>/modules/sales/receipt.php?sale_id=<?= (int)$s['id'] ?>">🧾</a>
                                <a class="act-btn print" href="<?= BASE_URL ?>/print/receipt_80mm.php?sale_id=<?= (int)$s['id'] ?>" target="_blank">🖨️</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" style="text-align:right;color:var(--muted);font-size:0.75rem;font-weight:400;">
                            Total (<?= $total_transactions ?> transaction<?= $total_transactions != 1 ? 's' : '' ?>)
                        </td>
                        <td class="amount-cell" style="color:var(--primary);"><?= CURRENCY ?> <?= number_format($total_revenue, 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Top products -->
    <div class="ds-panel" style="margin-bottom:0;">
        <div class="ds-panel-head">
            <span class="ds-panel-title">Top Products Today</span>
        </div>
        <div class="ds-panel-body" style="padding:10px 16px;">
            <?php if (empty($top_products)): ?>
            <p style="color:var(--muted);font-size:0.85rem;margin:0;">No product data.</p>
            <?php else: ?>
            <?php foreach ($top_products as $i => $p): ?>
            <?php
                $rank_cls = $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : ''));
            ?>
            <div class="prod-row">
                <div class="prod-rank <?= $rank_cls ?>"><?= $i + 1 ?></div>
                <div class="prod-info">
                    <div class="prod-name"><?= e($p['name']) ?></div>
                    <?php if ($p['sku']): ?>
                    <div class="prod-sku"><?= e($p['sku']) ?></div>
                    <?php endif; ?>
                </div>
                <div style="text-align:right;">
                    <div class="prod-rev"><?= CURRENCY ?> <?= number_format($p['total_revenue'], 2) ?></div>
                    <div class="prod-qty">× <?= number_format($p['total_qty'], 0) ?> sold</div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /ds-grid -->
<?php endif; ?>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
