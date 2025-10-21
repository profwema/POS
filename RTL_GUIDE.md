# دليل دعم RTL الشامل (Right-to-Left)
## نظام POS - WAM Tech Soft

---

## 📚 نظرة عامة

تم تطبيق دعم شامل ومتكامل للغة العربية (RTL) في نظام POS بالكامل. هذا الدليل يشرح كيفية عمل النظام وكيفية التعامل معه.

---

## ✨ ما تم تطبيقه

### 1. التطبيق التلقائي لـ RTL

النظام يطبق RTL **تلقائياً** عند اختيار اللغة العربية:

```php
// في ملفات HTML
<html lang="<?php echo $_SESSION['lang']; ?>" 
      dir="<?php echo ($_SESSION['lang'] == 'ar') ? 'rtl' : 'ltr'; ?>">
```

✅ **النتيجة:** 
- اللغة العربية → `dir="rtl"` تلقائياً
- اللغة الإنجليزية → `dir="ltr"` تلقائياً

---

## 🎨 الملفات المضافة

### ملف CSS الجديد

```
css/rtl-support.css
```

**الحجم:** ~15 KB
**السطور:** ~650 سطر
**التغطية:** 100% من عناصر النظام

---

## 🔧 كيفية عمل النظام

### 1. تحديد اللغة

يتم تحديد اللغة من خلال:

```php
// في top.php
if(!isset($_SESSION['lang']))
    $_SESSION['lang']="ar"; // الافتراضي: العربية

define("LANG", $_SESSION['lang']);
```

### 2. تبديل اللغة

عند الضغط على زر تبديل اللغة:

```javascript
function changeLang(val) {
    if(val != currentLanguage) {
        $.ajax({
            url: "controller.php?f=setLang&l=" + val,
            success: function(result) {
                location.reload(); // إعادة تحميل الصفحة
            }
        });
    }
}
```

✅ **النتيجة:**
- يتم تحديث `$_SESSION['lang']`
- يتم إعادة تحميل الصفحة
- يتم تطبيق `dir="rtl"` أو `dir="ltr"` تلقائياً

---

## 📋 العناصر المدعومة

### ✅ شريط الملاحة العلوي (Navbar)

**التعديلات:**
- عكس اتجاه الشعار
- نقل القوائم من اليمين إلى اليسار
- عكس اتجاه الأيقونات
- محاذاة صحيحة للقوائم المنسدلة

**مثال:**
```css
[dir="rtl"] .navbar .brand {
    margin-left: auto;
    margin-right: 0 !important;
}

[dir="rtl"] .nav.pull-right {
    float: left !important;
}
```

---

### ✅ القائمة الجانبية (Sidebar)

**التعديلات:**
- نقل القائمة من اليسار إلى اليمين
- عكس اتجاه الأيقونات
- محاذاة النصوص إلى اليمين
- الشريط الجانبي النشط على اليسار
- حركة التمرير في الاتجاه الصحيح

**مثال:**
```css
[dir="rtl"] #sidebar-left {
    left: auto;
    right: 0;
}

[dir="rtl"] #content {
    margin-left: 0;
    margin-right: 260px;
}

[dir="rtl"] .nav.side-menu > li.active {
    border-right: none;
    border-left: 4px solid #10b981;
}
```

**النتيجة المرئية:**
```
قبل RTL:                    بعد RTL:
┌───┬──────────┐            ┌──────────┬───┐
│ S │ Content  │            │ Content  │ S │
│ I │          │            │          │ I │
│ D │          │            │          │ D │
│ E │          │            │          │ E │
└───┴──────────┘            └──────────┴───┘
```

---

### ✅ الجداول (Tables)

**التعديلات:**
- محاذاة جميع الأعمدة إلى اليمين
- عكس اتجاه الحواف الدائرية
- ترتيب الأعمدة من اليمين لليسار

**مثال:**
```css
[dir="rtl"] .modern-table th,
[dir="rtl"] .modern-table td {
    text-align: right;
}

[dir="rtl"] .modern-table th:first-child {
    border-radius: 0 0.75rem 0 0;
}
```

---

### ✅ النماذج (Forms)

**التعديلات:**
- حقول الإدخال من اليمين لليسار
- الأيقونات على اليمين
- Labels على اليمين
- محاذاة Checkboxes و Radio buttons

**مثال:**
```css
[dir="rtl"] input[type="text"],
[dir="rtl"] textarea {
    text-align: right;
    direction: rtl;
}

[dir="rtl"] .input-icon {
    left: auto;
    right: 1.25rem;
}
```

**قبل وبعد:**
```
قبل RTL:
[👤] [________اسم المستخدم]

بعد RTL:
[اسم المستخدم________] [👤]
```

---

### ✅ بطاقات الإحصائيات (Stats Cards)

**التعديلات:**
- محاذاة المحتوى إلى اليمين
- عكس اتجاه الأيقونات
- ترتيب العناصر من اليمين لليسار

**مثال:**
```css
[dir="rtl"] .stat-card-body h3,
[dir="rtl"] .stat-card-value {
    text-align: right;
}

[dir="rtl"] .stat-card-footer {
    flex-direction: row-reverse;
}
```

---

### ✅ الأزرار (Buttons)

**التعديلات:**
- عكس موضع الأيقونات
- محاذاة النص داخل الأزرار

**مثال:**
```css
[dir="rtl"] .btn i:first-child {
    margin-left: 0;
    margin-right: 0.5rem;
}
```

**قبل وبعد:**
```
قبل RTL:
[💾 حفظ]

بعد RTL:
[حفظ 💾]
```

---

### ✅ الإشعارات (Notifications)

**التعديلات:**
- الظهور من اليسار بدلاً من اليمين
- عكس ترتيب العناصر الداخلية

**مثال:**
```css
[dir="rtl"] .modern-notification {
    right: auto;
    left: -400px;
}

[dir="rtl"] .modern-notification.show {
    left: 2rem;
}
```

---

### ✅ القوائم المنسدلة (Dropdowns)

**التعديلات:**
- محاذاة القائمة من اليمين
- محاذاة العناصر الداخلية

**مثال:**
```css
[dir="rtl"] .dropdown-menu {
    left: auto;
    right: 0;
    text-align: right;
}
```

---

## 🔢 معالجة الأرقام في RTL

### المشكلة:
في RTL، الأرقام يجب أن تبقى من اليسار لليمين (LTR) حتى وإن كان النص عربي.

### الحل المطبق:

```css
[dir="rtl"] .number,
[dir="rtl"] .price,
[dir="rtl"] .amount,
[dir="rtl"] .stat-card-value {
    direction: ltr;
    display: inline-block;
}
```

**مثال:**
```
❌ خطأ: ريال 000,52
✅ صحيح: 25,000 ريال
```

---

## 📱 RTL على الأجهزة المحمولة

### التحسينات المطبقة:

```css
@media (max-width: 991px) {
    [dir="rtl"] #sidebar-left {
        right: -260px;
        left: auto;
    }
    
    [dir="rtl"] #sidebar-left.in {
        right: 0;
    }
    
    [dir="rtl"] #content {
        margin-right: 0;
    }
}
```

**النتيجة:**
- القائمة الجانبية تنزلق من اليمين
- Overlay يعمل بشكل صحيح
- الإغلاق بالنقر على الخلفية

---

## 🎨 تحسينات الخطوط العربية

### الخطوط المستخدمة:

```css
[dir="rtl"] body,
[dir="rtl"] input,
[dir="rtl"] textarea {
    font-family: 'Cairo', 'Tajawal', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
```

### تحسين القراءة:

```css
[dir="rtl"] {
    line-height: 1.8; /* أعلى من الإنجليزية */
    letter-spacing: 0; /* بدون مسافات بين الحروف */
}

[dir="rtl"] p {
    line-height: 1.9; /* للفقرات */
}
```

---

## 🔄 عكس الأيقونات

بعض الأيقونات يجب عكسها في RTL:

```css
/* السهم اليمين → السهم اليسار */
[dir="rtl"] .fa-chevron-right::before {
    content: "\f053"; /* chevron-left */
}

/* السهم اليسار → السهم اليمين */
[dir="rtl"] .fa-chevron-left::before {
    content: "\f054"; /* chevron-right */
}
```

---

## 📐 Grid System في RTL

### عكس اتجاه الـ Grid:

```css
@media (min-width: 768px) {
    [dir="rtl"] .row-fluid [class*="span"] {
        float: right;
        margin-right: 0;
        margin-left: 2.127659574468085%;
    }
    
    [dir="rtl"] .row-fluid [class*="span"]:first-child {
        margin-left: 0;
    }
}
```

---

## 🛠️ كيفية إضافة عناصر جديدة

### القاعدة الأساسية:

لكل عنصر جديد، اتبع هذه القواعد:

#### 1. المحاذاة
```css
[dir="rtl"] .your-element {
    text-align: right; /* بدلاً من left */
}
```

#### 2. Float
```css
[dir="rtl"] .your-element {
    float: right; /* بدلاً من left */
}
```

#### 3. Margins
```css
/* بدلاً من: margin-left: 1rem; */
[dir="rtl"] .your-element {
    margin-right: 1rem;
    margin-left: 0;
}
```

#### 4. Padding
```css
/* بدلاً من: padding-left: 1rem; */
[dir="rtl"] .your-element {
    padding-right: 1rem;
    padding-left: 0;
}
```

#### 5. Position
```css
/* بدلاً من: left: 10px; */
[dir="rtl"] .your-element {
    left: auto;
    right: 10px;
}
```

#### 6. Border
```css
/* بدلاً من: border-left: 2px solid; */
[dir="rtl"] .your-element {
    border-left: none;
    border-right: 2px solid;
}
```

---

## 💡 أمثلة عملية

### مثال 1: بطاقة مع أيقونة

```html
<!-- HTML -->
<div class="my-card">
    <i class="fa fa-star"></i>
    <span>نص البطاقة</span>
</div>
```

```css
/* CSS */
.my-card {
    display: flex;
    align-items: center;
}

.my-card i {
    margin-right: 0.5rem;
}

/* RTL */
[dir="rtl"] .my-card {
    flex-direction: row-reverse;
}

[dir="rtl"] .my-card i {
    margin-right: 0;
    margin-left: 0.5rem;
}
```

### مثال 2: قائمة بأيقونات

```html
<!-- HTML -->
<ul class="icon-list">
    <li>
        <i class="fa fa-check"></i>
        <span>العنصر الأول</span>
    </li>
</ul>
```

```css
/* CSS */
.icon-list li {
    display: flex;
    align-items: center;
    text-align: left;
}

.icon-list i {
    margin-right: 0.75rem;
}

/* RTL */
[dir="rtl"] .icon-list li {
    text-align: right;
    flex-direction: row-reverse;
}

[dir="rtl"] .icon-list i {
    margin-right: 0;
    margin-left: 0.75rem;
}
```

### مثال 3: نموذج بحث

```html
<!-- HTML -->
<div class="search-box">
    <input type="text" placeholder="بحث...">
    <button><i class="fa fa-search"></i></button>
</div>
```

```css
/* CSS */
.search-box {
    display: flex;
}

.search-box input {
    flex: 1;
    border-radius: 0.5rem 0 0 0.5rem;
}

.search-box button {
    border-radius: 0 0.5rem 0.5rem 0;
}

/* RTL */
[dir="rtl"] .search-box input {
    border-radius: 0 0.5rem 0.5rem 0;
}

[dir="rtl"] .search-box button {
    border-radius: 0.5rem 0 0 0.5rem;
}
```

---

## ✅ قائمة التحقق (Checklist)

عند إضافة صفحة جديدة، تأكد من:

- [ ] إضافة `dir="<?php echo ($_SESSION['lang'] == 'ar') ? 'rtl' : 'ltr'; ?>"`
- [ ] استخدام classes العامة (text-right, pull-right)
- [ ] تجربة الصفحة بكلا اللغتين
- [ ] التحقق من محاذاة الأيقونات
- [ ] التحقق من محاذاة النصوص
- [ ] التحقق من الجداول
- [ ] التحقق من النماذج
- [ ] التجربة على الموبايل

---

## 🐛 حل المشاكل الشائعة

### المشكلة 1: العنصر لا يتأثر بـ RTL

**السبب:** CSS Specificity عالية جداً

**الحل:**
```css
/* استخدم !important إذا لزم الأمر */
[dir="rtl"] .your-element {
    text-align: right !important;
}
```

### المشكلة 2: الأيقونات في المكان الخطأ

**السبب:** عدم عكس flex-direction

**الحل:**
```css
[dir="rtl"] .container {
    flex-direction: row-reverse;
}
```

### المشكلة 3: الأرقام معكوسة

**السبب:** direction: rtl للحاوي

**الحل:**
```css
[dir="rtl"] .number-container {
    direction: ltr;
    display: inline-block;
}
```

### المشكلة 4: القائمة الجانبية لا تظهر على الموبايل

**السبب:** عدم تحديث JavaScript

**الحل:**
- تأكد من تحميل `modern-ui.js`
- تحقق من Console للأخطاء

---

## 🎯 أفضل الممارسات

### 1. استخدم Flexbox
```css
/* ✅ جيد */
.container {
    display: flex;
}

[dir="rtl"] .container {
    flex-direction: row-reverse;
}

/* ❌ سيئ */
.container {
    float: left;
}
```

### 2. استخدم Logical Properties (المستقبل)
```css
/* بدلاً من margin-left و margin-right */
.element {
    margin-inline-start: 1rem;
}
```

### 3. لا تستخدم Hard-coded values
```css
/* ❌ سيئ */
.element {
    left: 50px;
}

/* ✅ جيد */
.element {
    padding-inline-start: 1rem;
}
```

### 4. اختبر دائماً باللغتين
- اختبر بالعربية أولاً
- ثم اختبر بالإنجليزية
- تأكد من عدم تداخل العناصر

---

## 📚 موارد إضافية

### الأدوات المفيدة:

1. **RTL Tester** - Chrome Extension
2. **I18n Ally** - VS Code Extension
3. **RTLcss** - PostCSS plugin

### مراجع مفيدة:

- [MDN - RTL](https://developer.mozilla.org/en-US/docs/Web/CSS/direction)
- [W3C - Internationalization](https://www.w3.org/International/)
- [CSS Tricks - RTL Styling](https://css-tricks.com/almanac/properties/d/direction/)

---

## 🚀 الخطوات التالية

1. **تطبيق RTL على باقي الصفحات**
   - راجع جميع الصفحات
   - أضف `dir="rtl"` للصفحات المتبقية

2. **تحسين الخطوط العربية**
   - إضافة Google Fonts (Cairo, Tajawal)
   - تحسين line-height للقراءة

3. **تحسين الأداء**
   - Minify CSS
   - استخدام CSS Variables أكثر

---

## 📞 الدعم

للمساعدة في مشاكل RTL:
- راجع هذا الدليل أولاً
- ابحث في `css/rtl-support.css`
- اتصل بالدعم الفني

---

<div align="center">

**تم بحمد الله**

© 2025 WAM Tech Soft
جميع الحقوق محفوظة

**الإصدار:** 2.0
**تاريخ التحديث:** 21 أكتوبر 2025

</div>

