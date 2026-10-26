<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);

$rawId = $_REQUEST['sd'] ?? '';
$decrypted = $adController->encrypt_decrypt(2, urldecode($rawId), 0);
$idval = !empty($decrypted) ? $adController->encrypt_decrypt(2, $decrypted, 0) : $rawId;
if (empty($idval)) {
    $idval = $rawId;
}
$cleanId = intval($idval);
$idval2  = urlencode($adController->encrypt_decrypt(1, $rawId, 0));

$query   = "SELECT * FROM expenditure WHERE storeid='$storeid' AND id='$cleanId' LIMIT 1";
$res     = mysqli_query($adController->MySQL, $query);
$dataCat = mysqli_fetch_assoc($res);

$branchArray = [];
if (!empty($dataCat['store_branch'])) {						
    $branchArray = explode(",", $dataCat['store_branch']);
} else {
    $query      = "SELECT GROUP_CONCAT(id) AS br FROM branches WHERE storeid='$storeid'";
    $resBranch  = mysqli_query($adController->MySQL, $query);
    $dataBranch = mysqli_fetch_assoc($resBranch);
    $branchArray = !empty($dataBranch['br']) ? explode(",", $dataBranch['br']) : [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=UPDATE_EXPENDITURE?></title>
    <?php require_once("script_php_variables.php"); ?>
    <?php require_once("header.php"); ?>	
</head>
<body>
    <?php require_once("header_top.php"); ?>
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php"); ?>
            <div id="content" class="span10">
                <div>
                    <div class="box span12">
                        <div class="box-header" data-original-title>
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=UPDATE_EXPENDITURE?></h2>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="add-expenditure">
                                <input type="hidden" value="<?=htmlspecialchars($idval2)?>" name="nd">
                                <input type="hidden" value="updateExpenditure" name="f">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <fieldset>
                                    <div class="control-group">
                                        <label class="control-label" for="branch" style="font-weight:bold;"><?=BRANCH?> :</label>
                                        <div class="controls">
                                            <select name="branch" id='branch-items' class="span6" data-rel="chosen">
                                                <option value=''><?=GENERAL ?? 'عام (كافة الفروع)'?></option>
                                                <?php
                                                $query = "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY id ASC";
                                                $res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
                                                while ($data = mysqli_fetch_assoc($res)) {
                                                    $id   = $data['id'];
                                                    $name = $data['name_' . $language] ?? $data['name_ar'];
                                                    $selected = in_array($id, $branchArray) ? " selected='selected' " : "";
                                                    echo "<option value='" . htmlspecialchars($id) . "' $selected>" . htmlspecialchars($name) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="desc_ar" style="font-weight:bold;"><?=DESCRIPTION?> : </label>
                                        <div class="controls">
                                            <input type="text" class="span6" name='desc_ar' id='desc_ar' value="<?=htmlspecialchars($dataCat['desc_ar'] ?? $dataCat['desc_en'] ?? '')?>" required>
                                            <input type="hidden" name='desc_en' id='desc_en' value="<?=htmlspecialchars($dataCat['desc_en'] ?? $dataCat['desc_ar'] ?? '')?>">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="cost" style="font-weight:bold;"><?=COST?> :</label>
                                        <div class="controls">
                                            <input type="text" name='cost' id="cost" class="float-val span6" value="<?=htmlspecialchars($dataCat['cost'] ?? '')?>" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="dated" style="font-weight:bold;"><?=EXPENDITURE_DATE?> :</label>
                                        <div class="controls">
                                            <?php
                                            $eDate = !empty($dataCat['dated']) ? date("d/m/Y", strtotime($dataCat['dated'])) : date("d/m/Y");
                                            ?>
                                            <input type="text" class="input-large datepicker datepicker-nobar" name="dated" id="dated" value="<?=htmlspecialchars($eDate)?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-actions" style="margin-top:25px;">
                                        <button type="button" id='submit-expenditure' class="btn btn-primary"><i class="icon-save"></i> <?=SAVE?></button>
                                        <a href="expenditure.php" class="btn"><?=CANCEL?></a>
                                    </div>
                                    <p class='error-red' style="color:#dc2626; font-weight:bold; margin-top:10px;"></p>
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
    <script type="text/javascript">
    $(document).ready(function() {
        $("#desc_ar").on("input change", function() {
            $("#desc_en").val($(this).val());
        });
    });
    </script>
</body>
</html>
