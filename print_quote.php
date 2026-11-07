<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = defined('LANG') ? LANG : ($_SESSION['lang'] ?? 'ar');
$storeid  = (int)($_SESSION['storeid'] ?? 1);
$currency = defined('CURRENCY_NAME') ? CURRENCY_NAME : 'ج.م';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die("معرف عرض السعر غير صحيح.");
}

// Fetch quotation
$queryQuote = "SELECT q.*, 
                      c.name_ar AS cust_name_ar, c.name_en AS cust_name_en, c.phone AS cust_phone, c.vat_no AS cust_vat, c.address AS cust_address,
                      b.name_ar AS branch_name_ar, b.name_en AS branch_name_en, b.address AS branch_address
               FROM quote q
               LEFT JOIN customers c ON q.supplier = c.id AND (c.storeid='$storeid' OR c.storeid='0')
               LEFT JOIN branches b ON q.branch = b.id AND (b.storeid='$storeid' OR b.storeid='0')
               WHERE q.id = '$id' AND (q.storeid='$storeid' OR q.storeid='0')
               LIMIT 1";
$resQuote = mysqli_query($adController->MySQL, $queryQuote);
$quote = $resQuote ? mysqli_fetch_assoc($resQuote) : null;
if (!$quote) {
    die("لم يتم العثور على عرض السعر المطلوب.");
}

// Fetch main settings for company header
$qSet = "SELECT * FROM main_settings LIMIT 1";
$rSet = mysqli_query($adController->MySQL, $qSet);
$settings = $rSet ? mysqli_fetch_assoc($rSet) : [];

// Fetch print preferences from quote_settings
$qPref = "SELECT * FROM quote_settings WHERE storeid='$storeid' LIMIT 1";
$rPref = mysqli_query($adController->MySQL, $qPref);
$pref  = $rPref ? mysqli_fetch_assoc($rPref) : [];
$showBarcode  = (int)($pref['barcode'] ?? 1);
$showUnit     = (int)($pref['unit'] ?? 1);
$showDisc     = (int)($pref['disc'] ?? 1);
$showImg      = (int)($pref['img'] ?? 0);
$showVatRate  = (int)($pref['vat_rate'] ?? 1);
$showVatValue = (int)($pref['vat_value'] ?? 1);

// Fetch items
$queryItems = "SELECT qi.*, i.name_ar, i.name_en, i.barcode, i.image, u.name_ar AS unit_ar, u.name_en AS unit_en
               FROM quote_items qi
               LEFT JOIN items i ON qi.itemid = i.id
               LEFT JOIN units u ON i.unit = u.id
               WHERE qi.purchase_id = '$id' AND (qi.storeid='$storeid' OR qi.storeid='0')
               ORDER BY qi.id ASC";
$resItems = mysqli_query($adController->MySQL, $queryItems);

$compName = ($language == 'ar' ? ($settings['name_ar'] ?? '') : ($settings['name_en'] ?? '')) ?: ($settings['name_ar'] ?? 'شركة ومؤسسة تجارية');
$compVat  = $settings['tax_number'] ?? ($settings['vat_no'] ?? '');
$compTel  = $settings['phone'] ?? '';
$compAddr = $settings['address'] ?? '';
$logo     = !empty($settings['logo']) && file_exists($settings['logo']) ? $settings['logo'] : 'img/logo.png';

$custName = ($language == 'ar' ? ($quote['cust_name_ar'] ?? '') : ($quote['cust_name_en'] ?? '')) ?: ($quote['cust_name_ar'] ?? 'عميل محترم');
$invNo    = $quote['invoicenumber'] ?? '';
$invDate  = substr($quote['purchase_date'] ?? '', 0, 10);
$invDoc   = $quote['supplier_invoice'] ?? '---';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>عرض سعر #<?=htmlspecialchars($invNo)?> - <?=htmlspecialchars($compName)?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }
        .quote-sheet {
            background: #fff;
            max-width: 850px;
            margin: 0 auto;
            padding: 35px 45px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
        }
        .company-title {
            font-size: 22px;
            font-weight: 800;
            color: #1e3a8a;
            margin-bottom: 4px;
        }
        .company-subtitle {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }
        .quote-badge-box {
            text-align: left;
        }
        .quote-main-title {
            font-size: 24px;
            font-weight: 800;
            color: #2563eb;
            margin-bottom: 6px;
        }
        .info-grid {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px 20px;
        }
        .info-col {
            flex: 1;
            font-size: 13px;
            line-height: 1.8;
        }
        .info-col strong {
            color: #334155;
            min-width: 90px;
            display: inline-block;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 13px;
        }
        .items-table th {
            background: #2563eb;
            color: #fff;
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #1d4ed8;
        }
        .items-table td {
            padding: 9px 8px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .totals-table {
            width: 320px;
            margin-right: auto;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 25px;
        }
        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .totals-table tr.grand-total {
            background: #eff6ff;
            font-size: 15px;
            font-weight: 800;
            color: #1e3a8a;
            border-top: 2px solid #2563eb;
        }
        .print-btn-bar {
            max-width: 850px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print {
            background: #2563eb;
            color: #fff;
        }
        .btn-back {
            background: #e2e8f0;
            color: #334155;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .print-btn-bar {
                display: none !important;
            }
            .quote-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <a href="quotations.php" class="btn btn-back">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
        <button onclick="window.print()" class="btn btn-print">
            <i class="fas fa-print"></i> طباعة عرض السعر A4
        </button>
    </div>

    <div class="quote-sheet">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="company-title"><?=htmlspecialchars($compName)?></div>
                    <div class="company-subtitle">
                        <?php if ($compVat): ?>الرقم الضريبي: <strong><?=htmlspecialchars($compVat)?></strong><br><?php endif; ?>
                        <?php if ($compTel): ?>الهاتف / الجوال: <?=htmlspecialchars($compTel)?><br><?php endif; ?>
                        <?php if ($compAddr): ?>العنوان: <?=htmlspecialchars($compAddr)?><?php endif; ?>
                    </div>
                </td>
                <td style="width: 40%; vertical-align: top; text-align: left;">
                    <div class="quote-main-title">عرض سعر</div>
                    <div style="font-size: 14px; font-weight: 700; color: #475569;">#<?=htmlspecialchars($invNo)?></div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">تاريخ الإصدار: <?=htmlspecialchars($invDate)?></div>
                </td>
            </tr>
        </table>

        <!-- Customer & Details Grid -->
        <div class="info-grid">
            <div class="info-col">
                <div><strong>السادة / العميل:</strong> <?=htmlspecialchars($custName)?></div>
                <?php if (!empty($quote['cust_phone'])): ?><div><strong>رقم الاتصال:</strong> <?=htmlspecialchars($quote['cust_phone'])?></div><?php endif; ?>
                <?php if (!empty($quote['cust_vat'])): ?><div><strong>الرقم الضريبي:</strong> <?=htmlspecialchars($quote['cust_vat'])?></div><?php endif; ?>
            </div>
            <div class="info-col">
                <div><strong>الفرع المصدر:</strong> <?=htmlspecialchars($quote['branch_name_ar'] ?: 'الفرع الرئيسي')?></div>
                <div><strong>رقم المرجع:</strong> <?=htmlspecialchars($invDoc)?></div>
                <div><strong>صلاحية العرض:</strong> 15 يوماً من تاريخه</div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <?php if ($showBarcode): ?><th style="width: 14%;">الباركود</th><?php endif; ?>
                    <th style="width: 30%; text-align: right;">الصنف / البيان</th>
                    <?php if ($showUnit): ?><th style="width: 10%;">الوحدة</th><?php endif; ?>
                    <th style="width: 9%;">الكمية</th>
                    <th style="width: 11%;">سعر الوحدة</th>
                    <th style="width: 11%;">قبل الضريبة</th>
                    <?php if ($showVatRate): ?><th style="width: 8%;">الضريبة</th><?php endif; ?>
                    <?php if ($showVatValue): ?><th style="width: 10%;">مبلغ الضريبة</th><?php endif; ?>
                    <th style="width: 12%;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $itemIndex = 0;
                $grossSum = 0;
                $vatSum   = 0;
                $netSum   = 0;

                if ($resItems) {
                    while ($it = mysqli_fetch_assoc($resItems)) {
                        $itemIndex++;
                        $iName   = ($language == 'ar' ? $it['name_ar'] : $it['name_en']) ?: $it['name_ar'];
                        $bcode   = $it['barcode'] ?: '---';
                        $uName   = ($language == 'ar' ? ($it['unit_ar'] ?? '') : ($it['unit_en'] ?? '')) ?: 'قطعة';
                        $qty     = (float)($it['quantity'] ?? 1);
                        $price   = (float)($it['cost'] ?? 0);
                        $disc    = (float)($it['discount'] ?? 0);
                        $taxRate = (float)($it['tax'] ?? 14);

                        $rowGross = $qty * $price;
                        $discVal  = $rowGross * ($disc / 100);
                        $subtotal = max(0, $rowGross - $discVal);
                        $vatVal   = $subtotal * ($taxRate / 100);
                        $rowNet   = $subtotal + $vatVal;

                        $grossSum += $subtotal;
                        $vatSum   += $vatVal;
                        $netSum   += $rowNet;
                ?>
                <tr>
                    <td><?=$itemIndex?></td>
                    <?php if ($showBarcode): ?><td><code><?=htmlspecialchars($bcode)?></code></td><?php endif; ?>
                    <td style="text-align: right; font-weight: 600;"><?=htmlspecialchars($iName)?></td>
                    <?php if ($showUnit): ?><td><?=htmlspecialchars($uName)?></td><?php endif; ?>
                    <td><strong><?=flout_format($qty)?></strong></td>
                    <td><?=flout_format($price)?></td>
                    <td><?=flout_format($subtotal)?></td>
                    <?php if ($showVatRate): ?><td><?=$taxRate?>%</td><?php endif; ?>
                    <?php if ($showVatValue): ?><td><?=flout_format($vatVal)?></td><?php endif; ?>
                    <td style="font-weight: 700; color: #1e3a8a;"><?=flout_format($rowNet)?></td>
                </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>

        <!-- Totals Table -->
        <table class="totals-table">
            <tr>
                <td style="color: #64748b;">الإجمالي الخاضع للضريبة:</td>
                <td style="text-align: left; font-weight: 700;"><?=flout_format($grossSum)?> <?=$currency?></td>
            </tr>
            <tr>
                <td style="color: #64748b;">ضريبة القيمة المضافة 14%:</td>
                <td style="text-align: left; font-weight: 700; color: #ef4444;"><?=flout_format($vatSum)?> <?=$currency?></td>
            </tr>
            <tr class="grand-total">
                <td>الصافي الإجمالي لعرض السعر:</td>
                <td style="text-align: left;"><?=flout_format($netSum)?> <?=$currency?></td>
            </tr>
        </table>

        <!-- Terms and Signatures -->
        <div style="margin-top: 35px; border-top: 1px dashed #cbd5e1; padding-top: 15px; font-size: 11px; color: #64748b; line-height: 1.8;">
            <div>* الأسعار شاملة ضريبة القيمة المضافة طبقاً للأنظمة واللوائح الضريبية.</div>
            <div>* عرض السعر ساري لمدة 15 يوماً من تاريخ التحرير، وقابل للتعديل بعد انقضاء المدة.</div>
        </div>

        <table style="width: 100%; margin-top: 40px; text-align: center; font-size: 13px; font-weight: 700;">
            <tr>
                <td style="width: 50%;">إعداد المسؤول / المبيعات:<br><br>......................................</td>
                <td style="width: 50%;">اعتماد وقبول العميل:<br><br>......................................</td>
            </tr>
        </table>
    </div>

</body>
</html>

