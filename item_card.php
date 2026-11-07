<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

// Resolve item ID
$itemId = 0;
if (isset($_GET['id']) && (int)$_GET['id'] > 0) {
    $itemId = (int)$_GET['id'];
} elseif (isset($_GET['sd'])) {
    $rawThread = $adController->encrypt_decrypt(2, urldecode($_GET['sd']), 0);
    $rawThread = $adController->encrypt_decrypt(2, $rawThread, 0);
    $safeThread = mysqli_real_escape_string($adController->MySQL, $rawThread);
    $resFind = mysqli_query($adController->MySQL, "SELECT id FROM items WHERE item_thread='$safeThread' LIMIT 1");
    if ($resFind && $rowFind = mysqli_fetch_assoc($resFind)) {
        $itemId = (int)$rowFind['id'];
    }
}

if ($itemId <= 0) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h3>عفواً، لم يتم تحديد المنتج المطلوب.</h3><a href='items.php'>العودة لقائمة المنتجات</a></div>");
}

// Fetch item details
$itemQuery = "SELECT 
                i.*,
                c.name_ar AS cat_name_ar,
                c.name_en AS cat_name_en,
                u.name_ar AS unit_name_ar,
                u.name_en AS unit_name_en
              FROM items i
              LEFT JOIN categories c ON i.catid = c.id
              LEFT JOIN units u ON i.unit = u.id
              WHERE i.id = '$itemId' AND (i.storeid = '$storeid' OR i.storeid = '0')
              LIMIT 1";

$itemRes = mysqli_query($adController->MySQL, $itemQuery);
$item = mysqli_fetch_assoc($itemRes);

if (!$item) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h3>عفواً، المنتج غير موجود أو تم حذفه.</h3><a href='items.php'>العودة لقائمة المنتجات</a></div>");
}

$itemName = !empty($item['name_ar']) ? $item['name_ar'] : $item['name_en'];
$catName  = !empty($item['cat_name_ar']) ? $item['cat_name_ar'] : ($item['cat_name_en'] ?? 'عام');
$unitName = !empty($item['unit_name_ar']) ? $item['unit_name_ar'] : ($item['unit_name_en'] ?? 'قطعة');
$barcode  = !empty($item['barcode']) ? $item['barcode'] : ('WAM-' . str_pad($item['id'], 6, '0', STR_PAD_LEFT));
$price    = (float)$item['price'];
$vatRate  = (float)$item['vat'];
$taxInPrice = (int)$item['taxInPrice'];
$reorderLevel = (float)$item['selt'];
if ($reorderLevel <= 0) $reorderLevel = 5.0;

// Fetch latest purchase cost
$costQuery = "SELECT cost, date FROM income_items WHERE itemid = '$itemId' AND state = '1' ORDER BY id DESC LIMIT 1";
$costRes = mysqli_query($adController->MySQL, $costQuery);
$latestCost = 0.0;
if ($costRes && $costRow = mysqli_fetch_assoc($costRes)) {
    $latestCost = (float)$costRow['cost'];
}

// Calculate profit margin
$profitMargin = $price - $latestCost;
$profitMarginPercent = ($price > 0) ? (($profitMargin / $price) * 100) : 0;

// Calculate total current stock and stock per store
$storeStockQuery = "SELECT 
                        s.id AS store_id,
                        s.name_ar AS store_name_ar,
                        s.name_en AS store_name_en,
                        b.name_ar AS branch_name_ar,
                        (
                            COALESCE((SELECT SUM(o.quantity) FROM open_palance o WHERE o.itemid = '$itemId' AND o.store = s.id), 0) +
                            COALESCE((SELECT SUM(inc.quantity) FROM income_items inc WHERE inc.itemid = '$itemId' AND inc.store = s.id AND inc.state = '1'), 0) -
                            COALESCE((SELECT SUM(outg.quantity) FROM outgo_items outg WHERE outg.itemid = '$itemId' AND outg.store = s.id AND outg.state = '1'), 0)
                        ) AS stock_qty
                    FROM stores s
                    LEFT JOIN branches b ON s.branch = b.id
                    WHERE s.storeid = '$storeid' OR s.storeid = '0'";

$storeStockRes = mysqli_query($adController->MySQL, $storeStockQuery);
$storeStocks = [];
$totalStock = 0.0;

if ($storeStockRes) {
    while ($sRow = mysqli_fetch_assoc($storeStockRes)) {
        $qty = (float)$sRow['stock_qty'];
        $totalStock += $qty;
        $storeStocks[] = [
            'store_name' => !empty($sRow['store_name_ar']) ? $sRow['store_name_ar'] : $sRow['store_name_en'],
            'branch_name' => !empty($sRow['branch_name_ar']) ? $sRow['branch_name_ar'] : 'الفرع الرئيسي',
            'qty' => $qty
        ];
    }
}

// Expiry analysis
$expiryStatusText = 'غير محدد';
$expiryBadgeClass = 'badge-pro-secondary';
if (!empty($item['expiry_date']) && $item['expiry_date'] !== '0000-00-00') {
    try {
        $today = new DateTime();
        $expDate = new DateTime($item['expiry_date']);
        $diff = $today->diff($expDate);
        if ($diff->invert === 1 && (int)$diff->days > 0) {
            $expiryStatusText = 'منتهي الصلاحية منذ ' . $diff->days . ' يوم';
            $expiryBadgeClass = 'badge-pro-danger';
        } elseif ((int)$diff->days <= 60) {
            $expiryStatusText = 'قريب الانتهاء (متبقي ' . $diff->days . ' يوم)';
            $expiryBadgeClass = 'badge-pro-warning';
        } else {
            $expiryStatusText = 'ساري الصلاحية (متبقي ' . $diff->days . ' يوم)';
            $expiryBadgeClass = 'badge-pro-success';
        }
    } catch (\Throwable $t) {}
}

// Recent movement history (Last 10 transactions)
$movementQuery = "(
    SELECT 'income' AS m_type, invoice_Type AS reason, invoice_No AS doc_no, quantity, cost AS unit_price, date, store 
    FROM income_items 
    WHERE itemid = '$itemId' AND state = '1'
) UNION ALL (
    SELECT 'outgo' AS m_type, invoice_Type AS reason, invoice_No AS doc_no, quantity, cost AS unit_price, date, store 
    FROM outgo_items 
    WHERE itemid = '$itemId' AND state = '1'
) ORDER BY date DESC LIMIT 10";

$moveRes = mysqli_query($adController->MySQL, $movementQuery);
$movements = [];
if ($moveRes) {
    while ($mRow = mysqli_fetch_assoc($moveRes)) {
        $movements[] = $mRow;
    }
}

// Encrypted keys for edit/barcode links
$idval = urlencode($adController->encrypt_decrypt(1, $item['item_thread'], 0));
$secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>بطاقة الصنف - <?= htmlspecialchars($itemName) ?></title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        .product-hero-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .barcode-sticker {
            background: #ffffff;
            color: #000000;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            max-width: 320px;
            margin: 0 auto;
        }
        .sticker-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sticker-price {
            font-size: 18px;
            font-weight: 800;
            color: #047857;
            margin-top: 4px;
        }
        @media print {
            body {
                background: #fff !important;
            }
            .admin-sidebar, .admin-navbar, .page-header, .no-print, .btn-pro {
                display: none !important;
            }
            .admin-content {
                padding: 0 !important;
            }
            .print-only-sticker {
                display: block !important;
                margin: 0 auto;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="admin-wrapper">

        <!-- Professional Sidebar -->
        <?php require_once("components/sidebar_pro.php"); ?>

        <!-- Main Content Wrapper -->
        <div style="flex: 1; display: flex; flex-direction: column;">

            <!-- Professional Navbar -->
            <?php require_once("components/header_pro.php"); ?>

            <!-- Main Content -->
            <main class="admin-content">

                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-id-card text-primary me-2"></i>
                        بطاقة تعريف الصنف
                    </h1>
                    <div class="page-breadcrumb">
                        <div class="breadcrumb-item">
                            <i class="fas fa-home"></i>
                            <span>الرئيسية</span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <a href="items.php" class="text-decoration-none"><?= ITEMS ?></a>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span class="text-primary"><?= htmlspecialchars($itemName) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Product Hero Banner -->
                <div class="product-hero-card">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <?php if (!empty($item['image']) && file_exists($item['image'])): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="Product" class="rounded shadow" style="width: 110px; height: 110px; object-fit: cover; border: 3px solid rgba(255,255,255,0.2);">
                            <?php else: ?>
                                <div class="rounded d-inline-flex align-items-center justify-content-center shadow" style="width: 110px; height: 110px; background: rgba(255,255,255,0.1); border: 2px dashed rgba(255,255,255,0.3);">
                                    <i class="fas fa-box-open fa-3x text-light"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary px-3 py-1"><?= htmlspecialchars($catName) ?></span>
                                <span class="badge bg-secondary px-2 py-1"><?= htmlspecialchars($unitName) ?></span>
                                <span class="badge <?= ($item['enabled'] == '1') ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ($item['enabled'] == '1') ? 'نشط ومتاح للبيع' : 'معطل' ?>
                                </span>
                            </div>
                            <h2 class="h3 fw-bold mb-1 text-white"><?= htmlspecialchars($itemName) ?></h2>
                            <p class="text-white-50 small mb-2">
                                كود الصنف: <span class="font-monospace text-white">#ITEM-<?= $item['id'] ?></span> &nbsp;|&nbsp; 
                                الباركود: <span class="font-monospace text-white"><?= htmlspecialchars($barcode) ?></span>
                            </p>
                            <?php if (!empty($item['disc'])): ?>
                                <p class="small text-light mb-0" style="opacity: 0.85;">
                                    <?= nl2br(htmlspecialchars($item['disc'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3 text-md-end text-center mt-3 mt-md-0 no-print">
                            <div class="d-flex flex-column gap-2">
                                <a href="edit_item.php?sd=<?= $secondIdval ?>" class="btn-pro btn-pro-outline text-white border-white">
                                    <i class="fas fa-edit me-1"></i> تعديل بيانات الصنف
                                </a>
                                <a href="add_purchase.php" class="btn-pro btn-pro-success">
                                    <i class="fas fa-cart-plus me-1"></i> توريد كمية جديدة
                                </a>
                                <button onclick="window.print()" class="btn-pro btn-pro-primary">
                                    <i class="fas fa-print me-1"></i> طباعة الملصق / البطاقة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial & Inventory Overview KPI -->
                <div class="row g-3 mb-4">
                    <!-- Current Stock -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card <?= ($totalStock <= 0) ? 'border-danger' : (($totalStock <= $reorderLevel) ? 'warning' : 'success') ?>">
                            <i class="fas fa-boxes stat-card-icon"></i>
                            <div class="stat-card-value <?= ($totalStock <= 0) ? 'text-danger' : '' ?>">
                                <?= $totalStock ?> <span class="fs-6 fw-normal"><?= htmlspecialchars($unitName) ?></span>
                            </div>
                            <div class="stat-card-label">إجمالي الرصيد بالمخازن</div>
                            <div class="mt-2 small text-muted">
                                حد إعادة الطلب: <strong><?= $reorderLevel ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Selling Price -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <i class="fas fa-tag stat-card-icon text-primary"></i>
                            <div class="stat-card-value text-primary">
                                <?= number_format($price, 2) ?> <span class="fs-6 fw-normal"><?= $currency ?></span>
                            </div>
                            <div class="stat-card-label">سعر البيع للجمهور</div>
                            <div class="mt-2 small text-muted">
                                ضريبة القيمة المضافة: <strong><?= $vatRate ?>%</strong> (<?= ($taxInPrice == 1) ? 'شامل' : 'غير شامل' ?>)
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Cost -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card info">
                            <i class="fas fa-coins stat-card-icon text-info"></i>
                            <div class="stat-card-value text-info">
                                <?= number_format($latestCost, 2) ?> <span class="fs-6 fw-normal"><?= $currency ?></span>
                            </div>
                            <div class="stat-card-label">آخر سعر تكلفة شراء</div>
                            <div class="mt-2 small text-muted">
                                حسب أحدث فاتورة توريد
                            </div>
                        </div>
                    </div>

                    <!-- Profit Margin -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card success">
                            <i class="fas fa-chart-line stat-card-icon text-success"></i>
                            <div class="stat-card-value text-success">
                                <?= number_format($profitMargin, 2) ?> <span class="fs-6 fw-normal"><?= $currency ?></span>
                            </div>
                            <div class="stat-card-label">هامش الربح المتوقع</div>
                            <div class="mt-2 small text-success fw-semibold">
                                نسبة ربحية: +<?= number_format($profitMarginPercent, 1) ?>%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Left Column: Barcode & QR Sticker -->
                    <div class="col-lg-4">
                        <div class="card-pro mb-4">
                            <div class="card-header-pro">
                                <h3 class="card-title-pro">
                                    <i class="fas fa-barcode text-primary"></i>
                                    ملصق الباركود و QR Code
                                </h3>
                            </div>
                            <div class="card-body-pro text-center">
                                <div class="barcode-sticker shadow-sm mb-3">
                                    <div class="sticker-title"><?= htmlspecialchars($itemName) ?></div>
                                    <div class="text-muted small mb-2"><?= htmlspecialchars($catName) ?></div>
                                    
                                    <!-- Barcode SVG Rendered by JsBarcode -->
                                    <svg id="barcodeCanvas" class="w-100" style="max-height: 80px;"></svg>

                                    <!-- QR Code Canvas Rendered by qrcode.js -->
                                    <div id="qrcodeCanvas" class="d-flex justify-content-center my-3"></div>

                                    <div class="sticker-price">
                                        <?= number_format($price, 2) ?> <?= $currency ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 11px;">شامل ضريبة القيمة المضافة</div>
                                </div>

                                <div class="d-flex gap-2 justify-content-center no-print">
                                    <button onclick="printSticker()" class="btn-pro btn-pro-primary btn-pro-sm">
                                        <i class="fas fa-print me-1"></i> طباعة الملصق فقط
                                    </button>
                                    <a href="barcode_item.php?sd=<?= $secondIdval ?>" class="btn-pro btn-pro-outline btn-pro-sm">
                                        <i class="fas fa-copy me-1"></i> طباعة كميات متعددة
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Expiry & Specifications Card -->
                        <div class="card-pro">
                            <div class="card-header-pro">
                                <h3 class="card-title-pro">
                                    <i class="fas fa-info-circle text-info"></i>
                                    الصلاحية والمواصفات
                                </h3>
                            </div>
                            <div class="card-body-pro p-0">
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                        <span class="text-muted">تاريخ انتهاء الصلاحية:</span>
                                        <strong><?= !empty($item['expiry_date']) && $item['expiry_date'] !== '0000-00-00' ? htmlspecialchars($item['expiry_date']) : 'غير محدد' ?></strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                        <span class="text-muted">حالة الصلاحية:</span>
                                        <span class="badge-pro <?= $expiryBadgeClass ?>"><?= $expiryStatusText ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                        <span class="text-muted">تنبيه انتهاء الصلاحية:</span>
                                        <span><?= ($item['expiry_notification'] == '1') ? 'مفعّل' : 'معطل' ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                        <span class="text-muted">حد إعادة الطلب (Alert):</span>
                                        <span class="badge bg-warning text-dark px-2"><?= $reorderLevel ?> وحدات</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                        <span class="text-muted">سعة العبوة / الشد:</span>
                                        <span><?= (float)$item['intimate_stock'] ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                        <span class="text-muted">الخصم المتاح:</span>
                                        <span><?= (float)$item['discount'] ?>%</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Stock per Store & Movement History -->
                    <div class="col-lg-8">
                        <!-- Stock per store table -->
                        <div class="card-pro mb-4">
                            <div class="card-header-pro">
                                <h3 class="card-title-pro">
                                    <i class="fas fa-warehouse text-warning"></i>
                                    توزيع المخزون عبر الفروع والمستودعات
                                </h3>
                            </div>
                            <div class="card-body-pro p-0">
                                <div class="table-responsive">
                                    <table class="table-pro mb-0">
                                        <thead>
                                            <tr>
                                                <th>المستودع / المخزن</th>
                                                <th>الفرع التابع له</th>
                                                <th class="text-center">الرصيد المتاح</th>
                                                <th class="text-center">الحالة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($storeStocks)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted">لا توجد بيانات مخازن مسجلة.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($storeStocks as $stk): ?>
                                                    <tr>
                                                        <td class="fw-bold"><?= htmlspecialchars($stk['store_name']) ?></td>
                                                        <td class="text-muted small"><?= htmlspecialchars($stk['branch_name']) ?></td>
                                                        <td class="text-center">
                                                            <strong class="fs-6"><?= $stk['qty'] ?></strong> <?= htmlspecialchars($unitName) ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($stk['qty'] <= 0): ?>
                                                                <span class="badge-pro badge-pro-danger">منعدم</span>
                                                            <?php elseif ($stk['qty'] <= $reorderLevel): ?>
                                                                <span class="badge-pro badge-pro-warning">منخفض</span>
                                                            <?php else: ?>
                                                                <span class="badge-pro badge-pro-success">متوفر</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Movement History Table -->
                        <div class="card-pro">
                            <div class="card-header-pro">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title-pro">
                                        <i class="fas fa-history text-secondary"></i>
                                        سجل الحركات الأخيرة لهذا الصنف
                                    </h3>
                                    <a href="item-movement.php" class="btn-pro btn-pro-outline btn-pro-sm no-print">
                                        تقرير الحركة الكامل
                                    </a>
                                </div>
                            </div>
                            <div class="card-body-pro p-0">
                                <div class="table-responsive">
                                    <table class="table-pro mb-0">
                                        <thead>
                                            <tr>
                                                <th>التاريخ والوقت</th>
                                                <th>نوع الحركة</th>
                                                <th>رقم الفاتورة / المستند</th>
                                                <th class="text-center">الكمية</th>
                                                <th class="text-center">السعر</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($movements)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                        <i class="fas fa-inbox fa-2x mb-2 d-block text-muted"></i>
                                                        لا توجد حركات مسجلة مؤخراً لهذا الصنف
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($movements as $mv): ?>
                                                    <tr>
                                                        <td class="small text-muted font-monospace"><?= htmlspecialchars($mv['date']) ?></td>
                                                        <td>
                                                            <?php if ($mv['m_type'] === 'income'): ?>
                                                                <span class="badge-pro badge-pro-success">
                                                                    <i class="fas fa-arrow-down me-1"></i> توريد / شراء
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge-pro badge-pro-primary">
                                                                    <i class="fas fa-arrow-up me-1"></i> منصرف / بيع
                                                                </span>
                                                            <?php endif; ?>
                                                            <span class="text-muted small ms-1">(<?= htmlspecialchars($mv['reason']) ?>)</span>
                                                        </td>
                                                        <td>
                                                            <span class="font-monospace fw-semibold">#<?= htmlspecialchars($mv['doc_no']) ?></span>
                                                        </td>
                                                        <td class="text-center fw-bold <?= ($mv['m_type'] === 'income') ? 'text-success' : 'text-danger' ?>">
                                                            <?= ($mv['m_type'] === 'income' ? '+' : '-') . (float)$mv['quantity'] ?>
                                                        </td>
                                                        <td class="text-center text-muted small">
                                                            <?= number_format((float)$mv['unit_price'], 2) ?> <?= $currency ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Professional Footer -->
                <?php require_once("components/footer_pro.php"); ?>

            </main>

        </div>

    </div>

    <!-- Scripts -->
    <?php require_once("include.php"); ?>
    <script src="js/admin-pro.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Generate Code128 Barcode
            const barcodeVal = "<?= addslashes($barcode) ?>";
            try {
                JsBarcode("#barcodeCanvas", barcodeVal, {
                    format: "CODE128",
                    lineColor: "#000",
                    width: 2,
                    height: 50,
                    displayValue: true,
                    fontSize: 14,
                    textMargin: 2
                });
            } catch (e) {
                console.error("Barcode generation error:", e);
            }

            // Generate QR Code containing product identification
            const qrText = JSON.stringify({
                id: <?= (int)$item['id'] ?>,
                name: "<?= addslashes($itemName) ?>",
                price: <?= (float)$price ?>,
                barcode: barcodeVal,
                currency: "<?= $currency ?>"
            });

            try {
                new QRCode(document.getElementById("qrcodeCanvas"), {
                    text: qrText,
                    width: 110,
                    height: 110,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.M
                });
            } catch (e) {
                console.error("QR Code generation error:", e);
            }
        });

        function printSticker() {
            window.print();
        }
    </script>
</body>

</html>

