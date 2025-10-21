# أمثلة عملية للمكونات الجديدة
## WAM Tech Soft POS System - UI Components Guide

---

## 📦 بطاقات الإحصائيات (Stats Cards)

### مثال أساسي:

```html
<div class="stat-card primary">
    <div class="stat-card-header">
        <div class="stat-card-icon">
            <i class="fa fa-shopping-cart"></i>
        </div>
    </div>
    <div class="stat-card-body">
        <h3>إجمالي المبيعات</h3>
        <div class="stat-card-value">
            <span>25,000</span>
            <small>ريال</small>
        </div>
        <div class="stat-card-footer">
            <span class="stat-trend up">
                <i class="fa fa-arrow-up"></i> 12.5%
            </span>
            <span class="stat-period">مقارنة بالشهر الماضي</span>
        </div>
    </div>
</div>
```

### الأنواع المتاحة:
- `primary` - أزرق بنفسجي
- `success` - أخضر
- `warning` - برتقالي
- `danger` - أحمر
- `info` - أزرق
- `purple` - بنفسجي

---

## 🎯 أزرار الإجراءات السريعة (Quick Actions)

### مثال:

```html
<div class="quick-actions">
    <a href="add_item.php" class="quick-action-btn">
        <i class="fa fa-plus-circle"></i>
        <span>إضافة منتج جديد</span>
    </a>
    
    <a href="customers.php" class="quick-action-btn">
        <i class="fa fa-users"></i>
        <span>إدارة العملاء</span>
    </a>
    
    <a href="reports.php" class="quick-action-btn">
        <i class="fa fa-bar-chart"></i>
        <span>التقارير</span>
    </a>
</div>
```

---

## 📊 بطاقات الرسوم البيانية (Chart Cards)

### مثال:

```html
<div class="chart-card">
    <div class="chart-card-header">
        <h3 class="chart-card-title">
            <i class="fa fa-line-chart"></i>
            مبيعات آخر 7 أيام
        </h3>
        <div class="chart-card-actions">
            <button class="chart-filter-btn active">أسبوع</button>
            <button class="chart-filter-btn">شهر</button>
            <button class="chart-filter-btn">سنة</button>
        </div>
    </div>
    <div class="chart-card-body">
        <canvas id="myChart" height="300"></canvas>
    </div>
</div>
```

---

## 📋 قائمة النشاطات (Activity List)

### مثال:

```html
<ul class="activity-list">
    <li class="activity-item">
        <div class="activity-icon success">
            <i class="fa fa-check"></i>
        </div>
        <div class="activity-content">
            <div class="activity-title">عملية بيع جديدة</div>
            <div class="activity-description">فاتورة #12345 بقيمة 2,500 ريال</div>
        </div>
        <div class="activity-time">منذ 5 دقائق</div>
    </li>
    
    <li class="activity-item">
        <div class="activity-icon warning">
            <i class="fa fa-exclamation-triangle"></i>
        </div>
        <div class="activity-content">
            <div class="activity-title">تنبيه مخزون</div>
            <div class="activity-description">منتج (ماوس لوجيتك) متبقي 3 وحدات</div>
        </div>
        <div class="activity-time">منذ 10 دقائق</div>
    </li>
</ul>
```

---

## 📊 الجداول الحديثة (Modern Tables)

### مثال:

```html
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
            <tr>
                <td>1</td>
                <td>ماوس لوجيتك MX Master</td>
                <td>25</td>
                <td>250 ريال</td>
                <td>
                    <span class="status-badge success">متوفر</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>كيبورد ميكانيكي</td>
                <td>5</td>
                <td>350 ريال</td>
                <td>
                    <span class="status-badge warning">مخزون منخفض</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

---

## 🏷️ شارات الحالة (Status Badges)

### الأنواع المتاحة:

```html
<!-- نجاح / متوفر -->
<span class="status-badge success">متوفر</span>

<!-- تحذير / مخزون منخفض -->
<span class="status-badge warning">مخزون منخفض</span>

<!-- خطر / غير متوفر -->
<span class="status-badge danger">غير متوفر</span>

<!-- معلومات / قيد المعالجة -->
<span class="status-badge info">قيد المعالجة</span>
```

---

## 📊 شريط التقدم (Progress Bar)

### مثال:

```html
<div class="progress-modern">
    <div class="progress-bar-modern primary" style="width: 75%"></div>
</div>

<div class="progress-modern">
    <div class="progress-bar-modern success" style="width: 90%"></div>
</div>

<div class="progress-modern">
    <div class="progress-bar-modern warning" style="width: 45%"></div>
</div>
```

---

## 🔔 نظام الإشعارات (Notifications)

### استخدام JavaScript:

```javascript
// إشعار نجاح
showNotification('تم الحفظ بنجاح!', 'success', 3000);

// إشعار خطأ
showNotification('حدث خطأ أثناء الحفظ', 'error', 3000);

// إشعار تحذير
showNotification('تحذير: المخزون منخفض', 'warning', 4000);

// إشعار معلومات
showNotification('تم إرسال البريد الإلكتروني', 'info', 3000);
```

---

## ⏳ شاشات التحميل (Loading States)

### 1. Loading Overlay (شاشة كاملة):

```javascript
// إظهار شاشة التحميل
showLoading('جاري تحميل البيانات...');

// إخفاء شاشة التحميل
hideLoading();
```

### 2. Button Loading State:

```javascript
// تفعيل حالة التحميل للزر
$('#saveBtn').setLoading(true);

// معالجة البيانات...
setTimeout(function() {
    // إيقاف حالة التحميل
    $('#saveBtn').setLoading(false);
    showNotification('تم الحفظ بنجاح!', 'success');
}, 2000);
```

---

## 📦 الصناديق (Box Containers)

### مثال أساسي:

```html
<div class="box">
    <div class="box-header">
        <h2>
            <i class="fa fa-list"></i>
            قائمة المنتجات
        </h2>
    </div>
    <div class="box-content">
        <!-- المحتوى هنا -->
        <p>محتوى الصندوق...</p>
    </div>
</div>
```

### مع أزرار في الـ Header:

```html
<div class="box">
    <div class="box-header">
        <h2>
            <i class="fa fa-list"></i>
            قائمة المنتجات
        </h2>
        <div class="box-tools">
            <button class="btn btn-primary">
                <i class="fa fa-plus"></i> إضافة جديد
            </button>
        </div>
    </div>
    <div class="box-content">
        <!-- المحتوى -->
    </div>
</div>
```

---

## 🎨 الأزرار (Buttons)

### الأنواع الأساسية:

```html
<!-- زر أساسي -->
<button class="btn btn-primary">
    <i class="fa fa-save"></i> حفظ
</button>

<!-- زر نجاح -->
<button class="btn btn-success">
    <i class="fa fa-check"></i> تأكيد
</button>

<!-- زر خطر -->
<button class="btn btn-danger">
    <i class="fa fa-trash"></i> حذف
</button>

<!-- زر تحذير -->
<button class="btn btn-warning">
    <i class="fa fa-exclamation-triangle"></i> تنبيه
</button>

<!-- زر معلومات -->
<button class="btn btn-info">
    <i class="fa fa-info-circle"></i> معلومات
</button>
```

### أحجام مختلفة:

```html
<button class="btn btn-primary btn-lg">كبير</button>
<button class="btn btn-primary">عادي</button>
<button class="btn btn-primary btn-sm">صغير</button>
```

---

## 📝 النماذج (Forms)

### حقول الإدخال المحسّنة:

```html
<div class="form-group">
    <label>اسم المنتج</label>
    <input type="text" class="form-control" placeholder="أدخل اسم المنتج">
</div>

<div class="form-group">
    <label>الوصف</label>
    <textarea class="form-control" rows="3" placeholder="أدخل الوصف"></textarea>
</div>

<div class="form-group">
    <label>الفئة</label>
    <select class="form-control">
        <option>اختر الفئة</option>
        <option>إلكترونيات</option>
        <option>ملابس</option>
        <option>أغذية</option>
    </select>
</div>

<div class="form-group">
    <label>
        <input type="checkbox"> متوفر في المخزن
    </label>
</div>
```

---

## 🖼️ الحالات الفارغة (Empty States)

### مثال:

```html
<div class="empty-state">
    <div class="empty-state-icon">
        <i class="fa fa-inbox"></i>
    </div>
    <h3 class="empty-state-title">لا توجد بيانات</h3>
    <p class="empty-state-description">
        لم يتم العثور على أي منتجات. قم بإضافة منتج جديد للبدء.
    </p>
    <button class="btn btn-primary">
        <i class="fa fa-plus"></i> إضافة منتج جديد
    </button>
</div>
```

---

## 🎭 Loading Skeleton

### للاستخدام أثناء تحميل البيانات:

```html
<div class="box">
    <div class="box-content">
        <div class="skeleton skeleton-title"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text" style="width: 80%;"></div>
    </div>
</div>
```

---

## 🎬 الـ Animations

### استخدام Classes الجاهزة:

```html
<!-- تلاشي من الأسفل للأعلى -->
<div class="box slide-up">
    محتوى الصندوق
</div>

<!-- تلاشي عادي -->
<div class="box fade-in">
    محتوى الصندوق
</div>

<!-- مع تأخير -->
<div class="box slide-up" style="animation-delay: 0.2s;">
    محتوى الصندوق
</div>
```

---

## 🎨 Grid System للـ Dashboard

### تخطيط متجاوب:

```html
<!-- بطاقات الإحصائيات -->
<div class="dashboard-stats">
    <div class="stat-card primary">...</div>
    <div class="stat-card success">...</div>
    <div class="stat-card warning">...</div>
    <div class="stat-card danger">...</div>
</div>

<!-- الإجراءات السريعة -->
<div class="quick-actions">
    <a href="#" class="quick-action-btn">...</a>
    <a href="#" class="quick-action-btn">...</a>
    <a href="#" class="quick-action-btn">...</a>
    <a href="#" class="quick-action-btn">...</a>
</div>
```

---

## 💡 نصائح الاستخدام

### 1. استخدام الألوان بحكمة:
```
✅ استخدم primary للإجراءات الأساسية
✅ استخدم success للنجاح والتأكيد
✅ استخدم danger للحذف والإلغاء
✅ استخدم warning للتحذيرات
✅ استخدم info للمعلومات الإضافية
```

### 2. التناسق في التصميم:
```
✅ استخدم نفس نمط الأزرار في كل الصفحات
✅ حافظ على نفس المسافات والأحجام
✅ استخدم نفس نمط البطاقات
```

### 3. الأداء:
```
✅ لا تستخدم animations كثيرة في نفس الوقت
✅ استخدم debounce/throttle للأحداث المتكررة
✅ قم بتحميل البيانات الكبيرة بشكل تدريجي
```

---

## 📱 أمثلة للـ Responsive

### مثال كامل لصفحة:

```html
<!DOCTYPE html>
<html>
<head>
    <?php require_once("header.php");?>
    <link href="css/dashboard-modern.css" rel="stylesheet">
</head>
<body>
    <?php require_once("header_top.php");?>
    
    <div class="container-fluid-full">
        <div class="row-fluid">
            <?php require_once("left_menu.php");?>
            
            <div id="content" class="span10">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <h1>
                        <i class="fa fa-cube"></i> 
                        إدارة المنتجات
                    </h1>
                    <p>عرض وإدارة جميع المنتجات</p>
                </div>
                
                <!-- Stats -->
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
                                <span>250</span>
                            </div>
                        </div>
                    </div>
                    <!-- المزيد من البطاقات... -->
                </div>
                
                <!-- Table -->
                <div class="box">
                    <div class="box-header">
                        <h2>
                            <i class="fa fa-list"></i>
                            قائمة المنتجات
                        </h2>
                    </div>
                    <div class="box-content">
                        <div class="modern-table-wrapper">
                            <table class="modern-table">
                                <!-- محتوى الجدول -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once("footer.php");?>
</body>
</html>
```

---

## 🔧 تخصيص المكونات

### تغيير لون بطاقة الإحصائيات:

```css
.stat-card.custom {
    --card-color: #8b5cf6;
    --card-color-light: #a78bfa;
}
```

### إنشاء زر مخصص:

```css
.btn-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    transition: all 0.3s;
}

.btn-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}
```

---

## 📚 أمثلة متقدمة

### 1. Modal Dialog:

```html
<div class="modal-overlay" id="myModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>عنوان النافذة</h3>
            <button class="close-modal">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <!-- المحتوى -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary">إلغاء</button>
            <button class="btn btn-primary">حفظ</button>
        </div>
    </div>
</div>
```

### 2. Tabs:

```html
<div class="tabs">
    <ul class="tab-headers">
        <li class="active"><a href="#tab1">التفاصيل</a></li>
        <li><a href="#tab2">الإحصائيات</a></li>
        <li><a href="#tab3">الإعدادات</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab1" class="tab-pane active">
            محتوى التبويب الأول
        </div>
        <div id="tab2" class="tab-pane">
            محتوى التبويب الثاني
        </div>
        <div id="tab3" class="tab-pane">
            محتوى التبويب الثالث
        </div>
    </div>
</div>
```

---

## 🎯 الخلاصة

هذه المكونات توفر:
- ✅ تصميم موحد وجميل
- ✅ سهولة الاستخدام والتطبيق
- ✅ استجابة كاملة
- ✅ أداء محسّن
- ✅ إمكانية التخصيص

استخدم هذه المكونات في صفحاتك الجديدة للحصول على تجربة مستخدم متسقة ومحترفة.

---

**تحديث أخير:** أكتوبر 2025
**الإصدار:** 2.0

