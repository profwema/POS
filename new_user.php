<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

// Only authorized admin can run database seed/initialization
$adController->checkAuthorized('admin');

$MySQL    = $adController->MySQL;
$storeid  = intval($_SESSION['storeid'] ?? 1);
$language = $_SESSION['lang'] ?? 'ar';

function getNewCode($MySQL)
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
    return 'acc' . str_pad($code, 3, "0", STR_PAD_LEFT); 
}

function insertAccount($name_en, $name_ar, $parent, $refId, $MySQL, $storeid)
{
    $code      = getNewCode($MySQL);
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
        $logs[] = "الفرع الرئيسي موجود مسبقاً (معرف: {$insertIdBranch}).";
    } else {
        mysqli_query($MySQL, "INSERT INTO `branches` (`name_en`, `name_ar`, `storeid`) VALUES ('Main Branch', 'الفرع الرئيسي', '$storeid')");
        $insertIdBranch = mysqli_insert_id($MySQL);
        $logs[] = "تمت إضافة الفرع الرئيسي بنجاح (معرف: {$insertIdBranch}).";
    }

    // 2. عميل عام
    $qCheckCust = "SELECT id FROM customers WHERE storeid='$storeid' AND `default`='1' LIMIT 1";
    $rCheckCust = mysqli_query($MySQL, $qCheckCust);
    if ($rCheckCust && mysqli_num_rows($rCheckCust) > 0) {
        $logs[] = "العميل الافتراضي موجود مسبقاً.";
    } else {
        mysqli_query($MySQL, "INSERT INTO `customers` (`name_en`, `name_ar`, `default`, `storeid`) VALUES ('Cash cust', 'عميل عام', '1', '$storeid')");
        $refCust = mysqli_insert_id($MySQL);
        insertAccount('Cash cust', 'عميل عام', 1, $refCust, $MySQL, $storeid);
        $logs[] = "تمت إضافة العميل الافتراضي وحسابه المحاسبي بنجاح.";
    }

    // 3. مورد عام
    $qCheckSupp = "SELECT id FROM suppliers WHERE storeid='$storeid' AND `default`='1' LIMIT 1";
    $rCheckSupp = mysqli_query($MySQL, $qCheckSupp);
    if ($rCheckSupp && mysqli_num_rows($rCheckSupp) > 0) {
        $logs[] = "المورد الافتراضي موجود مسبقاً.";
    } else {
        mysqli_query($MySQL, "INSERT INTO `suppliers` (`name_en`, `name_ar`, `default`, `storeid`) VALUES ('Cash sup', 'مورد عام', '1', '$storeid')");
        $refSupp = mysqli_insert_id($MySQL);
        insertAccount('Cash sup', 'مورد عام', 5, $refSupp, $MySQL, $storeid);
        $logs[] = "تمت إضافة المورد الافتراضي وحسابه المحاسبي بنجاح.";
    }

    // 4. وحدة قياس افتراضية
    $qCheckUnit = "SELECT id FROM units WHERE storeid='$storeid' AND `default`='1' LIMIT 1";
    $rCheckUnit = mysqli_query($MySQL, $qCheckUnit);
    if ($rCheckUnit && mysqli_num_rows($rCheckUnit) > 0) {
        $logs[] = "الوحدة الافتراضية (قطعة) موجودة مسبقاً.";
    } else {
        mysqli_query($MySQL, "INSERT INTO `units` (`name_en`, `name_ar`, `default`, `storeid`) VALUES ('Piece', 'قطعة', '1', '$storeid')");
        $logs[] = "تمت إضافة الوحدة الافتراضية بنجاح.";
    }

    // 5. المخزن الرئيسي
    $qCheckStore = "SELECT id FROM stores WHERE storeid='$storeid' AND `default`='1' LIMIT 1";
    $rCheckStore = mysqli_query($MySQL, $qCheckStore);
    if ($rCheckStore && mysqli_num_rows($rCheckStore) > 0) {
        $logs[] = "المخزن الرئيسي موجود مسبقاً.";
    } else {
        mysqli_query($MySQL, "INSERT INTO `stores` (`name_en`, `name_ar`, `branch`, `default`, `storeid`) VALUES ('Main Store', 'المخزن الرئيسي', '$insertIdBranch', '1', '$storeid')");
        $refStore = mysqli_insert_id($MySQL);
        insertAccount('Main Store', 'المخزن الرئيسي', 37, $refStore, $MySQL, $storeid);
        $logs[] = "تمت إضافة المخزن الرئيسي وحساباته المحاسبية بنجاح.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - تهيئة البيانات الافتراضية</title>
    <?php require_once("header.php"); ?>	
    <style>
        .init-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            max-width: 700px;
            margin: 20px auto;
        }
        .log-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 13px;
            color: #0f172a;
        }
        .log-item {
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <?php require_once("header_top.php"); ?>
    
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php"); ?>
            <div id="content" class="span10">
                <div class="init-card">
                    <h2 style="margin-top:0; color:#1e293b; font-size:18px; border-bottom:2px solid #f1f5f9; padding-bottom:12px;">
                        <i class="halflings-icon refresh"></i> تهيئة البيانات الأساسية للمنظومة
                    </h2>
                    <p style="color:#64748b; line-height:1.6; margin-top:12px;">
                        تقوم هذه الأداة بفحص وتهيئة السجلات الافتراضية الأولية للنظام (الفرع الرئيسي، العميل العام النقدي، المورد العام، المخزن الرئيسي، وحساباتها المقابلة في شجرة الحسابات).
                    </p>

                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                        <button type="submit" name="initialize_defaults" class="btn btn-primary" style="padding:10px 24px; font-weight:bold; font-size:14px;">
                            <i class="halflings-icon white play"></i> بدء فحص وتوليد السجلات الافتراضية
                        </button>
                    </form>

                    <?php if (!empty($logs)): ?>
                        <div class="log-box">
                            <strong style="display:block; margin-bottom:10px; color:#059669;">نتائج عملية الفحص والتهيئة:</strong>
                            <?php foreach ($logs as $log): ?>
                                <div class="log-item">
                                    <i class="halflings-icon ok" style="color:#10b981;"></i>
                                    <span><?=htmlspecialchars($log)?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix"></div>
    
    <?php require_once("footer.php"); ?>
</body>
</html>