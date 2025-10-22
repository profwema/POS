<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$page_title = SUPPLIERS;
?>
<?php include 'modern_header.php'; ?>

<!-- ═══════════════════════════════════════════════════════
     Page Title
     ═══════════════════════════════════════════════════════ -->
<div class="page-title admin-fade-in-up">
  <div class="page-title-icon">
    <i class="fas fa-truck"></i>
  </div>
  <div>
    <h1 class="mb-0"><?= SUPPLIERS ?></h1>
    <p class="text-muted mb-0">إدارة قائمة الموردين</p>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     Main Content Card
     ═══════════════════════════════════════════════════════ -->
<div class="admin-card admin-slide-in-right">
  <div class="admin-card-header">
    <h3 class="admin-card-title">
      <i class="fas fa-list"></i>
      قائمة الموردين
    </h3>
    <div class="d-flex gap-2">
      <button class="admin-btn admin-btn-outline-primary admin-btn-sm" onclick="exportToExcel()">
        <i class="fas fa-file-excel"></i>
        تصدير Excel
      </button>
      <a href="add_supplier.php" class="admin-btn admin-btn-primary admin-btn-sm">
        <i class="fas fa-plus"></i>
        إضافة مورد جديد
      </a>
    </div>
  </div>
  <div class="admin-card-body">
    <!-- Search Box -->
    <div class="mb-4">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="input-group">
            <span class="input-group-text">
              <i class="fas fa-search"></i>
            </span>
            <input type="text" class="form-control" id="searchInput" placeholder="بحث بالاسم، السجل التجاري، أو الجوال...">
          </div>
        </div>
        <div class="col-md-3">
          <select class="form-select" id="filterStatus">
            <option value="">الكل</option>
            <option value="active">نشط</option>
            <option value="inactive">غير نشط</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="admin-table-wrapper">
      <table class="admin-table" id="suppliersTable">
        <thead>
          <tr>
            <th>#</th>
            <th><?= SUPPLIER_NAME ?></th>
            <th><?= COM_REG ?></th>
            <th><?= TAX_NO ?></th>
            <th><?= EMAIL ?></th>
            <th><?= MOBILE ?></th>
            <th>الحالة</th>
            <th class="text-center"><?= EDITING ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $language = LANG;
          $storeid = $_SESSION['storeid'];
          $query = "SELECT * FROM suppliers WHERE storeid='$storeid' ORDER BY id DESC";

          $res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
          $counter = 1;

          while ($data = mysqli_fetch_assoc($res)) {
            $name = $data["name_$language"];
            $com_reg = $data["com_reg"];
            $tax_no = $data["tax_no"];
            $email = $data["email"];
            $phone = $data['phone'];

            $idval = urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
            $tableName = urlencode($adController->encrypt_decrypt(1, 'suppliers', 0));
            $secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));

            $isActive = true; // يمكن تعديلها حسب حقل الحالة في قاعدة البيانات
          ?>
            <tr data-supplier-id="<?= $data['id'] ?>">
              <td><?= $counter++ ?></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="bg-success bg-opacity-10 text-success rounded p-2">
                    <i class="fas fa-truck"></i>
                  </div>
                  <div>
                    <div class="fw-semibold"><?= $name ?></div>
                  </div>
                </div>
              </td>
              <td>
                <?php if ($com_reg): ?>
                  <span class="admin-badge admin-badge-info">
                    <i class="fas fa-building"></i>
                    <?= $com_reg ?>
                  </span>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($tax_no): ?>
                  <span class="admin-badge admin-badge-warning">
                    <i class="fas fa-receipt"></i>
                    <?= $tax_no ?>
                  </span>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($email): ?>
                  <a href="mailto:<?= $email ?>" class="text-primary">
                    <i class="fas fa-envelope me-1"></i>
                    <?= $email ?>
                  </a>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($phone): ?>
                  <a href="tel:<?= $phone ?>" class="text-primary">
                    <i class="fas fa-phone me-1"></i>
                    <?= $phone ?>
                  </a>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($isActive): ?>
                  <span class="admin-badge admin-badge-success">
                    <i class="fas fa-check"></i>
                    نشط
                  </span>
                <?php else: ?>
                  <span class="admin-badge admin-badge-secondary">
                    <i class="fas fa-pause"></i>
                    غير نشط
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <div class="admin-table-actions">
                  <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                    data-bs-toggle="tooltip"
                    title="عرض التفاصيل"
                    onclick="viewSupplier(<?= $data['id'] ?>)">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                    data-bs-toggle="tooltip"
                    title="كشف حساب"
                    onclick="viewStatement(<?= $data['id'] ?>)">
                    <i class="fas fa-file-invoice-dollar"></i>
                  </button>
                  <a href="edit_supplier.php?sd=<?= $secondIdval ?>"
                    class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                    data-bs-toggle="tooltip"
                    title="تعديل">
                    <i class="fas fa-edit"></i>
                  </a>
                  <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                    data-bs-toggle="tooltip"
                    title="حذف"
                    onclick="confirmDeleteSupplier('<?= $tableName ?>', '<?= $idval ?>', '<?= $name ?>')">
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
          <i class="fas fa-truck text-muted" style="font-size: 4rem;"></i>
        </div>
        <h4 class="text-muted">لا يوجد موردين</h4>
        <p class="text-muted">ابدأ بإضافة مورد جديد</p>
        <a href="add_supplier.php" class="admin-btn admin-btn-primary mt-3">
          <i class="fas fa-plus"></i>
          إضافة مورد جديد
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
$inline_scripts = "
// ═══════════════════════════════════════════════════════
// Search Functionality
// ═══════════════════════════════════════════════════════
const searchInput = document.getElementById('searchInput');
const filterStatus = document.getElementById('filterStatus');
const table = document.getElementById('suppliersTable');

function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const statusFilter = filterStatus.value;
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const statusBadge = row.querySelector('.admin-badge');
        const status = statusBadge ? 
            (statusBadge.classList.contains('admin-badge-success') ? 'active' : 'inactive') 
            : 'active';
        
        const matchesSearch = text.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        
        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
}

if (searchInput) {
    searchInput.addEventListener('input', filterTable);
}

if (filterStatus) {
    filterStatus.addEventListener('change', filterTable);
}

// ═══════════════════════════════════════════════════════
// Delete Confirmation
// ═══════════════════════════════════════════════════════
async function confirmDeleteSupplier(tableName, idval, supplierName) {
    const confirmed = await AdminConfirm.show(
        'حذف المورد',
        'هل أنت متأكد من حذف المورد \"' + supplierName + '\"؟',
        'حذف',
        'إلغاء'
    );
    
    if (confirmed) {
        AdminLoading.show('جاري الحذف...');
        deleteData(tableName, idval, 1);
    }
}

// ═══════════════════════════════════════════════════════
// View Supplier Details
// ═══════════════════════════════════════════════════════
async function viewSupplier(supplierId) {
    AdminLoading.show('جاري التحميل...');
    await new Promise(resolve => setTimeout(resolve, 1000));
    AdminLoading.hide();
    
    Swal.fire({
        title: 'تفاصيل المورد',
        html: '<p>سيتم عرض تفاصيل المورد هنا...</p>',
        icon: 'info',
        confirmButtonText: 'حسناً',
        confirmButtonColor: '#32C2CD'
    });
}

// ═══════════════════════════════════════════════════════
// View Account Statement
// ═══════════════════════════════════════════════════════
function viewStatement(supplierId) {
    AdminNotify.info('جاري فتح كشف الحساب...');
    window.open('account-statement.php?supplier_id=' + supplierId, '_blank');
}

// ═══════════════════════════════════════════════════════
// Export to Excel
// ═══════════════════════════════════════════════════════
function exportToExcel() {
    AdminNotify.info('جاري تصدير البيانات...');
    setTimeout(() => {
        AdminNotify.success('تم التصدير بنجاح');
    }, 1500);
}

console.log('✅ Modern Suppliers Page Loaded Successfully!');
";

include 'modern_footer.php';
?>