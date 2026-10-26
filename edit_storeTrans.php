<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);

$rawInvoice = $_REQUEST['sd'] ?? '';
$decrypted = $adController->encrypt_decrypt(2, urldecode($rawInvoice), 0);
if (!empty($decrypted)) {
    $decrypted2 = $adController->encrypt_decrypt(2, $decrypted, 0);
    $invoice = !empty($decrypted2) ? $decrypted2 : $decrypted;
} else {
    $invoice = $rawInvoice;
}

$safeInvoice = mysqli_real_escape_string($adController->MySQL, $invoice);

$query   = "SELECT * FROM store_trans WHERE trans_number='$safeInvoice' AND storeid='$storeid' LIMIT 1";
$resP    = mysqli_query($adController->MySQL, $query);
$dataPur = mysqli_fetch_assoc($resP);

$itemsListArray = [];
$query = "SELECT * FROM store_trans_items WHERE storeid='$storeid' AND trans_number='$safeInvoice' ORDER BY id ASC";
$ress  = mysqli_query($adController->MySQL, $query);
if ($ress) {
    while ($dataItemsList = mysqli_fetch_assoc($ress)) {
        $itemsListArray[] = $dataItemsList;
    }
}
$numAlready = count($itemsListArray);

// جلب وتخزين الأصناف ووحداتها مؤقتاً باستعلام واحد لتسريع التحميل
$itemsQuery = "SELECT i.id, i.barcode, i.name_ar, i.name_en, u.name_ar AS unit_ar, u.name_en AS unit_en
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
            'name'    => $iName,
            'unit'    => $uName
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=EDIT_STORE_TRANSFAIR?></title>
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
    <script type="text/javascript">
        var numAlready = <?= max(0, $numAlready - 1) ?>;
    </script>    
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=EDIT_STORE_TRANSFAIR?></h2>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="storeTrans-form">
                                <input type="hidden" value="editStoreTrans" name="f">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <fieldset>                                                      
                                    <div class="control-group" style="display:flex; flex-wrap:wrap; gap:20px;">
                                        <div class="salesOptions">
                                            <label class="control-label" for="invoice" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=TRANSFAIR_NUMBER?> : </label>
                                            <div class="controls" style="margin-right:0;">
                                                <input type="text" class="input-medium" name='invoice' id='invoice' value='<?=htmlspecialchars($dataPur['trans_number'] ?? $invoice)?>' readonly style="background:#eee;">
                                            </div>
                                        </div>  
                                        
                                        <div class="salesOptions">
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=INVOICE_DATE?></label>
                                            <div class="controls" style="margin-right:0;">
                                                <?php
                                                $tDate = !empty($dataPur['trans_date']) ? date("d/m/Y", strtotime($dataPur['trans_date'])) : date("d/m/Y");
                                                ?>
                                                <input type="text" class="input-medium datepicker-nobar" name="trans_date" value='<?=htmlspecialchars($tDate)?>'>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group" style="display:flex; flex-wrap:wrap; gap:20px; margin-top:15px;">
                                        <div class="salesOptions" style="min-width:250px;">
                                            <label class="control-label" for="from" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=FROM_STORE?></label>
                                            <div class="controls" style="margin-right:0;">
                                                <select class='span12' name="from" id="from" data-rel="chosen">
                                                    <option value="">-- <?=CHOOSE_STORE ?? 'من مخزن'?> --</option>
                                                    <?php
                                                    $query = "SELECT * FROM stores WHERE storeid='$storeid' ORDER BY id ASC";
                                                    $res   = mysqli_query($adController->MySQL, $query);
                                                    $selectedFrom = $dataPur['storeFrom'] ?? '';
                                                    while ($data = mysqli_fetch_assoc($res)) {
                                                        $name = $data["name_" . $language] ?? $data["name_ar"];
                                                        $sel  = ($data['id'] == $selectedFrom) ? " selected='selected' " : "";
                                                        echo "<option value='" . htmlspecialchars($data['id']) . "' $sel>" . htmlspecialchars($name) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>        
                                        
                                        <div class="salesOptions" style="min-width:250px;">
                                            <label class="control-label" for="to" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=TO_STORE?></label>
                                            <div class="controls" style="margin-right:0;">
                                                <select class='span12' name="to" id="to" data-rel="chosen">
                                                    <option value="">-- <?=CHOOSE_STORE ?? 'إلى مخزن'?> --</option>
                                                    <?php
                                                    if ($res) {
                                                        mysqli_data_seek($res, 0);
                                                        $selectedTo = $dataPur['storeTo'] ?? '';
                                                        while ($data = mysqli_fetch_assoc($res)) {
                                                            $name = $data["name_" . $language] ?? $data["name_ar"];
                                                            $sel  = ($data['id'] == $selectedTo) ? " selected='selected' " : "";
                                                            echo "<option value='" . htmlspecialchars($data['id']) . "' $sel>" . htmlspecialchars($name) . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>                                           
                                    </div>

                                    <div class="control-group" style="margin-top:15px;">
                                        <label class="control-label" for="note" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=NOTES?></label>
                                        <div class="controls" style="margin-right:0;">
                                            <textarea class="span8" name="note" id="note" rows="2"><?=htmlspecialchars($dataPur['note'] ?? '')?></textarea>                                            
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="control-group">
                                        <h4 style="margin-bottom:15px;"><?=INCOMING_ITEMS ?? 'أصناف التحويل المخزني'?></h4>
                                        <div class="controls" style="margin-right:0;">	
                                            <table class="table table-bordered table-striped" style="width:100%;">                                                
                                                <thead>
                                                    <tr style="background:#f5f5f5;">
                                                        <th style="width:50px;">#</th>
                                                        <th style="width:65%;"><?=BARCODE?> | <?=ITEM_NAME?> | <?=UNITS?></th>    
                                                        <th style="width:25%;"><?=QUANTITY?></th>
                                                        <th style="width:35px;"></th>
                                                        <th style="width:35px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                for ($i = 0; $i < 10; $i++) {
                                                    $hasItem = isset($itemsListArray[$i]);
                                                    $rowStyle = ($i > 0 && !$hasItem) ? "style='display:none;'" : "";
                                                    $currentItemId = $hasItem ? $itemsListArray[$i]['itemid'] : '';
                                                    $currentQty    = $hasItem ? $itemsListArray[$i]['quantity'] : '';
                                                ?>
                                                    <tr <?= $rowStyle ?> id='row-v-<?= $i ?>'>
                                                        <td style="text-align:center; vertical-align:middle;"><?= $i + 1 ?></td>
                                                        <td>
                                                            <select id='barcode-<?= $i ?>' name="barcode[]" data-rel="chosen" class="span12" style="width:100%">
                                                                <option value=''>-- اختر الصنف --</option>
                                                                <?php
                                                                foreach ($cachedItems as $item) {
                                                                    $sel = ($item['id'] == $currentItemId) ? " selected='selected' " : "";
                                                                    $optText = "{$item['barcode']} | {$item['name']} | {$item['unit']}";
                                                                    echo "<option value='" . htmlspecialchars($item['id']) . "' $sel>" . htmlspecialchars($optText) . "</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td class="mult-padding">
                                                            <input type="text" id='item-qty-<?= $i ?>' class="item-qty span12" maxlength="100" name='item_qty[]' style="text-align:center;" value="<?= htmlspecialchars($currentQty) ?>">
                                                        </td>
                                                        <td class='del-palance-item mult-padding' prop='<?= $i ?>'><label title="<?=DELETE ?? 'حذف السطر'?>">×</label></td>
                                                        <td class='more-palance-item mult-padding' prop='<?= $i ?>'><label title="<?=ADD ?? 'إضافة سطر'?>">+</label></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>                                  				
                                    <hr>
                                    
                                    <div class="form-actions" style="margin-top:20px;">
                                        <button type="button" id='submit-storeTrans' class="btn btn-primary"><i class="icon-save"></i> <?=SAVE?></button>
                                        <a href="stores-transfer.php" class="btn"><?=CANCEL?></a>
                                    </div>
                                    <p class='error-red'></p>
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
                '.span12': { width: '100%' },
                '.span3':  { width: '95%' }
            };
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
        });
    </script>
</body>
</html>
