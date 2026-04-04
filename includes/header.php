<?php
if (!isset($pageTitle)) { $pageTitle = APP_NAME; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@600;700;800&family=Lato:wght@300;400;700&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
/* ================================================
   DESIGN TOKENS
================================================ */
:root {
    --primary:       #e8800a;
    --primary-dark:  #c46a07;
    --primary-light: #fdf0e0;
    --dark:          #1a1a1a;
    --surface:       #ffffff;
    --bg:            #f0ede8;
    --bg-2:          #e8e3dc;
    --border:        #d4cdc4;
    --muted:         #7a7060;
    --danger:        #c0392b;
    --success:       #1a7a4a;
    --warning:       #d4820a;
    --info:          #2563eb;

    --shadow-sm: 0 1px 4px rgba(0,0,0,0.08);
    --shadow:    0 4px 16px rgba(0,0,0,0.10);
    --shadow-lg: 0 12px 32px rgba(0,0,0,0.14);

    --radius-sm: 6px;
    --radius:    10px;
    --radius-lg: 16px;

    --font-display: 'Syne', sans-serif;
    --font-body:    'Lato', sans-serif;
    --font-mono:    'DM Mono', monospace;

    --trans: 0.22s cubic-bezier(0.4,0,0.2,1);
}

/* ================================================
   RESET
================================================ */
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
html { scroll-behavior:smooth; }

body {
    font-family: var(--font-body);
    background: var(--bg);
    color: var(--dark);
    font-size: 15px;
    line-height: 1.6;
    min-height: 100vh;
}

/* subtle grid texture */
body::before {
    content:'';
    position:fixed; inset:0;
    background-image:
        repeating-linear-gradient(0deg,   transparent, transparent 28px, rgba(0,0,0,0.016) 28px, rgba(0,0,0,0.016) 29px),
        repeating-linear-gradient(90deg,  transparent, transparent 28px, rgba(0,0,0,0.016) 28px, rgba(0,0,0,0.016) 29px);
    pointer-events:none; z-index:0;
}

.container, .container-fluid { position:relative; z-index:1; }

/* ================================================
   TYPOGRAPHY
================================================ */
h1,h2,h3,h4,h5,h6 {
    font-family: var(--font-display);
    font-weight: 700;
    line-height: 1.2;
    color: var(--dark);
}

code, .font-mono { font-family:var(--font-mono); font-size:0.88em; }

/* ================================================
   NAVBAR
================================================ */
.navbar {
    background: var(--dark) !important;
    padding: 0 20px;
    box-shadow: 0 2px 0 var(--primary), 0 4px 20px rgba(0,0,0,0.2);
    position: sticky;
    top: 0;
    z-index: 1000;
    min-height: 54px;
}

.navbar-brand {
    color: #fff !important;
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 1.1rem;
    letter-spacing: -0.02em;
    display: flex;
    align-items: center;
    gap: 7px;
    transition: opacity var(--trans);
    padding: 12px 0;
    flex-shrink: 0;
}

.navbar-brand::before {
    content: '⚙';
    color: var(--primary);
    font-size: 1.2rem;
}

.navbar-brand:hover { opacity: 0.85; }

.navbar-collapse {
    display: flex !important;
    align-items: center;
    flex: 1;
}

.navbar-toggler { display: none; }

.navbar-nav {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    overflow-x: auto;
    white-space: nowrap;
    align-items: center;
    padding: 6px 0;
    scrollbar-width: none;
    -ms-overflow-style: none;
    gap: 2px;
    width: 100%;
}

.navbar-nav::-webkit-scrollbar { display: none; }

.navbar-nav .nav-item { flex-shrink: 0; }

.navbar-nav .nav-link {
    color: rgba(255,255,255,0.75) !important;
    font-family: var(--font-body);
    font-weight: 700;
    font-size: 0.76rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 6px 11px;
    border-radius: var(--radius-sm);
    transition: background var(--trans), color var(--trans);
    white-space: nowrap;
}

.navbar-nav .nav-link:hover {
    background: rgba(255,255,255,0.12);
    color: #fff !important;
}

.navbar-nav .nav-link.active {
    background: var(--primary);
    color: #fff !important;
}

/* ================================================
   PAGE CONTAINER
================================================ */
.container.py-4 {
    padding-top: 24px !important;
    padding-bottom: 32px !important;
}

/* ================================================
   HEADER BAR
================================================ */
.header-bar {
    background: var(--surface);
    border-radius: var(--radius-lg);
    padding: 16px 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    position: relative;
    overflow: hidden;
}

.header-bar::before {
    content: '';
    position: absolute;
    left:0; top:0; bottom:0;
    width: 4px;
    background: var(--primary);
}

.header-title {
    font-family: var(--font-display);
    font-size: 1.2rem;
    font-weight: 700;
    letter-spacing: -0.01em;
}

.header-sub {
    font-size: 0.8rem;
    color: var(--muted);
    font-family: var(--font-mono);
    margin-top: 2px;
}

.header-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

/* ================================================
   CARDS
================================================ */
.card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border) !important;
    transition: box-shadow var(--trans);
}

.card:hover { box-shadow: var(--shadow); }

.card-body { padding: 1rem 1.2rem; }

/* Stat cards */
.card-stat { text-align:center; position:relative; overflow:hidden; }

.card-stat::after {
    content:'';
    position:absolute; bottom:0; left:50%;
    transform:translateX(-50%);
    width:36px; height:3px;
    background:var(--primary);
    border-radius:2px 2px 0 0;
}

.card-stat h6 {
    font-family:var(--font-body);
    font-size:0.7rem;
    font-weight:700;
    letter-spacing:0.08em;
    text-transform:uppercase;
    color:var(--muted);
    margin-bottom:6px;
}

.card-stat h2 {
    font-family:var(--font-display);
    font-size:2.2rem;
    font-weight:800;
    color:var(--dark);
    line-height:1;
    font-variant-numeric:tabular-nums;
}

/* ================================================
   TABLES
================================================ */
.table-responsive { overflow-x:auto; -webkit-overflow-scrolling:touch; border-radius:var(--radius); }

.table {
    margin:0;
    font-size:0.88rem;
    border-collapse:separate;
    border-spacing:0;
    width:100%;
}

.table thead tr { background:var(--bg-2); }

.table thead th {
    font-family:var(--font-body);
    font-size:0.7rem;
    font-weight:700;
    letter-spacing:0.07em;
    text-transform:uppercase;
    color:var(--muted);
    padding:10px 14px;
    border-bottom:2px solid var(--border);
    white-space:nowrap;
}

.table tbody tr { transition:background var(--trans); }
.table tbody tr:hover { background:var(--primary-light); }

.table tbody td {
    padding:9px 14px;
    border-bottom:1px solid rgba(0,0,0,0.05);
    vertical-align:middle;
}

.table tbody tr:last-child td { border-bottom:none; }

/* ================================================
   BUTTONS
================================================ */
.btn {
    font-family:var(--font-body);
    font-weight:700;
    font-size:0.82rem;
    letter-spacing:0.03em;
    border-radius:var(--radius-sm);
    padding:7px 16px;
    transition:all var(--trans);
    border:none;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:5px;
    text-decoration:none;
    white-space:nowrap;
}

.btn:active { transform:translateY(1px); }

.btn-primary { background:var(--primary); color:#fff !important; box-shadow:0 2px 8px rgba(232,128,10,0.3); }
.btn-primary:hover { background:var(--primary-dark); color:#fff !important; box-shadow:0 4px 14px rgba(232,128,10,0.38); }

.btn-secondary { background:var(--bg-2); color:var(--dark) !important; border:1px solid var(--border); }
.btn-secondary:hover { background:var(--border); color:var(--dark) !important; }

.btn-outline-secondary { background:transparent; color:var(--muted) !important; border:1.5px solid var(--border); }
.btn-outline-secondary:hover { background:var(--bg-2); color:var(--dark) !important; }

.btn-success { background:var(--success); color:#fff !important; box-shadow:0 2px 8px rgba(26,122,74,0.28); }
.btn-success:hover { background:#155e3a; color:#fff !important; }

.btn-danger { background:var(--danger); color:#fff !important; }
.btn-danger:hover { background:#a93226; color:#fff !important; }

.btn-warning { background:var(--warning); color:#fff !important; }
.btn-warning:hover { background:#b86e09; color:#fff !important; }

.btn-sm { font-size:0.73rem; padding:4px 9px; border-radius:5px; }
.btn-lg { font-size:0.95rem; padding:10px 24px; border-radius:var(--radius); }

/* ================================================
   FORMS
================================================ */
.form-control, .form-select {
    background:var(--surface);
    border:1.5px solid var(--border);
    border-radius:var(--radius-sm);
    padding:8px 12px;
    font-family:var(--font-body);
    font-size:0.9rem;
    color:var(--dark);
    transition:border-color var(--trans), box-shadow var(--trans);
    width:100%;
}

.form-control:focus, .form-select:focus {
    border-color:var(--primary);
    box-shadow:0 0 0 3px rgba(232,128,10,0.15);
    outline:none;
    background:var(--surface);
}

.form-label {
    font-weight:700;
    font-size:0.76rem;
    letter-spacing:0.05em;
    text-transform:uppercase;
    color:var(--muted);
    margin-bottom:5px;
    display:block;
}

.mb-3 { margin-bottom: 1rem; }

.input-group { display:flex; gap:0; }
.input-group .form-control { border-radius:var(--radius-sm) 0 0 var(--radius-sm); }
.input-group .btn { border-radius:0 var(--radius-sm) var(--radius-sm) 0; }

/* ================================================
   BADGES
================================================ */
.badge {
    font-family:var(--font-mono);
    font-size:0.7rem;
    font-weight:500;
    padding:3px 8px;
    border-radius:4px;
    letter-spacing:0.03em;
    display:inline-block;
}

.bg-success { background:#1a7a4a !important; color:#fff; }
.bg-secondary { background:#6c757d !important; color:#fff; }
.bg-danger { background:var(--danger) !important; color:#fff; }
.bg-warning { background:var(--warning) !important; color:#fff; }

.balance-high   { color:var(--danger);  font-family:var(--font-mono); font-weight:700; }
.balance-medium { color:var(--warning); font-family:var(--font-mono); font-weight:700; }
.balance-low    { color:var(--success); font-family:var(--font-mono); font-weight:700; }

/* ================================================
   MODULE ACTIONS
================================================ */
.module-actions { display:flex; gap:5px; flex-wrap:wrap; align-items:center; }

/* ================================================
   QUICK LINKS (DASHBOARD)
================================================ */
.quick-links {
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:14px;
    margin-top:16px;
}

.quick-card {
    background:var(--surface);
    border-radius:var(--radius-lg);
    padding:20px 14px;
    text-align:center;
    border:1.5px solid var(--border);
    transition:all var(--trans);
    cursor:pointer;
    position:relative;
    overflow:hidden;
    text-decoration:none;
    color:inherit;
    display:block;
}

.quick-card::before {
    content:'';
    position:absolute; inset:0;
    background:var(--primary-light);
    opacity:0;
    transition:opacity var(--trans);
}

.quick-card:hover {
    border-color:var(--primary);
    transform:translateY(-3px);
    box-shadow:var(--shadow);
    color:inherit;
    text-decoration:none;
}

.quick-card:hover::before { opacity:1; }

.quick-card a {
    text-decoration:none;
    color:inherit;
    display:block;
    position:relative;
    z-index:1;
}

.quick-card i {
    font-size:26px;
    display:block;
    margin-bottom:8px;
    transition:transform var(--trans);
    position:relative;
    z-index:1;
}

.quick-card:hover i { transform:scale(1.12); }

.quick-card h6 {
    font-family:var(--font-display);
    font-size:0.92rem;
    font-weight:700;
    margin:0 0 4px;
    position:relative; z-index:1;
}

.quick-card p {
    font-size:0.78rem;
    color:var(--muted);
    margin:0;
    position:relative; z-index:1;
}

/* ================================================
   POS GRID
================================================ */
.pos-grid {
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:1rem;
    align-items:start;
}

/* ================================================
   SUPPLIERS / CUSTOMERS MODULE
================================================ */
.suppliers-header, .customers-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:16px;
    flex-wrap:wrap;
    gap:10px;
}

.suppliers-card, .customers-card {
    background:var(--surface);
    border-radius:var(--radius-lg);
    padding:16px;
    border:1px solid var(--border);
    box-shadow:var(--shadow-sm);
}

.suppliers-table th, .customers-table th {
    background:var(--bg-2);
    font-weight:700;
    font-size:0.7rem;
    letter-spacing:0.07em;
    text-transform:uppercase;
    color:var(--muted);
}

.suppliers-table td, .customers-table td { font-size:0.87rem; }
.suppliers-table tbody tr:hover, .customers-table tbody tr:hover { background:var(--primary-light); }

.suppliers-search, .customers-search {
    display:flex;
    gap:10px;
    margin-bottom:14px;
    flex-wrap:wrap;
}

.suppliers-search input, .customers-search input { flex:1; min-width:160px; }

.suppliers-actions, .customers-actions { display:flex; gap:5px; flex-wrap:wrap; }
.suppliers-actions .btn, .customers-actions .btn { font-size:0.72rem; padding:3px 8px; }

/* ================================================
   ALERTS
================================================ */
.alert {
    border-radius:var(--radius);
    border:none;
    font-size:0.87rem;
    padding:11px 16px;
    border-left:4px solid;
    margin-bottom:16px;
}

.alert-success { background:#edfaf3; border-color:var(--success); color:#124d2e; }
.alert-danger  { background:#fdf0ef; border-color:var(--danger);  color:#7d2117; }
.alert-warning { background:#fdf5e6; border-color:var(--warning); color:#7a4a06; }
.alert-info    { background:#edf2fb; border-color:var(--info);    color:#1e3a8a; }

/* ================================================
   PAGINATION
================================================ */
.pagination { gap:3px; display:flex; flex-wrap:wrap; }

.page-link {
    font-family:var(--font-mono);
    font-size:0.8rem;
    border-radius:var(--radius-sm) !important;
    border:1px solid var(--border);
    color:var(--dark);
    padding:6px 11px;
    transition:all var(--trans);
    display:inline-block;
}

.page-link:hover { background:var(--primary-light); border-color:var(--primary); color:var(--primary-dark); }
.page-item.active .page-link { background:var(--primary); border-color:var(--primary); color:#fff; }

/* ================================================
   FOOTER
================================================ */
.footer {
    background:var(--dark);
    color:rgba(255,255,255,0.75);
    padding:20px 0;
    margin-top:40px;
    font-size:0.84rem;
    border-top:3px solid var(--primary);
    position:relative;
    z-index:1;
}

.footer .footer-content {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
}

.footer .footer-left {
    font-family:var(--font-display);
    font-weight:600;
    color:#fff;
    font-size:0.92rem;
}

.footer .footer-left small {
    display:block;
    font-family:var(--font-mono);
    font-weight:400;
    font-size:0.73rem;
    color:rgba(255,255,255,0.42);
    margin-top:2px;
}

.footer .footer-right { display:flex; gap:16px; flex-wrap:wrap; }

.footer a {
    color:rgba(255,255,255,0.58);
    text-decoration:none;
    font-weight:700;
    font-size:0.73rem;
    letter-spacing:0.06em;
    text-transform:uppercase;
    transition:color var(--trans);
}

.footer a:hover { color:var(--primary); }

/* ================================================
   SKELETON / LOADING
================================================ */
.skeleton {
    background:linear-gradient(90deg, var(--bg-2) 25%, var(--border) 50%, var(--bg-2) 75%);
    background-size:200% 100%;
    animation:shimmer 1.5s infinite;
    border-radius:var(--radius-sm);
}

@keyframes shimmer {
    0%   { background-position:200% 0; }
    100% { background-position:-200% 0; }
}

/* ================================================
   PRINT
================================================ */
@media print {
    .navbar, .footer, .btn, .header-actions { display:none !important; }
    body { background:#fff; font-size:12px; }
    .card { box-shadow:none !important; border:1px solid #ccc !important; }
    body::before { display:none; }
}

/* ================================================
   RESPONSIVE — LARGE (≤1200px)
================================================ */
@media (max-width:1200px) {
    .quick-links { grid-template-columns:repeat(4,1fr); gap:10px; }
}

/* ================================================
   RESPONSIVE — TABLET (≤992px)
================================================ */
@media (max-width:992px) {
    body { font-size:14.5px; }

    .navbar { padding:0 14px; }
    .navbar-nav { gap:1px; }
    .navbar-nav .nav-link { font-size:0.72rem; padding:5px 9px; }

    .pos-grid { grid-template-columns:1fr; }

    .quick-links { grid-template-columns:repeat(2,1fr); gap:12px; }

    .header-bar { flex-direction:column; align-items:flex-start; }

    .suppliers-header, .customers-header { flex-direction:column; align-items:flex-start; }

    .card-stat h2 { font-size:2rem; }
}

/* ================================================
   RESPONSIVE — MOBILE LARGE (≤768px)
================================================ */
@media (max-width:768px) {
    body { font-size:14px; }

    .navbar { padding:0 12px; }
    .navbar-brand { font-size:1rem; }
    .navbar-brand::before { font-size:1.05rem; }

    .container.py-4 {
        padding-top:16px !important;
        padding-bottom:20px !important;
        padding-left:12px !important;
        padding-right:12px !important;
    }

    /* Stack tables into cards on mobile */
    .table thead { display:none; }

    .table,
    .table tbody,
    .table tr,
    .table td { display:block; width:100%; }

    .table tbody tr {
        background:var(--surface);
        margin-bottom:10px;
        padding:12px 14px;
        border-radius:var(--radius);
        border:1px solid var(--border);
        box-shadow:var(--shadow-sm);
        position:relative;
    }

    .table tbody tr:hover { background:var(--surface); }

    .table tbody tr:last-child td { border-bottom:none; }

    .table td {
        padding:4px 0;
        font-size:0.87rem;
        border-bottom:none;
    }

    .table td::before {
        content:attr(data-label);
        font-weight:700;
        font-size:0.68rem;
        letter-spacing:0.05em;
        text-transform:uppercase;
        color:var(--muted);
        display:block;
        margin-bottom:1px;
    }

    /* Buttons go full-width on mobile */
    .btn { width:100%; }
    .btn-sm { width:auto; }

    .module-actions { flex-wrap:wrap; }
    .module-actions .btn { width:auto; flex:1 1 auto; }

    .header-bar { padding:14px 16px; gap:10px; }
    .header-title { font-size:1.05rem; }
    .header-actions { width:100%; }
    .header-actions .btn { flex:1; }

    .quick-links { grid-template-columns:1fr 1fr; gap:10px; }

    .quick-card { padding:16px 10px; }
    .quick-card i { font-size:22px; margin-bottom:6px; }
    .quick-card h6 { font-size:0.85rem; }
    .quick-card p { font-size:0.74rem; }

    .card-stat h2 { font-size:1.8rem; }

    .suppliers-search, .customers-search { flex-direction:column; }
    .suppliers-search input, .customers-search input { width:100%; }

    .suppliers-actions, .customers-actions { gap:4px; }
    .suppliers-actions .btn, .customers-actions .btn { flex:1 1 auto; }

    .footer { padding:16px 0; margin-top:28px; }
    .footer .footer-content { flex-direction:column; text-align:center; }
    .footer .footer-right { justify-content:center; }

    .pagination { justify-content:center; }
}

/* ================================================
   RESPONSIVE — MOBILE SMALL (≤576px)
================================================ */
@media (max-width:576px) {
    body { font-size:13.5px; }

    .navbar-brand { font-size:0.95rem; }
    .navbar-nav .nav-link { font-size:0.68rem; padding:5px 8px; }

    .quick-links { grid-template-columns:1fr 1fr; gap:8px; }

    .card-body { padding:0.85rem 1rem; }

    .pos-grid { gap:0.7rem; }

    .form-control, .form-select { font-size:0.87rem; padding:7px 10px; }

    h3 { font-size:1.15rem; }
    h4 { font-size:1rem; }
    h5 { font-size:0.92rem; }
}

/* ================================================
   RESPONSIVE — TINY (≤400px)
================================================ */
@media (max-width:400px) {
    .navbar { padding:0 10px; }
    .navbar-brand::before { display:none; }
    .navbar-brand { font-size:0.88rem; }
    .navbar-nav .nav-link { font-size:0.65rem; padding:4px 7px; }

    .quick-links { grid-template-columns:1fr; }
    .quick-card { padding:14px; }

    .card-stat h2 { font-size:1.6rem; }

    .btn { font-size:0.78rem; padding:8px 12px; }
    .btn-sm { font-size:0.7rem; padding:4px 8px; }

    h3 { font-size:1.05rem; }
}
    </style>
</head>
<body>
