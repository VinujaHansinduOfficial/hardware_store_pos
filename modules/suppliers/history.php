<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Suppliers History');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Suppliers History</h3>
<div class="alert alert-info">Use this page to show full transaction history for each supplier.</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
