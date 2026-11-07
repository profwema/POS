<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= defined('ADD_EXPENDITURE') ? ADD_EXPENDITURE : 'إضافة مصروف جديد' ?> - WAM Tech POS</title>

    <?php require_once("header.php"); ?>
    <link href="css/admin-pro.css" rel="stylesheet">
    <style>
        .form-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .form-header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #fff;
            padding: 1.5rem 2rem;
        }
        .form-body {
            padding: 2rem;
        }
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }
        .input-group-text {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
        }
        .cost-input {
            font-size: 1.25rem;
            font-weight: 700;
            color: #b91c1c;
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <?php require_once("components/sidebar_pro.php"); ?>

        <div class="admin-content">
            <?php require_once("components/header_pro.php"); ?>

            <div class="container-fluid px-4 py-4">
                <!-- Page Breadcrumb & Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-1">
                                <li class="breadcrumb-item"><a href="index.php">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="expenditure.php"><?= defined('EXPENDITURE') ? EXPENDITURE : 'المصروفات' ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?= defined('ADD_EXPENDITURE') ? ADD_EXPENDITURE : 'إضافة مصروف جديد' ?></li>
                            </ol>
                        </nav>
                        <h3 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-plus-circle text-primary me-2"></i>
                            <?= defined('ADD_EXPENDITURE') ? ADD_EXPENDITURE : 'إضافة سند مصروف جديد' ?>
                        </h3>
                    </div>
                    <div>
                        <a href="expenditure.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة لسجل المصروفات
                        </a>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8 col-xl-7">
                        <div class="form-card">
                            <div class="form-header">
                                <h5 class="mb-1 fw-bold text-white">
                                    <i class="fas fa-file-invoice-dollar me-2 text-warning"></i> بيانات سند الصرف
                                </h5>
                                <p class="mb-0 text-white-50 small">يرجى تسجيل بيانات المصروف والفرع المعني والمبلغ بدقة</p>
                            </div>

                            <div class="form-body">
                                <form id="addExpenditureForm" method="POST" action="controller.php">
                                    <input type="hidden" name="f" value="addExpenditure">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()); ?>">
                                    <input type="hidden" name="desc_en" id="desc_en" value="">

                                    <div class="row g-3">
                                        <!-- Branch Selection -->
                                        <div class="col-12 col-md-6">
                                            <label class="form-label" for="branch">
                                                <i class="fas fa-code-branch text-secondary me-1"></i> <?= defined('BRANCH') ? BRANCH : 'الفرع' ?> <span class="text-danger">*</span>
                                            </label>
                                            <select name="branch" id="branch" class="form-select select2" required>
                                                <option value="0">عام (كافة الفروع)</option>
                                                <?php
                                                $query = "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY id ASC";
                                                $res   = mysqli_query($adController->MySQL, $query);
                                                if ($res) {
                                                    while ($b = mysqli_fetch_assoc($res)) {
                                                        $bId   = $b['id'];
                                                        $bName = ($language == 'ar' ? $b['name_ar'] : $b['name_en']) ?: $b['name_ar'];
                                                        echo "<option value='" . htmlspecialchars($bId) . "'>" . htmlspecialchars($bName) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Expenditure Date -->
                                        <div class="col-12 col-md-6">
                                            <label class="form-label" for="dated">
                                                <i class="fas fa-calendar-alt text-secondary me-1"></i> <?= defined('EXPENDITURE_DATE') ? EXPENDITURE_DATE : 'تاريخ المصروف' ?> <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="dated" id="dated" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                        </div>

                                        <!-- Amount / Cost -->
                                        <div class="col-12">
                                            <label class="form-label" for="cost">
                                                <i class="fas fa-money-bill-wave text-secondary me-1"></i> <?= defined('COST') ? COST : 'المبلغ المصروف' ?> <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0.01" name="cost" id="cost" class="form-control cost-input" placeholder="0.00" required autofocus>
                                                <span class="input-group-text fw-bold text-dark"><?= $currency ?></span>
                                            </div>
                                            <div class="form-text">أدخل المبلغ الإجمالي للمصروف بالجنيه المصري</div>
                                        </div>

                                        <!-- Description -->
                                        <div class="col-12">
                                            <label class="form-label" for="desc_ar">
                                                <i class="fas fa-align-right text-secondary me-1"></i> <?= defined('DESCRIPTION') ? DESCRIPTION : 'بيان / وصف المصروف' ?> <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="desc_ar" id="desc_ar" rows="3" class="form-control" placeholder="مثال: فاتورة كهرباء، صيانة أجهزة، مستلزمات مكتبية، نثريات..." required></textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                        <a href="expenditure.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i> إلغاء
                                        </a>
                                        <div class="d-flex gap-2">
                                            <button type="submit" id="submitBtn" class="btn btn-primary px-4" style="background:#2563eb; border-color:#2563eb;">
                                                <i class="fas fa-save me-1"></i> حفظ المصروف
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once("footer.php"); ?>

    <script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({ width: '100%', dir: 'rtl' });
        }

        $('#desc_ar').on('input change', function() {
            $('#desc_en').val($(this).val());
        });

        $('#addExpenditureForm').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var btn = $('#submitBtn');
            var origHtml = btn.html();

            var cost = parseFloat($('#cost').val());
            if (isNaN(cost) || cost <= 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('تنبيه', 'يرجى إدخال مبلغ صحيح أكبر من الصفر', 'warning');
                } else {
                    alert('يرجى إدخال مبلغ صحيح أكبر من الصفر');
                }
                return false;
            }

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري الحفظ...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(resp) {
                    btn.prop('disabled', false).html(origHtml);
                    var cleanResp = $.trim(resp);

                    // Check if response contains success keyword or matches COST_ADDED_SUCCESSFULLY
                    if (cleanResp.indexOf('نجاح') !== -1 || cleanResp.indexOf('SUCCESS') !== -1 || cleanResp.indexOf('تم') !== -1) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحفظ بنجاح',
                                text: 'تم تسجيل سند المصروف بنجاح في النظام',
                                showCancelButton: true,
                                confirmButtonText: 'الذهاب لسجل المصروفات',
                                cancelButtonText: 'إضافة مصروف آخر'
                            }).then(function(result) {
                                if (result.isConfirmed) {
                                    window.location.href = 'expenditure.php';
                                } else {
                                    form[0].reset();
                                    $('#dated').val('<?= date('Y-m-d') ?>');
                                    $('#cost').focus();
                                }
                            });
                        } else {
                            alert('تم حفظ المصروف بنجاح');
                            window.location.href = 'expenditure.php';
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('خطأ في الحفظ', cleanResp, 'error');
                        } else {
                            alert('خطأ: ' + cleanResp);
                        }
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html(origHtml);
                    alert('حدث خطأ في الاتصال بالسيرفر');
                }
            });
        });
    });
    </script>
</body>
</html>
