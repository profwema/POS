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
    <title>WAM Tech Soft - <?=EXPENDITURE?></title>
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=EXPENDITURE?> - سجل المصروفات</h2>
                        </div>
                        <div class="box-content">
                            <p align="left">
                                <a class="btn btn-success" href="new_expenditure.php" title="<?=ADD_EXPENDITURE?>">
                                    <i class="icon-plus"></i> <?=ADD_EXPENDITURE ?? 'إضافة مصروف جديد'?>
                                </a>
                            </p>
                            <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                <thead>
                                    <tr>
                                        <th><?=BRANCH?></th>
                                        <th><?=DESCRIPTION?></th>
                                        <th><?=DATED?></th>
                                        <th><?=EXPENDITURE?> / <?=COST?></th>
                                        <th style="width:120px;"><?=EDITING?></th>
                                    </tr>
                                </thead>   
                                <tbody>
                                    <?php
                                    // جلب الفروع مسبقاً في مصفوفة لمنع استعلامات N+1 المتكررة
                                    $branchesMap = [];
                                    $qB = "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid'";
                                    $rB = mysqli_query($adController->MySQL, $qB);
                                    if ($rB) {
                                        while ($bRow = mysqli_fetch_assoc($rB)) {
                                            $branchesMap[$bRow['id']] = ($language == 'ar' ? $bRow['name_ar'] : $bRow['name_en']) ?: $bRow['name_ar'];
                                        }
                                    }

                                    $query = "SELECT * FROM expenditure WHERE storeid='$storeid' ORDER BY dated DESC, id DESC";
                                    $res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
                                    while ($data = mysqli_fetch_assoc($res)) {
                                        $arrayBranch = [];
                                        $storeBranch = trim($data['store_branch'] ?? '');
                                        
                                        if ($storeBranch !== '') {
                                            $bIds = explode(',', $storeBranch);
                                            foreach ($bIds as $bId) {
                                                $bId = trim($bId);
                                                if (isset($branchesMap[$bId])) {
                                                    $arrayBranch[] = htmlspecialchars($branchesMap[$bId]);
                                                }
                                            }
                                        }

                                        $branchName = !empty($arrayBranch) ? implode("<br>", $arrayBranch) : (GENERAL ?? 'عام');

                                        $idval       = urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
                                        $tableName   = urlencode($adController->encrypt_decrypt(1, 'expenditure', 0));
                                        $secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));
                                        
                                        $desc  = ($language == 'ar' ? ($data['desc_ar'] ?? $data['desc_en']) : ($data['desc_en'] ?? $data['desc_ar'])) ?? '';
                                        $dated = !empty($data['dated']) ? date("d/m/Y", strtotime($data['dated'])) : '';
                                        $cost  = floatval($data['cost'] ?? 0);

                                        echo "<tr>";
                                        echo "<td>$branchName</td>";
                                        echo "<td>" . htmlspecialchars($desc) . "</td>";
                                        echo "<td>" . htmlspecialchars($dated) . "</td>";
                                        echo "<td style='text-align:center; font-weight:bold; color:#b91c1c;'>" . number_format($cost, 2) . "</td>";
                                        echo "<td class='center' style='text-align:center;'>
                                                <a class='btn btn-info' href='edit_exp.php?sd=" . $secondIdval . "' title='" . (EDIT ?? 'تعديل') . "'>
                                                    <i class='halflings-icon white edit'></i>  
                                                </a>
                                                <a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteData(\"" . $tableName . "\",\"" . $idval . "\",\"\");' title='" . (DELETE ?? 'حذف') . "'>
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
