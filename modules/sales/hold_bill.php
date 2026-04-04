<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Hold Bill');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Hold Bill</h3>
<div class="alert alert-info">Hold current bill for later checkout.</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
