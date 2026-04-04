<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

echo json_encode([
    'success' => true,
    'message' => 'purchase_ajax.php endpoint ready',
    'table' => 'purchases'
]);
?>
