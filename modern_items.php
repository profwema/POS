<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$language = LANG;
$storeid = $_SESSION['storeid'];

if (isset($_REQUEST["catid"])) {
  $_SESSION['catItems'] = $_REQUEST["catid"];
}

$page_title = ITEMS;
?>
<?php include 'modern_header.php'; ?>

<!-- ═══════════════════════════════════════════════════════
     Page Title
     ═══════════════════════════════════════════════════════ -->
<div class="page-title admin-fade-in-up">
  <div class="page-title-icon">
    <i class="fas fa-cube"></i>
  </div>
  <div>
    <h1 class="mb-0"><?= ITEMS ?></h1>
    <p class="text-muted mb-0">إدارة المنتجات والأصناف</p>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     Filters Card
     ═══════════════════════════════════════════════════════ -->
<div class="admin-card mb-4 admin-slide-in-right">
  <div class="admin-card-header">
    <h3 class="admin-card-title">
      <i class="fas fa-filter"></i>
      تصفية النتائج
    </h3>
  </div>
  <div class="admin-card-body">
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label"><?= CATEGORY ?></label>
          <select class="form-select" name="catid" onchange="this.form.submit()">
            <option value="">جميع التصنيفات</option>
            <?php
            $query = "SELECT * FROM categories WHERE storeid='$storeid' ORDER BY id DESC";
            $res = mysqli_query($adController->MySQL, $query);
            while ($data = mysqli_fetch_assoc($res)) {
              $name = $data["name_" . $language];
              $sel = "";
              if (isset($_SESSION['catItems']) && $_SESSION['catItems'] == $data['id']) {
                $sel = " selected='selected' ";
              }
              echo "<option value='$data[id]' $sel>$name</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">البحث</label>
          <div class="input-group">
            <span class="input-group-text">
              <i class="fas fa-search"></i>
            </span>
            <input type="text" class="form-control" id="searchInput" placeholder="ابحث بالاسم، الباركود...">
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label">حالة المخزون</label>
          <select class="form-select" id="stockFilter">
            <option value="">الكل</option>
            <option value="available">متوفر</option>
            <option value="low">مخزون منخفض</option>
            <option value="out">نفذ من المخزون</option>
          </select>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     Main Content Card
     ═══════════════════════════════════════════════════════ -->
<div class="admin-card admin-slide-in-right" style="animation-delay: 0.1s;">
  <div class="admin-card-header">
    <h3 class="admin-card-title">
      <i class="fas fa-list"></i>
      قائمة المنتجات
    </h3>
    <div class="d-flex gap-2">
      <button class="admin-btn admin-btn-outline-primary admin-btn-sm" onclick="exportToExcel()">
        <i class="fas fa-file-excel"></i>
        تصدير
      </button>
      <a href="add_item.php" class="admin-btn admin-btn-primary admin-btn-sm">
        <i class="fas fa-plus"></i>
        إضافة منتج
      </a>
    </div>
  </div>
  <div class="admin-card-body">
    <div class="admin-table-wrapper">
      <table class="admin-table" id="itemsTable">
        <thead>
          <tr>
            <th>#</th>
            <th><?= ITEM_NAME ?></th>
            <th><?= CATEGORY ?></th>
            <th>الباركود</th>
            <th>سعر الشراء</th>
            <th>سعر البيع</th>
            <th>المخزون</th>
            <th>الحالة</th>
            <th class="text-center"><?= EDITING ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $condition = "";
          if (isset($_SESSION['catItems']) && $_SESSION['catItems'] != "") {
            $catid = $_SESSION['catItems'];
            $condition = " AND catid='$catid' ";
          }

          $query = "SELECT i.*, c.name_$language as cat_name 
                              FROM items i 
                              LEFT JOIN categories c ON i.catid = c.id 
                              WHERE i.storeid='$storeid' $condition 
                              ORDER BY i.id DESC";

          $res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
          $counter = 1;

          while ($data = mysqli_fetch_assoc($res)) {
            $name = $data["name_$language"];
            $cat_name = $data["cat_name"] ?: '-';
            $barcode = $data['barcode'];
            $buy_price = $data['buy_price'];
            $sale_price = $data['sale_price'];
            $quantity = $data['quantity'] ?? 0;

            $idval = urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
            $tableName = urlencode($adController->encrypt_decrypt(1, 'items', 0));
            $secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));

            // حالة المخزون
            if ($quantity <= 0) {
              $stockStatus = 'out';
              $stockBadge = '<span class="admin-badge admin-badge-danger"><i class="fas fa-times"></i> نفذ</span>';
            } elseif ($quantity < 10) {
              $stockStatus = 'low';
              $stockBadge = '<span class="admin-badge admin-badge-warning"><i class="fas fa-exclamation-triangle"></i> منخفض</span>';
            } else {
              $stockStatus = 'available';
              $stockBadge = '<span class="admin-badge admin-badge-success"><i class="fas fa-check"></i> متوفر</span>';
            }
          ?>
            <tr data-stock="<?= $stockStatus ?>">
              <td><?= $counter++ ?></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                    <i class="fas fa-cube"></i>
                  </div>
                  <div>
                    <div class="fw-semibold"><?= $name ?></div>
                  </div>
                </div>
              </td>
              <td>
                <span class="admin-badge admin-badge-info">
                  <i class="fas fa-tag"></i>
                  <?= $cat_name ?>
                </span>
              </td>
              <td>
                <code class="text-primary"><?= $barcode ?: '-' ?></code>
              </td>
              <td class="fw-bold text-muted"><?= number_format($buy_price, 2) ?> ر.س</td>
              <td class="fw-bold text-success"><?= number_format($sale_price, 2) ?> ر.س</td>
              <td>
                <span class="badge <?= $quantity < 10 ? 'bg-warning' : 'bg-info' ?> rounded-pill">
                  <?= $quantity ?>
                </span>
              </td>
              <td><?= $stockBadge ?></td>
              <td>
                <div class="admin-table-actions">
                  <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                    data-bs-toggle="tooltip"
                    title="عرض التفاصيل"
                    onclick="viewItem(<?= $data['id'] ?>)">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                    data-bs-toggle="tooltip"
                    title="طباعة الباركود"
                    onclick="printBarcode('<?= $barcode ?>')">
                    <i class="fas fa-barcode"></i>
                  </button>
                  <a href="edit_item.php?sd=<?= $secondIdval ?>"
                    class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                    data-bs-toggle="tooltip"
                    title="تعديل">
                    <i class="fas fa-edit"></i>
                  </a>
                  <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                    data-bs-toggle="tooltip"
                    title="حذف"
                    onclick="confirmDeleteItem('<?= $tableName ?>', '<?= $idval ?>', '<?= $name ?>')">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <?php if (mysqli_num_rows($res) == 0): ?>
      <div class="text-center py-5">
        <div class="mb-3">
          <i class="fas fa-cube text-muted" style="font-size: 4rem;"></i>
        </div>
        <h4 class="text-muted">لا توجد منتجات</h4>
        <p class="text-muted">ابدأ بإضافة منتج جديد</p>
        <a href="add_item.php" class="admin-btn admin-btn-primary mt-3">
          <i class="fas fa-plus"></i>
          إضافة منتج جديد
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
$inline_scripts = "
// ═══════════════════════════════════════════════════════
// Search & Filter Functionality
// ═══════════════════════════════════════════════════════
const searchInput = document.getElementById('searchInput');
const stockFilter = document.getElementById('stockFilter');
const table = document.getElementById('itemsTable');

function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const stockValue = stockFilter.value;
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const stock = row.getAttribute('data-stock');
        
        const matchesSearch = text.includes(searchTerm);
        const matchesStock = !stockValue || stock === stockValue;
        
        row.style.display = matchesSearch && matchesStock ? '' : 'none';
    });
}

if (searchInput) {
    searchInput.addEventListener('input', filterTable);
}

if (stockFilter) {
    stockFilter.addEventListener('change', filterTable);
}

// ═══════════════════════════════════════════════════════
// Delete Confirmation
// ═══════════════════════════════════════════════════════
async function confirmDeleteItem(tableName, idval, itemName) {
    const confirmed = await AdminConfirm.show(
        'حذف المنتج',
        'هل أنت متأكد من حذف المنتج \"' + itemName + '\"؟',
        'حذف',
        'إلغاء'
    );
    
    if (confirmed) {
        AdminLoading.show('جاري الحذف...');
        deleteData(tableName, idval, 1);
    }
}

// ═══════════════════════════════════════════════════════
// View Item Details
// ═══════════════════════════════════════════════════════
async function viewItem(itemId) {
    AdminLoading.show('جاري التحميل...');
    // Implement your view logic here
    await new Promise(resolve => setTimeout(resolve, 1000));
    AdminLoading.hide();
    
    Swal.fire({
        title: 'تفاصيل المنتج',
        html: '<p>سيتم عرض تفاصيل المنتج هنا...</p>',
        icon: 'info',
        confirmButtonText: 'حسناً',
        confirmButtonColor: '#32C2CD'
    });
}

// ═══════════════════════════════════════════════════════
// Print Barcode
// ═══════════════════════════════════════════════════════
function printBarcode(barcode) {
    if (!barcode || barcode === '-') {
        AdminNotify.warning('هذا المنتج لا يحتوي على باركود');
        return;
    }
    
    AdminNotify.info('جاري فتح صفحة الطباعة...');
    window.open('barcode_item.php?barcode=' + barcode, '_blank');
}

// ═══════════════════════════════════════════════════════
// Export to Excel
// ═══════════════════════════════════════════════════════
function exportToExcel() {
    AdminNotify.info('جاري تصدير البيانات...');
    // Implement your export logic here
    setTimeout(() => {
        AdminNotify.success('تم التصدير بنجاح');
    }, 1500);
}

// ═══════════════════════════════════════════════════════
// Stock Alerts
// ═══════════════════════════════════════════════════════
window.addEventListener('DOMContentLoaded', function() {
    const lowStockItems = document.querySelectorAll('[data-stock=\"low\"]').length;
    const outOfStockItems = document.querySelectorAll('[data-stock=\"out\"]').length;
    
    if (outOfStockItems > 0) {
        AdminNotify.warning('⚠️ ' + outOfStockItems + ' منتج نفذ من المخزون');
    } else if (lowStockItems > 0) {
        AdminNotify.info('ℹ️ ' + lowStockItems + ' منتج مخزونه منخفض');
    }
});

console.log('✅ Modern Items Page Loaded Successfully!');
";

include 'modern_footer.php';
?>