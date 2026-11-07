<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);

$rawSd = $_REQUEST['sd'] ?? '';
$decrypted = $adController->encrypt_decrypt(2, urldecode($rawSd), 0);
if (!empty($decrypted)) {
    $decrypted2 = $adController->encrypt_decrypt(2, $decrypted, 0);
    $itemThread = !empty($decrypted2) ? $decrypted2 : $decrypted;
} else {
    $itemThread = $rawSd;
}
$safeThread = mysqli_real_escape_string($adController->MySQL, $itemThread);

// Fetch item thread details
$query = "SELECT name_$language AS name, name_ar, name_en FROM items WHERE item_thread='$safeThread' LIMIT 1";
$res = mysqli_query($adController->MySQL, $query);
$dataCat = $res ? mysqli_fetch_assoc($res) : null;
$itemName = $dataCat ? (!empty($dataCat['name']) ? $dataCat['name'] : ($dataCat['name_ar'] ?? 'صنف')) : 'الصنف';

// Fetch all packaging units for this item thread
$itemsListArray = [];
$query = "SELECT i.*, u.name_ar AS unit_ar, u.name_en AS unit_en 
          FROM items i 
          LEFT JOIN units u ON i.unit = u.id 
          WHERE i.item_thread='$safeThread' 
          ORDER BY i.intimate_stock DESC";
$res45 = mysqli_query($adController->MySQL, $query);
if ($res45) {
    while ($it = mysqli_fetch_assoc($res45)) {
        $itemsListArray[] = $it;
    }
}
$numRows = count($itemsListArray);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة ملصقات الباركود | WAM Tech ERP</title>
    <link rel="stylesheet" href="css/admin-pro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .barcode-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .qty-sticker-input {
            width: 100px;
            text-align: center;
            font-size: 1.1rem;
            font-weight: bold;
            font-family: monospace;
            border: 2px solid #cbd5e1;
            border-radius: 8px;
            padding: 6px;
        }
        .qty-sticker-input:focus {
            border-color: #3b82f6;
            outline: none;
        }
    </style>
</head>
<body class="admin-pro-body">
    <?php require_once("components/header_pro.php"); ?>
    
    <div class="admin-wrapper">
        <?php require_once("components/sidebar_pro.php"); ?>
        
        <main class="admin-main">
            <div class="container-fluid px-4 py-4">
                
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">
                            <i class="fas fa-barcode text-primary me-2"></i> طباعة ملصقات الباركود
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="items.php">الأصناف</a></li>
                                <li class="breadcrumb-item active"><?= htmlspecialchars($itemName) ?></li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="items.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة لقائمة الأصناف
                        </a>
                    </div>
                </div>

                <!-- Main Form Card -->
                <div class="barcode-box mb-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <div class="me-3 p-3 bg-primary-subtle text-primary rounded-3 fs-3">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($itemName) ?></h4>
                            <span class="text-muted small">حدد عدد الملصقات المطلوب طباعتها لكل وحدة أو عبوة من عبوات هذا الصنف</span>
                        </div>
                    </div>

                    <form method="POST" action="report-barcode.php" target="_blank">
                        <input type="hidden" value="<?= htmlspecialchars($itemName) ?>" name="name">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 25%;">الوحدة / العبوة</th>
                                        <th style="width: 30%;">رمز الباركود (Barcode)</th>
                                        <th style="width: 20%;" class="text-end">السعر المعلن (ج.م)</th>
                                        <th style="width: 25%;" class="text-center">عدد الملصقات المطلوب طباعتها</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < $numRows; $i++): 
                                        $it = $itemsListArray[$i];
                                        $unitName = ($language == 'en' && !empty($it['unit_en'])) ? $it['unit_en'] : (!empty($it['unit_ar']) ? $it['unit_ar'] : 'وحدة');
                                        $barcode = $it['barcode'] ?: '---';
                                        $price = floatval($it['price'] ?? 0);
                                    ?>
                                        <tr>
                                            <td class="text-center text-muted fw-bold"><?= $i + 1 ?></td>
                                            <td>
                                                <input type="hidden" name="qty-id[]" value="<?= $it['id'] ?>">
                                                <input type="hidden" name="qty-unit[]" value="<?= htmlspecialchars($unitName) ?>">
                                                <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($unitName) ?></span>
                                            </td>
                                            <td>
                                                <input type="hidden" name="qty-barcode[]" value="<?= htmlspecialchars($barcode) ?>">
                                                <span class="font-monospace text-primary fw-bold fs-6">
                                                    <i class="fas fa-barcode me-1"></i> <?= htmlspecialchars($barcode) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <input type="hidden" name="qty-price[]" value="<?= $price ?>">
                                                <span class="font-monospace fw-bold text-dark fs-6">
                                                    <?= number_format($price, 2) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    <input type="number" name="qty-fill[]" min="0" max="500" value="1" class="qty-sticker-input" title="عدد النسخ">
                                                    <span class="text-muted small">ملصق</span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Printing Helper / Controls -->
                        <div class="row align-items-center pt-3 border-top">
                            <div class="col-md-6 text-muted small">
                                <i class="fas fa-info-circle text-primary me-1"></i> يتم تجهيز الملصقات لطباعة الباركود الحرارية (طابعات الملصقات ستيكر رول 207×118 pt).
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <button type="submit" class="btn btn-primary px-4 py-2 fs-6">
                                    <i class="fas fa-print me-1"></i> طباعة ملصقات الباركود
                                </button>
                                <a href="items.php" class="btn btn-outline-secondary px-4 py-2 fs-6 ms-2">
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
