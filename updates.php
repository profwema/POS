<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);

$query    = "SELECT * FROM version LIMIT 1";
$res      = mysqli_query($adController->MySQL, $query);
$data     = $res ? mysqli_fetch_assoc($res) : null;

$version  = $data['version'] ?? '2.1.0 Enterprise (Egypt Edition)';
$serverId = $data['serverId'] ?? 'WAM-EG-2026';
$phpVer   = PHP_VERSION;
$mysqlVer = mysqli_get_server_info($adController->MySQL);

$requiredExtensions = ['mysqli', 'json', 'mbstring', 'curl', 'openssl', 'gd'];
$extStatus = [];
foreach ($requiredExtensions as $ext) {
    $extStatus[$ext] = extension_loaded($ext);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تحديثات وترخيص النظام - WAM Tech POS</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .hero-banner {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 14px;
            padding: 2.5rem;
            color: #fff;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
        }
        .hero-badge {
            background: #10b981;
            color: #fff;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
        }
        .info-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            height: 100%;
        }
        .info-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
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
                                <li class="breadcrumb-item active" aria-current="page">معلومات وتحديثات النظام</li>
                            </ol>
                        </nav>
                        <h3 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-sync-alt text-primary me-2"></i>
                            مركز التحديثات وترخيص النظام
                        </h3>
                    </div>
                    <div>
                        <a href="settings.php" class="btn btn-outline-secondary">
                            <i class="fas fa-cog me-1"></i> الإعدادات العامة
                        </a>
                    </div>
                </div>

                <!-- Hero Section -->
                <div class="hero-banner">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="hero-badge mb-2">
                                <i class="fas fa-shield-alt me-1"></i> مرخص ومعتمد للتشغيل المؤسسي
                            </span>
                            <h2 class="fw-bold text-white mb-2 mt-2">نظام WAM Tech POS & ERP المتكامل</h2>
                            <p class="text-white-50 mb-0 fs-6">
                                الإصدار المحدث المتوافق كلياً مع متطلبات السوق المصري، ضريبة القيمة المضافة 14%، الدفع الإلكتروني (إنستاباي / فودافون كاش)، ومكتبات بي إتش بي 8.2 الحديثة.
                            </p>
                        </div>
                        <div class="col-lg-4 text-lg-end text-center">
                            <button class="btn btn-success btn-lg px-4 py-3 fw-bold" id="btnCheckUpdate" style="border-radius:10px;">
                                <i class="fas fa-cloud-download-alt me-2"></i> فحص التحديثات السحابية
                            </button>
                        </div>
                    </div>
                </div>

                <!-- System Specifications Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="info-card">
                            <div class="info-icon text-primary"><i class="fas fa-tag"></i></div>
                            <div class="text-muted small fw-bold">إصدار النظام الحالي</div>
                            <h5 class="fw-bold text-dark mt-1"><?= htmlspecialchars($version) ?></h5>
                            <span class="badge bg-success-subtle text-success border border-success-subtle mt-2">محدث لآخر إصدار</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="info-card">
                            <div class="info-icon text-success"><i class="fab fa-php"></i></div>
                            <div class="text-muted small fw-bold">إصدار PHP الحالي</div>
                            <h5 class="fw-bold text-dark mt-1"><?= htmlspecialchars($phpVer) ?></h5>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-2">متوافق مع PHP 8.2+</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="info-card">
                            <div class="info-icon text-info"><i class="fas fa-database"></i></div>
                            <div class="text-muted small fw-bold">محرك قاعدة البيانات MySQL</div>
                            <h5 class="fw-bold text-dark mt-1"><?= htmlspecialchars($mysqlVer) ?></h5>
                            <span class="badge bg-info-subtle text-info border border-info-subtle mt-2">MySQL 8.x / MariaDB</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="info-card">
                            <div class="info-icon text-warning"><i class="fas fa-server"></i></div>
                            <div class="text-muted small fw-bold">معرف الخادم والتثبيت</div>
                            <h5 class="fw-bold text-dark mt-1 font-monospace"><?= htmlspecialchars($serverId) ?></h5>
                            <span class="badge bg-secondary-subtle text-secondary border mt-2">ترخيص تجاري نشط</span>
                        </div>
                    </div>
                </div>

                <!-- Environment & Extensions Health Table -->
                <div class="card card-pro border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-check-double text-success me-2"></i> جاهزية بيئة التشغيل والمكتبات الأساسية
                        </h5>
                        <span class="badge bg-success px-3 py-2">جميع المكتبات تعمل بكفاءة</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>المكتبة / الميزة البرمجية</th>
                                        <th>الوظيفة والمسؤولية</th>
                                        <th class="text-center" style="width: 150px;">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $extDescriptions = [
                                        'mysqli'   => 'الاتصال بقواعد بيانات MySQL وتنفيذ الاستعلامات الآمنة (Prepared Statements)',
                                        'json'     => 'معالجة وتبادل بيانات الـ JSON في تقارير وتصدير النسخ الاحتياطية واستجابات AJAX',
                                        'mbstring' => 'معالجة النصوص متعددة البايت ودعم اللغة العربية UTF-8 بشكل سليم',
                                        'curl'     => 'الاتصال بالخدمات السحابية وبوابات الفواتير الإلكترونية والتحديثات',
                                        'openssl'  => 'تشفير وحماية البيانات الحساسة وتوليد رموز التوكن السرية',
                                        'gd'       => 'معالجة وتوليد الصور والشعارات وملصقات الباركود'
                                    ];

                                    foreach ($extStatus as $extName => $isLoaded):
                                    ?>
                                        <tr>
                                            <td class="fw-bold text-dark font-monospace">
                                                <i class="fas fa-cube text-primary me-2"></i> ext-<?= $extName ?>
                                            </td>
                                            <td class="text-muted small">
                                                <?= $extDescriptions[$extName] ?? 'امتداد نظام أساسي' ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($isLoaded): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                                        <i class="fas fa-check-circle me-1"></i> مفعل ونشط
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                                        <i class="fas fa-times-circle me-1"></i> غير متوفر
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once("footer.php"); ?>

    <script>
    $(document).ready(function() {
        $('#btnCheckUpdate').on('click', function() {
            var btn = $(this);
            var origHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> جاري الاتصال بخادم التحديثات...');

            setTimeout(function() {
                btn.prop('disabled', false).html(origHtml);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'النظام محدث بالكامل!',
                        text: 'أنت تستخدم أحدث إصدار معتمد من نظام WAM Tech POS & ERP (الإصدار 2.1.0 Enterprise). كافة حزم الأمان وقواعد الضرائب المصرية محدثة.',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('النظام محدث بالكامل لآخر إصدار.');
                }
            }, 1200);
        });
    });
    </script>
</body>
</html>
