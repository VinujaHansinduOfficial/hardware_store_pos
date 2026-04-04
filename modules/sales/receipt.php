<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Receipt');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Receipt</h3>
<div class="alert alert-info">Preview receipt before printing.</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
