<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
$pageTitle = page_title('Import Products');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>
<h3>Import Products (CSV)</h3>
<div class="card"><div class="card-body">
<p>Upload CSV to <code>/uploads/csv</code> and process it here.</p>
<form method="post" enctype="multipart/form-data">
    <input type="file" class="form-control mb-3" name="csv_file">
    <button class="btn btn-primary" type="submit">Upload</button>
</form>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
