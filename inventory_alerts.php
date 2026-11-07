<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

// Filter parameters
$filterType = trim($_GET['type'] ?? 'all');
$filterCat  = (int)($_GET['catid'] ?? 0);
$searchQ    = trim($_GET['q'] ?? '');

// Base query to fetch items with their calculated stock balance
$catCondition = ($filterCat > 0) ? " AND i.catid = '$filterCat' " : "";
$searchCondition = "";
if ($searchQ !== '') {
    $safeQ = mysqli_real_escape_string($adController->MySQL, $searchQ);
    $searchCondition = " AND (i.name_ar LIKE '%$safeQ%' OR i.name_en LIKE '%$safeQ%' OR i.barcode LIKE '%$safeQ%') ";
}

$query = "SELECT 
            i.id,
            i.name_ar,
            i.name_en,
            i.barcode,
            i.image,
            i.selt,
            i.intimate_stock,
            i.price,
            i.item_thread,
            i.expiry_date,
            i.expiry_notification,
            c.name_ar AS cat_name_ar,
            c.name_en AS cat_name_en,
            u.name_ar AS unit_name_ar,
            u.name_en AS unit_name_en,
            (
                COALESCE((SELECT SUM(o.quantity) FROM open_palance o WHERE o.itemid = i.id), 0) +
                COALESCE((SELECT SUM(inc.quantity) FROM income_items inc WHERE inc.itemid = i.id AND inc.state = '1'), 0) -
                COALESCE((SELECT SUM(outg.quantity) FROM outgo_items outg WHERE outg.itemid = i.id AND outg.state = '1'), 0)
            ) AS current_stock
          FROM items i
          LEFT JOIN categories c ON i.catid = c.id
          LEFT JOIN units u ON i.unit = u.id
          WHERE (i.storeid = '$storeid' OR i.storeid = '0') 
            AND i.enabled = '1'
            $catCondition
            $searchCondition
          ORDER BY i.name_ar ASC";

$res = mysqli_query($adController->MySQL, $query);

$outOfStockItems = [];
$lowStockItems   = [];
$nearExpiryItems = [];
$expiredItems    = [];
$allAlertItems   = [];

$today = new DateTime();

if ($res) {
    while ($item = mysqli_fetch_assoc($res)) {
        $stock = (float)$item['current_stock'];
        $reorderLevel = (float)$item['selt'];
        if ($reorderLevel <= 0) {
            $reorderLevel = 5.0; // Standard default minimum stock threshold
        }

        $isOutOfStock = ($stock <= 0);
        $isLowStock   = (!$isOutOfStock && $stock <= $reorderLevel);

        $isExpired    = false;
        $isNearExpiry = false;
        $daysUntilExpiry = null;

        if (!empty($item['expiry_date']) && $item['expiry_date'] !== '0000-00-00') {
            try {
                $expDate = new DateTime($item['expiry_date']);
                $diff = $today->diff($expDate);
                $invert = $diff->invert; // 1 if past date
                $days = (int)$diff->days;

                if ($invert === 1 && $days > 0) {
                    $isExpired = true;
                    $daysUntilExpiry = -$days;
                } else {
                    $daysUntilExpiry = $days;
                    if ($days <= 60) {
                        $isNearExpiry = true;
                    }
                }
            } catch (Exception $e) {
                // Ignore parse errors
            }
        }

        $item['calc_reorder']       = $reorderLevel;
        $item['is_out_of_stock']    = $isOutOfStock;
        $item['is_low_stock']       = $isLowStock;
        $item['is_near_expiry']     = $isNearExpiry;
        $item['is_expired']         = $isExpired;
        $item['days_until_expiry']  = $daysUntilExpiry;

        if ($isOutOfStock) {
            $outOfStockItems[] = $item;
        }
        if ($isLowStock) {
            $lowStockItems[] = $item;
        }
        if ($isNearExpiry) {
            $nearExpiryItems[] = $item;
        }
        if ($isExpired) {
            $expiredItems[] = $item;
        }

        if ($isOutOfStock || $isLowStock || $isNearExpiry || $isExpired) {
            $allAlertItems[] = $item;
        }
    }
}

// Determine which list to display
switch ($filterType) {
    case 'out_of_stock':
        $displayItems = $outOfStockItems;
        $pageSectionTitle = "المنتجات التي نفدت كميتها تماماً (Out of Stock)";
        break;
    case 'low_stock':
        $displayItems = $lowStockItems;
        $pageSectionTitle = "المنتجات التي قاربت على النفاد (Low Stock / حد إعادة الطلب)";
        break;
    case 'near_expiry':
        $displayItems = $nearExpiryItems;
        $pageSectionTitle = "المنتجات القريبة من انتهاء الصلاحية (خلال 60 يوماً)";
        break;
    case 'expired':
        $displayItems = $expiredItems;
        $pageSectionTitle = "المنتجات المنتهية الصلاحية (Expired)";
        break;
    default:
        $displayItems = $allAlertItems;
        $pageSectionTitle = "كافة تنبيهات المخزون والصلاحية النشطة";
        break;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WAM Tech POS - إدارة تنبيهات المخزون والصلاحية</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .badge-out-of-stock {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #f87171;
            font-weight: 600;
        }
        .badge-low-stock {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fcd34d;
            font-weight: 600;
        }
        .badge-near-expiry {
            background-color: #ffedd5;
            color: #c2410c;
            border: 1px solid #fdba74;
            font-weight: 600;
        }
        .badge-expired {
            background-color: #450a0a;
            color: #ffffff;
            font-weight: 600;
        }
        .filter-tab-btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .filter-tab-btn.active {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 2px 4px rgba(37,99,235,0.3);
        }
        @media print {
            .admin-sidebar, .admin-navbar, .page-header, .filter-section, .no-print, .btn-pro {
                display: none !important;
            }
            .admin-content {
                padding: 0 !important;
            }
            .card-pro {
                box-shadow: none !important;
                border: none !important;
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
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        نظام إدارة تنبيهات المخزون والصلاحية
                    </h1>
                    <div class="page-breadcrumb">
                        <div class="breadcrumb-item">
                            <i class="fas fa-home"></i>
                            <span>الرئيسية</span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span><?= STORED ?></span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span class="text-primary">التنبيهات الذكية</span>
                        </div>
                    </div>
                </div>

                <!-- KPI Summary Cards -->
                <div class="row g-3 mb-4">
                    <!-- Total Alerts -->
                    <div class="col-md-3 col-sm-6">
                        <a href="inventory_alerts.php?type=all" style="text-decoration: none; color: inherit;">
                            <div class="stat-card <?= ($filterType === 'all') ? 'border-primary' : '' ?>">
                                <i class="fas fa-bell stat-card-icon text-primary"></i>
                                <div class="stat-card-value text-primary"><?= count($allAlertItems) ?></div>
                                <div class="stat-card-label">إجمالي التنبيهات النشطة</div>
                                <div class="mt-2 small text-muted">
                                    تحتاج لمتابعة فورية
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Out of stock -->
                    <div class="col-md-3 col-sm-6">
                        <a href="inventory_alerts.php?type=out_of_stock" style="text-decoration: none; color: inherit;">
                            <div class="stat-card <?= ($filterType === 'out_of_stock') ? 'border-danger' : '' ?>">
                                <i class="fas fa-times-circle stat-card-icon text-danger"></i>
                                <div class="stat-card-value text-danger"><?= count($outOfStockItems) ?></div>
                                <div class="stat-card-label">نفاد الكمية تماماً (0)</div>
                                <div class="mt-2 small text-danger">
                                    <i class="fas fa-arrow-down me-1"></i> توقف المبيعات
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Low Stock -->
                    <div class="col-md-3 col-sm-6">
                        <a href="inventory_alerts.php?type=low_stock" style="text-decoration: none; color: inherit;">
                            <div class="stat-card warning <?= ($filterType === 'low_stock') ? 'border-warning' : '' ?>">
                                <i class="fas fa-exclamation-circle stat-card-icon text-warning"></i>
                                <div class="stat-card-value text-warning"><?= count($lowStockItems) ?></div>
                                <div class="stat-card-label">مخزون منخفض / حد الطلب</div>
                                <div class="mt-2 small text-warning">
                                    <i class="fas fa-truck-loading me-1"></i> بانتظار إعادة طلب
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Near Expiry / Expired -->
                    <div class="col-md-3 col-sm-6">
                        <a href="inventory_alerts.php?type=near_expiry" style="text-decoration: none; color: inherit;">
                            <div class="stat-card info <?= ($filterType === 'near_expiry' || $filterType === 'expired') ? 'border-info' : '' ?>">
                                <i class="fas fa-hourglass-half stat-card-icon text-info"></i>
                                <div class="stat-card-value text-info"><?= count($nearExpiryItems) + count($expiredItems) ?></div>
                                <div class="stat-card-label">قرب انتهاء / منتهية الصلاحية</div>
                                <div class="mt-2 small text-muted">
                                    <?= count($expiredItems) ?> منتهي | <?= count($nearExpiryItems) ?> قريباً
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Main Content Card -->
                <div class="card-pro">
                    <div class="card-header-pro">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h2 class="card-title-pro mb-0">
                                <i class="fas fa-list-alt text-primary"></i>
                                <?= htmlspecialchars($pageSectionTitle) ?>
                                <span class="badge-pro badge-pro-primary ms-2"><?= count($displayItems) ?></span>
                            </h2>
                            <div class="d-flex gap-2 no-print">
                                <button onclick="window.print()" class="btn-pro btn-pro-outline btn-pro-sm" title="طباعة التقرير">
                                    <i class="fas fa-print me-1"></i> طباعة
                                </button>
                                <button onclick="exportTableToExcel('alertsTable', 'inventory_alerts_report')" class="btn-pro btn-pro-success btn-pro-sm" title="تصدير إلى Excel">
                                    <i class="fas fa-file-excel me-1"></i> تصدير Excel
                                </button>
                                <a href="add_purchase.php" class="btn-pro btn-pro-primary btn-pro-sm">
                                    <i class="fas fa-plus me-1"></i> أمر توريد / شراء جديد
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-pro">

                        <!-- Filter Tabs & Search Controls -->
                        <div class="filter-section mb-4 p-3 bg-light rounded border no-print">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <!-- Filter Tabs -->
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="inventory_alerts.php?type=all&catid=<?= $filterCat ?>&q=<?= urlencode($searchQ) ?>" 
                                       class="btn-pro <?= ($filterType === 'all') ? 'btn-pro-primary' : 'btn-pro-outline' ?> btn-pro-sm">
                                        الكل (<?= count($allAlertItems) ?>)
                                    </a>
                                    <a href="inventory_alerts.php?type=out_of_stock&catid=<?= $filterCat ?>&q=<?= urlencode($searchQ) ?>" 
                                       class="btn-pro <?= ($filterType === 'out_of_stock') ? 'btn-pro-danger' : 'btn-pro-outline' ?> btn-pro-sm">
                                        <i class="fas fa-ban me-1"></i> نفد المخزون (<?= count($outOfStockItems) ?>)
                                    </a>
                                    <a href="inventory_alerts.php?type=low_stock&catid=<?= $filterCat ?>&q=<?= urlencode($searchQ) ?>" 
                                       class="btn-pro <?= ($filterType === 'low_stock') ? 'btn-pro-warning' : 'btn-pro-outline' ?> btn-pro-sm">
                                        <i class="fas fa-exclamation-triangle me-1"></i> مخزون منخفض (<?= count($lowStockItems) ?>)
                                    </a>
                                    <a href="inventory_alerts.php?type=near_expiry&catid=<?= $filterCat ?>&q=<?= urlencode($searchQ) ?>" 
                                       class="btn-pro <?= ($filterType === 'near_expiry') ? 'btn-pro-info' : 'btn-pro-outline' ?> btn-pro-sm">
                                        <i class="fas fa-clock me-1"></i> قرب انتهاء الصلاحية (<?= count($nearExpiryItems) ?>)
                                    </a>
                                    <a href="inventory_alerts.php?type=expired&catid=<?= $filterCat ?>&q=<?= urlencode($searchQ) ?>" 
                                       class="btn-pro <?= ($filterType === 'expired') ? 'btn-pro-danger' : 'btn-pro-outline' ?> btn-pro-sm">
                                        <i class="fas fa-calendar-times me-1"></i> منتهية (<?= count($expiredItems) ?>)
                                    </a>
                                </div>

                                <!-- Filter Form -->
                                <form method="GET" action="inventory_alerts.php" class="d-flex flex-wrap gap-2 align-items-center">
                                    <input type="hidden" name="type" value="<?= htmlspecialchars($filterType) ?>">
                                    
                                    <!-- Category dropdown -->
                                    <select name="catid" class="form-input-pro py-1 px-2" style="max-width: 180px;" onchange="this.form.submit()">
                                        <option value="0">-- كل التصنيفات --</option>
                                        <?php
                                        $catQ = "SELECT id, name_ar, name_en FROM categories WHERE storeid='$storeid' OR storeid='0' ORDER BY name_ar ASC";
                                        $catRes = mysqli_query($adController->MySQL, $catQ);
                                        if ($catRes) {
                                            while ($catRow = mysqli_fetch_assoc($catRes)) {
                                                $cName = !empty($catRow['name_ar']) ? $catRow['name_ar'] : $catRow['name_en'];
                                                $selected = ($filterCat === (int)$catRow['id']) ? 'selected' : '';
                                                echo "<option value='{$catRow['id']}' $selected>" . htmlspecialchars($cName) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>

                                    <!-- Search input -->
                                    <div class="input-group" style="width: 220px;">
                                        <input type="text" name="q" class="form-input-pro py-1 px-2" placeholder="بحث بالاسم أو الباركود..." value="<?= htmlspecialchars($searchQ) ?>">
                                        <button class="btn-pro btn-pro-primary btn-pro-sm" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>

                                    <?php if ($filterCat > 0 || $searchQ !== '' || $filterType !== 'all'): ?>
                                        <a href="inventory_alerts.php" class="btn-pro btn-pro-outline btn-pro-sm" title="إعادة تعيين الفلتر">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                        <!-- Alerts Table -->
                        <div class="table-responsive">
                            <table class="table-pro" id="alertsTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th class="text-center" style="width: 70px;">الصورة</th>
                                        <th>اسم المنتج</th>
                                        <th>الباركود</th>
                                        <th>التصنيف</th>
                                        <th class="text-center">الرصيد المتاح</th>
                                        <th class="text-center">حد الطلب</th>
                                        <th class="text-center">تاريخ الصلاحية</th>
                                        <th class="text-center">نوع التنبيه</th>
                                        <th class="text-center no-print">الإجراءات السريعة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($displayItems)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <div class="text-success mb-2">
                                                    <i class="fas fa-check-circle fa-3x"></i>
                                                </div>
                                                <h5 class="fw-bold">ممتاز! لا توجد تنبيهات نشطة في هذا القسم</h5>
                                                <p class="text-muted small">كافة المنتجات ضمن المستويات الآمنة وتواريخ الصلاحية سارية.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $counter = 0;
                                        foreach ($displayItems as $row): 
                                            $counter++;
                                            $name = !empty($row['name_ar']) ? $row['name_ar'] : $row['name_en'];
                                            $catName = !empty($row['cat_name_ar']) ? $row['cat_name_ar'] : ($row['cat_name_en'] ?? 'عام');
                                            $stockVal = (float)$row['current_stock'];
                                            $reorderVal = (float)$row['calc_reorder'];
                                            $barcodeVal = !empty($row['barcode']) ? $row['barcode'] : '---';

                                            $idVal = urlencode($adController->encrypt_decrypt(1, $row['item_thread'], 0));
                                            $secondIdVal = urlencode($adController->encrypt_decrypt(1, $idVal, 0));
                                        ?>
                                            <tr>
                                                <td class="text-center fw-bold"><?= $counter ?></td>
                                                <td class="text-center">
                                                    <?php if (!empty($row['image']) && file_exists($row['image'])): ?>
                                                        <img src="<?= htmlspecialchars($row['image']) ?>" alt="Product" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded d-inline-flex align-items-center justify-content-center text-muted" style="width: 45px; height: 45px;">
                                                            <i class="fas fa-box"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="item_card.php?id=<?= $row['id'] ?>" class="fw-bold text-decoration-none text-dark d-block">
                                                        <?= htmlspecialchars($name) ?>
                                                    </a>
                                                    <span class="text-muted small">
                                                        السعر: <?= number_format((float)$row['price'], 2) ?> <?= $currency ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border font-monospace">
                                                        <i class="fas fa-barcode me-1 text-muted"></i><?= htmlspecialchars($barcodeVal) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-secondary border">
                                                        <?= htmlspecialchars($catName) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($stockVal <= 0): ?>
                                                        <span class="badge bg-danger fs-6 px-2 py-1">
                                                            <?= $stockVal ?>
                                                        </span>
                                                    <?php elseif ($stockVal <= $reorderVal): ?>
                                                        <span class="badge bg-warning text-dark fs-6 px-2 py-1">
                                                            <?= $stockVal ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success fs-6 px-2 py-1">
                                                            <?= $stockVal ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center text-muted fw-semibold">
                                                    <?= $reorderVal ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (empty($row['expiry_date']) || $row['expiry_date'] === '0000-00-00'): ?>
                                                        <span class="text-muted small">غير محدد</span>
                                                    <?php else: ?>
                                                        <div class="small fw-semibold"><?= htmlspecialchars($row['expiry_date']) ?></div>
                                                        <?php if ($row['is_expired']): ?>
                                                            <span class="badge bg-danger small">
                                                                منتهي منذ <?= abs($row['days_until_expiry']) ?> يوم
                                                            </span>
                                                        <?php elseif ($row['is_near_expiry']): ?>
                                                            <span class="badge bg-warning text-dark small">
                                                                متبقي <?= $row['days_until_expiry'] ?> يوم
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success small">ساري</span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($row['is_out_of_stock']): ?>
                                                        <span class="badge badge-out-of-stock px-2 py-1">
                                                            <i class="fas fa-times me-1"></i> نفاد الكمية
                                                        </span>
                                                    <?php elseif ($row['is_low_stock']): ?>
                                                        <span class="badge badge-low-stock px-2 py-1">
                                                            <i class="fas fa-exclamation-triangle me-1"></i> قرب النفاد
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php if ($row['is_expired']): ?>
                                                        <span class="badge badge-expired px-2 py-1 mt-1 d-block">
                                                            <i class="fas fa-ban me-1"></i> منتهي الصلاحية
                                                        </span>
                                                    <?php elseif ($row['is_near_expiry']): ?>
                                                        <span class="badge badge-near-expiry px-2 py-1 mt-1 d-block">
                                                            <i class="fas fa-hourglass-half me-1"></i> قريبة الانتهاء
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center no-print">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <!-- Item Card -->
                                                        <a href="item_card.php?id=<?= $row['id'] ?>" class="btn-pro btn-pro-primary btn-pro-sm" title="بطاقة الصنف التفصيلية">
                                                            <i class="fas fa-id-card"></i>
                                                        </a>
                                                        <!-- Add Purchase -->
                                                        <a href="add_purchase.php" class="btn-pro btn-pro-success btn-pro-sm" title="إنشاء أمر شراء / توريد">
                                                            <i class="fas fa-cart-plus"></i>
                                                        </a>
                                                        <!-- Edit Item -->
                                                        <a href="edit_item.php?sd=<?= $secondIdVal ?>" class="btn-pro btn-pro-outline btn-pro-sm" title="تعديل بيانات الصنف">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
        function exportTableToExcel(tableID, filename = 'inventory_alerts') {
            let table = document.getElementById(tableID);
            if (!table) return;

            let html = table.outerHTML;
            // Clean action buttons before export
            let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            let downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            downloadLink.href = url;
            downloadLink.download = filename + '_' + new Date().toISOString().slice(0, 10) + '.xls';
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
</body>

</html>

