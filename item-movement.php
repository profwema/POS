<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid   = (int)($_SESSION['storeid'] ?? 0);
$language  = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$pgName    = basename(__FILE__);

$fromDateV = htmlspecialchars($_POST['from_date'] ?? '');
$toDateV   = htmlspecialchars($_POST['to_date'] ?? '');
$selectedIid = htmlspecialchars($_POST['iid'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    safe_query($adController->MySQL, "DELETE FROM report_item_move");
    safe_query($adController->MySQL, "DELETE FROM report_item_move_sam");
    safe_query($adController->MySQL, "DELETE FROM report_item_move_all");
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=ITEM_MOVEMENT?></title>
    <?php require_once("header.php");?>	
    <link href="css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=ITEM_MOVEMENT?></h2>
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
                                            <label class="control-label" for="iid"><?=ITEMS?></label>
                                            <div class="controls">                                              
                                                <select name="iid" id="iid" data-rel="chosen" style="width:80%" required>
                                                    <option value="">-- اختر الصنف --</option>
                                                    <?php
                                                    $queryItems = "SELECT i.id, i.item_thread, i.barcode, i.name_ar, i.name_en, u.name_ar AS unit_ar, u.name_en AS unit_en 
                                                                   FROM items i 
                                                                   LEFT JOIN units u ON i.unit = u.id 
                                                                   WHERE i.storeid='$storeid' OR i.storeid='0' 
                                                                   ORDER BY i.name_ar ASC";
                                                    $resItems = mysqli_query($adController->MySQL, $queryItems) or die(mysqli_error($adController->MySQL));
                                                    while($data = mysqli_fetch_assoc($resItems))
                                                    {
                                                        $barcode  = !empty($data['barcode']) ? $data['barcode'] : "-----";
                                                        $itemName = !empty($data['name_ar']) ? $data['name_ar'] : (!empty($data['name_en']) ? $data['name_en'] : '---');
                                                        $unitName = !empty($data['unit_ar']) ? $data['unit_ar'] : (!empty($data['unit_en']) ? $data['unit_en'] : "-----");
                                                        $threadVal = $data['item_thread'] ?? $data['id'];
                                                        $sel = ($selectedIid !== '' && $selectedIid == $threadVal) ? "selected" : "";
                                                        echo "<option value='" . htmlspecialchars($threadVal) . "' $sel>" . htmlspecialchars("$barcode | $itemName | $unitName") . "</option>";
                                                    }
                                                    ?>
                                                </select>                                              
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="control-group">
                                        <div class="salesOptions">
                                            <label class="control-label" for="from_date"><?=FROM_DATE?></label>
                                            <div class="controls">
                                                <input type="text" class="datepicker-nobar" id="from_date" name="from_date" value="<?=$fromDateV?>">
                                            </div>
                                        </div>
                                        <div class="salesOptions">
                                            <label class="control-label" for="to_date"><?=TO_DATE?></label>
                                            <div class="controls">
                                                <input type="text" class="datepicker-nobar" id="to_date" name="to_date" value="<?=$toDateV?>">
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
                    $itemId = trim((string)($_POST['iid'] ?? ''));
                    if ($itemId === '')
                    {
                        echo "<div class='alert alert-error'>يرجى اختيار صنف لعرض حركة المخزون.</div>";
                    }
                    else 
                    {
                        $itemIdEsc = mysqli_real_escape_string($adController->MySQL, $itemId);
                        $conditionSel = " AND item_thread = '$itemIdEsc' ";
                        $conditionArray = [];

                        $fDateFormatted = '';
                        if (!empty($_POST['from_date']))
                        {
                            $rawF = str_replace('/', '-', trim($_POST['from_date']));
                            $fDateFormatted = date("Y-m-d 00:00:00", strtotime($rawF));
                            $conditionArray[] = "o.date >= '$fDateFormatted'";
                        }

                        $tDateFormatted = '';
                        if (!empty($_POST['to_date']))
                        {
                            $rawT = str_replace('/', '-', trim($_POST['to_date']));
                            $tDateFormatted = date("Y-m-d 23:59:59", strtotime($rawT));
                            $conditionArray[] = "o.date <= '$tDateFormatted'";
                        }

                        $condition = '';
                        if (!empty($conditionArray)) {
                            $condition = " AND " . implode(" AND ", $conditionArray);
                        }

                        // Create safe temporary table without illegal zero-dates
                        $queryTemp = "CREATE TEMPORARY TABLE IF NOT EXISTS `item_card` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `date` datetime DEFAULT NULL,
                            `name` varchar(255) NOT NULL,
                            `store` varchar(255) NOT NULL,
                            `unit` varchar(255) NOT NULL,
                            `qty` decimal(10,2) NOT NULL,
                            `noOfQty` decimal(10,2) NOT NULL,
                            PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                        mysqli_query($adController->MySQL, $queryTemp);
                        safe_query($adController->MySQL, "TRUNCATE TABLE `item_card`");

                        $total = 0;
                        $itemsListArray = [];

                        $queryAll = "SELECT i.id AS id, i.name_$language AS name, i.unit, i.barcode, i.intimate_stock AS noOfQty, u.name_$language AS unit 
                                     FROM items i 
                                     LEFT JOIN units u ON i.unit = u.id 
                                     WHERE (i.storeid='$storeid' OR i.storeid='0') $conditionSel 
                                     ORDER BY i.intimate_stock DESC";
                        $resAll = mysqli_query($adController->MySQL, $queryAll);     
                        $itemHeaderName = '';

                        if ($resAll)
                        {
                            while($dataAll = mysqli_fetch_assoc($resAll))
                            {
                                $itemsListArray[] = $dataAll;
                                $curId   = $dataAll['id'];
                                $itemHeaderName = $dataAll['name'];

                                // 1. Open Balance
                                $query = "SELECT o.quantity AS qty, o.date AS date, o.store, s.name_$language AS store 
                                          FROM open_palance o 
                                          LEFT JOIN stores s ON o.store = s.id 
                                          WHERE o.itemid = '$curId' $condition AND (o.storeid='$storeid' OR o.storeid='0')";
                                $res = mysqli_query($adController->MySQL, $query);
                                if ($res) {
                                    while($data = mysqli_fetch_assoc($res))
                                    {
                                        $date    = $data['date'] ?? date('Y-m-d H:i:s');
                                        $name    = "OPENING_PALANCE";
                                        $store   = $data['store'] ?? '';
                                        $unit    = $dataAll['unit'] ?? '';
                                        $noOfQty = (float)($dataAll['noOfQty'] ?? 1);
                                        $qty     = (float)($data['qty'] ?? 0);
                                        $total   += $qty * $noOfQty;
                                        
                                        safe_query($adController->MySQL, 
                                            "INSERT INTO item_card (date, name, store, unit, qty, noOfQty) VALUES (?, ?, ?, ?, ?, ?)",
                                            [$date, $name, $store, $unit, $qty, $noOfQty]);
                                    }
                                }

                                // 2. Income items (إذن إضافة وارد / فواتير مشتريات)
                                $query = "SELECT o.quantity AS qty, o.date AS date, o.store, o.invoice_Type AS type, s.name_$language AS store 
                                          FROM income_items o 
                                          LEFT JOIN stores s ON o.store = s.id 
                                          WHERE o.itemid = '$curId' $condition AND o.state = '1' AND (o.storeid='$storeid' OR o.storeid='0')";
                                $res = mysqli_query($adController->MySQL, $query);
                                if ($res) {
                                    while($data = mysqli_fetch_assoc($res))
                                    {
                                        $date    = $data['date'] ?? date('Y-m-d H:i:s');
                                        $name    = $data['type'] ?? 'INCOMING_ENTRY';
                                        $store   = $data['store'] ?? '';
                                        $unit    = $dataAll['unit'] ?? '';
                                        $noOfQty = (float)($dataAll['noOfQty'] ?? 1);
                                        $qty     = (float)($data['qty'] ?? 0);
                                        $total   += $qty * $noOfQty;

                                        safe_query($adController->MySQL, 
                                            "INSERT INTO item_card (date, name, store, unit, qty, noOfQty) VALUES (?, ?, ?, ?, ?, ?)",
                                            [$date, $name, $store, $unit, $qty, $noOfQty]);
                                    }
                                }

                                // 3. Outgo items (إذن صرف صادر / فواتير مبيعات)
                                $query = "SELECT o.quantity AS qty, o.date AS date, o.store, o.invoice_Type AS type, s.name_$language AS store 
                                          FROM outgo_items o 
                                          LEFT JOIN stores s ON o.store = s.id 
                                          WHERE o.itemid = '$curId' $condition AND o.state = '1' AND (o.storeid='$storeid' OR o.storeid='0')";
                                $res = mysqli_query($adController->MySQL, $query);
                                if ($res) {
                                    while($data = mysqli_fetch_assoc($res))
                                    {
                                        $date    = $data['date'] ?? date('Y-m-d H:i:s');
                                        $name    = $data['type'] ?? 'OUTGOING_ENTRY';
                                        $store   = $data['store'] ?? '';
                                        $unit    = $dataAll['unit'] ?? '';
                                        $noOfQty = (float)($dataAll['noOfQty'] ?? 1);
                                        $qty     = (float)($data['qty'] ?? 0) * -1;
                                        $total   += $qty * $noOfQty;

                                        safe_query($adController->MySQL, 
                                            "INSERT INTO item_card (date, name, store, unit, qty, noOfQty) VALUES (?, ?, ?, ?, ?, ?)",
                                            [$date, $name, $store, $unit, $qty, $noOfQty]);
                                    }
                                }
                            }
                        }
                        ?>

                        <div>
                            <div class="box span12">
                                <a href="reports/item_move.php" target="_blank" title="طباعة التقرير">
                                    <img src="img/print.png" style="width:40px; margin-bottom: 15px;" alt="طباعة">
                                </a>                       
                                <div class="clearfix"></div>
                                
                                <center> 
                                    <table style="width:50%; margin-top: 10px" class="table table-striped table-bordered bootstrap-datatable">
                                        <thead>
                                            <tr>
                                                <th><?=UNIT?></th>
                                                <th><?=QTY_VAL?></th>
                                                <th><?=PALANCE?> : <?=htmlspecialchars((string)$total)?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $remainingTotal = $total;
                                            for($i = 0; $i < count($itemsListArray); $i++)
                                            {
                                                $divisor = !empty($itemsListArray[$i]['noOfQty']) ? (int)$itemsListArray[$i]['noOfQty'] : 1;
                                                $totalQty = intval($remainingTotal / $divisor);
                                                $totalAmount = 0;  
                                                $remainingTotal = $remainingTotal % $divisor;
                                                $unitName = $itemsListArray[$i]['unit'] ?? '';
                                                $qtyVal   = $divisor;
                                            ?>
                                            <tr>
                                                <td><div><?=htmlspecialchars($unitName)?></div></td>
                                                <td><div><?=htmlspecialchars((string)$qtyVal)?></div></td>                                
                                                <td><div><?=htmlspecialchars((string)$totalQty)?></div></td>
                                            </tr>
                                            <?php
                                                safe_query($adController->MySQL, 
                                                    "INSERT INTO `report_item_move_sam` (`unit`, `count`, `quantity`, `total`) VALUES (?, ?, ?, ?)",
                                                    [$unitName, $qtyVal, $totalQty, $totalAmount]);
                                            }
                                            ?>
                                        </tbody>
                                    </table> 
                                </center>

                                <div class="clearfix"></div>
                                <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?=DATE?></th>
                                            <th><?=TYPE?></th>
                                            <th><?=STORE?></th>
                                            <th><?=UNITS?></th>
                                            <th><?=QTY_VAL?></th>                                    
                                            <th><?=QTY?></th>
                                            <th><?=TOTALCOUNT?></th> 
                                            <th><?=COST?></th>
                                            <th><?=TOTAL?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT date, name, store, unit, noOfQty, qty FROM item_card ORDER BY date ASC";
                                        $resCard = mysqli_query($adController->MySQL, $query);
                                        $j = 0;
                                        if ($resCard)
                                        {
                                            while($data = mysqli_fetch_assoc($resCard))
                                            {
                                                $j++;
                                                $rowTotalCount = (float)$data['qty'] * (float)$data['noOfQty'];
                                                $price = 0;
                                                $totalAmount = (float)$data['qty'] * $price;
                                                $style = ($data['qty'] < 0) ? "style='color: red; font-weight: bold;'" : "";
                                                $rawName = $data['name'] ?? '';
                                                $typeName = defined($rawName) ? constant($rawName) : $rawName;
                                                ?>
                                                <tr <?=$style?>>
                                                    <td><?=$j?></td>
                                                    <td><?=htmlspecialchars((string)$data['date'])?></td>
                                                    <td><?=htmlspecialchars((string)$typeName)?></td>
                                                    <td><?=htmlspecialchars((string)$data['store'])?></td>
                                                    <td><?=htmlspecialchars((string)$data['unit'])?></td>
                                                    <td><?=htmlspecialchars((string)$data['noOfQty'])?></td>                                  
                                                    <td><?=htmlspecialchars((string)$data['qty'])?></td>
                                                    <td><?=htmlspecialchars((string)$rowTotalCount)?></td> 
                                                    <td><?=htmlspecialchars((string)$price)?></td> 
                                                    <td><?=htmlspecialchars((string)$totalAmount)?></td> 
                                                </tr>
                                                <?php
                                                safe_query($adController->MySQL, 
                                                    "INSERT INTO `report_item_move` (`date`, `type`, `store`, `unit`, `count`, `quantity`, `totalCount`, `cost`, `total`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                                                    [$data['date'], $typeName, $data['store'], $data['unit'], $data['noOfQty'], $data['qty'], $rowTotalCount, $price, $totalAmount]);
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php
                        safe_query($adController->MySQL, 
                            "INSERT INTO `report_item_move_all` (`item`, `from`, `to`) VALUES (?, ?, ?)",
                            [$itemHeaderName, $fDateFormatted, $tDateFormatted]);
                    }
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