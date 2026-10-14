# تقرير التدقيق المعماري والبرمجي الشامل (System Architecture & Codebase Audit)
**المشروع:** نظام WAM Tech (ERP + POS)  
**الدور:** Lead System Architect & Senior Systems Analyst  
**التاريخ:** أكتوبر 2026  
**طبيعة المهمة:** تدقيق واستكشاف استراتيجي وتقني (Audit & Discovery Mode) دون تعديل على الكود.

---

## 1. الملخص التنفيذي (Executive Summary)

يمثل نظام **WAM Tech POS & ERP** منصة أعمال متكاملة مصممة لإدارة نقاط البيع، المخازن، المشتريات، المبيعات، والحسابات المالية العامة. بعد الفحص المعمق لكامل الملفات والمجلدات وقاعدة البيانات ومنطق الأعمال، تبيّن أن النظام يمتلك **نواة وظيفية غنية بالأفكار المحاسبية والتشغيلية**، إلا أنه يعاني من **تراكم ديون تقنية حرجة للغاية (Critical Technical Debt)** وثغرات أمنية هيكلية ونواقص معمارية تجعل النظام هشاً أمام ضغط العمل المتزامن، ومعرضاً لاختراقات أمنية عالية الخطورة وانهيارات في تناسق البيانات المالية والمخزنية.

### مصفوفة تقييم الحالة العامة للمشروع:
| المحور | التقييم (من 10) | الحالة العامة | أبرز المخاطر |
| :--- | :---: | :---: | :--- |
| **المعمارية وهيكلية الكود** | 3.0 / 10 | 🔴 حرج جداً | نمط Spaghetti Monolithic، تحكم إلهي (God Class)، تكرار هائل. |
| **قاعدة البيانات ونموذج البيانات** | 2.5 / 10 | 🔴 حرج جداً | محرك MyISAM، انعدام المعاملات (ACID) والمفاتيح الأجنبية (FKs). |
| **الأمان والصلاحيات** | 2.0 / 10 | 🔴 حرج جداً | SQL Injection واسع، تجاوز المصادقة (EAR)، غياب كامل لـ RBAC و CSRF. |
| **منطق الأعمال وتدفق العمليات** | 4.5 / 10 | 🟠 متوسط | حساب مخزون بطيء جداً، تداخل تقارير عبر Truncate، أخطاء في الوردية. |
| **واجهة وتجربة المستخدم (POS UX)** | 4.0 / 10 | 🟠 متوسط | شاشة كاشير بطيئة، غياب الاختصارات (Hotkeys)، طباعة حرارية بطيئة ومتزامنة. |

---

## المحور الأول: نظرة عامة وتوصيف البنية الحالية (System Architecture)

### 1.1 المكونات التقنية والمكتبات (Tech Stack & Versions)
- **لغة الخادم (Backend):** 
  - PHP أصلي إجرائي وهجين (Procedural & Object-Oriented Hybrid).
  - الكود مكتوب بأسلوب متوافق مع إصدارات PHP القديمة (PHP 5.4 - 7.4)؛ يعتمد على وظائف أُلغيت في PHP 7.2+ مثل امتداد `mcrypt` في [enc.php](file:///d:/wamp/www/myworks/POS/enc.php#L35-L68) و [Encryption.php](file:///d:/wamp/www/myworks/POS/Encryption.php#L35-L70)، ومُنشئات أصناف بأسلوب PHP 4 في [validation.class.php](file:///d:/wamp/www/myworks/POS/validation.class.php#L15).
  - الاتصال بقاعدة البيانات يتم عبر `mysqli_*` الإجرائي المباشر في [db.conn.php](file:///d:/wamp/www/myworks/POS/db.conn.php#L8-L25).
  - وجود ملف [Sale.php](file:///d:/wamp/www/myworks/POS/Sale.php#L1-L16) يتبع إطار CodeIgniter (منسوخ بالكامل من نظام Open Source Point of Sale - OSPOS) وهو كود ميت (Dead Code) لا يعمل نظراً لغياب الثابت `BASEPATH`.
- **الواجهة الأمامية (Frontend):**
  - **طبقة الواجهة القديمة (Legacy Stack):** jQuery 1.9.1 (يعود لعام 2013 ويحتوي ثغرات XSS معروفة)، Bootstrap 2/3 القديم بأيقونات Halflings و Glyphicons، Chosen 1.4، DataTables قديم، مدمجة في شاشات المبيعات [pos.php](file:///d:/wamp/www/myworks/POS/pos.php#L57) و [accounts.php](file:///d:/wamp/www/myworks/POS/accounts.php).
  - **طبقة الواجهة الحديثة (Modern Admin Pro 2025):** محاولة إعادة تصميم حديثة تم تطبيقها على نحو 17 صفحة فقط (ملفات الماستر داتا كالتصنيفات والعملاء) عبر [css/admin-pro.css](file:///d:/wamp/www/myworks/POS/css/admin-pro.css) ومكتبات CDN في [include.php](file:///d:/wamp/www/myworks/POS/include.php#L11-L35) مثل Bootstrap 5.3.2، SweetAlert2 11.10، Select2 4.1، و Chart.js 4.4.
- **محرك التقارير (Reporting):**
  - يعتمد على `PHPJasperXML` مع `TCPDF` في مجلد [reports/](file:///d:/wamp/www/myworks/POS/reports/sales.php#L6-L26) لمعالجة قوالب JasperReports المصممة بـ Java (`.jrxml` و `.jasper`).
- **الطباعة الحرارية (Thermal Receipt Printing):**
  - يعتمد على حزمة `mike42/escpos-php` في مجلد [escpos/](file:///d:/wamp/www/myworks/POS/escpos/index.php#L1-L13) مع مكتبة `I18N_Arabic` لمعالجة التشكيل والحروف العربية.
  - وجود مجلدات وملفات معزولة تجريبية مثل Neodynamic WebClientPrint في مجلد [src/](file:///d:/wamp/www/myworks/POS/src/DemoPrintCommands.php) ومثبت [qz-tray-2.1.3.exe](file:///d:/wamp/www/myworks/POS/escpos/qz-tray-2.1.3.exe) بحجم 60 ميجابايت دون تفعيل فعلي في تدفق البيع اليومي.

### 1.2 نمط المعمارية المتبع (Architecture Pattern)
- **النمط الحالي:** معمارية متجانسة متداخلة (Spaghetti Monolithic Architecture).
- **متحكم إلهي وحيد (The God Controller Anti-pattern):**
  - تجميع كافة عمليات النظام في ملف واحد ضخم [controller.php](file:///d:/wamp/www/myworks/POS/controller.php) (4,712 سطر بحجم 193KB) يحوي أصنافاً ودوالاً متضاربة تدير في نفس الوقت: تسجيل الدخول، إنشاء الفواتير، رفع الصور، تشفير النصوص، معالجة الضرائب، وتوليد القيود المحاسبية.
  - وجود ملف مكرر غير مبرر يحمل اسم [controller_1.php](file:///d:/wamp/www/myworks/POS/controller_1.php) بحجم 199KB دون وجود إدارة إصدارات واضحة لفصل المهام.
- **انعدام فصل الاهتمامات (Zero Separation of Concerns):**
  - الصفحات الرئيسية (مثل [accounts.php](file:///d:/wamp/www/myworks/POS/accounts.php#L145-L177) و [add_purchase.php](file:///d:/wamp/www/myworks/POS/add_purchase.php#L40-L56)) تقوم بالاستعلام المباشر من قاعدة البيانات، ورسم هياكل الـ HTML، ودمج أكواد JavaScript و CSS في نفس الصفحة.
  - غياب طبقات الخدمات (Service Layer)، ومستودعات البيانات (Repository Layer)، ونماذج البيانات المنطقية (Entities/DTOs).

### 1.3 هيكل المجلدات وتوزيع الأدوار
- **المجلد الرئيسي (Root Directory):** مكتظ بأكثر من 80 ملف PHP تنفيذي لصفحات العرض والتعديل والإضافة المكررة (`add_*`, `edit_*`) دون استخدام نماذج قابلة لإعادة الاستخدام (Reusable Form Components).
- **مجلدات مكررة وملفات مهملة (Artifacts & Clutter):**
  - وجود مجلدين كاملين داخل المستودع: `POS--` و `POS-new` يحتويان على نسخ سابقة مكررة من النظام.
  - وجود ملفات نصوص مؤقتة ومسودات مهملة في الجذر مثل `f.txt`, `ou.txt`, `re.txt`, `db.conn.mah.php`.

---

## المحور الثاني: تدقيق قاعدة البيانات ونموذج البيانات (Data Model Audit)

### 2.1 محرك التخزين (Storage Engine)
> [!CAUTION]
> **الخطورة: حرجة جداً (Critical Severity)**  
> كافة جداول قاعدة البيانات في [wamtech_db.sql](file:///d:/wamp/www/myworks/POS/wamtech_db.sql#L50) تعمل بمحرك `ENGINE=MyISAM`.

#### التبعات التقنية الكارثية لمحرك MyISAM:
1. **انعدام المعاملات الذرية (Lack of ACID Transactions):** MyISAM لا يدعم `START TRANSACTION` أو `COMMIT` أو `ROLLBACK`. أي أمر تراجع برمجي يتم استدعاؤه يتم تجاهله من قبل محرك البيانات.
2. **أقفال على مستوى الجدول بالكامل (Table-Level Locking):** عند قيام كاشير بتسجيل فاتورة بيع (أمر `INSERT` في `sales_items`)، يتم قفل جدول المبيعات والمخزون بالكامل، مما يمنع بقية الكاشيرات في الفروع الأخرى من تسجيل أي حركة في نفس الثانية، مسبباً تجمداً دورياً (Bottlenecks) في فترات الذروة.
3. **تعرض الجداول للتلف (Data Corruption):** في حال انقطاع التيار الكهربائي المفاجئ عن خادم WAMP، تفقد جداول MyISAM فهرسها وتتطلب أوامر إصلاح يدوية (`REPAIR TABLE`).

### 2.2 العلاقات والمفاتيح الأجنبية (Foreign Keys & Integrity)
> [!WARNING]
> **الخطورة: حرجة (Critical Severity)**  
> **صفر مفاتيح أجنبية (0 Foreign Keys):** لا يوجد مفتاح أجنبي واحد معرّف في كامل قاعدة البيانات [wamtech_db.sql](file:///d:/wamp/www/myworks/POS/wamtech_db.sql).

- الاعتماد الكلي على حقول نصية وأرقام معرفة يدوياً (`itemid`, `store`, `customer`, `branch`) دون أي قيود سلامة مرجعية (`ON DELETE RESTRICT` / `CASCADE`).
- في حال حذف فرع أو مورد أو مستودع، تظل الفواتير والقيود المحاسبية تشير إلى معرّفات غير موجودة، مما يخل بالتقارير المالية والمحاسبية.

### 2.3 الفهارس واستعلامات البحث (Indexes & Query Optimization)
- معظم الأعمدة المستخدمة في شروط البحث والربط السريع (`barcode`, `invoice_No`, `document`, `date`, `storeid`) تفتقر للفهارس (Indexes).
- وجود محاولات ترقيعية فاشلة:
  - ملف [add_indexes.php](file:///d:/wamp/www/myworks/POS/add_indexes.php#L17-L20) يحتوي على خطأ برمجي (`for($i=1;$i<count($query);$i++)`) يتجاهل تنفيذ أول فهرس (`$query[0]` الخاص بـ `itemid`)، كما يحاول استخدام كائن `$adController` غير معرّف فيه، مما يؤدي إلى فشل السكربت.
  - ملف [add_index.php](file:///d:/wamp/www/myworks/POS/add_index.php#L38-L41) يقوم بمحاولة إضافة فهارس لجميع أعمدة جدول الطلبات عشوائياً، وهو ما يستهلك مساحة التخزين ويقلل من سرعة عمليات الإدراج والتحديث.

### 2.4 التعارضات والتزامن في الفواتير (Concurrency & Race Conditions)
> [!CAUTION]
> **الخطورة: حرجة (Critical Severity)**  
> آلية توليد أرقام الفواتير في النظام تسبب تصادماً حتمياً في بيئة متعددة الكاشيرات.

- في [controller.php:L2512-2525](file:///d:/wamp/www/myworks/POS/controller.php#L2512-L2525):
  ```php
  function getNewInvoiceNo($table,$field,$type,$branch) {
      $query = "SELECT $field AS No FROM $table ORDER BY SUBSTRING($field, -5) DESC LIMIT 1";
      $res = mysqli_query($this->MySQL,$query);
      ...
      $trans_No = intval($rest)+1;
  }
  ```
- **المشاكل:**
  1. إجراء `ORDER BY SUBSTRING($field, -5)` يلغي إمكانية استخدام أي فهرس B-Tree، مما يجبر محرك MySQL على قراءة الجدول بالكامل (Full Table Scan) عند كل فاتورة بيع جديدة.
  2. في حال ضغط زر الحفظ من كاشيرين في نفس اللحظة، سيحصل كلاهما على نفس الرقم التسلسلي، مما يؤدي إلى فشل إحدى الفاتورتين أو تداخل السجلات وتلف البيانات.
  3. تناقض بين رقم الفاتورة المعروض على الشاشة والرقم المخزن: في [add_saleInvoice.php:L48-66](file:///d:/wamp/www/myworks/POS/add_saleInvoice.php#L48-L66) يتم احتساب رقم الفاتورة عند تحميل الصفحة في المتصفح، ولكن عند الحفظ في [controller.php:L2776](file:///d:/wamp/www/myworks/POS/controller.php#L2776) يتم استدعاء دالة `getNewInvoiceNo` لتوليد رقم جديد تماماً يتجاهل الرقم الذي شاهده المحاسب على شاشته.

---

## المحور الثالث: تدقيق منطق الأعمال والوحدات (Business Logic Audit)

### 3.1 مسار عمليات نقاط البيع (POS Flow)
1. **مسار إنشاء الفاتورة:**
   - الكاشير يفتح [pos.php](file:///d:/wamp/www/myworks/POS/pos.php).
   - يتم التحقق من الوردية (`transactions`).
   - عند إتمام البيع، تستدعي الواجهة [controller.php:addSale](file:///d:/wamp/www/myworks/POS/controller.php#L2769-L2795) التي تستدعي بدروها `setSales`.
2. **انعدام الذرية في عمليات البيع والقيود:**
   - في [controller.php:L2651-2766](file:///d:/wamp/www/myworks/POS/controller.php#L2651-L2766)، تتكون عملية البيع الواحدة من سلسلة استعلامات متتالية:
     1. إدراج رأس فاتورة البيع في جدول `sales`.
     2. إدراج سجل في جدول الصرف `outgo`.
     3. حلقة تكرارية لإدراج الأصناف في `sales_items` و `outgo_items`.
     4. إنشاء قيود محاسبية مفصلة في `journal_items` (إيراد مبيعات، ضريبة قيمة مضافة، خصم مسموح به، حسابات الدفع: صندوق/فيزا/مدى، حساب العميل).
     5. إدراج رأس القيد في جدول `journal`.
   - **الخلل:** في حال انقطاع الاتصال أو حدوث خطأ في استعلام بالمنتصف (مثلاً عند السطر 2720)، لا يتم إلغاء ما تم حفظه، مما يترك النظام بفاتورة مسجلة دون بنود، أو بنود دون قيد محاسبي، مما يتسبب في **كارثة عدم تطابق الحسابات مع الإيرادات الفعلية**.
3. **تعدد طرق الدفع والخصومات:**
   - يدعم النظام تعدد طرق الدفع (نقد Cash، شبكة Mada، فيزا Visa، آجل Left) وتوزيعها على الحسابات (`acc007` للصندوق، `acc074` للفيزا، `acc075` لمدى)، وهو توجه ممتاز، ولكن يتم التعامل مع أكواد الحسابات عبر نصوص ثابتة مدمجة بالكود (Hardcoded Account Codes) بدلاً من قراءتها من إعدادات الربط المحاسبي المرنة.
4. **تأثير البيع على المخزون ومصيبة احتساب الرصيد:**
   - في [controller.php:getBalanceItem](file:///d:/wamp/www/myworks/POS/controller.php#L2452-L2510):
     - بدلاً من وجود جدول أرصدة مجمعة لحظية، يقوم النظام بحساب الرصيد عبر قراءة كل الحركات السابقة لكل صنف (حلقة تقرأ `open_palance` ثم `income_items` ثم `outgo_items`) وتجميعها عبر كود PHP!
     - هذا الاستعلام يتكرر لكل صنف في شاشة الكاشير، مما يجعل النظام شديد البطء كلما زادت حركات المتجر.
   - لا يوجد قفل سطري للمخزون لمنع البيع المزدوج في نفس اللحظة (Race Condition in Inventory).

### 3.2 خلل كارثي في إدارة الورديات (Cashier Shifts Bug)
> [!CAUTION]
> **الخطورة: حرجة (Critical Severity)**  
> السطر 235-247 في ملف [pos.php](file:///d:/wamp/www/myworks/POS/pos.php#L235-L247) يحتوي على خطأ منطقي يمنع إنشاء ورديات جديدة:
```php
$res = mysqli_query($adController->MySQL,$query); 
$data = mysqli_fetch_assoc($res);
if($num) {
    $newTrans = $data['id'];+1; // خطأ نحوي قاتل: الفاصلة المنقوطة تمنع الزيادة!
    $openPalance = $data['close_palance'];
} else {
    $newTrans = 1;
    $openPalance = 0;
}
$queryy = "INSERT INTO `transactions`(`id`, `user_id`, `open_palance`) VALUES ('$newTrans','$userId','$openPalance')";
```
- المتغير `$num` يعتمد على نتيجة سابقة تساوي صفراً، مما يدخل الشرط دائماً في تفريع `else`، فيصبح `$newTrans` دائماً `1`، وتفشل عملية إدخال الوردية فور وجود المعاملة رقم 1 بخطأ `Duplicate entry '1' for key 'PRIMARY'`.

### 3.3 وحدات الـ ERP الأساسية
1. **المشتريات والموردين (Purchases & Suppliers):**
   - آلية المشتريات متصلة بجدول `income` لزيادة المخزون وتوليد القيود المحاسبية، لكنها تعاني من نفس مشكلة انعدام المعاملات (No Transactions) واحتمال تكرار أرقام الفواتير.
2. **العملاء والخصومات (Customers & Discounts):**
   - إمكانية الإضافة السريعة للعملاء من شاشة الـ POS جيدة، إلا أنها تفتقر إلى التحقق من تكرار الأرقام الضريبية أو أرقام الهواتف بشكل دقيق قبل الحفظ.
3. **الحسابات والشجرة المحاسبية (Chart of Accounts & GL):**
   - الشجرة المحاسبية في [accounts.php](file:///d:/wamp/www/myworks/POS/accounts.php) مبنية بهيكل هرمي متعدد المستويات (`parent`, `code`, `hasChild`).
   - استعراض كشف الحساب في [account-statement.php:L11-33](file:///d:/wamp/www/myworks/POS/account-statement.php#L11-L33) يستخدم دالة استدعاء ذاتي (Recursion) تنفذ استعلام SQL لكل مستوى فرعي دون إدراج `$adController` في النطاق العام (`global $adController;`)، مما يؤدي إلى فشل الدالة أو إصدار أخطاء عند تفعيل مستويات تقرير الأخطاء القياسية.
   - وجود خطأ مطبعي في اسم الحقل البرمجي في [account-statement.php:L220](file:///d:/wamp/www/myworks/POS/account-statement.php#L220): `brarnch` بدلاً من `branch`.
4. **حركة الأصناف وجرد المخازن (Inventory & Stock Movement Disaster):**
   > [!CAUTION]
   > في الملفين [item-movement.php:L9-14](file:///d:/wamp/www/myworks/POS/item-movement.php#L9-L14) و [store-inventory.php:L9-12](file:///d:/wamp/www/myworks/POS/store-inventory.php#L9-L12):
   ```php
   $query = "TRUNCATE TABLE report_item_move_sam";
   $res = mysqli_query($adController->MySQL,$query);
   $query = "TRUNCATE TABLE report_item_move_all";
   $res = mysqli_query($adController->MySQL,$query);
   ```
   - النظام يقوم بحذف ومسح الجداول بالكامل (`TRUNCATE TABLE`) عند مجرد زيارة أي مستخدم للصفحة!
   - هذا يعني أنه إذا قام مستخدم باستخراج تقرير حركة المخزون، وقام مستخدم آخر في فرع ثانٍ بفتح الصفحة، سيتم **تصفير بيانات التقرير للأول في منتصف عمله**!

---

## المحور الرابع: تقييم الديون التقنية ومكامن الخلل (Technical Debt & Vulnerabilities)

### 4.1 الثغرات الأمنية (Security Vulnerabilities)

#### 1. حقن أوامر الاستعلام (SQL Injection - SQLi) - [حرجة جداً 🔴]
- معظم استعلامات النظام ملوثة بدمج المدخلات المباشرة من `$_GET` و `$_POST` دون تحضير (Prepared Statements) أو تعقيم (`mysqli_real_escape_string`).
- **أمثلة صارخة:**
  - تسجيل الدخول [controller.php:L150](file:///d:/wamp/www/myworks/POS/controller.php#L150):
    `SELECT * FROM employees WHERE user = '$user' AND password='$password'`  
    يمكن تجاوزه بسهولة عبر: `admin' -- `
  - فحص اسم المستخدم [check.php:L10](file:///d:/wamp/www/myworks/POS/check.php#L10):
    `SELECT id FROM users WHERE user='$username'`
  - تقارير كشوف الحسابات [account-statement.php:L220-224](file:///d:/wamp/www/myworks/POS/account-statement.php#L220-L224):
    دمج مباشر للتواريخ والفروع وحسابات العملاء.

#### 2. استدعاء الدوال العشوائي (Arbitrary Method Execution / RCE Vector) - [حرجة جداً 🔴]
- في [controller.php:L9-15](file:///d:/wamp/www/myworks/POS/controller.php#L9-L15):
  ```php
  $function = $_REQUEST['f'];
  $adController = new AdController();
  if($function !="")
      $adController->$function();
  ```
- لا توجد قائمة بيضاء (Whitelist) للدوال المسموح باستدعائها، ولا يوجد تحقق من الجلسة قبل تنفيذ الدالة، مما يسمح لأي مستخدم بتنفيذ أي دالة داخل الكلاس مباشرة عبر استدعاء الرابط: `controller.php?f=delete_invoice`.

#### 3. ثغرة التنفيذ بعد إعادة التوجيه (Execution After Redirect - EAR) - [حرجة جداً 🔴]
- في [redirection.php:L6-12](file:///d:/wamp/www/myworks/POS/redirection.php#L6-L12):
  ```php
  if(!isset($_SESSION['userlogged'])) {
      if(!in_array($pageName,$freepages)) {
          print("<script>location.replace('login.php');</script>");
      }
  }
  ```
- لا يوجد أمر `exit()` أو `die()` بعد طباعة سطر الجافاسكريبت!
- أي مهاجم يستخدم أداة مثل `curl` أو يقوم بتعطيل الجافاسكريبت في متصفحه سيتمكن من **تصفح وتلقي كافة مخرجات الصفحات المحمية بالكامل وتخطي تسجيل الدخول**.

#### 4. غياب نظام التحكم بالوصول المبني على الأدوار (Absence of RBAC) - [عالية 🟠]
- دالة [checkAuthorized](file:///d:/wamp/www/myworks/POS/controller.php#L1429-L1436) تفحص فقط `isset($_SESSION['storeid'])`.
- لا توجد مصفوفة أدوار وصلاحيات (Permissions Matrix) تفصل بين صلاحيات مدير النظام، المحاسب، الكاشير، وأمين المخزن. أي كاشير مسجل دخوله يستطيع الوصول لكامل مسارات المحاسبة والإعدادات.

#### 5. تخزين كلمات المرور بطرق ضعيفة (Weak Password Hashing) - [عالية 🟠]
- تشفير كلمات المرور يتم باستخدام `md5()` في [controller.php:L149](file:///d:/wamp/www/myworks/POS/controller.php#L149) دون استخدام Salt أو خوارزميات حديثة مثل `Bcrypt` أو `Argon2id`.

#### 6. تسريب بيانات الاتصال في الكود (Hardcoded Credentials) - [عالية 🟠]
- وجود كلمات مرور صريحة وعناوين IP خارجية لخوادم في الكود في [db.conn.php:L12](file:///d:/wamp/www/myworks/POS/db.conn.php#L12) و [reports/setting.php:L4](file:///d:/wamp/www/myworks/POS/reports/setting.php#L4).

#### 7. انعدام حماية تزوير الطلبات عبر المواقع (Zero CSRF Protection) - [عالية 🟠]
- جميع النماذج التي تنفذ عمليات حساسة (حذف فواتير، تعديل أسعار، حفظ مبيعات) لا تحتوي على CSRF Tokens.

#### 8. التشفير الوهمي (Fake / Obsolete Encryption) - [متوسطة 🟡]
- صنف [Encryption.php:L12-18](file:///d:/wamp/www/myworks/POS/Encryption.php#L12-L18) يحتوي على دوال تدعي التشفير، ولكنها في الواقع تنفذ `urlencode` و `urldecode` فقط!
- استدعاء خوارزميات قديمة عبر `mcrypt` (في نمط ECB غير الآمن) والتي تسبب انهياراً فورياً في حال تشغيل النظام على بيئات PHP 8 الحديثة.

---

### 4.2 مشاكل الأداء وجودة الكود (Performance & Code Smells)
1. **معضلة استعلامات N+1:**
   - متفشية في جميع شاشات الجداول؛ على سبيل المثال في [categories.php:L96-127](file:///d:/wamp/www/myworks/POS/categories.php#L96-L127)، لكل تصنيف يتم إطلاق استعلام للفرع واستعلام للصور، فإذا كان هناك 100 تصنيف يتم إطلاق أكثر من 200 استعلام للصفحة الواحدة بدلاً من استعلام موحد عبر `JOIN`.
2. **انعدام التخزين المؤقت (No Caching Strategy):**
   - لا يوجد استخدام لـ Redis أو Memcached أو حتى APCu لتخزين أسماء الأصناف، الفئات، أو إعدادات النظام التي تقرأ آلاف المرات يومياً من قاعدة البيانات.
3. **غياب الاختبارات التلقائية (Zero Automated Tests):**
   - لا توجد اختبارات وحدة (Unit Tests) أو اختبارات تكاملية (Integration Tests) للتحقق من صحة الحسابات المالية أو رصيد المخزون عند إتمام المبيعات والمشتريات.

---

## المحور الخامس: تقييم واجهة وتجربة المستخدم (UI/UX Review)

### 5.1 استجابة شاشة الكاشير لضغط العمل (Stress & Latency)
- **شاشة الكاشير القديمة [pos.php](file:///d:/wamp/www/myworks/POS/pos.php):**
  - عند مسح الباركود، يتم إطلاق استدعاء AJAX منفصل لجلب بيانات الصنف [js/pos.js:L75-100](file:///d:/wamp/www/myworks/POS/js/pos.js#L75-L100)، ثم يتم تنفيذ تأخير متعمد بمدة ثانية كاملة عبر `setTimeout` [js/pos.js:L65-69](file:///d:/wamp/www/myworks/POS/js/pos.js#L65-L69) لحساب الرصيد والإجماليات!
  - هذا السلوك يجعل تمرير الأصناف السريع بالباركود أمراً مستحيلاً؛ إذ ستتداخل العمليات ويتأخر المتصفح ويفقد الكاشير التركيز.
- **تعديل الـ DOM الهش عند حذف الأصناف:**
  - دالة [delItem](file:///d:/wamp/www/myworks/POS/js/pos.js#L159-L182) تقوم بالمرور في حلقة وتعديل معرّفات عناصر الـ DOM يدوياً (`row-v-i`, `barcode-i`, `item-qty-i`). هذا الأسلوب البرمجي القديم يسبب تضارباً شديداً في الأرقام وحسابات الإجمالي في حال حذف صنف وإضافة صنف آخر بسرعة.

### 5.2 دعم الاختصارات ولوحة المفاتيح (Shortcuts & Hotkeys)
- **غياب تام للاختصارات السريعة:** لا يوجد دعم لأزرار لوحة المفاتيح الوظيفية (مثل F1 للبحث، F2 للعميل، F4 لتسديد الدفعة، Enter لطباعة وإتمام الفاتورة، ESC للإلغاء).
- الكاشير مجبر على رفع يده واستخدام الفأرة للتنقل بين الحقول، أو الضغط على لوحة الأرقام الافتراضية المرسومة على الشاشة [js/pos.js:L259-288](file:///d:/wamp/www/myworks/POS/js/pos.js#L259-L288)، وهو ما يعيق السرعة المطلوبة في متاجر التجزئة ومحلات السوبرماركت والمطاعم.

### 5.3 الطباعة الحرارية (Thermal Printing)
- **الطباعة المتزامنة المعطلة للطلب (Blocking Synchronous Print):**
  - في [controller.php:L2786](file:///d:/wamp/www/myworks/POS/controller.php#L2786)، عند حفظ الفاتورة يتم استدعاء [escpos/index.php](file:///d:/wamp/www/myworks/POS/escpos/index.php) وطباعة الفاتورة قبل إرسال كود التوجيه `header("Location: pos.php")`.
  - إذا كانت الطابعة مغلقة، أو نفد منها الورق، أو انقطع كابل الـ USB، يعلق اتصال الـ HTTP للمتصفح بالكامل وينتهي بـ Timeout، ولا يعرف الكاشير ما إذا تم حفظ الفاتورة أم لا.
- **مسار الطابعة الثابت (Hardcoded Port):**
  - يتم الاتصال بالطابعة عبر مسار ثابت [escpos/index.php:L11](file:///d:/wamp/www/myworks/POS/escpos/index.php#L11): `new FilePrintConnector("//localhost/POS-80")`.
  - هذا يفترض أن نظام الـ POS يعمل محلياً فقط على نفس جهاز الكاشير وأن الطابعة مشاركة باسم `POS-80`. إذا تم رفع النظام على خادم سحابي أو شبكة محلية، تفشل الطباعة فوراً.
- **بطء طباعة اللغة العربية (Arabic GD Bitmaps):**
  - لطباعة الحروف العربية، يقوم النظام في [escpos/index.php:L22-25](file:///d:/wamp/www/myworks/POS/escpos/index.php#L22-L25) برسم النصوص العربية كصور نقطية (Bitmaps) عبر مكتبة GD وإرسالها نقطة بنقطة للطابعة.
  - إرسال الصور عبر الطابعة الحرارية يستغرق من 3 إلى 7 ثوانٍ لكل إيصال، مقارنة بأجزاء من الثانية في حال استخدام أوامر النصوص المباشرة أو الطباعة المباشرة عبر بروتوكولات العميل (Client-side Raw Printing).

---

## جدول تصنيف الملاحظات والخلل حسب الأولوية والخطورة

| # | الملاحظة / الخلل | الموقع بالكود | التصنيف | مستوى الخطورة | التأثير على النظام |
| :-: | :--- | :--- | :-: | :-: | :--- |
| **1** | محرك التخزين MyISAM وغياب المعاملات (ACID) | `wamtech_db.sql` | بنية البيانات | 🔴 حرج جداً | انهيار سلامة البيانات المحاسبية وتجمد الجداول |
| **2** | ثغرات SQL Injection واسعة النطاق | `controller.php`, `login.php`, إلخ | أمان | 🔴 حرج جداً | تسريب كامل للبيانات أو اختراق الخادم بالكامل |
| **3** | استدعاء الدوال العشوائي (Arbitrary Method Exec) | `controller.php:14` | أمان | 🔴 حرج جداً | تمكين أي زائر من حذف الفواتير وتعديل البيانات |
| **4** | خطأ منطقي يمنع بدء ورديات الكاشير الجديدة | `pos.php:235-247` | منطق أعمال | 🔴 حرج جداً | تعطل شاشة الكاشير بظهور Duplicate entry |
| **5** | مسح بيانات الجرد وحركة الأصناف عند فتح التقرير | `item-movement.php:9`, `store-inventory.php:9` | منطق أعمال | 🔴 حرج جداً | مسح متكرر للبيانات وتضارب بين المستخدمين |
| **6** | التخطي الأمني لنظام التحقق (Execution After Redirect) | `redirection.php:10` | أمان | 🔴 حرج جداً | تخطي المصادقة والوصول لأي صفحة داخلية |
| **7** | تصادم أرقام الفواتير (Invoice Concurrency Clashes) | `controller.php:2514` | أداء / تماسك | 🔴 حرج جداً | تلف في تسلسل الفواتير وفشل عمليات البيع المتزامنة |
| **8** | انعدام المفاتيح الأجنبية (Zero Foreign Keys) | `wamtech_db.sql` | بنية البيانات | 🟠 عالية | تراكم سجلات يتيمة وضياع الروابط المحاسبية |
| **9** | بطء استعلامات المخزون المعقدة (N+1 Stock Loop) | `controller.php:2452` | أداء | 🟠 عالية | استهلاك 100% من معالج السيرفر في الذروة |
| **10** | انعدام نظام الأدوار والصلاحيات (RBAC) | `controller.php:1429` | أمان | 🟠 عالية | تحكم الكاشير في شاشات الإدارة والمحاسبة |
| **11** | تشفير كلمات المرور بـ MD5 الضعيف وبلا Salt | `controller.php:149` | أمان | 🟠 عالية | سهولة كسر كلمات المرور بجداول Rainbow Tables |
| **12** | تسريب أسرار وقواعد بيانات خارجية صريحة في الكود | `db.conn.php:12` | أمان | 🟠 عالية | كشف بيانات خوادم الإنتاج للمطورين والمهاجمين |
| **13** | طباعة الفواتير الحرارية متزامنة ومعطلة للطلب | `escpos/index.php:11` | كفاءة / UX | 🟠 عالية | تجميد شاشة الكاشير في حال مشاكل الطابعة |
| **14** | بطء شاشة الكاشير وتأخير الـ 1 ثانية عند كل مسح صنف | `js/pos.js:65` | تجربة مستخدم | 🟠 عالية | طوابير انتظار طويلة وتعطيل عمليات الكاشير |
| **15** | غياب حماية النماذج من هجمات CSRF | كافة ملفات النماذج | أمان | 🟠 عالية | تنفيذ عمليات حساسة باسم المستخدم دون علمه |
| **16** | غياب اختصارات لوحة المفاتيح لشاشة البيع (Hotkeys) | `pos.php`, `js/pos.js` | تجربة مستخدم | 🟡 متوسطة | بطء الكاشير واعتماده الإجباري على الماوس |
| **17** | معضلة استعلامات N+1 في الجداول المعروضة | `categories.php:96` | أداء | 🟡 متوسطة | بطء ملحوظ في تحميل صفحات الإدارة |
| **18** | وجود أكواد ميتة ومكتبات معزولة غير مستخدمة | `Sale.php`, `qz-tray`, `src/` | ديون تقنية | 🟡 متوسطة | تشويش على التطوير وزيادة حجم المستودع بلا داعٍ |
| **19** | طباعة المتغيرات في الواجهة دون تعقيم (XSS) | `categories.php:105` | أمان | 🟡 متوسطة | إمكانية حقن كود خبيث عبر أسماء الأصناف |
| **20** | تضخم الكنترولر (God Class) وتكرار ملفه | `controller.php`, `controller_1.php` | معمارية كود | 🟡 متوسطة | صعوبة بالغة في الصيانة وإضافة ميزات جديدة |

---

## خارطة الطريق الهندسية المقترحة للإنقاذ والتحديث (Actionable Remediation Roadmap)

### المرحلة 1: التدخل العاجل وإصلاح المخاطر الحرجة (Emergency Phase - الأسبوع الأول)
1. **ترحيل محرك قاعدة البيانات إلى InnoDB:**
   - تحويل جميع الجداول فوراً من `MyISAM` إلى `InnoDB`:
     `ALTER TABLE table_name ENGINE=InnoDB;`
   - تفعيل المعاملات الذرية (`START TRANSACTION` / `COMMIT` / `ROLLBACK`) حول كافة عمليات البيع، المشتريات، والقيود المحاسبية.
2. **إغلاق الثغرات الأمنية الفورية:**
   - وضع `exit();` فوراً بعد توجيهات الجافاسكريبت في [redirection.php](file:///d:/wamp/www/myworks/POS/redirection.php).
   - حظر الاستدعاء المباشر لأي دالة في [controller.php](file:///d:/wamp/www/myworks/POS/controller.php) وتطبيق مصفوفة توجيه (Router / Action Whitelist).
   - نقل بيانات الاتصال الحساسة إلى ملف `.env` مشفر ومحمي، واستبدال مدخلات SQL المباشرة بـ Prepared Statements.
3. **إصلاح الأخطاء البرمجية المعطلة لبيئة العمل:**
   - تصحيح خطأ ورديات الكاشير في [pos.php](file:///d:/wamp/www/myworks/POS/pos.php#L237).
   - إزالة أوامر `TRUNCATE TABLE` العشوائية من ملفات حركة الأصناف والجرد واستبدالها بجداول مؤقتة خاصة بالجلسة (`TEMPORARY TABLE`).

### المرحلة 2: إعادة هيكلة النواة والأداء (Core Refactoring - الأسابيع 2 - 4)
1. **تفكيك المتحكم المتضخم (`controller.php`):**
   - تقسيمه إلى وحدات مستقلة:
     - `SalesController`
     - `InventoryController`
     - `PurchasesController`
     - `AccountingController`
     - `AuthController`
2. **إصلاح مشكلة حساب المخزون:**
   - إنشاء جدول مخصص لأرصدة الأصناف اللحظية (`item_stock_balances` بمفاتيح `item_id, store_id`) يتم تحديثه ذرياً (Atomic Increment/Decrement) عند البيع والشراء والتحويل، بدلاً من إعادة احتساب حركات التاريخ بالكامل في كل عملية.
3. **تطبيق نظام تسلسل فواتير ذري وآمن:**
   - استخدام تسلسلات ذرية (Sequences / Dedicated Counters with Row Locks) لمنع تصادم أرقام الفواتير نهائياً.

### المرحلة 3: تطوير شاشة الكاشير وتجربة المستخدم (POS Modernization - الأسابيع 5 - 6)
1. **تسريع شاشة الكاشير:**
   - إزالة مؤقت التأخير (`setTimeout 1000ms`).
   - تحميل كتالوج الأصناف الأكثر مبيعاً في التخزين المحلي للمتصفح (IndexedDB / LocalStorage) لتمكين البحث والمسح الفوري بدون طلبات شبكة متكررة (Zero-latency scanning).
   - دعم كامل للاختصارات السريعة (Hotkeys: F1-F12, Enter, Esc).
2. **معالجة الطباعة الحرارية:**
   - فصل الطباعة عن دورة استجابة الـ HTTP؛ إتمام عملية البيع فوراً وإرسال أمر الطباعة عبر بروتوكول محلي غير معطل (Background Job / Local Printing Service / WebSockets / QZ Tray).
   - استخدام قوالب الطباعة السريعة وتجنب تحويل النصوص لصور نقطية كلما أمكن.

### المرحلة 4: الحوكمة والموثوقية (Governance & Testing - الأسابيع 7 - 8)
1. تطبيق نظام حقيقي للصلاحيات والمجموعات (RBAC).
2. كتابة اختبارات آلية للتحقق من عدم تراجع الحسابات المالية (Accounting balance test: `Sum(Debit) == Sum(Credit)`).
3. إكمال توحيد الواجهات على نمط `admin-pro.css` لكافة شاشات المبيعات والمخازن والحسابات المتبقية.
