/**
 * Dark Mode Controller
 * WAM Tech Soft POS System
 * ========================================
 */

(function($) {
    'use strict';

    // Initialize Dark Mode
    $(document).ready(function() {
        initDarkMode();
        addThemeToggleButton();
    });

    /**
     * Initialize Dark Mode from localStorage
     */
    function initDarkMode() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);
    }

    /**
     * Set Theme
     */
    function setTheme(theme) {
        const html = document.documentElement;
        
        if (theme === 'dark') {
            html.setAttribute('data-theme', 'dark');
            updateToggleButton('dark');
        } else {
            html.setAttribute('data-theme', 'light');
            updateToggleButton('light');
        }
        
        localStorage.setItem('theme', theme);
    }

    /**
     * Toggle Theme
     */
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
        
        // Show notification
        if (typeof showNotification === 'function') {
            const message = newTheme === 'dark' 
                ? 'تم التبديل إلى الوضع الداكن' 
                : 'تم التبديل إلى الوضع الفاتح';
            showNotification(message, 'success', 2000);
        }
    }

    /**
     * Add Theme Toggle Button to Navbar
     */
    function addThemeToggleButton() {
        // Check if button already exists
        if ($('.theme-toggle').length > 0) {
            return;
        }

        const toggleButton = `
            <li class="theme-toggle-wrapper">
                <a href="javascript:void(0)" class="theme-toggle" id="themeToggle" title="تبديل الوضع الداكن">
                    <i class="fa fa-moon-o theme-icon"></i>
                </a>
            </li>
        `;

        // Add button to navbar
        $('.nav.pull-right').prepend(toggleButton);

        // Bind click event
        $('#themeToggle').on('click', function(e) {
            e.preventDefault();
            toggleTheme();
        });
    }

    /**
     * Update Toggle Button Icon
     */
    function updateToggleButton(theme) {
        const icon = $('.theme-icon');
        
        if (theme === 'dark') {
            icon.removeClass('fa-moon-o').addClass('fa-sun-o');
        } else {
            icon.removeClass('fa-sun-o').addClass('fa-moon-o');
        }
    }

    /**
     * Keyboard Shortcut (Ctrl/Cmd + Shift + D)
     */
    $(document).on('keydown', function(e) {
        // Ctrl+Shift+D or Cmd+Shift+D
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
            e.preventDefault();
            toggleTheme();
        }
    });

    /**
     * Auto Dark Mode based on system preference (optional)
     */
    function autoDetectTheme() {
        if (!localStorage.getItem('theme')) {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                setTheme('dark');
            }
        }
    }

    // Uncomment to enable auto-detection
    // autoDetectTheme();

    // Listen for system theme changes
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (!localStorage.getItem('theme-locked')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    // Export functions for global use
    window.toggleTheme = toggleTheme;
    window.setTheme = setTheme;

})(jQuery);

