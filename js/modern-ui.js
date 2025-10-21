/**
 * Modern UI Enhancement Script
 * WAM Tech Soft POS System - 2025
 * ========================================
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initSidebarToggle();
        initMenuAnimations();
        initScrollEffects();
        initTooltips();
        initLoadingStates();
        initPageTransitions();
        initResponsiveMenu();
    });

    /**
     * Sidebar Toggle Functionality
     */
    function initSidebarToggle() {
        // Mobile menu toggle
        $('.navbar-toggle').on('click', function(e) {
            e.preventDefault();
            $('#sidebar-left').toggleClass('in');
            $('body').toggleClass('sidebar-open');
            
            // Add overlay for mobile
            if ($('#sidebar-left').hasClass('in')) {
                if (!$('.sidebar-overlay').length) {
                    $('body').append('<div class="sidebar-overlay"></div>');
                    $('.sidebar-overlay').fadeIn(300);
                }
            } else {
                $('.sidebar-overlay').fadeOut(300, function() {
                    $(this).remove();
                });
            }
        });

        // Close sidebar when clicking overlay
        $(document).on('click', '.sidebar-overlay', function() {
            $('#sidebar-left').removeClass('in');
            $('body').removeClass('sidebar-open');
            $(this).fadeOut(300, function() {
                $(this).remove();
            });
        });

        // Close sidebar on mobile when clicking a link
        if ($(window).width() < 992) {
            $('.nav.side-menu a').on('click', function() {
                setTimeout(function() {
                    $('#sidebar-left').removeClass('in');
                    $('.sidebar-overlay').fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 200);
            });
        }
    }

    /**
     * Menu Animations - محسّن
     */
    function initMenuAnimations() {
        // Submenu toggle with Accordion animation
        $('.nav.side-menu > li > a').off('click').on('click', function(e) {
            var $parent = $(this).parent();
            var $submenu = $(this).next('.child_menu');

            if ($submenu.length) {
                e.preventDefault();
                
                // نظام Accordion: إغلاق جميع القوائم الفرعية الأخرى
                $('.nav.side-menu > li').not($parent).each(function() {
                    $(this).removeClass('active open');
                    $(this).find('.child_menu').removeClass('in show').slideUp(300);
                });
                
                // Toggle current submenu
                $parent.toggleClass('active open');
                
                if ($parent.hasClass('active')) {
                    // فتح القائمة الفرعية الحالية
                    $submenu.addClass('in show').slideDown(350, function() {
                        // Smooth scroll للقائمة
                        var scrollTop = $parent.offset().top - 100;
                        if (scrollTop > 0 && $('#sidebar-left').length) {
                            $('#sidebar-left').animate({
                                scrollTop: scrollTop
                            }, 300);
                        }
                    });
                } else {
                    // إغلاق القائمة الفرعية الحالية
                    $submenu.removeClass('in show').slideUp(300);
                }
                
                return false; // منع الانتقال للرابط
            }
        });

        // Highlight current page
        highlightCurrentPage();

        // Add ripple effect to menu items
        addRippleEffect('.nav.side-menu a, .btn');
    }

    /**
     * Highlight Current Page in Menu
     */
    function highlightCurrentPage() {
        var currentPath = window.location.pathname;
        var currentPage = currentPath.split('/').pop();
        
        $('.nav.side-menu a').each(function() {
            var linkPath = $(this).attr('href');
            
            if (linkPath && linkPath.indexOf(currentPage) !== -1 && currentPage !== '') {
                $(this).parent().addClass('current-page');
                
                // If it's a submenu item, open parent
                if ($(this).closest('.child_menu').length) {
                    $(this).closest('.child_menu').show()
                           .parent().addClass('active');
                }
            }
        });
    }

    /**
     * Scroll Effects
     */
    function initScrollEffects() {
        var navbar = $('.navbar');
        var lastScroll = 0;

        $(window).on('scroll', function() {
            var currentScroll = $(this).scrollTop();

            // Add shadow on scroll
            if (currentScroll > 10) {
                navbar.addClass('scrolled');
            } else {
                navbar.removeClass('scrolled');
            }

            // Hide/show navbar on scroll (optional)
            // if (currentScroll > lastScroll && currentScroll > 100) {
            //     navbar.css('transform', 'translateY(-100%)');
            // } else {
            //     navbar.css('transform', 'translateY(0)');
            // }

            lastScroll = currentScroll;
        });

        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 80
                }, 600, 'swing');
            }
        });
    }

    /**
     * Initialize Tooltips
     */
    function initTooltips() {
        // Simple tooltip implementation
        $('[data-tooltip]').each(function() {
            $(this).hover(
                function() {
                    var tooltipText = $(this).data('tooltip');
                    var tooltip = $('<div class="custom-tooltip">' + tooltipText + '</div>');
                    
                    $('body').append(tooltip);
                    
                    var pos = $(this).offset();
                    var width = $(this).outerWidth();
                    var height = $(this).outerHeight();
                    
                    tooltip.css({
                        top: pos.top - tooltip.outerHeight() - 10,
                        left: pos.left + (width / 2) - (tooltip.outerWidth() / 2)
                    }).fadeIn(200);
                },
                function() {
                    $('.custom-tooltip').fadeOut(200, function() {
                        $(this).remove();
                    });
                }
            );
        });
    }

    /**
     * Loading States
     */
    function initLoadingStates() {
        // Show loading overlay
        window.showLoading = function(message) {
            var loader = $('#overlay');
            if (loader.length) {
                if (message) {
                    loader.find('img').after('<p class="loading-message">' + message + '</p>');
                }
                loader.fadeIn(300);
            }
        };

        // Hide loading overlay
        window.hideLoading = function() {
            var loader = $('#overlay');
            if (loader.length) {
                loader.fadeOut(300, function() {
                    loader.find('.loading-message').remove();
                });
            }
        };

        // Button loading state
        $.fn.setLoading = function(loading) {
            return this.each(function() {
                var $btn = $(this);
                if (loading) {
                    $btn.data('original-text', $btn.html())
                        .addClass('loading')
                        .prop('disabled', true)
                        .html('<i class="fa fa-spinner fa-spin"></i> Loading...');
                } else {
                    $btn.removeClass('loading')
                        .prop('disabled', false)
                        .html($btn.data('original-text'));
                }
            });
        };
    }

    /**
     * Page Transitions
     */
    function initPageTransitions() {
        // Fade in page content
        $('.box').each(function(index) {
            $(this).css({
                opacity: 0,
                transform: 'translateY(20px)'
            }).delay(index * 100).animate({
                opacity: 1
            }, 400, function() {
                $(this).css('transform', 'translateY(0)');
            });
        });

        // Add loading class to body
        $('body').addClass('page-loaded');

        // Smooth page transitions on links (optional)
        // $('a:not([target="_blank"])').on('click', function(e) {
        //     var href = $(this).attr('href');
        //     if (href && href.indexOf('#') !== 0 && href.indexOf('javascript:') !== 0) {
        //         e.preventDefault();
        //         $('body').fadeOut(300, function() {
        //             window.location = href;
        //         });
        //     }
        // });
    }

    /**
     * Responsive Menu
     */
    function initResponsiveMenu() {
        var resizeTimer;
        
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                var windowWidth = $(window).width();
                
                // Close mobile menu on desktop
                if (windowWidth >= 992) {
                    $('#sidebar-left').removeClass('in');
                    $('body').removeClass('sidebar-open');
                    $('.sidebar-overlay').remove();
                }
            }, 250);
        });
    }

    /**
     * Ripple Effect
     */
    function addRippleEffect(selector) {
        $(document).on('click', selector, function(e) {
            var $this = $(this);
            
            // Don't add ripple to certain elements
            if ($this.find('.ripple').length) {
                return;
            }
            
            var ripple = $('<span class="ripple"></span>');
            $this.append(ripple);
            
            var x = e.pageX - $this.offset().left;
            var y = e.pageY - $this.offset().top;
            
            ripple.css({
                top: y + 'px',
                left: x + 'px'
            }).addClass('ripple-animate');
            
            setTimeout(function() {
                ripple.remove();
            }, 600);
        });
    }

    /**
     * Form Enhancements
     */
    function initFormEnhancements() {
        // Floating labels
        $('.form-control').on('focus blur', function(e) {
            $(this).parent().toggleClass('focused', (e.type === 'focus' || this.value.length > 0));
        }).trigger('blur');

        // Input validation feedback
        $('.form-control').on('blur', function() {
            var $input = $(this);
            var $group = $input.closest('.form-group');
            
            if ($input.val().length > 0) {
                $group.addClass('has-value');
            } else {
                $group.removeClass('has-value');
            }
        });
    }

    /**
     * Notification System
     */
    window.showNotification = function(message, type, duration) {
        type = type || 'info';
        duration = duration || 3000;
        
        var icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        var notification = $('<div class="modern-notification ' + type + '">' +
            '<i class="fa ' + icons[type] + '"></i>' +
            '<span>' + message + '</span>' +
            '<button class="close-notification"><i class="fa fa-times"></i></button>' +
            '</div>');
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.addClass('show');
        }, 10);
        
        // Auto hide
        var hideTimer = setTimeout(function() {
            hideNotification(notification);
        }, duration);
        
        // Manual close
        notification.find('.close-notification').on('click', function() {
            clearTimeout(hideTimer);
            hideNotification(notification);
        });
    };
    
    function hideNotification(notification) {
        notification.removeClass('show');
        setTimeout(function() {
            notification.remove();
        }, 300);
    }

    /**
     * Utility Functions
     */
    
    // Debounce function
    window.debounce = function(func, wait) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    };

    // Throttle function
    window.throttle = function(func, limit) {
        var inThrottle;
        return function() {
            var args = arguments;
            var context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function() {
                    inThrottle = false;
                }, limit);
            }
        };
    };

    /**
     * Add CSS for dynamic elements
     */
    function addDynamicStyles() {
        var styles = `
            <style id="modern-ui-dynamic-styles">
                /* Ripple Effect */
                .ripple {
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.3);
                    width: 20px;
                    height: 20px;
                    margin-top: -10px;
                    margin-left: -10px;
                    pointer-events: none;
                    transform: scale(0);
                }
                
                .ripple-animate {
                    animation: ripple-animation 0.6s ease-out;
                }
                
                @keyframes ripple-animation {
                    to {
                        transform: scale(20);
                        opacity: 0;
                    }
                }
                
                /* Sidebar Overlay */
                .sidebar-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 999;
                    display: none;
                }
                
                /* Custom Tooltip */
                .custom-tooltip {
                    position: absolute;
                    background: #1e293b;
                    color: white;
                    padding: 0.5rem 0.75rem;
                    border-radius: 0.375rem;
                    font-size: 0.875rem;
                    z-index: 9999;
                    white-space: nowrap;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    display: none;
                }
                
                .custom-tooltip::after {
                    content: '';
                    position: absolute;
                    top: 100%;
                    left: 50%;
                    margin-left: -5px;
                    border: 5px solid transparent;
                    border-top-color: #1e293b;
                }
                
                /* Modern Notification */
                .modern-notification {
                    position: fixed;
                    top: 90px;
                    right: -400px;
                    background: white;
                    padding: 1rem 1.5rem;
                    border-radius: 0.75rem;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    min-width: 300px;
                    max-width: 400px;
                    z-index: 10000;
                    transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }
                
                .modern-notification.show {
                    right: 2rem;
                }
                
                .modern-notification i:first-child {
                    font-size: 1.5rem;
                }
                
                .modern-notification.success {
                    border-left: 4px solid #10b981;
                }
                
                .modern-notification.success i:first-child {
                    color: #10b981;
                }
                
                .modern-notification.error {
                    border-left: 4px solid #ef4444;
                }
                
                .modern-notification.error i:first-child {
                    color: #ef4444;
                }
                
                .modern-notification.warning {
                    border-left: 4px solid #f59e0b;
                }
                
                .modern-notification.warning i:first-child {
                    color: #f59e0b;
                }
                
                .modern-notification.info {
                    border-left: 4px solid #3b82f6;
                }
                
                .modern-notification.info i:first-child {
                    color: #3b82f6;
                }
                
                .modern-notification span {
                    flex: 1;
                    color: #334155;
                }
                
                .close-notification {
                    background: none;
                    border: none;
                    color: #94a3b8;
                    cursor: pointer;
                    padding: 0.25rem;
                    transition: color 0.2s;
                }
                
                .close-notification:hover {
                    color: #334155;
                }
                
                /* Page Loaded State */
                body.page-loaded {
                    opacity: 1;
                }
                
                /* Navbar Scrolled State */
                .navbar.scrolled {
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                }
                
                /* Loading Message */
                .loading-message {
                    color: white;
                    font-size: 1.125rem;
                    margin-top: 1rem;
                    text-align: center;
                }
            </style>
        `;
        
        if (!$('#modern-ui-dynamic-styles').length) {
            $('head').append(styles);
        }
    }

    // Initialize dynamic styles
    addDynamicStyles();

})(jQuery);

