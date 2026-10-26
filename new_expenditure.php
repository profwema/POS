<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=ADD_EXPENDITURE?></title>
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=ADD_EXPENDITURE?></h2>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="add-expenditure">
                                <input type="hidden" value="addExpenditure" name="f">
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
                                                    echo "<option value='" . htmlspecialchars($id) . "'>" . htmlspecialchars($name) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="desc_ar" style="font-weight:bold;"><?=DESCRIPTION?> : </label>
                                        <div class="controls">
                                            <input type="text" class="span6" name='desc_ar' id='desc_ar' placeholder="وصف / بيان المصروف" required>
                                            <input type="hidden" name='desc_en' id='desc_en'>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="cost" style="font-weight:bold;"><?=COST?> :</label>
                                        <div class="controls">
                                            <input type="text" name='cost' id="cost" class="float-val span6" placeholder="0.00" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="dated" style="font-weight:bold;"><?=EXPENDITURE_DATE?> :</label>
                                        <div class="controls">
                                            <input type="text" class="input-large datepicker datepicker-nobar" name="dated" id="dated" value="<?=date("d/m/Y")?>">
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
