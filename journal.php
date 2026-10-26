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
    <title>WAM Tech Soft - <?=JOURNAL?></title>
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=JOURNAL?> - قيود اليومية العامة</h2>
                        </div>
                        <div class="box-content">
                            <p align="left">
                                <a class="btn btn-success" href="add_journal.php" title="<?=ADD_NEW ?? 'إضافة قيد جديد'?>">
                                    <i class="icon-plus"></i> <?=ADD_NEW ?? 'إضافة قيد يومية'?>
                                </a>
                            </p>
                            <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                <thead>
                                    <tr>
                                        <th><?=JOURNAL_NUMBER?></th>
                                        <th><?=DOCUMENT?></th>                                                              
                                        <th><?=BRANCH?></th>
                                        <th><?=DISCRIPTION?></th>
                                        <th><?=DEBIT?></th>
                                        <th><?=CREDIT?></th>
                                        <th><?=DATE?></th>
                                        <th style="width:120px;"><?=EDITING?></th>
                                    </tr>
                                </thead>   
                                <tbody>
                                    <?php
                                    // استعلام موحد مع LEFT JOIN لمنع استعلامات N+1 المتكررة
                                    $query = "SELECT j.*, b.name_ar AS branch_name_ar, b.name_en AS branch_name_en 
                                              FROM journal j
                                              LEFT JOIN branches b ON b.id = j.brarnch AND b.storeid = '$storeid'
                                              WHERE j.state = '1' 
                                              ORDER BY j.date DESC, j.id DESC";
                                    $res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));

                                    while ($data = mysqli_fetch_assoc($res)) {
                                        $journalNo   = $data["journalNo"] ?? '';										
                                        $docNo       = $data["docNo"] ?? '';
                                        $disc        = $data["disc"] ?? '';
                                        $totalDebit  = floatval($data["totalDebit"] ?? 0);
                                        $totalCredit = floatval($data["totalCredit"] ?? 0);                                                                                
                                        $date        = $data['date'] ?? '';

                                        $branchName = ($language == 'ar' ? $data['branch_name_ar'] : $data['branch_name_en']) ?? '';
                                        if (empty($branchName)) {
                                            $branchName = !empty($data['branch_name_ar']) ? $data['branch_name_ar'] : ($data['branch_name_en'] ?? '-----');
                                        }

                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($journalNo) . "</td>";
                                        echo "<td>" . htmlspecialchars($docNo ?: '-----') . "</td>";
                                        echo "<td>" . htmlspecialchars($branchName) . "</td>";
                                        echo "<td>" . htmlspecialchars($disc) . "</td>";
                                        echo "<td style='text-align:center; font-weight:bold; color:#15803d;'>" . number_format($totalDebit, 2) . "</td>";
                                        echo "<td style='text-align:center; font-weight:bold; color:#b91c1c;'>" . number_format($totalCredit, 2) . "</td>";                                                                                        
                                        echo "<td>" . htmlspecialchars($date) . "</td>";
                                        echo "<td class='center' style='text-align:center;'>
                                                <a class='btn btn-info' href='edit_journal.php?no=" . urlencode($journalNo) . "' title='" . (EDIT ?? 'تعديل') . "'>
                                                    <i class='halflings-icon white edit'></i>  
                                                </a>
                                                <a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteJournal(\"" . htmlspecialchars($journalNo, ENT_QUOTES) . "\");' title='" . (DELETE ?? 'حذف') . "'>
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
