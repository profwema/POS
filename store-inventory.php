<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid   = (int)($_SESSION['storeid'] ?? 0);
$language  = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$pgName    = basename(__FILE__);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    safe_query($adController->MySQL, "DELETE FROM report_inventory_sam");
    safe_query($adController->MySQL, "DELETE FROM report_inventory");
}

$selectedBr    = htmlspecialchars((string)($_REQUEST['br'] ?? ''));
$selectedStore = htmlspecialchars((string)($_REQUEST['store'] ?? ''));
$selectedCat   = htmlspecialchars((string)($_REQUEST['catid'] ?? ''));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=STORES_INVENTORY?></title>
    <?php require_once("header.php");?>	
</head>
<body>
    <?php require_once("header_top.php");?>
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php");?>
            <div id="content" class="span10">
                <div>
                    <div class="box span12">
                        <div class="box-header" data-original-title>
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=STORES_INVENTORY?></h2>
                            <div class="box-icon">
                                <a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" action="<?=$pgName?>" id="form" method="POST" autocomplete="off">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <fieldset>
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label" for="br"><?=BRANCH?></label>
                                            <div class="controls">
                                                <select onchange="this.form.submit()" class="span3" name="br" id="br">
                                                    <option value="">-- كل الفروع --</option>
                                                    <?php
                                                    $query = "SELECT name_en, name_ar, id FROM branches WHERE storeid='$storeid' OR storeid='0'";
                                                    $res   = mysqli_query($adController->MySQL, $query);
                                                    if ($res) {
                                                        while($data = mysqli_fetch_assoc($res))
                                                        {
                                                            $name = !empty($data['name_'.$language]) ? $data['name_'.$language] : ($data['name_en'] ?? '');
                                                            $sel  = ($selectedBr !== '' && $selectedBr == $data['id']) ? "selected" : "";
                                                            echo "<option value='{$data['id']}' $sel>" . htmlspecialchars($name) . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="salesOptions">
                                            <label class="control-label" for="store"><?=STORES?></label>
                                            <div class="controls">
                                                <select class="span3" name="store" id="store">
                                                    <option value="">-- كل المخازن --</option>
                                                    <?php
                                                    if ($selectedBr !== '')
                                                    {
                                                        $brEsc = (int)$selectedBr;
                                                        $query = "SELECT name_en, name_ar, id FROM stores WHERE (storeid='$storeid' OR storeid='0') AND branch='$brEsc'";
                                                        $res   = mysqli_query($adController->MySQL, $query);
                                                        if ($res) {
                                                            while($data = mysqli_fetch_assoc($res))
                                                            {
                                                                $name = !empty($data['name_'.$language]) ? $data['name_'.$language] : ($data['name_en'] ?? '');
                                                                $sel  = ($selectedStore !== '' && $selectedStore == $data['id']) ? "selected" : "";
                                                                echo "<option value='{$data['id']}' $sel>" . htmlspecialchars($name) . "</option>";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label" for="catid"><?=CATEGORY?></label>
                                            <div class="controls">
                                                <select onchange="this.form.submit()" class="span3" name="catid" id="catid">
                                                    <option value="">-- كل الفئات --</option>
                                                    <?php
                                                    $query = "SELECT id, name_ar, name_en FROM categories WHERE storeid='$storeid' OR storeid='0'";
                                                    $res   = mysqli_query($adController->MySQL, $query);
                                                    if ($res) {
                                                        while($data = mysqli_fetch_assoc($res))
                                                        {
                                                            $name = !empty($data["name_".$language]) ? $data["name_".$language] : ($data['name_ar'] ?? '');
                                                            $sel  = ($selectedCat !== '' && $selectedCat == $data['id']) ? "selected" : "";
                                                            echo "<option value='{$data['id']}' $sel>" . htmlspecialchars($name) . "</option>";
                                                        }
                                                    }
                                                    ?>											
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg" name="Submit"><?=LOAD_FORM?></button>
                                    <div class="clearfix"></div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
                if (isset($_POST['Submit']))
                {
                    $conditionItemArray = [" (storeid='$storeid' OR storeid='0') "];
                    $catid = (int)($_POST['catid'] ?? 0);
                    $catName = 'كل الفئات';
                    if ($catid > 0)   
                    {
                        $conditionItemArray[] = " catid = '$catid' ";  
                        $query = "SELECT name_ar, name_en FROM categories WHERE id='$catid' LIMIT 1";
                        $resStore = mysqli_query($adController->MySQL, $query);
                        if ($resStore && $dataStore = mysqli_fetch_assoc($resStore)) {
                            $catName = !empty($dataStore['name_ar']) ? $dataStore['name_ar'] : ($dataStore['name_en'] ?? 'فئة');
                        }
                    }
                    $conditionItem = implode(" AND ", $conditionItemArray);
                    
                    $conditionSearch = "";    
                    $branchid = (int)($_POST['br'] ?? 0);
                    $store    = (int)($_POST['store'] ?? 0);

                    $storeName = 'كل المخازن';
                    if ($store > 0)
                    {
                        $conditionSearch .= " AND o.store = '$store' ";
                        $query = "SELECT name_ar, name_en FROM stores WHERE id='$store' LIMIT 1";
                        $resStore = mysqli_query($adController->MySQL, $query);
                        if ($resStore && $dataStore = mysqli_fetch_assoc($resStore)) {
                            $storeName = !empty($dataStore['name_ar']) ? $dataStore['name_ar'] : ($dataStore['name_en'] ?? 'مخزن');
                        }
                    }

                    $branchName = 'كل الفروع';
                    if ($branchid > 0)
                    {
                        $conditionSearch .= " AND b.id = '$branchid' ";  
                        $query = "SELECT name_ar, name_en FROM branches WHERE id='$branchid' LIMIT 1";
                        $resBr = mysqli_query($adController->MySQL, $query);
                        if ($resBr && $dataBr = mysqli_fetch_assoc($resBr)) {
                            $branchName = !empty($dataBr['name_ar']) ? $dataBr['name_ar'] : ($dataBr['name_en'] ?? 'فرع');
                        }
                    }
                ?>
                <div>
                    <div class="box span12">  
                        <a href="reports/storeInvestory.php" target="_blank" title="طباعة الجرد">
                            <img src="img/print.png" style="width:40px; margin-bottom: 15px;" alt="طباعة">
                        </a> 
                        <div class="clearfix"></div>

                        <table class="table table-striped table-bordered bootstrap-datatable">
                            <thead>
                                <tr>    
                                    <th><?=BARCODE?></th>
                                    <th><?=ITEM_NAME?></th>
                                    <th><?=UNITS?></th>
                                    <th><?=FILLING?></th>
                                    <th><?=PALANCE?></th>
                                    <th><?=COST?></th>
                                    <th><?=TOTALS?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $allTotal = 0;
                            $queryThread = "SELECT item_thread, name_" . $language . " AS name FROM items WHERE $conditionItem GROUP BY item_thread";
                            $resThread   = mysqli_query($adController->MySQL, $queryThread);    
                            
                            if ($resThread)
                            {
                                while($dataThread = mysqli_fetch_assoc($resThread))
                                {
                                    $total = 0;
                                    $itemThread = mysqli_real_escape_string($adController->MySQL, (string)$dataThread['item_thread']);   
                                    $itemname   = $dataThread['name'] ?? '';  
                                    $itemsListArray = [];
                                     
                                    $queryItems = "SELECT i.id AS id, i.name_$language AS name, i.barcode AS barcode, i.intimate_stock AS noOfQty, u.name_$language AS unit 
                                                   FROM items i 
                                                   LEFT JOIN units u ON i.unit = u.id 
                                                   WHERE i.item_thread = '$itemThread' 
                                                   ORDER BY i.intimate_stock DESC";
                                    $resItem = mysqli_query($adController->MySQL, $queryItems);
                                    if ($resItem)
                                    {
                                        while($dataItem = mysqli_fetch_assoc($resItem))
                                        {
                                            $itemsListArray[] = $dataItem;
                                            $itemId = (int)$dataItem['id'];

                                            // 1. Open Balance
                                            $query = "SELECT o.quantity AS qty, o.date AS date, o.store, s.name_$language AS store 
                                                      FROM open_palance o 
                                                      INNER JOIN stores s ON o.store = s.id 
                                                      INNER JOIN branches b ON s.branch = b.id 
                                                      WHERE o.itemid = '$itemId' $conditionSearch AND (o.storeid='$storeid' OR o.storeid='0')";
                                            $res = mysqli_query($adController->MySQL, $query);
                                            if ($res) {
                                                while($data = mysqli_fetch_assoc($res)) {
                                                    $noOfQty = (float)($dataItem['noOfQty'] ?? 1);
                                                    $qty     = (float)($data['qty'] ?? 0);
                                                    $total   += $qty * $noOfQty;
                                                }
                                            }

                                            // 2. Income items
                                            $query = "SELECT o.quantity AS qty, o.date AS date, o.store, o.invoice_Type AS type, s.name_$language AS store 
                                                      FROM income_items o 
                                                      INNER JOIN stores s ON o.store = s.id 
                                                      INNER JOIN branches b ON s.branch = b.id 
                                                      WHERE o.itemid = '$itemId' $conditionSearch AND o.state = '1' AND (o.storeid='$storeid' OR o.storeid='0')";
                                            $res = mysqli_query($adController->MySQL, $query);
                                            if ($res) {
                                                while($data = mysqli_fetch_assoc($res)) {
                                                    $noOfQty = (float)($dataItem['noOfQty'] ?? 1);
                                                    $qty     = (float)($data['qty'] ?? 0);
                                                    $total   += $qty * $noOfQty;
                                                }
                                            }

                                            // 3. Outgo items
                                            $query = "SELECT o.quantity AS qty, o.date AS date, o.store, o.invoice_Type AS type, s.name_$language AS store 
                                                      FROM outgo_items o 
                                                      INNER JOIN stores s ON o.store = s.id 
                                                      INNER JOIN branches b ON s.branch = b.id 
                                                      WHERE o.itemid = '$itemId' $conditionSearch AND o.state = '1' AND (o.storeid='$storeid' OR o.storeid='0')";
                                            $res = mysqli_query($adController->MySQL, $query);
                                            if ($res) {
                                                while($data = mysqli_fetch_assoc($res)) {
                                                    $noOfQty = (float)($dataItem['noOfQty'] ?? 1);
                                                    $qty     = (float)($data['qty'] ?? 0) * -1;
                                                    $total   += $qty * $noOfQty;
                                                }
                                            }
                                        }
                                    }
                                    
                                    for($i = 0; $i < count($itemsListArray); $i++)
                                    {          
                                        $divisor = max(1, (int)($itemsListArray[$i]['noOfQty'] ?? 1));
                                        $palance = intval($total / $divisor);
                                        $total   = $total % $divisor;

                                        if ($palance != 0)
                                        {
                                            $barcode  = $itemsListArray[$i]['barcode'] ?? '---';
                                            $itemName = $itemsListArray[$i]['name'] ?? '---';
                                            $unitName = $itemsListArray[$i]['unit'] ?? '---';
                                            $count    = $divisor;
                                            $cost     = 0;
                                            $totals   = $palance * $cost; 
                                            $allTotal += $totals;

                                            safe_query($adController->MySQL, 
                                                "INSERT INTO `report_inventory` (`barcode`, `item`, `unit`, `count`, `quantity`, `cost`, `total`) VALUES (?, ?, ?, ?, ?, ?, ?)",
                                                [$barcode, $itemName, $unitName, $count, $palance, $cost, $totals]);
                                            ?>
                                            <tr>
                                                <td><div><?=htmlspecialchars((string)$barcode)?></div></td>
                                                <td><div><?=htmlspecialchars((string)$itemName)?></div></td>  
                                                <td><div><?=htmlspecialchars((string)$unitName)?></div></td> 
                                                <td><div><?=htmlspecialchars((string)$count)?></div></td>      
                                                <td><div><?=htmlspecialchars((string)$palance)?></div></td>                              
                                                <td><div><?=htmlspecialchars((string)$cost)?></div></td>
                                                <td><div><?=htmlspecialchars((string)$totals)?></div></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                    safe_query($adController->MySQL, 
                        "INSERT INTO `report_inventory_sam` (`pranch`, `store`, `category`, `totals`) VALUES (?, ?, ?, ?)",
                        [$branchName, $storeName, $catName, $allTotal]);
                }
                ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <?php require_once("footer.php");?>
        <script type="text/javascript">
            $(document).ready(function() {
                var config = {
                    '.span3': { width: '80%' }
                };
                for (var selector in config) {
                    $(selector).chosen(config[selector]);
                }
            });
        </script>
    </div>
</body>
</html>