<?php
session_start();

// Check if user is logged in
// if (!isset($_SESSION['userlogged'])) {
//     header('Location: login.php');
//     exit;
// }

// Page configurations
$page_title = 'لوحة التحكم - WAM Tech POS';
$additional_css = ['css/dashboard-charts.css']; // Optional additional CSS
?>

<?php include 'modern_header.php'; ?>

<!-- ═══════════════════════════════════════════════════════
     Dashboard Content
     ═══════════════════════════════════════════════════════ -->

<!-- Page Title -->
<div class="page-title admin-fade-in-up">
  <div class="page-title-icon">
    <i class="fas fa-tachometer-alt"></i>
  </div>
  <div>
    <h1 class="mb-0">لوحة التحكم</h1>
    <p class="text-muted mb-0">نظرة عامة على أداء النظام</p>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
  <!-- Sales Today -->
  <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="admin-stat-card stat-primary admin-fade-in-up" style="animation-delay: 0.1s;">
      <div class="admin-stat-header">
        <div>
          <div class="admin-stat-label">مبيعات اليوم</div>
          <div class="admin-stat-value">45,280</div>
          <div class="text-muted" style="font-size: 0.875rem;">ريال سعودي</div>
        </div>
        <div class="admin-stat-icon">
          <i class="fas fa-shopping-cart"></i>
        </div>
      </div>
      <div class="admin-stat-trend up">
        <i class="fas fa-arrow-up"></i>
        <span>12.5% مقارنة بالأمس</span>
      </div>
    </div>
  </div>

  <!-- Orders Today -->
  <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="admin-stat-card stat-success admin-fade-in-up" style="animation-delay: 0.2s;">
      <div class="admin-stat-header">
        <div>
          <div class="admin-stat-label">عدد الطلبات</div>
          <div class="admin-stat-value">156</div>
          <div class="text-muted" style="font-size: 0.875rem;">طلب</div>
        </div>
        <div class="admin-stat-icon">
          <i class="fas fa-receipt"></i>
        </div>
      </div>
      <div class="admin-stat-trend up">
        <i class="fas fa-arrow-up"></i>
        <span>8.2% مقارنة بالأمس</span>
      </div>
    </div>
  </div>

  <!-- New Customers -->
  <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="admin-stat-card stat-info admin-fade-in-up" style="animation-delay: 0.3s;">
      <div class="admin-stat-header">
        <div>
          <div class="admin-stat-label">عملاء جدد</div>
          <div class="admin-stat-value">28</div>
          <div class="text-muted" style="font-size: 0.875rem;">عميل</div>
        </div>
        <div class="admin-stat-icon">
          <i class="fas fa-users"></i>
        </div>
      </div>
      <div class="admin-stat-trend up">
        <i class="fas fa-arrow-up"></i>
        <span>15% هذا الأسبوع</span>
      </div>
    </div>
  </div>

  <!-- Low Stock -->
  <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="admin-stat-card stat-warning admin-fade-in-up" style="animation-delay: 0.4s;">
      <div class="admin-stat-header">
        <div>
          <div class="admin-stat-label">تنبيه مخزون</div>
          <div class="admin-stat-value">12</div>
          <div class="text-muted" style="font-size: 0.875rem;">منتج</div>
        </div>
        <div class="admin-stat-icon">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
      </div>
      <div class="admin-stat-trend down">
        <i class="fas fa-arrow-down"></i>
        <span>يحتاج إعادة طلب</span>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
  <!-- Sales Chart -->
  <div class="col-xl-8">
    <div class="admin-card admin-slide-in-right">
      <div class="admin-card-header">
        <h3 class="admin-card-title">
          <i class="fas fa-chart-line"></i>
          مبيعات آخر 7 أيام
        </h3>
        <div class="btn-group admin-card-actions" role="group">
          <button type="button" class="admin-btn admin-btn-sm admin-btn-primary">أسبوع</button>
          <button type="button" class="admin-btn admin-btn-sm admin-btn-outline-primary">شهر</button>
          <button type="button" class="admin-btn admin-btn-sm admin-btn-outline-primary">سنة</button>
        </div>
      </div>
      <div class="admin-card-body">
        <canvas id="salesChart" height="300"></canvas>
      </div>
    </div>
  </div>

  <!-- Top Products -->
  <div class="col-xl-4">
    <div class="admin-card admin-slide-in-right" style="animation-delay: 0.1s;">
      <div class="admin-card-header">
        <h3 class="admin-card-title">
          <i class="fas fa-star"></i>
          المنتجات الأكثر مبيعاً
        </h3>
      </div>
      <div class="admin-card-body">
        <ul class="list-unstyled mb-0">
          <li class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                <i class="fas fa-laptop"></i>
              </div>
              <div>
                <div class="fw-semibold">لاب توب HP</div>
                <small class="text-muted">45 مبيعات</small>
              </div>
            </div>
            <div class="text-success fw-bold">25,600 ر.س</div>
          </li>
          <li class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-success bg-opacity-10 text-success rounded p-2">
                <i class="fas fa-mobile"></i>
              </div>
              <div>
                <div class="fw-semibold">آيفون 15 برو</div>
                <small class="text-muted">38 مبيعات</small>
              </div>
            </div>
            <div class="text-success fw-bold">42,800 ر.س</div>
          </li>
          <li class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-info bg-opacity-10 text-info rounded p-2">
                <i class="fas fa-headphones"></i>
              </div>
              <div>
                <div class="fw-semibold">سماعات AirPods</div>
                <small class="text-muted">32 مبيعات</small>
              </div>
            </div>
            <div class="text-success fw-bold">8,900 ر.س</div>
          </li>
          <li class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-warning bg-opacity-10 text-warning rounded p-2">
                <i class="fas fa-keyboard"></i>
              </div>
              <div>
                <div class="fw-semibold">كيبورد لوجيتك</div>
                <small class="text-muted">28 مبيعات</small>
              </div>
            </div>
            <div class="text-success fw-bold">3,200 ر.س</div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Recent Activities & Quick Actions -->
<div class="row g-4">
  <!-- Recent Activities -->
  <div class="col-xl-8">
    <div class="admin-card admin-fade-in-up">
      <div class="admin-card-header">
        <h3 class="admin-card-title">
          <i class="fas fa-history"></i>
          النشاطات الأخيرة
        </h3>
        <a href="#" class="admin-btn admin-btn-sm admin-btn-outline-primary">
          عرض الكل
        </a>
      </div>
      <div class="admin-card-body">
        <div class="admin-table-wrapper">
          <table class="admin-table">
            <thead>
              <tr>
                <th>الوقت</th>
                <th>العملية</th>
                <th>المستخدم</th>
                <th>المبلغ</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <small class="text-muted">منذ دقيقتين</small>
                </td>
                <td>
                  <i class="fas fa-shopping-cart text-primary me-2"></i>
                  فاتورة مبيعات #1245
                </td>
                <td>أحمد محمد</td>
                <td class="fw-bold text-success">2,500 ر.س</td>
                <td>
                  <span class="admin-badge admin-badge-success">
                    <i class="fas fa-check"></i>
                    مكتمل
                  </span>
                </td>
                <td>
                  <div class="admin-table-actions">
                    <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                      data-bs-toggle="tooltip" title="عرض">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                      data-bs-toggle="tooltip" title="طباعة">
                      <i class="fas fa-print"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <small class="text-muted">منذ 5 دقائق</small>
                </td>
                <td>
                  <i class="fas fa-user-plus text-success me-2"></i>
                  عميل جديد
                </td>
                <td>سارة أحمد</td>
                <td>-</td>
                <td>
                  <span class="admin-badge admin-badge-info">
                    <i class="fas fa-info-circle"></i>
                    جديد
                  </span>
                </td>
                <td>
                  <div class="admin-table-actions">
                    <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary">
                      <i class="fas fa-edit"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <small class="text-muted">منذ 15 دقيقة</small>
                </td>
                <td>
                  <i class="fas fa-box text-warning me-2"></i>
                  إضافة منتج جديد
                </td>
                <td>محمد علي</td>
                <td>-</td>
                <td>
                  <span class="admin-badge admin-badge-success">
                    <i class="fas fa-check"></i>
                    مكتمل
                  </span>
                </td>
                <td>
                  <div class="admin-table-actions">
                    <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary">
                      <i class="fas fa-eye"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="col-xl-4">
    <div class="admin-card admin-fade-in-up" style="animation-delay: 0.1s;">
      <div class="admin-card-header">
        <h3 class="admin-card-title">
          <i class="fas fa-bolt"></i>
          إجراءات سريعة
        </h3>
      </div>
      <div class="admin-card-body">
        <div class="d-grid gap-3">
          <a href="add_saleInvoice.php" class="admin-btn admin-btn-primary">
            <i class="fas fa-plus-circle"></i>
            فاتورة مبيعات جديدة
          </a>
          <a href="add_purchase.php" class="admin-btn admin-btn-success">
            <i class="fas fa-shopping-bag"></i>
            فاتورة مشتريات جديدة
          </a>
          <a href="add_customer.php" class="admin-btn admin-btn-info">
            <i class="fas fa-user-plus"></i>
            إضافة عميل جديد
          </a>
          <a href="add_item.php" class="admin-btn admin-btn-warning">
            <i class="fas fa-cube"></i>
            إضافة منتج جديد
          </a>
          <a href="saleInvoice-report.php" class="admin-btn admin-btn-outline-primary">
            <i class="fas fa-chart-bar"></i>
            تقرير المبيعات
          </a>
          <a href="store-inventory.php" class="admin-btn admin-btn-outline-primary">
            <i class="fas fa-warehouse"></i>
            جرد المخزون
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
// Inline scripts for charts
$inline_scripts = "
// Sales Chart Configuration
const salesCtx = document.getElementById('salesChart');
if (salesCtx) {
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
            datasets: [{
                label: 'المبيعات (ريال)',
                data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                borderColor: '#32C2CD',
                backgroundColor: 'rgba(50, 194, 205, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#32C2CD',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        family: 'Cairo'
                    },
                    bodyFont: {
                        size: 13,
                        family: 'Cairo'
                    },
                    rtl: true,
                    textDirection: 'rtl'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('ar-SA') + ' ر.س';
                        },
                        font: {
                            family: 'Cairo'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: 'Cairo'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Example: Show welcome notification
setTimeout(() => {
    AdminNotify.success('مرحباً بك في لوحة التحكم!');
}, 500);
";

include 'modern_footer.php';
?>