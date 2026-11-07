<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);

// Pre-cache leaf accounts with their parent account names
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
        $aName = ($language == 'en' && !empty($acc['name_en'])) ? $acc['name_en'] : (!empty($acc['name_ar']) ? $acc['name_ar'] : 'حساب');
        $pName = ($language == 'en' && !empty($acc['parent_en'])) ? $acc['parent_en'] : (!empty($acc['parent_ar']) ? $acc['parent_ar'] : '');
        $code  = $acc['code'];
        $label = ($pName ? $pName . " » " : "") . $aName . " (" . $code . ")";
        $cachedAccounts[] = [
            'code'  => $code,
            'label' => $label
        ];
    }
}

// Pre-cache branches
$branches = [];
$resBranches = mysqli_query($adController->MySQL, "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' OR storeid='0' ORDER BY id ASC");
if ($resBranches) {
    while ($br = mysqli_fetch_assoc($resBranches)) {
        $branches[] = $br;
    }
}

// Generate new journal number
$query = "SELECT journalNo FROM journal ORDER BY id DESC LIMIT 1";
$res   = mysqli_query($adController->MySQL, $query);
$num   = $res ? mysqli_num_rows($res) : 0;
$data  = $res ? mysqli_fetch_assoc($res) : null;
if ($num && !empty($data['journalNo'])) {
    $rest = substr($data['journalNo'], -4);  
    $trans_No = intval($rest) + 1;
} else {
    $trans_No = 1;
}
$time = time();
$journalNo = 'JOUR-' . $time . '-' . str_pad($trans_No, 4, "0", STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة قيد يومية عامة جديد | WAM Tech ERP</title>
    <link rel="stylesheet" href="css/admin-pro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .table-custom {
            border: 1px solid #edf2f7;
            border-radius: 8px;
            overflow: hidden;
        }
        .table-custom th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 12px;
            text-align: center;
        }
        .table-custom td {
            padding: 8px;
            vertical-align: middle;
        }
        .balance-box {
            border-radius: 12px;
            padding: 18px 24px;
            transition: all 0.2s ease-in-out;
        }
        .balance-box.balanced {
            background-color: #f0fdf4;
            border: 2px solid #86efac;
            color: #166534;
        }
        .balance-box.unbalanced {
            background-color: #fff1f2;
            border: 2px solid #fca5a5;
            color: #991b1b;
        }
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
                            <i class="fas fa-plus-circle text-primary me-2"></i> إضافة قيد يومية عامة جديد
                        </h2>
                        <p class="text-muted mb-0">إنشاء قيد محاسبي مزدوج متوازن مع تحديد مراكز التكلفة والمراجع</p>
                    </div>
                    <div>
                        <a href="journal.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة لدفتر اليومية
                        </a>
                    </div>
                </div>

                <form id="journalForm">
                    <input type="hidden" name="f" value="addJournal">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="total-debit" id="hiddenTotalDebit" value="0.00">
                    <input type="hidden" name="total-credit" id="hiddenTotalCredit" value="0.00">
                    <input type="hidden" name="cost" id="cost" value="0">

                    <!-- Header Card -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-info-circle text-primary me-2"></i> بيانات القيد المحاسبي
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-hashtag me-1"></i> رقم القيد
                                    </label>
                                    <input type="text" name="journalNo" class="form-control font-monospace bg-light fw-bold text-primary" value="<?= htmlspecialchars($journalNo) ?>" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-file-alt me-1"></i> رقم المستند المرفق
                                    </label>
                                    <input type="text" name="document" id="document" class="form-control" placeholder="رقم الشيك أو الإيصال أو المستند">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-code-branch me-1"></i> الفرع <span class="text-danger">*</span>
                                    </label>
                                    <select name="branch" class="form-select select2-basic" required>
                                        <option value="">-- اختر الفرع --</option>
                                        <?php foreach ($branches as $br): 
                                            $brName = $br['name_' . $language] ?? $br['name_ar'];
                                        ?>
                                            <option value="<?= $br['id'] ?>"><?= htmlspecialchars($brName) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-calendar-alt me-1"></i> تاريخ القيد <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-align-right me-1"></i> البيان العام للقيد (شرح القيد) <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="disc" id="disc" class="form-control" rows="2" placeholder="بيان توضيحي لسبب القيد وأطراف المعاملة..." required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Dynamic Table -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-list-ol text-primary me-2"></i> أطراف القيد (الحسابات، المدين والدائن)
                            </h6>
                            <button type="button" id="btnAddRow" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> إضافة طرف جديد
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive table-custom mb-3">
                                <table class="table table-bordered align-middle mb-0" id="journalTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;">#</th>
                                            <th style="width: 35%;">الحساب المالي <span class="text-danger">*</span></th>
                                            <th style="width: 14%;">مدين (Debit)</th>
                                            <th style="width: 14%;">دائن (Credit)</th>
                                            <th style="width: 22%;">البيان / الشرح التفصيلي</th>
                                            <th style="width: 11%;">المرجع</th>
                                            <th style="width: 45px;">حذف</th>
                                        </tr>
                                    </thead>
                                    <tbody id="journalTableBody">
                                        <!-- Dynamic Rows Injected by JS -->
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" id="btnAddRowBottom" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-1"></i> إضافة سطر آخر للقيد
                            </button>
                        </div>
                    </div>

                    <!-- Balance Checker Box -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-4">
                            <div class="balance-box unbalanced" id="balanceStatusBox">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <span class="d-block small text-muted">إجمالي المدين (Debit):</span>
                                        <span class="fs-4 fw-bold font-monospace text-success" id="displayTotalDebit">0.00 ج.م</span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="d-block small text-muted">إجمالي الدائن (Credit):</span>
                                        <span class="fs-4 fw-bold font-monospace text-danger" id="displayTotalCredit">0.00 ج.م</span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="d-block small text-muted">الفارق (Difference):</span>
                                        <span class="fs-4 fw-bold font-monospace" id="displayDifference">0.00 ج.م</span>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <div class="badge px-3 py-2 fs-6 rounded-pill" id="balanceStatusBadge">
                                            <i class="fas fa-exclamation-circle me-1"></i> القيد غير متزن
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit & Actions -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-4 text-end">
                            <a href="journal.php" class="btn btn-outline-secondary px-4 py-2 fs-6 me-2">
                                إلغاء
                            </a>
                            <button type="submit" id="btnSubmitJournal" class="btn btn-primary px-4 py-2 fs-6" disabled>
                                <i class="fas fa-save me-1"></i> ترحيل واعتماد القيد
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        var cachedAccounts = <?= json_encode($cachedAccounts, JSON_UNESCAPED_UNICODE) ?>;
        var rowCount = 0;

        function buildAccountOptions(selectedCode) {
            var html = '<option value="">-- اختر الحساب --</option>';
            for (var i = 0; i < cachedAccounts.length; i++) {
                var acc = cachedAccounts[i];
                var sel = (selectedCode && selectedCode == acc.code) ? 'selected' : '';
                html += '<option value="' + acc.code + '" ' + sel + '>' + acc.label + '</option>';
            }
            return html;
        }

        function addRow() {
            rowCount++;
            var idx = rowCount;
            var tr = $('<tr id="row-' + idx + '"></tr>');

            var tdSeq = $('<td class="text-center text-muted fw-bold row-seq"></td>');
            
            var tdAcc = $('<td></td>');
            var selectAcc = $('<select name="account[]" class="form-select select2-acc-row" required></select>');
            selectAcc.html(buildAccountOptions(null));
            tdAcc.append(selectAcc);

            var tdDebit  = $('<td><input type="number" step="0.01" min="0" name="debit[]" class="form-control text-center item-debit-input font-monospace fw-bold text-success" value="0.00"></td>');
            var tdCredit = $('<td><input type="number" step="0.01" min="0" name="credit[]" class="form-control text-center item-credit-input font-monospace fw-bold text-danger" value="0.00"></td>');
            var tdDisc   = $('<td><input type="text" name="discItem[]" class="form-control" placeholder="شرح البند"></td>');
            var tdRef    = $('<td><input type="text" name="ref[]" class="form-control font-monospace" placeholder="مرجع"><input type="hidden" name="costItem[]" value="0"></td>');
            var tdDel    = $('<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fas fa-times"></i></button></td>');

            tr.append(tdSeq, tdAcc, tdDebit, tdCredit, tdDisc, tdRef, tdDel);
            $('#journalTableBody').append(tr);

            selectAcc.select2({ dir: 'rtl', width: '100%' });

            renumberRows();
            calculateBalance();
        }

        function renumberRows() {
            $('#journalTableBody tr').each(function(index) {
                $(this).find('.row-seq').text(index + 1);
            });
        }

        function calculateBalance() {
            var totalDebit  = 0;
            var totalCredit = 0;

            $('#journalTableBody tr').each(function() {
                var d = parseFloat($(this).find('.item-debit-input').val()) || 0;
                var c = parseFloat($(this).find('.item-credit-input').val()) || 0;
                totalDebit += d;
                totalCredit += c;
            });

            $('#hiddenTotalDebit').val(totalDebit.toFixed(2));
            $('#hiddenTotalCredit').val(totalCredit.toFixed(2));
            $('#displayTotalDebit').text(totalDebit.toFixed(2) + ' ج.م');
            $('#displayTotalCredit').text(totalCredit.toFixed(2) + ' ج.م');

            var diff = Math.abs(totalDebit - totalCredit);
            $('#displayDifference').text(diff.toFixed(2) + ' ج.م');

            var isBalanced = (diff < 0.01) && (totalDebit > 0);

            if (isBalanced) {
                $('#balanceStatusBox').removeClass('unbalanced').addClass('balanced');
                $('#balanceStatusBadge')
                    .removeClass('bg-danger text-white')
                    .addClass('bg-success text-white')
                    .html('<i class="fas fa-check-circle me-1"></i> القيد متزن 100%');
                $('#btnSubmitJournal').prop('disabled', false);
            } else {
                $('#balanceStatusBox').removeClass('balanced').addClass('unbalanced');
                $('#balanceStatusBadge')
                    .removeClass('bg-success text-white')
                    .addClass('bg-danger text-white')
                    .html('<i class="fas fa-exclamation-circle me-1"></i> القيد غير متزن (فارق ' + diff.toFixed(2) + ')');
                $('#btnSubmitJournal').prop('disabled', true);
            }
        }

        $(document).ready(function() {
            $('.select2-basic').select2({ dir: 'rtl', width: '100%' });

            // Initial 2 rows for double-entry
            addRow();
            addRow();

            $('#btnAddRow, #btnAddRowBottom').on('click', function() {
                addRow();
            });

            $(document).on('click', '.btn-remove-row', function() {
                if ($('#journalTableBody tr').length <= 2) {
                    alert('يجب أن يحتوي القيد المحاسبي على طرفين على الأقل (مدين ودائن).');
                    return;
                }
                $(this).closest('tr').remove();
                renumberRows();
                calculateBalance();
            });

            $(document).on('input', '.item-debit-input, .item-credit-input', function() {
                calculateBalance();
            });

            // Prevent entering both debit and credit on the same row
            $(document).on('input', '.item-debit-input', function() {
                var val = parseFloat($(this).val()) || 0;
                if (val > 0) {
                    $(this).closest('tr').find('.item-credit-input').val('0.00');
                }
                calculateBalance();
            });

            $(document).on('input', '.item-credit-input', function() {
                var val = parseFloat($(this).val()) || 0;
                if (val > 0) {
                    $(this).closest('tr').find('.item-debit-input').val('0.00');
                }
                calculateBalance();
            });

            // Form Submit
            $('#journalForm').on('submit', function(e) {
                e.preventDefault();

                var diff = Math.abs(parseFloat($('#hiddenTotalDebit').val()) - parseFloat($('#hiddenTotalCredit').val()));
                if (diff >= 0.01 || parseFloat($('#hiddenTotalDebit').val()) <= 0) {
                    alert('خطأ محاسبي: لا يمكن ترحيل قيد غير متوازن! يجب أن يتساوى إجمالي المدين مع إجمالي الدائن تماماً.');
                    return;
                }

                var btn = $('#btnSubmitJournal');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري ترحيل القيد...');

                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(resp) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> ترحيل واعتماد القيد');
                        if ($.trim(resp) === '1' || parseInt(resp) > 0) {
                            alert('تم ترحيل قيد اليومية العامة بنجاح.');
                            window.location.href = 'journal.php';
                        } else {
                            alert(resp || 'حدث خطأ أثناء ترحيل القيد.');
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> ترحيل واعتماد القيد');
                        alert('تعذر الاتصال بالخادم.');
                    }
                });
            });
        });
    </script>
</body>
</html>
