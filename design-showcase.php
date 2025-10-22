<?php
require_once("top.php");
require_once("redirection.php");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <title>عرض نظام التصميم - WAM Tech Soft</title>
  <?php require_once("header.php"); ?>
  <style>
    .showcase-section {
      background: white;
      border-radius: var(--radius-xl);
      padding: var(--space-8);
      margin-bottom: var(--space-6);
      box-shadow: var(--shadow-md);
    }

    .showcase-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary-600);
      margin-bottom: var(--space-6);
      padding-bottom: var(--space-3);
      border-bottom: 2px solid var(--primary-100);
    }

    .component-row {
      margin-bottom: var(--space-6);
    }

    .component-label {
      font-weight: 600;
      color: var(--gray-700);
      margin-bottom: var(--space-3);
      display: block;
    }

    .color-box {
      width: 100px;
      height: 80px;
      border-radius: var(--radius-lg);
      display: flex;
      align-items: flex-end;
      padding: var(--space-2);
      color: white;
      font-size: 0.75rem;
      font-weight: 600;
      box-shadow: var(--shadow-sm);
    }

    .spacing-demo {
      background: var(--primary-50);
      border: 2px dashed var(--primary-300);
      border-radius: var(--radius-md);
      padding: var(--space-2);
      display: inline-block;
    }

    .header-bar {
      background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
      color: white;
      padding: var(--space-8);
      border-radius: var(--radius-2xl);
      margin-bottom: var(--space-8);
      text-align: center;
    }
  </style>
</head>

<body>
  <?php require_once("header_top.php"); ?>

  <div class="container-fluid-full">
    <div class="row-fluid">
      <?php require_once("left_menu.php"); ?>

      <div id="content" class="span10">
        <!-- Header -->
        <div class="header-bar">
          <h1 class="text-3xl font-bold mb-3">
            <i class="fa fa-paint-brush"></i>
            عرض نظام التصميم الشامل
          </h1>
          <p class="text-lg">
            استعراض شامل لجميع المكونات والأنماط المحسّنة
          </p>
        </div>

        <!-- 1. الأزرار -->
        <div class="showcase-section">
          <h2 class="showcase-title">
            <i class="fa fa-hand-pointer-o"></i>
            1. نظام الأزرار الشامل
          </h2>

          <!-- أحجام الأزرار -->
          <div class="component-row">
            <span class="component-label">أحجام الأزرار</span>
            <div class="d-flex gap-3 items-end flex-wrap">
              <button class="btn btn-primary btn-xs">زر XS</button>
              <button class="btn btn-primary btn-sm">زر SM</button>
              <button class="btn btn-primary">زر عادي</button>
              <button class="btn btn-primary btn-lg">زر LG</button>
              <button class="btn btn-primary btn-xl">زر XL</button>
            </div>
          </div>

          <!-- أنواع الأزرار -->
          <div class="component-row">
            <span class="component-label">أنواع الأزرار</span>
            <div class="d-flex gap-3 flex-wrap">
              <button class="btn btn-primary">Primary</button>
              <button class="btn btn-secondary">Secondary</button>
              <button class="btn btn-success">Success</button>
              <button class="btn btn-warning">Warning</button>
              <button class="btn btn-danger">Danger</button>
              <button class="btn btn-info">Info</button>
            </div>
          </div>

          <!-- أزرار Outline -->
          <div class="component-row">
            <span class="component-label">أزرار محددة (Outline)</span>
            <div class="d-flex gap-3 flex-wrap">
              <button class="btn btn-outline">Outline</button>
              <button class="btn btn-outline-secondary">Outline Secondary</button>
            </div>
          </div>

          <!-- أزرار Ghost & Soft -->
          <div class="component-row">
            <span class="component-label">أزرار شفافة وناعمة</span>
            <div class="d-flex gap-3 flex-wrap">
              <button class="btn btn-ghost">Ghost</button>
              <button class="btn btn-soft-primary">Soft Primary</button>
              <button class="btn btn-soft-success">Soft Success</button>
            </div>
          </div>

          <!-- أزرار مع أيقونات -->
          <div class="component-row">
            <span class="component-label">أزرار مع أيقونات</span>
            <div class="d-flex gap-3 flex-wrap">
              <button class="btn btn-primary">
                <i class="fa fa-save"></i>
                حفظ
              </button>
              <button class="btn btn-success">
                <i class="fa fa-check"></i>
                تأكيد
              </button>
              <button class="btn btn-danger">
                <i class="fa fa-trash"></i>
                حذف
              </button>
              <button class="btn btn-primary btn-icon-only">
                <i class="fa fa-search"></i>
              </button>
            </div>
          </div>

          <!-- حالات الأزرار -->
          <div class="component-row">
            <span class="component-label">حالات الأزرار</span>
            <div class="d-flex gap-3 flex-wrap">
              <button class="btn btn-primary">عادي</button>
              <button class="btn btn-primary" disabled>معطل</button>
              <button class="btn btn-primary btn-loading">جاري التحميل...</button>
            </div>
          </div>

          <!-- مجموعة أزرار -->
          <div class="component-row">
            <span class="component-label">مجموعة أزرار</span>
            <div class="btn-group">
              <button class="btn btn-primary">الأول</button>
              <button class="btn btn-outline">الثاني</button>
              <button class="btn btn-outline">الثالث</button>
            </div>
          </div>
        </div>

        <!-- 2. الروابط -->
        <div class="showcase-section">
          <h2 class="showcase-title">
            <i class="fa fa-link"></i>
            2. نظام الروابط المحسّن
          </h2>

          <div class="component-row">
            <span class="component-label">أنواع الروابط</span>
            <div class="d-flex flex-column gap-3">
              <div>
                <a href="#" class="link-primary">رابط أساسي</a>
              </div>
              <div>
                <a href="#" class="link-secondary">رابط ثانوي</a>
              </div>
              <div>
                <a href="#" class="link-muted">رابط خافت</a>
              </div>
              <div>
                <a href="#" class="link-underline">رابط مع خط متحرك</a>
              </div>
              <div>
                <a href="#" class="link-icon">
                  التالي
                  <i class="fa fa-arrow-left"></i>
                </a>
              </div>
              <div>
                <a href="https://example.com" class="link-external">رابط خارجي</a>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. نظام الألوان -->
        <div class="showcase-section">
          <h2 class="showcase-title">
            <i class="fa fa-palette"></i>
            3. نظام الألوان المتدرج
          </h2>

          <div class="component-row">
            <span class="component-label">الألوان الأساسية (Primary)</span>
            <div class="d-flex gap-2 flex-wrap">
              <div class="color-box" style="background: #eef2ff; color: #312e81;">50</div>
              <div class="color-box" style="background: #e0e7ff; color: #312e81;">100</div>
              <div class="color-box" style="background: #c7d2fe; color: #312e81;">200</div>
              <div class="color-box" style="background: #a5b4fc; color: #312e81;">300</div>
              <div class="color-box" style="background: #818cf8;">400</div>
              <div class="color-box" style="background: #6366f1;">500</div>
              <div class="color-box" style="background: #4f46e5;">600</div>
              <div class="color-box" style="background: #4338ca;">700</div>
              <div class="color-box" style="background: #3730a3;">800</div>
              <div class="color-box" style="background: #312e81;">900</div>
            </div>
          </div>

          <div class="component-row">
            <span class="component-label">الألوان الدلالية</span>
            <div class="d-flex gap-2 flex-wrap">
              <div class="color-box" style="background: #10b981;">Success</div>
              <div class="color-box" style="background: #f59e0b;">Warning</div>
              <div class="color-box" style="background: #ef4444;">Danger</div>
              <div class="color-box" style="background: #3b82f6;">Info</div>
            </div>
          </div>
        </div>

        <!-- 4. المسافات -->
        <div class="showcase-section">
          <h2 class="showcase-title">
            <i class="fa fa-arrows-h"></i>
            4. نظام المسافات
          </h2>

          <div class="component-row">
            <span class="component-label">Margin (الهامش الخارجي)</span>
            <div class="d-flex flex-column gap-3">
              <div><code class="mr-3">m-1</code> <span class="spacing-demo m-1">4px</span></div>
              <div><code class="mr-3">m-2</code> <span class="spacing-demo m-2">8px</span></div>
              <div><code class="mr-3">m-3</code> <span class="spacing-demo m-3">12px</span></div>
              <div><code class="mr-3">m-4</code> <span class="spacing-demo m-4">16px</span></div>
              <div><code class="mr-3">m-6</code> <span class="spacing-demo m-6">24px</span></div>
            </div>
          </div>

          <div class="component-row">
            <span class="component-label">Gap (المسافة بين العناصر)</span>
            <div class="d-flex gap-1 items-center mb-2">
              <div style="background: var(--primary-500); color: white; padding: var(--space-2); border-radius: var(--radius-md);">1</div>
              <div style="background: var(--primary-500); color: white; padding: var(--space-2); border-radius: var(--radius-md);">2</div>
              <div style="background: var(--primary-500); color: white; padding: var(--space-2); border-radius: var(--radius-md);">3</div>
              <code class="mr-3">gap-1</code>
            </div>
            <div class="d-flex gap-4 items-center">
              <div style="background: var(--secondary-500); color: white; padding: var(--space-2); border-radius: var(--radius-md);">1</div>
              <div style="background: var(--secondary-500); color: white; padding: var(--space-2); border-radius: var(--radius-md);">2</div>
              <div style="background: var(--secondary-500); color: white; padding: var(--space-2); border-radius: var(--radius-md);">3</div>
              <code class="mr-3">gap-4</code>
            </div>
          </div>
        </div>

        <!-- 5. المحاذاة -->
        <div class="showcase-section">
          <h2 class="showcase-title">
            <i class="fa fa-align-justify"></i>
            5. نظام المحاذاة (Flexbox)
          </h2>

          <div class="component-row">
            <span class="component-label">justify-between</span>
            <div class="d-flex justify-between p-4" style="background: var(--gray-100); border-radius: var(--radius-lg);">
              <div style="background: var(--primary-500); color: white; padding: var(--space-3); border-radius: var(--radius-md);">عنصر 1</div>
              <div style="background: var(--primary-500); color: white; padding: var(--space-3); border-radius: var(--radius-md);">عنصر 2</div>
              <div style="background: var(--primary-500); color: white; padding: var(--space-3); border-radius: var(--radius-md);">عنصر 3</div>
            </div>
          </div>

          <div class="component-row">
            <span class="component-label">justify-center items-center</span>
            <div class="d-flex justify-center items-center p-4" style="background: var(--gray-100); border-radius: var(--radius-lg); min-height: 100px;">
              <div style="background: var(--secondary-500); color: white; padding: var(--space-4); border-radius: var(--radius-md);">عنصر في الوسط</div>
            </div>
          </div>

          <div class="component-row">
            <span class="component-label">justify-evenly</span>
            <div class="d-flex justify-evenly p-4" style="background: var(--gray-100); border-radius: var(--radius-lg);">
              <div style="background: var(--warning); color: white; padding: var(--space-3); border-radius: var(--radius-md);">1</div>
              <div style="background: var(--warning); color: white; padding: var(--space-3); border-radius: var(--radius-md);">2</div>
              <div style="background: var(--warning); color: white; padding: var(--space-3); border-radius: var(--radius-md);">3</div>
            </div>
          </div>
        </div>

        <!-- 6. البطاقات -->
        <div class="showcase-section">
          <h2 class="showcase-title">
            <i class="fa fa-clone"></i>
            6. البطاقات (Cards)
          </h2>

          <div class="row-fluid">
            <div class="span4">
              <div class="card">
                <div class="card-header">
                  بطاقة عادية
                </div>
                <div class="card-body">
                  <p>محتوى البطاقة مع نص توضيحي.</p>
                </div>
                <div class="card-footer">
                  <button class="btn btn-primary btn-sm">حفظ</button>
                </div>
              </div>
            </div>
            <div class="span4">
              <div class="card card-elevated">
                <div class="card-body">
                  <h3 class="text-lg font-semibold mb-3">بطاقة بارزة</h3>
                  <p class="text-sm text-secondary">هذه بطاقة بارزة مع ظل أكبر وتأثير hover محسّن.</p>
                </div>
              </div>
            </div>
            <div class="span4">
              <div class="card" style="background: linear-gradient(135deg, var(--primary-500), var(--primary-600)); color: white; border: none;">
                <div class="card-body">
                  <h3 class="text-lg font-semibold mb-3">بطاقة ملونة</h3>
                  <p class="text-sm">بطاقة مع خلفية متدرجة جميلة.</p>
                  <button class="btn btn-ghost mt-3" style="color: white; border-color: white;">تفاصيل</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 7. حقول الإدخال -->
        <div class="showcase-section">
          <h2 class="showcase-title">
            <i class="fa fa-edit"></i>
            7. حقول الإدخال
          </h2>

          <div class="row-fluid">
            <div class="span6">
              <div class="mb-4">
                <label class="component-label">حقل نصي</label>
                <input type="text" placeholder="أدخل النص هنا">
              </div>
              <div class="mb-4">
                <label class="component-label">كلمة السر</label>
                <input type="password" placeholder="••••••••">
              </div>
            </div>
            <div class="span6">
              <div class="mb-4">
                <label class="component-label">قائمة منسدلة</label>
                <select>
                  <option>الخيار الأول</option>
                  <option>الخيار الثاني</option>
                  <option>الخيار الثالث</option>
                </select>
              </div>
              <div class="mb-4">
                <label class="component-label">منطقة نصية</label>
                <textarea placeholder="اكتب ملاحظاتك هنا" rows="3"></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- 8. الشارات والتنبيهات -->
        <div class="showcase-section">
          <h2 class="showcase-title">
            <i class="fa fa-tags"></i>
            8. الشارات والتنبيهات
          </h2>

          <div class="component-row">
            <span class="component-label">الشارات (Badges)</span>
            <div class="d-flex gap-3 flex-wrap">
              <span class="badge badge-primary">جديد</span>
              <span class="badge badge-success">نشط</span>
              <span class="badge badge-warning">معلق</span>
              <span class="badge badge-danger">محذوف</span>
            </div>
          </div>

          <div class="component-row">
            <span class="component-label">التنبيهات (Alerts)</span>
            <div class="alert alert-success">
              <i class="fa fa-check-circle"></i>
              تم الحفظ بنجاح!
            </div>
            <div class="alert alert-warning">
              <i class="fa fa-exclamation-triangle"></i>
              تحذير: يرجى المراجعة قبل المتابعة.
            </div>
            <div class="alert alert-danger">
              <i class="fa fa-times-circle"></i>
              خطأ: فشلت العملية، يرجى المحاولة مرة أخرى.
            </div>
            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i>
              معلومة: يوجد تحديث جديد متاح للنظام.
            </div>
          </div>
        </div>

        <!-- 9. الجدول -->
        <div class="showcase-section">
          <h2 class="showcase-title">
            <i class="fa fa-table"></i>
            9. الجداول
          </h2>

          <table>
            <thead>
              <tr>
                <th>الاسم</th>
                <th>البريد الإلكتروني</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="font-semibold">أحمد محمد</td>
                <td>ahmad@example.com</td>
                <td><span class="badge badge-success">نشط</span></td>
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
                <td class="font-semibold">فاطمة أحمد</td>
                <td>fatma@example.com</td>
                <td><span class="badge badge-warning">معلق</span></td>
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
                <td class="font-semibold">محمد علي</td>
                <td>mohamed@example.com</td>
                <td><span class="badge badge-success">نشط</span></td>
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

        <!-- 10. مثال عملي شامل -->
        <div class="showcase-section">
          <h2 class="showcase-title">
            <i class="fa fa-rocket"></i>
            10. مثال عملي شامل
          </h2>

          <!-- شريط أدوات -->
          <div class="d-flex justify-between items-center mb-6 p-4" style="background: var(--bg-secondary); border-radius: var(--radius-lg);">
            <div>
              <h3 class="text-lg font-semibold mb-0">قائمة العملاء</h3>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-primary">
                <i class="fa fa-plus"></i>
                إضافة عميل
              </button>
              <button class="btn btn-outline">
                <i class="fa fa-filter"></i>
                تصفية
              </button>
              <button class="btn btn-ghost btn-icon-only">
                <i class="fa fa-refresh"></i>
              </button>
            </div>
          </div>

          <!-- إحصائيات سريعة -->
          <div class="row-fluid mb-6">
            <div class="span3">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-between items-center mb-3">
                    <span class="text-sm text-tertiary">إجمالي العملاء</span>
                    <span class="badge badge-primary">+12%</span>
                  </div>
                  <h3 class="text-2xl font-bold mb-0">1,524</h3>
                </div>
              </div>
            </div>
            <div class="span3">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-between items-center mb-3">
                    <span class="text-sm text-tertiary">عملاء نشطين</span>
                    <span class="badge badge-success">+8%</span>
                  </div>
                  <h3 class="text-2xl font-bold mb-0">982</h3>
                </div>
              </div>
            </div>
            <div class="span3">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-between items-center mb-3">
                    <span class="text-sm text-tertiary">عملاء جدد</span>
                    <span class="badge badge-info">+24</span>
                  </div>
                  <h3 class="text-2xl font-bold mb-0">124</h3>
                </div>
              </div>
            </div>
            <div class="span3">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-between items-center mb-3">
                    <span class="text-sm text-tertiary">معلق</span>
                    <span class="badge badge-warning">-3%</span>
                  </div>
                  <h3 class="text-2xl font-bold mb-0">48</h3>
                </div>
              </div>
            </div>
          </div>

          <!-- نموذج -->
          <div class="card">
            <div class="card-header">
              <h4 class="mb-0">إضافة عميل جديد</h4>
            </div>
            <div class="card-body">
              <div class="row-fluid">
                <div class="span6 mb-4">
                  <label class="component-label">الاسم الكامل</label>
                  <input type="text" placeholder="أدخل الاسم الكامل">
                </div>
                <div class="span6 mb-4">
                  <label class="component-label">البريد الإلكتروني</label>
                  <input type="email" placeholder="example@domain.com">
                </div>
                <div class="span6 mb-4">
                  <label class="component-label">رقم الهاتف</label>
                  <input type="tel" placeholder="+966 XX XXX XXXX">
                </div>
                <div class="span6 mb-4">
                  <label class="component-label">المدينة</label>
                  <select>
                    <option>الرياض</option>
                    <option>جدة</option>
                    <option>الدمام</option>
                  </select>
                </div>
                <div class="span12 mb-4">
                  <label class="component-label">ملاحظات</label>
                  <textarea placeholder="ملاحظات إضافية (اختياري)" rows="3"></textarea>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <div class="d-flex justify-end gap-3">
                <button class="btn btn-ghost">إلغاء</button>
                <button class="btn btn-primary">
                  <i class="fa fa-save"></i>
                  حفظ العميل
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Info -->
        <div class="card" style="background: linear-gradient(135deg, var(--primary-50), var(--secondary-50)); border: none; text-align: center;">
          <div class="card-body py-6">
            <h3 class="text-xl font-bold mb-3">
              <i class="fa fa-check-circle text-success"></i>
              نظام التصميم v3.0 جاهز للاستخدام!
            </h3>
            <p class="text-secondary mb-4">
              جميع المكونات المذكورة أعلاه جاهزة للاستخدام في أي صفحة من صفحات النظام.
            </p>
            <div class="d-flex justify-center gap-3">
              <a href="DESIGN_SYSTEM_GUIDE.md" class="btn btn-primary" target="_blank">
                <i class="fa fa-book"></i>
                دليل الاستخدام
              </a>
              <a href="index.php" class="btn btn-outline">
                <i class="fa fa-home"></i>
                العودة للرئيسية
              </a>
            </div>
          </div>
        </div>

      </div><!--/.content-->
    </div><!--/.row-fluid-->
  </div><!--/.container-fluid-->

  <?php require_once("script.php"); ?>
</body>

</html>