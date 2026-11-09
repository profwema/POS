<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * شاشة نقطة البيع السريعة للكاشير - Fast POS Cashier Engine
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();

$user = Auth::user();
$userId = (int)$user['id'];

// Check active shift
$activeShift = Shift::getActiveShift($userId);
$stores = Store::getAll(true);
$categories = Category::getAll(true);
$customers = Customer::getAll('', 50);
$heldCarts = PosService::getHeldCarts($userId);

// Fetch top 30 active items for quick-touch grid
$quickItems = Item::getAll(['is_active' => 1], 30);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Security::csrfToken() ?>">
    <title>شاشة الكاشير السريعة (POS) | <?= APP_NAME ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Fira+Code:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin-pro.css?v=2.1">
    
    <style>
        :root {
            --pos-header-h: 60px;
            --pos-cart-w: 460px;
        }

        body {
            background-color: var(--bg-body);
            overflow: hidden;
            height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
            color: var(--text-main);
            user-select: none;
        }

        /* POS Header */
        .pos-header {
            height: var(--pos-header-h);
            background: linear-gradient(135deg, #070d18 0%, #0b1528 100%);
            border-bottom: 2px solid rgba(2, 132, 199, 0.35);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            position: relative;
            z-index: 50;
        }

        /* Layout Grid */
        .pos-workspace {
            display: grid;
            grid-template-columns: 1fr var(--pos-cart-w);
            height: calc(100vh - var(--pos-header-h));
            overflow: hidden;
        }

        /* Catalog / Products Section (Right Side) */
        .pos-catalog-panel {
            display: flex;
            flex-direction: column;
            border-left: 1px solid var(--border-color);
            background: rgba(15, 23, 42, 0.4);
            overflow: hidden;
        }

        /* Search & Barcode Bar */
        .pos-search-bar {
            padding: 0.85rem 1.25rem;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .barcode-input-wrap {
            position: relative;
            flex: 1;
        }

        .barcode-input-wrap input {
            padding-right: 42px;
            font-family: 'Fira Code', monospace;
            font-size: 1.1rem;
            height: 46px;
            background: rgba(2, 6, 23, 0.8);
            border: 2px solid var(--brand-blue);
            color: #fff;
            box-shadow: 0 0 12px rgba(2, 132, 199, 0.25);
        }

        .barcode-input-wrap input:focus {
            border-color: var(--brand-amber);
            box-shadow: 0 0 16px rgba(245, 158, 11, 0.35);
        }

        .barcode-input-wrap i {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: var(--brand-blue);
        }

        /* Category Filter Chips */
        .pos-category-chips {
            display: flex;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            overflow-x: auto;
            white-space: nowrap;
            background: rgba(15, 23, 42, 0.6);
            border-bottom: 1px solid var(--border-color);
        }

        .cat-chip {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .cat-chip:hover, .cat-chip.active {
            background: var(--brand-blue);
            color: #fff;
            border-color: var(--brand-blue);
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.4);
        }

        /* Product Cards Grid */
        .pos-products-grid {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            grid-auto-rows: 125px;
            gap: 0.85rem;
            align-content: start;
        }

        .product-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.15s ease;
        }

        .product-card:hover {
            transform: translateY(-2px);
            border-color: var(--brand-amber);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
        }

        .product-card:active {
            transform: scale(0.97);
        }

        .product-card .p-name {
            font-weight: 700;
            font-size: 0.88rem;
            line-height: 1.3;
            color: #fff;
            max-height: 2.6em;
            overflow: hidden;
        }

        .product-card .p-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 4px;
        }

        .product-card .p-price {
            font-weight: 800;
            font-size: 1rem;
            color: #38bdf8;
            font-family: 'Fira Code', sans-serif;
        }

        .product-card .p-scale-tag {
            font-size: 0.65rem;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            padding: 1px 4px;
            border-radius: 4px;
        }

        /* Cart Section (Left Side) */
        .pos-cart-panel {
            display: flex;
            flex-direction: column;
            background: var(--bg-card);
            overflow: hidden;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.3);
        }

        .pos-cart-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(2, 6, 23, 0.5);
        }

        /* Cart Items List */
        .pos-cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem;
        }

        .cart-item-row {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 0.6rem 0.75rem;
            margin-bottom: 0.4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.15s ease;
        }

        .cart-item-row:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .cart-item-info {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-weight: 700;
            font-size: 0.88rem;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cart-item-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .cart-qty-ctrl {
            display: flex;
            align-items: center;
            gap: 4px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 6px;
            padding: 2px;
            border: 1px solid var(--border-color);
        }

        .cart-qty-btn {
            width: 24px;
            height: 24px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        .cart-qty-btn:hover {
            background: var(--brand-blue);
        }

        .cart-qty-input {
            width: 44px;
            border: none;
            background: transparent;
            color: #fff;
            font-weight: 800;
            text-align: center;
            font-size: 0.9rem;
            font-family: 'Fira Code', monospace;
        }

        .cart-item-total {
            font-weight: 800;
            font-size: 0.98rem;
            color: #38bdf8;
            font-family: 'Fira Code', monospace;
            min-width: 65px;
            text-align: left;
        }

        .cart-del-btn {
            background: transparent;
            border: none;
            color: #f87171;
            cursor: pointer;
            padding: 4px;
            opacity: 0.7;
        }

        .cart-del-btn:hover {
            opacity: 1;
        }

        /* Cart Summary & Checkout Box */
        .pos-checkout-box {
            padding: 1rem;
            background: rgba(2, 6, 23, 0.85);
            border-top: 2px solid var(--border-color);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
        }

        .grand-total-box {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin: 0.6rem 0 0.85rem;
            padding-top: 0.6rem;
            border-top: 1px dashed var(--border-color);
        }

        .grand-total-label {
            font-size: 1.1rem;
            font-weight: 800;
            color: #fff;
        }

        .grand-total-val {
            font-size: 2rem;
            font-weight: 900;
            color: var(--brand-amber);
            font-family: 'Fira Code', monospace;
            text-shadow: 0 0 15px rgba(245, 158, 11, 0.3);
        }

        /* Action Buttons Grid */
        .pos-action-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 0.6rem;
        }

        .btn-cash-quick {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 800;
            font-size: 1.05rem;
            padding: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.15s ease;
        }

        .btn-cash-quick:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .btn-pay-modal {
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.95rem;
            padding: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            box-shadow: 0 4px 15px rgba(2, 132, 199, 0.3);
        }

        .btn-pay-modal:hover {
            filter: brightness(1.1);
        }

        /* Quick Pay Modal Preset Buttons */
        .cash-preset-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: #fff;
            border-radius: 6px;
            padding: 8px 12px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .cash-preset-btn:hover {
            background: var(--brand-blue);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .pos-workspace {
                grid-template-columns: 1fr 380px;
            }
        }
    </style>
</head>
<body>

    <!-- ================= POS HEADER ================= -->
    <header class="pos-header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="<?= BASE_URL ?>index.php" title="العودة للوحة الإدارة" style="display: flex; align-items: center;">
                <img src="<?= BASE_URL ?>assets/brand/logo-horizontal-light.png" alt="المبرمج | Almubarmig" style="height: 30px; object-fit: contain;">
            </a>
            <span class="badge badge-amber" style="font-size: 0.72rem;">نقطة البيع السريعة (POS)</span>
            
            <?php if ($activeShift): ?>
                <span class="badge badge-emerald" style="font-family: monospace; font-size: 0.75rem;" title="الوردية الحالية">
                    <i class="fas fa-clock" style="margin-left: 3px;"></i>
                    <?= htmlspecialchars($activeShift['shift_number']) ?>
                </span>
            <?php else: ?>
                <span class="badge badge-rose" style="font-size: 0.75rem;">لا توجد وردية مفتوحة</span>
            <?php endif; ?>
        </div>

        <div style="display: flex; align-items: center; gap: 1rem;">
            <!-- Current Time -->
            <div id="liveClock" style="font-family: 'Fira Code', monospace; font-size: 0.95rem; font-weight: 700; color: #38bdf8;">
                --:--:--
            </div>

            <!-- Held Carts Button -->
            <button type="button" class="btn btn-outline btn-sm" onclick="openHeldCartsModal()" style="border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;">
                <i class="fas fa-pause-circle"></i>
                المعلقة (<span id="heldCount"><?= count($heldCarts) ?></span>)
                <kbd style="background: rgba(0,0,0,0.4); font-size: 0.65rem; padding: 1px 4px; border-radius: 3px;">F10</kbd>
            </button>

            <!-- Hotkeys Guide Button -->
            <button type="button" class="btn btn-outline btn-sm" onclick="openHotkeysModal()" title="دليل اختصارات الكيبورد">
                <i class="fas fa-keyboard"></i>
                اختصارات <kbd style="background: rgba(0,0,0,0.4); font-size: 0.65rem; padding: 1px 4px; border-radius: 3px;">F1</kbd>
            </button>

            <!-- Cashier Profile / Close Shift -->
            <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 20px; border: 1px solid var(--border-color);">
                <i class="fas fa-user-circle" style="color: #38bdf8;"></i>
                <span style="font-size: 0.85rem; font-weight: 700;"><?= htmlspecialchars($user['full_name'] ?? $user['username']) ?></span>
                
                <?php if ($activeShift): ?>
                    <button type="button" class="btn btn-outline btn-sm" onclick="openCloseShiftModal()" style="padding: 2px 8px; font-size: 0.75rem; border-color: rgba(248,113,113,0.4); color: #f87171;" title="إغلاق الوردية وجرد النقدية">
                        إغلاق الوردية
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-primary btn-sm" onclick="openOpenShiftModal()" style="padding: 2px 8px; font-size: 0.75rem;">
                        بدء وردية
                    </button>
                <?php endif; ?>
            </div>

            <!-- Exit POS to Dashboard -->
            <a href="<?= BASE_URL ?>index.php" class="btn btn-outline btn-sm" style="border: none; color: var(--text-muted);" title="خروج من شاشة البيع">
                <i class="fas fa-sign-out-alt" style="font-size: 1.1rem;"></i>
            </a>
        </div>
    </header>

    <!-- ================= MAIN POS WORKSPACE ================= -->
    <div class="pos-workspace">

        <!-- RIGHT PANEL: Products, Search & Category Filters -->
        <div class="pos-catalog-panel">
            
            <!-- Barcode & Search Input Bar -->
            <div class="pos-search-bar">
                <div class="barcode-input-wrap">
                    <input type="text" id="barcodeInput" placeholder="امسح الباركود أو ابحث بالاسم... (F2)" autocomplete="off" autofocus>
                    <i class="fas fa-barcode"></i>
                </div>
                <button type="button" class="btn btn-outline" onclick="openCameraScannerModal()" title="مسح الباركود عبر كاميرا الموبايل / الويب كام" style="height: 46px; border-color: rgba(56,189,248,0.4); color: #38bdf8; display: flex; align-items: center; gap: 0.4rem;">
                    <i class="fas fa-camera"></i>
                    <span style="font-size: 0.82rem;">كاميرا</span>
                </button>
            </div>

            <!-- Category Filter Chips -->
            <div class="pos-category-chips">
                <div class="cat-chip active" onclick="filterCategory(null, this)">
                    <i class="fas fa-th-large"></i> الكل
                </div>
                <div class="cat-chip" onclick="filterCategory('scale', this)" style="border-color: rgba(16, 185, 129, 0.4); color: #34d399;">
                    ⚖️ أصناف الميزان
                </div>
                <?php foreach ($categories as $cat): ?>
                    <div class="cat-chip" onclick="filterCategory(<?= $cat['id'] ?>, this)">
                        <i class="<?= htmlspecialchars($cat['icon']) ?>"></i>
                        <?= htmlspecialchars($cat['name']) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Product Cards Grid -->
            <div id="productsGrid" class="pos-products-grid">
                <?php foreach ($quickItems as $item): ?>
                    <div class="product-card" onclick="onProductCardClick(<?= htmlspecialchars(json_encode($item)) ?>)" data-cat-id="<?= $item['category_id'] ?>" data-scale="<?= $item['is_scale'] ?>">
                        <div class="p-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="p-footer">
                            <div class="p-price"><?= number_format($item['retail_price'], 2) ?> <span style="font-size: 0.7rem; color: #94a3b8;">ج.م</span></div>
                            <?php if ($item['is_scale']): ?>
                                <span class="p-scale-tag">⚖️ ميزان</span>
                            <?php else: ?>
                                <span style="font-size: 0.7rem; color: var(--text-muted);"><?= htmlspecialchars($item['unit_symbol']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- LEFT PANEL: Shopping Cart & Checkout Summary -->
        <div class="pos-cart-panel">
            
            <!-- Cart Header (Customer Selector + Reset) -->
            <div class="pos-cart-header">
                <div style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-user" style="color: #38bdf8;"></i>
                    <select id="customerSelect" class="form-control" style="height: 34px; padding: 2px 8px; font-size: 0.85rem; max-width: 250px;">
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($c['id'] == 1) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-outline btn-sm" onclick="openNewCustomerModal()" title="إضافة عميل سريع" style="padding: 4px 8px; height: 34px;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <button type="button" class="btn btn-outline btn-sm" onclick="clearCart()" title="إلغاء وتفريغ السلة (Esc)" style="border-color: rgba(248,113,113,0.3); color: #f87171; padding: 4px 8px;">
                    <i class="fas fa-trash-alt"></i> مسح
                </button>
            </div>

            <!-- Cart Items Scroll List -->
            <div id="cartItemsList" class="pos-cart-items">
                <!-- Rendered dynamically by JS -->
            </div>

            <!-- Cart Checkout Summary Box -->
            <div class="pos-checkout-box">
                <div class="summary-row">
                    <span>الإجمالي قبل الضريبة:</span>
                    <span id="subtotalVal" style="font-family: 'Fira Code', monospace; color: #fff;">0.00 ج.م</span>
                </div>

                <div class="summary-row" id="taxRow">
                    <span>ضريبة القيمة المضافة (14%):</span>
                    <span id="taxVal" style="font-family: 'Fira Code', monospace; color: #fff;">0.00 ج.م</span>
                </div>

                <div class="summary-row" id="discountRow" style="display: none; color: #f87171;">
                    <span>الخصم الممنوح:</span>
                    <span id="discountVal" style="font-family: 'Fira Code', monospace;">-0.00 ج.م</span>
                </div>

                <div class="grand-total-box">
                    <span class="grand-total-label">الصافي المطلوب:</span>
                    <span id="grandTotalVal" class="grand-total-val">0.00</span>
                </div>

                <!-- Action Buttons Grid -->
                <div class="pos-action-grid">
                    <button type="button" class="btn-cash-quick" onclick="processQuickCash()">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>كاش سريع (F4)</span>
                    </button>

                    <button type="button" class="btn-pay-modal" onclick="openPaymentModal()">
                        <i class="fas fa-credit-card"></i>
                        <span>دفع متعدد (F7)</span>
                    </button>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-top: 0.5rem;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="holdCurrentCart()" style="width: 100%; border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;">
                        <i class="fas fa-pause"></i> تعليق السلة (F8)
                    </button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="promptDiscount()" style="width: 100%; border-color: rgba(56, 189, 248, 0.4); color: #38bdf8;">
                        <i class="fas fa-percent"></i> إضافة خصم (F6)
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- ================= MODAL 1: نافذة الدفع المتعدد والمتقدم (F7) ================= -->
    <div id="paymentModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="width: 100%; max-width: 620px; background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 25px 50px rgba(0,0,0,0.6); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #fff; margin: 0;">
                    <i class="fas fa-cash-register" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                    إنهاء وسداد الفاتورة
                </h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="closePaymentModal()" style="border: none; font-size: 1.1rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Total Banner -->
            <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 8px; padding: 0.75rem 1.25rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 700; color: #fde68a;">إجمالي الفاتورة المطلوب سداده:</span>
                <span id="modalGrandTotal" style="font-size: 1.75rem; font-weight: 900; color: #fbbf24; font-family: 'Fira Code', monospace;">0.00 ج.م</span>
            </div>

            <!-- Payment Methods Inputs -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
                
                <!-- 1. كاش -->
                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 0.75rem; align-items: center;">
                    <label style="font-weight: 700; color: #fff; margin: 0;">
                        <i class="fas fa-money-bill" style="color: #10b981; margin-left: 0.4rem;"></i> نقداً (كاش):
                    </label>
                    <input type="number" step="0.01" min="0" id="payCash" class="form-control" placeholder="0.00" oninput="calculatePayments()" style="font-size: 1.1rem; font-weight: 700;">
                </div>

                <!-- Quick Cash Presets -->
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button type="button" class="cash-preset-btn" onclick="setCashPreset('exact')">المبلغ بالضبط</button>
                    <button type="button" class="cash-preset-btn" onclick="setCashPreset(50)">50 ج.م</button>
                    <button type="button" class="cash-preset-btn" onclick="setCashPreset(100)">100 ج.م</button>
                    <button type="button" class="cash-preset-btn" onclick="setCashPreset(200)">200 ج.م</button>
                    <button type="button" class="cash-preset-btn" onclick="setCashPreset(500)">500 ج.م</button>
                </div>

                <!-- 2. بطاقة بنكية / فيزا / ميزة POS -->
                <div style="display: grid; grid-template-columns: 140px 1fr 140px; gap: 0.75rem; align-items: center;">
                    <label style="font-weight: 700; color: #fff; margin: 0;">
                        <i class="fas fa-credit-card" style="color: #38bdf8; margin-left: 0.4rem;"></i> كارت / ميزة:
                    </label>
                    <input type="number" step="0.01" min="0" id="payCard" class="form-control" placeholder="0.00" oninput="calculatePayments()">
                    <input type="text" id="payCardRef" class="form-control" placeholder="رقم العملية (POS)">
                </div>

                <!-- 3. شبكة إنستاباي (InstaPay IPN) -->
                <div style="display: grid; grid-template-columns: 140px 1fr 140px; gap: 0.75rem; align-items: center;">
                    <label style="font-weight: 700; color: #fff; margin: 0;">
                        <i class="fas fa-bolt" style="color: #f59e0b; margin-left: 0.4rem;"></i> إنستاباي (IPN):
                    </label>
                    <input type="number" step="0.01" min="0" id="payInstapay" class="form-control" placeholder="0.00" oninput="calculatePayments()">
                    <input type="text" id="payInstapayRef" class="form-control" placeholder="رقم التحويل / IPA">
                </div>

                <!-- 4. محفظة إلكترونية ذكية (Vodafone/Orange/Etisalat/WE) -->
                <div style="display: grid; grid-template-columns: 140px 1fr 140px; gap: 0.75rem; align-items: center;">
                    <label style="font-weight: 700; color: #fff; margin: 0;">
                        <i class="fas fa-mobile-alt" style="color: #a855f7; margin-left: 0.4rem;"></i> محفظة كاش:
                    </label>
                    <input type="number" step="0.01" min="0" id="payWallet" class="form-control" placeholder="0.00" oninput="calculatePayments()">
                    <input type="text" id="payWalletRef" class="form-control" placeholder="رقم المحفظة / العملية">
                </div>

                <!-- 5. آجل على حساب العميل -->
                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 0.75rem; align-items: center;">
                    <label style="font-weight: 700; color: #fff; margin: 0;">
                        <i class="fas fa-user-clock" style="color: #ec4899; margin-left: 0.4rem;"></i> آجل على الحساب:
                    </label>
                    <input type="number" step="0.01" min="0" id="payCredit" class="form-control" placeholder="0.00" oninput="calculatePayments()">
                </div>
            </div>

            <!-- Calculation Result Bar -->
            <div style="background: rgba(2, 6, 23, 0.8); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.85rem 1.25rem; margin-bottom: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <div style="font-size: 0.82rem; color: var(--text-muted);">إجمالي المدفوع:</div>
                    <div id="modalTotalPaid" style="font-size: 1.25rem; font-weight: 800; color: #fff; font-family: 'Fira Code', monospace;">0.00 ج.م</div>
                </div>
                <div>
                    <div style="font-size: 0.82rem; color: var(--text-muted);" id="modalChangeLabel">المتبقي للعميل (الباقي):</div>
                    <div id="modalChangeVal" style="font-size: 1.25rem; font-weight: 900; color: #10b981; font-family: 'Fira Code', monospace;">0.00 ج.م</div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-outline" onclick="closePaymentModal()">إلغاء</button>
                <button type="button" class="btn btn-primary" id="confirmCheckoutBtn" onclick="submitCheckout()">
                    <i class="fas fa-print"></i> تأكيد وطباعة الإيصال
                </button>
            </div>
        </div>
    </div>

    <!-- ================= MODAL 2: فتح وردية جديدة ================= -->
    <div id="openShiftModal" class="modal-backdrop" style="display: <?= $activeShift ? 'none' : 'flex' ?>; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(6px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="width: 100%; max-width: 450px; background: var(--bg-card); border: 1px solid var(--brand-amber); box-shadow: 0 25px 50px rgba(0,0,0,0.7); padding: 1.5rem;">
            <div style="text-align: center; margin-bottom: 1.25rem;">
                <i class="fas fa-door-open" style="font-size: 2.5rem; color: var(--brand-amber); margin-bottom: 0.5rem;"></i>
                <h3 style="font-size: 1.3rem; font-weight: 800; color: #fff; margin: 0;">بدء وردية كاشير جديدة</h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">
                    يرجى تسجيل العهدة النقدية الافتتاحية في الدرج قبل بدء البيع
                </p>
            </div>

            <form id="openShiftForm" onsubmit="submitOpenShift(event)">
                <div class="form-group">
                    <label class="form-label required">العهدة النقدية الافتتاحية (كاش الدرج)</label>
                    <div style="position: relative;">
                        <input type="number" step="0.01" min="0" id="openingBalanceInput" class="form-control" value="0.00" required style="font-size: 1.25rem; font-weight: 800; color: #38bdf8; text-align: center;">
                        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);">ج.م</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required">مستودع البيع / الفرع</label>
                    <select id="shiftStoreSelect" class="form-control" required>
                        <?php foreach ($stores as $st): ?>
                            <option value="<?= $st['id'] ?>" <?= ($st['id'] == 2) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($st['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">ملاحظات البداية</label>
                    <textarea id="shiftOpenNotes" class="form-control" rows="2" placeholder="أي ملاحظات حول عهدة بداية الوردية..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; font-weight: 800; justify-content: center; margin-top: 1rem;">
                    <i class="fas fa-play"></i> بدء الوردية وفتح شاشة البيع
                </button>
            </form>
        </div>
    </div>

    <!-- ================= MODAL 3: إغلاق الوردية وجرد النقدية ================= -->
    <div id="closeShiftModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="width: 100%; max-width: 500px; background: var(--bg-card); border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1rem;">
                <h3 style="font-size: 1.2rem; font-weight: 800; color: #fff; margin: 0;">
                    <i class="fas fa-lock" style="color: #f87171; margin-left: 0.5rem;"></i>
                    إغلاق الوردية وجرد النقدية
                </h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="closeCloseShiftModal()" style="border: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="shiftSummaryLoading" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem;"></i>
                <div style="margin-top: 0.5rem;">جاري احتساب حسابات الوردية...</div>
            </div>

            <form id="closeShiftForm" style="display: none;" onsubmit="submitCloseShift(event)">
                <input type="hidden" id="closingShiftId" value="<?= $activeShift['id'] ?? '' ?>">

                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.88rem;">
                        <span style="color: var(--text-muted);">العهدة الافتتاحية:</span>
                        <strong id="sumOpenCash" style="color: #fff;">0.00 ج.م</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.88rem;">
                        <span style="color: var(--text-muted);">مبيعات الكاش بالوردية:</span>
                        <strong id="sumSalesCash" style="color: #10b981;">0.00 ج.م</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px dashed var(--border-color); padding-top: 0.5rem; font-size: 1.05rem;">
                        <span style="font-weight: 800; color: #fde68a;">النقدية المتوقعة بالدرج:</span>
                        <strong id="sumExpectedCash" style="color: #fbbf24; font-family: 'Fira Code', monospace;">0.00 ج.م</strong>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required">النقدية الفعلية بعد العد (كاش الدرج الفعلي)</label>
                    <input type="number" step="0.01" min="0" id="actualCashInput" class="form-control" required placeholder="0.00" oninput="calculateShiftDiff()" style="font-size: 1.25rem; font-weight: 800; color: #38bdf8;">
                </div>

                <div style="background: rgba(0,0,0,0.3); border-radius: 6px; padding: 0.6rem 1rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">الفارق (عجز / زيادة):</span>
                    <strong id="shiftDiffVal" style="font-size: 1.1rem; font-family: 'Fira Code', monospace; color: #fff;">0.00 ج.م</strong>
                </div>

                <div class="form-group">
                    <label class="form-label">ملاحظات الإغلاق أو سبب الفارق</label>
                    <textarea id="shiftCloseNotes" class="form-control" rows="2" placeholder="أي ملاحظات حول الجرد أو تسليم الشيفت..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="closeCloseShiftModal()">إلغاء</button>
                    <button type="submit" class="btn btn-primary" style="background: #dc2626; border-color: #dc2626;">
                        <i class="fas fa-check-circle"></i> اعتماد الإغلاق وتسليم الوردية
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL 4: الفواتير المعلقة (F10) ================= -->
    <div id="heldCartsModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="width: 100%; max-width: 650px; background: var(--bg-card); border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1rem;">
                <h3 style="font-size: 1.2rem; font-weight: 800; color: #fff; margin: 0;">
                    <i class="fas fa-pause-circle" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                    الفواتير والسلات المعلقة للكاشير
                </h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="closeHeldCartsModal()" style="border: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="heldCartsListContainer">
                <!-- Injected by JS -->
            </div>
        </div>
    </div>

    <!-- ================= MODAL 5: إضافة عميل سريع ================= -->
    <div id="quickCustomerModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="width: 100%; max-width: 440px; background: var(--bg-card); border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1rem;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #fff; margin: 0;">
                    <i class="fas fa-user-plus" style="color: #38bdf8; margin-left: 0.5rem;"></i>
                    إضافة عميل سريع
                </h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="closeNewCustomerModal()" style="border: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="quickCustForm" onsubmit="submitQuickCustomer(event)">
                <div class="form-group">
                    <label class="form-label required">اسم العميل</label>
                    <input type="text" id="newCustName" class="form-control" placeholder="الاسم ثلاثي أو اسم المنشأة" required>
                </div>
                <div class="form-group">
                    <label class="form-label">رقم الهاتف / الموبايل</label>
                    <input type="text" id="newCustPhone" class="form-control" placeholder="01xxxxxxxxx">
                </div>
                <div class="form-group">
                    <label class="form-label">الحد الائتماني للآجل (اختياري)</label>
                    <input type="number" step="0.01" min="0" id="newCustCredit" class="form-control" placeholder="0.00">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                    <button type="button" class="btn btn-outline" onclick="closeNewCustomerModal()">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ واختيار العميل</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL 6: قارئ كاميرا الموبايل / الويب كام ================= -->
    <div id="cameraScannerModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="width: 100%; max-width: 480px; background: var(--bg-card); border: 1px solid var(--border-color); padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #fff; margin: 0;">
                    <i class="fas fa-camera" style="color: #38bdf8; margin-left: 0.5rem;"></i>
                    مسح الباركود عبر الكاميرا
                </h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="closeCameraScannerModal()" style="border: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div style="position: relative; width: 100%; height: 300px; background: #000; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                <video id="cameraVideo" style="width: 100%; height: 100%; object-fit: cover;"></video>
                <div style="position: absolute; width: 80%; height: 120px; border: 2px dashed #38bdf8; border-radius: 8px; pointer-events: none; box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);"></div>
                <div id="cameraStatusText" style="position: absolute; bottom: 10px; color: #fff; font-size: 0.8rem; background: rgba(0,0,0,0.7); padding: 2px 10px; border-radius: 4px;">جاري تشغيل الكاميرا...</div>
            </div>

            <div style="margin-top: 1rem; text-align: center; color: var(--text-muted); font-size: 0.82rem;">
                وجّه الباركود داخل الإطار المضيء ليتم التعرف عليه تلقائياً
            </div>
        </div>
    </div>

    <!-- ================= MODAL 7: دليل الاختصارات (F1) ================= -->
    <div id="hotkeysModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="width: 100%; max-width: 500px; background: var(--bg-card); border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1rem;">
                <h3 style="font-size: 1.2rem; font-weight: 800; color: #fff; margin: 0;">
                    <i class="fas fa-keyboard" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                    دليل اختصارات لوحة المفاتيح للكاشير
                </h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="closeHotkeysModal()" style="border: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.88rem;">
                <div style="background: rgba(255,255,255,0.03); padding: 0.6rem; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <span>كاش سريع:</span> <kbd style="background: var(--brand-emerald); color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: 800;">F4</kbd>
                </div>
                <div style="background: rgba(255,255,255,0.03); padding: 0.6rem; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <span>دفع متعدد:</span> <kbd style="background: var(--brand-blue); color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: 800;">F7</kbd>
                </div>
                <div style="background: rgba(255,255,255,0.03); padding: 0.6rem; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <span>تركيز الباركود:</span> <kbd style="background: rgba(255,255,255,0.1); color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: 800;">F2</kbd>
                </div>
                <div style="background: rgba(255,255,255,0.03); padding: 0.6rem; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <span>إضافة خصم:</span> <kbd style="background: rgba(255,255,255,0.1); color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: 800;">F6</kbd>
                </div>
                <div style="background: rgba(255,255,255,0.03); padding: 0.6rem; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <span>تعليق السلة:</span> <kbd style="background: rgba(245,158,11,0.3); color: #fbbf24; padding: 2px 8px; border-radius: 4px; font-weight: 800;">F8</kbd>
                </div>
                <div style="background: rgba(255,255,255,0.03); padding: 0.6rem; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <span>السلات المعلقة:</span> <kbd style="background: rgba(245,158,11,0.3); color: #fbbf24; padding: 2px 8px; border-radius: 4px; font-weight: 800;">F10</kbd>
                </div>
                <div style="background: rgba(255,255,255,0.03); padding: 0.6rem; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; grid-column: 1 / -1;">
                    <span>إلغاء وتفريغ السلة:</span> <kbd style="background: #dc2626; color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: 800;">Esc</kbd>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= POS JAVASCRIPT ENGINE ================= -->
    <script>
    // System Configurations
    const TAX_RATE = 14.0; // Standard 14% Egyptian VAT
    let cart = [];
    let currentDiscount = 0.0;
    let expectedCashInDrawer = 0.0;

    // Web Audio Beep (Zero external files needed)
    function playBeep(freq = 800, type = 'sine', duration = 0.08) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = type;
            osc.frequency.value = freq;
            osc.connect(gain);
            gain.connect(ctx.destination);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + duration);
            osc.start();
            osc.stop(ctx.currentTime + duration);
        } catch (e) {}
    }

    // Live Clock
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
        document.getElementById('liveClock').textContent = timeStr;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Barcode Input Handler
    const barcodeInput = document.getElementById('barcodeInput');
    barcodeInput.addEventListener('keydown', async function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = this.value.trim();
            if (code) {
                await handleBarcodeScan(code);
                this.value = '';
            }
        }
    });

    // Scan Barcode via API
    async function handleBarcodeScan(code) {
        try {
            const res = await fetch('<?= BASE_URL ?>api/pos.php?action=search_item&q=' + encodeURIComponent(code));
            const data = await res.json();

            if (data.success && data.items && data.items.length > 0) {
                const item = data.items[0];
                let qty = 1.0;

                // If detected weight from scale barcode
                if (item.detected_weight && parseFloat(item.detected_weight) > 0) {
                    qty = parseFloat(item.detected_weight);
                }

                addItemToCart(item, qty);
                playBeep(950, 'sine', 0.09); // Success beep
            } else {
                playBeep(300, 'square', 0.18); // Error beep
                alert('لم يتم العثور على صنف بالباركود: ' + code);
            }
        } catch (err) {
            console.error(err);
        }
    }

    // Product Card Click
    function onProductCardClick(item) {
        addItemToCart(item, 1.0);
        playBeep(850, 'sine', 0.06);
    }

    // Add Item to Cart
    function addItemToCart(item, quantity = 1.0) {
        const existingIdx = cart.findIndex(c => c.id === item.id);
        const price = parseFloat(item.retail_price);
        const taxRate = parseFloat(item.tax_rate ?? TAX_RATE);

        if (existingIdx > -1 && !item.is_scale) {
            cart[existingIdx].quantity += quantity;
        } else {
            cart.push({
                id: item.id,
                name: item.name,
                barcode: item.barcode,
                price: price,
                quantity: quantity,
                tax_rate: taxRate,
                unit_symbol: item.unit_symbol || 'ق',
                is_scale: !!item.is_scale
            });
        }

        renderCart();
        focusBarcode();
    }

    // Render Cart Items & Totals
    function renderCart() {
        const container = document.getElementById('cartItemsList');
        
        if (cart.length === 0) {
            container.innerHTML = `
                <div style="height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); opacity: 0.5;">
                    <i class="fas fa-shopping-basket" style="font-size: 3rem; margin-bottom: 0.75rem;"></i>
                    <p style="font-size: 0.95rem; margin: 0;">السلة فارغة، امسح الباركود للبدء</p>
                </div>
            `;
            updateTotals(0, 0, 0);
            return;
        }

        let html = '';
        let subtotal = 0;
        let totalTax = 0;

        cart.forEach((item, index) => {
            const lineSub = item.price * item.quantity;
            const lineTax = lineSub * (item.tax_rate / 100);
            subtotal += lineSub;
            totalTax += lineTax;

            html += `
                <div class="cart-item-row">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-meta">
                            ${item.price.toFixed(2)} ج.م / ${item.unit_symbol}
                            ${item.tax_rate > 0 ? `<span style="color: #38bdf8;">(ضريبة ${item.tax_rate}%)</span>` : ''}
                        </div>
                    </div>

                    <div class="cart-qty-ctrl">
                        <button type="button" class="cart-qty-btn" onclick="updateQty(${index}, -1)">-</button>
                        <input type="text" class="cart-qty-input" value="${item.is_scale ? item.quantity.toFixed(3) : item.quantity}" onchange="changeQtyManual(${index}, this.value)">
                        <button type="button" class="cart-qty-btn" onclick="updateQty(${index}, 1)">+</button>
                    </div>

                    <div class="cart-item-total">
                        ${(lineSub + lineTax).toFixed(2)}
                    </div>

                    <button type="button" class="cart-del-btn" onclick="removeCartItem(${index})" title="حذف">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });

        container.innerHTML = html;
        updateTotals(subtotal, totalTax, currentDiscount);
    }

    function updateQty(index, delta) {
        if (!cart[index]) return;
        const step = cart[index].is_scale ? 0.25 : 1.0;
        cart[index].quantity += (delta * step);
        if (cart[index].quantity <= 0.001) {
            cart.splice(index, 1);
        }
        renderCart();
    }

    function changeQtyManual(index, val) {
        const qty = parseFloat(val);
        if (isNaN(qty) || qty <= 0) {
            cart.splice(index, 1);
        } else {
            cart[index].quantity = qty;
        }
        renderCart();
    }

    function removeCartItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function clearCart() {
        if (cart.length > 0 && confirm('هل أنت متأكد من تفريغ وإلغاء السلة الحالية؟')) {
            cart = [];
            currentDiscount = 0.0;
            renderCart();
        }
    }

    function updateTotals(subtotal, tax, discount) {
        const grand = Math.max(0, (subtotal + tax) - discount);
        
        document.getElementById('subtotalVal').textContent = subtotal.toFixed(2) + ' ج.م';
        document.getElementById('taxVal').textContent = tax.toFixed(2) + ' ج.م';
        document.getElementById('grandTotalVal').textContent = grand.toFixed(2);

        if (discount > 0) {
            document.getElementById('discountRow').style.display = 'flex';
            document.getElementById('discountVal').textContent = '-' + discount.toFixed(2) + ' ج.م';
        } else {
            document.getElementById('discountRow').style.display = 'none';
        }
    }

    function getGrandTotal() {
        let subtotal = 0, tax = 0;
        cart.forEach(it => {
            const line = it.price * it.quantity;
            subtotal += line;
            tax += line * (it.tax_rate / 100);
        });
        return Math.max(0, (subtotal + tax) - currentDiscount);
    }

    // Quick Cash Checkout (F4)
    async function processQuickCash() {
        if (cart.length === 0) {
            alert('السلة فارغة. يرجى إضافة أصناف أولاً.');
            return;
        }

        const grand = getGrandTotal();
        const payload = {
            customer_id: document.getElementById('customerSelect').value,
            items: cart.map(it => ({ item_id: it.id, quantity: it.quantity, price: it.price })),
            discount_amount: currentDiscount,
            payments: [{ method: 'cash', amount: grand }]
        };

        await executeCheckout(payload);
    }

    // Execute Checkout via API
    async function executeCheckout(payload) {
        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch('<?= BASE_URL ?>api/pos.php?action=checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (data.success) {
                playBeep(1200, 'sine', 0.15);
                cart = [];
                currentDiscount = 0.0;
                renderCart();
                closePaymentModal();

                // Open receipt print window
                window.open(data.receipt_url, '_blank', 'width=450,height=700');
            } else {
                alert(data.message || 'فشلت عملية البيع.');
            }
        } catch (e) {
            alert('حدث خطأ أثناء الاتصال بالنظام: ' + e.message);
        }
    }

    // Multi-Payment Modal (F7)
    function openPaymentModal() {
        if (cart.length === 0) {
            alert('السلة فارغة. يرجى إضافة أصناف أولاً.');
            return;
        }
        const grand = getGrandTotal();
        document.getElementById('modalGrandTotal').textContent = grand.toFixed(2) + ' ج.م';
        document.getElementById('payCash').value = grand.toFixed(2);
        document.getElementById('payCard').value = '';
        document.getElementById('payCardRef').value = '';
        document.getElementById('payInstapay').value = '';
        document.getElementById('payInstapayRef').value = '';
        document.getElementById('payWallet').value = '';
        document.getElementById('payWalletRef').value = '';
        document.getElementById('payCredit').value = '';
        calculatePayments();
        document.getElementById('paymentModal').style.display = 'flex';
        setTimeout(() => document.getElementById('payCash').focus(), 100);
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').style.display = 'none';
        focusBarcode();
    }

    function setCashPreset(val) {
        const grand = getGrandTotal();
        if (val === 'exact') {
            document.getElementById('payCash').value = grand.toFixed(2);
        } else {
            document.getElementById('payCash').value = parseFloat(val).toFixed(2);
        }
        calculatePayments();
    }

    function calculatePayments() {
        const grand = getGrandTotal();
        const cash = parseFloat(document.getElementById('payCash').value) || 0;
        const card = parseFloat(document.getElementById('payCard').value) || 0;
        const instapay = parseFloat(document.getElementById('payInstapay').value) || 0;
        const wallet = parseFloat(document.getElementById('payWallet').value) || 0;
        const credit = parseFloat(document.getElementById('payCredit').value) || 0;

        const totalPaid = cash + card + instapay + wallet + credit;
        document.getElementById('modalTotalPaid').textContent = totalPaid.toFixed(2) + ' ج.م';

        const diff = totalPaid - grand;
        const changeValEl = document.getElementById('modalChangeVal');
        const changeLabelEl = document.getElementById('modalChangeLabel');

        if (diff >= 0) {
            changeLabelEl.textContent = 'المتبقي للعميل (الباقي):';
            changeValEl.textContent = diff.toFixed(2) + ' ج.م';
            changeValEl.style.color = '#10b981';
        } else {
            changeLabelEl.textContent = 'المتبقي مطلوب سداده:';
            changeValEl.textContent = Math.abs(diff).toFixed(2) + ' ج.م';
            changeValEl.style.color = '#f87171';
        }
    }

    async function submitCheckout() {
        const grand = getGrandTotal();
        const cash = parseFloat(document.getElementById('payCash').value) || 0;
        const card = parseFloat(document.getElementById('payCard').value) || 0;
        const instapay = parseFloat(document.getElementById('payInstapay').value) || 0;
        const wallet = parseFloat(document.getElementById('payWallet').value) || 0;
        const credit = parseFloat(document.getElementById('payCredit').value) || 0;

        const totalPaid = cash + card + instapay + wallet + credit;
        if (totalPaid < grand && credit <= 0) {
            alert('المبلغ المدفوع أقل من إجمالي الفاتورة!');
            return;
        }

        const payments = [];
        if (cash > 0) payments.push({ method: 'cash', amount: cash });
        if (card > 0) payments.push({ method: 'card', amount: card, reference_number: document.getElementById('payCardRef').value.trim() });
        if (instapay > 0) payments.push({ method: 'instapay', amount: instapay, reference_number: document.getElementById('payInstapayRef').value.trim() });
        if (wallet > 0) payments.push({ method: 'wallet', amount: wallet, reference_number: document.getElementById('payWalletRef').value.trim() });
        if (credit > 0) payments.push({ method: 'credit', amount: credit });

        const payload = {
            customer_id: document.getElementById('customerSelect').value,
            items: cart.map(it => ({ item_id: it.id, quantity: it.quantity, price: it.price })),
            discount_amount: currentDiscount,
            payments: payments
        };

        await executeCheckout(payload);
    }

    // Category Filter Chips
    function filterCategory(catId, btn) {
        document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');

        const cards = document.querySelectorAll('.product-card');
        cards.forEach(c => {
            if (catId === null) {
                c.style.display = 'flex';
            } else if (catId === 'scale') {
                c.style.display = (c.getAttribute('data-scale') === '1') ? 'flex' : 'none';
            } else {
                c.style.display = (c.getAttribute('data-cat-id') == catId) ? 'flex' : 'none';
            }
        });
    }

    // Hold Cart (F8)
    async function holdCurrentCart() {
        if (cart.length === 0) {
            alert('السلة فارغة، لا يوجد ما يتم تعليقه.');
            return;
        }
        const ref = prompt('أدخل وصفاً أو اسماً للسلة المعلقة (مثال: عميل يشتري خبز):', 'معلقة #' + (new Date()).toLocaleTimeString('ar-EG'));
        if (!ref) return;

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch('<?= BASE_URL ?>api/pos.php?action=hold_cart', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({
                    reference: ref,
                    customer_id: document.getElementById('customerSelect').value,
                    items: cart,
                    total: getGrandTotal()
                })
            });
            const d = await res.json();
            if (d.success) {
                cart = [];
                currentDiscount = 0.0;
                renderCart();
                updateHeldCount();
                alert('تم تعليق الفاتورة بنجاح. يمكنك استرجاعها عبر (F10).');
            }
        } catch (e) {
            alert(e.message);
        }
    }

    async function updateHeldCount() {
        const res = await fetch('<?= BASE_URL ?>api/pos.php?action=get_held_carts');
        const d = await res.json();
        if (d.success && d.held_carts) {
            document.getElementById('heldCount').textContent = d.held_carts.length;
        }
    }

    async function openHeldCartsModal() {
        const res = await fetch('<?= BASE_URL ?>api/pos.php?action=get_held_carts');
        const d = await res.json();
        const container = document.getElementById('heldCartsListContainer');

        if (!d.success || !d.held_carts || d.held_carts.length === 0) {
            container.innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 2rem;">لا توجد فواتير معلقة حالياً.</div>';
        } else {
            let html = '<div style="display: flex; flex-direction: column; gap: 0.75rem;">';
            d.held_carts.forEach(hc => {
                html += `
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 700; color: #fff;">${hc.hold_reference}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                                العميل: ${hc.customer_name || 'نقدي'} | المبلغ: ${parseFloat(hc.total_amount).toFixed(2)} ج.م
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" onclick="resumeHeldCart(${hc.id})">
                            <i class="fas fa-play"></i> استرجاع السلة
                        </button>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        }
        document.getElementById('heldCartsModal').style.display = 'flex';
    }

    function closeHeldCartsModal() {
        document.getElementById('heldCartsModal').style.display = 'none';
        focusBarcode();
    }

    async function resumeHeldCart(holdId) {
        if (cart.length > 0 && !confirm('سيتم استبدال السلة الحالية بالسلة المعلقة. متابعة؟')) {
            return;
        }
        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const fd = new FormData();
            fd.append('hold_id', holdId);
            const res = await fetch('<?= BASE_URL ?>api/pos.php?action=resume_held_cart', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token },
                body: fd
            });
            const d = await res.json();
            if (d.success && d.cart) {
                cart = d.cart.cart_data || [];
                renderCart();
                closeHeldCartsModal();
                updateHeldCount();
            }
        } catch (e) {
            alert(e.message);
        }
    }

    // Prompt Discount (F6)
    function promptDiscount() {
        const val = prompt('أدخل قيمة الخصم بالجنيه المصري (ج.م):', currentDiscount);
        if (val !== null) {
            currentDiscount = Math.max(0, parseFloat(val) || 0);
            renderCart();
        }
    }

    // Open Shift Submission
    async function submitOpenShift(e) {
        e.preventDefault();
        const balance = document.getElementById('openingBalanceInput').value;
        const storeId = document.getElementById('shiftStoreSelect').value;
        const notes = document.getElementById('shiftOpenNotes').value;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const fd = new FormData();
        fd.append('opening_balance', balance);
        fd.append('store_id', storeId);
        fd.append('notes', notes);

        const res = await fetch('<?= BASE_URL ?>api/pos.php?action=open_shift', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            body: fd
        });
        const d = await res.json();
        if (d.success) {
            window.location.reload();
        } else {
            alert(d.message);
        }
    }

    // Close Shift Modal
    async function openCloseShiftModal() {
        document.getElementById('closeShiftModal').style.display = 'flex';
        document.getElementById('shiftSummaryLoading').style.display = 'block';
        document.getElementById('closeShiftForm').style.display = 'none';

        try {
            const res = await fetch('<?= BASE_URL ?>api/pos.php?action=get_shift_status');
            const d = await res.json();
            if (d.success && d.has_shift) {
                document.getElementById('sumOpenCash').textContent = parseFloat(d.shift.opening_balance).toFixed(2) + ' ج.م';
                document.getElementById('sumSalesCash').textContent = parseFloat(d.shift.total_sales_cash).toFixed(2) + ' ج.م';
                document.getElementById('sumExpectedCash').textContent = parseFloat(d.expected_cash).toFixed(2) + ' ج.م';
                expectedCashInDrawer = parseFloat(d.expected_cash);
                document.getElementById('closingShiftId').value = d.shift.id;

                document.getElementById('shiftSummaryLoading').style.display = 'none';
                document.getElementById('closeShiftForm').style.display = 'block';
                document.getElementById('actualCashInput').focus();
            }
        } catch (e) {
            alert(e.message);
        }
    }

    function closeCloseShiftModal() {
        document.getElementById('closeShiftModal').style.display = 'none';
        focusBarcode();
    }

    function calculateShiftDiff() {
        const actual = parseFloat(document.getElementById('actualCashInput').value) || 0;
        const diff = actual - expectedCashInDrawer;
        const el = document.getElementById('shiftDiffVal');
        el.textContent = (diff >= 0 ? '+' : '') + diff.toFixed(2) + ' ج.م';
        el.style.color = (Math.abs(diff) < 0.01) ? '#10b981' : (diff > 0 ? '#38bdf8' : '#f87171');
    }

    async function submitCloseShift(e) {
        e.preventDefault();
        const shiftId = document.getElementById('closingShiftId').value;
        const actual = document.getElementById('actualCashInput').value;
        const notes = document.getElementById('shiftCloseNotes').value;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const fd = new FormData();
        fd.append('shift_id', shiftId);
        fd.append('actual_cash', actual);
        fd.append('notes', notes);

        const res = await fetch('<?= BASE_URL ?>api/pos.php?action=close_shift', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            body: fd
        });
        const d = await res.json();
        if (d.success) {
            alert('تم إغلاق الوردية وجرد النقدية بنجاح.');
            window.location.href = '<?= BASE_URL ?>index.php';
        } else {
            alert(d.message);
        }
    }

    // Quick Customer Modal
    function openNewCustomerModal() {
        document.getElementById('quickCustForm').reset();
        document.getElementById('quickCustomerModal').style.display = 'flex';
        document.getElementById('newCustName').focus();
    }
    function closeNewCustomerModal() {
        document.getElementById('quickCustomerModal').style.display = 'none';
        focusBarcode();
    }
    async function submitQuickCustomer(e) {
        e.preventDefault();
        const name = document.getElementById('newCustName').value.trim();
        const phone = document.getElementById('newCustPhone').value.trim();
        const credit = document.getElementById('newCustCredit').value;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const fd = new FormData();
        fd.append('name', name);
        fd.append('phone', phone);
        fd.append('credit_limit', credit);

        const res = await fetch('<?= BASE_URL ?>api/pos.php?action=quick_customer', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            body: fd
        });
        const d = await res.json();
        if (d.success && d.customer) {
            const select = document.getElementById('customerSelect');
            const opt = document.createElement('option');
            opt.value = d.customer.id;
            opt.textContent = d.customer.name;
            opt.selected = true;
            select.appendChild(opt);
            closeNewCustomerModal();
        } else {
            alert(d.message);
        }
    }

    // Camera Barcode Scanner
    let cameraStream = null;
    let cameraInterval = null;

    async function openCameraScannerModal() {
        document.getElementById('cameraScannerModal').style.display = 'flex';
        const video = document.getElementById('cameraVideo');
        const statusText = document.getElementById('cameraStatusText');

        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }
            });
            video.srcObject = cameraStream;
            video.play();
            statusText.textContent = 'الكاميرا جاهزة، وجّه الباركود داخل الإطار...';

            if ('BarcodeDetector' in window) {
                const detector = new BarcodeDetector();
                cameraInterval = setInterval(async () => {
                    try {
                        const barcodes = await detector.detect(video);
                        if (barcodes.length > 0) {
                            const code = barcodes[0].rawValue;
                            closeCameraScannerModal();
                            await handleBarcodeScan(code);
                        }
                    } catch (err) {}
                }, 300);
            } else {
                statusText.textContent = 'متصفحك لا يدعم BarcodeDetector المباشر. استخدم ماسح الباركود اليدوي.';
            }
        } catch (e) {
            statusText.textContent = 'تعذر تشغيل الكاميرا: ' + e.message;
        }
    }

    function closeCameraScannerModal() {
        if (cameraInterval) clearInterval(cameraInterval);
        if (cameraStream) {
            cameraStream.getTracks().forEach(t => t.stop());
            cameraStream = null;
        }
        document.getElementById('cameraScannerModal').style.display = 'none';
        focusBarcode();
    }

    // Hotkeys Guide Modal (F1)
    function openHotkeysModal() {
        document.getElementById('hotkeysModal').style.display = 'flex';
    }
    function closeHotkeysModal() {
        document.getElementById('hotkeysModal').style.display = 'none';
        focusBarcode();
    }

    // Global Keydown Handler (F1 - F10, Esc)
    window.addEventListener('keydown', function(e) {
        // F1: Help / Hotkeys
        if (e.key === 'F1') {
            e.preventDefault();
            openHotkeysModal();
            return;
        }
        // F2: Focus Barcode
        if (e.key === 'F2') {
            e.preventDefault();
            focusBarcode();
            return;
        }
        // F4: Quick Cash
        if (e.key === 'F4') {
            e.preventDefault();
            processQuickCash();
            return;
        }
        // F6: Discount
        if (e.key === 'F6') {
            e.preventDefault();
            promptDiscount();
            return;
        }
        // F7: Multi-Payment Modal
        if (e.key === 'F7') {
            e.preventDefault();
            openPaymentModal();
            return;
        }
        // F8: Hold Cart
        if (e.key === 'F8') {
            e.preventDefault();
            holdCurrentCart();
            return;
        }
        // F10: Held Carts Modal
        if (e.key === 'F10') {
            e.preventDefault();
            openHeldCartsModal();
            return;
        }
        // Esc: Clear / Close Modals
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal-backdrop');
            let closedAny = false;
            modals.forEach(m => {
                if (m.style.display === 'flex' && m.id !== 'openShiftModal') {
                    m.style.display = 'none';
                    closedAny = true;
                }
            });
            if (!closedAny && cart.length > 0) {
                clearCart();
            }
            focusBarcode();
        }
    });

    function focusBarcode() {
        const inp = document.getElementById('barcodeInput');
        if (inp) {
            inp.focus();
            inp.select();
        }
    }

    // Initial autofocus
    window.addEventListener('DOMContentLoaded', () => {
        renderCart();
        focusBarcode();
    });
    </script>
</body>
</html>
