# دليل البدء السريع - نظام POS المحدّث
## WAM Tech Soft - Quick Start Guide

---

## 🚀 البدء السريع في 5 دقائق

### ✅ الخطوة 1: التأكد من الملفات

تأكد من وجود الملفات التالية في المشروع:

```
css/
├── modern-theme.css        ✓
├── modern-login.css        ✓
└── dashboard-modern.css    ✓

js/
└── modern-ui.js            ✓
```

---

## 📄 الخطوة 2: إنشاء صفحة جديدة

### القالب الأساسي:

```php
<?php
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>عنوان الصفحة - WAM Tech Soft</title>
    <?php require_once("header.php");?>
    <link href="css/dashboard-modern.css" rel="stylesheet">
</head>

<body>
    <?php require_once("header_top.php");?>
    
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php");?>
            
            <div id="content" class="span10">
                
                <!-- ضع محتوى صفحتك هنا -->
                
            </div>
        </div>
    </div>
    
    <?php require_once("footer.php");?>
</body>
</html>
```

---

## 🎨 الخطوة 3: إضافة المحتوى

### 1. عنوان الصفحة:

```html
<div class="welcome-section" style="margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">
        <i class="fa fa-cube" style="color: #6366f1;"></i> 
        عنوان الصفحة
    </h1>
    <p style="color: #64748b; font-size: 1rem;">
        وصف مختصر للصفحة
    </p>
</div>
```

### 2. بطاقات الإحصائيات:

```html
<div class="dashboard-stats">
    <div class="stat-card primary slide-up">
        <div class="stat-card-header">
            <div class="stat-card-icon">
                <i class="fa fa-shopping-cart"></i>
            </div>
        </div>
        <div class="stat-card-body">
            <h3>عنوان الإحصائية</h3>
            <div class="stat-card-value">
                <span>1,234</span>
                <small>وحدة</small>
            </div>
            <div class="stat-card-footer">
                <span class="stat-trend up">
                    <i class="fa fa-arrow-up"></i> 12.5%
                </span>
                <span class="stat-period">مقارنة بالشهر الماضي</span>
            </div>
        </div>
    </div>
</div>
```

### 3. صندوق مع جدول:

```html
<div class="box">
    <div class="box-header">
        <h2>
            <i class="fa fa-list"></i> 
            عنوان القائمة
        </h2>
    </div>
    <div class="box-content">
        <div class="modern-table-wrapper">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>عنصر تجريبي</td>
                        <td>
                            <span class="status-badge success">نشط</span>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
```

---

## 💬 الخطوة 4: إضافة التفاعلية

### إظهار إشعار:

```javascript
<script>
$(document).ready(function() {
    // إشعار نجاح
    $('#saveBtn').click(function() {
        showNotification('تم الحفظ بنجاح!', 'success', 3000);
    });
    
    // إشعار خطأ
    $('#deleteBtn').click(function() {
        showNotification('حدث خطأ أثناء الحذف', 'error', 3000);
    });
});
</script>
```

### حالة التحميل للزر:

```javascript
<script>
$('#submitBtn').click(function() {
    var btn = $(this);
    btn.setLoading(true);
    
    // محاكاة عملية async
    setTimeout(function() {
        btn.setLoading(false);
        showNotification('تمت العملية بنجاح!', 'success');
    }, 2000);
});
</script>
```

### شاشة تحميل كاملة:

```javascript
<script>
function loadData() {
    showLoading('جاري تحميل البيانات...');
    
    $.ajax({
        url: 'get_data.php',
        method: 'GET',
        success: function(data) {
            hideLoading();
            showNotification('تم التحميل بنجاح', 'success');
        },
        error: function() {
            hideLoading();
            showNotification('فشل التحميل', 'error');
        }
    });
}
</script>
```

---

## 🎯 أمثلة سريعة

### صفحة قائمة بسيطة:

```php
<?php
require_once("top.php");
require_once("redirection.php");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>المنتجات - WAM Tech Soft</title>
    <?php require_once("header.php");?>
    <link href="css/dashboard-modern.css" rel="stylesheet">
</head>

<body>
    <?php require_once("header_top.php");?>
    
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php");?>
            
            <div id="content" class="span10">
                <!-- العنوان -->
                <div class="welcome-section" style="margin-bottom: 2rem;">
                    <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b;">
                        <i class="fa fa-cube" style="color: #6366f1;"></i> 
                        إدارة المنتجات
                    </h1>
                </div>
                
                <!-- الإحصائيات -->
                <div class="dashboard-stats">
                    <div class="stat-card primary">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="fa fa-cube"></i>
                            </div>
                        </div>
                        <div class="stat-card-body">
                            <h3>إجمالي المنتجات</h3>
                            <div class="stat-card-value">
                                <span><?php echo getTotalProducts(); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- الجدول -->
                <div class="box">
                    <div class="box-header">
                        <h2>
                            <i class="fa fa-list"></i> 
                            قائمة المنتجات
                        </h2>
                    </div>
                    <div class="box-content">
                        <button class="btn btn-primary" style="margin-bottom: 1rem;">
                            <i class="fa fa-plus"></i> إضافة منتج جديد
                        </button>
                        
                        <div class="modern-table-wrapper">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>اسم المنتج</th>
                                        <th>الكمية</th>
                                        <th>السعر</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // استعلام قاعدة البيانات
                                    $query = "SELECT * FROM items LIMIT 10";
                                    $result = mysqli_query($conn, $query);
                                    
                                    while($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $row['qty']; ?></td>
                                        <td><?php echo $row['price']; ?> ريال</td>
                                        <td>
                                            <?php if($row['qty'] > 10) { ?>
                                                <span class="status-badge success">متوفر</span>
                                            <?php } else if($row['qty'] > 0) { ?>
                                                <span class="status-badge warning">مخزون منخفض</span>
                                            <?php } else { ?>
                                                <span class="status-badge danger">غير متوفر</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a href="edit_item.php?id=<?php echo $row['id']; ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm delete-btn" 
                                                    data-id="<?php echo $row['id']; ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once("footer.php");?>
    
    <script>
    $(document).ready(function() {
        // حذف منتج
        $('.delete-btn').click(function() {
            var id = $(this).data('id');
            if(confirm('هل أنت متأكد من الحذف؟')) {
                $(this).setLoading(true);
                
                $.ajax({
                    url: 'delete_item.php',
                    method: 'POST',
                    data: {id: id},
                    success: function(response) {
                        showNotification('تم الحذف بنجاح', 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    },
                    error: function() {
                        showNotification('فشل الحذف', 'error');
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
```

---

## 📝 صفحة نموذج (Form Page)

```php
<?php
require_once("top.php");
require_once("redirection.php");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>إضافة منتج - WAM Tech Soft</title>
    <?php require_once("header.php");?>
    <link href="css/dashboard-modern.css" rel="stylesheet">
</head>

<body>
    <?php require_once("header_top.php");?>
    
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php");?>
            
            <div id="content" class="span10">
                <!-- العنوان -->
                <div class="welcome-section" style="margin-bottom: 2rem;">
                    <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b;">
                        <i class="fa fa-plus-circle" style="color: #6366f1;"></i> 
                        إضافة منتج جديد
                    </h1>
                </div>
                
                <!-- النموذج -->
                <div class="box">
                    <div class="box-header">
                        <h2>
                            <i class="fa fa-edit"></i> 
                            بيانات المنتج
                        </h2>
                    </div>
                    <div class="box-content">
                        <form id="productForm" method="POST">
                            <div class="row-fluid">
                                <div class="span6">
                                    <div class="form-group">
                                        <label>اسم المنتج *</label>
                                        <input type="text" name="name" class="form-control" 
                                               placeholder="أدخل اسم المنتج" required>
                                    </div>
                                </div>
                                
                                <div class="span6">
                                    <div class="form-group">
                                        <label>الكود</label>
                                        <input type="text" name="code" class="form-control" 
                                               placeholder="كود المنتج">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row-fluid">
                                <div class="span4">
                                    <div class="form-group">
                                        <label>السعر *</label>
                                        <input type="number" name="price" class="form-control" 
                                               placeholder="0.00" step="0.01" required>
                                    </div>
                                </div>
                                
                                <div class="span4">
                                    <div class="form-group">
                                        <label>الكمية *</label>
                                        <input type="number" name="qty" class="form-control" 
                                               placeholder="0" required>
                                    </div>
                                </div>
                                
                                <div class="span4">
                                    <div class="form-group">
                                        <label>الفئة *</label>
                                        <select name="category" class="form-control" required>
                                            <option value="">اختر الفئة</option>
                                            <?php
                                            $cats = mysqli_query($conn, "SELECT * FROM categories");
                                            while($cat = mysqli_fetch_assoc($cats)) {
                                                echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>الوصف</label>
                                <textarea name="description" class="form-control" rows="3" 
                                          placeholder="وصف المنتج (اختياري)"></textarea>
                            </div>
                            
                            <div class="form-actions" style="margin-top: 2rem;">
                                <button type="submit" class="btn btn-primary" id="saveBtn">
                                    <i class="fa fa-save"></i> حفظ
                                </button>
                                <a href="items.php" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> إلغاء
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once("footer.php");?>
    
    <script>
    $(document).ready(function() {
        $('#productForm').submit(function(e) {
            e.preventDefault();
            
            var btn = $('#saveBtn');
            btn.setLoading(true);
            
            $.ajax({
                url: 'save_item.php',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    btn.setLoading(false);
                    showNotification('تم الحفظ بنجاح!', 'success');
                    
                    setTimeout(function() {
                        window.location.href = 'items.php';
                    }, 1500);
                },
                error: function() {
                    btn.setLoading(false);
                    showNotification('فشل الحفظ، حاول مرة أخرى', 'error');
                }
            });
        });
    });
    </script>
</body>
</html>
```

---

## 🎨 نصائح سريعة

### 1. اختيار الألوان المناسبة:

```
primary   → للأزرار الرئيسية (حفظ، إضافة)
success   → للنجاح والتأكيد
warning   → للتحذيرات
danger    → للحذف والإلغاء
info      → للمعلومات الإضافية
```

### 2. استخدام الأيقونات:

```html
<!-- أيقونات شائعة -->
<i class="fa fa-plus"></i>      <!-- إضافة -->
<i class="fa fa-edit"></i>      <!-- تعديل -->
<i class="fa fa-trash"></i>     <!-- حذف -->
<i class="fa fa-save"></i>      <!-- حفظ -->
<i class="fa fa-search"></i>    <!-- بحث -->
<i class="fa fa-print"></i>     <!-- طباعة -->
<i class="fa fa-download"></i>  <!-- تحميل -->
<i class="fa fa-upload"></i>    <!-- رفع -->
```

### 3. التحقق من النماذج:

```javascript
$('form').submit(function(e) {
    // التحقق من الحقول
    var name = $('input[name="name"]').val();
    if(name.trim() == '') {
        e.preventDefault();
        showNotification('الرجاء إدخال الاسم', 'warning');
        return false;
    }
    
    // باقي الكود...
});
```

---

## ⚡ اختصارات مفيدة

### الإشعارات السريعة:

```javascript
// نجاح
showNotification('✓ تم بنجاح', 'success');

// خطأ
showNotification('✗ فشلت العملية', 'error');

// تحذير
showNotification('⚠ تحذير هام', 'warning');

// معلومات
showNotification('ℹ معلومة مفيدة', 'info');
```

### التحميل السريع:

```javascript
// بداية العملية
showLoading();

// نهاية العملية
hideLoading();
```

---

## 🚨 أخطاء شائعة وحلولها

### ❌ المشكلة: التصميم لا يظهر
```
✅ الحل: تأكد من إضافة:
<link href="css/modern-theme.css" rel="stylesheet">
```

### ❌ المشكلة: الإشعارات لا تعمل
```
✅ الحل: تأكد من إضافة:
<script src="js/modern-ui.js"></script>
```

### ❌ المشكلة: القائمة الجانبية لا تعمل
```
✅ الحل: تأكد من:
1. وجود jQuery
2. تحميل modern-ui.js
3. ترتيب السكربتات صحيح
```

---

## 📚 الخطوات التالية

بعد إتقان الأساسيات:

1. اقرأ [UI_UPDATE_GUIDE.md](UI_UPDATE_GUIDE.md) للتفاصيل الكاملة
2. راجع [UI_COMPONENTS_EXAMPLES.md](UI_COMPONENTS_EXAMPLES.md) للأمثلة المتقدمة
3. جرب إنشاء صفحاتك المخصصة

---

## 💡 دعم إضافي

للحصول على المساعدة:
- راجع ملفات الأمثلة في المشروع
- اطلع على الـ Source Code للصفحات الموجودة
- اتصل بفريق الدعم الفني

---

**نتمنى لك تجربة تطوير ممتعة! 🎉**

© 2025 WAM Tech Soft

