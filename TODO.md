# ✅ قائمة المهام التنفيذية الحية - TODO Master Checklist
> **نظام WAM Tech POS & ERP**  
> **مبدأ العمل:** إصلاح المشكلات البرمجية والأمنية أولاً ➔ تحسين أداء البيع ➔ استكمال الواجهات المتبقية.  
> **آخر تحديث:** 21 أكتوبر 2026

---

## 📍 المؤشر الحي الحالي (Current Active Task Pointer)
> 👈 **المهمة التالية مباشرة قيد التنفيذ:**  
> **[المسار الثاني - بند 2.1]** تطبيق المعاملات الذرية (`START TRANSACTION` / `COMMIT` / `ROLLBACK`) في دورة المبيعات والمشتريات والقيود المحاسبية لمنع تشتت البيانات وضمان التكامل المحاسبي (ACID).

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

## ⚙️ المسار 2: سلامة منطق الأعمال وقاعدة البيانات (Business Logic & ACID)

- [x] إصلاح الخطأ القاتل لورديات الكاشير في `pos.php`.
- [ ] **2.1** تطبيق المعاملات الذرية (`START TRANSACTION` / `COMMIT` / `ROLLBACK`) في دورة المبيعات والمشتريات والقيود المحاسبية.
- [ ] **2.2** حل مشكلة تصادم أرقام الفواتير والتزامن بين الكاشيرات (`Concurrency Fix`).
- [ ] **2.3** إلغاء مسح بيانات الجرد وحركة الأصناف (`TRUNCATE TABLE`) في `item-movement.php` و `store-inventory.php` واستبدالها بجداول مؤقتة.
- [ ] **2.4** تسريع احتساب أرصدة المخزون وحل مشكلة بطء استعلام حركة التاريخ في `getBalanceItem`.

---

## 🚀 المسار 3: تحسين شاشة الكاشير وتجربة البيع (POS UX & Performance)

- [x] بناء وتوحيد نظام التصميم الحديث `admin-pro.css` ودعم الوضع الليلي (Dark Mode).
- [x] إضافة اختصارات النظام العامة عبر `keyboard-shortcuts.js`.
- [ ] **3.1** إزالة تأخير المسح (1 ثانية) في `js/pos.js` وتفعيل القراءة الفورية بالباركود.
- [ ] **3.2** ربط اختصارات لوحة المفاتيح المباشرة لشاشة الكاشير (`F1`-`F12`, `Enter`, `Esc`).
- [ ] **3.3** فصل الطباعة الحرارية لتكون غير معطلة للواجهة (Asynchronous Printing).

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

### 2. قسم المبيعات (9 صفحات - الأولوية العالية):
- [ ] `add_saleInvoice.php` ⭐ الأهم
- [ ] `edit_saleInvoice.php`
- [ ] `saleInvoice.php`
- [ ] `quotations.php`
- [ ] `add_qouta.php`
- [ ] `edit_qouta.php`
- [ ] `salesRet.php`
- [ ] `add_salesRet.php`
- [ ] `edit_salesRet.php`

### 3. قسم المشتريات (6 صفحات):
- [ ] `purchase.php`
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
