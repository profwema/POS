<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=INCOMING_ENTRY?></title>
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=INCOMING_ENTRY?></h2>
                        </div>
                        <div class="box-content">
                            <p align="left">
                                <a class="btn btn-success" href="add_income.php" title="<?=ADD_NEW ?? 'إضافة جديد'?>">
                                    <i class="icon-plus"></i> <?=ADD_NEW ?? 'إضافة إذن وارد'?>
                                </a>
                            </p>
                            <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                <thead>
                                    <tr>
                                        <th><?=INVOICE_NUMBER?></th>
                                        <th><?=TYPE?></th>
                                        <th><?=RELATED_INVOICE_NO?></th>
                                        <th><?=BRANCH?></th>
                                        <th><?=DATE?></th>
                                        <th style="width:120px;"><?=EDITING?></th>
                                    </tr>
                                </thead>   
                                <tbody>
                                    <?php
                                    // استعلام واحد مع LEFT JOIN لمنع N+1 استعلام
                                    $query = "SELECT inc.*, b.name_ar AS branch_name_ar, b.name_en AS branch_name_en 
                                              FROM income inc
                                              LEFT JOIN branches b ON b.id = inc.branch AND b.storeid = '$storeid'
                                              WHERE inc.storeid = '$storeid' AND inc.state = '1' 
                                              ORDER BY inc.date DESC, inc.id DESC";
                                    $res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));

                                    while ($data = mysqli_fetch_assoc($res)) {
                                        $branchName = ($language == 'ar' ? $data['branch_name_ar'] : $data['branch_name_en']) ?? '';
                                        if (empty($branchName)) {
                                            $branchName = !empty($data['branch_name_ar']) ? $data['branch_name_ar'] : ($data['branch_name_en'] ?? '-----');
                                        }

                                        $invoice        = $data["income_No"] ?? '';	
                                        $type           = $data["invoice_type"] ?? '';
                                        $purch_invoice  = $data["invoice_No"] ?? '';
                                        $purchase_date  = $data['date'] ?? '';

                                        $idval          = urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
                                        $invoiceTable   = urlencode($adController->encrypt_decrypt(1, 'income', 0));
                                        $detailTable    = urlencode($adController->encrypt_decrypt(1, 'income_items', 0));
                                        $secondIdval    = urlencode($adController->encrypt_decrypt(1, $idval, 0));

                                        $typeLabel = '';
                                        if (!empty($type)) {
                                            $typeLabel = defined($type) ? constant($type) : $type;
                                        }

                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($invoice) . "</td>";
                                        echo "<td>" . htmlspecialchars($typeLabel) . "</td>";
                                        echo "<td>" . htmlspecialchars($purch_invoice ?: '-----') . "</td>";
                                        echo "<td>" . htmlspecialchars($branchName) . "</td>";
                                        echo "<td>" . htmlspecialchars($purchase_date) . "</td>";
                                        
                                        if (empty($purch_invoice)) {
                                            echo "<td class='center' style='text-align:center;'>
                                                    <a class='btn btn-info' href='edit_income.php?sd=" . $secondIdval . "' title='" . (EDIT ?? 'تعديل') . "'>
                                                        <i class='halflings-icon white edit'></i>  
                                                    </a>
                                                    <a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteInvoice(\"" . $invoiceTable . "\",\"" . $detailTable . "\",\"" . $idval . "\");' title='" . (DELETE ?? 'حذف') . "'>
                                                        <i class='halflings-icon white trash'></i> 
                                                    </a>
                                                </td>";
                                        } else {
                                            echo "<td class='center' style='text-align:center;'><span class='label label-info'>" . (RELATED_INVOICE_NO ?? 'مرتبط بفاتورة') . "</span></td>";
                                        }
                                        echo "</tr>";
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
	
    <div class="clearfix"></div>
    <?php require_once("footer.php"); ?>
</body>
</html>
