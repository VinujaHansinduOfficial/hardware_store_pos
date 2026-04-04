<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

// ── Hold bill (JSON POST) ──────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'hold') {
    header('Content-Type: application/json');
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!$data) { echo json_encode(['success'=>false,'message'=>'Invalid data']); exit; }

    $stmt = $pdo->prepare("INSERT INTO held_bills (bill_data, created_at) VALUES (?, NOW())");
    $ok = $stmt->execute([json_encode($data)]);
    echo json_encode(['success' => $ok]);
    exit;
}

// ── Save sale (form POST) ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id    = (int)($_POST['customer_id'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? 'Cash';
    $bill_discount  = (float)($_POST['bill_discount'] ?? 0);
    $is_outstanding = (int)($_POST['is_outstanding'] ?? 0);
    $items          = json_decode($_POST['items_json'] ?? '[]', true);

    if (empty($items)) {
        header('Location: ../modules/sales/pos.php?error=no_items');
        exit;
    }

    // Calculate totals
    $total_amount = 0;
    foreach ($items as $item) {
        $total_amount += (float)($item['line_total'] ?? 0);
    }
    $net_amount = max(0, $total_amount - $bill_discount);

    $status = $is_outstanding ? 'outstanding' : 'completed';

    try {
        $pdo->beginTransaction();

        // Insert sale
        $stmt = $pdo->prepare(
            "INSERT INTO sales (customer_id, payment_method, bill_discount, total_amount, net_amount, sale_date, status)
             VALUES (?, ?, ?, ?, ?, NOW(), ?)"
        );
        $stmt->execute([
            $customer_id ?: null,
            $payment_method,
            $bill_discount,
            $total_amount,
            $net_amount,
            $status
        ]);
        $sale_id = (int)$pdo->lastInsertId();

        // Insert sale items + deduct stock
        $itemStmt = $pdo->prepare(
            "INSERT INTO sale_items (sale_id, product_id, qty, unit_price, item_discount, line_total)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stockStmt = $pdo->prepare(
            "UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?"
        );
        $invStmt = $pdo->prepare(
            "INSERT INTO inventory_transactions (product_id, transaction_type, qty, reference_type, reference_id, notes, created_at)
             VALUES (?, 'stock_out', ?, 'sale', ?, 'POS sale', NOW())"
        );

        foreach ($items as $item) {
            $pid  = (int)$item['product_id'];
            $qty  = (float)$item['qty'];
            $price = (float)$item['unit_price'];
            $disc = (float)($item['item_discount'] ?? 0);
            $line = (float)$item['line_total'];

            $itemStmt->execute([$sale_id, $pid, $qty, $price, $disc, $line]);
            $stockStmt->execute([$qty, $pid]);
            $invStmt->execute([$pid, $qty, $sale_id]);
        }

        // If outstanding, add to customer's outstanding balance
        if ($is_outstanding && $customer_id > 0) {
            $pdo->prepare(
                "UPDATE customers SET outstanding_balance = outstanding_balance + ? WHERE id = ?"
            )->execute([$net_amount, $customer_id]);
        }

        $pdo->commit();
        header('Location: ../modules/sales/receipt.php?sale_id=' . $sale_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        header('Location: ../modules/sales/pos.php?error=db&msg=' . urlencode($e->getMessage()));
        exit;
    }
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request']);
