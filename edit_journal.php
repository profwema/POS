<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);

$rawNo = $_REQUEST['no'] ?? '';
$decrypted = $adController->encrypt_decrypt(2, urldecode($rawNo), 0);
$no = (!empty($decrypted) && $decrypted !== false) ? $decrypted : $rawNo;
$safeNo = mysqli_real_escape_string($adController->MySQL, $no);

$query   = "SELECT * FROM journal WHERE journalNo='$safeNo' LIMIT 1";
$resP    = mysqli_query($adController->MySQL, $query);
$dataPur = mysqli_fetch_assoc($resP);

$itemsListArray = [];
$query = "SELECT * FROM journal_items WHERE journalNo='$safeNo' ORDER BY id ASC";
$ress  = mysqli_query($adController->MySQL, $query);
if ($ress) {
    while ($dataItemsList = mysqli_fetch_assoc($ress)) {
        $itemsListArray[] = $dataItemsList;
    }
}
$numAlready = count($itemsListArray);

// جلب وتخزين الحسابات الفرعية مع الحساب الأب باستعلام واحد فقط
$accQuery = "SELECT a.id, a.code, a.name_ar, a.name_en, 
                    p.name_ar AS parent_ar, p.name_en AS parent_en
             FROM accounts a
             LEFT JOIN accounts p ON p.id = a.parent
             WHERE a.hasChild = '0'
             ORDER BY a.parent ASC, a.code ASC";
$resAcc = mysqli_query($adController->MySQL, $accQuery);

$cachedAccounts = [];
if ($resAcc) {
    while ($acc = mysqli_fetch_assoc($resAcc)) {
        $aName = ($language == 'en' && !empty($acc['name_en'])) ? $acc['name_en'] : (!empty($acc['name_ar']) ? $acc['name_ar'] : $acc['name_en']);
        $pName = ($language == 'en' && !empty($acc['parent_en'])) ? $acc['parent_en'] : (!empty($acc['parent_ar']) ? $acc['parent_ar'] : $acc['parent_en']);
        $code  = $acc['code'];
        $label = ($pName ? $pName . " -> " : "") . $aName;
        $cachedAccounts[] = [
            'code'  => $code,
            'label' => $label
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - <?=JOURNAL?> - <?=EDIT?></title>
    <?php require_once("script_php_variables.php"); ?>
    <?php require_once("header.php"); ?>	
    <style>
        .mult-padding {
            padding: 4px !important;
            text-align: center;
        }
        .del-jurnal-item, .more-jurnal-item {
            cursor: pointer;
            text-align: center;
            width: 32px;
        }
        .del-jurnal-item label, .more-jurnal-item label {
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            margin: 0;
            display: block;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .del-jurnal-item label {
            color: #fff;
            background-color: #d9534f;
        }
        .more-jurnal-item label {
            color: #fff;
            background-color: #5cb85c;
        }
        .error-red {
            color: #d9534f;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
    <script type="text/javascript">
        var numAlready = <?= max(0, $numAlready - 1) ?>;
    </script>  
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
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=JOURNAL?> - <?=EDIT?></h2>
                        </div>
                        <div class="box-content" id="printed">
                            <form class="form-horizontal" id="journal-form">
                                <input type="hidden" value="editJournal" name="f">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <fieldset>
                                    <div class="control-group" style="display:flex; flex-wrap:wrap; gap:20px;">
                                        <div class="salesOptions">
                                            <label class="control-label" for="invoice" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=JOURNAL_NUMBER?>:</label>
                                            <div class="controls" style="margin-right:0;">
                                                <input type="text" class="input-medium" name='journalNo' id='invoice' value='<?=htmlspecialchars($dataPur['journalNo'] ?? $no)?>' readonly style="background:#eee;">
                                            </div>
                                        </div>  

                                        <div class="salesOptions">
                                            <label class="control-label" for="document" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=DOCUMENT?> : </label>
                                            <div class="controls" style="margin-right:0;">
                                                <input type="text" class="input-medium" name='document' id='document' value="<?=htmlspecialchars($dataPur['docNo'] ?? '')?>">
                                            </div>
                                        </div>

                                        <div class="salesOptions">
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=BRANCH?> :</label>
                                            <div class="controls" style="margin-right:0;">
                                                <select name="branch" data-rel="chosen" class="input-medium">
                                                    <option value=''>-- اختر الفرع --</option>
                                                    <?php
                                                    $query = "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY id ASC";
                                                    $res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
                                                    $currBranch = $dataPur['brarnch'] ?? '';
                                                    while ($data = mysqli_fetch_assoc($res)) {
                                                        $bra_id = $data['id'];
                                                        $name   = $data['name_' . $language] ?? $data['name_ar'];
                                                        $sel    = ($bra_id == $currBranch) ? " selected='selected' " : "";
                                                        echo "<option value='" . htmlspecialchars($bra_id) . "' $sel>" . htmlspecialchars($name) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="salesOptions">
                                            <label class="control-label" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=DATE?> :</label>
                                            <div class="controls" style="margin-right:0;">
                                                <?php
                                                $jDate = !empty($dataPur['date']) ? date("d/m/Y", strtotime($dataPur['date'])) : date("d/m/Y");
                                                ?>
                                                <input type="text" class="input-medium datepicker-nobar" name="date" value='<?=htmlspecialchars($jDate)?>'>
                                            </div>                                                           
                                        </div>                                                             
                                    </div>

                                    <div class="control-group" style="margin-top:15px;">                                                                                                                       
                                        <label class="control-label" for="disc" style="width:auto; float:none; margin-bottom:5px; font-weight:bold;"><?=DISCRIPTION?> : </label>
                                        <div class="controls" style="margin-right:0;">
                                            <textarea class="span10" name="disc" id="disc" rows="2" placeholder="بيان / شرح القيد المحاسبي العام"><?=htmlspecialchars($dataPur['disc'] ?? '')?></textarea>                                            
                                        </div>                                  
                                    </div>                                                              

                                    <div class="control-group" style="margin-top:20px;">
                                        <h4 style="margin-bottom:15px;"><?=JOURNAL_ITEMS ?? 'بنود القيد المحاسبي'?></h4>
                                        <div class="controls" style="margin-right:0;">	
                                            <table class="table table-bordered table-striped" style="width:100%;">                                                
                                                <thead>
                                                    <tr style="background:#f5f5f5;">
                                                        <th style="width:35%;"><?=ACCOUNT_NAME?></th>    
                                                        <th style="width:12%;"><?=DEBIT?></th>
                                                        <th style="width:12%;"><?=CREDIT?></th>
                                                        <th style="width:25%;"><?=DISCRIPTION?></th>
                                                        <th style="width:10%;"><?=REFERENE ?? 'المرجع'?></th>
                                                        <th style="width:3%;"></th>
                                                        <th style="width:3%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                for ($i = 0; $i < 20; $i++) {
                                                    $hasItem    = isset($itemsListArray[$i]);
                                                    $rowStyle   = ($i >= 2 && !$hasItem) ? "style='display:none;'" : "";
                                                    $itemCode   = $hasItem ? ($itemsListArray[$i]['accCode'] ?? '') : '';
                                                    $itemDebit  = $hasItem ? ($itemsListArray[$i]['debit'] ?? '0.00') : '0.00';
                                                    $itemCredit = $hasItem ? ($itemsListArray[$i]['credit'] ?? '0.00') : '0.00';
                                                    $itemDisc   = $hasItem ? ($itemsListArray[$i]['disc'] ?? '') : '';
                                                    $itemRef    = $hasItem ? ($itemsListArray[$i]['reference'] ?? '') : '';
                                                ?>
                                                    <tr <?= $rowStyle ?> id='row-v-<?= $i ?>'>
                                                        <td>
                                                            <select name="account[]" id='account-<?= $i ?>' data-rel="chosen" class="span12" style="width:100%">
                                                                <option value="0">-- اختر الحساب --</option>
                                                                <?php
                                                                foreach ($cachedAccounts as $acc) {
                                                                    $sel = ($acc['code'] == $itemCode) ? " selected='selected' " : "";
                                                                    echo "<option value='" . htmlspecialchars($acc['code']) . "' $sel>" . htmlspecialchars($acc['label']) . "</option>";
                                                                }
                                                                ?>
                                                            </select> 
                                                        </td>
                                                        <td class="mult-padding">
                                                            <input type="text" id='debit-<?= $i ?>' class="debit span12" maxlength="100" name='debit[]' value="<?= htmlspecialchars($itemDebit) ?>" style="text-align:center;">
                                                        </td>
                                                        <td class="mult-padding">
                                                            <input type="text" id='credit-<?= $i ?>' class="credit span12" maxlength="100" name='credit[]' value="<?= htmlspecialchars($itemCredit) ?>" style="text-align:center;">
                                                        </td>
                                                        <td class="mult-padding">
                                                            <input type="text" id='disc-<?= $i ?>' class="span12" maxlength="100" name='discItem[]' value="<?= htmlspecialchars($itemDisc) ?>" placeholder="شرح البند">
                                                        </td>
                                                        <td class="mult-padding">
                                                            <input type="text" id='ref-<?= $i ?>' class="span12" maxlength="100" name='ref[]' value="<?= htmlspecialchars($itemRef) ?>" placeholder="مرجع">
                                                        </td>
                                                        <td class='del-jurnal-item mult-padding edit' prop='<?= $i ?>'><label title="<?=DELETE ?? 'حذف السطر'?>">×</label></td>
                                                        <td class='more-jurnal-item mult-padding edit' prop='<?= $i ?>'><label title="<?=ADD ?? 'إضافة سطر'?>">+</label></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>                                  				
                                   
                                    <div class="control-group" style="margin-top:20px;">
                                        <div style="float: left; width: 340px;">
                                            <table class="table table-bordered" style="background:#f8fafc;">                                                
                                                <thead>
                                                    <tr>
                                                        <th colspan="2" style="text-align:center; background:#e2e8f0;"><?=TOTAlS?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td style="width:45%; font-weight:bold;">إجمالي المدين :</td>
                                                        <td>
                                                            <input type="text" class="span12" name='total-debit' id="total-debit" value="0.00" readonly style="font-weight:bold; color:#15803d; text-align:center;">
                                                        </td>    
                                                    </tr>
                                                    <tr>
                                                        <td style="font-weight:bold;">إجمالي الدائن :</td>
                                                        <td>
                                                            <input type="text" class="span12" name="total-credit" id="total-credit" value="0.00" readonly style="font-weight:bold; color:#b91c1c; text-align:center;">
                                                        </td>    
                                                    </tr>
                                                    <tr style="font-size: 15px;">
                                                        <td style="font-weight:bold;">الفرق (التوازن) :</td>
                                                        <td>
                                                            <input type="text" class="span12" name="difference" id="difference" value="0.00" readonly style="font-weight:bold; text-align:center;">
                                                        </td>    
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="form-actions" style="margin-top:20px;">
                                        <button type="button" id='submit-journal' class="btn btn-primary"><i class="icon-save"></i> <?=SAVE?></button>
                                        <a href="journal.php" class="btn"><?=CANCEL?></a>
                                    </div>
                                    <p class='error-red'></p>
                                </fieldset>
                            </form>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
    <div class="clearfix"></div>
    <?php require_once("footer.php"); ?>
    
    <script type="text/javascript">
    function totals() {
        var total_debit = 0;
        var total_credit = 0;
        
        $(".debit").each(function() {
            var val = parseFloat($(this).val()) || 0;
            total_debit += val;
        });
        
        $(".credit").each(function() {
            var val = parseFloat($(this).val()) || 0;
            total_credit += val;
        });
        
        $("#total-debit").val(total_debit.toFixed(2));
        $("#total-credit").val(total_credit.toFixed(2));
        
        var difference = total_debit - total_credit;
        $("#difference").val(difference.toFixed(2));
        
        if (Math.abs(difference) < 0.001) {
            $("#difference").css({'color': '#15803d', 'background': '#f0fdf4'});
        } else {
            $("#difference").css({'color': '#b91c1c', 'background': '#fef2f2'});
        }
    }
             
    $(document).ready(function() {
        var config = {
            '.span12': { width: '100%' },
            '.span3':  { width: '95%' }
        };
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }

        $(".debit").on("change blur keyup", function() {
            var id = $(this).attr('id').replace('debit-', '');
            var val = parseFloat($(this).val()) || 0;
            if (val > 0) {
                $("#credit-" + id).val('0.00');
            }
            totals();
        });

        $(".credit").on("change blur keyup", function() {
            var id = $(this).attr('id').replace('credit-', '');
            var val = parseFloat($(this).val()) || 0;
            if (val > 0) {
                $("#debit-" + id).val('0.00');
            }
            totals();
        });

        $(".del-jurnal-item").on("click", function() {
            setTimeout(totals, 150);
        });
        
        totals();
    });
    </script>
</body>
</html>
