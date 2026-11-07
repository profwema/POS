<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);
$pgName   = basename(__FILE__);

// Fetch KPIs for accounts
$queryKpi = "SELECT 
                COUNT(*) as total_accounts,
                SUM(CASE WHEN hasChild = '1' THEN 1 ELSE 0 END) as parent_accounts,
                SUM(CASE WHEN hasChild = '0' THEN 1 ELSE 0 END) as leaf_accounts
             FROM accounts";
$resKpi = mysqli_query($adController->MySQL, $queryKpi);
$dataKpi = $resKpi ? mysqli_fetch_assoc($resKpi) : [];

$kpiTotalAccounts  = (int)($dataKpi['total_accounts'] ?? 0);
$kpiParentAccounts = (int)($dataKpi['parent_accounts'] ?? 0);
$kpiLeafAccounts   = (int)($dataKpi['leaf_accounts'] ?? 0);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شجرة ودليل الحسابات العامة | WAM Tech ERP</title>
    <link rel="stylesheet" href="css/admin-pro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .kpi-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px 20px;
            border: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .accounts-tree-card {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 20px;
            min-height: 580px;
            max-height: 720px;
            overflow-y: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .account-form-card {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .account {
            margin: 4px 0;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.15s ease-in-out;
        }
        .account:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateX(-2px);
        }
        .account.active-selected {
            background-color: #e0f2fe;
            border-color: #0284c7;
            color: #0369a1;
            font-weight: bold;
        }
        .account a.acc {
            color: #1e293b;
            text-decoration: none;
            font-weight: 500;
            flex-grow: 1;
            font-size: 0.92rem;
        }
        .account a.acc:hover {
            color: #0284c7;
        }
        .pair {
            padding-right: 22px;
            margin-right: 4px;
            border-right: 1px dashed #cbd5e1;
        }
        #pair-0 {
            padding-right: 0px;
            border-right: none;
        }
        .showChild, .hideChild {
            cursor: pointer;
            color: #0284c7;
            font-size: 15px;
            width: 18px;
            text-align: center;
        }
        .icon-plus-sign:before { content: "\f0fe"; font-family: "Font Awesome 6 Free"; font-weight: 900; }
        .icon-minus-sign:before { content: "\f146"; font-family: "Font Awesome 6 Free"; font-weight: 900; }
        .icon-minus:before { content: "\f111"; font-family: "Font Awesome 6 Free"; font-weight: 400; font-size: 6px; color: #94a3b8; }
    </style>
</head>
<body class="admin-pro-body">
    <?php require_once("components/header_pro.php"); ?>
    
    <div class="admin-wrapper">
        <?php require_once("components/sidebar_pro.php"); ?>
        
        <main class="admin-main">
            <div class="container-fluid px-4 py-4">
                
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">
                            <i class="fas fa-sitemap text-primary me-2"></i> دليل وشجرة الحسابات المالية
                        </h2>
                        <p class="text-muted mb-0">الهيكل المالي الموحد للحسابات، الأصول، الخصوم، الإيرادات والمصروفات</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="journal.php" class="btn btn-outline-primary">
                            <i class="fas fa-book me-1"></i> قيود اليومية العامة
                        </a>
                        <a href="budget.php" class="btn btn-outline-secondary">
                            <i class="fas fa-chart-pie me-1"></i> ميزان المراجعة
                        </a>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">إجمالي الحسابات بالدليل</div>
                                <div class="fs-4 fw-bold text-dark font-monospace"><?= number_format($kpiTotalAccounts) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                                <i class="fas fa-calculator"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">حسابات رئيسية (مجموعات)</div>
                                <div class="fs-4 fw-bold text-primary font-monospace"><?= number_format($kpiParentAccounts) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#e0e7ff; color:#4338ca;">
                                <i class="fas fa-folder"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6">
                        <div class="kpi-card">
                            <div>
                                <div class="text-muted small fw-bold">حسابات فرعية (أستاذ مساعد)</div>
                                <div class="fs-4 fw-bold text-success font-monospace"><?= number_format($kpiLeafAccounts) ?></div>
                            </div>
                            <div class="kpi-icon" style="background:#dcfce7; color:#16a34a;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Split View -->
                <div class="row g-4">
                    
                    <!-- Left / Right: Tree View (Right on RTL) -->
                    <div class="col-lg-5">
                        <div class="accounts-tree-card">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <h5 class="fw-bold text-dark mb-0">
                                    <i class="fas fa-folder-tree text-primary me-2"></i> شجرة الحسابات
                                </h5>
                                <button type="button" id="btnRefreshTree" class="btn btn-sm btn-outline-secondary" title="تحديث الشجرة">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            
                            <!-- Search box for tree items -->
                            <div class="mb-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" id="treeSearchInput" class="form-control" placeholder="بحث باسم أو رمز الحساب...">
                                </div>
                            </div>

                            <div id="pair-0" class="pair">
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-spinner fa-spin me-2"></i> جاري تحميل شجرة الحسابات...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Left / Right: Account Form & Actions (Left on RTL) -->
                    <div class="col-lg-7">
                        <div class="account-form-card">
                            
                            <!-- Action Buttons Bar -->
                            <div class="d-flex flex-wrap gap-2 mb-4 p-2 bg-light rounded-3 border">
                                <button type="button" id="new" class="btn btn-primary px-3">
                                    <i class="fas fa-plus me-1"></i> حساب جديد
                                </button>
                                <button type="button" id="edit" class="btn btn-info px-3 text-white" disabled>
                                    <i class="fas fa-edit me-1"></i> تعديل
                                </button>
                                <button type="button" id="del" class="btn btn-danger px-3" disabled>
                                    <i class="fas fa-trash me-1"></i> حذف
                                </button>
                                <a href="javascript:void(0)" id="btnStatement" class="btn btn-outline-dark px-3 ms-auto d-none" target="_blank">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> كشف الحساب
                                </a>
                            </div>

                            <!-- Form -->
                            <form id="form_Account">
                                <input type="hidden" name="f" id="f" value="">
                                <input type="hidden" name="aid" id="aid" value="">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="type" id="ty">
                                <input type="hidden" name="flbs" id="fl">
                                <input type="hidden" name="name_en" id="name_en">

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-level-up-alt text-primary me-1"></i> الحساب الأب (الحساب الرئيسي)
                                    </label>
                                    <select name="pairent" id="pairent" class="form-select select2-field" disabled>
                                        <option value="0">حساب رئيسي (بدون أب)</option>
                                    </select>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-secondary">
                                            <i class="fas fa-tag text-info me-1"></i> نوع الحساب
                                        </label>
                                        <select id="type" class="form-select select2-field" disabled>
                                            <option value="0">-- اختر نوع الحساب --</option>
                                            <?php
                                            $queryT = "SELECT * FROM account_type ORDER BY id ASC";
                                            $resT   = mysqli_query($adController->MySQL, $queryT);
                                            if ($resT) {
                                                while ($dT = mysqli_fetch_assoc($resT)) {
                                                    $nameT = $dT['name_' . $language] ?? $dT['name_ar'];
                                                    echo "<option value='{$dT['id']}'>" . htmlspecialchars($nameT) . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-secondary">
                                            <i class="fas fa-chart-pie text-warning me-1"></i> القائمة المالية (الميزانية / الأرباح)
                                        </label>
                                        <select id="flbs" class="form-select select2-field" disabled>
                                            <option value="0">-- اختر القائمة المالية --</option>
                                            <?php
                                            $queryB = "SELECT * FROM budget_set ORDER BY id ASC";
                                            $resB   = mysqli_query($adController->MySQL, $queryB);
                                            if ($resB) {
                                                while ($dB = mysqli_fetch_assoc($resB)) {
                                                    $nameB = $dB['name_' . $language] ?? $dB['name_ar'];
                                                    echo "<option value='{$dB['id']}'>" . htmlspecialchars($nameB) . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold text-secondary">
                                            <i class="fas fa-file-signature text-primary me-1"></i> اسم الحساب <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" name="name_ar" id="name_ar" placeholder="مثال: الصندوق الرئيسي / البنك الأهلي / المبيعات" disabled required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-secondary">
                                            <i class="fas fa-barcode text-secondary me-1"></i> رمز الحساب (الكود)
                                        </label>
                                        <input type="text" class="form-control font-monospace" name="code" id="code" placeholder="مثال: 1101" disabled>
                                    </div>
                                </div>

                                <div class="mb-4 p-3 bg-light rounded-3 border">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" value="1" name="child" id="child" disabled>
                                        <label class="form-check-label fw-bold text-dark" for="child">
                                            حساب رئيسي يتفرع منه حسابات أخرى (Parent Account)
                                        </label>
                                        <div class="text-muted small">إذا تم تفعيل هذا الخيار، سيتم اعتبار الحساب وعاء تجميعي ولا يتم القيد المباشر عليه في سندات اليومية.</div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 pt-3 border-top">
                                    <button type="button" id="submit-acc" class="btn btn-success px-4 py-2" disabled>
                                        <i class="fas fa-save me-1"></i> حفظ البيانات
                                    </button>
                                    <button type="button" id="reset" class="btn btn-outline-secondary px-4 py-2" disabled>
                                        إلغاء
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        $('.select2-field').select2({
            dir: 'rtl',
            width: '100%'
        });

        fill_treeview(0);     
        fill_parent_account();  

        function fill_parent_account() {
            var u = "controller.php?f=fill_parent_account";   
            $.ajax({    
                url: u,
                success: function(data) {   
                    $('#pairent').html(data).trigger('change');
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

        $('#btnRefreshTree').on('click', function() {
            fill_treeview(0);
            fill_parent_account();
        });

        function enableForm() {       
            $("#pairent, #name_ar, #code, #child, #submit-acc, #reset").prop("disabled", false);
            var p = $("#pairent").val();
            if (p == '0' || p == '') {
                $("#type, #flbs").prop("disabled", false);
            }
        }

        function disableForm() {       
            $("#pairent, #type, #flbs, #name_ar, #name_en, #code, #child, #submit-acc, #reset, #edit, #del").prop("disabled", true);
            $("#new").prop("disabled", false);
            $("#btnStatement").addClass('d-none');
            $('.account').removeClass('active-selected');
        }        

        function clearform() {  
            $("#pairent").val('0').trigger("change");
            $("#type").val('0').trigger("change");
            $("#flbs").val('0').trigger("change"); 
            $("#name_en, #name_ar, #code, #aid").val(''); 
            $("#child").prop('checked', false);
        }        

        $("#new").click(function() {
            clearform();
            enableForm();  
            $("#edit, #del").prop("disabled", true);
            $("#f").val('addAccount');
            $("#name_ar").focus();
        }); 

        $("#edit").click(function() {
            enableForm();  
            $("#new, #del").prop("disabled", true);
            $("#f").val('editAccount');
            $("#name_ar").focus();
        });            
           
        $("#submit-acc").click(function() {
            $("#ty").val($("#type").val());
            $("#fl").val($("#flbs").val());
            $("#name_en").val($("#name_ar").val());
            
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري الحفظ...');

            $.ajax({
                url: "controller.php",
                method: "POST",
                data: $("#form_Account").serialize(),
                success: function(data) {                      
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ البيانات');
                    alert(data);       
                    fill_treeview(0);
                    fill_parent_account();
                    clearform();
                    disableForm();
                },
                error: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ البيانات');
                    alert("حدث خطأ أثناء حفظ الحساب.");
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
                $.ajax({
                    url: u,
                    success: function(data) {
                        alert(data);
                        fill_treeview(0);
                        fill_parent_account();
                        clearform();
                        disableForm();
                    },
                    error: function() {
                        alert("حدث خطأ أثناء حذف الحساب.");
                    }
                });
            }
        });          
          
        $("#reset").click(function() {
            clearform();
            disableForm(); 
        });

        $("#pairent").change(function() {              
            var index = $(this).val();
            if (index > 0) {
                var type = $('option:selected', this).attr('type');
                var budget = $('option:selected', this).attr('budget');    
                $("#type").val(type).trigger("change").prop("disabled", true);
                $("#flbs").val(budget).trigger("change").prop("disabled", true);
            } else {
                if (!$("#submit-acc").is(':disabled')) {
                    $("#type, #flbs").prop("disabled", false);
                    $("#type, #flbs").val('0').trigger("change");
                }
            }
        });    
        
        $(document).on("click", "a.acc", function() {
            $('.account').removeClass('active-selected');
            $(this).closest('.account').addClass('active-selected');

            $("#edit, #del").prop("disabled", false);
            $("#pairent").val($(this).attr('p')).trigger("change");
            $("#type").val($(this).attr('t')).trigger("change");
            $("#flbs").val($(this).attr('b')).trigger("change");
            $("#aid").val($(this).attr('aid'));                
            $("#name_en").val($(this).attr('e'));
            $("#name_ar").val($(this).attr('a'));
            $("#code").val($(this).attr('d'));   
            if ($(this).attr('c') == '1') {
                $("#child").prop('checked', true);
            } else {
                $("#child").prop('checked', false);
            }

            var accCode = $(this).attr('d');
            if (accCode) {
                $('#btnStatement').removeClass('d-none').attr('href', 'account-statement.php?acc=' + encodeURIComponent(accCode));
            } else {
                $('#btnStatement').addClass('d-none');
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

        // Search filter in tree
        $('#treeSearchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.account').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
    </script>
</body>
</html>
