<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

$rawId = $_REQUEST['sd'] ?? '';
$decrypted = $adController->encrypt_decrypt(2, urldecode($rawId), 0);
$idval = !empty($decrypted) ? $adController->encrypt_decrypt(2, $decrypted, 0) : $rawId;

if (empty($idval) || !is_numeric($idval)) {
    $dec3 = $adController->encrypt_decrypt(2, $idval, 0);
    if (!empty($dec3) && is_numeric($dec3)) {
        $idval = $dec3;
    }
}

$cleanId = intval($idval);
if ($cleanId <= 0 && is_numeric($rawId)) {
    $cleanId = intval($rawId);
}

// Fetch record
$query   = "SELECT * FROM expenditure WHERE storeid='$storeid' AND id='$cleanId' LIMIT 1";
$res     = mysqli_query($adController->MySQL, $query);
$dataCat = $res ? mysqli_fetch_assoc($res) : null;

if (!$dataCat) {
    header("Location: expenditure.php");
    exit();
}

$selectedBranch = $dataCat['store_branch'] ?? '';
$currentCost    = (float)($dataCat['cost'] ?? 0);
$currentDesc    = $dataCat['desc_ar'] ?: ($dataCat['desc_en'] ?? '');
$currentDate    = !empty($dataCat['dated']) ? date('Y-m-d', strtotime($dataCat['dated'])) : date('Y-m-d');

// Prepare encrypted ID for update
$idval2 = urlencode($adController->encrypt_decrypt(1, (string)$cleanId, 0));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= defined('UPDATE_EXPENDITURE') ? UPDATE_EXPENDITURE : 'تعديل سند المصروف' ?> #<?= $cleanId ?> - WAM Tech POS</title>

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
                                <li class="breadcrumb-item active" aria-current="page"><?= defined('UPDATE_EXPENDITURE') ? UPDATE_EXPENDITURE : 'تعديل المصروف' ?> #<?= $cleanId ?></li>
                            </ol>
                        </nav>
                        <h3 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-edit text-warning me-2"></i>
                            <?= defined('UPDATE_EXPENDITURE') ? UPDATE_EXPENDITURE : 'تعديل سند المصروف' ?> <span class="badge bg-secondary-subtle text-secondary fs-6">#<?= $cleanId ?></span>
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
                                    <i class="fas fa-file-invoice-dollar me-2 text-warning"></i> تعديل بيانات سند الصرف
                                </h5>
                                <p class="mb-0 text-white-50 small">قم بتحديث الفرع أو المبلغ أو بيان المصروف ثم اضغط حفظ التعديلات</p>
                            </div>

                            <div class="form-body">
                                <form id="editExpenditureForm" method="POST" action="controller.php">
                                    <input type="hidden" name="f" value="updateExpenditure">
                                    <input type="hidden" name="nd" value="<?= htmlspecialchars((string)$cleanId); ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()); ?>">
                                    <input type="hidden" name="desc_en" id="desc_en" value="<?= htmlspecialchars($currentDesc) ?>">

                                    <div class="row g-3">
                                        <!-- Branch Selection -->
                                        <div class="col-12 col-md-6">
                                            <label class="form-label" for="branch">
                                                <i class="fas fa-code-branch text-secondary me-1"></i> <?= defined('BRANCH') ? BRANCH : 'الفرع' ?> <span class="text-danger">*</span>
                                            </label>
                                            <select name="branch" id="branch" class="form-select select2" required>
                                                <option value="0" <?= ($selectedBranch === '' || $selectedBranch === '0') ? 'selected' : '' ?>>عام (كافة الفروع)</option>
                                                <?php
                                                $query = "SELECT * FROM branches WHERE storeid='$storeid' ORDER BY id ASC";
                                                $res   = mysqli_query($adController->MySQL, $query);
                                                if ($res) {
                                                    while ($b = mysqli_fetch_assoc($res)) {
                                                        $bId   = $b['id'];
                                                        $bName = ($language == 'ar' ? $b['name_ar'] : $b['name_en']) ?: $b['name_ar'];
                                                        $isSelected = ((string)$selectedBranch === (string)$bId || in_array((string)$bId, explode(',', (string)$selectedBranch))) ? 'selected' : '';
                                                        echo "<option value='" . htmlspecialchars($bId) . "' $isSelected>" . htmlspecialchars($bName) . "</option>";
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
                                            <input type="date" name="dated" id="dated" class="form-control" value="<?= htmlspecialchars($currentDate) ?>" required>
                                        </div>

                                        <!-- Amount / Cost -->
                                        <div class="col-12">
                                            <label class="form-label" for="cost">
                                                <i class="fas fa-money-bill-wave text-secondary me-1"></i> <?= defined('COST') ? COST : 'المبلغ المصروف' ?> <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0.01" name="cost" id="cost" class="form-control cost-input" value="<?= htmlspecialchars((string)$currentCost) ?>" required>
                                                <span class="input-group-text fw-bold text-dark"><?= $currency ?></span>
                                            </div>
                                            <div class="form-text">المبلغ الإجمالي للمصروف بالجنيه المصري</div>
                                        </div>

                                        <!-- Description -->
                                        <div class="col-12">
                                            <label class="form-label" for="desc_ar">
                                                <i class="fas fa-align-right text-secondary me-1"></i> <?= defined('DESCRIPTION') ? DESCRIPTION : 'بيان / وصف المصروف' ?> <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="desc_ar" id="desc_ar" rows="3" class="form-control" required><?= htmlspecialchars($currentDesc) ?></textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                        <a href="expenditure.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i> إلغاء
                                        </a>
                                        <div class="d-flex gap-2">
                                            <button type="submit" id="submitBtn" class="btn btn-warning text-dark fw-bold px-4">
                                                <i class="fas fa-save me-1"></i> حفظ التعديلات
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

        $('#editExpenditureForm').on('submit', function(e) {
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

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري التعديل...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(resp) {
                    btn.prop('disabled', false).html(origHtml);
                    var cleanResp = $.trim(resp);

                    if (cleanResp.indexOf('نجاح') !== -1 || cleanResp.indexOf('SUCCESS') !== -1 || cleanResp.indexOf('تم') !== -1) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التعديل بنجاح',
                                text: 'تم تحديث سند المصروف بنجاح',
                                confirmButtonText: 'العودة لسجل المصروفات'
                            }).then(function() {
                                window.location.href = 'expenditure.php';
                            });
                        } else {
                            alert('تم تعديل المصروف بنجاح');
                            window.location.href = 'expenditure.php';
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('خطأ في التعديل', cleanResp, 'error');
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
