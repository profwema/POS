<?php
/**
 * شاشة طباعة ملصقات الباركود للمنتجات
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 */
$pageTitle = "طباعة ملصقات الباركود";
?>

<div class="barcode-print-page">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fas fa-barcode" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                طباعة ملصقات الباركود والأسعار
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                دعم طابعات الباركود الحرارية (38×25مم و 50×25مم) وورق A4 متعدد الملصقات
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= BASE_URL ?>items.php" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i>
                العودة للأصناف
            </a>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i>
                طباعة الآن
            </button>
        </div>
    </div>

    <!-- Controls & Customizer -->
    <div class="card no-print" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; align-items: end;">
            <!-- Select Item -->
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">اختر الصنف المطلوب طباعته</label>
                <select id="itemSelect" class="form-control" onchange="onItemChange(this.value)">
                    <option value="">-- اختر صنفاً --</option>
                    <?php foreach ($allItems as $it): ?>
                        <option value="<?= $it['id'] ?>" 
                            data-name="<?= htmlspecialchars($it['name']) ?>" 
                            data-barcode="<?= htmlspecialchars($it['barcode']) ?>" 
                            data-price="<?= htmlspecialchars($it['retail_price']) ?>"
                            <?= ($selectedItem && $selectedItem['id'] == $it['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($it['name']) ?> (<?= $it['barcode'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Label Size -->
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">مقاس الملصق (Label Size)</label>
                <select id="labelSize" class="form-control" onchange="renderLabels()">
                    <option value="thermal-38x25" selected>حراري مفرد: 38×25 مم (قياسي)</option>
                    <option value="thermal-50x25">حراري مفرد: 50×25 مم</option>
                    <option value="a4-sheet">ورق عادي A4 (24 ملصق بالصفحة)</option>
                </select>
            </div>

            <!-- Number of copies -->
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">عدد النسخ المطلوبة</label>
                <input type="number" id="labelCopies" class="form-control" value="1" min="1" max="100" oninput="renderLabels()">
            </div>

            <!-- Custom Store Name -->
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">اسم المتجر أعلى الملصق</label>
                <input type="text" id="storeName" class="form-control" value="<?= htmlspecialchars(Helpers::setting('store_name', BRAND_NAME)) ?>" oninput="renderLabels()">
            </div>

            <!-- Toggles -->
            <div class="form-group" style="margin-bottom: 0; display: flex; gap: 1.5rem; align-items: center; padding-bottom: 0.5rem;">
                <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-size: 0.88rem; font-weight: 700; margin: 0;">
                    <input type="checkbox" id="showPrice" checked onchange="renderLabels()">
                    إظهار السعر (ج.م)
                </label>
                <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-size: 0.88rem; font-weight: 700; margin: 0;">
                    <input type="checkbox" id="showStoreName" checked onchange="renderLabels()">
                    إظهار اسم المتجر
                </label>
            </div>
        </div>
    </div>

    <!-- Live Preview Area -->
    <div class="card" style="padding: 1.5rem;">
        <div class="no-print" style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 800; color: var(--text-main);">
                <i class="fas fa-eye" style="color: var(--brand-blue); margin-left: 0.4rem;"></i>
                معاينة الطباعة المباشرة (Live Print Preview)
            </div>
            <div style="font-size: 0.82rem; color: var(--text-muted);">
                تأكد من اختيار مقاس الملصق المتطابق مع طابعتك في شاشة الطباعة
            </div>
        </div>

        <!-- Container for print labels -->
        <div id="labelsContainer" class="labels-container thermal-38x25">
            <!-- Dynamic Labels Injected Here -->
        </div>
    </div>
</div>

<!-- Print Stylesheet -->
<style>
/* Base Styles for Preview */
.labels-container {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    background: #0f172a;
    padding: 1rem;
    border-radius: 8px;
    justify-content: center;
}

.barcode-label {
    background: #ffffff !important;
    color: #000000 !important;
    border: 1px dashed #94a3b8;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    text-align: center;
    padding: 2mm;
    overflow: hidden;
    font-family: 'Cairo', sans-serif;
}

/* 38mm x 25mm */
.labels-container.thermal-38x25 .barcode-label {
    width: 38mm;
    height: 25mm;
    max-width: 38mm;
    max-height: 25mm;
}
.labels-container.thermal-38x25 .label-store { font-size: 7pt; font-weight: 700; line-height: 1; margin-bottom: 1mm; }
.labels-container.thermal-38x25 .label-name { font-size: 7.5pt; font-weight: 800; line-height: 1.1; max-height: 5.5mm; overflow: hidden; }
.labels-container.thermal-38x25 .label-price { font-size: 8.5pt; font-weight: 900; line-height: 1; margin-top: 1mm; }

/* 50mm x 25mm */
.labels-container.thermal-50x25 .barcode-label {
    width: 50mm;
    height: 25mm;
    max-width: 50mm;
    max-height: 25mm;
}
.labels-container.thermal-50x25 .label-store { font-size: 7.5pt; font-weight: 700; margin-bottom: 1mm; }
.labels-container.thermal-50x25 .label-name { font-size: 8.5pt; font-weight: 800; max-height: 6mm; overflow: hidden; }
.labels-container.thermal-50x25 .label-price { font-size: 9.5pt; font-weight: 900; margin-top: 1mm; }

/* A4 Sheet layout */
.labels-container.a4-sheet {
    background: #fff;
    color: #000;
    width: 210mm;
    min-height: 297mm;
    padding: 10mm;
    gap: 4mm;
    margin: 0 auto;
}
.labels-container.a4-sheet .barcode-label {
    width: 63.5mm;
    height: 33.9mm;
    border: 1px dotted #ccc;
    padding: 3mm;
}
.labels-container.a4-sheet .label-store { font-size: 8pt; font-weight: 700; }
.labels-container.a4-sheet .label-name { font-size: 9pt; font-weight: 800; }
.labels-container.a4-sheet .label-price { font-size: 10pt; font-weight: 900; }

/* PRINT MEDIA QUERY */
@media print {
    body, html {
        background: #ffffff !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .app-sidebar, .app-header, .app-footer, .no-print, .card-header {
        display: none !important;
    }

    .app-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        padding: 0 !important;
    }

    .labels-container {
        background: #ffffff !important;
        padding: 0 !important;
        gap: 0 !important;
    }

    .barcode-label {
        border: none !important;
        page-break-inside: avoid;
    }

    /* Print specific page sizing */
    @page {
        margin: 0;
    }
}
</style>

<script src="<?= BASE_URL ?>assets/js/barcode.js"></script>
<script>
let currentItemData = null;

function onItemChange(itemId) {
    if (!itemId) {
        currentItemData = null;
        renderLabels();
        return;
    }
    const select = document.getElementById('itemSelect');
    const opt = select.options[select.selectedIndex];
    currentItemData = {
        name: opt.getAttribute('data-name'),
        barcode: opt.getAttribute('data-barcode'),
        price: opt.getAttribute('data-price')
    };
    renderLabels();
}

function renderLabels() {
    const container = document.getElementById('labelsContainer');
    const size = document.getElementById('labelSize').value;
    const copies = parseInt(document.getElementById('labelCopies').value) || 1;
    const storeName = document.getElementById('storeName').value;
    const showPrice = document.getElementById('showPrice').checked;
    const showStore = document.getElementById('showStoreName').checked;

    container.className = 'labels-container ' + size;

    if (!currentItemData) {
        container.innerHTML = '<div class="no-print" style="padding: 3rem; text-align: center; color: var(--text-muted);"><i class="fas fa-barcode" style="font-size: 2.5rem; opacity: 0.4; margin-bottom: 0.5rem;"></i><br>يرجى اختيار صنف لعرض ملصق الباركود</div>';
        return;
    }

    let barHeight = 28;
    let barWidth = 1.3;
    if (size === 'thermal-50x25') {
        barHeight = 32;
        barWidth = 1.6;
    } else if (size === 'a4-sheet') {
        barHeight = 40;
        barWidth = 1.8;
    }

    const svgBarcode = BarcodeGenerator.renderSvg(currentItemData.barcode, {
        height: barHeight,
        barWidth: barWidth,
        showText: true
    });

    let singleLabelHtml = `
        <div class="barcode-label">
            ${showStore ? `<div class="label-store">${storeName}</div>` : ''}
            <div class="label-name">${currentItemData.name}</div>
            <div style="width: 100%; display: flex; justify-content: center; overflow: hidden;">
                ${svgBarcode}
            </div>
            ${showPrice ? `<div class="label-price">${parseFloat(currentItemData.price).toFixed(2)} ج.م</div>` : ''}
        </div>
    `;

    let html = '';
    for (let i = 0; i < copies; i++) {
        html += singleLabelHtml;
    }
    container.innerHTML = html;
}

// Initial render if item was selected by default
window.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('itemSelect');
    if (select.value) {
        onItemChange(select.value);
    }
});
</script>

