<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);

// Generate transfer number
$query = "SELECT trans_number FROM store_trans WHERE storeid='$storeid' ORDER BY id DESC LIMIT 1";
$res   = mysqli_query($adController->MySQL, $query);
$num   = $res ? mysqli_num_rows($res) : 0;
$data  = $res ? mysqli_fetch_assoc($res) : null;
if ($num && !empty($data['trans_number'])) {
    $rest = substr($data['trans_number'], -4);  
    $trans_No = intval($rest) + 1;
} else {
    $trans_No = 1;
}
$time    = time();
$invoice = $storeid . '-' . $time . '-' . str_pad($trans_No, 4, "0", STR_PAD_LEFT);

// Pre-cache stores
$stores = [];
$resStores = mysqli_query($adController->MySQL, "SELECT id, name_ar, name_en, defualt FROM stores WHERE storeid='$storeid' OR storeid='0' ORDER BY id ASC");
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
$itemsQuery = "SELECT i.id, i.barcode, i.name_ar, i.name_en, u.name_ar AS unit_ar, u.name_en AS unit_en
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
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء تحويل مخزني جديد | WAM Tech ERP</title>
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
                            <i class="fas fa-truck-loading text-primary me-2"></i> إنشاء إذن تحويل مخزني
                        </h2>
                        <p class="text-muted mb-0">نقل بضائع ومخزون من مستودع إلى مستودع آخر مع تحديث أرصدة الطرفين تلقائياً</p>
                    </div>
                    <div>
                        <a href="stores-transfer.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة لسجل التحويلات
                        </a>
                    </div>
                </div>

                <form id="storeTransForm">
                    <input type="hidden" name="f" value="addStoreTrans">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                    <!-- Transfer Header Card -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-info-circle text-primary me-2"></i> بيانات إذن التحويل
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-hashtag me-1"></i> رقم إذن التحويل
                                    </label>
                                    <input type="text" name="invoice" class="form-control font-monospace bg-light fw-bold text-primary" value="<?= htmlspecialchars($invoice) ?>" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-calendar-alt me-1"></i> تاريخ التحويل <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="trans_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-warehouse text-danger me-1"></i> من مخزن (المصدر) <span class="text-danger">*</span>
                                    </label>
                                    <select name="from" id="fromStore" class="form-select select2-basic" required>
                                        <option value="">-- اختر مخزن المصدر --</option>
                                        <?php foreach ($stores as $st): 
                                            $stName = $st['name_' . $language] ?? $st['name_ar'];
                                            $sel = ($currStore !== '' && $currStore == $st['id']) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $st['id'] ?>" <?= $sel ?>><?= htmlspecialchars($stName) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-warehouse text-success me-1"></i> إلى مخزن (الوجهة) <span class="text-danger">*</span>
                                    </label>
                                    <select name="to" id="toStore" class="form-select select2-basic" required>
                                        <option value="">-- اختر مخزن الوجهة --</option>
                                        <?php foreach ($stores as $st): 
                                            $stName = $st['name_' . $language] ?? $st['name_ar'];
                                        ?>
                                            <option value="<?= $st['id'] ?>"><?= htmlspecialchars($stName) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-comment-alt me-1"></i> ملاحظات وبيان سبب التحويل
                                    </label>
                                    <textarea name="note" class="form-control" rows="2" placeholder="أدخل أية ملاحظات توضيحية بخصوص عملية النقل..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Dynamic Table -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-boxes text-primary me-2"></i> الأصناف والكميات المراد تحويلها
                            </h6>
                            <button type="button" id="btnAddRow" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> إضافة بند للتحويل
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive table-custom mb-3">
                                <table class="table table-bordered align-middle mb-0" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th style="width: 60%;">الصنف (الباركود | الاسم | الوحدة) <span class="text-danger">*</span></th>
                                            <th style="width: 30%;">الكمية المحولة <span class="text-danger">*</span></th>
                                            <th style="width: 60px;">حذف</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <!-- Dynamic Rows Injected by JS -->
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" id="btnAddRowBottom" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-1"></i> إضافة سطر آخر
                            </button>
                        </div>
                    </div>

                    <!-- Actions & Footer -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <div class="d-flex gap-4">
                                        <div>
                                            <span class="text-muted d-block small">عدد الأصناف المحولة:</span>
                                            <span class="fw-bold fs-5 text-dark" id="displayTotalRows">0</span>
                                        </div>
                                        <div>
                                            <span class="text-muted d-block small">إجمالي الكميات:</span>
                                            <span class="fw-bold fs-5 text-success font-monospace" id="displayTotalQty">0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                    <button type="submit" id="btnSubmitForm" class="btn btn-primary px-4 py-2 fs-6">
                                        <i class="fas fa-check-circle me-1"></i> اعتماد وحفظ التحويل
                                    </button>
                                    <a href="stores-transfer.php" class="btn btn-outline-secondary px-4 py-2 fs-6 ms-2">
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
        var rowCount = 0;

        function buildItemOptions(selectedId) {
            var html = '<option value="">-- اختر الصنف --</option>';
            for (var i = 0; i < cachedItems.length; i++) {
                var it = cachedItems[i];
                var sel = (selectedId && selectedId == it.id) ? 'selected' : '';
                var optVal = it.id;
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
            
            var tdItem = $('<td></td>');
            var select = $('<select name="barcode[]" class="form-select select2-item-row" required></select>');
            select.html(buildItemOptions(null));
            tdItem.append(select);

            var tdQty = $('<td><input type="number" step="0.01" min="0.01" name="item_qty[]" class="form-control text-center item-qty-input font-monospace fw-bold" required placeholder="0.00"></td>');
            var tdDel = $('<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fas fa-times"></i></button></td>');

            tr.append(tdSeq, tdItem, tdQty, tdDel);
            $('#itemsTableBody').append(tr);

            select.select2({
                dir: 'rtl',
                width: '100%'
            });

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
                var itId = $(this).find('.select2-item-row').val();
                var q = parseFloat($(this).find('.item-qty-input').val()) || 0;
                if (itId && q > 0) {
                    count++;
                    totalQty += q;
                }
            });

            $('#displayTotalRows').text(count);
            $('#displayTotalQty').text(totalQty.toFixed(2));
        }

        $(document).ready(function() {
            $('.select2-basic').select2({
                dir: 'rtl',
                width: '100%'
            });

            // Initial 2 rows
            addRow();
            addRow();

            $('#btnAddRow, #btnAddRowBottom').on('click', function() {
                addRow();
            });

            $(document).on('click', '.btn-remove-row', function() {
                if ($('#itemsTableBody tr').length <= 1) {
                    alert('يجب أن يحتوي إذن التحويل على صنف واحد على الأقل.');
                    return;
                }
                $(this).closest('tr').remove();
                renumberRows();
                calculateAll();
            });

            $(document).on('input', '.item-qty-input', function() {
                calculateAll();
            });

            // Form Submit with Validation
            $('#storeTransForm').on('submit', function(e) {
                e.preventDefault();

                var fromSt = $('#fromStore').val();
                var toSt   = $('#toStore').val();

                if (!fromSt || !toSt) {
                    alert('يرجى تحديد مخزن المصدر ومخزن الوجهة.');
                    return;
                }

                if (fromSt === toSt) {
                    alert('خطأ: لا يمكن التحويل لنفس المخزن! يجب أن يكون مخزن الوجهة مختلفاً عن مخزن المصدر.');
                    return;
                }

                var hasValidRow = false;
                $('#itemsTableBody tr').each(function() {
                    var itId = $(this).find('.select2-item-row').val();
                    var q = parseFloat($(this).find('.item-qty-input').val()) || 0;
                    if (itId && q > 0) {
                        hasValidRow = true;
                    }
                });

                if (!hasValidRow) {
                    alert('يرجى إدخال صنف واحد على الأقل مع تحديد كمية التحويل.');
                    return;
                }

                var btn = $('#btnSubmitForm');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري اعتماد التحويل...');

                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(resp) {
                        btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> اعتماد وحفظ التحويل');
                        if ($.trim(resp) === '1' || parseInt(resp) > 0) {
                            alert('تم تسجيل إذن التحويل المخزني بنجاح وتحديث أرصدة المخازن.');
                            window.location.href = 'stores-transfer.php';
                        } else {
                            alert(resp || 'حدث خطأ أثناء حفظ التحويل.');
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> اعتماد وحفظ التحويل');
                        alert('تعذر الاتصال بالخادم.');
                    }
                });
            });
        });
    </script>
</body>
</html>
