<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? (defined('LANG') ? LANG : 'ar');
$storeid  = intval($_SESSION['storeid'] ?? 1);

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

// Pre-cache items list with cost/price
$itemsQuery = "SELECT i.id, i.barcode, i.vat, i.price, i.name_ar, i.name_en, 
                      u.name_ar AS unit_ar, u.name_en AS unit_en,
                      COALESCE((SELECT pi.cost FROM purchase_items pi WHERE pi.itemid = i.id ORDER BY pi.id DESC LIMIT 1), i.price, 0) as cost_price
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
            'id'       => (int)$it['id'],
            'barcode'  => $bCode,
            'name'     => $iName,
            'unit'     => $uName,
            'cost'     => (float)($it['cost_price'] ?: 0),
            'price'    => (float)($it['price'] ?: 0),
            'vat'      => (float)($it['vat'] ?: 0)
        ];
    }
}

$open_palance_date = $_SESSION['open_palance_date'] ?? date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة رصيد افتتاحي جديد | WAM Tech ERP</title>
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
        .row-total-val {
            font-weight: 700;
            font-family: monospace;
            font-size: 1.05rem;
            color: #1e293b;
        }
        .summary-card {
            background: linear-gradient(135deg, #f8fafc, #edf2f7);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
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
                            <i class="fas fa-plus-circle text-primary me-2"></i> إضافة رصيد افتتاحي جديد
                        </h2>
                        <p class="text-muted mb-0">تسجيل أرصدة أول المدة للمستودعات مع تحديد التكلفة والكميات</p>
                    </div>
                    <div>
                        <a href="open-palance.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة للأرصدة الافتتاحية
                        </a>
                    </div>
                </div>

                <form id="openPalanceForm">
                    <input type="hidden" name="f" value="addOpenPalance">
                    <input type="hidden" name="is_ajax" value="1">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                    <!-- Store & Date Header Info -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-warehouse text-primary me-2"></i> بيانات المستودع وتاريخ الاعتماد
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-store me-1"></i> المخزن المستهدف <span class="text-danger">*</span>
                                    </label>
                                    <select name="store" id="storeSelect" class="form-select" required>
                                        <option value="">-- اختر المخزن --</option>
                                        <?php foreach ($stores as $st): 
                                            $stName = $st['name_' . $language] ?? $st['name_ar'];
                                            $sel = ($currStore !== '' && $currStore == $st['id']) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $st['id'] ?>" <?= $sel ?>><?= htmlspecialchars($stName) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary">
                                        <i class="fas fa-calendar-alt me-1"></i> تاريخ الرصيد الافتتاحي <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($open_palance_date) ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Dynamic Table -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-boxes text-primary me-2"></i> بنود وأصناف الرصيد الافتتاحي
                            </h6>
                            <button type="button" id="btnAddRow" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> إضافة بند جديد
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive table-custom mb-3">
                                <table class="table table-bordered align-middle mb-0" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th style="width: 35%;">الصنف (الباركود | الاسم | الوحدة) <span class="text-danger">*</span></th>
                                            <th style="width: 13%;">الكمية <span class="text-danger">*</span></th>
                                            <th style="width: 13%;">سعر التكلفة (ج.م)</th>
                                            <th style="width: 10%;">الخصم %</th>
                                            <th style="width: 10%;">الضريبة %</th>
                                            <th style="width: 14%;">الإجمالي (ج.م)</th>
                                            <th style="width: 50px;">حذف</th>
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

                    <!-- Summary & Actions -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <div class="d-flex gap-4">
                                        <div>
                                            <span class="text-muted d-block small">عدد البنود:</span>
                                            <span class="fw-bold fs-5 text-dark" id="displayTotalRows">0</span>
                                        </div>
                                        <div>
                                            <span class="text-muted d-block small">إجمالي الكميات:</span>
                                            <span class="fw-bold fs-5 text-success font-monospace" id="displayTotalQty">0.00</span>
                                        </div>
                                        <div>
                                            <span class="text-muted d-block small">إجمالي القيمة الافتتاحية:</span>
                                            <span class="fw-bold fs-4 text-primary font-monospace" id="displayTotalAmount">0.00 ج.م</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                    <button type="submit" id="btnSubmitForm" class="btn btn-primary px-4 py-2 fs-6">
                                        <i class="fas fa-save me-1"></i> حفظ واعتماد الأرصدة
                                    </button>
                                    <a href="open-palance.php" class="btn btn-outline-secondary px-4 py-2 fs-6 ms-2">
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
                html += '<option value="' + optVal + '" data-cost="' + it.cost + '" data-price="' + it.price + '" data-vat="' + it.vat + '" ' + sel + '>' + optText + '</option>';
            }
            return html;
        }

        function addRow(prefill) {
            rowCount++;
            var idx = rowCount;
            var tr = $('<tr id="row-' + idx + '"></tr>');

            var tdSeq = $('<td class="text-center text-muted fw-bold row-seq">' + $('#itemsTableBody tr').length + 1 + '</td>');
            
            var tdItem = $('<td></td>');
            var select = $('<select name="barcode[]" class="form-select select2-item-row" required></select>');
            select.html(buildItemOptions(prefill ? prefill.id : null));
            tdItem.append(select);

            var tdQty = $('<td><input type="number" step="0.01" min="0.01" name="item_qty[]" class="form-control text-center item-qty-input" required placeholder="0.00"></td>');
            var tdCost = $('<td><input type="number" step="0.01" name="item_price[]" class="form-control text-center item-cost-input" value="0.00"></td>');
            var tdDisc = $('<td><input type="number" step="0.01" name="item_disc[]" class="form-control text-center item-disc-input" value="0"></td>');
            var tdVat  = $('<td><input type="number" step="0.01" name="item_vat[]" class="form-control text-center item-vat-input" value="0" readonly style="background:#f1f5f9;"></td>');
            var tdTot  = $('<td class="text-end row-total-val font-monospace"><span id="total-span-' + idx + '">0.00</span></td>');
            var tdDel  = $('<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fas fa-times"></i></button></td>');

            tr.append(tdSeq, tdItem, tdQty, tdCost, tdDisc, tdVat, tdTot, tdDel);
            $('#itemsTableBody').append(tr);

            select.select2({
                dir: 'rtl',
                width: '100%'
            });

            select.on('change', function() {
                var opt = $(this).find('option:selected');
                var cost = opt.data('cost') || opt.data('price') || 0;
                var vat  = opt.data('vat') || 0;
                tr.find('.item-cost-input').val(parseFloat(cost).toFixed(2));
                tr.find('.item-vat-input').val(parseFloat(vat).toFixed(2));
                calculateRow(tr);
            });

            renumberRows();
            calculateAll();
        }

        function calculateRow(tr) {
            var q = parseFloat(tr.find('.item-qty-input').val()) || 0;
            var c = parseFloat(tr.find('.item-cost-input').val()) || 0;
            var d = parseFloat(tr.find('.item-disc-input').val()) || 0;

            var preTotal = q * c;
            var discVal  = preTotal * d / 100;
            var total    = preTotal - discVal;

            tr.find('.row-total-val span').text(total.toFixed(2));
            calculateAll();
        }

        function calculateAll() {
            var totalQty = 0;
            var totalAmount = 0;
            var count = 0;

            $('#itemsTableBody tr').each(function() {
                count++;
                var q = parseFloat($(this).find('.item-qty-input').val()) || 0;
                var t = parseFloat($(this).find('.row-total-val span').text()) || 0;
                totalQty += q;
                totalAmount += t;
            });

            $('#displayTotalRows').text(count);
            $('#displayTotalQty').text(totalQty.toFixed(2));
            $('#displayTotalAmount').text(totalAmount.toFixed(2) + ' ج.م');
        }

        function renumberRows() {
            $('#itemsTableBody tr').each(function(index) {
                $(this).find('.row-seq').text(index + 1);
            });
        }

        $(document).ready(function() {
            // Initial 3 rows
            addRow();
            addRow();
            addRow();

            $('#btnAddRow, #btnAddRowBottom').on('click', function() {
                addRow();
            });

            $(document).on('click', '.btn-remove-row', function() {
                if ($('#itemsTableBody tr').length <= 1) {
                    alert('يجب أن يحتوي الرصيد الافتتاحي على بند واحد على الأقل.');
                    return;
                }
                $(this).closest('tr').remove();
                renumberRows();
                calculateAll();
            });

            $(document).on('input', '.item-qty-input, .item-cost-input, .item-disc-input', function() {
                var tr = $(this).closest('tr');
                calculateRow(tr);
            });

            // AJAX submission
            $('#openPalanceForm').on('submit', function(e) {
                e.preventDefault();

                if (!$('#storeSelect').val()) {
                    alert('يرجى اختيار المخزن أولاً.');
                    return;
                }

                var hasValidRow = false;
                $('#itemsTableBody tr').each(function() {
                    var itId = $(this).find('.select2-item-row').val();
                    var qty  = parseFloat($(this).find('.item-qty-input').val()) || 0;
                    if (itId && qty > 0) {
                        hasValidRow = true;
                    }
                });

                if (!hasValidRow) {
                    alert('يرجى اختيار صنف واحد على الأقل مع إدخال كمية صحيحة.');
                    return;
                }

                var btn = $('#btnSubmitForm');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري حفظ الأرصدة...');

                $.ajax({
                    url: 'controller.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(resp) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ واعتماد الأرصدة');
                        if (resp.success) {
                            alert('تم حفظ الأرصدة الافتتاحية بنجاح.');
                            window.location.href = 'open-palance.php';
                        } else {
                            alert(resp.error || 'حدث خطأ أثناء حفظ الأرصدة.');
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ واعتماد الأرصدة');
                        // Fallback if not JSON
                        alert('تم إرسال الطلب، جاري مراجعة النتيجة...');
                        window.location.href = 'open-palance.php';
                    }
                });
            });
        });
    </script>
</body>
</html>
