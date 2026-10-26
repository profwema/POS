<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);

// جلب وتخزين الأصناف ووحداتها مؤقتاً باستعلام واحد لتسريع التحميل ومنع آلاف الاستعلامات المتكررة
$nameCol = "name_" . $language;
$itemsQuery = "SELECT i.id, i.barcode, i.vat, i.name_ar, i.name_en, 
                      u.name_ar AS unit_ar, u.name_en AS unit_en
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
            'id'       => $it['id'],
            'vat'      => floatval($it['vat'] ?? 0),
            'barcode'  => $bCode,
            'name'     => $iName,
            'unit'     => $uName
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=NEW_ITEM?></title>
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=OPENING_PALANCE?> - <?=NEW_ITEM?></h2>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" id="openPalance-form">
                                <input type="hidden" value="addOpenPalance" name="f">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <fieldset>                                    
                                    <div class="control-group">
                                        <div class="salesOptions" style="display:inline-block;">
                                            <label class="control-label" for="store" style="width:auto; float:none; margin-left:10px; display:inline-block; font-weight:bold;"><?=STORES?></label>
                                            <div class="controls" style="margin-left:0; display:inline-block;">
                                                <select class='span12' name="store" id="store" style="min-width:220px;">
                                                    <option value="">-- <?=CHOOSE_STORE ?? 'اختر المخزن'?> --</option>
                                                    <?php
                                                    $query = "SELECT * FROM stores WHERE storeid='$storeid' ORDER BY id ASC";
                                                    $res = mysqli_query($adController->MySQL, $query);
                                                    $currStore = $_SESSION['store'] ?? '';
                                                    while ($data = mysqli_fetch_assoc($res)) {
                                                        $name = $data["name_" . $language] ?? $data["name_ar"];
                                                        $sel = "";                                                                
                                                        if ($currStore != '' && $currStore == $data['id']) {
                                                            $sel = " selected='selected' ";
                                                        } elseif ($currStore == '' && ($data["defualt"] ?? 0) == 1) {
                                                            $_SESSION['store'] = $data['id'];
                                                            $sel = " selected='selected' ";
                                                        }
                                                        echo "<option value='" . htmlspecialchars($data['id']) . "' $sel>" . htmlspecialchars($name) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>                                                                               
                                    </div>
                                    <hr>                                                    
                                    <div class="control-group">
                                        <h4 style="margin-bottom:15px;"><?=ITEMS?></h4>
                                        <div class="controls" style="margin-left:0;">	
                                            <table class="table table-bordered table-striped" style="width:100%">                                                
                                                <thead>
                                                    <tr style="background:#f5f5f5;">
                                                        <th style="width:38%"><?=BARCODE?> | <?=ITEM_NAME?> | <?=UNITS?></th>    
                                                        <th style="width:12%"><?=QTY?></th>
                                                        <th style="width:12%"><?=COST_PRICE?></th>
                                                        <th style="width:10%"><?=DISCOUNT?> %</th>
                                                        <th style="width:10%"><?=VAT?> %</th>
                                                        <th style="width:12%"><?=TOTAL?></th>
                                                        <th style="width:3%"></th>
                                                        <th style="width:3%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                for ($i = 0; $i < 10; $i++) {
                                                    $rowStyle = ($i > 0) ? "style='display:none;'" : "";
                                                ?>
                                                    <tr <?= $rowStyle ?> id='row-v-<?= $i ?>'>
                                                        <td>
                                                            <select name="barcode[]" data-rel="chosen" class="barcode-select span12" style="width:100%">
                                                                <option value=''>-- اختر الصنف --</option>
                                                                <?php
                                                                foreach ($cachedItems as $item) {
                                                                    $optVal = "{$i}@@{$item['id']}@@{$item['vat']}@@{$item['barcode']}@@{$item['name']}@@{$item['unit']}";
                                                                    $optText = "{$item['barcode']} | {$item['name']} | {$item['unit']}";
                                                                    echo "<option value='" . htmlspecialchars($optVal) . "'>" . htmlspecialchars($optText) . "</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td class='mult-padding'>
                                                            <input type="text" id='item-qty-<?= $i ?>' class="item-qty span12" maxlength="100" name='item_qty[]' style="text-align:center;">
                                                        </td>
                                                        <td class='mult-padding'>
                                                            <input type="text" id='item-price-<?= $i ?>' class="item-price span12" maxlength="100" name='item_price[]' style="text-align:center;">
                                                        </td>
                                                        <td class='mult-padding'>
                                                            <input type="text" id='item-discount-<?= $i ?>' class="item-discount span12" maxlength="100" name='item_disc[]' style="text-align:center;" value="0">
                                                        </td>
                                                        <td class='mult-padding'>
                                                            <input type="text" id='item-vat-<?= $i ?>' class="item-vat span12" maxlength="100" name='item_vat[]' readonly style="text-align:center; background:#eee;">
                                                        </td>
                                                        <td class='mult-padding'>
                                                            <input type="text" id='item-total-<?= $i ?>' class="item-total span12" maxlength="100" name='item-total[]' readonly style="text-align:center; font-weight:bold; background:#eee;">
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
                                        <button type="button" id='submit-openPalance' class="btn btn-primary"><i class="icon-save"></i> <?=SAVE?></button>
                                        <a href="open-palance.php" class="btn"><?=CANCEL?></a>
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
                '.barcode-select': { width: '100%' }
            };
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
            
            $(".item-qty, .item-price, .item-discount").on("blur change keyup", function() {
                var id = $(this).attr('id').split('-').pop();
                if (typeof totalItemOpenPalance === 'function') {
                    totalItemOpenPalance(id);
                }
            });
        });
    </script>
</body>
</html>
