<?php
/**
 * طباعة إيصال الكاشير الحراري 80mm - Thermal Receipt
 * WAM Tech POS & ERP
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */

require_once __DIR__ . '/config/config.php';

Auth::requireLogin();

$saleId = (int)($_GET['id'] ?? 0);
$sale = Sale::getFullInvoice($saleId);

if (!$sale) {
    die("الفاتورة غير موجودة.");
}

$storeName = Helpers::setting('store_name', APP_NAME);
$storePhone = Helpers::setting('store_phone', BRAND_PHONE);
$storeTax = Helpers::setting('store_tax_number', '123-456-789');
$receiptFooter = Helpers::setting('pos_receipt_footer', 'برمجة وتطوير: المبرمج (01002283766) almubarmig.com');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة مبيعات #<?= htmlspecialchars($sale['invoice_number']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', -apple-system, sans-serif;
        }

        body {
            background: #f1f5f9;
            color: #000;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .receipt-container {
            width: 78mm;
            background: #fff;
            padding: 12px 10px;
            border-radius: 4px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .receipt-logo {
            max-height: 42px;
            margin-bottom: 4px;
            filter: grayscale(100%);
        }

        .receipt-store-title {
            font-size: 14pt;
            font-weight: 900;
            line-height: 1.2;
        }

        .receipt-store-slogan {
            font-size: 8pt;
            color: #333;
            margin-bottom: 4px;
        }

        .receipt-meta {
            font-size: 8pt;
            line-height: 1.4;
            color: #222;
        }

        .receipt-info-row {
            display: flex;
            justify-content: space-between;
            font-size: 8.5pt;
            margin-bottom: 3px;
        }

        .receipt-items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 8.5pt;
        }

        .receipt-items-table th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 4px 0;
            text-align: right;
            font-weight: 800;
        }

        .receipt-items-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .totals-section {
            border-top: 1px dashed #000;
            padding-top: 6px;
            margin-top: 4px;
            font-size: 9pt;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .grand-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 13pt;
            font-weight: 900;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            margin: 6px 0;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 8px;
            font-size: 8pt;
            line-height: 1.4;
        }

        .no-print-bar {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }

        .btn {
            background: #0284c7;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            text-decoration: none;
            font-size: 14px;
        }

        /* PRINT MEDIA QUERY */
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                width: 100% !important;
                padding: 0 !important;
            }
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button onclick="window.print()" class="btn" style="background: #10b981;">🖨️ طباعة الإيصال</button>
        <a href="<?= BASE_URL ?>pos.php" class="btn" style="background: #475569;">العودة لنقطة البيع</a>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <img src="<?= BASE_URL ?>assets/brand/logo-icon.png" alt="Logo" class="receipt-logo">
            <div class="receipt-store-title"><?= htmlspecialchars($storeName) ?></div>
            <div class="receipt-store-slogan"><?= BRAND_SLOGAN ?></div>
            <div class="receipt-meta">
                <div>هاتف: <?= htmlspecialchars($storePhone) ?></div>
                <div>الرقم الضريبي: <?= htmlspecialchars($storeTax) ?></div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div style="margin-bottom: 6px;">
            <div class="receipt-info-row">
                <span>رقم الفاتورة:</span>
                <strong style="font-family: monospace; font-size: 9.5pt;"><?= htmlspecialchars($sale['invoice_number']) ?></strong>
            </div>
            <div class="receipt-info-row">
                <span>التاريخ والوقت:</span>
                <span><?= date('Y-m-d h:i A', strtotime($sale['created_at'])) ?></span>
            </div>
            <div class="receipt-info-row">
                <span>الكاشير:</span>
                <span><?= htmlspecialchars($sale['cashier_name'] ?? 'كاشير') ?></span>
            </div>
            <?php if (!empty($sale['shift_number'])): ?>
            <div class="receipt-info-row">
                <span>رقم الوردية:</span>
                <span><?= htmlspecialchars($sale['shift_number']) ?></span>
            </div>
            <?php endif; ?>
            <div class="receipt-info-row">
                <span>العميل:</span>
                <span><?= htmlspecialchars($sale['customer_name'] ?? 'عميل نقدي') ?></span>
            </div>
        </div>

        <!-- Items Table -->
        <table class="receipt-items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">الصنف</th>
                    <th style="width: 20%; text-align: center;">الكمية</th>
                    <th style="width: 15%; text-align: left;">السعر</th>
                    <th style="width: 15%; text-align: left;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sale['items'] as $item): ?>
                <tr>
                    <td>
                        <div style="font-weight: 700;"><?= htmlspecialchars($item['item_name']) ?></div>
                    </td>
                    <td style="text-align: center;">
                        <?= (float)$item['quantity'] ?> <?= $item['unit_symbol'] ?>
                    </td>
                    <td style="text-align: left;">
                        <?= number_format($item['unit_price'], 2) ?>
                    </td>
                    <td style="text-align: left; font-weight: 700;">
                        <?= number_format($item['total_price'], 2) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <span>الإجمالي قبل الضريبة:</span>
                <span><?= number_format($sale['subtotal'], 2) ?> ج.م</span>
            </div>

            <?php if ($sale['tax_amount'] > 0): ?>
            <div class="total-row">
                <span>ضريبة القيمة المضافة (14%):</span>
                <span><?= number_format($sale['tax_amount'], 2) ?> ج.م</span>
            </div>
            <?php endif; ?>

            <?php if ($sale['discount_amount'] > 0): ?>
            <div class="total-row" style="color: #b91c1c;">
                <span>الخصم الممنوح:</span>
                <span>-<?= number_format($sale['discount_amount'], 2) ?> ج.م</span>
            </div>
            <?php endif; ?>

            <div class="grand-total-row">
                <span>الصافي المطلوب:</span>
                <span><?= number_format($sale['grand_total'], 2) ?> ج.م</span>
            </div>

            <!-- Payments details -->
            <div style="font-size: 8pt; margin-top: 4px;">
                <?php foreach ($sale['payments'] as $p): 
                    $methodName = match($p['method']) {
                        'cash'     => 'نقداً (كاش)',
                        'card'     => 'بطاقة / فيزا POS',
                        'instapay' => 'إنستاباي (InstaPay IPN)',
                        'wallet'   => 'محفظة ذكية (Vodafone/Orange/Etisalat)',
                        'credit'   => 'آجل على الحساب',
                        default    => $p['method']
                    };
                ?>
                <div class="total-row">
                    <span>مدفوع <?= $methodName ?>:</span>
                    <strong><?= number_format($p['amount'], 2) ?> ج.م</strong>
                </div>
                <?php endforeach; ?>

                <?php 
                    $totalPaid = array_sum(array_column($sale['payments'], 'amount'));
                    $change = max(0.0, $totalPaid - (float)$sale['grand_total']);
                    if ($change > 0):
                ?>
                <div class="total-row" style="font-weight: 800; font-size: 9pt; border-top: 1px dotted #000; padding-top: 2px;">
                    <span>المتبقي للعميل (الباقي):</span>
                    <span><?= number_format($change, 2) ?> ج.م</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <div style="font-weight: 700; margin-bottom: 2px;">شكراً لتعاملكم معنا، ونسعد بزيارتكم دائماً!</div>
            <div style="font-size: 7pt; color: #444; margin-top: 4px;"><?= htmlspecialchars($receiptFooter) ?></div>
        </div>
    </div>

    <script>
        // Auto print when loaded
        window.addEventListener('load', () => {
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>

