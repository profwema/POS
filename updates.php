<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = $_SESSION['lang'] ?? 'ar';
$storeid  = intval($_SESSION['storeid'] ?? 1);

$query    = "SELECT * FROM version LIMIT 1";
$res      = mysqli_query($adController->MySQL, $query);
$data     = $res ? mysqli_fetch_assoc($res) : null;

$version  = $data['version'] ?? '2.0.0 Enterprise';
$serverId = $data['serverId'] ?? 'WAM-PRO-2026';
$phpVer   = PHP_VERSION;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>WAM Tech Soft - تحديثات وترخيص النظام</title>
    <?php require_once("script_php_variables.php"); ?>
    <?php require_once("header.php"); ?>	
    <style>
        .version-hero {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 12px;
            padding: 30px;
            color: #fff;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .version-info h1 {
            font-size: 24px;
            margin: 0 0 8px 0;
            color: #fff;
            font-weight: bold;
        }
        .version-badge {
            display: inline-block;
            background: #10b981;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }
        .sys-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        .sys-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .sys-card .icon {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .sys-card .title {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 6px;
        }
        .sys-card .val {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
        }
    </style>
</head>
<body>
    <?php require_once("header_top.php"); ?>
    
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php"); ?>
            <div id="content" class="span10">
                <div class="version-hero">
                    <div class="version-info">
                        <h1>نظام WAM Tech POS & ERP المتكامل</h1>
                        <p style="color:#94a3b8; margin:0 0 10px 0;">المنظومة السحابية والمحلية المتطورة لإدارة نقاط البيع والمخازن والمالية</p>
                        <span class="version-badge">الإصدار: <?=htmlspecialchars($version)?></span>
                    </div>
                    <div>
                        <button class="btn btn-success" id="updareCheck" style="padding:10px 20px; font-weight:bold;">
                            <i class="halflings-icon white refresh"></i> التحقق من التحديثات السحابية
                        </button>
                    </div>
                </div>

                <div class="sys-cards">
                    <div class="sys-card" style="border-top:3px solid #10b981;">
                        <div class="title">بيئة تشغيل الخادم (PHP)</div>
                        <div class="val" style="direction:ltr;">PHP <?=htmlspecialchars($phpVer)?></div>
                    </div>
                    <div class="sys-card" style="border-top:3px solid #3b82f6;">
                        <div class="title">محرك قواعد البيانات</div>
                        <div class="val">MySQL (InnoDB ACID)</div>
                    </div>
                    <div class="sys-card" style="border-top:3px solid #6366f1;">
                        <div class="title">معرّف الخادم والترخيص</div>
                        <div class="val" style="direction:ltr;"><?=htmlspecialchars($serverId)?></div>
                    </div>
                    <div class="sys-card" style="border-top:3px solid #f59e0b;">
                        <div class="title">حالة الأمان والحماية</div>
                        <div class="val" style="color:#10b981;">Bcrypt + CSRF Active</div>
                    </div>
                </div>

                <div class="box span12" style="margin-right:0;">
                    <div class="box-header" data-original-title>
                        <h2>
                            <i class="halflings-icon info-sign"></i>
                            <span class="break"></span>
                            سجل التحديثات وملاحظات الإصدار
                        </h2>
                    </div>
                    <div class="box-content">
                        <div style="line-height:1.8; color:#334155;">
                            <h4 style="color:#1e293b; font-weight:bold; margin-bottom:10px;">أبرز مميزات وتحسينات الإصدار v2.0 Enterprise:</h4>
                            <ul>
                                <li><strong>ترقية التوافقية الكاملة مع PHP 8.2:</strong> إزالة كافة الأخطاء والتحذيرات السابقة وتأمين الاستدعاءات الصارمة.</li>
                                <li><strong>محرك الحسابات والمعاملات الذرية (ACID):</strong> حماية الفواتير وقيود اليومية والمخزون من التلف وفقدان البيانات.</li>
                                <li><strong>الحماية المتقدمة ضد الهجمات:</strong> تأمين النماذج بـ CSRF Tokens وتشفير كلمات المرور بخوارزمية Bcrypt الحديثة والاستعلامات الآمنة.</li>
                                <li><strong>تسريع فائق لشاشة الكاشير ونقاط البيع:</strong> إزالة التأخير الزمني في قراءة الباركود، ودعم اختصارات F1-F10 السريعة.</li>
                                <li><strong>واجهة عربية موحدة 100% (RTL):</strong> وتصميم متجاوب وسلس يلائم مختلف مقاسات الشاشات وأجهزة نقاط البيع.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix"></div>
    
    <?php require_once("footer.php"); ?>
</body>
</html>
