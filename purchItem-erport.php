<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid  = intval($_SESSION['storeid'] ?? 1);
$language = $_SESSION['lang'] ?? 'ar';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Submit'])) {
    mysqli_query($adController->MySQL, "DELETE FROM report_saleitem");
    mysqli_query($adController->MySQL, "DELETE FROM report_saleitem_all");
}

$pgName           = basename($_SERVER['PHP_SELF']);
$selectedCat      = intval($_REQUEST['catid'] ?? 0);
$selectedItem     = trim($_REQUEST['iid'] ?? '');
$selectedSupplier = intval($_REQUEST['supplier'] ?? 0);
$selectedBranch   = intval($_REQUEST['branch'] ?? 0);
$fr_date          = $_POST['fr_date'] ?? '';
$to_date          = $_POST['to_date'] ?? '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=ITEMS_REPORT?></title>
    <?php require_once("header.php"); ?>	
    <link href="css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
    <style>
        .datetime {
            width: 85%;
            border: 1px solid #10b981 !important;
            padding: 6px 10px;
            border-radius: 4px;
        }
        .titles {
            background: #2d3748;
            color: #fff;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 15px;
            border-radius: 4px;
            margin: 15px 0 10px 0;
        }
        .totalAll {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        .totalAll .counts {
            flex: 1;
            min-width: 260px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .totalAll table {
            width: 100%;
            border-collapse: collapse;
        }
        .totalAll table td {
            padding: 8px 12px;
            border-bottom: 1px solid #edf2f7;
            font-size: 13px;
        }
        .totalAll table td.title {
            background: #edf2f7;
            font-weight: bold;
            color: #2d3748;
            border-bottom: 2px solid #cbd5e0;
        }
        .totalAll table td.head {
            color: #4a5568;
            font-weight: 600;
        }
        .totalAll table td.value {
            text-align: left;
            font-weight: bold;
            color: #1a202c;
            direction: ltr;
        }
        .br {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 20px 0;
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
                            <h2>
                                <i class="halflings-icon edit"></i>
                                <span class="break"></span>
                                <a href="purchase.php"><?=PURCHASES?></a> &gt;&gt; <?=ITEMS_REPORT?>
                            </h2>
                            <div class="box-icon">
                                <a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" autocomplete="off" action="<?=$pgName?>" id='form' method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <fieldset>
                                    <div class="control-group" style="display:flex; flex-wrap:wrap; gap:15px;">
                                        <div class="salesOptions" style="min-width:260px;">
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=CATEGORY?></label>
                                            <div class="controls" style="margin-right:0;">
                                                <select onchange="this.form.submit()" data-rel="chosen" name="catid" class="span12">
                                                    <option value="">-- <?=ALL ?? 'كافة الأقسام'?> --</option>
                                                    <?php
                                                    $queryCat = "SELECT id, name_ar, name_en FROM categories WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                                    $resCat   = mysqli_query($adController->MySQL, $queryCat);
                                                    if ($resCat) {
                                                        while ($dataCat = mysqli_fetch_assoc($resCat)) {
                                                            $name = ($language == 'ar' ? $dataCat['name_ar'] : $dataCat['name_en']) ?: $dataCat['name_ar'];
                                                            $sel  = ($selectedCat == $dataCat['id']) ? "selected" : "";
                                                            echo "<option value='" . htmlspecialchars($dataCat['id']) . "' $sel>" . htmlspecialchars($name) . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="salesOptions" style="min-width:260px;">
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=ITEMS?></label>
                                            <div class="controls" style="margin-right:0;">                                              
                                                <select name="iid" data-rel="chosen" class="span12">
                                                    <option value="">-- <?=ALL ?? 'كافة الأصناف'?> --</option>
                                                    <?php
                                                    $catSearch = ($selectedCat > 0) ? " AND catid = '$selectedCat' " : "";
                                                    $queryItems = "SELECT i.id, i.item_thread, i.barcode, i.name_ar, i.name_en, u.name_ar AS unit_ar, u.name_en AS unit_en
                                                                   FROM items i
                                                                   LEFT JOIN units u ON u.id = i.unit AND u.storeid = '$storeid'
                                                                   WHERE i.storeid = '$storeid' $catSearch
                                                                   GROUP BY i.item_thread
                                                                   ORDER BY i.name_ar ASC";
                                                    $resItems = mysqli_query($adController->MySQL, $queryItems);
                                                    if ($resItems) {
                                                        while ($dataIt = mysqli_fetch_assoc($resItems)) {
                                                            $barcode = !empty($dataIt['barcode']) ? $dataIt['barcode'] : "-----";
                                                            $iName   = ($language == 'ar' ? $dataIt['name_ar'] : $dataIt['name_en']) ?: $dataIt['name_ar'];
                                                            $uName   = ($language == 'ar' ? $dataIt['unit_ar'] : $dataIt['unit_en']) ?: $dataIt['unit_ar'];
                                                            $optText = "{$barcode} | {$iName}" . (!empty($uName) ? " | {$uName}" : "");
                                                            $sel     = ($selectedItem === $dataIt['item_thread']) ? "selected" : "";
                                                            echo "<option value='" . htmlspecialchars($dataIt['item_thread']) . "' $sel>" . htmlspecialchars($optText) . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>                                              
                                            </div>
                                        </div>       
                                    </div>       

                                    <div class="control-group" style="display:flex; flex-wrap:wrap; gap:15px; margin-top:10px;">
                                        <div class="salesOptions" style="min-width:260px;">
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=SUPPLIER?> : </label>
                                            <div class="controls" style="margin-right:0;">
                                                <select name="supplier" data-rel="chosen" class="span12">
                                                    <option value="">-- <?=ALL ?? 'كافة الموردين'?> --</option>
                                                    <?php
                                                    $querySupp = "SELECT id, name_ar, name_en FROM suppliers WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                                    $resSupp   = mysqli_query($adController->MySQL, $querySupp);
                                                    if ($resSupp) {
                                                        while ($dataSupp = mysqli_fetch_assoc($resSupp)) {
                                                            $sId   = $dataSupp['id'];
                                                            $sName = ($language == 'ar' ? $dataSupp['name_ar'] : $dataSupp['name_en']) ?: $dataSupp['name_ar'];
                                                            $sel   = ($selectedSupplier == $sId) ? "selected" : "";
                                                            echo "<option value='" . htmlspecialchars($sId) . "' $sel>" . htmlspecialchars($sName) . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="salesOptions" style="min-width:260px;">
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=BRANCH?> :</label>
                                            <div class="controls" style="margin-right:0;">
                                                <select name="branch" data-rel="chosen" class="span12">
                                                    <option value="">-- <?=ALL ?? 'كافة الفروع'?> --</option>
                                                    <?php
                                                    $queryBranch = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                                    $resBranch   = mysqli_query($adController->MySQL, $queryBranch);
                                                    if ($resBranch) {
                                                        while ($dataBr = mysqli_fetch_assoc($resBranch)) {
                                                            $bId   = $dataBr['id'];
                                                            $bName = ($language == 'ar' ? $dataBr['name_ar'] : $dataBr['name_en']) ?: $dataBr['name_ar'];
                                                            $sel   = ($selectedBranch == $bId) ? "selected" : "";
                                                            echo "<option value='" . htmlspecialchars($bId) . "' $sel>" . htmlspecialchars($bName) . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group" style="display:flex; flex-wrap:wrap; gap:15px; margin-top:10px;">
                                        <div class="salesOptions" style="min-width:260px;">                               
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=FROM_DATE?></label>
                                            <div class="controls" style="margin-right:0;">
                                                <input type="text" class="datetime datetimepicker" id="fr_date" name="fr_date" value="<?=htmlspecialchars($fr_date)?>" placeholder="yyyy-mm-dd hh:00:00">
                                            </div>
                                        </div>
                                        <div class="salesOptions" style="min-width:260px;">                                                                 
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=TO_DATE?></label>
                                            <div class="controls" style="margin-right:0;">
                                                <input type="text" class="datetime datetimepicker" id="to_date" name="to_date" value="<?=htmlspecialchars($to_date)?>" placeholder="yyyy-mm-dd hh:00:00">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions" style="margin-top:15px;">
                                        <button type="submit" class="btn btn-primary" name="Submit"><?=defined('LOAD_FORM') ? LOAD_FORM : 'عرض التقرير'?></button>
                                        <a href="<?=$pgName?>" class="btn"><?=defined('CANCEL') ? CANCEL : 'إلغاء'?></a>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
                if (isset($_POST['Submit'])) {
                    $grossTotal   = 0;
                    $discTotal    = 0;  
                    $vatTotal     = 0;
                    $purchTotal   = 0;  
                    $rePurchTotal = 0;

                    $conditionPurch = '';
                    $conditionRet   = '';
                    $conditionItem  = '';

                    if ($selectedCat > 0) {
                        $conditionItem .= " AND i.catid = '$selectedCat'";
                        $qC = "SELECT name_ar, name_en FROM categories WHERE id='$selectedCat' AND storeid='$storeid' LIMIT 1";
                        $rC = mysqli_query($adController->MySQL, $qC);
                        $dC = mysqli_fetch_assoc($rC);
                        $catName = ($language == 'ar' ? ($dC['name_ar'] ?? '') : ($dC['name_en'] ?? '')) ?: ($dC['name_ar'] ?? 'الكل');
                    } else {
                        $catName = 'الكل';
                    }

                    if (!empty($selectedItem)) {
                        $safeItemThread = mysqli_real_escape_string($adController->MySQL, $selectedItem);
                        $conditionItem .= " AND i.item_thread = '$safeItemThread'";
                        $qI = "SELECT name_ar, name_en FROM items WHERE item_thread='$safeItemThread' AND storeid='$storeid' LIMIT 1";
                        $rI = mysqli_query($adController->MySQL, $qI);
                        $dI = mysqli_fetch_assoc($rI);
                        $itemName = ($language == 'ar' ? ($dI['name_ar'] ?? '') : ($dI['name_en'] ?? '')) ?: ($dI['name_ar'] ?? 'الكل');
                    } else {
                        $itemName = 'الكل';
                    }

                    if ($selectedSupplier > 0) {
                        $conditionPurch .= " AND p.supplier = '$selectedSupplier'";
                        $conditionRet   .= " AND pr.supplier = '$selectedSupplier'";
                        $qSupp = "SELECT name_ar, name_en FROM suppliers WHERE id='$selectedSupplier' AND storeid='$storeid' LIMIT 1";
                        $rSupp = mysqli_query($adController->MySQL, $qSupp);
                        $dSupp = mysqli_fetch_assoc($rSupp);
                        $suppName = ($language == 'ar' ? ($dSupp['name_ar'] ?? '') : ($dSupp['name_en'] ?? '')) ?: ($dSupp['name_ar'] ?? 'الكل');
                    } else {
                        $suppName = 'الكل';
                    }

                    if ($selectedBranch > 0) {
                        $conditionPurch .= " AND p.branch = '$selectedBranch'";
                        $conditionRet   .= " AND pr.branch = '$selectedBranch'";
                        $qBr = "SELECT name_ar, name_en FROM branches WHERE id='$selectedBranch' AND storeid='$storeid' LIMIT 1";
                        $rBr = mysqli_query($adController->MySQL, $qBr);
                        $dBr = mysqli_fetch_assoc($rBr);
                        $branchName = ($language == 'ar' ? ($dBr['name_ar'] ?? '') : ($dBr['name_en'] ?? '')) ?: ($dBr['name_ar'] ?? 'الكل');
                    } else {
                        $branchName = 'الكل';
                    }

                    if (!empty($fr_date)) {
                        $safeFDate = mysqli_real_escape_string($adController->MySQL, $fr_date);
                        $conditionPurch .= " AND p.purchase_date >= '$safeFDate'";
                        $conditionRet   .= " AND pr.purchase_date >= '$safeFDate'";
                    }
                    if (!empty($to_date)) {
                        $safeTDate = mysqli_real_escape_string($adController->MySQL, $to_date);
                        $conditionPurch .= " AND p.purchase_date <= '$safeTDate'";
                        $conditionRet   .= " AND pr.purchase_date <= '$safeTDate'";
                    }
                ?>
                <div>
                    <div class="box span12">
                        <div style="margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;">
                            <a href='reports/purchItemReport.php' target='_blank' class="btn btn-success" title="طباعة تقرير PDF">
                                <i class="icon-print"></i> طباعة التقرير PDF
                            </a>
                        </div> 
                        <div class="titles"><?=TOTAlS?></div>
                        <div class="totalAll">
                            <div class="counts"> 
                                <table>
                                    <tr>
                                        <td class="title" colspan="2"><b><?=PURCHASES?></b></td>
                                    </tr>       
                                    <tr>
                                        <td class="head"><b><?=TOTAL?></b></td>
                                        <td class="value"><div id='gross-total'>0.00</div></td>
                                    </tr>                                    
                                    <tr>
                                        <td class="head"><b><?=DISCOUNT?></b></td>
                                        <td class="value"><div id='disc-total'>0.00</div></td>
                                    </tr>
                                    <tr>
                                        <td class="head"><b><?=VAT?></b></td>
                                        <td class="value"><div id='vat-total'>0.00</div></td>
                                    </tr>
                                    <tr>
                                        <td class="head"><b><?=TOTAL?></b></td>
                                        <td class="value"><div id='sales-total'>0.00</div></td>
                                    </tr>                                    
                                </table>                               
                            </div>
                            <div class="counts"> 
                                <table>
                                    <tr>
                                        <td class="title" colspan="2"><b><?=RETURNED_PURCHASES?></b></td>
                                    </tr>                                   
                                    <tr>
                                        <td class="head"><b><?=TOTAL?></b></td>
                                        <td class="value"><div id='reSales-total'>0.00</div></td>
                                    </tr>
                                </table>    
                            </div>
                            <div class="counts"> 
                                <table>
                                    <tr>
                                        <td class="title"><b><?=NET_BALANCE?></b></td>
                                    </tr>                                   
                                    <tr>
                                        <td class="head value" style="text-align:center;"><div id='net-total' style="font-size:16px; color:#2563eb;">0.00</div></td>
                                    </tr>                        
                                </table> 
                            </div>                          
                        </div>
                        <div class="clearfix"></div>
                        <hr class="br">

                        <div class="titles"><?=PURCHASES?></div>
                        <table class="table table-striped table-bordered bootstrap-datatable datatable">     
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?=ITEM_NAME?></th> 
                                    <th><?=UNITS?></th>   
                                    <th><?=BARCODE?></th> 
                                    <th><?=QUANTITY?></th>
                                    <th><?=PRICE?></th>
                                    <th><?=TOTAL?></th>            
                                    <th><?=DISCOUNT?></th>         
                                    <th><?=TAX?></th>   
                                    <th><?=TOTAL?></th>                  
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $queryPurchItems = "SELECT 
                                                    pi.itemid,
                                                    i.barcode,
                                                    i.name_ar, i.name_en,
                                                    u.name_ar AS unit_ar, u.name_en AS unit_en,
                                                    SUM(pi.quantity) AS total_qty,
                                                    AVG(pi.cost) AS avg_price,
                                                    SUM(pi.cost * pi.quantity) AS gross_total,
                                                    SUM(pi.cost * pi.quantity * pi.discount / 100) AS total_disc,
                                                    SUM((pi.cost * pi.quantity - (pi.cost * pi.quantity * pi.discount / 100)) * pi.tax / 100) AS total_vat,
                                                    SUM((pi.cost * pi.quantity - (pi.cost * pi.quantity * pi.discount / 100)) * (1 + pi.tax / 100)) AS net_total
                                                FROM purchase_items pi
                                                JOIN purchase p ON p.invoicenumber = pi.invoice_No AND p.storeid = '$storeid' AND p.state = '1' $conditionPurch
                                                JOIN items i ON i.id = pi.itemid AND i.storeid = '$storeid' $conditionItem
                                                LEFT JOIN units u ON u.id = i.unit AND u.storeid = '$storeid'
                                                WHERE pi.state = '1'
                                                GROUP BY pi.itemid
                                                ORDER BY i.name_ar ASC";
                            $resPurchItems = mysqli_query($adController->MySQL, $queryPurchItems);
                            $i = 0;
                            if ($resPurchItems) {
                                while ($rowPurch = mysqli_fetch_assoc($resPurchItems)) {
                                    $i++;
                                    $itemId    = $rowPurch['itemid'];
                                    $barcode   = !empty($rowPurch['barcode']) ? $rowPurch['barcode'] : '-----';
                                    $name      = ($language == 'ar' ? $rowPurch['name_ar'] : $rowPurch['name_en']) ?: $rowPurch['name_ar'];
                                    $unit      = ($language == 'ar' ? $rowPurch['unit_ar'] : $rowPurch['unit_en']) ?: $rowPurch['unit_ar'];
                                    $itsQty    = round(floatval($rowPurch['total_qty']), 2);
                                    $priceItem = round(floatval($rowPurch['avg_price']), 2);
                                    $grossItem = round(floatval($rowPurch['gross_total']), 2);
                                    $discItem  = round(floatval($rowPurch['total_disc']), 2);
                                    $vatItem   = round(floatval($rowPurch['total_vat']), 2);
                                    $netItem   = round(floatval($rowPurch['net_total']), 2);

                                    $grossTotal += $grossItem;
                                    $discTotal  += $discItem;
                                    $vatTotal   += $vatItem;
                                    $purchTotal += $netItem;

                                    $safeName = mysqli_real_escape_string($adController->MySQL, $name);
                                    $safeUnit = mysqli_real_escape_string($adController->MySQL, $unit ?? '');
                                    $safeBar  = mysqli_real_escape_string($adController->MySQL, $barcode);

                                    $qIns = "INSERT INTO `report_saleitem` (`itemId`, `barcode`, `item`, `unit`, `quantity`, `price`, `discount`, `vat`, `total`, `cost`, `profit`) VALUES ('$itemId', '$safeBar', '$safeName', '$safeUnit', '$itsQty', '$priceItem', '$discItem', '$vatItem', '$netItem', '', '')";
                                    mysqli_query($adController->MySQL, $qIns);
                            ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><?=htmlspecialchars($name)?></td>
                                    <td><?=htmlspecialchars($unit)?></td>
                                    <td><?=htmlspecialchars($barcode)?></td>                                    
                                    <td><?=flout_format($itsQty)?></td>
                                    <td><?=flout_format($priceItem)?></td>
                                    <td><?=flout_format($grossItem)?></td>                                
                                    <td><?=flout_format($discItem)?></td>
                                    <td><?=flout_format($vatItem)?></td>
                                    <td><?=flout_format($netItem)?></td>                                
                                </tr>
                            <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                        <hr class="br">

                        <div class="titles"><?=RETURNED_PURCHASES?></div>
                        <table class="table table-striped table-bordered bootstrap-datatable datatable">     
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?=ITEM_NAME?></th> 
                                    <th><?=QUANTITY?></th>
                                    <th><?=PRICE?></th>
                                    <th><?=TOTAL?></th>            
                                    <th><?=DISCOUNT?></th>         
                                    <th><?=TAX?></th>             
                                    <th><?=TOTAL?></th>             
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $queryRetPurch = "SELECT 
                                                pri.itemid,
                                                i.barcode,
                                                i.name_ar, i.name_en,
                                                SUM(pri.quantity) AS total_qty,
                                                AVG(pri.cost) AS avg_price,
                                                SUM(pri.cost * pri.quantity) AS gross_total,
                                                SUM(pri.cost * pri.quantity * pri.discount / 100) AS total_disc,
                                                SUM((pri.cost * pri.quantity - (pri.cost * pri.quantity * pri.discount / 100)) * pri.tax / 100) AS total_vat,
                                                SUM((pri.cost * pri.quantity - (pri.cost * pri.quantity * pri.discount / 100)) * (1 + pri.tax / 100)) AS net_total
                                              FROM purchase_ret_items pri
                                              JOIN purchaseRet pr ON pr.invoicenumber = pri.invoice_No AND pr.storeid = '$storeid' AND pr.state = '1' $conditionRet
                                              JOIN items i ON i.id = pri.itemid AND i.storeid = '$storeid' $conditionItem
                                              WHERE pri.state = '1'
                                              GROUP BY pri.itemid
                                              ORDER BY i.name_ar ASC";
                            $resRetPurch = mysqli_query($adController->MySQL, $queryRetPurch);
                            $j = 0;
                            if ($resRetPurch) {
                                while ($rowRet = mysqli_fetch_assoc($resRetPurch)) {
                                    $j++;
                                    $retItemId   = $rowRet['itemid'];
                                    $name        = ($language == 'ar' ? $rowRet['name_ar'] : $rowRet['name_en']) ?: $rowRet['name_ar'];
                                    $itsQty      = round(floatval($rowRet['total_qty']), 2);
                                    $priceItem   = round(floatval($rowRet['avg_price']), 2);
                                    $grossItem   = round(floatval($rowRet['gross_total']), 2);
                                    $discItem    = round(floatval($rowRet['total_disc']), 2);
                                    $vatItem     = round(floatval($rowRet['total_vat']), 2);
                                    $netItem     = round(floatval($rowRet['net_total']), 2);

                                    $rePurchTotal += $netItem;

                                    $qUp = "UPDATE `report_saleitem` SET `retQuantity`='$itsQty', `retTotal`='$netItem' WHERE `itemId`='$retItemId'";
                                    mysqli_query($adController->MySQL, $qUp);
                            ?>
                                <tr>
                                    <td><?=$j?></td>
                                    <td><?=htmlspecialchars($name)?></td>
                                    <td><?=flout_format($itsQty)?></td>
                                    <td><?=flout_format($priceItem)?></td>
                                    <td><?=flout_format($grossItem)?></td>                                
                                    <td><?=flout_format($discItem)?></td>
                                    <td><?=flout_format($vatItem)?></td>
                                    <td><?=flout_format($netItem)?></td>                                
                                </tr>
                            <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>      
                <?php
                    $allGross   = flout_format($grossTotal);                
                    $allDdisc   = flout_format($discTotal);            
                    $allVat     = flout_format($vatTotal);     
                    $allPurch   = flout_format($purchTotal);
                    $allRePurch = flout_format($rePurchTotal); 
                    $net        = flout_format($purchTotal - $rePurchTotal);       
                    
                    $safeFdate  = mysqli_real_escape_string($adController->MySQL, $fr_date);
                    $safeTdate  = mysqli_real_escape_string($adController->MySQL, $to_date);
                    $safeBranch = mysqli_real_escape_string($adController->MySQL, $branchName);
                    $safeSupp   = mysqli_real_escape_string($adController->MySQL, $suppName);
                    $safeCat    = mysqli_real_escape_string($adController->MySQL, $catName);
                    $safeItem   = mysqli_real_escape_string($adController->MySQL, $itemName);

                    $querySummary = "INSERT INTO `report_saleitem_all` (`from`, `to`, `branch`, `customer`, `category`, `item`, `grossTotal`, `discTotal`, `vatTotal`, `salesTotal`, `reSalesTotal`, `netTotal`) VALUES ('$safeFdate','$safeTdate','$safeBranch','$safeSupp','$safeCat','$safeItem','$allGross','$allDdisc','$allVat','$allPurch','$allRePurch','$net')";
                    mysqli_query($adController->MySQL, $querySummary);
                ?>            
                <script type="text/javascript">
                    $(document).ready(function() {
                        $("#gross-total").html("<?=$allGross?>");                
                        $("#disc-total").html("<?=$allDdisc?>");            
                        $("#vat-total").html("<?=$allVat?>");     
                        $("#sales-total").html("<?=$allPurch?>"); 
                        $("#reSales-total").html("<?=$allRePurch?>"); 
                        $("#net-total").html("<?=$net?>");   
                    });
                </script>
                <?php
                } // End if (isset($_POST['Submit']))
                ?>
            </div>
        </div>
    </div>
	
    <div class="clearfix"></div>
    <?php require_once("footer.php"); ?>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
    <script type="text/javascript">  
        $('.datetimepicker').datetimepicker({
            format: 'yyyy-mm-dd hh:00:00',
            minView: 1,        
            showMeridian: true,
            todayBtn: true,
            autoclose: true
        });

        $(document).ready(function() {
            var config = {
                '.span3'  : { width: '90%' },
                '.span12' : { width: '100%' }
            };
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
        });
    </script>
</body>
</html>