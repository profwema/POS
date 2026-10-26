<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid  = intval($_SESSION['storeid'] ?? 1);
$language = $_SESSION['lang'] ?? 'ar';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mysqli_query($adController->MySQL, "DELETE FROM report_saleinvoice");
    mysqli_query($adController->MySQL, "DELETE FROM report_saleinvoice_all");
}

$pgName = basename($_SERVER['PHP_SELF']);
$selectedCustomer = intval($_REQUEST['customer'] ?? 0);
$selectedBranch   = intval($_REQUEST['branch'] ?? 0);
$fr_date          = $_POST['fr_date'] ?? '';
$to_date          = $_POST['to_date'] ?? '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=INVOICE_REPORT?></title>
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
                                <a href="saleInvoice.php"><?=SALES?></a> &gt;&gt; <?=INVOICE_REPORT?>
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
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=CUSTOMER_NAME?> : </label>
                                            <div class="controls" style="margin-right:0;">
                                                <select name="customer" class="span12" data-rel="chosen">
                                                    <option value=''>-- <?=ALL ?? 'كافة العملاء'?> --</option>
                                                    <?php
                                                    $queryCust = "SELECT id, name_ar, name_en FROM customers WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                                    $resCust = mysqli_query($adController->MySQL, $queryCust);
                                                    if ($resCust) {
                                                        while ($dataCust = mysqli_fetch_assoc($resCust)) {
                                                            $cId   = $dataCust['id'];
                                                            $cName = ($language == 'ar' ? $dataCust['name_ar'] : $dataCust['name_en']) ?: $dataCust['name_ar'];
                                                            $sel   = ($selectedCustomer == $cId) ? "selected" : "";
                                                            echo "<option value='" . htmlspecialchars($cId) . "' $sel>" . htmlspecialchars($cName) . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="salesOptions" style="min-width:260px;">
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=BRANCH?> :</label>
                                            <div class="controls" style="margin-right:0;">
                                                <select name="branch" class="span12" data-rel="chosen">
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
                    $sabakahTotal = 0;  
                    $cashTotal    = 0;
                    $cardTotal    = 0;
                    $grossTotal   = 0;
                    $discTotal    = 0;  
                    $vatTotal     = 0;
                    $delvTotal    = 0;
                    $salesTotal   = 0;  
                    $leftTotal    = 0;
                    $reSalesTotal = 0;

                    $conditionSales = '';
                    $conditionRet   = '';

                    if ($selectedCustomer > 0) {
                        $conditionSales .= " AND s.customer = '$selectedCustomer'";
                        $conditionRet   .= " AND sr.customer = '$selectedCustomer'";
                        $qCust = "SELECT name_ar, name_en FROM customers WHERE id='$selectedCustomer' AND storeid='$storeid' LIMIT 1";
                        $rCust = mysqli_query($adController->MySQL, $qCust);
                        $dCust = mysqli_fetch_assoc($rCust);
                        $custName = ($language == 'ar' ? ($dCust['name_ar'] ?? '') : ($dCust['name_en'] ?? '')) ?: ($dCust['name_ar'] ?? 'الكل');
                    } else {
                        $custName = 'الكل';
                    }

                    if ($selectedBranch > 0) {
                        $conditionSales .= " AND s.branch = '$selectedBranch'";
                        $conditionRet   .= " AND sr.branch = '$selectedBranch'";
                        $qBr = "SELECT name_ar, name_en FROM branches WHERE id='$selectedBranch' AND storeid='$storeid' LIMIT 1";
                        $rBr = mysqli_query($adController->MySQL, $qBr);
                        $dBr = mysqli_fetch_assoc($rBr);
                        $branchName = ($language == 'ar' ? ($dBr['name_ar'] ?? '') : ($dBr['name_en'] ?? '')) ?: ($dBr['name_ar'] ?? 'الكل');
                    } else {
                        $branchName = 'الكل';
                    }

                    if (!empty($fr_date)) {
                        $safeFDate = mysqli_real_escape_string($adController->MySQL, $fr_date);
                        $conditionSales .= " AND s.invoice_date >= '$safeFDate'";
                        $conditionRet   .= " AND sr.invoice_date >= '$safeFDate'";
                    }
                    if (!empty($to_date)) {
                        $safeTDate = mysqli_real_escape_string($adController->MySQL, $to_date);
                        $conditionSales .= " AND s.invoice_date <= '$safeTDate'";
                        $conditionRet   .= " AND sr.invoice_date <= '$safeTDate'";
                    }
                ?>
                <div>
                    <div class="box span12">   
                        <div style="margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;">
                            <a href='reports/saleInvoiceReport.php' target='_blank' class="btn btn-success" title="طباعة تقرير PDF">
                                <i class="icon-print"></i> طباعة التقرير PDF
                            </a>
                        </div> 
                        <div class="titles"><?=TOTAlS?></div>
                        <div class="totalAll">
                            <div class="counts"> 
                                <table>
                                    <tr>
                                        <td class="title" colspan="2"><b><?=PAYMENT_TYPE?></b></td>
                                    </tr>
                                    <tr>
                                        <td class="head"><img src="img/cash.png" style="width:20px; vertical-align:middle; margin-left:5px;"/><b><?=CASH?></b></td>
                                        <td class="value"><div id='cash-total'>0.00</div></td>
                                    </tr>
                                    <tr>
                                        <td class="head"><img src="img/mada.png" style="width:20px; vertical-align:middle; margin-left:5px;"/><b><?=SABAKAH?></b></td>
                                        <td class="value"><div id='sabakah-total'>0.00</div></td>
                                    </tr>
                                    <tr>
                                        <td class="head"><img src="img/visa.png" style="width:20px; vertical-align:middle; margin-left:5px;"/><b><?=CARD_PAYMENT?></b></td>
                                        <td class="value"><div id='card-total'>0.00</div></td>
                                    </tr>
                                    <tr>
                                        <td class="head"><b><?=TOTAL?> <?=PAID?></b></td>
                                        <td class="value"><div id='pay-total'>0.00</div></td>
                                    </tr>   
                                    <tr>
                                        <td class="head"><b><?=LEFT?></b></td>
                                        <td class="value"><div id='left-total'>0.00</div></td>
                                    </tr>                                       
                                </table> 
                            </div>
                            <div class="counts"> 
                                <table>
                                    <tr>
                                        <td class="title" colspan="2"><b><?=SALES?></b></td>
                                    </tr>       
                                    <tr>
                                        <td class="head"><b><?=TOTAL?></b></td>
                                        <td class="value"><div id='gross-total'>0.00</div></td>
                                    </tr>   
                                    <tr>
                                        <td class="head"><b><?=VAT?></b></td>
                                        <td class="value"><div id='vat-total'>0.00</div></td>
                                    </tr>                                        
                                    <tr>
                                        <td class="head"><b><?=DELIVER?></b></td>
                                        <td class="value"><div id='delv-total'>0.00</div></td>
                                    </tr>                                 
                                    <tr>
                                        <td class="head"><b><?=DISCOUNT_ADDED?></b></td>
                                        <td class="value"><div id='disc-total'>0.00</div></td>
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
                                        <td class="title" colspan="2"><b><?=RETURNED_SALES?></b></td>
                                    </tr>                                   
                                    <tr>
                                        <td class="head"><b><?=TOTAL?></b></td>
                                        <td class="value"><div id='reSales-total'>0.00</div></td>
                                    </tr>
                                </table>
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

                        <div class="titles"><?=SALES?></div>
                        <table class="table table-striped table-bordered bootstrap-datatable datatable">     
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?=INVOICE_NUMBER?></th>  
                                    <th><?=DATE?></th>             
                                    <th><?=CUSTOMER_NAME?></th>    
                                    <th><?=TOTAL?></th>   
                                    <th><?=TAX?></th>             
                                    <th><?=DELIVER?></th>                                    
                                    <th><?=DISCOUNT_ADDED?></th>         
                                    <th><?=TOTAL?></th>            
                                    <th><?=PAID?></th>          
                                    <th><?=LEFT?></th>          
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $querySales = "SELECT s.*, c.name_ar AS cust_name_ar, c.name_en AS cust_name_en
                                           FROM sales s
                                           LEFT JOIN customers c ON c.id = s.customer AND c.storeid = '$storeid'
                                           WHERE s.storeid = '$storeid' AND s.state = '1' $conditionSales 
                                           ORDER BY s.invoicenumber DESC";
                            $resSales = mysqli_query($adController->MySQL, $querySales);
                            $i = 0;
                            if ($resSales) {
                                while ($data = mysqli_fetch_assoc($resSales)) {
                                    $i++;   
                                    $invoice_no   = $data['invoicenumber'] ?? '';
                                    $invoice_date = $data['invoice_date'] ?? '';
                                    $customer     = ($language == 'ar' ? ($data['cust_name_ar'] ?? '') : ($data['cust_name_en'] ?? '')) ?: ($data['cust_name_ar'] ?? '-----');

                                    $gross     = round(floatval($data['gross_total'] ?? 0), 2);
                                    $dis_added = round(floatval($data['dis_added'] ?? 0), 2);
                                    $vat_all   = round(floatval($data['vat_all'] ?? 0), 2);
                                    $delver    = round(floatval($data['delver'] ?? 0), 2);
                                    $all_total = round(floatval($data['all_total'] ?? 0), 2);

                                    $cash = round(floatval($data['cash'] ?? 0), 2);
                                    $visa = round(floatval($data['visa'] ?? 0), 2);
                                    $mada = round(floatval($data['mada'] ?? 0), 2);
                                    $paid = $cash + $visa + $mada;
                                    $left = round(floatval($data['left'] ?? 0), 2);

                                    $cashTotal    += $cash;
                                    $cardTotal    += $visa;
                                    $sabakahTotal += $mada;
                                    $leftTotal    += $left;

                                    $grossTotal += $gross;
                                    $discTotal  += $dis_added;
                                    $vatTotal   += $vat_all;
                                    $delvTotal  += $delver;
                                    $salesTotal += $all_total;

                                    $safeCust = mysqli_real_escape_string($adController->MySQL, $customer);
                                    $safeInv  = mysqli_real_escape_string($adController->MySQL, $invoice_no);
                                    $safeDate = mysqli_real_escape_string($adController->MySQL, $invoice_date);

                                    $queryIns = "INSERT INTO `report_saleinvoice` (`invoice_date`,`invoicenumber`,`customer`,`user`,`gross_total`,`dis_added`,`vat_all`,`delver`,`all_total`,`paid`,`left`,`cost`,`profit`) VALUES ('$safeDate','$safeInv','$safeCust','','$gross','$dis_added','$vat_all','$delver','$all_total','$paid','$left','','')";
                                    mysqli_query($adController->MySQL, $queryIns);
                            ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><a href='edit_saleInvoice.php?sd=<?=urlencode($invoice_no)?>'><?=htmlspecialchars($invoice_no)?></a></td>  
                                    <td><?=htmlspecialchars($invoice_date)?></td>
                                    <td><?=htmlspecialchars($customer)?></td>
                                    <td><?=flout_format($gross)?></td>
                                    <td><?=flout_format($vat_all)?></td>
                                    <td><?=flout_format($delver)?></td>                                
                                    <td><?=flout_format($dis_added)?></td>
                                    <td><?=flout_format($all_total)?></td>                                
                                    <td><?=flout_format($paid)?></td>
                                    <td><?=flout_format($left)?></td>
                                </tr>
                            <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                        <hr class="br">

                        <div class="titles"><?=RETURNED_SALES?></div>
                        <table class="table table-striped table-bordered bootstrap-datatable datatable">     
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?=INVOICE_NUMBER?></th>  
                                    <th><?=DATE?></th>             
                                    <th><?=CUSTOMER_NAME?></th>    
                                    <th><?=SALE_INVOICE?></th>                                  
                                    <th><?=TOTAL?></th>  
                                    <th><?=TAX?></th>             
                                    <th><?=DISCOUNT_ADDED?></th>         
                                    <th><?=TOTAL?></th>            
                                    <th><?=PAYMENT?></th>          
                                    <th><?=LEFT?></th>          
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $queryRet = "SELECT sr.*, c.name_ar AS cust_name_ar, c.name_en AS cust_name_en
                                         FROM sales_ret sr
                                         LEFT JOIN customers c ON c.id = sr.customer AND c.storeid = '$storeid'
                                         WHERE sr.storeid = '$storeid' AND sr.state = '1' $conditionRet 
                                         ORDER BY sr.invoicenumber DESC";
                            $resRet = mysqli_query($adController->MySQL, $queryRet);
                            $j = 0;
                            if ($resRet) {
                                while ($dataRet = mysqli_fetch_assoc($resRet)) {
                                    $j++;
                                    $invoice_no   = $dataRet['invoicenumber'] ?? '';
                                    $salesInvoice = $dataRet['salesInvoice'] ?? '';
                                    $invoice_date = $dataRet['invoice_date'] ?? '';
                                    $customer     = ($language == 'ar' ? ($dataRet['cust_name_ar'] ?? '') : ($dataRet['cust_name_en'] ?? '')) ?: ($dataRet['cust_name_ar'] ?? '-----');

                                    $gross     = round(floatval($dataRet['gross_total'] ?? 0), 2);
                                    $dis_added = round(floatval($dataRet['dis_added'] ?? 0), 2);
                                    $vat_all   = round(floatval($dataRet['vat_all'] ?? 0), 2);
                                    $all_total = round(floatval($dataRet['all_total'] ?? 0), 2);
                                    $paid      = (($dataRet['paymentType'] ?? 0) == 1) ? $all_total : 0;
                                    $left      = $all_total - $paid;
                                    $reSalesTotal += $all_total;
                            ?>
                                <tr>
                                    <td><?=$j?></td>
                                    <td><a href='edit_salesRet.php?sd=<?=urlencode($invoice_no)?>'><?=htmlspecialchars($invoice_no)?></a></td>  
                                    <td><?=htmlspecialchars($invoice_date)?></td>
                                    <td><?=htmlspecialchars($customer)?></td>
                                    <td><?=htmlspecialchars($salesInvoice)?></td>                                  
                                    <td><?=flout_format($gross)?></td>
                                    <td><?=flout_format($vat_all)?></td>
                                    <td><?=flout_format($dis_added)?></td>
                                    <td><?=flout_format($all_total)?></td>                                
                                    <td><?=flout_format($paid)?></td>
                                    <td><?=flout_format($left)?></td>
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
                    $allDelv    = flout_format($delvTotal);
                    $allSales   = flout_format($salesTotal);
                    $allReSales = flout_format($reSalesTotal); 
                    $net        = flout_format($salesTotal - $reSalesTotal);               

                    $allMada = flout_format($sabakahTotal);            
                    $allCash = flout_format($cashTotal);     
                    $allCard = flout_format($cardTotal); 
                    $allPaid = flout_format($cardTotal + $cashTotal + $sabakahTotal);
                    $allLeft = flout_format($leftTotal);           

                    $safeFdate     = mysqli_real_escape_string($adController->MySQL, $fr_date);
                    $safeTdate     = mysqli_real_escape_string($adController->MySQL, $to_date);
                    $safeBranch    = mysqli_real_escape_string($adController->MySQL, $branchName);
                    $safeCustName  = mysqli_real_escape_string($adController->MySQL, $custName);

                    $querySummary = "INSERT INTO `report_saleinvoice_all` (`from`, `to`, `branch`, `user`, `customer`, `grossTotal`, `discTotal`, `vatTotal`, `delvTotal`, `salesTotal`, `reSalesTotal`, `netTotal`, `payTotal`, `cashTotal`, `cardTotal`, `sabakahTotal`, `leftTotal`) VALUES ('$safeFdate','$safeTdate','$safeBranch','','$safeCustName','$allGross','$allDdisc','$allVat','$allDelv','$allSales','$allReSales','$net','$allPaid','$allCash','$allCard','$allMada','$allLeft')";
                    mysqli_query($adController->MySQL, $querySummary);
                ?>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $("#sabakah-total").html("<?=$allMada?>");            
                        $("#cash-total").html("<?=$allCash?>");     
                        $("#card-total").html("<?=$allCard?>"); 
                        $("#pay-total").html("<?=$allPaid?>");
                        $("#left-total").html("<?=$allLeft?>");    
                        
                        $("#gross-total").html("<?=$allGross?>");                
                        $("#disc-total").html("<?=$allDdisc?>");            
                        $("#vat-total").html("<?=$allVat?>");     
                        $("#delv-total").html("<?=$allDelv?>");
                        $("#sales-total").html("<?=$allSales?>"); 
                        $("#reSales-total").html("<?=$allReSales?>"); 
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
                '.span3'  : { width: '80%' },
                '.span12' : { width: '100%' }
            };
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
        });
    </script>
</body>
</html>