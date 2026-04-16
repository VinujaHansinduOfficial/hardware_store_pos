</div><!-- /container -->

<footer class="footer">
    <div class="container text-center">
        <strong><?= APP_NAME ?></strong> &copy; <?= date('Y') ?>
        <br>
        <small>Hardware Store POS System</small>
        <br><br>
        <span class="developer-credit">Developed by <strong>NEXHUS CODE HUB</strong> © All Rights Reserved</span>
    </div>
</footer>

<!-- Footer Styles -->
<style>
/* ===== Footer Professional Style ===== */
html, body {
    min-height: 100%;
    margin: 0;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.footer {
    background-color: #121212; /* Dark professional background */
    color: #fff;
    border-top: 3px solid #ffc107; /* Accent top border */
    font-size: 0.9rem;
    width: 100%;
    text-align: center;
    flex-shrink: 0;
    margin-top: auto;
    padding: 20px 0;
}

.footer small {
    color: rgba(255, 255, 255, 0.7); /* faded description */
}

.developer-credit {
    color: #ffc107; /* Accent color */
    font-weight: 500;
    display: block;
    margin-top: 5px;
}

.footer a.footer-link {
    color: #fff;
    text-decoration: none;
    margin-left: 15px;
    transition: color 0.3s ease;
}

.footer a.footer-link:first-child {
    margin-left: 0;
}

.footer a.footer-link:hover {
    color: #ffc107;
    text-decoration: underline;
}
</style>

<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script src="<?= BASE_URL ?>/assets/js/ajax.js"></script>
<script src="<?= BASE_URL ?>/assets/js/pos.js"></script>
</body>
</html>
