<!-- ═══════════════════════════════════════════════════════════════
   PROFESSIONAL ADMIN FOOTER
   Clean, minimal footer with copyright and version info
   ═══════════════════════════════════════════════════════════════ -->
<footer class="admin-footer">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2" style="padding: 1rem 0;">
    <div class="text-muted small">
      &copy; <?= date('Y') ?> <strong class="text-primary">WAM Tech Soft</strong>. جميع الحقوق محفوظة.
    </div>
    <div class="d-flex align-items-center gap-3 small">
      <a href="#" class="text-muted text-decoration-none hover-primary">
        <i class="fas fa-question-circle me-1"></i> المساعدة
      </a>
      <span class="text-muted">|</span>
      <a href="#" class="text-muted text-decoration-none hover-primary">
        <i class="fas fa-file-alt me-1"></i> الوثائق
      </a>
      <span class="text-muted">|</span>
      <a href="#" class="text-muted text-decoration-none hover-primary">
        <i class="fas fa-headset me-1"></i> الدعم
      </a>
      <span class="text-muted">|</span>
      <span class="badge-pro badge-pro-primary">
        <i class="fas fa-code-branch me-1"></i> v3.0
      </span>
    </div>
  </div>
</footer>

<style>
  .admin-footer {
    margin-top: auto;
    padding: 0 1.5rem;
    border-top: 1px solid var(--gray-200);
    background: var(--white);
  }

  .hover-primary:hover {
    color: var(--primary) !important;
  }
</style>