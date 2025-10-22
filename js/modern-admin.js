/*!
 * Modern Admin RTL JavaScript v1.0
 * Modern interactive features for Admin RTL Dashboard
 * Built with ES6+ and latest JavaScript features
 * Date: October 21, 2025
 */

;(function () {
  'use strict'

  // ═══════════════════════════════════════════════════════════════
  // 1. Theme Management (Dark Mode Toggle)
  // ═══════════════════════════════════════════════════════════════
  class ThemeManager {
    constructor() {
      this.themeKey = 'admin-theme'
      this.currentTheme = this.getTheme()
      this.init()
    }

    init() {
      this.applyTheme(this.currentTheme)
      this.bindEvents()
    }

    getTheme() {
      return localStorage.getItem(this.themeKey) || 'light'
    }

    setTheme(theme) {
      localStorage.setItem(this.themeKey, theme)
      this.currentTheme = theme
    }

    applyTheme(theme) {
      document.documentElement.setAttribute('data-bs-theme', theme)
      const icon = document.getElementById('themeIcon')
      if (icon) {
        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon'
      }
    }

    toggleTheme() {
      const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark'
      this.setTheme(newTheme)
      this.applyTheme(newTheme)
    }

    bindEvents() {
      const toggleBtn = document.getElementById('themeToggle')
      if (toggleBtn) {
        toggleBtn.addEventListener('click', () => this.toggleTheme())
      }
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 2. Sidebar Management
  // ═══════════════════════════════════════════════════════════════
  class SidebarManager {
    constructor() {
      this.sidebar = document.getElementById('adminSidebar')
      this.toggleBtn = document.getElementById('sidebarToggle')
      this.init()
    }

    init() {
      this.bindEvents()
      this.handleResize()
    }

    toggle() {
      if (this.sidebar) {
        this.sidebar.classList.toggle('show')
      }
    }

    hide() {
      if (this.sidebar) {
        this.sidebar.classList.remove('show')
      }
    }

    handleResize() {
      window.addEventListener('resize', () => {
        if (window.innerWidth > 991) {
          this.hide()
        }
      })
    }

    bindEvents() {
      if (this.toggleBtn) {
        this.toggleBtn.addEventListener('click', () => this.toggle())
      }

      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', (e) => {
        if (window.innerWidth <= 991) {
          if (
            this.sidebar &&
            !this.sidebar.contains(e.target) &&
            !this.toggleBtn.contains(e.target)
          ) {
            this.hide()
          }
        }
      })
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 3. Notification System (Toast Messages)
  // ═══════════════════════════════════════════════════════════════
  class NotificationManager {
    static show(message, type = 'success', duration = 3000) {
      // Using SweetAlert2 Toast
      if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-start',
          showConfirmButton: false,
          timer: duration,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          },
        })

        Toast.fire({
          icon: type,
          title: message,
        })
      } else {
        // Fallback to console
        console.log(`[${type.toUpperCase()}] ${message}`)
      }
    }

    static success(message) {
      this.show(message, 'success')
    }

    static error(message) {
      this.show(message, 'error')
    }

    static warning(message) {
      this.show(message, 'warning')
    }

    static info(message) {
      this.show(message, 'info')
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 4. AJAX Helper (Using Axios)
  // ═══════════════════════════════════════════════════════════════
  class AjaxHelper {
    static async get(url, params = {}) {
      try {
        const response = await axios.get(url, { params })
        return response.data
      } catch (error) {
        console.error('AJAX GET Error:', error)
        NotificationManager.error('حدث خطأ في تحميل البيانات')
        throw error
      }
    }

    static async post(url, data = {}) {
      try {
        const response = await axios.post(url, data)
        return response.data
      } catch (error) {
        console.error('AJAX POST Error:', error)
        NotificationManager.error('حدث خطأ في إرسال البيانات')
        throw error
      }
    }

    static async delete(url) {
      try {
        const response = await axios.delete(url)
        return response.data
      } catch (error) {
        console.error('AJAX DELETE Error:', error)
        NotificationManager.error('حدث خطأ في حذف البيانات')
        throw error
      }
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 5. Form Validation Helper
  // ═══════════════════════════════════════════════════════════════
  class FormValidator {
    static validateForm(formElement) {
      if (!formElement.checkValidity()) {
        formElement.classList.add('was-validated')
        return false
      }
      return true
    }

    static validateRequired(value, fieldName) {
      if (!value || value.trim() === '') {
        NotificationManager.error(`حقل ${fieldName} مطلوب`)
        return false
      }
      return true
    }

    static validateEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!emailRegex.test(email)) {
        NotificationManager.error('البريد الإلكتروني غير صحيح')
        return false
      }
      return true
    }

    static validatePhone(phone) {
      const phoneRegex = /^[0-9]{10}$/
      if (!phoneRegex.test(phone)) {
        NotificationManager.error('رقم الهاتف غير صحيح (10 أرقام)')
        return false
      }
      return true
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 6. Confirmation Dialog Helper
  // ═══════════════════════════════════════════════════════════════
  class ConfirmDialog {
    static async show(title, text, confirmText = 'نعم', cancelText = 'لا') {
      if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
          title: title,
          text: text,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#32C2CD',
          cancelButtonColor: '#e74c3c',
          confirmButtonText: confirmText,
          cancelButtonText: cancelText,
          reverseButtons: true,
        })
        return result.isConfirmed
      }
      return confirm(`${title}\n${text}`)
    }

    static async delete(itemName = 'هذا العنصر') {
      return await this.show(
        'تأكيد الحذف',
        `هل أنت متأكد من حذف ${itemName}؟ لا يمكن التراجع عن هذا الإجراء.`,
        'حذف',
        'إلغاء'
      )
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 7. Loading Indicator
  // ═══════════════════════════════════════════════════════════════
  class LoadingManager {
    static show(message = 'جاري التحميل...') {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: message,
          allowOutsideClick: false,
          allowEscapeKey: false,
          allowEnterKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading()
          },
        })
      }
    }

    static hide() {
      if (typeof Swal !== 'undefined') {
        Swal.close()
      }
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 8. Number Formatting Utilities
  // ═══════════════════════════════════════════════════════════════
  class NumberFormatter {
    static formatCurrency(amount, currency = 'SAR') {
      return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: currency,
      }).format(amount)
    }

    static formatNumber(number, decimals = 2) {
      return new Intl.NumberFormat('ar-SA', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
      }).format(number)
    }

    static formatPercent(value) {
      return new Intl.NumberFormat('ar-SA', {
        style: 'percent',
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
      }).format(value / 100)
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 9. Date Formatting Utilities
  // ═══════════════════════════════════════════════════════════════
  class DateFormatter {
    static formatDate(date) {
      return new Intl.DateTimeFormat('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }).format(new Date(date))
    }

    static formatDateTime(date) {
      return new Intl.DateTimeFormat('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      }).format(new Date(date))
    }

    static formatTime(date) {
      return new Intl.DateTimeFormat('ar-SA', {
        hour: '2-digit',
        minute: '2-digit',
      }).format(new Date(date))
    }

    static getRelativeTime(date) {
      const rtf = new Intl.RelativeTimeFormat('ar-SA', { numeric: 'auto' })
      const diff = new Date(date) - new Date()
      const seconds = Math.floor(diff / 1000)
      const minutes = Math.floor(seconds / 60)
      const hours = Math.floor(minutes / 60)
      const days = Math.floor(hours / 24)

      if (Math.abs(days) > 0) return rtf.format(days, 'day')
      if (Math.abs(hours) > 0) return rtf.format(hours, 'hour')
      if (Math.abs(minutes) > 0) return rtf.format(minutes, 'minute')
      return rtf.format(seconds, 'second')
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 10. Initialize Everything
  // ═══════════════════════════════════════════════════════════════
  document.addEventListener('DOMContentLoaded', function () {
    // Initialize Theme Manager
    window.themeManager = new ThemeManager()

    // Initialize Sidebar Manager
    window.sidebarManager = new SidebarManager()

    // Make utilities globally accessible
    window.AdminNotify = NotificationManager
    window.AdminAjax = AjaxHelper
    window.AdminValidator = FormValidator
    window.AdminConfirm = ConfirmDialog
    window.AdminLoading = LoadingManager
    window.AdminNumber = NumberFormatter
    window.AdminDate = DateFormatter

    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="tooltip"]')
    )
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Initialize Bootstrap popovers
    const popoverTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="popover"]')
    )
    popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl)
    })

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)')
    alerts.forEach((alert) => {
      setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert)
        bsAlert.close()
      }, 5000)
    })

    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth'

    console.log('✅ Modern Admin RTL System Initialized Successfully!')
  })

  // ═══════════════════════════════════════════════════════════════
  // 11. Example Usage Functions (for testing)
  // ═══════════════════════════════════════════════════════════════

  // Example: Delete confirmation
  window.confirmDelete = async function (id, name) {
    const confirmed = await AdminConfirm.delete(name)
    if (confirmed) {
      AdminLoading.show('جاري الحذف...')
      try {
        await AdminAjax.delete(`delete.php?id=${id}`)
        AdminLoading.hide()
        AdminNotify.success('تم الحذف بنجاح')
        // Reload or remove element
        location.reload()
      } catch (error) {
        AdminLoading.hide()
      }
    }
  }

  // Example: Save form with validation
  window.saveForm = async function (formId) {
    const form = document.getElementById(formId)
    if (!AdminValidator.validateForm(form)) {
      return false
    }

    AdminLoading.show('جاري الحفظ...')
    const formData = new FormData(form)

    try {
      const result = await AdminAjax.post(form.action, formData)
      AdminLoading.hide()
      AdminNotify.success('تم الحفظ بنجاح')
      return true
    } catch (error) {
      AdminLoading.hide()
      return false
    }
  }
})()
