<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid  = intval($_SESSION['storeid'] ?? 1);
$language = $_SESSION['lang'] ?? 'ar';

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
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - الإعدادات العامة</title>
    <?php require_once("script_php_variables.php"); ?>
    <?php require_once("header.php"); ?>	
    <style>
        .settings-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .settings-card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 16px;
            color: #1e293b;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }
        .form-group {
            flex: 1;
            min-width: 250px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #475569;
            font-size: 13px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-top: 25px;
        }
        .checkbox-group input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .logo-preview {
            max-height: 80px;
            max-width: 150px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 4px;
            margin-top: 8px;
            background: #f8fafc;
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
                            <i class="halflings-icon cogs"></i>
                            <span class="break"></span>
                            الإعدادات العامة للنظام والمنشأة
                        </h2>
                    </div>
                    <div class="box-content">
                        <form id="settings">
                            <input type="hidden" name="f" value="updateSettings">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                            
                            <!-- 1. بيانات المنشأة -->
                            <div class="settings-card">
                                <h3><i class="halflings-icon home"></i> بيانات المنشأة الأساسية</h3>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2;">
                                        <label for="name"><?=CUMPANY_NAME ?? 'اسم المنشأة / الشركة'?> <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="name_ar" id="name" value="<?=$compName?>" placeholder="مثال: مطعم ومطبخ السعادة" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="tax_no"><?=TAX_NO ?? 'الرقم الضريبي'?> <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="tax_no" id="tax_no" value="<?=$tax_no?>" placeholder="300000000000003" onkeypress="return isNumber(event)" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="activity"><?=CUMPANY_ACTIVITY ?? 'النشاط التجاري'?></label>
                                        <textarea name="activity" id="activity" rows="3" placeholder="وصف نشاط المنشأة التجاري"><?=$activity?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="logo"><?=CUMPANY_LOGO ?? 'رابط الشعار (URL)'?></label>
                                        <input type="url" name="logo" id="logo" value="<?=$logo?>" placeholder="https://example.com/logo.png">
                                        <?php if (!empty($logo)): ?>
                                            <div>
                                                <img src="<?=$logo?>" class="logo-preview" alt="الشعار">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. بيانات التواصل والعنوان -->
                            <div class="settings-card">
                                <h3><i class="halflings-icon phone"></i> بيانات التواصل والعنوان</h3>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="phone"><?=PHONE ?? 'رقم الهاتف / الجوال'?> <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="phone" id="phone" value="<?=$phone?>" maxlength="15" onkeypress="return isNumber(event)" placeholder="0500000000" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email"><?=EMAIL ?? 'البريد الإلكتروني'?> <span style="color:#ef4444;">*</span></label>
                                        <input type="email" name="email" id="email" value="<?=$email?>" placeholder="info@example.com" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="site"><?=WEB_SITE ?? 'الموقع الإلكتروني'?></label>
                                        <input type="text" name="site" id="site" value="<?=$site?>" placeholder="www.example.com">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="address"><?=ADDRESS ?? 'العنوان الوطني / التفصيلي'?></label>
                                        <input type="text" name="address" id="address" value="<?=$address?>" placeholder="المدينة، الحي، اسم الشارع">
                                    </div>
                                </div>
                            </div>

                            <!-- 3. خيارات النظام العامة -->
                            <div class="settings-card">
                                <h3><i class="halflings-icon wrench"></i> خيارات وسياسات النظام العامة</h3>
                                <div class="form-row">
                                    <div class="form-group">
                                        <div class="checkbox-group">
                                            <input type="checkbox" value="1" id="enableNegative" name="enableNegative" <?=$enableNegative === 1 ? "checked" : ""?>>
                                            <label for="enableNegative" style="margin-bottom:0; cursor:pointer;"><?=ENABLE_NEGTIVE ?? 'السماح بالبيع بالسالب (تجاوز رصيد المخزن)'?></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox-group">
                                            <input type="checkbox" value="1" id="smoking" name="smoking" <?=$smoking === 1 ? "checked" : ""?>>
                                            <label for="smoking" style="margin-bottom:0; cursor:pointer;"><?=SMOKING ?? 'تطبيق ضريبة التبغ / المشتقات الخاصة'?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="error-red" style="font-weight:bold; color:#ef4444; margin-bottom:15px;"></p>

                            <div class="form-actions" style="background:none; border:none; padding:0;">
                                <button type="button" id="submit-settings" class="btn btn-primary" style="padding:8px 24px; font-weight:bold;">
                                    <i class="halflings-icon white ok"></i> <?=SAVE ?? 'حفظ الإعدادات'?>
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
