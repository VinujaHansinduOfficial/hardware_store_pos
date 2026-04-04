<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Daily Summary');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Daily Summary</h3>
<div class="alert alert-info">Show daily sales totals, payments, discounts, and returns.</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
