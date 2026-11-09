<div class="auth-wrapper">
    <div class="auth-card">
        <!-- Brand Logo -->
        <div style="margin-bottom: 1.5rem;">
            <img src="<?= BASE_URL ?>assets/brand/logo-horizontal-light.png" alt="المبرمج | Almubarmig" class="brand-img" style="max-width: 220px;">
            <div style="font-size: 0.8rem; color: #38bdf8; font-weight: 700; margin-top: 0.25rem;">
                منظومة WAM Tech POS & ERP Enterprise (إصدار مصر)
            </div>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; padding: 0.75rem 1rem; border-radius: var(--radius-md); font-size: 0.85rem; margin-bottom: 1.5rem; text-align: right;">
                <i class="fas fa-exclamation-circle" style="margin-left: 6px;"></i>
                <?= Security::escape($errorMessage) ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="<?= BASE_URL ?>login.php" method="POST" style="text-align: right;">
            <?= Security::csrfInput() ?>

            <div class="form-group">
                <label class="form-label" style="color: #cbd5e1;">اسم المستخدم (Username)</label>
                <div style="position: relative;">
                    <input type="text" name="username" class="form-control" placeholder="أدخل اسم المستخدم..." required autofocus
                           value="<?= Security::escape($_POST['username'] ?? '') ?>"
                           style="background: rgba(255, 255, 255, 0.07); color: #fff; border-color: rgba(255,255,255,0.15); padding-right: 2.5rem;">
                    <i class="fas fa-user" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.75rem;">
                <label class="form-label" style="color: #cbd5e1;">كلمة المرور (Password)</label>
                <div style="position: relative;">
                    <input type="password" name="password" class="form-control" placeholder="أدخل كلمة المرور..." required
                           style="background: rgba(255, 255, 255, 0.07); color: #fff; border-color: rgba(255,255,255,0.15); padding-right: 2.5rem;">
                    <i class="fas fa-lock" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem; border-radius: var(--radius-md);">
                <i class="fas fa-sign-in-alt"></i>
                <span>تسجيل الدخول للنظام</span>
            </button>
        </form>

        <!-- Quick Demo Accounts Helper -->
        <div style="margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.78rem; color: #94a3b8; text-align: right;">
            <div style="font-weight: 700; color: #fde68a; margin-bottom: 0.4rem;">
                <i class="fas fa-key" style="margin-left: 4px;"></i> بيانات الدخول التجريبية السريعة:
            </div>
            <div style="display: flex; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap;">
                <span>المدير: <code>admin</code> / <code>admin123</code></span>
                <span>الكاشير: <code>cashier1</code> / <code>123456</code></span>
            </div>
        </div>

        <div style="margin-top: 1.5rem; font-size: 0.72rem; color: #64748b;">
            تطوير وهندسة برمجية بواسطة: <strong style="color: #38bdf8;">المبرمج | Almubarmig</strong><br>
            <em>"من الفكرة إلى نظام يعمل"</em>
        </div>
    </div>
</div>

