<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);

$rawParam = $_REQUEST['d'] ?? '';
$idval    = intval($adController->encrypt_decrypt(2, urldecode($rawParam), 0));

$query        = "SELECT * FROM discount WHERE id='$idval' AND storeid='$storeid' LIMIT 1";
$res          = mysqli_query($adController->MySQL, $query);
$dataDiscount = $res ? mysqli_fetch_assoc($res) : null;

if (!$dataDiscount) {
    header("Location: current_discounts.php");
    exit();
}

$branchId = intval($dataDiscount['branchid'] ?? 0);
$amount   = floatval($dataDiscount['amount'] ?? 0);
$minamt   = floatval($dataDiscount['minamount'] ?? 0);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - تعديل سياسة الخصم</title>
    <?php require_once("script_php_variables.php"); ?>
    <?php require_once("header.php"); ?>	
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
                            <i class="halflings-icon edit"></i>
                            <span class="break"></span>
                            تعديل سياسة الخصم للفرع
                        </h2>
                    </div>
                    <div class="box-content">
                        <form class="form-horizontal" id="update-discount">
                            <input type="hidden" value="discountUpdate" name="f">
                            <input type="hidden" value="<?=htmlspecialchars($rawParam)?>" name="vd">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                            <fieldset>
                                <div class="control-group">
                                    <label class="control-label"><?=BRANCH ?? 'الفرع'?> : <span style="color:#ef4444;">*</span></label>
                                    <div class="controls">
                                        <select name="branch" id="branch-items" class="span5" data-rel="chosen" required>
                                            <option value="">-- اختر الفرع --</option>
                                            <?php
                                            $query = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                            $res   = mysqli_query($adController->MySQL, $query);
                                            if ($res) {
                                                while ($data = mysqli_fetch_assoc($res)) {
                                                    $name = ($language == 'ar' ? $data['name_ar'] : $data['name_en']) ?: $data['name_ar'];
                                                    $sel  = ($branchId === intval($data['id'])) ? "selected" : "";
                                                    echo "<option value='" . htmlspecialchars($data['id']) . "' $sel>" . htmlspecialchars($name) . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="amount"><?=DISCOUNT ?? 'قيمة الخصم'?> : <span style="color:#ef4444;">*</span></label>
                                    <div class="controls">
                                        <input type="number" step="0.01" class="span5" name="amount" id="amount" value="<?=htmlspecialchars($amount)?>" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="minamt"><?=MIN_AMOUNT ?? 'الحد الأدنى لقيمة الفاتورة'?> : <span style="color:#ef4444;">*</span></label>
                                    <div class="controls">
                                        <input type="number" step="0.01" class="span5" name="minamt" id="minamt" value="<?=htmlspecialchars($minamt)?>" required>
                                    </div>
                                </div>

                                <p class="error-red" style="font-weight:bold; color:#ef4444; margin:15px 0;"></p>

                                <div class="form-actions">
                                    <button type="button" id="submit-discount" class="btn btn-primary" style="padding:7px 24px;">
                                        <i class="halflings-icon white ok"></i> <?=SAVE ?? 'حفظ التعديلات'?>
                                    </button>
                                    <a href="current_discounts.php" class="btn" style="padding:7px 18px;"><?=CANCEL ?? 'إلغاء'?></a>
                                </div>
                            </fieldset>
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
