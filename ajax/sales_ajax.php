<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = (int)($_POST['customer_id'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? 'Cash';
    $bill_discount = (float)($_POST['bill_discount'] ?? 0);

    $stmt = $pdo->prepare("INSERT INTO sales (customer_id, payment_method, bill_discount, total_amount, net_amount, sale_date, status) VALUES (?, ?, ?, 0, 0, NOW(), 'completed')");
    $stmt->execute([$customer_id, $payment_method, $bill_discount]);

    header('Location: ../modules/sales/receipt.php?sale_id=' . $pdo->lastInsertId());
    exit;
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
