<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

// Only authorized admin can run database seed/initialization
$adController->checkAuthorized('admin');

$MySQL    = $adController->MySQL;
$storeid  = intval($_SESSION['storeid'] ?? 1);
$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');

function getNewAccountCode($MySQL)
{
    $queryCode = "SELECT code FROM accounts ORDER BY id DESC LIMIT 1";
    $resCode   = mysqli_query($MySQL, $queryCode);
    $numCode   = $resCode ? mysqli_num_rows($resCode) : 0;
    $dataCode  = $resCode ? mysqli_fetch_assoc($resCode) : null;
    if ($numCode && !empty($dataCode['code'])) {
        $rest = substr($dataCode['code'], -3);  
        $code = intval($rest) + 1;
    } else {
        $code = 1;
    }
    return 'acc' . str_pad((string)$code, 3, "0", STR_PAD_LEFT); 
}

function insertAccountSeed($name_en, $name_ar, $parent, $refId, $MySQL, $storeid)
{
    $code      = getNewAccountCode($MySQL);
    $queryCode = "SELECT type, budget FROM accounts WHERE id = '$parent' LIMIT 1";
    $resCode   = mysqli_query($MySQL, $queryCode);
    $dataCode  = $resCode ? mysqli_fetch_assoc($resCode) : null;
    $type      = $dataCode ? $dataCode['type'] : 1; 
    $flbs      = $dataCode ? $dataCode['budget'] : 1;            
    
    $escNameEn = mysqli_real_escape_string($MySQL, $name_en);
    $escNameAr = mysqli_real_escape_string($MySQL, $name_ar);

    $query = "INSERT INTO accounts (code, name_en, name_ar, type, budget, parent, hasChild, refId, storeid)
              VALUES ('$code', '$escNameEn', '$escNameAr', '$type', '$flbs', '$parent', '0', '$refId', '$storeid')";
    mysqli_query($MySQL, $query);
}

$logs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['initialize_defaults'])) {
    // 1. الفرع الرئيسي
    $qCheckBr = "SELECT id FROM branches WHERE storeid='$storeid' LIMIT 1";
    $rCheckBr = mysqli_query($MySQL, $qCheckBr);
    if ($rCheckBr && mysqli_num_rows($rCheckBr) > 0) {
        $rowBr = mysqli_fetch_assoc($rCheckBr);
        $insertIdBranch = $rowBr['id'];
        $logs[] = ['type' => 'info', 'text' => "الفرع الرئيسي موجود مسبقاً (معرف رقم: {$insertIdBranch})."];
    } else {
        mysqli_query($MySQL, "INSERT INTO `branches` (`name_en`, `name_ar`, `storeid`) VALUES ('Main Branch', 'الفرع الرئيسي', '$storeid')");
        $insertIdBranch = mysqli_insert_id($MySQL);
        $logs[] = ['type' => 'success', 'text' => "تم إنشاء الفرع الرئيسي بنجاح (معرف رقم: {$insertIdBranch})."];
    }

    // 2. عميل عام
    $qCheckCust = "SELECT id FROM customers WHERE storeid='$storeid' AND `default`='1' LIMIT 1";
    $rCheckCust = mysqli_query($MySQL, $qCheckCust);
    if ($rCheckCust && mysqli_num_rows($rCheckCust) > 0) {
        $logs[] = ['type' => 'info', 'text' => "العميل الافتراضي (عميل عام / نقدي) موجود مسبقاً."];
    } else {
        mysqli_query($MySQL, "INSERT INTO `customers` (`name_en`, `name_ar`, `default`, `storeid`) VALUES ('Cash cust', 'عميل نقدي عام', '1', '$storeid')");
        $refCust = mysqli_insert_id($MySQL);
        insertAccountSeed('Cash cust', 'عميل نقدي عام', 1, $refCust, $MySQL, $storeid);
        $logs[] = ['type' => 'success', 'text' => "تم إنشاء العميل النقدي الافتراضي وتوليد حسابه المحاسبي بنجاح."];
    }

    // 3. مورد عام
    $qCheckSupp = "SELECT id FROM suppliers WHERE storeid='$storeid' AND `default`='1' LIMIT 1";
    $rCheckSupp = mysqli_query($MySQL, $qCheckSupp);
    if ($rCheckSupp && mysqli_num_rows($rCheckSupp) > 0) {
        $logs[] = ['type' => 'info', 'text' => "المورد الافتراضي (مورد عام نقدي) موجود مسبقاً."];
    } else {
        mysqli_query($MySQL, "INSERT INTO `suppliers` (`name_en`, `name_ar`, `default`, `storeid`) VALUES ('Cash sup', 'مورد عام نقدي', '1', '$storeid')");
        $refSupp = mysqli_insert_id($MySQL);
        insertAccountSeed('Cash sup', 'مورد عام نقدي', 5, $refSupp, $MySQL, $storeid);
        $logs[] = ['type' => 'success', 'text' => "تم إنشاء المورد الافتراضي وتوليد حسابه المحاسبي بنجاح."];
    }

    // 4. وحدة قياس افتراضية
    $qCheckUnit = "SELECT id FROM units WHERE storeid='$storeid' AND `default`='1' LIMIT 1";
    $rCheckUnit = mysqli_query($MySQL, $qCheckUnit);
    if ($rCheckUnit && mysqli_num_rows($rCheckUnit) > 0) {
        $logs[] = ['type' => 'info', 'text' => "وحدة القياس الافتراضية (قطعة) موجودة مسبقاً."];
    } else {
        mysqli_query($MySQL, "INSERT INTO `units` (`name_en`, `name_ar`, `default`, `storeid`) VALUES ('Piece', 'قطعة', '1', '$storeid')");
        $logs[] = ['type' => 'success', 'text' => "تم إنشاء وحدة القياس الأساسية (قطعة) بنجاح."];
    }

    // 5. المخزن الرئيسي
    $qCheckStore = "SELECT id FROM stores WHERE storeid='$storeid' AND `default`='1' LIMIT 1";
    $rCheckStore = mysqli_query($MySQL, $qCheckStore);
    if ($rCheckStore && mysqli_num_rows($rCheckStore) > 0) {
        $logs[] = ['type' => 'info', 'text' => "المخزن الرئيسي موجود مسبقاً ومحدد كافتراضي."];
    } else {
        mysqli_query($MySQL, "INSERT INTO `stores` (`name_en`, `name_ar`, `branch`, `default`, `storeid`) VALUES ('Main Store', 'المخزن الرئيسي', '$insertIdBranch', '1', '$storeid')");
        $refStore = mysqli_insert_id($MySQL);
        insertAccountSeed('Main Store', 'المخزن الرئيسي', 37, $refStore, $MySQL, $storeid);
        $logs[] = ['type' => 'success', 'text' => "تم إنشاء المخزن الرئيسي وربطه بالفرع وحسابه المحاسبي بنجاح."];
    }
}

// Current status checks
$hasBranch = mysqli_num_rows(mysqli_query($MySQL, "SELECT id FROM branches WHERE storeid='$storeid' LIMIT 1")) > 0;
$hasCust   = mysqli_num_rows(mysqli_query($MySQL, "SELECT id FROM customers WHERE storeid='$storeid' AND `default`='1' LIMIT 1")) > 0;
$hasSupp   = mysqli_num_rows(mysqli_query($MySQL, "SELECT id FROM suppliers WHERE storeid='$storeid' AND `default`='1' LIMIT 1")) > 0;
$hasUnit   = mysqli_num_rows(mysqli_query($MySQL, "SELECT id FROM units WHERE storeid='$storeid' AND `default`='1' LIMIT 1")) > 0;
$hasStore  = mysqli_num_rows(mysqli_query($MySQL, "SELECT id FROM stores WHERE storeid='$storeid' AND `default`='1' LIMIT 1")) > 0;

$allReady = $hasBranch && $hasCust && $hasSupp && $hasUnit && $hasStore;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تهيئة البيانات الافتراضية للنظام - WAM Tech POS</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .init-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .check-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 0.75rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .check-item:hover {
            background: #f1f5f9;
        }
        .log-box {
            background: #0f172a;
            color: #f8fafc;
            border-radius: 10px;
            padding: 1.25rem;
            font-family: monospace;
            font-size: 0.9rem;
            margin-top: 1.5rem;
            max-height: 250px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <?php require_once("components/sidebar_pro.php"); ?>

        <div class="admin-content">
            <?php require_once("components/header_pro.php"); ?>

            <div class="container-fluid px-4 py-4">
                <!-- Page Breadcrumb & Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-1">
                                <li class="breadcrumb-item"><a href="index.php">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="#">النظام</a></li>
                                <li class="breadcrumb-item active" aria-current="page">تهيئة البيانات الأساسية</li>
                            </ol>
                        </nav>
                        <h3 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-magic text-primary me-2"></i>
                            معالج تهيئة البيانات الأساسية والافتراضية للمتجر
                        </h3>
                    </div>
                    <div>
                        <a href="settings.php" class="btn btn-outline-secondary">
                            <i class="fas fa-cog me-1"></i> الإعدادات العامة
                        </a>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <div class="init-card">
                            <div class="text-center mb-4">
                                <div class="avatar-lg bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px; height:70px; font-size:1.8rem;">
                                    <i class="fas fa-sliders-h"></i>
                                </div>
                                <h4 class="fw-bold text-dark">فحص وتثبيت البيانات الافتراضية للنظام</h4>
                                <p class="text-muted small">
                                    تتطلب عمليات نقاط البيع والمخازن وجود فرع رئيسي، ومخزن افتراضي، وعميل نقدي عام، ومورد نقدي عام، ووحدة قياس.
                                </p>
                            </div>

                            <!-- Readiness Checklist -->
                            <div class="mb-4">
                                <div class="check-item">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="fas fa-code-branch fs-4 text-primary"></i>
                                        <div>
                                            <strong class="d-block text-dark">1. الفرع الرئيسي للمنشأة</strong>
                                            <span class="text-muted small">فرع أساسي لربط عمليات البيع والشراء والمخازن</span>
                                        </div>
                                    </div>
                                    <span class="badge <?= $hasBranch ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' ?> px-3 py-2">
                                        <?= $hasBranch ? '<i class="fas fa-check me-1"></i> مهيأ وموجود' : '<i class="fas fa-times me-1"></i> غير مهيأ' ?>
                                    </span>
                                </div>

                                <div class="check-item">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="fas fa-warehouse fs-4 text-info"></i>
                                        <div>
                                            <strong class="d-block text-dark">2. المخزن الرئيسي الافتراضي</strong>
                                            <span class="text-muted small">المستودع الرئيسي لحفظ وإيداع أرصدة الأصناف</span>
                                        </div>
                                    </div>
                                    <span class="badge <?= $hasStore ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' ?> px-3 py-2">
                                        <?= $hasStore ? '<i class="fas fa-check me-1"></i> مهيأ وموجود' : '<i class="fas fa-times me-1"></i> غير مهيأ' ?>
                                    </span>
                                </div>

                                <div class="check-item">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="fas fa-user-tag fs-4 text-success"></i>
                                        <div>
                                            <strong class="d-block text-dark">3. العميل النقدي الافتراضي (Cash Customer)</strong>
                                            <span class="text-muted small">عميل عام لاستقبال فواتير نقاط البيع النقدية السريعة</span>
                                        </div>
                                    </div>
                                    <span class="badge <?= $hasCust ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' ?> px-3 py-2">
                                        <?= $hasCust ? '<i class="fas fa-check me-1"></i> مهيأ وموجود' : '<i class="fas fa-times me-1"></i> غير مهيأ' ?>
                                    </span>
                                </div>

                                <div class="check-item">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="fas fa-truck-loading fs-4 text-danger"></i>
                                        <div>
                                            <strong class="d-block text-dark">4. المورد النقدي الافتراضي (Cash Supplier)</strong>
                                            <span class="text-muted small">مورد عام لإجراء فواتير المشتريات النقدية السريعة</span>
                                        </div>
                                    </div>
                                    <span class="badge <?= $hasSupp ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' ?> px-3 py-2">
                                        <?= $hasSupp ? '<i class="fas fa-check me-1"></i> مهيأ وموجود' : '<i class="fas fa-times me-1"></i> غير مهيأ' ?>
                                    </span>
                                </div>

                                <div class="check-item">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="fas fa-balance-scale fs-4 text-warning"></i>
                                        <div>
                                            <strong class="d-block text-dark">5. وحدة القياس الأساسية (قطعة / Piece)</strong>
                                            <span class="text-muted small">الوحدة الافتراضية لتعريف وبيع الأصناف بالمتجر</span>
                                        </div>
                                    </div>
                                    <span class="badge <?= $hasUnit ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' ?> px-3 py-2">
                                        <?= $hasUnit ? '<i class="fas fa-check me-1"></i> مهيأ وموجود' : '<i class="fas fa-times me-1"></i> غير مهيأ' ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Initialization Button -->
                            <form method="POST" action="new_user.php" class="text-center">
                                <input type="hidden" name="initialize_defaults" value="1">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()); ?>">

                                <?php if ($allReady): ?>
                                    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center justify-content-center gap-2 mb-3">
                                        <i class="fas fa-check-circle fs-5"></i>
                                        <strong>تهانينا! كافة البيانات الافتراضية مهيأة ومكتملة تماماً وجاهزة للعمل.</strong>
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary px-4 py-2">
                                        <i class="fas fa-redo me-1"></i> إعادة الفحص والتحقق من التناسق
                                    </button>
                                <?php else: ?>
                                    <div class="alert alert-warning border-0 shadow-sm mb-3">
                                        <i class="fas fa-exclamation-triangle me-1"></i> تنبيه: بعض البيانات الافتراضية مفقودة. اضغط على الزر أدناه لإنشائها وربط شجرة الحسابات تلقائياً.
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg px-5 py-3" style="background:#2563eb; border-color:#2563eb; font-weight:700;">
                                        <i class="fas fa-magic me-2"></i> بدء تهيئة وتثبيت البيانات الافتراضية الآن
                                    </button>
                                <?php endif; ?>
                            </form>

                            <!-- Execution Log -->
                            <?php if (!empty($logs)): ?>
                                <div class="log-box">
                                    <div class="fw-bold mb-2 text-warning border-bottom border-secondary pb-1">
                                        <i class="fas fa-terminal me-1"></i> سجل عمليات التهيئة:
                                    </div>
                                    <?php foreach ($logs as $log): ?>
                                        <div class="mb-1 text-<?= $log['type'] === 'success' ? 'success' : 'info' ?>">
                                            &gt; <?= htmlspecialchars($log['text']) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once("footer.php"); ?>
</body>
</html>
