<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid  = intval($_SESSION['storeid'] ?? 1);
$language = $_SESSION['lang'] ?? 'ar';
$pgName   = basename($_SERVER['PHP_SELF']);

$orderid  = trim($_REQUEST['o'] ?? '');
$catid    = intval($_REQUEST['catid'] ?? 0);
$itemId   = intval($_REQUEST['iid'] ?? 0);
$branchid = intval($_REQUEST['br'] ?? 0);
$empId    = intval($_REQUEST['ei'] ?? 0);

$fDate = trim($_REQUEST['from_date'] ?? date('Y-m-d'));
$tDate = trim($_REQUEST['to_date'] ?? date('Y-m-d'));

$fromDateTime = date('Y-m-d 00:00:00', strtotime($fDate));
$toDateTime   = date('Y-m-d 23:59:59', strtotime($tDate));

$conditionArr = ["o.storeid = '$storeid'"];
$conditionArr[] = "o.specific_date >= '$fromDateTime'";
$conditionArr[] = "o.specific_date <= '$toDateTime'";

if ($orderid !== '') {
    $escOrder = mysqli_real_escape_string($adController->MySQL, $orderid);
    $conditionArr[] = "(o.hasvalue = '$escOrder' OR o.invoice_number = '$escOrder')";
}
if ($itemId > 0) {
    $conditionArr[] = "o.itemid = '$itemId'";
}
if ($catid > 0) {
    $conditionArr[] = "i.catid = '$catid'";
}
if ($branchid > 0) {
    $conditionArr[] = "o.branch = '$branchid'";
}
if ($empId > 0) {
    $conditionArr[] = "o.userid = '$empId'";
}

$whereClause = implode(" AND ", $conditionArr);

// Execute single aggregated query with JOINs
$query = "SELECT 
            o.id,
            o.dated,
            o.specific_date,
            o.hasvalue,
            o.invoice_number,
            o.qty,
            o.cost,
            o.discount,
            o.vat,
            o.payment_type,
            b.name_ar AS branch_ar,
            b.name_en AS branch_en,
            i.name_ar AS item_ar,
            i.name_en AS item_en,
            c.name_ar AS cat_ar,
            c.name_en AS cat_en,
            e.name_ar AS emp_ar,
            e.name_en AS emp_en
          FROM orders o
          LEFT JOIN branches b ON b.id = o.branch AND b.storeid = '$storeid'
          LEFT JOIN items i ON i.id = o.itemid AND i.storeid = '$storeid'
          LEFT JOIN categories c ON c.id = i.catid AND c.storeid = '$storeid'
          LEFT JOIN employees e ON e.id = o.userid AND e.storeid = '$storeid'
          WHERE $whereClause
          ORDER BY o.specific_date DESC, o.id DESC
          LIMIT 2500";

$res = mysqli_query($adController->MySQL, $query);

$tBodyDesktop = [];
$tBodyMobile  = [];
$finalTotal   = 0;
$cashTotal    = 0;
$cardTotal    = 0;
$sabakahTotal = 0;
$totalQty     = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $qty       = floatval($row['qty']);
        $cost      = floatval($row['cost']);
        $discount  = floatval($row['discount']);
        $vatRate   = floatval($row['vat']);
        
        $subtotal  = ($qty * $cost) - $discount;
        $vatAmount = ($vatRate > 0) ? (($subtotal / 100) * $vatRate) : 0;
        $lineTotal = $subtotal + $vatAmount;

        $finalTotal += $lineTotal;
        $totalQty   += $qty;

        $pType = strval($row['payment_type'] ?? '');
        if ($pType === '1') {
            $cashTotal += $lineTotal;
        } elseif ($pType === '2') {
            $cardTotal += $lineTotal;
        } elseif ($pType === '3') {
            $sabakahTotal += $lineTotal;
        }

        $branchName = ($language == 'ar' ? $row['branch_ar'] : $row['branch_en']) ?: ($row['branch_ar'] ?? '---');
        $itemName   = ($language == 'ar' ? $row['item_ar'] : $row['item_en']) ?: ($row['item_ar'] ?? '---');
        $catName    = ($language == 'ar' ? $row['cat_ar'] : $row['cat_en']) ?: ($row['cat_ar'] ?? '---');
        $orderDisplay = $row['hasvalue'] ?: ($row['invoice_number'] ?: $row['id']);
        $dateDisplay  = !empty($row['specific_date']) ? date("Y-m-d H:i", strtotime($row['specific_date'])) : date("Y-m-d H:i", strtotime($row['dated']));

        $tBodyDesktop[] = "<tr>
            <td>" . htmlspecialchars($dateDisplay) . "</td>
            <td>" . htmlspecialchars($branchName) . "</td>
            <td><strong>" . htmlspecialchars($orderDisplay) . "</strong></td>
            <td>" . htmlspecialchars($catName) . "</td>
            <td>" . htmlspecialchars($itemName) . "</td>
            <td style='text-align:center; font-weight:bold;'>" . number_format($qty, 2) . "</td>
            <td style='text-align:left; direction:ltr;'>" . number_format($cost, 2) . "</td>
            <td style='text-align:left; direction:ltr; color:#dc2626;'>" . number_format($discount, 2) . "</td>
            <td style='text-align:left; direction:ltr; font-weight:bold; color:#059669;'>" . number_format($lineTotal, 2) . "</td>
        </tr>";

        $tBodyMobile[] = "<tr>
            <td>" . htmlspecialchars($dateDisplay) . "</td>
            <td><strong>" . htmlspecialchars($orderDisplay) . "</strong></td>
            <td>" . htmlspecialchars($itemName) . "</td>
            <td>" . number_format($qty, 2) . " &times; " . number_format($cost, 2) . ($discount > 0 ? " (-" . number_format($discount, 2) . ")" : "") . "</td>
            <td style='text-align:left; direction:ltr; font-weight:bold; color:#059669;'>" . number_format($lineTotal, 2) . "</td>
        </tr>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - تقرير تفاصيل المبيعات</title>
    <?php require_once("header.php"); ?>	

    <style>
        .filter-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .stats-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1;
            min-width: 180px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-box .title {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .stat-box .num {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            direction: ltr;
        }
        @media (max-width: 700px) {
            #mobile { display: block; }
            #desktop { display: none; }
        }
        @media (min-width: 701px) {
            #mobile { display: none; }
            #desktop { display: block; }
        }
    </style>
</head>
<body>
    <?php require_once("header_top.php"); ?>
    
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php"); ?>
            <div id="content" class="span10">
                <div class="box span12">
                    <div class="box-header" data-original-title>
                        <h2>
                            <i class="halflings-icon list-alt"></i>
                            <span class="break"></span>
                            تقرير تفاصيل المبيعات اليومية
                        </h2>
                        <div class="box-icon">
                            <a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="box-content">
                        <form class="form-horizontal" action="<?=$pgName?>" id='form' method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                            <fieldset>
                                <div class="row-fluid">
                                    <div class="span3">
                                        <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=ORDER_ID ?? 'رقم الطلب'?></label>
                                        <div class="controls" style="margin-right:0;">
                                            <select class="span12" data-rel="chosen" name="o">
                                                <option value="">-- <?=ALL ?? 'كافة الطلبات'?> --</option>
                                                <?php
                                                $qOrders = "SELECT DISTINCT hasvalue FROM orders WHERE storeid='$storeid' AND specific_date >= '$fromDateTime' AND specific_date <= '$toDateTime' AND hasvalue != '' ORDER BY id DESC LIMIT 100";
                                                $rOrders = mysqli_query($adController->MySQL, $qOrders);
                                                if ($rOrders) {
                                                    while ($dOrder = mysqli_fetch_assoc($rOrders)) {
                                                        $oid = $dOrder['hasvalue'];
                                                        $sel = ($orderid === $oid) ? "selected" : "";
                                                        echo "<option value='" . htmlspecialchars($oid) . "' $sel>" . htmlspecialchars($oid) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="span3">
                                        <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=CATEGORIES ?? 'القسم'?></label>
                                        <div class="controls" style="margin-right:0;">
                                            <select class="span12" data-rel="chosen" name="catid">
                                                <option value="">-- <?=ALL ?? 'كافة الأقسام'?> --</option>
                                                <?php
                                                $qCats = "SELECT id, name_ar, name_en FROM categories WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                                $rCats = mysqli_query($adController->MySQL, $qCats);
                                                if ($rCats) {
                                                    while ($dCat = mysqli_fetch_assoc($rCats)) {
                                                        $cName = ($language == 'ar' ? $dCat['name_ar'] : $dCat['name_en']) ?: $dCat['name_ar'];
                                                        $sel   = ($catid == $dCat['id']) ? "selected" : "";
                                                        echo "<option value='" . htmlspecialchars($dCat['id']) . "' $sel>" . htmlspecialchars($cName) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="span3">
                                        <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=ITEMS ?? 'الصنف'?></label>
                                        <div class="controls" style="margin-right:0;">
                                            <select class="span12" data-rel="chosen" name="iid">
                                                <option value="">-- <?=ALL ?? 'كافة الأصناف'?> --</option>
                                                <?php
                                                $itemFilter = ($catid > 0) ? " AND catid='$catid'" : "";
                                                $qItems = "SELECT id, name_ar, name_en, barcode FROM items WHERE storeid='$storeid' $itemFilter ORDER BY name_ar ASC";
                                                $rItems = mysqli_query($adController->MySQL, $qItems);
                                                if ($rItems) {
                                                    while ($dItem = mysqli_fetch_assoc($rItems)) {
                                                        $iName = ($language == 'ar' ? $dItem['name_ar'] : $dItem['name_en']) ?: $dItem['name_ar'];
                                                        $bar   = !empty($dItem['barcode']) ? " ({$dItem['barcode']})" : "";
                                                        $sel   = ($itemId == $dItem['id']) ? "selected" : "";
                                                        echo "<option value='" . htmlspecialchars($dItem['id']) . "' $sel>" . htmlspecialchars($iName . $bar) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="span3">
                                        <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=BRANCH ?? 'الفرع'?></label>
                                        <div class="controls" style="margin-right:0;">
                                            <select class="span12" data-rel="chosen" name="br">
                                                <option value="">-- <?=ALL ?? 'كافة الفروع'?> --</option>
                                                <?php
                                                $qBranches = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                                $rBranches = mysqli_query($adController->MySQL, $qBranches);
                                                if ($rBranches) {
                                                    while ($dBranch = mysqli_fetch_assoc($rBranches)) {
                                                        $bName = ($language == 'ar' ? $dBranch['name_ar'] : $dBranch['name_en']) ?: $dBranch['name_ar'];
                                                        $sel   = ($branchid == $dBranch['id']) ? "selected" : "";
                                                        echo "<option value='" . htmlspecialchars($dBranch['id']) . "' $sel>" . htmlspecialchars($bName) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row-fluid" style="margin-top:15px;">
                                    <div class="span3">
                                        <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=CASHIER ?? 'الكاشير / الموظف'?></label>
                                        <div class="controls" style="margin-right:0;">
                                            <select class="span12" data-rel="chosen" name="ei">
                                                <option value="">-- <?=ALL ?? 'كافة الكاشيرات'?> --</option>
                                                <?php
                                                $qEmp = "SELECT id, name_ar, name_en FROM employees WHERE storeid='$storeid' ORDER BY name_ar ASC";
                                                $rEmp = mysqli_query($adController->MySQL, $qEmp);
                                                if ($rEmp) {
                                                    while ($dEmp = mysqli_fetch_assoc($rEmp)) {
                                                        $eName = ($language == 'ar' ? $dEmp['name_ar'] : $dEmp['name_en']) ?: $dEmp['name_ar'];
                                                        $sel   = ($empId == $dEmp['id']) ? "selected" : "";
                                                        echo "<option value='" . htmlspecialchars($dEmp['id']) . "' $sel>" . htmlspecialchars($eName) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="span3">
                                        <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=FROM_DATE ?? 'من تاريخ'?></label>
                                        <div class="controls" style="margin-right:0;">
                                            <input type="date" class="span12" id="from_date" name="from_date" value="<?=htmlspecialchars($fDate)?>">
                                        </div>
                                    </div>

                                    <div class="span3">
                                        <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=TO_DATE ?? 'إلى تاريخ'?></label>
                                        <div class="controls" style="margin-right:0;">
                                            <input type="date" class="span12" id="to_date" name="to_date" value="<?=htmlspecialchars($tDate)?>">
                                        </div>
                                    </div>

                                    <div class="span3" style="padding-top:24px;">
                                        <button type="submit" class="btn btn-primary span6" style="padding:7px;"><?=LOAD_FORM ?? 'عرض التقرير'?></button>
                                        <a href="<?=$pgName?>" class="btn span5" style="padding:7px;"><?=CANCEL ?? 'إعادة ضبط'?></a>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>

                <!-- إحصائيات سريعة للنتائج -->
                <div class="stats-summary">
                    <div class="stat-box" style="border-top:3px solid #10b981;">
                        <div class="title">إجمالي المبيعات</div>
                        <div class="num" style="color:#059669;"><?=number_format($finalTotal, 2)?></div>
                    </div>
                    <div class="stat-box" style="border-top:3px solid #3b82f6;">
                        <div class="title">إجمالي الكاش (نقدي)</div>
                        <div class="num" style="color:#2563eb;"><?=number_format($cashTotal, 2)?></div>
                    </div>
                    <div class="stat-box" style="border-top:3px solid #f59e0b;">
                        <div class="title">إجمالي البطاقة / الشبكة</div>
                        <div class="num" style="color:#d97706;"><?=number_format($cardTotal + $sabakahTotal, 2)?></div>
                    </div>
                    <div class="stat-box" style="border-top:3px solid #6366f1;">
                        <div class="title">إجمالي الكميات المباعة</div>
                        <div class="num" style="color:#4f46e5;"><?=number_format($totalQty, 2)?></div>
                    </div>
                </div>

                <div class="box span12" style="margin-right:0;">
                    <div class="box-header" data-original-title>
                        <h2><i class="halflings-icon th-list"></i><span class="break"></span>تفاصيل بنود المبيعات (<?=count($tBodyDesktop)?> بند)</h2>
                    </div>
                    <div class="box-content" id="desktop">
                        <table class="table table-striped table-bordered bootstrap-datatable datatable">
                            <thead>
                                <tr>
                                    <th><?=DATED ?? 'التاريخ'?></th>
                                    <th><?=BRANCH ?? 'الفرع'?></th>
                                    <th><?=ORDER_ID ?? 'رقم الطلب'?></th>
                                    <th><?=CATEGORY ?? 'القسم'?></th>
                                    <th><?=ITEMS ?? 'الصنف'?></th>
                                    <th><?=QUANTITY ?? 'الكمية'?></th>
                                    <th><?=PRICE ?? 'السعر'?></th>
                                    <th><?=DISCOUNT ?? 'الخصم'?></th>
                                    <th><?=TOTAL ?? 'الإجمالي'?></th>
                                </tr>
                            </thead>   
                            <tbody>
                                <?php echo implode("", $tBodyDesktop); ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="box-content" id="mobile">
                        <table class="table table-striped table-bordered bootstrap-datatable datatable">
                            <thead>
                                <tr>
                                    <th><?=DATED ?? 'التاريخ'?></th>
                                    <th><?=ORDER_ID ?? 'رقم الطلب'?></th>
                                    <th><?=ITEMS ?? 'الصنف'?></th>
                                    <th><?=DETAIL ?? 'التفاصيل'?></th>
                                    <th><?=PRICE ?? 'الإجمالي'?></th>
                                </tr>
                            </thead>   
                            <tbody>
                                <?php echo implode("", $tBodyMobile); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix"></div>
    
    <?php require_once("footer.php"); ?>
</body>
</html>
