<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);

// مسح جداول التقارير المؤقتة للأرصدة الافتتاحية بأمان
$query = "TRUNCATE TABLE report_open_palance";
mysqli_query($adController->MySQL, $query);
$query = "TRUNCATE TABLE report_open_palance_all";
mysqli_query($adController->MySQL, $query);

if (isset($_REQUEST["store"])) {
    $_SESSION['store'] = trim($_REQUEST["store"]);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=OPENING_PALANCE?></title>
    <?php require_once("header.php"); ?>
    <style>
        .totalAll {
            width: 140px;
            border: #D73D3D solid 1px;
            padding: 8px 15px;
            margin-right: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #D73D3D;
            background: #fff5f5;
            text-align: center;
            border-radius: 4px;
        }
        .salesOptions {
            display: inline-block;
            margin-left: 20px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <?php require_once("header_top.php"); ?>
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php"); ?>
            <div class="span10" id="content">
                <div>
                    <div class="box span12">
                        <div class="box-header" data-original-title="">
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=OPENING_PALANCE?></h2>
                        </div>
                        <div class="box-content">
                            <p align="left">
                                <a class="btn btn-success" href="add_open_palance.php" title="<?=NEW_ITEM?>">
                                    <i class="icon-plus"></i> <?=NEW_ITEM?>
                                </a>
                            </p>
                            <div class="box-content">
                                <form action="open-palance.php" class="form-horizontal" id='form' method="post" name="form">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                    <fieldset>
                                        <div class="control-group" style="display:flex; align-items:center; flex-wrap:wrap; gap:15px;">
                                            <div class="salesOptions" style="min-width:220px;">
                                                <label class="control-label" for="store" style="width:auto; float:none; margin-bottom:5px;"><?=STORES?></label>
                                                <div class="controls" style="margin-right:0;">
                                                    <select class='span12' name="store" id="store" onchange="this.form.submit()">
                                                        <option value="">-- <?=ALL_STORES ?? 'كل المخازن'?> --</option>
                                                        <?php
                                                        $query = "SELECT * FROM stores WHERE storeid='$storeid' ORDER BY id ASC";
                                                        $res   = mysqli_query($adController->MySQL, $query);
                                                        $currentStore = $_SESSION['store'] ?? '';
                                                        while ($data = mysqli_fetch_assoc($res)) {
                                                            $name = $data["name_" . $language] ?? $data["name_ar"];
                                                            $sel = "";
                                                            if ($currentStore != '' && $currentStore == $data['id']) {
                                                                $sel = " selected='selected' ";
                                                            } elseif ($currentStore == '' && ($data["defualt"] ?? 0) == 1) {
                                                                $_SESSION['store'] = $data['id'];
                                                                $currentStore = $data['id'];
                                                                $sel = " selected='selected' ";
                                                            }
                                                            echo "<option value='" . htmlspecialchars($data['id']) . "' $sel>" . htmlspecialchars($name) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="salesOptions" style="min-width:240px;">
                                                <?php 
                                                    $queryDate = "SELECT * FROM open_palance_date WHERE storeid='$storeid' ORDER BY id DESC LIMIT 1";
                                                    $resDate   = mysqli_query($adController->MySQL, $queryDate);
                                                    $dataDate  = mysqli_fetch_assoc($resDate);
                                                    $open_palance_date = !empty($dataDate['date']) ? date("d/m/Y", strtotime($dataDate['date'])) : date("d/m/Y");
                                                    $_SESSION['open_palance_date'] = $open_palance_date;
                                                ?>
                                                <label class="control-label" for="palance-date" style="width:auto; float:none; margin-bottom:5px;"><?=DATE?></label>						
                                                <div class="controls" style="margin-right:0; display:flex; gap:5px;">
                                                    <input type="text" class="input-medium datepicker-nobar" name="palance-date" id="palance-date" value='<?=htmlspecialchars($_SESSION['open_palance_date'])?>' style="margin-bottom:0;">
                                                    <button type="button" id='open-palance-date' class="btn btn-primary"><?=EDIT?></button>
                                                </div>
                                            </div>  

                                            <div class="salesOptions" style="min-width:160px;">
                                                <label class="control-label" style="width:auto; float:none; margin-bottom:5px;"><?=TOTAL?></label>
                                                <div class="totalAll" id="totalAll">0.00</div>
                                            </div>  

                                            <div class="salesOptions" style="padding-top:22px;">
                                                <button type="submit" id='report' name='report' class="btn btn-dark" title="<?=REPORT ?? 'طباعة التقرير'?>">
                                                    <i class='halflings-icon white list-alt'></i> <?=REPORT ?? 'تقرير'?>
                                                </button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>

                            <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th><?=STORE?></th>                                        
                                        <th><?=ITEM_NAME?></th>
                                        <th><?=UNIT?></th>
                                        <th><?=QTY?></th>
                                        <th><?=PRICE?></th>
                                        <th><?=DISCOUNT?> %</th>
                                        <th><?=TOTAL?></th> 
                                        <th style="width:110px;"><?=ACTIONS ?? 'العمليات'?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $storeSearch = '';
                                    if (!empty($_SESSION['store'])) {
                                        $cleanStore = intval($_SESSION['store']);
                                        $storeSearch = " AND op.store = $cleanStore ";
                                    }

                                    $allTotal = 0;
                                    $i = 1;
                                    $reportInserts = [];

                                    // استعلام موحد مع LEFT JOIN لمنع مشكلة N+1 استعلام
                                    $query = "SELECT op.*, 
                                                     s.name_ar AS store_name_ar, s.name_en AS store_name_en,
                                                     i.barcode, i.intimate_stock, i.name_ar AS item_name_ar, i.name_en AS item_name_en,
                                                     u.name_ar AS unit_name_ar, u.name_en AS unit_name_en
                                              FROM open_palance op
                                              LEFT JOIN stores s ON s.id = op.store AND s.storeid = '$storeid'
                                              LEFT JOIN items i ON i.id = op.itemid AND i.storeid = '$storeid'
                                              LEFT JOIN units u ON u.id = i.unit AND u.storeid = '$storeid'
                                              WHERE op.storeid = '$storeid' $storeSearch
                                              ORDER BY op.id ASC";

                                    $res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));

                                    while ($data = mysqli_fetch_assoc($res)) {
                                        $idval      = urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
                                        $tableName  = urlencode($adController->encrypt_decrypt(1, 'open_palance', 0));

                                        $barcode    = $data["barcode"] ?? '';
                                        $count      = floatval($data["intimate_stock"] ?? 0);

                                        $itemName   = ($language == 'ar' ? $data['item_name_ar'] : $data['item_name_en']) ?? '';
                                        if (empty($itemName) || $itemName == '`') {
                                            $itemName = !empty($data['item_name_ar']) ? $data['item_name_ar'] : ($data['item_name_en'] ?? '');
                                        }

                                        $unitName   = ($language == 'ar' ? $data['unit_name_ar'] : $data['unit_name_en']) ?? '';
                                        if (empty($unitName)) {
                                            $unitName = !empty($data['unit_name_ar']) ? $data['unit_name_ar'] : ($data['unit_name_en'] ?? '');
                                        }

                                        $storeName  = ($language == 'ar' ? $data['store_name_ar'] : $data['store_name_en']) ?? '';
                                        if (empty($storeName)) {
                                            $storeName = !empty($data['store_name_ar']) ? $data['store_name_ar'] : ($data['store_name_en'] ?? '');
                                        }

                                        $quantity   = floatval($data['quantity'] ?? 0);
                                        $cost       = floatval($data['cost'] ?? 0);
                                        $discount   = floatval($data['discount'] ?? 0);

                                        $preTotal   = $quantity * $cost;
                                        $discValue  = $preTotal * $discount / 100;
                                        $discTotal  = $preTotal - $discValue;
                                        $allTotal  += $discTotal;

                                        // تجهيز إدراج تقرير الأرصدة
                                        $safeStoreName = mysqli_real_escape_string($adController->MySQL, $storeName);
                                        $safeBarcode   = mysqli_real_escape_string($adController->MySQL, $barcode);
                                        $safeItemName  = mysqli_real_escape_string($adController->MySQL, $itemName);
                                        $safeUnitName  = mysqli_real_escape_string($adController->MySQL, $unitName);

                                        $reportQuery = "INSERT INTO `report_open_palance` (`id`, `store`, `barcode`, `item`, `unit`, `count`, `quantity`, `cost`, `discount`, `total`) VALUES ('$i', '$safeStoreName', '$safeBarcode', '$safeItemName', '$safeUnitName', '$count', '$quantity', '$cost', '$discount', '$discTotal')";
                                        mysqli_query($adController->MySQL, $reportQuery);
                                        ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= htmlspecialchars($storeName) ?></td>
                                            <td>
                                                <input type="hidden" id="item-id-<?= $i ?>" value='<?= htmlspecialchars($data['id']) ?>' name='id[]'/>
                                                <?= htmlspecialchars($itemName) ?>
                                            </td>
                                            <td><?= htmlspecialchars($unitName) ?></td>
                                            <td>
                                                <input type="text" id='item-qty-<?= $i ?>' class="item-qty span12" maxlength="20" name='item_qty[]' style="text-align:center;" value="<?= $quantity ?>">
                                            </td>
                                            <td>
                                                <input type="text" id='item-price-<?= $i ?>' class="item-price span12" maxlength="20" name='item_price[]' style="text-align:center;" value="<?= $cost ?>">
                                            </td>
                                            <td>
                                                <input type="text" id='item-discount-<?= $i ?>' class="item-discount span12" maxlength="20" name='item_disc[]' style="text-align:center;" value="<?= $discount ?>">
                                            </td>
                                            <td id='item-total-<?= $i ?>' style="font-weight:bold; text-align:center;">
                                                <?= number_format($discTotal, 2, '.', '') ?>
                                            </td>                                                    
                                            <td class='center' style="text-align:center;">                                                        
                                                <a class='item-edit btn btn-info' id='item-edit-<?= $i ?>' href='javascript:void(0)' title="<?=EDIT?>">
                                                    <i class='halflings-icon white edit'></i>  
                                                </a>
                                                <a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteData("<?= $tableName ?>", "<?= $idval ?>");' title="<?=DELETE ?? 'حذف'?>">
                                                    <i class='halflings-icon white trash'></i> 
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (empty($_SESSION['store'])) {
        $store = 'كل المخازن';
    } else {
        $cleanStore = intval($_SESSION['store']);
        $query     = "SELECT * FROM stores WHERE id='$cleanStore' AND storeid='$storeid'";
        $resStore  = mysqli_query($adController->MySQL, $query);
        $dataStore = mysqli_fetch_assoc($resStore);
        $store     = $dataStore['name_ar'] ?? 'المخزن';
    }

    $safeStore = mysqli_real_escape_string($adController->MySQL, $store);
    $reportDate = !empty($dataDate['date']) ? $dataDate['date'] : date('Y-m-d H:i:s');
    $safeDate  = mysqli_real_escape_string($adController->MySQL, $reportDate);

    $query = "INSERT INTO `report_open_palance_all` (`store`, `date`, `total`) VALUES ('$safeStore', '$safeDate', '$allTotal')";
    mysqli_query($adController->MySQL, $query);

    if (isset($_POST["report"])) {
        unset($_POST["report"]);
    ?>
        <script type="text/javascript">
            window.open('reports/openPalance.php', '_blank');
        </script>    
    <?php
    }
    ?>
     
    <div class="clearfix"></div>
    <?php require_once("footer.php"); ?>
    
    <script type="text/javascript">
        function totalOpenPalance(id) {
            var qty = parseFloat($("#item-qty-" + id).val()) || 0;
            var price = parseFloat($("#item-price-" + id).val()) || 0;
            var disc = parseFloat($("#item-discount-" + id).val()) || 0;
            
            var preTotal = qty * price;
            var discount = preTotal * disc / 100;
            var total = preTotal - discount;
            
            $("#item-total-" + id).html(total.toFixed(2));
            calculateAllTotals();
        }

        function calculateAllTotals() {
            var sum = 0;
            $("td[id^='item-total-']").each(function() {
                var val = parseFloat($(this).text()) || 0;
                sum += val;
            });
            $("#totalAll").html(sum.toFixed(2));
        }

        $(document).ready(function() {
            calculateAllTotals();

            var config = {
                '.span12': { width: '100%' },
                '.span3': { width: '95%' }
            };
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }

            $(".item-qty, .item-price, .item-discount").on("blur change keyup", function() {
                var id = $(this).attr('id').split('-').pop();
                totalOpenPalance(id);
            });
            
            $(".item-edit").click(function() {  
                var id = $(this).attr('id').replace('item-edit-', '');
                var q = $("#item-qty-" + id).val();
                var p = $("#item-price-" + id).val();
                var d = $("#item-discount-" + id).val();
                var v = $("#item-id-" + id).val();

                $("#overlay").show(300);
                $.ajax({
                    url: "controller.php",
                    data: {
                        'f': 'update_openPalance',
                        'q': q,
                        'p': p,
                        'd': d,
                        'id': v
                    },
                    type: "POST",
                    success: function(result) {
                        $("#overlay").hide(300);
                        if (parseInt($.trim(result)) > 0) {
                            if (typeof data_updated !== 'undefined') {
                                alert(data_updated);
                            } else {
                                alert("تم التحديث بنجاح");
                            }
                            location.reload();
                        } else {
                            alert(result);
                        }
                    },
                    error: function() {
                        $("#overlay").hide(300);
                        alert("حدث خطأ أثناء حفظ البيانات");
                    }
                });
            });   
        });
    </script>
</body>
</html>