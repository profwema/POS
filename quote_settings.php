<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);

$query   = "SELECT * FROM quote_settings WHERE storeid='$storeid' LIMIT 1";
$res     = mysqli_query($adController->MySQL, $query);
$dataCat = $res ? mysqli_fetch_assoc($res) : null;

$barcode  = intval($dataCat['barcode'] ?? 0);
$unit     = intval($dataCat['unit'] ?? 0);
$disc     = intval($dataCat['disc'] ?? 0);
$img      = intval($dataCat['img'] ?? 0);
$vatValue = intval($dataCat['vat_value'] ?? 0);
$vatRate  = intval($dataCat['vat_rate'] ?? 0);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - إعدادات عروض الأسعار</title>
    <?php require_once("script_php_variables.php"); ?>
    <?php require_once("header.php"); ?>	
    <style>
        .setting-switch-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .option-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .option-item {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .option-item:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .option-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-left: 12px;
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
    <?php require_once("header_top.php"); ?>
    
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php"); ?>
            <div id="content" class="span10">
                <div class="box span12">
                    <div class="box-header" data-original-title>
                        <h2>
                            <i class="halflings-icon file"></i>
                            <span class="break"></span>
                            إعدادات طباعة وعرض عروض الأسعار
                        </h2>
                    </div>
                    <div class="box-content">
                        <form class="form-horizontal" id="q-settings">
                            <input type="hidden" name="f" value="updateQSettings">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                            
                            <div class="setting-switch-card">
                                <h3 style="margin-top:0; font-size:16px; color:#1e293b; border-bottom:2px solid #f1f5f9; padding-bottom:10px;">
                                    <i class="halflings-icon check"></i> خيارات ظهور الأعمدة والبيانات في عرض السعر
                                </h3>
                                <p style="color:#64748b; font-size:13px; margin-top:5px;">
                                    حدد الحقول التي ترغب في إظهارها تلقائياً عند إنشاء وطباعة مستندات عروض الأسعار للعملاء:
                                </p>
                                
                                <div class="option-grid">
                                    <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                        <input type="checkbox" value="1" name="barcode" id="chk_barcode" <?=$barcode === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                        <label for="chk_barcode"><?=BARCODE ?? 'رمز الباركود للسلعة'?></label>
                                    </div>

                                    <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                        <input type="checkbox" value="1" name="unit" id="chk_unit" <?=$unit === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                        <label for="chk_unit"><?=UNITS ?? 'وحدة قياس الصنف (حبة، كرتون...)'?></label>
                                    </div>

                                    <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                        <input type="checkbox" value="1" name="disc" id="chk_disc" <?=$disc === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                        <label for="chk_disc"><?=DISCRIPTION ?? 'وصف وتفاصيل الصنف'?></label>
                                    </div>

                                    <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                        <input type="checkbox" value="1" name="img" id="chk_img" <?=$img === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                        <label for="chk_img"><?=IMAGE ?? 'صورة المنتج المعروض'?></label>
                                    </div>

                                    <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                        <input type="checkbox" value="1" name="vatRate" id="chk_vatRate" <?=$vatRate === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                        <label for="chk_vatRate"><?=VAT_RATIO ?? 'نسبة ضريبة القيمة المضافة (%)'?></label>
                                    </div>

                                    <div class="option-item" onclick="var cb = $(this).find('input')[0]; cb.checked = !cb.checked;">
                                        <input type="checkbox" value="1" name="vatValue" id="chk_vatValue" <?=$vatValue === 1 ? 'checked' : ''?> onclick="event.stopPropagation();">
                                        <label for="chk_vatValue"><?=VAT_VALUE ?? 'قيمة مبلغ الضريبة المستحق'?></label>
                                    </div>
                                </div>
                            </div>

                            <p class="error-red" style="font-weight:bold; color:#ef4444; margin-bottom:15px;"></p>

                            <div class="form-actions" style="background:none; border:none; padding:0;">
                                <button type="button" id="submit-Qsettings" class="btn btn-primary" style="padding:8px 24px; font-weight:bold;">
                                    <i class="halflings-icon white ok"></i> <?=SAVE ?? 'حفظ خيارات العرض'?>
                                </button>
                                <button type="reset" class="btn" style="padding:8px 18px;"><?=CANCEL ?? 'إلغاء'?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix"></div>
    
    <?php require_once("footer.php"); ?>
</body>
</html>
