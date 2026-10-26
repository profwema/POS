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
    <title>WAM Tech Soft - <?=ACCOUNTS?></title>
    <?php require_once("header.php"); ?>	
    <style>
        .accountsTree {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 6px;
            padding: 15px;
            margin-top: 10px;
            width: 48%;
            float: right;
            height: 560px;
            overflow-y: auto;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .accountEdit {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 6px;
            padding: 15px;
            margin-top: 10px;
            width: 48%;
            float: left;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .form-buttons {
            padding: 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .form-buttons button {
            min-width: 90px;
            font-weight: bold;
            padding: 6px 14px;
            border-radius: 4px;
        }
        .pair {
            padding-right: 25px;
            margin-right: 0px;
        }
        #pair-0 {
            padding-right: 0px;
            margin-right: 0px;
        }
        .account {
            margin: 4px 0;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .account:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
        }
        .account a.acc {
            color: #1e293b;
            text-decoration: none;
            font-weight: 500;
            flex-grow: 1;
        }
        .account a.acc:hover {
            color: #0284c7;
        }
        .account i.showChild, .account i.hideChild {
            cursor: pointer;
            color: #0284c7;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <?php require_once("header_top.php"); ?>
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php"); ?>
            <div id="content" class="span10">
                <div class="row-fluid sortable">
                    <div class="box span12">
                        <div class="box-header" data-original-title>
                            <h2><i class="halflings-icon edit"></i><span class="break"></span><?=ACCOUNTS?> - دليل وشجرة الحسابات</h2>
                        </div>
                        <div class="box-content clearfix" style="min-height:600px;">
                            <!-- شجرة الحسابات جهة اليمين -->
                            <div class="accountsTree">
                                <h4 style="margin-top:0; margin-bottom:12px; border-bottom:1px solid #eee; padding-bottom:8px; color:#334155;">
                                    <i class="icon-sitemap"></i> شجرة الحسابات
                                </h4>
                                <div id="pair-0" class='pair'></div>
                            </div>            

                            <!-- لوحة تفاصيل وتعديل الحساب جهة اليسار -->
                            <div class="accountEdit">
                                <div class="form-buttons">
                                    <button type="button" id='new' class="btn btn-primary"><i class="icon-plus"></i> <?=ADD?></button>
                                    <button type="button" id='edit' class="btn btn-info" disabled><i class="icon-edit"></i> <?=EDIT?></button>
                                    <button type="button" id='del' class="btn btn-danger" disabled><i class="icon-trash"></i> <?=DELETE?></button>
                                </div>                                    

                                <form class="form-horizontal" id="form_Account">
                                    <input type="hidden" name="f" id='f'>
                                    <input type="hidden" name="aid" id='aid'>
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                    
                                    <div class="control-group">
                                        <label class="control-label" for="pairent" style="width:130px; text-align:right; font-weight:bold;"><?=PAIRENT_ACCOUNT?> :</label>
                                        <div class="controls" style="margin-right:150px;">
                                            <select name="pairent" id='pairent' class="span12" disabled>
                                                <option value="0">حساب رئيسي (بدون أب)</option>
                                            </select> 
                                        </div>
                                    </div>  

                                    <div class="control-group">
                                        <label class="control-label" for="type" style="width:130px; text-align:right; font-weight:bold;"><?=ACCOUNT_TYPE?> :</label>
                                        <div class="controls" style="margin-right:150px;">
                                            <select id='type' class='span12' disabled>
                                                <option value="0">-- اختر نوع الحساب --</option>
                                                <?php
                                                $query = "SELECT * FROM account_type ORDER BY id ASC";
                                                $res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
                                                while ($data = mysqli_fetch_assoc($res)) {
                                                    $id   = $data['id'];
                                                    $name = $data['name_' . $language] ?? $data['name_ar'];
                                                    echo "<option value='" . htmlspecialchars($id) . "'>" . htmlspecialchars($name) . "</option>";
                                                }
                                                ?>
                                            </select> 
                                            <input type="hidden" name="type" id='ty'>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="flbs" style="width:130px; text-align:right; font-weight:bold;"><?=PLBS?> :</label>
                                        <div class="controls" style="margin-right:150px;">
                                            <select id='flbs' class='span12' disabled>
                                                <option value="0">-- اختر القائمة المالية (<?=PLBS?>) --</option>
                                                <?php
                                                $query = "SELECT * FROM budget_set ORDER BY id ASC";
                                                $res   = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
                                                while ($data = mysqli_fetch_assoc($res)) {
                                                    $id   = $data['id'];
                                                    $name = $data['name_' . $language] ?? $data['name_ar'];
                                                    echo "<option value='" . htmlspecialchars($id) . "'>" . htmlspecialchars($name) . "</option>";
                                                }
                                                ?>
                                            </select> 
                                            <input type="hidden" name="flbs" id='fl'>
                                        </div>
                                    </div>                                        

                                    <div class="control-group">
                                        <label class="control-label" for="name_ar" style="width:130px; text-align:right; font-weight:bold;"><?=ACCOUNT_NAME?> : </label>
                                        <div class="controls" style="margin-right:150px;">
                                            <input type="text" class="span12" name='name_ar' id='name_ar' placeholder="اسم الحساب" disabled>
                                            <input type="hidden" name='name_en' id='name_en'>
                                        </div>
                                    </div> 

                                    <div class="control-group">
                                        <label class="control-label" for="code" style="width:130px; text-align:right; font-weight:bold;"><?=ACCOUNT_CODE?> : </label>
                                        <div class="controls" style="margin-right:150px;">
                                            <input type="text" class="span12" name='code' id='code' placeholder="رمز الحساب المالي" disabled>
                                        </div>
                                    </div>                                             

                                    <div class="control-group">
                                        <label class="control-label" style="width:130px; text-align:right; font-weight:bold;"><?=CHILD?> :</label>
                                        <div class="controls" style="margin-right:150px;">
                                            <label class="checkbox inline">
                                                <input type="checkbox" value="1" name="child" id="child" disabled>
                                                حساب رئيسي (يحتوي على حسابات فرعية)
                                            </label>
                                        </div>
                                    </div>                                             

                                    <div class="form-buttons" style="margin-top:25px;">
                                        <button type="button" id='submit-acc' class="btn btn-success" disabled><i class="icon-save"></i> <?=SAVE?></button>
                                        <button type="button" id='reset' class="btn" disabled><?=CANCEL?></button>
                                    </div>  
                                </form>                                                     
                            </div>                                       
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
    <div class="clearfix"></div>
    <?php require_once("footer.php"); ?>
    
    <script type="text/javascript">
    $(document).ready(function() {
        var config = {
            '.span12': { width: '100%' },
            '.span3':  { width: '90%' }
        };
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }
        
        fill_treeview(0);     
        fill_parent_account();  

        function fill_parent_account() {
            var u = "controller.php?f=fill_parent_account";   
            $.ajax({    
                url: u,
                success: function(data) {   
                    $('#pairent').html(data);
                    $("#pairent").chosen("destroy").chosen();
                }
            });   
        }      

        function fill_treeview(pair) {      
            var u = "controller.php?f=fitch&p=" + pair; 
            $.ajax({
                url: u,
                success: function(data) {
                    $('#pair-' + pair).html(data);
                }
            });
        }        

        function enableForm() {       
            var p = $("#pairent").val();   
            $("#pairent").removeAttr("disabled");  
            $("#pairent").val(p).trigger("chosen:updated");
            if (p == '0' || p == '') {
                var t = $("#type").val();           
                $("#type").removeAttr("disabled");
                $("#type").val(t).trigger("chosen:updated");
                var f = $("#flbs").val();   
                $("#flbs").removeAttr("disabled");    
                $("#flbs").val(f).trigger("chosen:updated");            
            }   
            $("#name_en").removeAttr("disabled");
            $("#name_ar").removeAttr("disabled"); 
            $("#code").removeAttr("disabled");
            $("#child").removeAttr("disabled");
            $("#submit-acc").removeAttr("disabled");
            $("#reset").removeAttr("disabled");
        }

        function disableForm() {       
            $("#pairent").attr('disabled', 'disabled');              
            $("#type").attr('disabled', 'disabled');              
            $("#flbs").attr('disabled', 'disabled'); 
            $("#pairent").val('0').trigger("chosen:updated");
            $("#type").val('').trigger("chosen:updated");
            $("#flbs").val('').trigger("chosen:updated");            
            $("#name_en").attr('disabled', 'disabled');
            $("#name_ar").attr('disabled', 'disabled');
            $("#code").attr('disabled', 'disabled');
            $("#child").attr('disabled', 'disabled');
            $("#submit-acc").attr('disabled', 'disabled');
            $("#reset").attr('disabled', 'disabled');
            $("#edit").attr('disabled', 'disabled');
            $("#del").attr('disabled', 'disabled');
            $("#new").removeAttr("disabled");
        }        

        function clearform() {  
            $("#pairent").val('0').trigger("chosen:updated");
            $("#type").val('').trigger("chosen:updated");
            $("#flbs").val('').trigger("chosen:updated"); 
            $("#name_en").val('');
            $("#name_ar").val(''); 
            $("#code").val(''); 
            $("#child").prop('checked', false);
            $("#aid").val('');
        }        

        $("#new").click(function() {
            clearform();
            enableForm();  
            $("#edit").attr('disabled', 'disabled');
            $("#del").attr('disabled', 'disabled');
            $("#f").val('addAccount');
            $("#name_ar").focus();
        }); 

        $("#edit").click(function() {
            enableForm();  
            $("#new").attr('disabled', 'disabled');
            $("#del").attr('disabled', 'disabled');
            $("#f").val('editAccount');
            $("#name_ar").focus();
        });            
           
        $("#submit-acc").click(function() {
            $("#ty").val($("#type").val());
            $("#fl").val($("#flbs").val());
            $("#name_en").val($("#name_ar").val());
            
            $("#overlay").show(300);
            $.ajax({
                url: "controller.php",
                method: "POST",
                data: $("#form_Account").serialize(),
                success: function(data) {                      
                    $("#overlay").hide(300);
                    alert(data);       
                    fill_treeview(0);
                    fill_parent_account();
                    clearform();
                    disableForm();
                },
                error: function() {
                    $("#overlay").hide(300);
                    alert("حدث خطأ أثناء حفظ الحساب");
                }
            });
        });
          
        $("#del").click(function() {
            var aid = $("#aid").val();
            if (!aid) {
                alert("يرجى اختيار حساب لحذفه أولاً");
                return;
            }
            if (confirm("هل أنت متأكد من رغبتك في حذف هذا الحساب؟")) {
                var u = "controller.php?f=delAccount&aid=" + aid; 
                $("#overlay").show(300);
                $.ajax({
                    url: u,
                    success: function(data) {
                        $("#overlay").hide(300);
                        alert(data);
                        fill_treeview(0);
                        fill_parent_account();
                        clearform();
                        disableForm();
                    },
                    error: function() {
                        $("#overlay").hide(300);
                        alert("حدث خطأ أثناء حذف الحساب");
                    }
                });
            }
        });          
          
        $("#reset").click(function() {
            fill_treeview(0);
            fill_parent_account();
            clearform();
            disableForm(); 
        });

        $("#pairent").chosen().change(function() {              
            var index = $(this).val();
            if (index > 0) {
                var type = $('option:selected', this).attr('type');
                var budget = $('option:selected', this).attr('budget');    
                $("#type").attr('disabled', 'disabled');              
                $("#type").val(type).trigger("chosen:updated");
                $("#flbs").attr('disabled', 'disabled'); 
                $("#flbs").val(budget).trigger("chosen:updated");
            } else {
                $("#type").removeAttr("disabled");
                $("#flbs").removeAttr("disabled");                  
                $("#type").val('').trigger("chosen:updated");
                $("#flbs").val('').trigger("chosen:updated");                           
            }
        });    
        
        $(document).on("click", "a.acc", function() {
            if ($('#submit-acc').is(':disabled')) {
                $("#edit").removeAttr("disabled");
                $("#del").removeAttr("disabled");
                $("#pairent").val($(this).attr('p')).trigger("chosen:updated");
                $("#type").val($(this).attr('t')).trigger("chosen:updated");
                $("#flbs").val($(this).attr('b')).trigger("chosen:updated");
                $("#aid").val($(this).attr('aid'));                
                $("#name_en").val($(this).attr('e'));
                $("#name_ar").val($(this).attr('a'));
                $("#code").val($(this).attr('d'));   
                if ($(this).attr('c') == '1') {
                    $("#child").prop('checked', true);
                } else {
                    $("#child").prop('checked', false);
                }
            }
            return false;
        });
                
        $(document).on("click", "i.showChild", function() {
            $(this).toggleClass('icon-plus-sign icon-minus-sign');
            $(this).toggleClass('showChild hideChild');
            fill_treeview($(this).attr('p'));     
            return false;
        });   

        $(document).on("click", "i.hideChild", function() {
            $(this).toggleClass('icon-minus-sign icon-plus-sign');
            $(this).toggleClass('hideChild showChild');
            $('#pair-' + $(this).attr('p')).html('');
            return false;
        }); 
    });
    </script>
</body>
</html>
