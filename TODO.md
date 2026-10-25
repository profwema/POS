# ✅ قائمة المهام التنفيذية الحية - TODO Master Checklist
> **نظام WAM Tech POS & ERP**  
> **مبدأ العمل:** إصلاح المشكلات البرمجية والأمنية أولاً ➔ تحسين أداء البيع ➔ استكمال الواجهات المتبقية.  
> **آخر تحديث:** 21 أكتوبر 2026

---

## 📍 المؤشر الحي الحالي (Current Active Task Pointer)
> 👈 **المهمة التالية مباشرة قيد التنفيذ:**  
> **[المسار الرابع - قسم المبيعات - بند 2]** استكمال وتحديث صفحة تعديل فاتورة المبيعات `edit_saleInvoice.php` وحل استعلامات N+1 وإضافة حماية CSRF وتحديث تسلسل الحقول.

---

## 🛡️ المسار 1: إصلاحات الأمان وحماية البيانات (Security & Data Integrity) - 100% مكتمل ✅

- [x] ترقية التشفير من `mcrypt` المهملة إلى `AES-256-CBC` عبر OpenSSL (`enc.php` و `Encryption.php`).
- [x] إغلاق ثغرة التخطي بعد التوجيه بإضافة `exit();` في `redirection.php`.
- [x] تحويل قاعدة البيانات بالكامل (85 جدولاً) إلى محرك `InnoDB` لدعم المعاملات الذرية.
- [x] إنشاء فهارس الاستعلام السريعة الأساسية في الجداول الرئيسية.
- [x] تأمين استدعاء الدوال الحساسة بفحص `method_exists()` في `controller.php`.
- [x] **1.1** تأمين الاستعلامات الحساسة ضد SQL Injection باستخدام Prepared Statements (`safe_query`) في تسجيل الدخول وفحص المستخدمين واسترجاع الأصناف ودوال الحذف.
- [x] **1.2** ترقية تشفير كلمات المرور من MD5 إلى `password_hash()` بخوارزمية Bcrypt مع دعم الترقية التلقائية والتحقق الآمن.
- [x] **1.3** إضافة رموز حماية النماذج ضد تزوير الطلبات (`CSRF Tokens`) وتفعيل `ajaxPrefilter` التلقائي لطلبات AJAX والتحقق المركزي.
- [x] **1.4** عزل بيانات الاتصال الحساسة في ملف تكوين آمن ومحمي (`config.php`) وحذف كلمات المرور وعناوين السيرفرات الخارجية الصريحة.
- [x] **1.5** تطبيق مصفوفة الصلاحيات والأدوار (RBAC) وفصل صلاحيات الكاشير عن المحاسب والمدير وحماية الصفحات الحساسة إدارياً ومحاسبياً وتقريرياً وتأمين عمليات الحذف والتعديل.
- [x] **1.6** إلغاء اللغة الإنجليزية وتوحيد واجهات الإدخال بنسبة 100% عربي (الخيار ب): تحييد أعمدة `_en` في الداتا بيز (`NULL DEFAULT ''`) لمنع الأخطاء غير المتوقعة، وربط المعالجات البرمجية لحفظ الاسم العربي في كلا الحقلين تلقائياً، وإلغاء كافة حقول `LANGUAGE_1` و `LANGUAGE_2` من النماذج.

---

## ⚙️ المسار 2: سلامة منطق الأعمال وقاعدة البيانات (Business Logic & ACID) - 100% مكتمل ✅

- [x] إصلاح الخطأ القاتل لورديات الكاشير في `pos.php`.
- [x] **2.1** تطبيق المعاملات الذرية (`START TRANSACTION` / `COMMIT` / `ROLLBACK`) في دورة المبيعات والمشتريات والقيود المحاسبية.
- [x] **2.2** حل مشكلة تصادم أرقام الفواتير والتزامن بين الكاشيرات (`Concurrency Fix`) بقفل الحصص `FOR UPDATE`.
- [x] **2.3** إلغاء مسح بيانات الجرد وحركة الأصناف والتقارير (`TRUNCATE TABLE`) في 7 ملفات واستبدالها بحذف شرطي عند POST فقط.
- [x] **2.4** تسريع احتساب أرصدة المخزون وحل مشكلة بطء استعلام حركة التاريخ في `getBalanceItem` بالتجميع المباشر في SQL ومعالجة أخطاء PHP 8.2.

---

## 🚀 المسار 3: تحسين شاشة الكاشير وتجربة البيع (POS UX & Performance) - 100% مكتمل ✅

- [x] بناء وتوحيد نظام التصميم الحديث `admin-pro.css` ودعم الوضع الليلي (Dark Mode).
- [x] إضافة اختصارات النظام العامة عبر `keyboard-shortcuts.js`.
- [x] **3.1** إزالة تأخير المسح (1.4 ثانية + 1 ثانية لكل صنف) في `js/pos.js` وتفعيل القراءة الفورية بالباركود.
- [x] **3.2** ربط اختصارات لوحة المفاتيح المباشرة لشاشة الكاشير (`F1`-`F10`, `Esc`) وشريط الأدوات السريع.
- [x] **3.3** فصل وحماية الطباعة الحرارية لتكون غير معطلة للواجهة (Asynchronous Non-blocking Printing) مع إشعار Toast أنيق.

---

## 📄 المسار 4: استكمال صفحات النظام المتبقية

### 1. إكمال قسم الملفات والبيانات الأساسية (مكتمل بالكامل ✅):
- [x] `add_item.php` - إضافة منتج/صنف (موحد عربي)
- [x] `add_employee.php` - إضافة موظف (موحد عربي)
- [x] `edit_item.php` - تعديل منتج (موحد عربي)
- [x] `edit_customer.php` - تعديل عميل (تصميم حديث وموحد عربي)
- [x] `edit_supp.php` - تعديل مورد (تصميم حديث وموحد عربي)
- [x] `edit_emp.php` - تعديل موظف (موحد عربي)
- [x] `edit_branch.php` - تعديل فرع (تصميم حديث وموحد عربي)
- [x] `add_services.php` & `edit_service.php` - خدمات (تصميم حديث وموحد عربي)
- [x] `settings.php` & `accounts.php` & `pos.php` - توحيد حقول الإدخال كاملة عربي

### 2. قسم المبيعات (9 صفحات - مكتمل ومختبر 100% ✅):
- [x] `add_saleInvoice.php` ⭐ الأهم (تم تسريع الاستعلامات N+1، وتوليد الرقم الآمن، وحماية CSRF)
- [x] `edit_saleInvoice.php` (معالجة انهيار PHP 8.2، وحماية CSRF، وتسريع الاستعلامات)
- [x] `saleInvoice.php` (تسريع استعلامات الفواتير عبر LEFT JOIN، وحماية الفلاتر، والتوافق مع PHP 8.2)
- [x] `quotations.php` (ربط العملاء والفروع بـ LEFT JOIN، وتصحيح استدعاء الحذف الآمن)
- [x] `add_qouta.php` (تسريع الاستعلامات N+1، وتوليد الرقم الآمن، وحماية CSRF)
- [x] `edit_qouta.php` (معالجة انهيار count(null) في PHP 8.2، وتخزين الأصناف والمخازن مؤقتاً، وحماية CSRF)
- [x] `salesRet.php` (ربط العملاء والفروع بـ LEFT JOIN، وتصحيح روابط الإضافة)
- [x] `add_salesRet.php` (تسريع استعلامات بنود الفاتورة N+1، وتوليد الرقم الآمن، وحماية CSRF)
- [x] `edit_salesRet.php` (إصلاح ثوابت ومصفوفات PHP 8.2، وتخزين الأصناف والمخازن مؤقتاً، وحماية CSRF)

### 3. قسم المشتريات (6 صفحات):
- [ ] `purchase.php` ⬅️ (نقطة البداية التالية)
- [ ] `add_purchase.php`
- [ ] `edit_purchase.php`
- [ ] `purchaseRet.php`
- [ ] `add_purchaseRet.php`
- [ ] `edit_purchaseRet.php`

### 4. قسم المخازن (9 صفحات):
- [ ] `item-movement.php`
- [ ] `store-inventory.php`
- [ ] `open-palance.php`
- [ ] `add_open_palance.php`
- [ ] `incoming-entry.php`
- [ ] `outgoing-entry.php`
- [ ] `stores-transfer.php`
- [ ] `add_storeTrans.php`
- [ ] `edit_storeTrans.php`

### 5. قسم الحسابات (13 صفحة):
- [ ] `accounts.php`
- [ ] `budget.php`
- [ ] `journal.php`
- [ ] `add_journal.php`
- [ ] `edit_journal.php`
- [ ] `account-statement.php`
- [ ] `expenditure.php`
- [ ] `new_expenditure.php`
- [ ] `edit_exp.php`
- [ ] `add_income.php`
- [ ] `edit_income.php`
- [ ] `add_outgo.php`
- [ ] `edit_outgo.php`

### 6. قسم التقارير (6 صفحات):
- [ ] `saleInvoice-report.php`
- [ ] `saleItem-report.php`
- [ ] `purchInvoice-report.php`
- [ ] `purchItem-erport.php`
- [ ] `detail-sales.php`
- [ ] `report-barcode.php`

### 7. قسم الإعدادات (7 صفحات):
- [ ] `settings.php`
- [ ] `quote_settings.php`
- [ ] `current_discounts.php`
- [ ] `add_discount.php`
- [ ] `edit_discount.php`
- [ ] `new_user.php`
- [ ] `updates.php`

### 8. قسم العمليات ونقطة البيع (6 صفحات):
- [ ] `pos.php` - نقطة البيع
- [ ] `services.php`
- [ ] `add_services.php`
- [ ] `edit_service.php`
- [ ] `delivery.php`
- [ ] `all_orders.php`
