<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$storeid  = intval($_SESSION['storeid'] ?? 1);
$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$acc = array();

$totalCredit = 0;
$totalDebit = 0;
$BalanceCredit = 0;
$BalanceDebit = 0;

function getAccounts($account)
{
    global $acc, $adController;
    $safeAccount = mysqli_real_escape_string($adController->MySQL, $account);
    $query       = "SELECT id, hasChild FROM accounts WHERE code = '$safeAccount' LIMIT 1";
    $result      = mysqli_query($adController->MySQL, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $fetchRes = mysqli_fetch_assoc($result);
        if (($fetchRes['hasChild'] ?? 0) == 0) {
            $acc[] = $account;
        } else {
            $sql   = "SELECT code FROM accounts WHERE parent = '" . intval($fetchRes['id']) . "'";
            $query = mysqli_query($adController->MySQL, $sql);
            if ($query && mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                    getAccounts($row['code']);                      
                }
            }           
        }
    } else {
        $acc[] = $account;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=ACCOUNT_STATEMENT ?? 'كشف حساب'?></title>
    <?php require_once("header.php"); ?>	
    <style>
        .datetime {
            width: 85%;
            border: 1px solid #10b981 !important;
            padding: 6px 10px;
            border-radius: 4px;
        }
        .credit {
            color: #15803d;
            font-weight: bold;
        }
        .debit {
            color: #b91c1c;
            font-weight: bold;
        }
        .movement {
            background-color: #fefce8 !important;
        }
        .balance {
            background-color: #eff6ff !important;
        }          
        .totals-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
            margin-bottom: 15px;
        }
        .filter-item {
            min-width: 240px;
        }
    </style>
    <link href="css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=ACCOUNT_STATEMENT ?? 'كشف حساب'?></h2>
                        </div>
                        <div class="box-content">
                            <form class="form-horizontal" autocomplete="off" action="account-statement.php" id='form' method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <fieldset>
                                    <div class="filter-row">
                                        <div class="filter-item" style="flex:2;">
                                            <label class="control-label" for="account" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=ACCOUNT_NAME?> :</label>
                                            <div class="controls" style="margin-right:0;">
                                                <select class='span12' name="account" id='account' data-rel="chosen">
                                                    <option value="0">-- كافة الحسابات --</option>
                                                    <?php
                                                    // استعلام موحد مع LEFT JOIN لجلب الحساب واسم الحساب الأب بدون تكرار
                                                    $query = "SELECT a.id, a.code, a.name_ar, a.name_en, p.name_ar AS parent_ar, p.name_en AS parent_en
                                                              FROM accounts a
                                                              LEFT JOIN accounts p ON p.id = a.parent
                                                              ORDER BY a.parent ASC, a.code ASC";
                                                    $res = mysqli_query($adController->MySQL, $query);
                                                    $currAcc = $_REQUEST["account"] ?? '';
                                                    while ($data = mysqli_fetch_assoc($res)) {
                                                        $name   = ($language == 'en' && !empty($data['name_en'])) ? $data['name_en'] : (!empty($data['name_ar']) ? $data['name_ar'] : $data['name_en']);
                                                        $parent = ($language == 'en' && !empty($data['parent_en'])) ? $data['parent_en'] : (!empty($data['parent_ar']) ? $data['parent_ar'] : $data['parent_en']);
                                                        $code   = $data['code'];
                                                        $sel    = ($currAcc !== '' && $currAcc == $code) ? " selected='selected' " : "";
                                                        $label  = ($parent ? $parent . " -> " : "") . $name;
                                                        echo "<option value='" . htmlspecialchars($code) . "' $sel>" . htmlspecialchars($label) . "</option>";
                                                    }
                                                    ?>
                                                </select> 
                                            </div>
                                        </div>

                                        <div class="filter-item" style="flex:1;">
                                            <label class="control-label" for="branch" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=BRANCH?> :</label>
                                            <div class="controls" style="margin-right:0;">
                                                <select name="branch" id="branch" class="span12" data-rel="chosen">
                                                    <option value="">-- كافة الفروع --</option>
                                                    <?php
                                                    $query = "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY id ASC";
                                                    $res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
                                                    $currBranch = $_REQUEST['branch'] ?? '';
                                                    while ($data = mysqli_fetch_assoc($res)) {
                                                        $id   = $data['id'];
                                                        $name = $data['name_' . $language] ?? $data['name_ar'];
                                                        $sel  = ($currBranch !== '' && $currBranch == $id) ? " selected='selected' " : "";
                                                        echo "<option value='" . htmlspecialchars($id) . "' $sel>" . htmlspecialchars($name) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="filter-row">
                                        <div class="filter-item">                               
                                            <label class="control-label" for="fr_date" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=FROM_DATE?> :</label>
                                            <div class="controls" style="margin-right:0;">
                                                <input type="text" class="datetime datetimepicker span12" id="fr_date" name="fr_date" value="<?= htmlspecialchars($_POST['fr_date'] ?? '') ?>" placeholder="من تاريخ">              
                                            </div>
                                        </div>
                                        <div class="filter-item">                                                                 
                                            <label class="control-label" for="to_date" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=TO_DATE?> :</label>
                                            <div class="controls" style="margin-right:0;">
                                                <input type="text" class="datetime datetimepicker span12" id="to_date" name="to_date" value="<?= htmlspecialchars($_POST['to_date'] ?? '') ?>" placeholder="إلى تاريخ">
                                            </div>
                                        </div>
                                        <div class="filter-item" style="padding-top:22px;">
                                            <button type="submit" class="btn btn-primary" name="Submit"><i class="icon-search"></i> <?=LOAD_FORM ?? 'عرض كشف الحساب'?></button>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
                if (isset($_POST['Submit'])) {
                    $conditionRep = '';
                    $account = trim($_POST['account'] ?? '');  
                    if (!empty($account) && $account !== '0') {
                        getAccounts($account);      
                        if (!empty($acc)) {
                            $escapedAccs = array_map(function($a) use ($adController) {
                                return "'" . mysqli_real_escape_string($adController->MySQL, $a) . "'";
                            }, $acc);
                            $conditionRep .= " AND ji.accCode IN (" . implode(",", $escapedAccs) . ") ";
                        }
                    }

                    $branch = trim($_POST['branch'] ?? '');  
                    if (!empty($branch)) {
                        $cleanBranch = intval($branch);
                        $conditionRep .= " AND ji.brarnch = '$cleanBranch' ";
                    }

                    $fdate = trim($_POST['fr_date'] ?? '');  
                    if (!empty($fdate)) {
                        $cleanFdate = mysqli_real_escape_string($adController->MySQL, $fdate);
                        $conditionRep .= " AND ji.date >= '$cleanFdate' ";                 
                    }

                    $tdate = trim($_POST['to_date'] ?? '');  
                    if (!empty($tdate)) {
                        $cleanTdate = mysqli_real_escape_string($adController->MySQL, $tdate);
                        $conditionRep .= " AND ji.date <= '$cleanTdate' ";                     
                    }
                ?>
                <div>
                    <div class="box span12">   
                        <div class="box-content">
                            <div class="totals-card">
                                <h4 style="margin-top:0; margin-bottom:12px; color:#334155; text-align:center;"><?=TOTALS?> والملخص المالي</h4>
                                <table class="table table-bordered" style="width:70%; margin:0 auto; text-align:center;">
                                    <thead>
                                        <tr style="background:#f8fafc;">      
                                            <th class="credit"><?=TOTAL?> <?=CREDIT?></th>      
                                            <th class='debit'><?=TOTAL?> <?=DEBIT?></th>
                                            <th class="credit"><?=BALANCE?> <?=CREDIT?></th>
                                            <th class='debit'><?=BALANCE?> <?=DEBIT?></th>
                                        </tr>
                                    </thead>   
                                    <tbody>
                                        <tr>
                                            <td class='credit movement' id="credit" style="font-size:16px;"></td> 
                                            <td class='debit movement' id="debit" style="font-size:16px;"></td>
                                            <td class='credit balance' id="balanceCredit" style="font-size:16px;"></td>                             
                                            <td class='debit balance' id="balanceDebit" style="font-size:16px;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th><?=DATE?></th>    
                                        <th><?=JOURNAL_NUMBER?></th>   
                                        <th><?=ACCOUNT_NAME?></th>    
                                        <th><?=BRANCH?></th>                                             
                                        <th><?=DISCRIPTION?></th>                                        
                                        <th class='debit'><?=DEBIT?></th>
                                        <th class="credit"><?=CREDIT?></th>                                        
                                        <th class='debit'><?=BALANCE?> <?=DEBIT?></th>        
                                        <th class="credit"><?=BALANCE?> <?=CREDIT?></th>
                                    </tr>
                                </thead>   
                                <tbody>
                                <?php                                    
                                $oldBalanceDebit  = 0;
                                $oldBalanceCredit = 0; 
                                $totalDebit       = 0;
                                $totalCredit      = 0;
                                $i = 0;

                                // استعلام موحد مع LEFT JOIN لمنع آلاف الاستعلامات المتكررة
                                $query = "SELECT ji.*, 
                                                 a.name_ar AS acc_name_ar, a.name_en AS acc_name_en,
                                                 b.name_ar AS branch_name_ar, b.name_en AS branch_name_en
                                          FROM journal_items ji
                                          LEFT JOIN accounts a ON a.code = ji.accCode
                                          LEFT JOIN branches b ON b.id = ji.brarnch
                                          WHERE ji.state = '1' $conditionRep 
                                          ORDER BY ji.date ASC, ji.id ASC";

                                $res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
                                while ($data = mysqli_fetch_assoc($res)) {
                                    $accName = ($language == 'ar' ? $data['acc_name_ar'] : $data['acc_name_en']) ?? '';
                                    if (empty($accName)) {
                                        $accName = !empty($data['acc_name_ar']) ? $data['acc_name_ar'] : ($data['acc_name_en'] ?? $data['accCode']);
                                    }

                                    $branchName = ($language == 'ar' ? $data['branch_name_ar'] : $data['branch_name_en']) ?? '';
                                    if (empty($branchName)) {
                                        $branchName = !empty($data['branch_name_ar']) ? $data['branch_name_ar'] : ($data['branch_name_en'] ?? '-----');
                                    }

                                    $journalNo = $data["journalNo"] ?? '';										
                                    $disc      = $data["disc"] ?? '';
                                    $credit    = floatval($data["credit"] ?? 0);
                                    $debit     = floatval($data["debit"] ?? 0);                                                                                
                                    $date      = $data['date'] ?? '';
                                    
                                    $newBalanceDebit  = $oldBalanceDebit - $oldBalanceCredit + $debit - $credit;
                                    $newBalanceCredit = $oldBalanceCredit - $oldBalanceDebit + $credit - $debit; 
                                    if ($newBalanceDebit <= 0)  $newBalanceDebit = 0;
                                    if ($newBalanceCredit <= 0) $newBalanceCredit = 0;

                                    echo "<tr>";
                                    echo "<td>" . ++$i . "</td>";    
                                    echo "<td>" . htmlspecialchars($date) . "</td>";        
                                    echo "<td><a href='edit_journal.php?no=" . urlencode($journalNo) . "'>" . htmlspecialchars($journalNo) . "</a></td>";             
                                    echo "<td>" . htmlspecialchars($accName) . "</td>";       
                                    echo "<td>" . htmlspecialchars($branchName) . "</td>";                               
                                    echo "<td>" . htmlspecialchars($disc) . "</td>";                                 
                                    echo "<td class='debit movement' style='text-align:center;'>" . flout_format($debit) . "</td>";
                                    echo "<td class='credit movement' style='text-align:center;'>" . flout_format($credit) . "</td>";  
                                    echo "<td class='debit balance' style='text-align:center;'>" . flout_format($newBalanceDebit) . "</td>";    
                                    echo "<td class='credit balance' style='text-align:center;'>" . flout_format($newBalanceCredit) . "</td>";                                
                                    echo "</tr>";

                                    $oldBalanceDebit  = $newBalanceDebit; 
                                    $oldBalanceCredit = $newBalanceCredit;   
                                    $totalDebit  += $debit;
                                    $totalCredit += $credit;
                                }

                                $BalanceDebit  = $totalDebit - $totalCredit;
                                $BalanceCredit = $totalCredit - $totalDebit; 
                                if ($BalanceDebit <= 0)  $BalanceDebit = 0;
                                if ($BalanceCredit <= 0) $BalanceCredit = 0;
                                ?>
                                </tbody>
                            </table>                                
                        </div>
                    </div>
                </div>
                <?php } ?>

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
                '.span12': { width: '100%' },
                '.span3':  { width: '90%' } 
            };
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }            
            
            $("#credit").html("<?=flout_format($totalCredit)?>");            
            $("#debit").html("<?=flout_format($totalDebit)?>");     
            $("#balanceCredit").html("<?=flout_format($BalanceCredit)?>");                
            $("#balanceDebit").html("<?=flout_format($BalanceDebit)?>");            
        });                              
    </script>
</body>
</html>