<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

$products  = $pdo->query("SELECT id, name, sku, selling_price, stock_qty FROM products WHERE is_active = 1 ORDER BY name")->fetchAll();
$customers = $pdo->query("SELECT id, name, phone, outstanding_balance FROM customers WHERE is_active = 1 ORDER BY name")->fetchAll();

$pageTitle = page_title('POS — New Sale');
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<!-- ===================== POS STYLES ===================== -->
<style>
/* POS layout */
.pos-wrap {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 16px;
    align-items: start;
}

/* Product search */
.product-search-wrap { position: relative; margin-bottom: 12px; }
#productSearch {
    padding-left: 38px;
    font-size: 0.9rem;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    width: 100%;
    height: 40px;
}
#productSearch:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(232,128,10,0.15); outline: none; }
.search-icon {
    position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
    font-size: 1rem; color: var(--muted); pointer-events: none;
}

/* Product grid */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    max-height: calc(100vh - 260px);
    overflow-y: auto;
    padding-right: 4px;
}

.product-grid::-webkit-scrollbar { width: 5px; }
.product-grid::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

.prod-tile {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    padding: 12px 10px;
    cursor: pointer;
    transition: all var(--trans);
    text-align: center;
    user-select: none;
    position: relative;
}

.prod-tile:hover { border-color: var(--primary); box-shadow: var(--shadow); transform: translateY(-2px); }
.prod-tile.out-of-stock { opacity: 0.45; cursor: not-allowed; }
.prod-tile.out-of-stock:hover { transform: none; box-shadow: none; border-color: var(--border); }

.prod-tile .prod-name {
    font-family: var(--font-display);
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 4px;
    line-height: 1.2;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.prod-tile .prod-sku {
    font-family: var(--font-mono);
    font-size: 0.68rem;
    color: var(--muted);
    margin-bottom: 6px;
}

.prod-tile .prod-price {
    font-family: var(--font-mono);
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--primary);
}

.prod-tile .prod-stock {
    font-size: 0.68rem;
    color: var(--muted);
    margin-top: 3px;
}

.prod-tile .prod-stock.low { color: var(--danger); font-weight: 700; }

/* Order items table */
.order-items-wrap { min-height: 120px; }

.order-table { width: 100%; font-size: 0.84rem; border-collapse: collapse; }
.order-table th {
    font-family: var(--font-body);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--muted);
    padding: 7px 8px;
    border-bottom: 2px solid var(--border);
    text-align: left;
    white-space: nowrap;
}

.order-table td { padding: 7px 8px; border-bottom: 1px solid rgba(0,0,0,0.05); vertical-align: middle; }
.order-table tbody tr:last-child td { border-bottom: none; }

.qty-control {
    display: flex;
    align-items: center;
    gap: 4px;
}

.qty-btn {
    width: 26px; height: 26px;
    border: 1.5px solid var(--border);
    border-radius: 5px;
    background: var(--bg-2);
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    transition: all var(--trans);
    flex-shrink: 0;
    color: var(--dark);
}
.qty-btn:hover { background: var(--primary); border-color: var(--primary); color: #fff; }

.qty-input {
    width: 46px;
    height: 26px;
    text-align: center;
    border: 1.5px solid var(--border);
    border-radius: 5px;
    font-family: var(--font-mono);
    font-size: 0.82rem;
    padding: 0 4px;
    color: var(--dark);
    background: var(--surface);
}
.qty-input:focus { border-color: var(--primary); outline: none; }

.remove-btn {
    background: none;
    border: none;
    color: var(--danger);
    cursor: pointer;
    font-size: 1rem;
    padding: 2px 4px;
    border-radius: 4px;
    transition: background var(--trans);
    line-height: 1;
}
.remove-btn:hover { background: #fdf0ef; }

.item-discount-input {
    width: 60px;
    height: 26px;
    text-align: right;
    border: 1.5px solid var(--border);
    border-radius: 5px;
    font-family: var(--font-mono);
    font-size: 0.82rem;
    padding: 0 6px;
    color: var(--dark);
    background: var(--surface);
}
.item-discount-input:focus { border-color: var(--primary); outline: none; }

/* Billing panel */
.billing-panel { position: sticky; top: 80px; }

.bill-section-title {
    font-family: var(--font-body);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid var(--border);
}

/* Customer selector */
.customer-select-wrap { position: relative; }

.customer-search-input {
    width: 100%;
    padding: 8px 12px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.88rem;
    font-family: var(--font-body);
    color: var(--dark);
    background: var(--surface);
    transition: border-color var(--trans);
}
.customer-search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(232,128,10,0.12); outline: none; }

.customer-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0; right: 0;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    z-index: 200;
    max-height: 200px;
    overflow-y: auto;
    display: none;
}

.customer-dropdown.open { display: block; }

.customer-option {
    padding: 9px 12px;
    cursor: pointer;
    transition: background var(--trans);
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.customer-option:last-child { border-bottom: none; }
.customer-option:hover { background: var(--primary-light); }

.customer-option .c-name { font-weight: 700; font-size: 0.86rem; color: var(--dark); }
.customer-option .c-meta { font-size: 0.74rem; color: var(--muted); font-family: var(--font-mono); }
.customer-option .c-outstanding { color: var(--danger); font-weight: 700; }

.selected-customer {
    background: var(--primary-light);
    border: 1.5px solid var(--primary);
    border-radius: var(--radius-sm);
    padding: 8px 12px;
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: 6px;
}

.selected-customer.show { display: flex; }
.selected-customer .sc-name { font-weight: 700; font-size: 0.86rem; }
.selected-customer .sc-outstanding { font-family: var(--font-mono); font-size: 0.78rem; color: var(--danger); }
.selected-customer .sc-clear { background: none; border: none; cursor: pointer; color: var(--muted); font-size: 1rem; padding: 0; line-height: 1; }
.selected-customer .sc-clear:hover { color: var(--danger); }

/* Outstanding toggle */
.outstanding-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: var(--bg-2);
    border-radius: var(--radius-sm);
    border: 1.5px solid var(--border);
    cursor: pointer;
    transition: all var(--trans);
    margin-top: 8px;
}
.outstanding-toggle:hover { border-color: var(--primary); }
.outstanding-toggle input[type=checkbox] { width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer; }
.outstanding-toggle label { font-size: 0.85rem; font-weight: 700; color: var(--dark); cursor: pointer; margin: 0; }
.outstanding-toggle .ot-sub { font-size: 0.74rem; color: var(--muted); font-family: var(--font-mono); }

.outstanding-toggle.active { background: #fdf0ef; border-color: var(--danger); }
.outstanding-toggle.active label { color: var(--danger); }

/* Totals */
.totals-box { background: var(--bg-2); border-radius: var(--radius); padding: 14px; margin-bottom: 14px; }

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
    font-size: 0.85rem;
}

.total-row .tl { color: var(--muted); }
.total-row .tv { font-family: var(--font-mono); font-weight: 500; color: var(--dark); }
.total-row .tv.discount { color: var(--danger); }
.total-row.grand { padding-top: 10px; margin-top: 6px; border-top: 2px solid var(--border); }
.total-row.grand .tl { font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; color: var(--dark); }
.total-row.grand .tv { font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; color: var(--primary); }

/* Empty state */
.empty-order {
    text-align: center;
    padding: 30px 16px;
    color: var(--muted);
}
.empty-order .eo-icon { font-size: 2.5rem; margin-bottom: 8px; opacity: 0.5; }
.empty-order p { font-size: 0.85rem; }

/* Save btn */
#saveSaleBtn {
    width: 100%;
    padding: 13px;
    font-size: 1rem;
    font-family: var(--font-display);
    font-weight: 700;
    letter-spacing: 0.02em;
    border-radius: var(--radius);
    background: var(--primary);
    color: #fff;
    border: none;
    cursor: pointer;
    transition: all var(--trans);
    box-shadow: 0 3px 12px rgba(232,128,10,0.35);
}
#saveSaleBtn:hover:not(:disabled) { background: var(--primary-dark); box-shadow: 0 5px 18px rgba(232,128,10,0.4); }
#saveSaleBtn:disabled { opacity: 0.5; cursor: not-allowed; box-shadow: none; }

/* Hold bill btn */
#holdBillBtn {
    width: 100%;
    padding: 9px;
    font-size: 0.85rem;
    font-family: var(--font-body);
    font-weight: 700;
    border-radius: var(--radius-sm);
    background: transparent;
    color: var(--muted);
    border: 1.5px solid var(--border);
    cursor: pointer;
    transition: all var(--trans);
    margin-top: 8px;
}
#holdBillBtn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

/* Bill discount input */
.bill-discount-wrap { position: relative; }
.bill-discount-wrap .currency-symbol {
    position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
    font-family: var(--font-mono); font-size: 0.82rem; color: var(--muted);
    pointer-events: none;
}
.bill-discount-wrap input { padding-left: 28px; }

/* Payment method pills */
.pay-methods { display: flex; gap: 6px; flex-wrap: wrap; }
.pay-pill {
    flex: 1;
    padding: 7px 8px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--surface);
    cursor: pointer;
    font-family: var(--font-body);
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--muted);
    text-align: center;
    transition: all var(--trans);
    white-space: nowrap;
}
.pay-pill:hover { border-color: var(--primary); color: var(--primary); }
.pay-pill.selected { background: var(--primary); border-color: var(--primary); color: #fff; }

/* Responsive */
@media (max-width: 1100px) {
    .pos-wrap { grid-template-columns: 1fr 320px; }
    .product-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); }
}

@media (max-width: 900px) {
    .pos-wrap { grid-template-columns: 1fr; }
    .billing-panel { position: static; }
    .product-grid { max-height: 320px; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
}

@media (max-width: 576px) {
    .product-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; max-height: 260px; }
    .order-table th, .order-table td { padding: 6px 5px; font-size: 0.8rem; }
    .qty-input { width: 38px; }
    .item-discount-input { width: 50px; }
}
</style>

<!-- ===================== PAGE HEADER ===================== -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
    <div>
        <h3 style="font-family:var(--font-display);font-size:1.4rem;font-weight:800;margin:0;">🛒 New Sale</h3>
        <p style="color:var(--muted);font-size:0.82rem;font-family:var(--font-mono);margin:3px 0 0;">Click a product to add it to the order</p>
    </div>
    <a href="suspended_bills.php" class="btn btn-outline-secondary btn-sm">📋 Suspended Bills</a>
</div>

<!-- ===================== POS LAYOUT ===================== -->
<div class="pos-wrap">

    <!-- LEFT: Product picker + order table -->
    <div style="display:flex;flex-direction:column;gap:14px;">

        <!-- Product picker card -->
        <div class="card">
            <div class="card-body">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <span style="font-family:var(--font-display);font-size:0.95rem;font-weight:700;">Products</span>
                    <span style="font-size:0.75rem;font-family:var(--font-mono);color:var(--muted);" id="productCount"><?= count($products) ?> items</span>
                </div>
                <div class="product-search-wrap">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="productSearch" placeholder="Search by name or SKU…">
                </div>
                <div class="product-grid" id="productGrid">
                    <?php foreach ($products as $p): ?>
                    <div class="prod-tile <?= $p['stock_qty'] <= 0 ? 'out-of-stock' : '' ?>"
                         data-id="<?= $p['id'] ?>"
                         data-name="<?= e($p['name']) ?>"
                         data-sku="<?= e($p['sku'] ?? '') ?>"
                         data-price="<?= (float)$p['selling_price'] ?>"
                         data-stock="<?= (float)$p['stock_qty'] ?>"
                         onclick="addToOrder(this)">
                        <div class="prod-name" title="<?= e($p['name']) ?>"><?= e($p['name']) ?></div>
                        <?php if ($p['sku']): ?>
                        <div class="prod-sku"><?= e($p['sku']) ?></div>
                        <?php endif; ?>
                        <div class="prod-price">Rs. <?= number_format($p['selling_price'], 2) ?></div>
                        <div class="prod-stock <?= $p['stock_qty'] <= 5 ? 'low' : '' ?>">
                            Stock: <?= number_format($p['stock_qty'], 0) ?>
                            <?= $p['stock_qty'] <= 0 ? '— Out' : ($p['stock_qty'] <= 5 ? '⚠ Low' : '') ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Order items card -->
        <div class="card">
            <div class="card-body">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <span style="font-family:var(--font-display);font-size:0.95rem;font-weight:700;">Order Items</span>
                    <button onclick="clearOrder()" style="background:none;border:none;font-size:0.75rem;color:var(--muted);cursor:pointer;font-family:var(--font-body);" title="Clear all items">✕ Clear</button>
                </div>
                <div class="order-items-wrap">
                    <div class="empty-order" id="emptyOrder">
                        <div class="eo-icon">🛒</div>
                        <p>No items added yet.<br>Click a product above to start.</p>
                    </div>
                    <table class="order-table" id="orderTable" style="display:none;">
                        <thead>
                            <tr>
                                <th style="width:35%;">Product</th>
                                <th style="width:20%;">Qty</th>
                                <th style="width:15%;text-align:right;">Price</th>
                                <th style="width:15%;text-align:right;">Disc.</th>
                                <th style="width:15%;text-align:right;">Total</th>
                                <th style="width:5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="orderBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- RIGHT: Billing panel -->
    <div class="billing-panel">
        <div class="card">
            <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">

                <!-- Customer -->
                <div>
                    <div class="bill-section-title">Customer</div>
                    <div class="customer-select-wrap">
                        <input type="text" class="customer-search-input" id="customerSearch"
                               placeholder="Search customer…" autocomplete="off">
                        <div class="customer-dropdown" id="customerDropdown">
                            <div class="customer-option" data-id="0" data-name="Walk-in Customer" data-outstanding="0" onclick="selectCustomer(this)">
                                <div class="c-name">👤 Walk-in Customer</div>
                                <div class="c-meta">No account</div>
                            </div>
                            <?php foreach ($customers as $c): ?>
                            <div class="customer-option"
                                 data-id="<?= $c['id'] ?>"
                                 data-name="<?= e($c['name']) ?>"
                                 data-phone="<?= e($c['phone'] ?? '') ?>"
                                 data-outstanding="<?= (float)$c['outstanding_balance'] ?>"
                                 onclick="selectCustomer(this)">
                                <div class="c-name"><?= e($c['name']) ?></div>
                                <div class="c-meta">
                                    <?= $c['phone'] ? e($c['phone']) . ' · ' : '' ?>
                                    <?php if ($c['outstanding_balance'] > 0): ?>
                                    <span class="c-outstanding">Outstanding: Rs. <?= number_format($c['outstanding_balance'], 2) ?></span>
                                    <?php else: ?>
                                    No outstanding
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="selected-customer" id="selectedCustomer">
                            <div>
                                <div class="sc-name" id="scName"></div>
                                <div class="sc-outstanding" id="scOutstanding" style="display:none;"></div>
                            </div>
                            <button class="sc-clear" onclick="clearCustomer()" title="Change customer">✕</button>
                        </div>
                    </div>

                    <!-- Outstanding toggle -->
                    <div class="outstanding-toggle" id="outstandingToggle" style="display:none;" onclick="toggleOutstanding()">
                        <input type="checkbox" id="isOutstanding" onclick="event.stopPropagation();" onchange="toggleOutstanding()">
                        <div>
                            <label for="isOutstanding">Mark as Outstanding</label>
                            <div class="ot-sub">Customer pays later</div>
                        </div>
                    </div>
                </div>

                <!-- Payment method -->
                <div>
                    <div class="bill-section-title">Payment Method</div>
                    <div class="pay-methods">
                        <button class="pay-pill selected" data-method="Cash" onclick="selectPayment(this)">💵 Cash</button>
                        <button class="pay-pill" data-method="Card" onclick="selectPayment(this)">💳 Card</button>
                        <button class="pay-pill" data-method="Bank Transfer" onclick="selectPayment(this)">🏦 Bank</button>
                        <button class="pay-pill" data-method="Cheque" onclick="selectPayment(this)">📝 Cheque</button>
                    </div>
                </div>

                <!-- Bill discount -->
                <div>
                    <div class="bill-section-title">Bill Discount</div>
                    <div class="bill-discount-wrap">
                        <span class="currency-symbol">Rs.</span>
                        <input type="number" id="billDiscount" class="form-control" value="0" min="0" step="0.01"
                               oninput="recalc()" placeholder="0.00">
                    </div>
                </div>

                <!-- Totals -->
                <div class="totals-box">
                    <div class="total-row">
                        <span class="tl">Subtotal</span>
                        <span class="tv" id="tSubtotal">Rs. 0.00</span>
                    </div>
                    <div class="total-row">
                        <span class="tl">Item Discounts</span>
                        <span class="tv discount" id="tItemDiscount">— Rs. 0.00</span>
                    </div>
                    <div class="total-row">
                        <span class="tl">Bill Discount</span>
                        <span class="tv discount" id="tBillDiscount">— Rs. 0.00</span>
                    </div>
                    <div class="total-row grand">
                        <span class="tl">Grand Total</span>
                        <span class="tv" id="tGrand">Rs. 0.00</span>
                    </div>
                </div>

                <!-- Action buttons -->
                <button id="saveSaleBtn" onclick="submitSale()" disabled>
                    ✅ Save Sale
                </button>
                <button id="holdBillBtn" onclick="holdBill()">⏸ Hold / Suspend Bill</button>

            </div>
        </div>
    </div>

</div>

<!-- Hidden form for submission -->
<form id="saleForm" method="POST" action="<?= BASE_URL ?>/ajax/sales_ajax.php" style="display:none;">
    <input type="hidden" name="customer_id" id="fCustomerId" value="0">
    <input type="hidden" name="payment_method" id="fPaymentMethod" value="Cash">
    <input type="hidden" name="bill_discount" id="fBillDiscount" value="0">
    <input type="hidden" name="is_outstanding" id="fIsOutstanding" value="0">
    <input type="hidden" name="items_json" id="fItemsJson" value="[]">
</form>

<!-- ===================== POS SCRIPT ===================== -->
<script>
// ── State ──────────────────────────────────────────────
let orderItems   = [];   // { id, name, price, qty, itemDiscount, stock }
let selectedCust = { id: 0, name: 'Walk-in Customer', outstanding: 0 };
let paymentMethod = 'Cash';
let isOutstanding = false;

// ── Add to order ────────────────────────────────────────
function addToOrder(tile) {
    if (tile.classList.contains('out-of-stock')) return;

    const id    = parseInt(tile.dataset.id);
    const name  = tile.dataset.name;
    const price = parseFloat(tile.dataset.price);
    const stock = parseFloat(tile.dataset.stock);

    const existing = orderItems.find(i => i.id === id);
    if (existing) {
        if (existing.qty >= stock) {
            flashTile(tile, 'error'); return;
        }
        existing.qty++;
    } else {
        orderItems.push({ id, name, price, qty: 1, itemDiscount: 0, stock });
    }

    flashTile(tile, 'success');
    renderOrder();
}

function flashTile(tile, type) {
    const color = type === 'success' ? 'var(--primary-light)' : '#fdf0ef';
    const border = type === 'success' ? 'var(--primary)' : 'var(--danger)';
    tile.style.background = color;
    tile.style.borderColor = border;
    setTimeout(() => { tile.style.background = ''; tile.style.borderColor = ''; }, 300);
}

// ── Render order table ──────────────────────────────────
function renderOrder() {
    const tbody  = document.getElementById('orderBody');
    const table  = document.getElementById('orderTable');
    const empty  = document.getElementById('emptyOrder');

    if (orderItems.length === 0) {
        table.style.display = 'none';
        empty.style.display = 'block';
        recalc(); return;
    }

    table.style.display = 'table';
    empty.style.display = 'none';

    tbody.innerHTML = orderItems.map((item, idx) => `
        <tr>
            <td>
                <div style="font-weight:700;font-size:0.83rem;line-height:1.2;">${escHtml(item.name)}</div>
                <div style="font-size:0.72rem;color:var(--muted);font-family:var(--font-mono);">
                    Rs. ${fmt(item.price)} · Stock: ${item.stock}
                </div>
            </td>
            <td>
                <div class="qty-control">
                    <button class="qty-btn" onclick="changeQty(${idx}, -1)">−</button>
                    <input class="qty-input" type="number" min="1" max="${item.stock}"
                           value="${item.qty}" onchange="setQty(${idx}, this.value)">
                    <button class="qty-btn" onclick="changeQty(${idx}, 1)">+</button>
                </div>
            </td>
            <td style="text-align:right;font-family:var(--font-mono);">${fmt(item.price)}</td>
            <td style="text-align:right;">
                <input class="item-discount-input" type="number" min="0" step="0.01"
                       value="${item.itemDiscount}" placeholder="0"
                       onchange="setItemDiscount(${idx}, this.value)"
                       title="Discount for this item">
            </td>
            <td style="text-align:right;font-family:var(--font-mono);font-weight:700;color:var(--dark);">
                ${fmt(lineTotal(item))}
            </td>
            <td>
                <button class="remove-btn" onclick="removeItem(${idx})" title="Remove">✕</button>
            </td>
        </tr>
    `).join('');

    recalc();
}

function lineTotal(item) {
    return Math.max(0, (item.price * item.qty) - item.itemDiscount);
}

// ── Qty / discount controls ─────────────────────────────
function changeQty(idx, delta) {
    const item = orderItems[idx];
    const newQty = item.qty + delta;
    if (newQty < 1) { removeItem(idx); return; }
    if (newQty > item.stock) return;
    item.qty = newQty;
    renderOrder();
}

function setQty(idx, val) {
    const q = Math.max(1, Math.min(parseInt(val) || 1, orderItems[idx].stock));
    orderItems[idx].qty = q;
    renderOrder();
}

function setItemDiscount(idx, val) {
    orderItems[idx].itemDiscount = Math.max(0, parseFloat(val) || 0);
    recalc();
}

function removeItem(idx) {
    orderItems.splice(idx, 1);
    renderOrder();
}

function clearOrder() {
    orderItems = [];
    renderOrder();
}

// ── Recalculate totals ──────────────────────────────────
function recalc() {
    const subtotal     = orderItems.reduce((s, i) => s + i.price * i.qty, 0);
    const itemDisc     = orderItems.reduce((s, i) => s + i.itemDiscount, 0);
    const billDisc     = Math.max(0, parseFloat(document.getElementById('billDiscount').value) || 0);
    const grand        = Math.max(0, subtotal - itemDisc - billDisc);

    document.getElementById('tSubtotal').textContent     = 'Rs. ' + fmt(subtotal);
    document.getElementById('tItemDiscount').textContent = '— Rs. ' + fmt(itemDisc);
    document.getElementById('tBillDiscount').textContent = '— Rs. ' + fmt(billDisc);
    document.getElementById('tGrand').textContent        = 'Rs. ' + fmt(grand);

    document.getElementById('saveSaleBtn').disabled = orderItems.length === 0;
}

// ── Customer ────────────────────────────────────────────
document.getElementById('customerSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const dd = document.getElementById('customerDropdown');
    dd.classList.add('open');
    dd.querySelectorAll('.customer-option').forEach(opt => {
        const name  = (opt.dataset.name  || '').toLowerCase();
        const phone = (opt.dataset.phone || '').toLowerCase();
        opt.style.display = (name.includes(q) || phone.includes(q)) ? '' : 'none';
    });
});

document.getElementById('customerSearch').addEventListener('focus', function() {
    document.getElementById('customerDropdown').classList.add('open');
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.customer-select-wrap')) {
        document.getElementById('customerDropdown').classList.remove('open');
    }
});

function selectCustomer(opt) {
    selectedCust = {
        id:          parseInt(opt.dataset.id),
        name:        opt.dataset.name,
        outstanding: parseFloat(opt.dataset.outstanding) || 0
    };

    document.getElementById('customerSearch').value = '';
    document.getElementById('customerDropdown').classList.remove('open');

    document.getElementById('scName').textContent = selectedCust.name;

    const scOut = document.getElementById('scOutstanding');
    if (selectedCust.outstanding > 0) {
        scOut.textContent = 'Outstanding: Rs. ' + fmt(selectedCust.outstanding);
        scOut.style.display = '';
    } else {
        scOut.style.display = 'none';
    }

    document.getElementById('selectedCustomer').classList.add('show');

    // Show outstanding toggle only for real customers
    const toggle = document.getElementById('outstandingToggle');
    toggle.style.display = selectedCust.id > 0 ? 'flex' : 'none';
    isOutstanding = false;
    document.getElementById('isOutstanding').checked = false;
    toggle.classList.remove('active');
}

function clearCustomer() {
    selectedCust = { id: 0, name: 'Walk-in Customer', outstanding: 0 };
    document.getElementById('selectedCustomer').classList.remove('show');
    document.getElementById('customerSearch').value = '';
    document.getElementById('outstandingToggle').style.display = 'none';
    isOutstanding = false;
}

// ── Outstanding toggle ──────────────────────────────────
function toggleOutstanding() {
    isOutstanding = document.getElementById('isOutstanding').checked;
    document.getElementById('outstandingToggle').classList.toggle('active', isOutstanding);
    // If outstanding, force payment method to "Outstanding"
    if (isOutstanding) {
        paymentMethod = 'Outstanding';
        document.querySelectorAll('.pay-pill').forEach(p => p.classList.remove('selected'));
    } else {
        paymentMethod = 'Cash';
        document.querySelector('.pay-pill[data-method="Cash"]').classList.add('selected');
    }
}

// ── Payment method ──────────────────────────────────────
function selectPayment(btn) {
    if (isOutstanding) return;  // locked when outstanding
    document.querySelectorAll('.pay-pill').forEach(p => p.classList.remove('selected'));
    btn.classList.add('selected');
    paymentMethod = btn.dataset.method;
}

// ── Product search ──────────────────────────────────────
document.getElementById('productSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const tiles = document.querySelectorAll('.prod-tile');
    let visible = 0;
    tiles.forEach(t => {
        const match = t.dataset.name.toLowerCase().includes(q) || (t.dataset.sku || '').toLowerCase().includes(q);
        t.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('productCount').textContent = visible + ' items';
});

// ── Submit sale ─────────────────────────────────────────
function submitSale() {
    if (orderItems.length === 0) return;

    document.getElementById('fCustomerId').value   = selectedCust.id;
    document.getElementById('fPaymentMethod').value = isOutstanding ? 'Outstanding' : paymentMethod;
    document.getElementById('fBillDiscount').value  = document.getElementById('billDiscount').value || 0;
    document.getElementById('fIsOutstanding').value = isOutstanding ? 1 : 0;
    document.getElementById('fItemsJson').value     = JSON.stringify(orderItems.map(i => ({
        product_id:    i.id,
        qty:           i.qty,
        unit_price:    i.price,
        item_discount: i.itemDiscount,
        line_total:    lineTotal(i)
    })));

    document.getElementById('saveSaleBtn').disabled = true;
    document.getElementById('saveSaleBtn').textContent = '⏳ Saving…';
    document.getElementById('saleForm').submit();
}

// ── Hold bill ───────────────────────────────────────────
function holdBill() {
    if (orderItems.length === 0) { alert('No items to hold.'); return; }

    const billData = {
        customer: selectedCust,
        items: orderItems,
        paymentMethod,
        isOutstanding,
        billDiscount: parseFloat(document.getElementById('billDiscount').value) || 0,
        heldAt: new Date().toISOString()
    };

    fetch('<?= BASE_URL ?>/ajax/sales_ajax.php?action=hold', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(billData)
    }).then(r => r.json()).then(data => {
        if (data.success) {
            clearOrder();
            clearCustomer();
            document.getElementById('billDiscount').value = 0;
            recalc();
            alert('Bill suspended successfully!');
        } else {
            alert('Failed to hold bill: ' + (data.message || 'Unknown error'));
        }
    }).catch(() => alert('Network error holding bill.'));
}

// ── Helpers ─────────────────────────────────────────────
function fmt(n) { return parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }

function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Init
recalc();
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
