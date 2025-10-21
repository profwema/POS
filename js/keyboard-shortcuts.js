/**
 * Keyboard Shortcuts للنظام
 * WAM Tech Soft POS System
 * ========================================
 */

(function($) {
    'use strict';

    // Initialize
    $(document).ready(function() {
        initKeyboardShortcuts();
        showShortcutsHelper();
    });

    /**
     * Initialize Keyboard Shortcuts
     */
    function initKeyboardShortcuts() {
        $(document).on('keydown', function(e) {
            
            // Prevent shortcuts when typing in inputs
            if ($(e.target).is('input, textarea, select')) {
                // Allow some shortcuts even in inputs
                if (!(e.ctrlKey || e.metaKey)) {
                    return;
                }
            }

            // Ctrl/Cmd + S: Save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                triggerSave();
                return false;
            }

            // Ctrl/Cmd + K: Quick Search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                openQuickSearch();
                return false;
            }

            // Ctrl/Cmd + P: Print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
                return false;
            }

            // Ctrl/Cmd + B: Sidebar Toggle
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                toggleSidebar();
                return false;
            }

            // Ctrl/Cmd + /: Show Shortcuts
            if ((e.ctrlKey || e.metaKey) && e.key === '/') {
                e.preventDefault();
                showShortcutsModal();
                return false;
            }

            // Ctrl/Cmd + N: New Item (if on items page)
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                triggerNew();
                return false;
            }

            // Ctrl/Cmd + E: Edit (if item selected)
            if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
                e.preventDefault();
                triggerEdit();
                return false;
            }

            // ESC: Close modals/dialogs
            if (e.key === 'Escape') {
                closeModalsAndDialogs();
                return false;
            }

            // F2: Rename/Edit first editable
            if (e.key === 'F2') {
                e.preventDefault();
                triggerFirstEdit();
                return false;
            }

            // Ctrl/Cmd + Home: Go to Dashboard
            if ((e.ctrlKey || e.metaKey) && e.key === 'Home') {
                e.preventDefault();
                window.location.href = 'index.php';
                return false;
            }
        });
    }

    /**
     * Trigger Save
     */
    function triggerSave() {
        // Look for save button
        const saveBtn = $('button[type="submit"], button#save, button.save-btn, input[type="submit"]').first();
        
        if (saveBtn.length) {
            saveBtn.click();
            if (typeof showNotification === 'function') {
                showNotification('جاري الحفظ...', 'info', 1500);
            }
        } else {
            // Try form submit
            const form = $('form').first();
            if (form.length) {
                form.submit();
            }
        }
    }

    /**
     * Open Quick Search
     */
    function openQuickSearch() {
        // Check if search input exists
        let searchInput = $('input[type="search"], input[name="search"], #search').first();
        
        if (!searchInput.length) {
            // Create quick search modal
            createQuickSearchModal();
        } else {
            searchInput.focus().select();
        }
    }

    /**
     * Create Quick Search Modal
     */
    function createQuickSearchModal() {
        if ($('#quickSearchModal').length) {
            $('#quickSearchModal').show().find('input').focus();
            return;
        }

        const modal = `
            <div id="quickSearchModal" style="
                position: fixed;
                top: 20%;
                left: 50%;
                transform: translateX(-50%);
                background: white;
                padding: 2rem;
                border-radius: 1rem;
                box-shadow: 0 20px 50px rgba(0,0,0,0.3);
                z-index: 10000;
                min-width: 500px;
            ">
                <h3 style="margin: 0 0 1rem;">بحث سريع</h3>
                <input type="text" id="quickSearchInput" placeholder="ابحث عن أي شيء..." style="
                    width: 100%;
                    padding: 1rem;
                    font-size: 1rem;
                    border: 2px solid #e2e8f0;
                    border-radius: 0.5rem;
                ">
                <div id="quickSearchResults" style="margin-top: 1rem; max-height: 300px; overflow-y: auto;"></div>
                <button onclick="$('#quickSearchModal').hide()" style="
                    margin-top: 1rem;
                    padding: 0.5rem 1rem;
                    background: #6366f1;
                    color: white;
                    border: none;
                    border-radius: 0.5rem;
                    cursor: pointer;
                ">إغلاق (ESC)</button>
            </div>
            <div id="quickSearchOverlay" onclick="$('#quickSearchModal').hide()" style="
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 9999;
            "></div>
        `;

        $('body').append(modal);
        $('#quickSearchInput').focus();

        // Close on ESC
        $(document).on('keydown.quicksearch', function(e) {
            if (e.key === 'Escape') {
                $('#quickSearchModal, #quickSearchOverlay').remove();
                $(document).off('keydown.quicksearch');
            }
        });
    }

    /**
     * Toggle Sidebar
     */
    function toggleSidebar() {
        $('#sidebar-left').toggleClass('in');
        $('body').toggleClass('sidebar-open');
    }

    /**
     * Show Shortcuts Modal
     */
    function showShortcutsModal() {
        if ($('#shortcutsModal').length) {
            $('#shortcutsModal').show();
            return;
        }

        const shortcuts = [
            { keys: 'Ctrl + S', action: 'حفظ' },
            { keys: 'Ctrl + K', action: 'بحث سريع' },
            { keys: 'Ctrl + P', action: 'طباعة' },
            { keys: 'Ctrl + B', action: 'إظهار/إخفاء القائمة الجانبية' },
            { keys: 'Ctrl + Shift + D', action: 'تبديل الوضع الداكن' },
            { keys: 'Ctrl + N', action: 'جديد' },
            { keys: 'Ctrl + E', action: 'تعديل' },
            { keys: 'Ctrl + Home', action: 'الذهاب للرئيسية' },
            { keys: 'Ctrl + /', action: 'عرض الاختصارات' },
            { keys: 'ESC', action: 'إغلاق النوافذ' },
            { keys: 'F2', action: 'تعديل' }
        ];

        let shortcutsList = shortcuts.map(s => 
            `<tr>
                <td style="padding: 0.75rem; border-bottom: 1px solid #e2e8f0; font-weight: 600;">${s.keys}</td>
                <td style="padding: 0.75rem; border-bottom: 1px solid #e2e8f0;">${s.action}</td>
            </tr>`
        ).join('');

        const modal = `
            <div id="shortcutsModal" style="
                position: fixed;
                top: 10%;
                left: 50%;
                transform: translateX(-50%);
                background: white;
                padding: 2rem;
                border-radius: 1rem;
                box-shadow: 0 20px 50px rgba(0,0,0,0.3);
                z-index: 10000;
                max-width: 600px;
                width: 90%;
            ">
                <h2 style="margin: 0 0 1.5rem;">⌨️ اختصارات لوحة المفاتيح</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 0.75rem; text-align: right;">الاختصار</th>
                            <th style="padding: 0.75rem; text-align: right;">الوظيفة</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${shortcutsList}
                    </tbody>
                </table>
                <button onclick="$('#shortcutsModal, #shortcutsOverlay').remove()" style="
                    margin-top: 1.5rem;
                    padding: 0.75rem 1.5rem;
                    background: #6366f1;
                    color: white;
                    border: none;
                    border-radius: 0.5rem;
                    cursor: pointer;
                    font-weight: 600;
                ">فهمت (ESC)</button>
            </div>
            <div id="shortcutsOverlay" onclick="$('#shortcutsModal, #shortcutsOverlay').remove()" style="
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 9999;
            "></div>
        `;

        $('body').append(modal);
    }

    /**
     * Show Shortcuts Helper (Small floating button)
     */
    function showShortcutsHelper() {
        // Don't show if already exists
        if ($('#shortcutsHelper').length) {
            return;
        }

        const helper = `
            <div id="shortcutsHelper" style="
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                background: #6366f1;
                color: white;
                padding: 0.75rem 1rem;
                border-radius: 2rem;
                cursor: pointer;
                box-shadow: 0 4px 12px rgba(99,102,241,0.4);
                z-index: 1000;
                font-size: 0.875rem;
                font-weight: 600;
                transition: all 0.3s;
            " title="اضغط Ctrl + / لعرض الاختصارات">
                <i class="fa fa-keyboard-o"></i> اختصارات
            </div>
        `;

        $('body').append(helper);

        $('#shortcutsHelper').on('click', function() {
            showShortcutsModal();
        }).hover(
            function() { $(this).css('transform', 'scale(1.05)'); },
            function() { $(this).css('transform', 'scale(1)'); }
        );

        // Auto-hide after 10 seconds
        setTimeout(function() {
            $('#shortcutsHelper').fadeOut(300);
        }, 10000);
    }

    /**
     * Trigger New
     */
    function triggerNew() {
        const newBtn = $('button.new-btn, a.new-btn, .btn-new, [href*="add_"]').first();
        if (newBtn.length) {
            newBtn[0].click();
        }
    }

    /**
     * Trigger Edit
     */
    function triggerEdit() {
        const editBtn = $('button.edit-btn, a.edit-btn, .btn-edit, [href*="edit_"]').first();
        if (editBtn.length) {
            editBtn[0].click();
        }
    }

    /**
     * Trigger First Edit
     */
    function triggerFirstEdit() {
        const firstInput = $('input[type="text"]:visible, textarea:visible').first();
        if (firstInput.length) {
            firstInput.focus().select();
        }
    }

    /**
     * Close Modals and Dialogs
     */
    function closeModalsAndDialogs() {
        // Close any visible modals
        $('.modal, .dialog, [id*="Modal"], [id*="modal"]').hide();
        $('.modal-overlay, .overlay:visible').remove();
        
        // Close quick search if open
        $('#quickSearchModal, #quickSearchOverlay').remove();
        $('#shortcutsModal, #shortcutsOverlay').remove();
    }

})(jQuery);

