<!-- Navbar -->
<nav class="topnav">
    <div class="topnav__inner">

        <!-- Brand -->
        <a class="topnav__brand" href="<?= BASE_URL ?>/index.php"><?= APP_NAME ?></a>

        <!-- Nav Links (always horizontal, scrollable on small screens) -->
        <div class="topnav__links">
            <a class="topnav__link" href="<?= BASE_URL ?>/modules/dashboard/index.php">
                <svg viewBox="0 0 20 20" fill="currentColor"><path d="M2 11l8-8 8 8v7a1 1 0 01-1 1H3a1 1 0 01-1-1v-7z"/></svg>
                <span>Dashboard</span>
            </a>
            <a class="topnav__link" href="<?= BASE_URL ?>/modules/categories/index.php">
                <svg viewBox="0 0 20 20" fill="currentColor"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 8a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zm6-6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zm0 8a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Categories</span>
            </a>
            <a class="topnav__link" href="<?= BASE_URL ?>/modules/subcategories/index.php">
                <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 4a1 1 0 000 2h10a1 1 0 100-2H3zm0 4a1 1 0 000 2h6a1 1 0 100-2H3zm0 4a1 1 0 100 2h8a1 1 0 100-2H3z" clip-rule="evenodd"/></svg>
                <span>Subcategories</span>
            </a>
            <a class="topnav__link" href="<?= BASE_URL ?>/modules/products/index.php">
                <svg viewBox="0 0 20 20" fill="currentColor"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                <span>Products</span>
            </a>
            <a class="topnav__link" href="<?= BASE_URL ?>/modules/suppliers/index.php">
                <svg viewBox="0 0 20 20" fill="currentColor"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H11a1 1 0 001-1V5a1 1 0 00-1-1H3z"/><path d="M14 7h2l2 5v2h-1.05a2.5 2.5 0 00-4.9 0H11V7h3z"/></svg>
                <span>Suppliers</span>
            </a>
            <a class="topnav__link" href="<?= BASE_URL ?>/modules/customers/index.php">
                <svg viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 14.094A5.973 5.973 0 004 17v1H1v-1a3 3 0 013.75-2.906z"/></svg>
                <span>Customers</span>
            </a>
            <a class="topnav__link topnav__link--accent" href="<?= BASE_URL ?>/modules/sales/pos.php">
                <svg viewBox="0 0 20 20" fill="currentColor"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg>
                <span>POS</span>
            </a>
            <a class="topnav__link" href="<?= BASE_URL ?>/modules/purchases/history.php">
                <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/></svg>
                <span>Purchases</span>
            </a>
            <a class="topnav__link" href="<?= BASE_URL ?>/modules/reports/daily_sales.php">
                <svg viewBox="0 0 20 20" fill="currentColor"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-4a1 1 0 011-1h2a1 1 0 011 1v13a1 1 0 01-1 1h-2a1 1 0 01-1-1V3z"/></svg>
                <span>Reports</span>
            </a>
        </div>

    </div>
</nav>

<!-- Spacer so content doesn't hide under fixed nav -->
<div style="height: 64px;"></div>

<style>
/* ── Reset & tokens ───────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --nav-bg:        #0f1117;
    --nav-border:    rgba(255,255,255,.07);
    --nav-height:    64px;
    --brand-color:   #ffc107;
    --link-color:    rgba(255,255,255,.72);
    --link-hover-bg: rgba(255,193,7,.12);
    --link-hover-fg: #ffc107;
    --accent-bg:     #ffc107;
    --accent-fg:     #0f1117;
    --accent-hover:  #ffca28;
    --radius:        8px;
    --font:          'Segoe UI', system-ui, sans-serif;
    --shadow:        0 2px 16px rgba(0,0,0,.45);
}

/* ── Navbar shell ─────────────────────────── */
.topnav {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 1000;
    height: var(--nav-height);
    background: var(--nav-bg);
    border-bottom: 1px solid var(--nav-border);
    box-shadow: var(--shadow);
    font-family: var(--font);
}

.topnav__inner {
    display: flex;
    align-items: center;
    height: 100%;
    padding: 0 20px;
    gap: 24px;
    max-width: 1600px;
    margin: 0 auto;
}

/* ── Brand ────────────────────────────────── */
.topnav__brand {
    flex-shrink: 0;
    font-size: 1.15rem;
    font-weight: 700;
    letter-spacing: .5px;
    color: var(--brand-color);
    text-decoration: none;
    white-space: nowrap;
    transition: opacity .2s;
}
.topnav__brand:hover { opacity: .8; }

/* ── Links strip ──────────────────────────── */
.topnav__links {
    display: flex;           /* always horizontal */
    flex-direction: row;
    align-items: center;
    gap: 2px;
    flex: 1;
    overflow-x: auto;        /* scroll on tiny screens */
    overflow-y: hidden;
    /* hide scrollbar visually */
    scrollbar-width: none;
    -ms-overflow-style: none;
    list-style: none;        /* kills any inherited bullets */
}
.topnav__links::-webkit-scrollbar { display: none; }

/* ── Individual link ──────────────────────── */
.topnav__link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
    padding: 7px 13px;
    border-radius: var(--radius);
    font-size: .82rem;
    font-weight: 500;
    letter-spacing: .3px;
    color: var(--link-color);
    text-decoration: none;
    white-space: nowrap;
    transition: background .22s ease, color .22s ease, transform .15s ease;
    list-style: none;        /* extra safety */
}
.topnav__link svg {
    width: 15px;
    height: 15px;
    flex-shrink: 0;
    opacity: .7;
    transition: opacity .2s;
}

/* Hover */
.topnav__link:hover {
    color: var(--link-hover-fg);
    background: var(--link-hover-bg);
    transform: translateY(-1px);
    text-decoration: none;
}
.topnav__link:hover svg { opacity: 1; }

/* Active page highlight */
.topnav__link.active {
    color: var(--link-hover-fg);
    background: var(--link-hover-bg);
}

/* POS accent button */
.topnav__link--accent {
    background: var(--accent-bg);
    color: var(--accent-fg);
    font-weight: 700;
    padding: 7px 16px;
}
.topnav__link--accent svg { opacity: 1; }
.topnav__link--accent:hover {
    background: var(--accent-hover);
    color: var(--accent-fg);
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(255,193,7,.35);
}

/* ── Very small screens: hide labels, show icons only ── */
@media (max-width: 480px) {
    .topnav__link span { display: none; }
    .topnav__link { padding: 8px 10px; }
    .topnav__link svg { width: 18px; height: 18px; opacity: .85; }
    .topnav__brand { font-size: .95rem; }
}
</style>

<script>
/* Mark current page link as active */
(function () {
    const path = window.location.pathname;
    document.querySelectorAll('.topnav__link').forEach(function (a) {
        if (a.getAttribute('href') && path.includes(a.getAttribute('href').split('/').pop())) {
            a.classList.add('active');
        }
    });
})();
</script>
