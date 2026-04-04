<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Customers History');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Customers History</h3>
<div class="alert alert-info">Use this page to show full transaction history for each customer.</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
