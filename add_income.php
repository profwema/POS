<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);

// Pre-cache branches
$branches = [];
$resBranches = mysqli_query($adController->MySQL, "SELECT id, name_ar, name_en FROM branches WHERE storeid='$storeid' OR storeid='0' ORDER BY id ASC");
if ($resBranches) {
    while ($br = mysqli_fetch_assoc($resBranches)) {
        $branches[] = $br;
    }
}

// Pre-cache stores
$stores = [];
$resStores = mysqli_query($adController->MySQL, "SELECT id, name_ar, name_en, branch, defualt FROM stores WHERE storeid='$storeid' OR storeid='0' ORDER BY id ASC");
$currStore = $_SESSION['store'] ?? '';
if ($resStores) {
    while ($st = mysqli_fetch_assoc($resStores)) {
        $stores[] = $st;
        if ($currStore === '' && ($st['defualt'] ?? 0) == 1) {
            $currStore = $st['id'];
        }
    }
}

// Pre-cache items list
$itemsQuery = "SELECT i.id, i.barcode, i.vat, i.name_ar, i.name_en, u.name_ar AS unit_ar, u.name_en AS unit_en
               FROM items i
               LEFT JOIN units u ON u.id = i.unit
               WHERE i.storeid = '$storeid' OR i.storeid = '0'
               ORDER BY i.name_ar ASC";
$resItems = mysqli_query($adController->MySQL, $itemsQuery);

$cachedItems = [];
if ($resItems) {
    while ($it = mysqli_fetch_assoc($resItems)) {
        $bCode = !empty($it['barcode']) ? $it['barcode'] : '---';
        $iName = ($language == 'en' && !empty($it['name_en'])) ? $it['name_en'] : (!empty($it['name_ar']) ? $it['name_ar'] : 'صنف');
        $uName = ($language == 'en' && !empty($it['unit_en'])) ? $it['unit_en'] : (!empty($it['unit_ar']) ? $it['unit_ar'] : 'وحدة');
        
        $cachedItems[] = [
            'id'      => (int)$it['id'],
            'barcode' => $bCode,
            'name'    => $iName,
            'unit'    => $uName
        ];
    }
}

// Generate new incoming invoice number
$query = "SELECT income_No FROM income WHERE storeid='$storeid' ORDER BY id DESC LIMIT 1";
$res   = mysqli_query($adController->MySQL, $query);
$num   = $res ? mysqli_num_rows($res) : 0;
$data  = $res ? mysqli_fetch_assoc($res) : null;
if ($num && !empty($data['income_No'])) {
    $rest = substr($data['income_No'], -4);  
    $trans_No = intval($rest) + 1;
} else {
    $trans_No = 1;
}
$time    = time();
$invoice = $storeid . '-' . $time . '-' . str_pad($trans_No, 4, "0", STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء إذن إضافة وارد مخزني | WAM Tech ERP</title>
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
                            <i class="fas fa-plus-circle text-success me-2"></i> إنشاء إذن إضافة وارد مخزني
                        </h2>
                        <p class="text-muted mb-0">إدخال بضائع للمستودع بصورة مباشرة وزيادة أرصدة الأصناف</p>
                    </div>
                    <div>
                        <a href="incoming-entry.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة لسجل أذونات الوارد
                        </a>
                    </div>
                </div>

                <form id="incomingForm">
                    <input type="hidden" name="f" value="addIncoming">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                    <!-- Header Card -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-info-circle text-primary me-2"></i> بيانات إذن الإضافة
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-hashtag me-1"></i> رقم إذن الوارد
                                    </label>
                                    <input type="text" name="invoice" class="form-control font-monospace bg-light fw-bold text-success" value="<?= htmlspecialchars($invoice) ?>" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-calendar-alt me-1"></i> تاريخ الإذن <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>

                                <div class="col-md-4">
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
                            </div>
                        </div>
                    </div>

                    <!-- Items Dynamic Table -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-boxes text-success me-2"></i> أصناف إذن الإضافة
                            </h6>
                            <button type="button" id="btnAddRow" class="btn btn-sm btn-success">
                                <i class="fas fa-plus me-1"></i> إضافة بند جديد
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive table-custom mb-3">
                                <table class="table table-bordered align-middle mb-0" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;">#</th>
                                            <th style="width: 25%;">المخزن المستلم <span class="text-danger">*</span></th>
                                            <th style="width: 45%;">الصنف (الباركود | الاسم | الوحدة) <span class="text-danger">*</span></th>
                                            <th style="width: 20%;">الكمية الواردة <span class="text-danger">*</span></th>
                                            <th style="width: 50px;">حذف</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <!-- Dynamic Rows Injected by JS -->
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" id="btnAddRowBottom" class="btn btn-outline-success">
                                <i class="fas fa-plus me-1"></i> إضافة سطر آخر
                            </button>
                        </div>
                    </div>

                    <!-- Summary & Actions -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <div class="d-flex gap-4">
                                        <div>
                                            <span class="text-muted d-block small">عدد الأصناف:</span>
                                            <span class="fw-bold fs-5 text-dark" id="displayTotalRows">0</span>
                                        </div>
                                        <div>
                                            <span class="text-muted d-block small">إجمالي الكميات الواردة:</span>
                                            <span class="fw-bold fs-5 text-success font-monospace" id="displayTotalQty">0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                    <button type="submit" id="btnSubmitForm" class="btn btn-success px-4 py-2 fs-6">
                                        <i class="fas fa-save me-1"></i> حفظ واعتماد إذن الوارد
                                    </button>
                                    <a href="incoming-entry.php" class="btn btn-outline-secondary px-4 py-2 fs-6 ms-2">
                                        إلغاء
                                    </a>
                                </div>
                            </div>
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
        var cachedItems = <?= json_encode($cachedItems, JSON_UNESCAPED_UNICODE) ?>;
        var storesList  = <?= json_encode($stores, JSON_UNESCAPED_UNICODE) ?>;
        var currStoreId = "<?= $currStore ?>";
        var lang        = "<?= $language ?>";
        var rowCount    = 0;

        function buildStoreOptions(selectedId) {
            var html = '<option value="">-- اختر المخزن --</option>';
            for (var i = 0; i < storesList.length; i++) {
                var st = storesList[i];
                var name = (lang === 'en' && st.name_en) ? st.name_en : st.name_ar;
                var sel = (selectedId && selectedId == st.id) ? 'selected' : (!selectedId && currStoreId == st.id ? 'selected' : '');
                html += '<option value="' + st.id + '" ' + sel + '>' + name + '</option>';
            }
            return html;
        }

        function buildItemOptions(selectedId) {
            var html = '<option value="">-- اختر الصنف --</option>';
            for (var i = 0; i < cachedItems.length; i++) {
                var it = cachedItems[i];
                var sel = (selectedId && selectedId == it.id) ? 'selected' : '';
                var optVal = '0@@' + it.id;
                var optText = it.barcode + ' | ' + it.name + ' (' + it.unit + ')';
                html += '<option value="' + optVal + '" ' + sel + '>' + optText + '</option>';
            }
            return html;
        }

        function addRow() {
            rowCount++;
            var idx = rowCount;
            var tr = $('<tr id="row-' + idx + '"></tr>');

            var tdSeq = $('<td class="text-center text-muted fw-bold row-seq"></td>');
            
            var tdStore = $('<td></td>');
            var selectStore = $('<select name="store[]" class="form-select select2-store-row" required></select>');
            selectStore.html(buildStoreOptions(null));
            tdStore.append(selectStore);

            var tdItem = $('<td></td>');
            var selectItem = $('<select name="barcode[]" class="form-select select2-item-row" required></select>');
            selectItem.html(buildItemOptions(null));
            tdItem.append(selectItem);

            var tdQty = $('<td><input type="number" step="0.01" min="0.01" name="item_qty[]" class="form-control text-center item-qty-input font-monospace fw-bold" required placeholder="0.00"></td>');
            var tdDel = $('<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fas fa-times"></i></button></td>');

            tr.append(tdSeq, tdStore, tdItem, tdQty, tdDel);
            $('#itemsTableBody').append(tr);

            selectStore.select2({ dir: 'rtl', width: '100%' });
            selectItem.select2({ dir: 'rtl', width: '100%' });

            renumberRows();
            calculateAll();
        }

        function renumberRows() {
            $('#itemsTableBody tr').each(function(index) {
                $(this).find('.row-seq').text(index + 1);
            });
        }

        function calculateAll() {
            var totalQty = 0;
            var count = 0;

            $('#itemsTableBody tr').each(function() {
                var itVal = $(this).find('.select2-item-row').val();
                var q = parseFloat($(this).find('.item-qty-input').val()) || 0;
                if (itVal && q > 0) {
                    count++;
                    totalQty += q;
                }
            });

            $('#displayTotalRows').text(count);
            $('#displayTotalQty').text(totalQty.toFixed(2));
        }

        $(document).ready(function() {
            $('.select2-basic').select2({ dir: 'rtl', width: '100%' });

            // Initial 2 rows
            addRow();
            addRow();

            $('#btnAddRow, #btnAddRowBottom').on('click', function() {
                addRow();
            });

            $(document).on('click', '.btn-remove-row', function() {
                if ($('#itemsTableBody tr').length <= 1) {
                    alert('يجب أن يحتوي إذن الوارد على صنف واحد على الأقل.');
                    return;
                }
                $(this).closest('tr').remove();
                renumberRows();
                calculateAll();
            });

            $(document).on('input', '.item-qty-input', function() {
                calculateAll();
            });

            // Form Submit
            $('#incomingForm').on('submit', function(e) {
                e.preventDefault();

                var hasValidRow = false;
                $('#itemsTableBody tr').each(function() {
                    var stVal = $(this).find('.select2-store-row').val();
                    var itVal = $(this).find('.select2-item-row').val();
                    var q     = parseFloat($(this).find('.item-qty-input').val()) || 0;
                    if (stVal && itVal && q > 0) {
                        hasValidRow = true;
                    }
                });

                if (!hasValidRow) {
                    alert('يرجى تحديد المخزن، الصنف، وإدخال كمية صحيحة لبند واحد على الأقل.');
                    return;
                }

                var btn = $('#btnSubmitForm');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري حفظ إذن الوارد...');

                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(resp) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ واعتماد إذن الوارد');
                        if ($.trim(resp) === '1' || parseInt(resp) > 0) {
                            alert('تم تسجيل إذن الإضافة الوارد بنجاح وزيادة أرصدة الأصناف بالمخازن.');
                            window.location.href = 'incoming-entry.php';
                        } else {
                            alert(resp || 'حدث خطأ أثناء حفظ الإذن.');
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ واعتماد إذن الوارد');
                        alert('تعذر الاتصال بالخادم.');
                    }
                });
            });
        });
    </script>
</body>
</html>
