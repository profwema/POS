<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);

$query   = "SELECT * FROM quote_settings WHERE storeid='$storeid' LIMIT 1";
$res     = mysqli_query($adController->MySQL, $query);
$dataCat = $res ? mysqli_fetch_assoc($res) : null;

$barcode  = (int)($dataCat['barcode'] ?? 0);
$unit     = (int)($dataCat['unit'] ?? 0);
$disc     = (int)($dataCat['disc'] ?? 0);
$img      = (int)($dataCat['img'] ?? 0);
$vatValue = (int)($dataCat['vat_value'] ?? 0);
$vatRate  = (int)($dataCat['vat_rate'] ?? 0);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إعدادات طباعة وعرض عروض الأسعار - ERP WAM Tech</title>
    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .setting-switch-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .option-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }
        .option-item {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .option-item:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .option-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-left: 14px;
            cursor: pointer;
        }
        .option-item label {
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            user-select: none;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php require_once("components/sidebar_pro.php"); ?>
        <div style="flex: 1; display: flex; flex-direction: column;">
            <?php require_once("components/header_pro.php"); ?>
            <main class="admin-content">
                
                <!-- Page Breadcrumbs -->
                <div class="page-header d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="page-title fs-4 fw-bold mb-1">
                            <i class="fas fa-sliders-h text-primary me-2"></i>
                            إعدادات طباعة وعرض عروض الأسعار
                        </h1>
                        <div class="page-breadcrumb text-muted fs-7">
                            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-home"></i> الرئيسية</a>
                            <span class="mx-2">/</span>
                            <a href="quotations.php" class="text-decoration-none text-muted">عروض الأسعار</a>
                            <span class="mx-2">/</span>
                            <span class="text-primary fw-semibold">خيارات الطباعة</span>
                        </div>
                    </div>
                    <div>
                        <a href="quotations.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة لعروض الأسعار
                        </a>
                    </div>
                </div>

                <div class="setting-switch-card">
                    <h3 class="fs-5 fw-bold text-dark border-bottom pb-3 mb-2">
                        <i class="fas fa-check-square text-success me-2"></i> خيارات ظهور الأعمدة والبيانات في عرض السعر
                    </h3>
                    <p class="text-muted fs-7 mb-4">
                        حدد الحقول التي ترغب في إظهارها تلقائياً عند إنشاء وطباعة مستندات عروض الأسعار الرسمية للعملاء:
                    </p>

                    <form id="q-settings" autocomplete="off">
                        <input type="hidden" name="f" value="updateQSettings">
                        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars(csrf_token())?>">

                        <div class="option-grid">
                            <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                <input type="checkbox" value="1" name="barcode" id="chk_barcode" <?=$barcode === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                <label for="chk_barcode"><i class="fas fa-barcode text-secondary me-2"></i> رمز الباركود للصنف</label>
                            </div>

                            <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                <input type="checkbox" value="1" name="unit" id="chk_unit" <?=$unit === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                <label for="chk_unit"><i class="fas fa-balance-scale text-secondary me-2"></i> وحدة قياس الصنف (قطعة، طرد، كرتون)</label>
                            </div>

                            <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                <input type="checkbox" value="1" name="disc" id="chk_disc" <?=$disc === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                <label for="chk_disc"><i class="fas fa-align-right text-secondary me-2"></i> وصف وتفاصيل الصنف الإضافية</label>
                            </div>

                            <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                <input type="checkbox" value="1" name="img" id="chk_img" <?=$img === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                <label for="chk_img"><i class="fas fa-image text-secondary me-2"></i> صورة المنتج المعروض</label>
                            </div>

                            <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                <input type="checkbox" value="1" name="vatRate" id="chk_vatRate" <?=$vatRate === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                <label for="chk_vatRate"><i class="fas fa-percentage text-danger me-2"></i> نسبة ضريبة القيمة المضافة 14%</label>
                            </div>

                            <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                <input type="checkbox" value="1" name="vatValue" id="chk_vatValue" <?=$vatValue === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                <label for="chk_vatValue"><i class="fas fa-coins text-warning me-2"></i> قيمة مبلغ الضريبة المستحق</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4 pt-3 border-top">
                            <button type="submit" id="submit-Qsettings" class="btn btn-primary px-4 fw-bold">
                                <i class="fas fa-check me-1"></i> حفظ خيارات العرض والطباعة
                            </button>
                            <a href="quotations.php" class="btn btn-light px-4">إلغاء</a>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </div>

    <?php require_once("footer.php"); ?>
    <script>
        document.getElementById('q-settings').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submit-Qsettings');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري الحفظ...';

            $.ajax({
                url: 'controller.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check me-1"></i> حفظ خيارات العرض والطباعة';
                    if ($.trim(res) == '1') {
                        alert('تم حفظ إعدادات عروض الأسعار بنجاح!');
                        window.location.href = 'quotations.php';
                    } else {
                        alert('تنبيه: ' + res);
                    }
                },
                error: function(xhr) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check me-1"></i> حفظ خيارات العرض والطباعة';
                    alert('حدث خطأ في الاتصال بالسيرفر: ' + xhr.statusText);
                }
            });
        });
    </script>
</body>
</html>
