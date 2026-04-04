<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("UPDATE suppliers SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE id = ?");
$stmt->execute([$id]);
header('Location: index.php');
exit;
?>
