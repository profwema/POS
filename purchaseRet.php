<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';
$pgName   = basename(__FILE__);

// Fetch purchase returns list
$query = "SELECT pr.*, 
                 s.name_ar AS supplier_name_ar, 
                 s.name_en AS supplier_name_en, 
                 b.name_ar AS branch_name_ar,
                 b.name_en AS branch_name_en
          FROM purchase_ret pr 
          LEFT JOIN suppliers s ON pr.supplier = s.id AND (s.storeid = '$storeid' OR s.storeid = '0') 
          LEFT JOIN branches b ON pr.branch = b.id AND (b.storeid = '$storeid' OR b.storeid = '0') 
          WHERE pr.storeid = '$storeid' AND pr.state = '1' 
          ORDER BY pr.id DESC";

$res = mysqli_query($adController->MySQL, $query);

$returns = [];
$totalReturned = 0.0;
$returnsCount = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $returns[] = $row;
        $totalReturned += (float)$row['all_total'];
        $returnsCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WAM Tech POS - إدارة مرتجعات المشتريات</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        @media print {
            .admin-sidebar, .admin-navbar, .page-header, .no-print, .btn-pro {
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
                        <i class="fas fa-undo-alt text-warning me-2"></i>
                        <?= RETURNED_PURCHASES ?> - مرتجعات فواتير الشراء
                    </h1>
                    <div class="page-breadcrumb">
                        <div class="breadcrumb-item">
                            <i class="fas fa-home"></i>
                            <span>الرئيسية</span>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <a href="purchase.php" class="text-decoration-none"><?= PURCHASES ?></a>
                        </div>
                        <span class="breadcrumb-separator">/</span>
                        <div class="breadcrumb-item">
                            <span class="text-primary"><?= RETURNED_PURCHASES ?></span>
                        </div>
                    </div>
                </div>

                <!-- KPI Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4 col-sm-6">
                        <div class="stat-card warning">
                            <i class="fas fa-undo stat-card-icon text-warning"></i>
                            <div class="stat-card-value text-warning">
                                <?= number_format($totalReturned, 2) ?> <small class="fs-6"><?= $currency ?></small>
                            </div>
                            <div class="stat-card-label">إجمالي قيمة المرتجعات للموردين</div>
                            <div class="mt-2 small text-muted">
                                تم خصمها من المخازن وحسابات الموردين
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="stat-card">
                            <i class="fas fa-file-invoice stat-card-icon text-primary"></i>
                            <div class="stat-card-value text-primary">
                                <?= $returnsCount ?>
                            </div>
                            <div class="stat-card-label">عدد فواتير المرتجع</div>
                            <div class="mt-2 small text-muted">
                                فواتير مرتجع شراء معتمدة
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <div class="stat-card info">
                            <i class="fas fa-plus-circle stat-card-icon text-info"></i>
                            <div class="d-flex flex-column justify-content-center h-100">
                                <a href="add_purchaseRet.php" class="btn-pro btn-pro-primary w-100 justify-content-center py-2">
                                    <i class="fas fa-plus me-1"></i> تسجيل مرتجع شراء جديد
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Card -->
                <div class="card-pro">
                    <div class="card-header-pro">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h2 class="card-title-pro mb-0">
                                <i class="fas fa-list text-primary"></i>
                                سجل فواتير المرتجعات
                                <span class="badge-pro badge-pro-primary ms-2"><?= $returnsCount ?></span>
                            </h2>
                            <div class="d-flex gap-2 no-print">
                                <button onclick="window.print()" class="btn-pro btn-pro-outline btn-pro-sm" title="طباعة">
                                    <i class="fas fa-print me-1"></i> طباعة
                                </button>
                                <a href="add_purchaseRet.php" class="btn-pro btn-pro-primary btn-pro-sm">
                                    <i class="fas fa-plus me-1"></i> إضافة مرتجع جديد
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-pro p-0">
                        <div class="table-responsive">
                            <table class="table-pro mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 40px;">#</th>
                                        <th>رقم إذن المرتجع</th>
                                        <th>فاتورة الشراء الأصلية</th>
                                        <th>المورد</th>
                                        <th>فاتورة المورد</th>
                                        <th>الفرع</th>
                                        <th>تاريخ المرتجع</th>
                                        <th class="text-center">إجمالي المرتجع</th>
                                        <th class="text-center">طريقة التسوية</th>
                                        <th class="text-center no-print">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($returns)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <i class="fas fa-inbox fa-3x mb-2 d-block text-muted"></i>
                                                لا توجد مرتجعات مشتريات مسجلة حتى الآن
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $i = 0;
                                        foreach ($returns as $ret): 
                                            $i++;
                                            $invNumber = htmlspecialchars($ret['invoicenumber'] ?? '');
                                            $origInvoice = htmlspecialchars($ret['purchInvoice'] ?? '');
                                            $suppName = !empty($ret['supplier_name_ar']) ? $ret['supplier_name_ar'] : ($ret['supplier_name_en'] ?? 'مورد عام');
                                            $brName   = !empty($ret['branch_name_ar']) ? $ret['branch_name_ar'] : ($ret['branch_name_en'] ?? 'الرئيسي');
                                            $isCash   = ((int)$ret['paymentType'] === 1);

                                            $idval        = urlencode($adController->encrypt_decrypt(1, $ret["invoicenumber"] ?? '', 0));
                                            $invoiceTable = urlencode($adController->encrypt_decrypt(1, 'purchase_ret', 0));
                                            $otherTable   = urlencode($adController->encrypt_decrypt(1, 'outgo', 0));
                                        ?>
                                            <tr>
                                                <td class="text-center fw-bold text-muted"><?= $i ?></td>
                                                <td>
                                                    <a href="reports/purchaseRet.php?sd=<?= urlencode($invNumber) ?>" target="_blank" class="font-monospace fw-bold text-primary text-decoration-none">
                                                        #<?= $invNumber ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="purchase.php?fr_date=&to_date=&supplier=0&pay_type=all" class="font-monospace text-muted text-decoration-none">
                                                        #<?= $origInvoice ?>
                                                    </a>
                                                </td>
                                                <td class="fw-semibold">
                                                    <?= htmlspecialchars($suppName) ?>
                                                </td>
                                                <td class="font-monospace small text-muted">
                                                    <?= htmlspecialchars($ret['supplier_invoice'] ?: '---') ?>
                                                </td>
                                                <td class="small text-muted">
                                                    <?= htmlspecialchars($brName) ?>
                                                </td>
                                                <td class="small font-monospace text-muted">
                                                    <?= htmlspecialchars(substr($ret['invoice_date'], 0, 10)) ?>
                                                </td>
                                                <td class="text-center fw-bold text-danger">
                                                    <?= number_format((float)$ret['all_total'], 2) ?> <?= $currency ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($isCash): ?>
                                                        <span class="badge-pro badge-pro-success">استرداد نقدي (صندوق)</span>
                                                    <?php else: ?>
                                                        <span class="badge-pro badge-pro-warning">خصم من حساب المورد</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center no-print">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="reports/purchaseRet.php?sd=<?= urlencode($invNumber) ?>" target="_blank" class="btn-pro btn-pro-outline btn-pro-sm" title="طباعة">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                        <button class="btn-pro btn-pro-danger btn-pro-sm" onclick="deleteInvoice('<?= $invoiceTable ?>', '<?= $idval ?>', '<?= $otherTable ?>');" title="حذف">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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
</body>

</html>
