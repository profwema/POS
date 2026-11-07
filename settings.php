<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid  = intval($_SESSION['storeid'] ?? 1);
$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

$query   = "SELECT * FROM main_settings WHERE storeid='$storeid' LIMIT 1";
$res     = mysqli_query($adController->MySQL, $query);
$dataCat = $res ? mysqli_fetch_assoc($res) : null;

$compName       = htmlspecialchars(!empty($dataCat['name_ar']) ? $dataCat['name_ar'] : ($dataCat['name_en'] ?? ''));
$activity       = htmlspecialchars($dataCat['activity'] ?? '');
$logo           = htmlspecialchars($dataCat['logo'] ?? '');
$site           = htmlspecialchars($dataCat['site'] ?? '');
$smoking        = intval($dataCat['smoking'] ?? 0);
$enableNegative = intval($dataCat['enableNegative'] ?? 0);
$tax_no         = htmlspecialchars($dataCat['tax_no'] ?? '');
$phone          = htmlspecialchars($dataCat['phone'] ?? '');
$email          = htmlspecialchars($dataCat['email'] ?? '');
$address        = htmlspecialchars($dataCat['address'] ?? '');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إعدادات المنشأة والنظام - WAM Tech POS</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .settings-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }
        .settings-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .settings-header i {
            color: #2563eb;
        }
        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }
        .logo-preview-box {
            max-height: 100px;
            max-width: 180px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px;
            margin-top: 10px;
            background: #f8fafc;
            object-fit: contain;
        }
        .policy-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transition: all 0.2s ease;
        }
        .policy-card:hover {
            border-color: #cbd5e1;
            background: #f1f5f9;
        }
        .policy-card input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #2563eb;
            margin-top: 2px;
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
                                <li class="breadcrumb-item active" aria-current="page">الإعدادات العامة</li>
                            </ol>
                        </nav>
                        <h3 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-cogs text-primary me-2"></i>
                            الإعدادات العامة للنظام والمنشأة
                        </h3>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="quote_settings.php" class="btn btn-outline-primary">
                            <i class="fas fa-file-invoice me-1"></i> إعدادات عروض الأسعار
                        </a>
                        <a href="backup.php" class="btn btn-outline-secondary">
                            <i class="fas fa-database me-1"></i> النسخ الاحتياطي
                        </a>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-xl-10">
                        <form id="settingsForm" method="POST" action="controller.php">
                            <input type="hidden" name="f" value="updateSettings">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()); ?>">

                            <!-- 1. بيانات المنشأة الأساسية -->
                            <div class="settings-card">
                                <div class="settings-header">
                                    <i class="fas fa-building"></i> بيانات المنشأة والترخيص التجاري
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label for="name_ar" class="form-label">
                                            <?= defined('CUMPANY_NAME') ? CUMPANY_NAME : 'اسم المنشأة / الشركة' ?> <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name_ar" id="name_ar" class="form-control" value="<?= $compName ?>" placeholder="مثال: شركة وائل للتقنية والتجارة" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="tax_no" class="form-label">
                                            <?= defined('TAX_NO') ? TAX_NO : 'الرقم الضريبي (سجل ضريبة القيمة المضافة)' ?> <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="tax_no" id="tax_no" class="form-control font-monospace" value="<?= $tax_no ?>" placeholder="مثال: 300123456789001" required>
                                        <div class="form-text">الرقم الضريبي المسجل بمصلحة الضرائب المصرية (14% VAT)</div>
                                    </div>

                                    <div class="col-md-7">
                                        <label for="activity" class="form-label">
                                            <?= defined('CUMPANY_ACTIVITY') ? CUMPANY_ACTIVITY : 'النشاط التجاري والصناعي' ?>
                                        </label>
                                        <textarea name="activity" id="activity" class="form-control" rows="3" placeholder="وصف النشاط التجاري وطبيعة عمل المنشأة..."><?= $activity ?></textarea>
                                    </div>

                                    <div class="col-md-5">
                                        <label for="logo" class="form-label">
                                            <?= defined('CUMPANY_LOGO') ? CUMPANY_LOGO : 'رابط شعار المنشأة (Logo URL)' ?>
                                        </label>
                                        <input type="text" name="logo" id="logo" class="form-control" value="<?= $logo ?>" placeholder="img/logo.png أو رابط مباشر">
                                        <div class="mt-2 text-center" id="logoPreviewContainer">
                                            <?php if (!empty($logo)): ?>
                                                <img src="<?= $logo ?>" class="logo-preview-box" id="logoPreview" alt="شعار المنشأة">
                                            <?php else: ?>
                                                <img src="img/logo.png" class="logo-preview-box" id="logoPreview" alt="شعار افتراضي" style="opacity:0.6;">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. بيانات الاتصال والعنوان -->
                            <div class="settings-card">
                                <div class="settings-header">
                                    <i class="fas fa-map-marker-alt"></i> بيانات التواصل والعنوان
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="phone" class="form-label">
                                            <?= defined('PHONE') ? PHONE : 'رقم الهاتف / الخط الساخن' ?> <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="phone" id="phone" class="form-control font-monospace" value="<?= $phone ?>" placeholder="مثال: 01012345678" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="email" class="form-label">
                                            <?= defined('EMAIL') ? EMAIL : 'البريد الإلكتروني الرسمي' ?> <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" name="email" id="email" class="form-control" value="<?= $email ?>" placeholder="info@company.com" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="site" class="form-label">
                                            <?= defined('WEB_SITE') ? WEB_SITE : 'الموقع الإلكتروني' ?>
                                        </label>
                                        <input type="text" name="site" id="site" class="form-control" value="<?= $site ?>" placeholder="www.example.com">
                                    </div>
                                    <div class="col-12">
                                        <label for="address" class="form-label">
                                            <?= defined('ADDRESS') ? ADDRESS : 'العنوان التفصيلي ومقر الإدارة' ?>
                                        </label>
                                        <input type="text" name="address" id="address" class="form-control" value="<?= $address ?>" placeholder="المحافظة، المدينة، اسم الشارع، رقم المبنى">
                                    </div>
                                </div>
                            </div>

                            <!-- 3. سياسات البيع والمخزون والضرائب -->
                            <div class="settings-card">
                                <div class="settings-header">
                                    <i class="fas fa-sliders-h"></i> سياسات البيع والمخزون والضرائب
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="policy-card">
                                            <input type="checkbox" value="1" id="enableNegative" name="enableNegative" <?= $enableNegative === 1 ? "checked" : "" ?>>
                                            <div>
                                                <label for="enableNegative" class="fw-bold mb-1 d-block" style="cursor:pointer;">
                                                    <?= defined('ENABLE_NEGTIVE') ? ENABLE_NEGTIVE : 'السماح بالبيع بالسالب (تجاوز رصيد المخزن)' ?>
                                                </label>
                                                <small class="text-muted">يتيح إصدار فواتير البيع حتى لو كان رصيد الصنف في المخزن صفراً أو غير كافٍ.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="policy-card">
                                            <input type="checkbox" value="1" id="smoking" name="smoking" <?= $smoking === 1 ? "checked" : "" ?>>
                                            <div>
                                                <label for="smoking" class="fw-bold mb-1 d-block" style="cursor:pointer;">
                                                    <?= defined('SMOKING') ? SMOKING : 'تطبيق ضريبة التبغ والمنتجات الخاصة' ?>
                                                </label>
                                                <small class="text-muted">تفعيل الحقول والضرائب النوعية الخاصة بمنتجات التبغ والمشتقات المتخصصة.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Egyptian Market Info Box -->
                                    <div class="col-12 mt-3">
                                        <div class="alert alert-light border d-flex align-items-center gap-3 mb-0" style="border-radius:10px;">
                                            <div class="fs-2 text-success"><i class="fas fa-check-double"></i></div>
                                            <div>
                                                <strong class="text-dark">توافق السوق المصري الافتراضي:</strong>
                                                <span class="text-muted d-block small">العملة الأساسية للنظام: <strong><?= $currency ?> (الجنيه المصري)</strong> | نسبة ضريبة القيمة المضافة: <strong>14% القياسية</strong> | وسائل الدفع الإلكترونية: <strong>فودافون كاش / إنستاباي / فيزا كارت</strong>.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> إلغاء
                                </a>
                                <button type="submit" id="submitBtn" class="btn btn-primary px-4 py-2" style="background:#2563eb; border-color:#2563eb; font-weight:700;">
                                    <i class="fas fa-save me-1"></i> حفظ إعدادات النظام
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once("footer.php"); ?>

    <script>
    $(document).ready(function() {
        // Live logo preview
        $('#logo').on('input change', function() {
            var url = $(this).val();
            if (url) {
                $('#logoPreview').attr('src', url).css('opacity', '1');
            }
        });

        // AJAX Form Submit with SweetAlert2
        $('#settingsForm').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var btn = $('#submitBtn');
            var origHtml = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري حفظ الإعدادات...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(resp) {
                    btn.prop('disabled', false).html(origHtml);
                    var cleanResp = $.trim(resp);

                    if (cleanResp === '1' || cleanResp.indexOf('نجاح') !== -1 || cleanResp.indexOf('SUCCESS') !== -1) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحفظ بنجاح',
                                text: 'تم تحديث إعدادات المنشأة والنظام بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            alert('تم حفظ الإعدادات بنجاح');
                            location.reload();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('خطأ', cleanResp, 'error');
                        } else {
                            alert('خطأ: ' + cleanResp);
                        }
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html(origHtml);
                    alert('حدث خطأ في الاتصال بالسيرفر أثناء حفظ الإعدادات');
                }
            });
        });
    });
    </script>
</body>
</html>
