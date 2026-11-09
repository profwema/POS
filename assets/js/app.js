/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * المتحكم التفاعلي الرئيسي - Client Controller
 * براند: المبرمج | Almubarmig
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. تهيئة الوضع الليلي / النهاري
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const savedTheme = localStorage.getItem('wam_theme') || 'light';

    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        if (themeToggleBtn) {
            themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            themeToggleBtn.title = 'التبديل إلى الوضع النهاري';
        }
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            if (isDark) {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('wam_theme', 'light');
                themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
                themeToggleBtn.title = 'التبديل إلى الوضع الليلي';
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('wam_theme', 'dark');
                themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
                themeToggleBtn.title = 'التبديل إلى الوضع النهاري';
            }
        });
    }

    // 2. إظهار/إخفاء القائمة الجانبية في الشاشات الصغيرة
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.querySelector('.app-sidebar');

    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });

        // إغلاق القائمة عند النقر خارجها
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }
});

/**
 * دالة مساعدة لتنفيذ طلبات AJAX مع تضمين توكن CSRF تلقائياً
 */
async function apiRequest(url, method = 'GET', data = null) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    const headers = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };

    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }

    const options = { method, headers };

    if (data) {
        if (data instanceof FormData) {
            options.body = data;
        } else {
            headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(data);
        }
    }

    const response = await fetch(url, options);
    return await response.json();
}

