<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

echo json_encode([
    'success' => true,
    'message' => 'supplier_payment_ajax.php endpoint ready',
    'table' => 'supplier_payments'
]);
?>
