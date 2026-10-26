<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=PLBS?></title>
    <?php require_once("script_php_variables.php"); ?>
    <?php require_once("header.php"); ?>	
    <style>
        .budget-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 12px;
            background: #f8fafc;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .budget-row label {
            min-width: 80px;
            margin-bottom: 0;
            font-weight: bold;
            color: #334155;
        }
    </style>
</head>
<body>
    <?php require_once("header_top.php"); ?>
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php"); ?>
            <div id="content" class="span10">
                <div class="row-fluid sortable">
                    <div class="box span12">
                        <div class="box-header" data-original-title>
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=PLBS?> - إعدادات القوائم المالية والميزانية</h2>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="update-flbs">
                                <input type="hidden" value="flbsUpdate" name="f">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <fieldset>
                                    <div style="margin-bottom: 20px;">
                                        <p class="alert alert-info" style="font-size:14px;">
                                            <i class="icon-info-sign"></i> يمكنك ضبط وتعديل مسميات القوائم المالية (الميزانية العمومية، الأرباح والخسائر، إلخ) ورموزها أدناه:
                                        </p>
                                    </div>

                                    <?php
                                    $query = "SELECT * FROM budget_set ORDER BY id ASC";
                                    $res   = mysqli_query($adController->MySQL, $query);

                                    $hasRows = false;
                                    if ($res && mysqli_num_rows($res) > 0) {
                                        $hasRows = true;
                                        while ($dataCat = mysqli_fetch_assoc($res)) {
                                            $id     = $dataCat['id'];
                                            $code   = $dataCat['code'] ?? '';
                                            $nameAr = $dataCat['name_ar'] ?? '';
                                            $nameEn = $dataCat['name_en'] ?? '';
                                            ?>
                                            <div class="budget-row">
                                                <input type="hidden" value="<?= htmlspecialchars($id) ?>" name="id[]">
                                                <label>الرمز :</label>
                                                <input type="text" class="input-medium" name="code[]" value="<?= htmlspecialchars($code) ?>" placeholder="رمز البند" readonly style="background:#eee; text-align:center;">
                                                <label>الاسم بالعربية :</label>
                                                <input type="text" class="span5" name="name_ar[]" value="<?= htmlspecialchars($nameAr) ?>" placeholder="اسم البند المالي بالعربية">
                                                <input type="hidden" name="name_en[]" value="<?= htmlspecialchars($nameAr ?: $nameEn) ?>">
                                            </div>
                                            <?php
                                        }
                                    }

                                    // إضافة سطر جديد فارغ لإتاحة إضافة بنود جديدة
                                    $queryCode = "SELECT code FROM budget_set ORDER BY id DESC LIMIT 1";
                                    $resCode   = mysqli_query($adController->MySQL, $queryCode);
                                    $dataCode  = $resCode ? mysqli_fetch_assoc($resCode) : null;
                                    $newCodeNum = 1;
                                    if (!empty($dataCode['code'])) {
                                        $newCodeNum = intval(substr($dataCode['code'], -3)) + 1;
                                    }
                                    $newCode = 'bud' . str_pad($newCodeNum, 3, "0", STR_PAD_LEFT);
                                    ?>
                                    <div class="budget-row" style="background:#f0fdf4; border-color:#bbf7d0;">
                                        <input type="hidden" value="0" name="id[]">
                                        <label style="color:#15803d;">+ بند جديد :</label>
                                        <input type="text" class="input-medium" name="code[]" value="<?= htmlspecialchars($newCode) ?>" placeholder="رمز جديد" readonly style="background:#eee; text-align:center;">
                                        <label style="color:#15803d;">الاسم بالعربية :</label>
                                        <input type="text" class="span5" name="name_ar[]" value="" placeholder="أدخل اسم البند لإضافته">
                                        <input type="hidden" name="name_en[]" value="">
                                    </div>
                                    
                                    <div class="form-actions" style="margin-top:25px;">
                                        <button type="button" id="submit-flbs" class="btn btn-primary"><i class="icon-save"></i> <?=SAVE?></button>
                                        <button type="reset" class="btn"><?=CANCEL?></button>
                                    </div>
                                    <p class="error-red" style="color:#dc2626; font-weight:bold; margin-top:10px;"></p>
                                </fieldset>
                            </form>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
    <div class="clearfix"></div>
    <?php require_once("footer.php"); ?>
</body>
</html>
