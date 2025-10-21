/**
 * Auto-save للنماذج
 * WAM Tech Soft POS System
 * ========================================
 */

(function($) {
    'use strict';

    // Initialize
    $(document).ready(function() {
        initAutoSave();
    });

    /**
     * Initialize Auto-save
     */
    function initAutoSave() {
        // Enable auto-save for forms with data-autosave attribute
        $('form[data-autosave]').each(function() {
            const form = $(this);
            const formId = form.attr('id') || 'form_' + Math.random().toString(36).substr(2, 9);
            form.attr('id', formId);
            
            enableAutoSave(formId);
        });

        // Restore saved data on page load
        restoreSavedForms();
    }

    /**
     * Enable Auto-save for specific form
     */
    function enableAutoSave(formId) {
        const form = $('#' + formId);
        if (!form.length) return;

        const saveInterval = form.data('autosave-interval') || 30000; // 30 seconds default
        let saveTimer;
        let hasChanges = false;

        // Monitor form changes
        form.on('input change', 'input, textarea, select', function() {
            hasChanges = true;
            
            // Clear existing timer
            if (saveTimer) {
                clearTimeout(saveTimer);
            }

            // Set new timer
            saveTimer = setTimeout(function() {
                if (hasChanges) {
                    saveFormData(formId);
                    hasChanges = false;
                }
            }, saveInterval);
        });

        // Save on blur (when leaving input)
        form.on('blur', 'input, textarea, select', function() {
            if (hasChanges) {
                saveFormData(formId);
                hasChanges = false;
            }
        });

        // Clear saved data on successful submit
        form.on('submit', function() {
            setTimeout(function() {
                clearSavedFormData(formId);
            }, 1000);
        });

        // Show indicator
        addAutoSaveIndicator(formId);
    }

    /**
     * Save Form Data to localStorage
     */
    function saveFormData(formId) {
        const form = $('#' + formId);
        if (!form.length) return;

        const formData = {};
        
        // Collect form data
        form.find('input, textarea, select').each(function() {
            const field = $(this);
            const name = field.attr('name');
            
            if (!name) return;

            if (field.is(':checkbox')) {
                formData[name] = field.is(':checked');
            } else if (field.is(':radio')) {
                if (field.is(':checked')) {
                    formData[name] = field.val();
                }
            } else {
                formData[name] = field.val();
            }
        });

        // Save to localStorage
        const storageKey = 'autosave_' + formId;
        localStorage.setItem(storageKey, JSON.stringify({
            data: formData,
            timestamp: new Date().getTime(),
            url: window.location.href
        }));

        // Show save indicator
        showSaveIndicator(formId, 'saved');

        console.log('✅ Form auto-saved:', formId);
    }

    /**
     * Restore Saved Forms
     */
    function restoreSavedForms() {
        $('form').each(function() {
            const form = $(this);
            const formId = form.attr('id');
            
            if (!formId) return;

            const storageKey = 'autosave_' + formId;
            const savedData = localStorage.getItem(storageKey);

            if (savedData) {
                try {
                    const parsed = JSON.parse(savedData);
                    const age = new Date().getTime() - parsed.timestamp;
                    const maxAge = 24 * 60 * 60 * 1000; // 24 hours

                    // Only restore if less than 24 hours old and same URL
                    if (age < maxAge && parsed.url === window.location.href) {
                        showRestorePrompt(formId, parsed);
                    } else {
                        // Clear old data
                        localStorage.removeItem(storageKey);
                    }
                } catch (e) {
                    console.error('Error restoring form data:', e);
                }
            }
        });
    }

    /**
     * Show Restore Prompt
     */
    function showRestorePrompt(formId, savedData) {
        const timestamp = new Date(savedData.timestamp);
        const timeAgo = getTimeAgo(timestamp);

        const prompt = `
            <div class="autosave-restore-prompt" id="restore_${formId}" style="
                position: fixed;
                top: 100px;
                right: 2rem;
                background: white;
                padding: 1.5rem;
                border-radius: 0.75rem;
                box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                z-index: 9999;
                max-width: 350px;
                border-left: 4px solid #10b981;
                animation: slideInRight 0.3s;
            ">
                <h4 style="margin: 0 0 0.5rem; color: #1e293b; font-size: 1rem;">
                    <i class="fa fa-info-circle" style="color: #10b981;"></i>
                    بيانات محفوظة
                </h4>
                <p style="margin: 0 0 1rem; color: #64748b; font-size: 0.875rem;">
                    تم حفظ بيانات هذا النموذج تلقائياً ${timeAgo}. هل تريد استعادتها؟
                </p>
                <div style="display: flex; gap: 0.5rem;">
                    <button onclick="window.restoreFormData('${formId}')" style="
                        flex: 1;
                        padding: 0.5rem 1rem;
                        background: #10b981;
                        color: white;
                        border: none;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        font-weight: 600;
                    ">استعادة</button>
                    <button onclick="window.dismissRestore('${formId}')" style="
                        flex: 1;
                        padding: 0.5rem 1rem;
                        background: #e2e8f0;
                        color: #64748b;
                        border: none;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        font-weight: 600;
                    ">تجاهل</button>
                </div>
            </div>
        `;

        $('body').append(prompt);

        // Auto-dismiss after 30 seconds
        setTimeout(function() {
            $('#restore_' + formId).fadeOut(300, function() {
                $(this).remove();
            });
        }, 30000);
    }

    /**
     * Restore Form Data
     */
    window.restoreFormData = function(formId) {
        const storageKey = 'autosave_' + formId;
        const savedData = localStorage.getItem(storageKey);

        if (savedData) {
            try {
                const parsed = JSON.parse(savedData);
                const form = $('#' + formId);

                // Restore each field
                $.each(parsed.data, function(name, value) {
                    const field = form.find('[name="' + name + '"]');
                    
                    if (field.is(':checkbox')) {
                        field.prop('checked', value);
                    } else if (field.is(':radio')) {
                        field.filter('[value="' + value + '"]').prop('checked', true);
                    } else {
                        field.val(value);
                    }
                });

                // Dismiss prompt
                $('#restore_' + formId).fadeOut(300, function() {
                    $(this).remove();
                });

                if (typeof showNotification === 'function') {
                    showNotification('تم استعادة البيانات المحفوظة', 'success', 2000);
                }
            } catch (e) {
                console.error('Error restoring form:', e);
            }
        }
    };

    /**
     * Dismiss Restore Prompt
     */
    window.dismissRestore = function(formId) {
        const storageKey = 'autosave_' + formId;
        localStorage.removeItem(storageKey);
        
        $('#restore_' + formId).fadeOut(300, function() {
            $(this).remove();
        });
    };

    /**
     * Clear Saved Form Data
     */
    function clearSavedFormData(formId) {
        const storageKey = 'autosave_' + formId;
        localStorage.removeItem(storageKey);
        console.log('🗑️ Cleared auto-saved data for:', formId);
    }

    /**
     * Add Auto-save Indicator
     */
    function addAutoSaveIndicator(formId) {
        if ($('#autosave_indicator_' + formId).length) return;

        const indicator = `
            <div id="autosave_indicator_${formId}" class="autosave-indicator" style="
                position: fixed;
                bottom: 2rem;
                left: 2rem;
                padding: 0.5rem 1rem;
                background: rgba(15, 23, 42, 0.9);
                color: white;
                border-radius: 2rem;
                font-size: 0.8125rem;
                display: none;
                z-index: 1000;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            ">
                <i class="fa fa-circle-o-notch fa-spin"></i> جاري الحفظ...
            </div>
        `;

        $('body').append(indicator);
    }

    /**
     * Show Save Indicator
     */
    function showSaveIndicator(formId, status) {
        const indicator = $('#autosave_indicator_' + formId);
        if (!indicator.length) return;

        if (status === 'saving') {
            indicator.html('<i class="fa fa-circle-o-notch fa-spin"></i> جاري الحفظ...')
                     .css('background', 'rgba(59, 130, 246, 0.9)')
                     .fadeIn(200);
        } else if (status === 'saved') {
            indicator.html('<i class="fa fa-check"></i> تم الحفظ')
                     .css('background', 'rgba(16, 185, 129, 0.9)')
                     .fadeIn(200);
            
            setTimeout(function() {
                indicator.fadeOut(300);
            }, 2000);
        }
    }

    /**
     * Get Time Ago
     */
    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        
        if (seconds < 60) return 'منذ لحظات';
        if (seconds < 3600) return 'منذ ' + Math.floor(seconds / 60) + ' دقيقة';
        if (seconds < 86400) return 'منذ ' + Math.floor(seconds / 3600) + ' ساعة';
        return 'منذ ' + Math.floor(seconds / 86400) + ' يوم';
    }

    // Add CSS animation
    const style = `
        <style>
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>
    `;
    $('head').append(style);

})(jQuery);

