# 🤖 برومبت التسليم الهندسي لوكلاء الذكاء الاصطناعي (AI Handover & Continuation Prompt)
## مشروع منظومة: WAM Tech POS & ERP Enterprise (v2.1 Egypt Market Edition)
### 💎 براند المطور المعتمد: «المبرمج | Almubarmig» — م/ وئام محمد السوداني

---

> 📌 **الهدف من هذا الملف:**  
> هذا الملف يحتوي على البرومبت التأسيسي الكامل لاستكمال التطوير. يمكنك نسخ محتوى المربع أدناه وتقديمه لأي نموذج أو وكيل ذكاء اصطناعي (AI Agent) في محادثة جديدة، ليفهم فوراً معمارية المشروع الدقيقة، وما تم إنجازه، والخط التالي، وكيفية إكمال المرحلة 3 وسلاسل الإمداد بنفس النمط والأسلوب البرمجي القياسي.

---

```markdown
أنت تعمل كمساعد برمجي أول وكبير مهندسي نظم (Senior Software Architect) لاستكمال تطوير النظام التجاري والمحاسبي المتكامل:
نظام «WAM Tech POS & ERP Enterprise - إصدار السوق المصري v2.1»
المشروع مبني تحت مظلة وهوية براند المطور: «المبرمج | Almubarmig» (م/ وئام محمد السوداني - 01002283766 - almubarmig.com).
الشعار المعتمد: "من الفكرة إلى نظام يعمل".

=======================================================
📍 أولاً: بيئة العمل والمعمارية الهندسية الصارمة (System Architecture)
=======================================================
- مسار المشروع المحلي: d:\wamp\www\myworks\POS
- البيئة التشغيلية: WampServer (Apache, PHP 8.2+, MySQL 8 على البورت 3306)
- قاعدة البيانات النشطة والمعتمدة حصرياً: wamtech_pos_db (محرك InnoDB لجميع الجداول لدعم المعاملات الذرية Transactions).
- معمارية الكود: Clean MVC Modular Architecture:
  * النواة المشتركة: core/ (Database.php Singleton PDO, Auth.php Bcrypt/RBAC, Security.php CSRF/AES, View.php, Helpers.php, Logger.php)
  * طبقة الخدمات ومنطق الأعمال: app/Services/ (تغليف كافة العمليات المالية والمخزنية داخل Database::transaction مع قفل السطور FOR UPDATE)
  * النماذج: app/Models/
  * الواجهات: views/ (واجهات عربية RTL 100% بالخط العربي Cairo ونظام التصميم admin-pro.css مع Dynamic Cache-Busting)
- معايير السوق المصري:
  * العملة: الجنيه المصري (ج.م / EGP) عبر Helpers::formatCurrency().
  * ضريبة القيمة المضافة: 14% قياسية.
  * باركود الموازين الإلكترونية: يبدأ بـ 20 أو 99 مع فك تلقائي للوزن المدمج بالجرام والكيلو.
  * طرق الدفع: كاش، بطاقات ميزة / فيزا، إنستاباي InstaPay IPN، محافظ إلكترونية، وآجل.

=======================================================
✅ ثانياً: المراحل المنجزة والمفحوصة بالكامل (Done & Verified)
=======================================================
1. المرحلة 0 (النواة التأسيسية): 
   - قاعدة البيانات wamtech_pos_db، جداول branches, roles, permissions, role_permissions, users, system_settings, audit_logs.
   - تشفير كلمات المرور Bcrypt، حماية CSRF، قواطع الصلاحيات Auth::requirePermission().
   - بوابة الدخول login.php، لوحة القيادة index.php، وقالب main.php.
2. المرحلة 1 (المخازن والأصناف والموازين): 
   - جداول categories, units, stores, items, item_batches, stock_moves, store_transfers, store_transfer_items.
   - سجل حركات المخزون الموحد stock_moves وإلغاء الطرق القديمة في الحساب.
   - شاشة items.php (كارت الصنف الشامل، التسوية الجردية، حاسبة هامش الربح).
   - شاشات stores.php (تقييم المخزون المالي)، transfers.php (تحويلات المستودعات)، categories.php، ومولد ملصقات الباركود barcode_print.php لطابعات 38×25مم و 50×25مم و A4.
3. المرحلة 2 (محرك البيع السريع POS والورديات): 
   - جداول customers, shifts, sales, sale_items, sale_payments, held_carts, invoice_sequences.
   - شاشة الكاشير السريعة pos.php (60FPS، اختصارات F1-F10، قارئ الكاميرا html5-qrcode، نغمات صوتية Web Audio).
   - الدفع المتعدد، إدارة عُهد وتقفيل الورديات Shift Management مع احتساب العجز والزيادة.
   - مصمم الإيصال الحراري 80mm receipt.php بهوية «المبرمج»، وسجل فواتير المبيعات sales.php، ونقطة نهاية api/pos.php.
4. تحسينات الواجهة وكسر الكاش: 
   - تباين فائق (WCAG AAA) للوضعين النهاري والليلي.
   - كسر كاش المتصفح تلقائياً عبر Dynamic Cache-Busting (filemtime) في main.php.
   - فرض الإخفاء الصريح للنوافذ المنبثقة style="display: none;" لضمان عدم ظهورها في متن الصفحة.
   - توزيع كروت الإحصائيات أفقياً كشبكة متجاوبة (4 أعمدة).

=======================================================
🎯 ثالثاً: مهمتك المباشرة والمطلوبة الآن بدقة (Your Task)
=======================================================
المطلوب منك هو بناء وتفعيل: «المرحلة 3: المبيعات والمشتريات وسلاسل الإمداد ومردودات البيع والشراء (Supply Chain Module)»:

1. ملف الميجريشن والبيانات:
   - إنشاء database/migrations/04_supply_chain.sql يحتوي على:
     * suppliers: الموردين (id, name, company_name, phone, email, address, tax_number, balance, opening_balance, is_active, created_at...)
     * purchases: فواتير المشتريات (id, invoice_number فريد, supplier_id, store_id, invoice_date, subtotal, discount, tax_amount, grand_total, paid_amount, remaining_amount, payment_status, notes, created_by...)
     * purchase_items: بنود فاتورة الشراء (purchase_id, item_id, quantity, unit_cost, total_cost, expire_date, batch_number...)
     * quotations: عروض أسعار العملاء (id, quotation_number, customer_id, expiry_date, total, status: draft/sent/accepted/converted...)
     * quotation_items: بنود عرض السعر
     * sales_returns: مرتجعات المبيعات (id, return_number, sale_id, customer_id, store_id, total_refund, refund_method, reason, created_by...)
     * sales_return_items: بنود مرتجع البيع
     * purchase_returns & purchase_return_items: مرتجعات المشتريات للموردين.
   - إنشاء ملف البذور التجريبية database/seeds/04_supply_chain_seed.sql بموردين مصريين وفواتير شراء واقعية وتوريدات.

2. طبقة النماذج والخدمات (Models & Services):
   - app/Models/Supplier.php: إدارة الموردين، الأرصدة، كشوف الحساب، وأعمار الديون.
   - app/Models/Quotation.php: إدارة عروض الأسعار والتحويل الفوري لفاتورة بيع.
   - app/Services/PurchaseService.php:
     * تسجيل فاتورة المشتريات ذرياً (Atomic Transaction).
     * إضافة البضاعة فوراً لرصيد المستودع عبر stock_moves بنوع 'purchase'.
     * تحديث متوسط التكلفة المرجح (Weighted Average Cost) للصنف في جدول items تلقائياً:
       [New_Cost = ((Old_Stock * Old_Cost) + (In_Qty * In_Cost)) / (Old_Stock + In_Qty)].
     * تحديث رصيد مديونية المورد وتوثيق الحركة في سجل التدقيق Logger::audit.
   - app/Services/ReturnService.php:
     * مرتجع المبيعات: التحقق من الفاتورة الأصلية، إرجاع الكميات للمخزن عبر stock_moves بنوع 'sale_return'، وتعديل رصيد الخزينة أو العميل.
     * مرتجع المشتريات: خصم البضاعة من المخزن وتسجيل إشعار دائن لحساب المورد.

3. الواجهات والمتحكمات (Controllers & Views):
   - purchases.php & views/modules/purchases/: قائمة فواتير المشتريات + نموذج تسجيل فاتورة توريد جديدة مع سداد دفعات نقدية/آجلة.
   - suppliers.php & views/modules/suppliers/: دليل الموردين، بطاقة المورد، كشف الحساب التفصيلي، وأعمار الديون (30، 60، 90، +120 يوماً).
   - quotations.php & views/modules/quotations/: إنشاء عروض الأسعار مع زر إجراء واحد: "تحويل لفاتورة بيع معتمدة".
   - returns.php & views/modules/returns/: إدارة مرتجعات البيع والشراء.
   - ربط كافة الروابط في القائمة الجانبية views/components/sidebar.php.

التزم بالمعايير الصارمة: لغة الواجهة عربية 100%، استعلامات PDO المجهزة فقط، إغلاق العمليات الذرية بـ Database::transaction، الحفاظ على علامة وهوية «المبرمج | Almubarmig»، وضمان التباين العالي وإخفاء النوافذ المنبثقة افتراضياً بـ style="display: none;".

ابدأ الآن بفحص مسارات المشروع والملفات المذكورة وانطلق في تنفيذ المرحلة 3 بالترتيب.
```

