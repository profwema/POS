# 📦 Professional Components Library - Complete Guide

**Version:** 3.0 Professional Edition  
**Date:** October 22, 2025

---

## 📋 Table of Contents

1. [Stat Cards](#1-stat-cards)
2. [Professional Cards](#2-professional-cards)
3. [Buttons](#3-buttons)
4. [Badges](#4-badges)
5. [Tables](#5-tables)
6. [Forms](#6-forms)
7. [Utilities](#7-utilities)

---

## 1. Stat Cards

### Basic Usage

```html
<div class="stat-card">
  <i class="fas fa-shopping-cart stat-card-icon"></i>
  <div class="stat-card-value">42,350</div>
  <div class="stat-card-label">إجمالي المبيعات</div>
</div>
```

### Variants

#### Primary (Default - Blue)

```html
<div class="stat-card">
  <!-- content -->
</div>
```

#### Success (Green)

```html
<div class="stat-card success">
  <i class="fas fa-chart-line stat-card-icon"></i>
  <div class="stat-card-value">18,750</div>
  <div class="stat-card-label">صافي الربح</div>
</div>
```

#### Warning (Orange)

```html
<div class="stat-card warning">
  <i class="fas fa-file-invoice stat-card-icon"></i>
  <div class="stat-card-value">1,256</div>
  <div class="stat-card-label">عدد الفواتير</div>
</div>
```

#### Danger (Red)

```html
<div class="stat-card danger">
  <i class="fas fa-users stat-card-icon"></i>
  <div class="stat-card-value">342</div>
  <div class="stat-card-label">عدد العملاء</div>
</div>
```

#### Info (Cyan)

```html
<div class="stat-card info">
  <i class="fas fa-box stat-card-icon"></i>
  <div class="stat-card-value">856</div>
  <div class="stat-card-label">المنتجات</div>
</div>
```

### Advanced Example

```html
<div class="stat-card">
  <i class="fas fa-shopping-cart stat-card-icon"></i>
  <div class="stat-card-value">42,350</div>
  <div class="stat-card-label">إجمالي المبيعات (ريال)</div>
  <div class="mt-2 small">
    <i class="fas fa-arrow-up me-1"></i> +12.5% من الشهر الماضي
  </div>
</div>
```

---

## 2. Professional Cards

### Basic Card

```html
<div class="card-pro">
  <div class="card-body-pro">محتوى البطاقة</div>
</div>
```

### Card with Header

```html
<div class="card-pro">
  <div class="card-header-pro">
    <h2 class="card-title-pro">
      <i class="fas fa-chart-line"></i>
      عنوان البطاقة
    </h2>
  </div>
  <div class="card-body-pro">محتوى البطاقة</div>
</div>
```

### Card with Footer

```html
<div class="card-pro">
  <div class="card-header-pro">
    <h2 class="card-title-pro">عنوان البطاقة</h2>
  </div>
  <div class="card-body-pro">محتوى البطاقة</div>
  <div class="card-footer-pro">
    <a href="#" class="text-decoration-none small">
      عرض المزيد
      <i class="fas fa-arrow-left ms-1"></i>
    </a>
  </div>
</div>
```

### Card with Actions in Header

```html
<div class="card-pro">
  <div class="card-header-pro">
    <div class="d-flex justify-content-between align-items-center">
      <h2 class="card-title-pro">
        <i class="fas fa-shopping-bag"></i>
        آخر الطلبات
      </h2>
      <a href="orders.php" class="btn-pro btn-pro-primary btn-pro-sm">
        عرض الكل
        <i class="fas fa-arrow-left"></i>
      </a>
    </div>
  </div>
  <div class="card-body-pro">
    <!-- content -->
  </div>
</div>
```

---

## 3. Buttons

### Types

#### Primary Button

```html
<button class="btn-pro btn-pro-primary">
  <i class="fas fa-save me-1"></i>
  حفظ
</button>
```

#### Success Button

```html
<button class="btn-pro btn-pro-success">
  <i class="fas fa-check me-1"></i>
  موافق
</button>
```

#### Danger Button

```html
<button class="btn-pro btn-pro-danger">
  <i class="fas fa-trash me-1"></i>
  حذف
</button>
```

#### Outline Button

```html
<button class="btn-pro btn-pro-outline">
  <i class="fas fa-times me-1"></i>
  إلغاء
</button>
```

### Sizes

#### Small

```html
<button class="btn-pro btn-pro-primary btn-pro-sm">زر صغير</button>
```

#### Default

```html
<button class="btn-pro btn-pro-primary">زر عادي</button>
```

#### Large

```html
<button class="btn-pro btn-pro-primary btn-pro-lg">زر كبير</button>
```

### Button Groups

```html
<div class="d-flex gap-2">
  <button class="btn-pro btn-pro-primary">حفظ</button>
  <button class="btn-pro btn-pro-outline">إلغاء</button>
</div>
```

### As Link

```html
<a href="page.php" class="btn-pro btn-pro-primary">
  <i class="fas fa-arrow-left me-1"></i>
  الانتقال
</a>
```

---

## 4. Badges

### Types

```html
<!-- Primary -->
<span class="badge-pro badge-pro-primary">جديد</span>

<!-- Success -->
<span class="badge-pro badge-pro-success">نشط</span>

<!-- Warning -->
<span class="badge-pro badge-pro-warning">قيد الانتظار</span>

<!-- Danger -->
<span class="badge-pro badge-pro-danger">ملغي</span>

<!-- Info -->
<span class="badge-pro badge-pro-info">معلومة</span>
```

### With Icons

```html
<span class="badge-pro badge-pro-success">
  <i class="fas fa-check me-1"></i> مكتمل
</span>
```

### In Table

```html
<td>
  <span class="badge-pro badge-pro-success">مكتمل</span>
</td>
```

---

## 5. Tables

### Basic Table

```html
<table class="table-pro">
  <thead>
    <tr>
      <th>العمود 1</th>
      <th>العمود 2</th>
      <th>العمود 3</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>قيمة 1</td>
      <td>قيمة 2</td>
      <td>قيمة 3</td>
    </tr>
  </tbody>
</table>
```

### Table with Actions

```html
<table class="table-pro">
  <thead>
    <tr>
      <th>الاسم</th>
      <th>البريد الإلكتروني</th>
      <th>الحالة</th>
      <th class="text-center">الإجراءات</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>أحمد محمد</strong></td>
      <td>ahmad@email.com</td>
      <td><span class="badge-pro badge-pro-success">نشط</span></td>
      <td class="text-center">
        <button class="btn-pro btn-pro-outline btn-pro-sm">
          <i class="fas fa-edit"></i>
        </button>
        <button class="btn-pro btn-pro-danger btn-pro-sm">
          <i class="fas fa-trash"></i>
        </button>
      </td>
    </tr>
  </tbody>
</table>
```

### Table in Card

```html
<div class="card-pro">
  <div class="card-header-pro">
    <h2 class="card-title-pro">قائمة البيانات</h2>
  </div>
  <div class="card-body-pro">
    <div class="table-responsive">
      <table class="table-pro">
        <!-- table content -->
      </table>
    </div>
  </div>
</div>
```

---

## 6. Forms

### Form Group

```html
<div class="form-group-pro">
  <label class="form-label-pro">الاسم</label>
  <input type="text" class="form-input-pro" placeholder="أدخل الاسم" />
</div>
```

### Complete Form

```html
<div class="card-pro">
  <div class="card-header-pro">
    <h2 class="card-title-pro">إضافة عميل جديد</h2>
  </div>
  <div class="card-body-pro">
    <form>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="form-group-pro">
            <label class="form-label-pro">الاسم الأول</label>
            <input
              type="text"
              class="form-input-pro"
              placeholder="أدخل الاسم الأول"
            />
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group-pro">
            <label class="form-label-pro">الاسم الأخير</label>
            <input
              type="text"
              class="form-input-pro"
              placeholder="أدخل الاسم الأخير"
            />
          </div>
        </div>
        <div class="col-12">
          <div class="form-group-pro">
            <label class="form-label-pro">البريد الإلكتروني</label>
            <input
              type="email"
              class="form-input-pro"
              placeholder="example@email.com"
            />
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="card-footer-pro">
    <div class="d-flex gap-2">
      <button class="btn-pro btn-pro-primary">
        <i class="fas fa-save me-1"></i>
        حفظ
      </button>
      <button class="btn-pro btn-pro-outline">
        <i class="fas fa-times me-1"></i>
        إلغاء
      </button>
    </div>
  </div>
</div>
```

---

## 7. Utilities

### Text Colors

```html
<span class="text-primary">نص أساسي</span>
<span class="text-success">نص نجاح</span>
<span class="text-warning">نص تحذير</span>
<span class="text-danger">نص خطر</span>
<span class="text-muted">نص مخفف</span>
```

### Background Colors

```html
<div class="bg-primary">خلفية أساسية</div>
<div class="bg-success">خلفية نجاح</div>
<div class="bg-warning">خلفية تحذير</div>
<div class="bg-danger">خلفية خطر</div>
```

### Spacing

```html
<!-- Margin Top -->
<div class="mt-1">Margin top 0.5rem</div>
<div class="mt-2">Margin top 1rem</div>
<div class="mt-3">Margin top 1.5rem</div>
<div class="mt-4">Margin top 2rem</div>

<!-- Margin Bottom -->
<div class="mb-1">Margin bottom 0.5rem</div>
<div class="mb-2">Margin bottom 1rem</div>
<div class="mb-3">Margin bottom 1.5rem</div>
<div class="mb-4">Margin bottom 2rem</div>
```

### Flexbox

```html
<!-- Basic Flex -->
<div class="d-flex align-items-center justify-content-between">
  <div>Left</div>
  <div>Right</div>
</div>

<!-- Flex with Gap -->
<div class="d-flex gap-2">
  <button class="btn-pro btn-pro-primary">Button 1</button>
  <button class="btn-pro btn-pro-outline">Button 2</button>
</div>

<div class="d-flex gap-3">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>
```

---

## 💡 Tips & Best Practices

### 1. Consistent Spacing

✅ Always use the spacing utilities (`.gap-2`, `.mt-3`, `.mb-4`)  
✅ Don't use inline styles for margins/paddings

### 2. Icon Usage

✅ Always add `me-1` or `ms-1` for icon spacing  
✅ Use Font Awesome 6 classes

### 3. Responsive Design

✅ Use Bootstrap grid system (`.col-md-6`, `.col-lg-4`)  
✅ Use responsive utilities (`.d-none`, `.d-md-block`)

### 4. Color Consistency

✅ Use semantic colors (primary, success, warning, danger)  
✅ Don't create custom colors unless absolutely necessary

### 5. Component Composition

✅ Combine components logically (Cards + Tables, Cards + Forms)  
✅ Keep nesting shallow (max 3-4 levels)

---

## 📚 Examples

Check `dashboard_pro_example.php` for complete working examples!

---

**© 2025 WAM Tech Soft - Components Guide v3.0**
