<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

$sale_id = (int)($_GET['sale_id'] ?? 0);
if (!$sale_id) { header('Location: pos.php'); exit; }

// Fetch sale
$stmt = $pdo->prepare("
    SELECT s.*,
           c.name  AS customer_name,
           c.phone AS customer_phone,
           c.email AS customer_email
    FROM   sales s
    LEFT JOIN customers c ON c.id = s.customer_id
    WHERE  s.id = ?
");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch();
if (!$sale) { die('Sale not found.'); }

// Fetch items
$items = $pdo->prepare("
    SELECT si.*, p.name AS product_name, p.sku
    FROM   sale_items si
    JOIN   products   p  ON p.id = si.product_id
    WHERE  si.sale_id = ?
    ORDER  BY si.id
");
$items->execute([$sale_id]);
$items = $items->fetchAll();

$pageTitle = page_title('Receipt #' . $sale_id);
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<style>
/* ── Receipt page layout ── */
.receipt-page {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 20px;
    align-items: start;
}

/* ── Action panel ── */
.action-panel .card-body { display: flex; flex-direction: column; gap: 10px; }

.action-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: var(--radius);
    border: 1.5px solid var(--border);
    background: var(--surface);
    cursor: pointer;
    font-family: var(--font-body);
    font-weight: 700;
    font-size: 0.88rem;
    color: var(--dark);
    text-decoration: none;
    transition: all var(--trans);
    width: 100%;
    text-align: left;
}

.action-btn:hover { border-color: var(--primary); background: var(--primary-light); color: var(--dark); }
.action-btn.primary-action { background: var(--primary); border-color: var(--primary); color: #fff; box-shadow: 0 3px 12px rgba(232,128,10,0.35); }
.action-btn.primary-action:hover { background: var(--primary-dark); color: #fff; }
.action-btn .ab-icon { font-size: 1.3rem; flex-shrink: 0; }
.action-btn .ab-text .ab-title { display: block; }
.action-btn .ab-text .ab-sub { font-size: 0.74rem; color: inherit; opacity: 0.75; font-weight: 400; display: block; margin-top: 1px; }
.action-btn.primary-action .ab-text .ab-sub { opacity: 0.85; }

.sale-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.meta-item {
    background: var(--bg-2);
    border-radius: var(--radius-sm);
    padding: 10px 12px;
}

.meta-item .mi-label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 3px;
}

.meta-item .mi-value {
    font-family: var(--font-mono);
    font-size: 0.88rem;
    font-weight: 500;
    color: var(--dark);
}

.status-pill {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.73rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.status-completed  { background: #edfaf3; color: #1a7a4a; }
.status-outstanding{ background: #fdf0ef; color: #c0392b; }
.status-held       { background: #fdf5e6; color: #d4820a; }

/* ── Receipt preview ── */
.receipt-preview {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.receipt-header {
    background: var(--dark);
    color: #fff;
    padding: 20px 24px 16px;
    text-align: center;
    position: relative;
}

.receipt-header::after {
    content: '';
    display: block;
    position: absolute;
    bottom: -1px; left: 0; right: 0;
    height: 3px;
    background: var(--primary);
}

.receipt-store-name {
    font-family: var(--font-display);
    font-size: 1.4rem;
    font-weight: 800;
    letter-spacing: -0.01em;
    margin-bottom: 4px;
}

.receipt-store-sub {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.6);
    font-family: var(--font-mono);
}

.receipt-body { padding: 20px 24px; }

.receipt-info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px dashed var(--border);
    flex-wrap: wrap;
}

.receipt-info-block .rib-label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 3px;
}

.receipt-info-block .rib-value {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--dark);
}

.receipt-info-block .rib-sub {
    font-size: 0.76rem;
    color: var(--muted);
    font-family: var(--font-mono);
}

/* Items table */
.receipt-items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }

.receipt-items thead th {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--muted);
    padding: 8px 6px;
    border-bottom: 2px solid var(--border);
    text-align: left;
}

.receipt-items thead th:last-child,
.receipt-items tbody td:last-child { text-align: right; }

.receipt-items thead th:nth-child(2),
.receipt-items thead th:nth-child(3),
.receipt-items thead th:nth-child(4) { text-align: right; }

.receipt-items tbody td {
    padding: 9px 6px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    font-size: 0.88rem;
    vertical-align: middle;
}

.receipt-items tbody td:nth-child(2),
.receipt-items tbody td:nth-child(3),
.receipt-items tbody td:nth-child(4) { text-align: right; font-family: var(--font-mono); }

.receipt-items tbody tr:last-child td { border-bottom: none; }

.item-name-cell .in-name { font-weight: 700; font-size: 0.88rem; }
.item-name-cell .in-sku  { font-size: 0.72rem; color: var(--muted); font-family: var(--font-mono); }

/* Totals */
.receipt-totals {
    background: var(--bg-2);
    border-radius: var(--radius);
    padding: 14px 16px;
    margin-bottom: 16px;
}

.rt-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
    font-size: 0.86rem;
}

.rt-row .rt-label { color: var(--muted); }
.rt-row .rt-value { font-family: var(--font-mono); font-weight: 500; }
.rt-row .rt-value.disc { color: var(--danger); }

.rt-row.rt-grand {
    padding-top: 10px;
    margin-top: 6px;
    border-top: 2px solid var(--border);
}

.rt-row.rt-grand .rt-label { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--dark); }
.rt-row.rt-grand .rt-value { font-family: var(--font-display); font-size: 1.25rem; font-weight: 800; color: var(--primary); }

/* Outstanding notice */
.outstanding-notice {
    background: #fdf0ef;
    border: 1.5px solid var(--danger);
    border-radius: var(--radius-sm);
    padding: 10px 14px;
    font-size: 0.84rem;
    color: #7d2117;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
}

/* Footer */
.receipt-footer {
    text-align: center;
    padding: 14px 24px 20px;
    border-top: 1px dashed var(--border);
    color: var(--muted);
    font-size: 0.78rem;
    line-height: 1.8;
}

/* ── Responsive ── */
@media (max-width: 960px) {
    .receipt-page { grid-template-columns: 1fr; }
    .action-panel { order: -1; }
    .sale-meta-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 576px) {
    .sale-meta-grid { grid-template-columns: 1fr 1fr; }
    .receipt-body { padding: 16px; }
    .receipt-header { padding: 16px; }
    .receipt-store-name { font-size: 1.1rem; }
}
</style>

<!-- Page header -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
    <div>
        <h3 style="margin:0;">
            Receipt
            <span style="font-family:var(--font-mono);font-size:0.9rem;color:var(--muted);">#<?= str_pad($sale_id, 5, '0', STR_PAD_LEFT) ?></span>
        </h3>
        <p style="color:var(--muted);font-size:0.8rem;font-family:var(--font-mono);margin:3px 0 0;">
            <?= date('D, d M Y · H:i A', strtotime($sale['sale_date'])) ?>
        </p>
    </div>
    <a href="pos.php" class="btn btn-secondary btn-sm">← New Sale</a>
</div>

<div class="receipt-page">

    <!-- ── LEFT: Receipt preview ── -->
    <div class="receipt-preview" id="receiptPreview">

        <div class="receipt-header">
            <div class="receipt-store-name">⚙ <?= APP_NAME ?></div>
            <div class="receipt-store-sub">Hardware Store · Point of Sale</div>
        </div>

        <div class="receipt-body">

            <!-- Sale info row -->
            <div class="receipt-info-row">
                <div class="receipt-info-block">
                    <div class="rib-label">Sale #</div>
                    <div class="rib-value"><?= str_pad($sale_id, 5, '0', STR_PAD_LEFT) ?></div>
                    <div class="rib-sub"><?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?></div>
                </div>
                <div class="receipt-info-block">
                    <div class="rib-label">Customer</div>
                    <div class="rib-value"><?= $sale['customer_name'] ? e($sale['customer_name']) : 'Walk-in' ?></div>
                    <?php if ($sale['customer_phone']): ?>
                    <div class="rib-sub"><?= e($sale['customer_phone']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="receipt-info-block">
                    <div class="rib-label">Payment</div>
                    <div class="rib-value"><?= e($sale['payment_method']) ?></div>
                    <div class="rib-sub">
                        <span class="status-pill status-<?= e($sale['status']) ?>"><?= ucfirst(e($sale['status'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- Outstanding notice -->
            <?php if ($sale['status'] === 'outstanding'): ?>
            <div class="outstanding-notice">
                ⚠ This sale is marked as outstanding. Payment is pending from the customer.
            </div>
            <?php endif; ?>

            <!-- Items table -->
            <table class="receipt-items">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align:right;">Qty</th>
                        <th style="text-align:right;">Unit</th>
                        <th style="text-align:right;">Disc.</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="item-name-cell">
                                <div class="in-name"><?= e($item['product_name']) ?></div>
                                <?php if ($item['sku']): ?>
                                <div class="in-sku"><?= e($item['sku']) ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= number_format($item['qty'], 0) ?></td>
                        <td><?= number_format($item['unit_price'], 2) ?></td>
                        <td><?= $item['item_discount'] > 0 ? number_format($item['item_discount'], 2) : '—' ?></td>
                        <td><strong><?= number_format($item['line_total'], 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="receipt-totals">
                <?php
                $item_disc_total = array_sum(array_column($items, 'item_discount'));
                ?>
                <div class="rt-row">
                    <span class="rt-label">Subtotal (<?= count($items) ?> item<?= count($items) != 1 ? 's' : '' ?>)</span>
                    <span class="rt-value"><?= CURRENCY ?> <?= number_format($sale['total_amount'], 2) ?></span>
                </div>
                <?php if ($item_disc_total > 0): ?>
                <div class="rt-row">
                    <span class="rt-label">Item Discounts</span>
                    <span class="rt-value disc">− <?= CURRENCY ?> <?= number_format($item_disc_total, 2) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($sale['bill_discount'] > 0): ?>
                <div class="rt-row">
                    <span class="rt-label">Bill Discount</span>
                    <span class="rt-value disc">− <?= CURRENCY ?> <?= number_format($sale['bill_discount'], 2) ?></span>
                </div>
                <?php endif; ?>
                <div class="rt-row rt-grand">
                    <span class="rt-label">Grand Total</span>
                    <span class="rt-value"><?= CURRENCY ?> <?= number_format($sale['net_amount'], 2) ?></span>
                </div>
            </div>

        </div><!-- /receipt-body -->

        <div class="receipt-footer">
            Thank you for your purchase!<br>
            <strong><?= APP_NAME ?></strong><br>
            <?= date('d M Y, H:i A', strtotime($sale['sale_date'])) ?>
        </div>

    </div><!-- /receipt-preview -->

    <!-- ── RIGHT: Action panel ── -->
    <div class="action-panel">

        <!-- Sale metadata -->
        <div class="card" style="margin-bottom:14px;">
            <div class="card-body">
                <div style="font-family:var(--font-body);font-size:0.68rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Sale Summary</div>
                <div class="sale-meta-grid">
                    <div class="meta-item">
                        <div class="mi-label">Sale ID</div>
                        <div class="mi-value">#<?= str_pad($sale_id, 5, '0', STR_PAD_LEFT) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="mi-label">Status</div>
                        <div class="mi-value">
                            <span class="status-pill status-<?= e($sale['status']) ?>"><?= ucfirst(e($sale['status'])) ?></span>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="mi-label">Items</div>
                        <div class="mi-value"><?= count($items) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="mi-label">Payment</div>
                        <div class="mi-value" style="font-size:0.8rem;"><?= e($sale['payment_method']) ?></div>
                    </div>
                    <div class="meta-item" style="grid-column:1/3;">
                        <div class="mi-label">Grand Total</div>
                        <div class="mi-value" style="font-size:1.1rem;color:var(--primary);font-weight:700;">
                            <?= CURRENCY ?> <?= number_format($sale['net_amount'], 2) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-body">
                <div style="font-family:var(--font-body);font-size:0.68rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Actions</div>

                <!-- Print thermal 80mm -->
                <a href="<?= BASE_URL ?>/print/receipt_80mm.php?sale_id=<?= $sale_id ?>"
                   target="_blank" class="action-btn primary-action">
                    <span class="ab-icon">🖨️</span>
                    <span class="ab-text">
                        <span class="ab-title">Print Receipt (80mm)</span>
                        <span class="ab-sub">Thermal printer — opens in new tab</span>
                    </span>
                </a>

                <!-- Browser print -->
                <button onclick="browserPrint()" class="action-btn">
                    <span class="ab-icon">🖥️</span>
                    <span class="ab-text">
                        <span class="ab-title">Print / Save as PDF</span>
                        <span class="ab-sub">Uses browser print dialog</span>
                    </span>
                </button>

                <hr style="border:none;border-top:1px solid var(--border);margin:4px 0;">

                <!-- New sale -->
                <a href="pos.php" class="action-btn">
                    <span class="ab-icon">➕</span>
                    <span class="ab-text">
                        <span class="ab-title">New Sale</span>
                        <span class="ab-sub">Start a fresh order</span>
                    </span>
                </a>

                <!-- Back to sales list -->
                <a href="daily_summary.php" class="action-btn">
                    <span class="ab-icon">📋</span>
                    <span class="ab-text">
                        <span class="ab-title">Daily Summary</span>
                        <span class="ab-sub">View today's sales</span>
                    </span>
                </a>

            </div>
        </div>

    </div><!-- /action-panel -->
</div><!-- /receipt-page -->

<script>
function browserPrint() {
    window.print();
}
</script>

<!-- Browser print styles -->
<style>
@media print {
    .navbar, .footer, .action-panel,
    h3, p[style*="color:var(--muted)"],
    .btn { display: none !important; }

    body { background: #fff !important; }
    body::before { display: none !important; }

    .container.py-4 { padding: 0 !important; }
    .receipt-page { display: block !important; }

    .receipt-preview {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        max-width: 380px;
        margin: 0 auto;
    }
}
</style>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
