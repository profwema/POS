<?php
/**
 * شاشة إدارة المستخدمين والموظفين والصلاحيات - Users & RBAC Management
 * WAM Tech POS & ERP - Cyber-Luxe Arabic RTL Design
 * Author: Almubarmig (Eng. Weaam Mohammed El-Sudani)
 */
$pageTitle = "إدارة المستخدمين والموظفين وحوكمة الصلاحيات";
$currentUserId = Auth::user()['id'] ?? 0;
?>

<div class="users-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                <i class="fas fa-user-shield" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                إدارة المستخدمين والموظفين وحوكمة الصلاحيات
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0;">
                إنشاء وتأمين حسابات الكاشير والمحاسبين ومدراء الفروع وتعيين أدوارهم الوظيفية
            </p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" onclick="openAddUserModal()" style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);">
                <i class="fas fa-user-plus"></i>
                إضافة مستخدم جديد
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if ($msg === 'user_created'): ?>
        <div class="alert alert-success" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
            <span>تم إنشاء حساب المستخدم الجديد وتشفير كلمة المرور بنجاح.</span>
        </div>
    <?php elseif ($msg === 'user_updated'): ?>
        <div class="alert alert-success" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
            <span>تم تحديث بيانات المستخدم بنجاح.</span>
        </div>
    <?php elseif ($msg === 'status_updated'): ?>
        <div class="alert alert-success" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-toggle-on" style="font-size: 1.25rem;"></i>
            <span>تم تعديل حالة نشاط حساب المستخدم بنجاح.</span>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #ef4444; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-exclamation-triangle" style="font-size: 1.25rem;"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- Summary Metrics -->
    <?php
    $totalUsers = count($users);
    $activeUsers = count(array_filter($users, fn($u) => !empty($u['is_active'])));
    $inactiveUsers = $totalUsers - $activeUsers;
    ?>
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
        <div class="stat-card card" style="padding: 1.25rem;">
            <div class="stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">إجمالي المستخدمين</div>
                <div class="stat-value" style="color: #38bdf8;"><?= $totalUsers ?></div>
                <span class="stat-sub">في كافة الفروع</span>
            </div>
        </div>

        <div class="stat-card card" style="padding: 1.25rem;">
            <div class="stat-icon emerald">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">الحسابات النشطة</div>
                <div class="stat-value" style="color: #10b981;"><?= $activeUsers ?></div>
                <span class="stat-sub">مصرح لهم بالدخول</span>
            </div>
        </div>

        <div class="stat-card card" style="padding: 1.25rem;">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="fas fa-user-slash"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">الحسابات المعطلة</div>
                <div class="stat-value" style="color: #ef4444;"><?= $inactiveUsers ?></div>
                <span class="stat-sub">محظورين مؤقتاً</span>
            </div>
        </div>

        <div class="stat-card card" style="padding: 1.25rem;">
            <div class="stat-icon amber">
                <i class="fas fa-id-badge"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">الأدوار الوظيفية</div>
                <div class="stat-value" style="color: var(--brand-amber);"><?= count($roles) ?></div>
                <span class="stat-sub">مستويات الصلاحيات</span>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card" style="overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                قائمة مستخدمي النظام ومسؤولي الفروع
            </h3>
            <span class="badge badge-primary">
                <?= $totalUsers ?> مستخدم مسجل
            </span>
        </div>

        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                <thead>
                    <tr style="background: var(--bg-surface); text-align: right;">
                        <th style="padding: 1rem; width: 45px;">#</th>
                        <th style="padding: 1rem;">المستخدم</th>
                        <th style="padding: 1rem;">الدور الوظيفي والصلاحيات</th>
                        <th style="padding: 1rem;">الفرع</th>
                        <th style="padding: 1rem;">بيانات الاتصال</th>
                        <th style="padding: 1rem; text-align: center;">الحالة</th>
                        <th style="padding: 1rem; text-align: center;">آخر تسجيل دخول</th>
                        <th style="padding: 1rem; text-align: center; width: 140px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                لا يوجد مستخدمين مسجلين في النظام.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $idx => $u): ?>
                            <?php
                            $roleSlug = $u['role_slug'] ?? '';
                            $roleBadgeColor = '#38bdf8';
                            $roleBg = 'rgba(56, 189, 248, 0.12)';
                            if ($roleSlug === 'admin') {
                                $roleBadgeColor = '#f59e0b';
                                $roleBg = 'rgba(245, 158, 11, 0.15)';
                            } elseif ($roleSlug === 'accountant') {
                                $roleBadgeColor = '#10b981';
                                $roleBg = 'rgba(16, 185, 129, 0.12)';
                            } elseif ($roleSlug === 'cashier') {
                                $roleBadgeColor = '#a855f7';
                                $roleBg = 'rgba(168, 85, 247, 0.12)';
                            } elseif ($roleSlug === 'manager') {
                                $roleBadgeColor = '#38bdf8';
                                $roleBg = 'rgba(56, 189, 248, 0.12)';
                            }
                            $isSelf = ($u['id'] == $currentUserId);
                            ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.85rem;">
                                    <?= $idx + 1 ?>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <div style="font-weight: 700; color: var(--text-main);">
                                        <?= htmlspecialchars($u['full_name']) ?>
                                        <?php if ($isSelf): ?>
                                            <span class="badge badge-amber" style="font-size: 0.68rem; margin-right: 0.25rem;">أنت</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 0.78rem; color: var(--text-muted); font-family: monospace;">
                                        @<?= htmlspecialchars($u['username']) ?>
                                    </div>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span class="badge" style="background: <?= $roleBg ?>; color: <?= $roleBadgeColor ?>; font-weight: 700;">
                                        <?= htmlspecialchars($u['role_name'] ?? 'غير محدد') ?>
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem; font-size: 0.88rem; color: var(--text-muted);">
                                    <i class="fas fa-store-alt" style="margin-left: 0.25rem;"></i>
                                    <?= htmlspecialchars($u['branch_name'] ?? 'الفرع الرئيسي') ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; font-size: 0.85rem;">
                                    <div><?= htmlspecialchars($u['phone'] ?? '-') ?></div>
                                    <div style="color: var(--text-muted); font-size: 0.75rem;"><?= htmlspecialchars($u['email'] ?? '') ?></div>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center;">
                                    <?php if (!empty($u['is_active'])): ?>
                                        <span class="badge badge-emerald">نشط</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">معطل</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center; font-size: 0.82rem; font-family: monospace; color: var(--text-muted);">
                                    <?= !empty($u['last_login_at']) ? htmlspecialchars($u['last_login_at']) : 'لم يدخل بعد' ?>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.4rem; justify-content: center;">
                                        <button type="button" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;" onclick="openEditUserModal(<?= htmlspecialchars(json_encode($u)) ?>)" title="تعديل المستخدم">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if (!$isSelf): ?>
                                            <a href="<?= BASE_URL ?>users.php?action=toggle_status&id=<?= $u['id'] ?>" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; color: <?= !empty($u['is_active']) ? '#ef4444' : '#10b981' ?>; border-color: rgba(255,255,255,0.1);" onclick="return confirm('هل تريد بالتأكيد تغيير حالة هذا الحساب؟')" title="<?= !empty($u['is_active']) ? 'تعطيل الحساب' : 'تفعيل الحساب' ?>">
                                                <i class="fas fa-power-off"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add User (Explicitly Hidden with inline style) -->
<div id="addUserModal" class="modal-backdrop" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 1050; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="max-width: 650px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--text-main);">
                <i class="fas fa-user-plus" style="color: #38bdf8; margin-left: 0.5rem;"></i>
                إضافة مستخدم / موظف جديد
            </h3>
            <button type="button" onclick="closeAddUserModal()" class="btn btn-outline" style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="<?= BASE_URL ?>users.php">
            <?= Security::csrfInput() ?>
            <input type="hidden" name="action" value="store">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">الاسم الكامل للموظف *</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="مثال: أحمد محمود علي">
                </div>

                <div class="form-group">
                    <label class="form-label">اسم المستخدم للدخول (Username) *</label>
                    <input type="text" name="username" class="form-control" required placeholder="مثال: ahmed_pos" style="font-family: monospace;">
                </div>

                <div class="form-group">
                    <label class="form-label">كلمة المرور (6 خانات على الأقل) *</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••" minlength="6">
                </div>

                <div class="form-group">
                    <label class="form-label">الدور الوظيفي والصلاحيات *</label>
                    <select name="role_id" class="form-control" required>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= ($r['slug'] === 'cashier') ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الفرع التابع له *</label>
                    <select name="branch_id" class="form-control" required>
                        <?php foreach ($branches as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" placeholder="01xxxxxxxxx">
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" placeholder="user@company.com">
                </div>

                <div class="form-group">
                    <label class="form-label">حالة الحساب</label>
                    <select name="is_active" class="form-control">
                        <option value="1">مفعل (يمكنه الدخول)</option>
                        <option value="0">معطل</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeAddUserModal()" class="btn btn-outline">إلغاء</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem;">
                    <i class="fas fa-check"></i>
                    إنشاء الحساب
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit User (Explicitly Hidden with inline style) -->
<div id="editUserModal" class="modal-backdrop" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 1050; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="max-width: 650px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--text-main);">
                <i class="fas fa-user-edit" style="color: var(--brand-amber); margin-left: 0.5rem;"></i>
                تعديل بيانات المستخدم
            </h3>
            <button type="button" onclick="closeEditUserModal()" class="btn btn-outline" style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="<?= BASE_URL ?>users.php">
            <?= Security::csrfInput() ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="editUserId">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">الاسم الكامل للموظف *</label>
                    <input type="text" name="full_name" id="editFullName" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">اسم المستخدم (Username) *</label>
                    <input type="text" name="username" id="editUsername" class="form-control" required style="font-family: monospace;">
                </div>

                <div class="form-group">
                    <label class="form-label">كلمة المرور الجديدة</label>
                    <input type="password" name="password" class="form-control" placeholder="اتركه فارغاً إذا كنت لا ترغب في التغيير">
                    <small style="color: var(--text-muted); font-size: 0.75rem;">تترك فارغة للاحتفاظ بكلمة المرور الحالية.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">الدور الوظيفي والصلاحيات *</label>
                    <select name="role_id" id="editRoleId" class="form-control" required>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الفرع التابع له *</label>
                    <select name="branch_id" id="editBranchId" class="form-control" required>
                        <?php foreach ($branches as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" id="editPhone" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" id="editEmail" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">حالة الحساب</label>
                    <select name="is_active" id="editIsActive" class="form-control">
                        <option value="1">مفعل</option>
                        <option value="0">معطل</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeEditUserModal()" class="btn btn-outline">إلغاء</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem;">
                    <i class="fas fa-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddUserModal() {
    document.getElementById('addUserModal').style.display = 'flex';
}
function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
}

function openEditUserModal(u) {
    document.getElementById('editUserId').value = u.id;
    document.getElementById('editFullName').value = u.full_name || '';
    document.getElementById('editUsername').value = u.username || '';
    document.getElementById('editRoleId').value = u.role_id;
    document.getElementById('editBranchId').value = u.branch_id || 1;
    document.getElementById('editPhone').value = u.phone || '';
    document.getElementById('editEmail').value = u.email || '';
    document.getElementById('editIsActive').value = u.is_active;

    document.getElementById('editUserModal').style.display = 'flex';
}
function closeEditUserModal() {
    document.getElementById('editUserModal').style.display = 'none';
}
</script>
