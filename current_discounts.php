<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - خصومات الفروع</title>
    <?php require_once("header.php"); ?>	
</head>
<body>
    <?php require_once("header_top.php"); ?>
    
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php"); ?>
            <div id="content" class="span10">
                <div class="row-fluid">
                    <div class="box span12">
                        <div class="box-header" data-original-title>
                            <h2>
                                <i class="halflings-icon tags"></i>
                                <span class="break"></span>
                                سياسات الخصم للفروع
                            </h2>
                            <div class="box-icon">
                                <a href="add_discount.php" class="btn-add">
                                    <i class="halflings-icon plus"></i> <?=ADD_NEW ?? 'إضافة خصم جديد'?>
                                </a>
                            </div>
                        </div>
                        <div class="box-content">
                            <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                <thead>
                                    <tr>
                                        <th><?=BRANCH ?? 'الفرع'?></th>
                                        <th><?=DISCOUNT ?? 'قيمة الخصم'?></th>
                                        <th><?=MIN_AMOUNT ?? 'الحد الأدنى للفاتورة'?></th>
                                        <th><?=EDITING ?? 'الإجراءات'?></th>
                                    </tr>
                                </thead>   
                                <tbody>
                                    <?php
                                    $query = "SELECT d.id, d.amount, d.minamount, b.name_ar, b.name_en
                                              FROM discount d
                                              LEFT JOIN branches b ON b.id = d.branchid AND b.storeid = '$storeid'
                                              WHERE d.storeid = '$storeid'
                                              ORDER BY d.id DESC";
                                    $res = mysqli_query($adController->MySQL, $query);
                                    if ($res) {
                                        while ($data = mysqli_fetch_assoc($res)) {
                                            $bName     = ($language == 'ar' ? $data['name_ar'] : $data['name_en']) ?: ($data['name_ar'] ?? 'كافة الفروع');
                                            $idval     = urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
                                            $tableName = urlencode($adController->encrypt_decrypt(1, 'discount', 0));

                                            echo "<tr>";
                                            echo "<td><strong>" . htmlspecialchars($bName) . "</strong></td>";
                                            echo "<td style='text-align:left; direction:ltr; font-weight:bold; color:#059669;'>" . number_format(floatval($data['amount']), 2) . "</td>";
                                            echo "<td style='text-align:left; direction:ltr;'>" . number_format(floatval($data['minamount']), 2) . "</td>";
                                            echo "<td class='center' style='white-space:nowrap;'>
                                                    <a class='btn btn-info' title='تعديل' href='edit_discount.php?d=$idval'>
                                                        <i class='halflings-icon white edit'></i>  
                                                    </a>
                                                    <a class='btn btn-danger' title='حذف' href='javascript:void(0)' onclick='javascript:deleteData(\"$tableName\",\"$idval\",\"\");'>
                                                        <i class='halflings-icon white trash'></i> 
                                                    </a>
                                                  </td>";
                                            echo "</tr>";
                                        }
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
