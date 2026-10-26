<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);

// جلب الفروع والمخازن والأصناف ووحداتها مسبقاً لمنع تكرار آلاف الاستعلامات
$branchesQuery = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' ORDER BY id ASC";
$resBranches = mysqli_query($adController->MySQL, $branchesQuery);
$cachedBranches = [];
if ($resBranches) {
    while ($br = mysqli_fetch_assoc($resBranches)) {
        $brName = ($language == 'en' && !empty($br['name_en'])) ? $br['name_en'] : (!empty($br['name_ar']) ? $br['name_ar'] : $br['name_en']);
        $cachedBranches[] = [
            'id'   => $br['id'],
            'name' => $brName
        ];
    }
}

$storesQuery = "SELECT id, name_ar, name_en FROM stores WHERE storeid='$storeid' ORDER BY id ASC";
$resStores = mysqli_query($adController->MySQL, $storesQuery);
$cachedStores = [];
if ($resStores) {
    while ($st = mysqli_fetch_assoc($resStores)) {
        $stName = ($language == 'en' && !empty($st['name_en'])) ? $st['name_en'] : (!empty($st['name_ar']) ? $st['name_ar'] : $st['name_en']);
        $cachedStores[] = [
            'id'   => $st['id'],
            'name' => $stName
        ];
    }
}

$itemsQuery = "SELECT i.id, i.barcode, i.vat, i.name_ar, i.name_en, u.name_ar AS unit_ar, u.name_en AS unit_en
               FROM items i
               LEFT JOIN units u ON u.id = i.unit AND u.storeid = '$storeid'
               WHERE i.storeid = '$storeid'
               ORDER BY i.name_ar ASC";
$resItems = mysqli_query($adController->MySQL, $itemsQuery);
$cachedItems = [];
if ($resItems) {
    while ($it = mysqli_fetch_assoc($resItems)) {
        $bCode = !empty($it['barcode']) ? $it['barcode'] : '-----';
        $iName = ($language == 'en' && !empty($it['name_en'])) ? $it['name_en'] : (!empty($it['name_ar']) ? $it['name_ar'] : $it['name_en']);
        if (empty($iName) || $iName == '`') {
            $iName = '-----';
        }
        $uName = ($language == 'en' && !empty($it['unit_en'])) ? $it['unit_en'] : (!empty($it['unit_ar']) ? $it['unit_ar'] : $it['unit_en']);
        if (empty($uName)) {
            $uName = '-----';
        }
        $cachedItems[] = [
            'id'      => $it['id'],
            'barcode' => $bCode,
            'vat'     => $it['vat'] ?? 0,
            'name'    => $iName,
            'unit'    => $uName
        ];
    }
}

// توليد رقم إذن الوارد
$query = "SELECT income_No FROM income WHERE storeid='$storeid' ORDER BY id DESC LIMIT 1";
$res   = mysqli_query($adController->MySQL, $query);
$num   = $res ? mysqli_num_rows($res) : 0;
$data  = $res ? mysqli_fetch_assoc($res) : null;
if ($num && !empty($data['income_No'])) {
    $rest = substr($data['income_No'], -4);  
    $trans_No = intval($rest) + 1;
} else {
    $trans_No = 1;
}
$time    = time();
$invoice = $storeid . '-' . $time . '-' . str_pad($trans_No, 4, "0", STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=ADD_INCOMING_INVOICE?></title>
    <?php require_once("script_php_variables.php"); ?>
    <?php require_once("header.php"); ?>	
    <style>
        .mult-padding {
            padding: 4px !important;
            text-align: center;
        }
        .del-palance-item, .more-palance-item {
            cursor: pointer;
            text-align: center;
            width: 32px;
        }
        .del-palance-item label, .more-palance-item label {
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            margin: 0;
            display: block;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .del-palance-item label {
            color: #fff;
            background-color: #d9534f;
        }
        .more-palance-item label {
            color: #fff;
            background-color: #5cb85c;
        }
        .error-red {
            color: #d9534f;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=ADD_INCOMING_INVOICE?></h2>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="incoming-form">
                                <input type="hidden" value="addIncoming" name="f">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <fieldset>
                                    <div class="control-group" style="display:flex; flex-wrap:wrap; gap:20px;">
                                        <div class="salesOptions">
                                            <label class="control-label" for="invoice" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=INVOICE_NUMBER?> : </label>
                                            <div class="controls" style="margin-right:0;">
                                                <input type="text" class="input-medium" name='invoice' id='invoice' value='<?=htmlspecialchars($invoice)?>' readonly style="background:#eee;">
                                            </div>
                                        </div>  

                                        <div class="salesOptions">
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=INVOICE_DATE?></label>
                                            <div class="controls" style="margin-right:0;">
                                                <input type="text" class="input-medium datepicker-nobar" name="date" value='<?=date("d/m/Y")?>'>
                                            </div>
                                        </div>

                                        <div class="salesOptions" style="min-width:250px;">
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=BRANCH?> :</label>
                                            <div class="controls" style="margin-right:0;">
                                                <select name="branch" data-rel="chosen" class="span12">
                                                    <option value=''>-- اختر الفرع --</option>
                                                    <?php
                                                    foreach ($cachedBranches as $br) {
                                                        echo "<option value='" . htmlspecialchars($br['id']) . "'>" . htmlspecialchars($br['name']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="control-group">
                                        <h4 style="margin-bottom:15px;"><?=INCOMING_ITEMS?></h4>
                                        <div class="controls" style="margin-right:0;">	
                                            <table class="table table-striped table-bordered" style="width:100%;">                                                
                                                <thead>
                                                    <tr style="background:#f5f5f5;">
                                                        <th style="width:25%;"><?=STORES?></th>
                                                        <th style="width:50%;"><?=BARCODE?> | <?=ITEM_NAME?> | <?=UNITS?></th>    
                                                        <th style="width:15%;"><?=QUANTITY?></th>
                                                        <th style="width:35px;"></th>
                                                        <th style="width:35px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                for ($i = 0; $i < 10; $i++) {
                                                    $rowStyle = ($i > 0) ? "style='display:none;'" : "";
                                                ?>
                                                    <tr <?= $rowStyle ?> id='row-v-<?= $i ?>'>
                                                        <td>
                                                            <select name="store[]" data-rel="chosen" class="span12" style="width:100%;">
                                                                <option value="">-- اختر المخزن --</option>
                                                                <?php
                                                                foreach ($cachedStores as $st) {
                                                                    echo "<option value='" . htmlspecialchars($st['id']) . "'>" . htmlspecialchars($st['name']) . "</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select id='barcode-<?= $i ?>' name="barcode[]" data-rel="chosen" class="barcode-select span12" style="width:100%;">
                                                                <option value=''>-- اختر الصنف --</option>
                                                                <?php
                                                                foreach ($cachedItems as $item) {
                                                                    $val = "{$i}@@{$item['id']}@@{$item['vat']}@@{$item['barcode']}@@{$item['name']}@@{$item['unit']}";
                                                                    $optText = "{$item['barcode']} | {$item['name']} | {$item['unit']}";
                                                                    echo "<option value='" . htmlspecialchars($val) . "'>" . htmlspecialchars($optText) . "</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td class="mult-padding">
                                                            <input type="text" id='item-qty-<?= $i ?>' class="item-qty span12" maxlength="100" name='item_qty[]' style="text-align:center;">
                                                        </td>
                                                        <td class='del-palance-item mult-padding' prop='<?= $i ?>'><label title="<?=DELETE ?? 'حذف'?>">×</label></td>
                                                        <td class='more-palance-item mult-padding' prop='<?= $i ?>'><label title="<?=ADD ?? 'إضافة سطر'?>">+</label></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>                                  				
                                    <hr>

                                    <div class="form-actions">
                                        <button type="button" id='submit-income' class="btn btn-primary"><?=SAVE?></button>
                                        <a href="incoming-entry.php" class="btn"><?=CANCEL?></a>
                                    </div>
                                    <p class='error-red'>&nbsp;</p>
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
            var config = {
                '.span3' : { width: '95%' }
            };
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
        });
    </script>
</body>
</html>
