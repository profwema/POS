# مرجع RTL السريع ⚡
## نظام POS - دليل مختصر

---

## 🎯 التطبيق السريع

### في ملف HTML:
```php
<html lang="<?php echo $_SESSION['lang']; ?>" 
      dir="<?php echo ($_SESSION['lang'] == 'ar') ? 'rtl' : 'ltr'; ?>">
```

### في ملف CSS:
```css
<link href="css/rtl-support.css" rel="stylesheet">
```

✅ **هذا كل ما تحتاجه!** النظام يطبق RTL تلقائياً.

---

## 📋 جدول التحويلات السريع

| LTR | RTL |
|-----|-----|
| `left: 10px` | `left: auto; right: 10px` |
| `margin-left: 1rem` | `margin-right: 1rem; margin-left: 0` |
| `padding-left: 1rem` | `padding-right: 1rem; padding-left: 0` |
| `float: left` | `float: right` |
| `text-align: left` | `text-align: right` |
| `border-left: 2px` | `border-right: 2px; border-left: none` |
| `border-radius: 5px 0 0 5px` | `border-radius: 0 5px 5px 0` |

---

## 🔧 القواعد الذهبية الـ 5

### 1️⃣ المحاذاة
```css
[dir="rtl"] .element {
    text-align: right;
}
```

### 2️⃣ Float
```css
[dir="rtl"] .element {
    float: right;
}
```

### 3️⃣ Flexbox
```css
[dir="rtl"] .container {
    flex-direction: row-reverse;
}
```

### 4️⃣ Position
```css
[dir="rtl"] .element {
    left: auto;
    right: 10px;
}
```

### 5️⃣ Margins/Padding
```css
[dir="rtl"] .element {
    margin-right: 1rem;
    margin-left: 0;
}
```

---

## 🎨 الأنماط الشائعة

### بطاقة مع أيقونة
```css
.card {
    display: flex;
    align-items: center;
}

[dir="rtl"] .card {
    flex-direction: row-reverse;
}

[dir="rtl"] .card i {
    margin-left: 0.5rem;
    margin-right: 0;
}
```

### قائمة بنقاط
```css
[dir="rtl"] ul {
    padding-right: 2rem;
    padding-left: 0;
}
```

### زر مع أيقونة
```css
[dir="rtl"] .btn i:first-child {
    margin-right: 0.5rem;
    margin-left: 0;
}
```

### جدول
```css
[dir="rtl"] table {
    text-align: right;
}

[dir="rtl"] td,
[dir="rtl"] th {
    text-align: right;
}
```

---

## 🔢 الأرقام في RTL

```css
/* الأرقام تبقى LTR */
[dir="rtl"] .number,
[dir="rtl"] .price {
    direction: ltr;
    display: inline-block;
}
```

**مثال:**
```
✅ صحيح: 25,000 ريال
❌ خطأ: ريال 000,52
```

---

## 📱 القائمة الجانبية

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

---

## 🎯 Classes المساعدة

### محاذاة النص
```css
[dir="rtl"] .text-left { text-align: right !important; }
[dir="rtl"] .text-right { text-align: left !important; }
```

### Float
```css
[dir="rtl"] .pull-left { float: right !important; }
[dir="rtl"] .pull-right { float: left !important; }
```

### Margins
```css
[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
[dir="rtl"] .ml-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
```

---

## 🐛 الأخطاء الشائعة

### ❌ خطأ شائع 1
```css
.element {
    margin-left: 1rem;
}
```

### ✅ الحل
```css
.element {
    margin-left: 1rem;
}

[dir="rtl"] .element {
    margin-left: 0;
    margin-right: 1rem;
}
```

---

### ❌ خطأ شائع 2
```css
.container {
    display: flex;
}
/* لم يتم عكس الاتجاه */
```

### ✅ الحل
```css
.container {
    display: flex;
}

[dir="rtl"] .container {
    flex-direction: row-reverse;
}
```

---

### ❌ خطأ شائع 3
```css
.element {
    position: absolute;
    left: 20px;
}
/* لم يتم تحديث الموضع */
```

### ✅ الحل
```css
.element {
    position: absolute;
    left: 20px;
}

[dir="rtl"] .element {
    left: auto;
    right: 20px;
}
```

---

## ⚡ اختصارات مفيدة

### Template سريع للعناصر الجديدة
```css
/* LTR الأساسي */
.my-element {
    display: flex;
    align-items: center;
    text-align: left;
}

.my-element i {
    margin-right: 0.5rem;
}

/* RTL */
[dir="rtl"] .my-element {
    flex-direction: row-reverse;
    text-align: right;
}

[dir="rtl"] .my-element i {
    margin-right: 0;
    margin-left: 0.5rem;
}
```

---

## 🎨 الخطوط العربية

```css
[dir="rtl"] body,
[dir="rtl"] input,
[dir="rtl"] textarea {
    font-family: 'Cairo', 'Tajawal', 'Segoe UI', sans-serif;
    line-height: 1.8;
}
```

---

## 📱 Mobile RTL

```css
@media (max-width: 991px) {
    [dir="rtl"] #sidebar-left {
        right: -260px;
        left: auto;
    }
    
    [dir="rtl"] #sidebar-left.in {
        right: 0;
    }
}
```

---

## ✅ Checklist سريع

عند إضافة صفحة جديدة:

- [ ] ✅ `dir="rtl"` في HTML
- [ ] ✅ `css/rtl-support.css` محمّل
- [ ] ✅ الأيقونات في المكان الصحيح
- [ ] ✅ النصوص محاذاة صحيحة
- [ ] ✅ الجداول محاذاة صحيحة
- [ ] ✅ الأرقام غير معكوسة
- [ ] ✅ اختبار على الموبايل

---

## 🚀 روابط سريعة

- [الدليل الكامل](RTL_GUIDE.md)
- [ملف CSS](css/rtl-support.css)
- [أمثلة عملية](UI_COMPONENTS_EXAMPLES.md)

---

## 💡 نصيحة سريعة

**استخدم Chrome DevTools:**
1. افتح F12
2. اذهب إلى Elements
3. أضف `dir="rtl"` للـ `<html>`
4. شاهد التغييرات فوراً

---

<div align="center">

**مرجع سريع لـ RTL**

استخدم `Ctrl+F` للبحث السريع

© 2025 WAM Tech Soft

</div>

