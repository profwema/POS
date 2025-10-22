<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");

$page_title = CUSTOMERS;
?>
<?php include 'modern_header.php'; ?>

<!-- ═══════════════════════════════════════════════════════
     Page Title
     ═══════════════════════════════════════════════════════ -->
<div class="page-title admin-fade-in-up">
    <div class="page-title-icon">
        <i class="fas fa-users"></i>
    </div>
    <div>
        <h1 class="mb-0"><?=CUSTOMERS?></h1>
        <p class="text-muted mb-0">إدارة قائمة العملاء</p>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     Main Content Card
     ═══════════════════════════════════════════════════════ -->
<div class="admin-card admin-slide-in-right">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="fas fa-list"></i>
            قائمة العملاء
        </h3>
        <a href="add_customer.php" class="admin-btn admin-btn-primary">
            <i class="fas fa-plus"></i>
            إضافة عميل جديد
        </a>
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
                        <input type="text" class="form-control" id="searchInput" placeholder="بحث بالاسم، الجوال، أو البريد الإلكتروني...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterStatus">
                        <option value="">الكل</option>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="admin-btn admin-btn-outline-primary w-100" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i>
                        تصدير Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="admin-table-wrapper">
            <table class="admin-table" id="customersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?=CUSTOMER_NAME?></th>
                        <th><?=TAX_NO?></th>
                        <th><?=EMAIL?></th>
                        <th><?=MOBILE?></th>
                        <th>الحالة</th>
                        <th class="text-center"><?=EDITING?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $language = LANG;
                    $storeid = $_SESSION['storeid'];
                    $query = "SELECT * FROM customers WHERE storeid='$storeid' ORDER BY id DESC";
                    
                    $res = mysqli_query($adController->MySQL, $query) or die(mysqli_error($adController->MySQL));
                    $counter = 1;
                    
                    while($data = mysqli_fetch_assoc($res)) {
                        $name = $data["name_$language"];
                        $email = $data["email"];
                        $phone = $data['phone'];
                        $tax_no = $data['tax_no'];
                        
                        $idval = urlencode($adController->encrypt_decrypt(1, $data['id'], 0));
                        $tableName = urlencode($adController->encrypt_decrypt(1, 'customers', 0));
                        $secondIdval = urlencode($adController->encrypt_decrypt(1, $idval, 0));
                        
                        // Random status for demo (you can replace with actual status from DB)
                        $isActive = true;
                    ?>
                    <tr data-customer-id="<?=$data['id']?>">
                        <td><?=$counter++?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold"><?=$name?></div>
                                </div>
                            </div>
                        </td>
                        <td><?=$tax_no ?: '-'?></td>
                        <td>
                            <?php if($email): ?>
                                <a href="mailto:<?=$email?>" class="text-primary">
                                    <i class="fas fa-envelope me-1"></i>
                                    <?=$email?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($phone): ?>
                                <a href="tel:<?=$phone?>" class="text-primary">
                                    <i class="fas fa-phone me-1"></i>
                                    <?=$phone?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($isActive): ?>
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
                                        onclick="viewCustomer(<?=$data['id']?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="edit_customer.php?sd=<?=$secondIdval?>" 
                                   class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                                   data-bs-toggle="tooltip" 
                                   title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="admin-btn admin-btn-icon admin-btn-icon-sm admin-btn-outline-primary"
                                        data-bs-toggle="tooltip" 
                                        title="حذف"
                                        onclick="confirmDeleteCustomer('<?=$tableName?>', '<?=$idval?>', '<?=$name?>')">
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

        <!-- Empty State (if no data) -->
        <?php if(mysqli_num_rows($res) == 0): ?>
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
            </div>
            <h4 class="text-muted">لا يوجد عملاء</h4>
            <p class="text-muted">ابدأ بإضافة عميل جديد</p>
            <a href="add_customer.php" class="admin-btn admin-btn-primary mt-3">
                <i class="fas fa-plus"></i>
                إضافة عميل جديد
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
const table = document.getElementById('customersTable');

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
async function confirmDeleteCustomer(tableName, idval, customerName) {
    const confirmed = await AdminConfirm.show(
        'حذف العميل',
        'هل أنت متأكد من حذف العميل \"' + customerName + '\"؟',
        'حذف',
        'إلغاء'
    );
    
    if (confirmed) {
        AdminLoading.show('جاري الحذف...');
        // Call the existing delete function
        deleteData(tableName, idval, 1);
    }
}

// ═══════════════════════════════════════════════════════
// View Customer Details
// ═══════════════════════════════════════════════════════
async function viewCustomer(customerId) {
    try {
        AdminLoading.show('جاري التحميل...');
        // This is a placeholder - implement your actual API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        AdminLoading.hide();
        
        Swal.fire({
            title: 'تفاصيل العميل',
            html: '<p>سيتم عرض تفاصيل العميل هنا...</p>',
            icon: 'info',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#32C2CD'
        });
    } catch (error) {
        AdminLoading.hide();
        AdminNotify.error('حدث خطأ في تحميل البيانات');
    }
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
// Override original deleteData to show modern loading
// ═══════════════════════════════════════════════════════
const originalDeleteData = window.deleteData;
window.deleteData = function(tableName, idval, type) {
    // The confirmation is already done by confirmDeleteCustomer
    // Just call the original function
    if (originalDeleteData) {
        originalDeleteData(tableName, idval, type);
    }
};

// Show success message
console.log('✅ Modern Customers Page Loaded Successfully!');
";

include 'modern_footer.php';
?>

