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
    <title>WAM Tech Soft - <?=STORE_TRANSFAIR?></title>
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=STORE_TRANSFAIR?></h2>
                        </div>
                        <div class="box-content">
                            <p align="left">
                                <a class="btn btn-success" href="add_storeTrans.php" title="<?=ADD_STORE_TRANSFAIR?>">
                                    <i class="icon-plus"></i> <?=ADD_STORE_TRANSFAIR?>
                                </a>
                            </p>
                            <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                <thead>
                                    <tr>
                                        <th><?=TRANS_NUMBER?></th>
                                        <th><?=STORE_FROM?></th>
                                        <th><?=STORE_TO?></th>
                                        <th><?=ENTRY_DATE?></th>
                                        <th><?=CREATED_ON?></th>
                                        <th style="width:120px;"><?=EDITING?></th>
                                    </tr>
                                </thead>   
                                <tbody>
                                    <?php
                                    // استعلام موحد مع LEFT JOIN لمنع مشكلة N+1 استعلام
                                    $query = "SELECT st.*, 
                                                     sf.name_ar AS from_name_ar, sf.name_en AS from_name_en,
                                                     st_to.name_ar AS to_name_ar, st_to.name_en AS to_name_en
                                              FROM store_trans st
                                              LEFT JOIN stores sf ON sf.id = st.storeFrom AND sf.storeid = '$storeid'
                                              LEFT JOIN stores st_to ON st_to.id = st.storeTo AND st_to.storeid = '$storeid'
                                              WHERE st.storeid = '$storeid' AND st.state = '1'
                                              ORDER BY st.id DESC";
                                    $res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));

                                    while ($data = mysqli_fetch_assoc($res)) {
                                        $invoice = $data["trans_number"] ?? '';	

                                        $from = ($language == 'ar' ? $data['from_name_ar'] : $data['from_name_en']) ?? '';
                                        if (empty($from)) {
                                            $from = !empty($data['from_name_ar']) ? $data['from_name_ar'] : ($data['from_name_en'] ?? '-----');
                                        }

                                        $to = ($language == 'ar' ? $data['to_name_ar'] : $data['to_name_en']) ?? '';
                                        if (empty($to)) {
                                            $to = !empty($data['to_name_ar']) ? $data['to_name_ar'] : ($data['to_name_en'] ?? '-----');
                                        }

                                        $trans_date = $data['trans_date'] ?? '';
                                        $dated      = $data["dated"] ?? '';

                                        $idval       = urlencode($adController->encrypt_decrypt(1, $invoice, 0));
                                        $secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));

                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($invoice) . "</td>";
                                        echo "<td>" . htmlspecialchars($from) . "</td>";
                                        echo "<td>" . htmlspecialchars($to) . "</td>";
                                        echo "<td>" . htmlspecialchars($trans_date) . "</td>";
                                        echo "<td>" . htmlspecialchars($dated) . "</td>";
                                        echo "<td class='center' style='text-align:center;'>
                                                <a class='btn btn-info' href='edit_storeTrans.php?sd=" . $secondIdval . "' title='" . (EDIT ?? 'تعديل') . "'>
                                                    <i class='halflings-icon white edit'></i>  
                                                </a>
                                                <a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteTrans(\"" . $idval . "\");' title='" . (DELETE ?? 'حذف') . "'>
                                                    <i class='halflings-icon white trash'></i> 
                                                </a>
                                            </td>";
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
