<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

$sale_id = (int)($_GET['sale_id'] ?? 0);
if (!$sale_id) { die('No sale ID.'); }

// Fetch sale
$stmt = $pdo->prepare("
    SELECT s.*, c.name AS customer_name, c.phone AS customer_phone
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
    JOIN   products   p ON p.id = si.product_id
    WHERE  si.sale_id = ?
    ORDER  BY si.id
");
$items->execute([$sale_id]);
$items = $items->fetchAll();

$item_disc_total = array_sum(array_column($items, 'item_discount'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?= str_pad($sale_id, 5, '0', STR_PAD_LEFT) ?> — <?= APP_NAME ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;700&display=swap');

        /* ── 80mm thermal receipt ── */
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Iskoola Pota', 'Noto Sans Sinhala', 'Segoe UI Historic', sans-serif;
            font-size: 12px;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            padding: 20px 10px 60px;
        }

        .thermal {
            background: #fff;
            width: 80mm;
            min-height: 120mm;
            padding: 6mm 5mm;
            box-shadow: 0 4px 24px rgba(0,0,0,0.15);
            border-radius: 2px;
        }

        /* Store header */
        .th-header {
            text-align: center;
            margin-bottom: 6mm;
            padding-bottom: 4mm;
            border-bottom: 1px dashed #555;
        }

        .th-store-name {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .th-store-sub {
            font-size: 10px;
            color: #555;
        }

        /* Divider */
        .th-divider {
            border: none;
            border-top: 1px dashed #aaa;
            margin: 3mm 0;
        }

        .th-divider-solid {
            border: none;
            border-top: 1px solid #333;
            margin: 3mm 0;
        }

        /* Info rows */
        .th-info {
            font-size: 11px;
            margin-bottom: 3mm;
        }

        .th-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5px;
        }

        .th-row .th-key   { color: #555; white-space: nowrap; }
        .th-row .th-val   { font-weight: bold; text-align: right; }

        /* Section title */
        .th-section {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #777;
            margin: 3mm 0 1.5mm;
        }

        /* Items */
        .th-items { width: 100%; margin-bottom: 2mm; }

        .th-items thead th {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #333;
            padding: 2px 0;
            font-weight: bold;
        }

        .th-items thead th:last-child,
        .th-items tbody td:last-child { text-align: right; }

        .th-items thead th:nth-child(2),
        .th-items thead th:nth-child(3),
        .th-items tbody td:nth-child(2),
        .th-items tbody td:nth-child(3) { text-align: right; }

        .th-items tbody td {
            font-size: 11px;
            padding: 2.5px 0;
            vertical-align: top;
            border-bottom: 1px dotted #ddd;
        }

        .th-items tbody tr:last-child td { border-bottom: none; }

        .item-name { font-weight: bold; font-size: 11px; line-height: 1.3; }
        .item-sku  { font-size: 9px; color: #777; }

        /* Totals */
        .th-totals { margin: 2mm 0; }

        .th-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 1.5px 0;
        }

        .th-total-row .ttl { color: #555; }
        .th-total-row .ttv { font-weight: bold; }

        .th-grand {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
            padding: 3mm 0 1mm;
            border-top: 1px solid #333;
            margin-top: 2mm;
        }

        /* Outstanding banner */
        .th-outstanding {
            border: 2px solid #333;
            text-align: center;
            padding: 3mm;
            font-size: 11px;
            font-weight: bold;
            margin: 3mm 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Barcode-style receipt number */
        .th-receipt-num {
            text-align: center;
            font-size: 18px;
            letter-spacing: 4px;
            font-weight: bold;
            margin: 2mm 0 1mm;
        }

        .th-receipt-label {
            text-align: center;
            font-size: 9px;
            color: #777;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 3mm;
        }

        /* Footer */
        .th-footer {
            text-align: center;
            margin-top: 5mm;
            padding-top: 3mm;
            border-top: 1px dashed #aaa;
            font-size: 10px;
            color: #555;
            line-height: 1.8;
        }

        /* Print button (hidden on print) */
        .print-controls {
            width: 80mm;
            margin: 0 auto 12px;
            display: flex;
            gap: 8px;
        }

        .print-controls button, .print-controls a {
            flex: 1;
            padding: 9px;
            border: none;
            border-radius: 6px;
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
        }

        .btn-print { background: #1a1a1a; color: #fff; }
        .btn-print:hover { background: #333; }
        .btn-close { background: #f0ede8; color: #1a1a1a; border: 1.5px solid #d4cdc4; }
        .btn-close:hover { background: #e8e3dc; }

        /* ── PRINT MEDIA ── */
        @media print {
            body { background: #fff; padding: 0; }

            .thermal {
                box-shadow: none;
                width: 100%;
                padding: 3mm 2mm;
            }

            .print-controls { display: none !important; }
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }
    </style>
</head>
<body>

<!-- Print controls (hidden on actual print) -->
<div class="print-controls">
    <button class="btn-print" onclick="window.print()">🖨 Print Receipt</button>
    <a class="btn-close" href="javascript:window.close()">✕ Close</a>
</div>

<!-- 80mm Receipt -->
<div class="thermal" id="receipt">

    <!-- Store header -->
    <div class="th-header">
        <div class="th-store-name"><?= APP_NAME ?></div>
        <div class="th-store-sub">උපාංග වෙළඳසල් · අලෙවිය</div>
    </div>

    <!-- Receipt number -->
    <div class="th-receipt-num"><?= str_pad($sale_id, 5, '0', STR_PAD_LEFT) ?></div>
    <div class="th-receipt-label">පත්‍රිකා අංකය</div>

    <!-- Sale info -->
    <div class="th-info">
        <div class="th-row">
            <span class="th-key">දිනය</span>
            <span class="th-val"><?= date('d/m/Y', strtotime($sale['sale_date'])) ?></span>
        </div>
        <div class="th-row">
            <span class="th-key">වේලාව</span>
            <span class="th-val"><?= date('H:i A', strtotime($sale['sale_date'])) ?></span>
        </div>
        <div class="th-row">
            <span class="th-key">ගනුදෙනුකරු</span>
            <span class="th-val"><?= $sale['customer_name'] ? e($sale['customer_name']) : 'Walk-in' ?></span>
        </div>
        <?php if ($sale['customer_phone']): ?>
        <div class="th-row">
            <span class="th-key">දුරකථන</span>
            <span class="th-val"><?= e($sale['customer_phone']) ?></span>
        </div>
        <?php endif; ?>
        <div class="th-row">
            <span class="th-key">ගෙවීම</span>
            <span class="th-val"><?= e($sale['payment_method']) ?></span>
        </div>
    </div>

    <hr class="th-divider-solid">

    <!-- Outstanding notice -->
    <?php if ($sale['status'] === 'outstanding'): ?>
    <div class="th-outstanding">
        *** ඉතිරිව තිබේ — ගෙවීම් බලාපොරොත්තුයි ***
    </div>
    <?php endif; ?>

    <!-- Items -->
    <div class="th-section">අයිතම</div>
    <table class="th-items">
        <thead>
            <tr>
                <th style="width:45%;">අයිතමය</th>
                <th style="width:12%;text-align:right;">ප්‍රමාණය</th>
                <th style="width:20%;text-align:right;">මිල</th>
                <th style="width:23%;text-align:right;">එකතුව</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <div class="item-name"><?= e($item['product_name']) ?></div>
                    <?php if ($item['sku']): ?>
                    <div class="item-sku"><?= e($item['sku']) ?></div>
                    <?php endif; ?>
                    <?php if ($item['item_discount'] > 0): ?>
                    <div class="item-sku">Disc: -<?= number_format($item['item_discount'], 2) ?></div>
                    <?php endif; ?>
                </td>
                <td><?= number_format($item['qty'], 0) ?></td>
                <td><?= number_format($item['unit_price'], 2) ?></td>
                <td><strong><?= number_format($item['line_total'], 2) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <hr class="th-divider">

    <!-- Totals -->
    <div class="th-totals">
        <div class="th-total-row">
            <span class="ttl">උප මුදල</span>
            <span class="ttv"><?= CURRENCY ?> <?= number_format($sale['total_amount'], 2) ?></span>
        </div>
        <?php if ($item_disc_total > 0): ?>
        <div class="th-total-row">
            <span class="ttl">අයිතම වට්ටම්</span>
            <span class="ttv">- <?= number_format($item_disc_total, 2) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($sale['bill_discount'] > 0): ?>
        <div class="th-total-row">
            <span class="ttl">බිල් වට්ටම</span>
            <span class="ttv">- <?= number_format($sale['bill_discount'], 2) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="th-grand">
        <span>TOTAL</span>
        <span><?= CURRENCY ?> <?= number_format($sale['net_amount'], 2) ?></span>
    </div>

    <!-- Footer -->
    <div class="th-footer">
        *** ඔබේ මිලදී ගැනීම සඳහා ස්තුතියි! ***<br>
        <?= APP_NAME ?><br>
        <?= date('d M Y H:i', strtotime($sale['sale_date'])) ?>
    </div>

</div><!-- /thermal -->

<script>
// Auto-trigger print if ?autoprint=1
if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
    window.onload = () => window.print();
}
</script>

</body>
</html>
