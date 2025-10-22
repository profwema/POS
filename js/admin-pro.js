/*!
 * ═════════════════════════════════════════════════════════════════
 * WAM Tech POS - Professional Admin Dashboard JavaScript
 * Version: 3.0 Professional Edition
 * ═════════════════════════════════════════════════════════════════
 */

;(function () {
  'use strict'

  // ═══════════════════════════════════════════════════════════════
  // 1. SIDEBAR TOGGLE
  // ═══════════════════════════════════════════════════════════════
  class SidebarController {
    constructor() {
      this.sidebar = document.querySelector('.admin-sidebar')
      this.toggleBtn = document.querySelector('.navbar-toggle')
      this.overlay = this.createOverlay()

      this.init()
    }

    init() {
      if (!this.sidebar || !this.toggleBtn) return

      // Toggle button click
      this.toggleBtn.addEventListener('click', () => this.toggle())

      // Overlay click (mobile)
      this.overlay.addEventListener('click', () => this.close())

      // Restore state from localStorage (desktop only)
      if (window.innerWidth >= 992) {
        const collapsed = localStorage.getItem('sidebarCollapsed') === 'true'
        if (collapsed) this.sidebar.classList.add('collapsed')
      }

      // Handle window resize
      window.addEventListener('resize', () => this.handleResize())
    }

    toggle() {
      if (window.innerWidth < 992) {
        // Mobile: show/hide
        this.sidebar.classList.toggle('show')
        this.overlay.classList.toggle('show')
        document.body.style.overflow = this.sidebar.classList.contains('show')
          ? 'hidden'
          : ''
      } else {
        // Desktop: collapse/expand
        this.sidebar.classList.toggle('collapsed')
        localStorage.setItem(
          'sidebarCollapsed',
          this.sidebar.classList.contains('collapsed')
        )
      }
    }

    close() {
      this.sidebar.classList.remove('show')
      this.overlay.classList.remove('show')
      document.body.style.overflow = ''
    }

    handleResize() {
      if (window.innerWidth >= 992) {
        this.close()
      } else {
        this.sidebar.classList.remove('collapsed')
      }
    }

    createOverlay() {
      let overlay = document.querySelector('.sidebar-overlay')
      if (!overlay) {
        overlay = document.createElement('div')
        overlay.className = 'sidebar-overlay'
        overlay.style.cssText = `
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.5);
          z-index: 1019;
          display: none;
          transition: opacity 0.3s ease;
        `
        document.body.appendChild(overlay)
      }
      return overlay
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 2. SUBMENU TOGGLE
  // ═══════════════════════════════════════════════════════════════
  class SubmenuController {
    constructor() {
      this.menuItems = document.querySelectorAll(
        '.nav-link[data-toggle="submenu"]'
      )
      this.init()
    }

    init() {
      this.menuItems.forEach((item) => {
        item.addEventListener('click', (e) => {
          e.preventDefault()
          this.toggleSubmenu(item)
        })
      })

      // Auto-expand active submenu
      this.expandActiveSubmenu()
    }

    toggleSubmenu(item) {
      const submenu = item.nextElementSibling
      if (!submenu || !submenu.classList.contains('nav-submenu')) return

      const isOpen = submenu.classList.contains('show')

      // Close all submenus
      document.querySelectorAll('.nav-submenu.show').forEach((menu) => {
        menu.classList.remove('show')
        menu.previousElementSibling.setAttribute('aria-expanded', 'false')
      })

      // Toggle current submenu
      if (!isOpen) {
        submenu.classList.add('show')
        item.setAttribute('aria-expanded', 'true')
      }
    }

    expandActiveSubmenu() {
      const activeLink = document.querySelector('.nav-link.active')
      if (activeLink) {
        const submenu = activeLink.closest('.nav-submenu')
        if (submenu) {
          submenu.classList.add('show')
          submenu.previousElementSibling.setAttribute('aria-expanded', 'true')
        }
      }
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 3. ACTIVE MENU HIGHLIGHTING
  // ═══════════════════════════════════════════════════════════════
  class ActiveMenuController {
    constructor() {
      this.currentPage = window.location.pathname.split('/').pop()
      this.init()
    }

    init() {
      const links = document.querySelectorAll('.nav-link[href]')
      links.forEach((link) => {
        const href = link.getAttribute('href')
        if (href === this.currentPage) {
          link.classList.add('active')
        }
      })
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 4. DARK/LIGHT MODE TOGGLE
  // ═══════════════════════════════════════════════════════════════
  class ThemeController {
    constructor() {
      this.toggleBtn = document.getElementById('themeToggle')
      this.themeIcon = document.getElementById('themeIcon')
      this.init()
    }

    init() {
      if (!this.toggleBtn) return

      // Load saved theme
      const savedTheme = localStorage.getItem('theme') || 'light'
      this.setTheme(savedTheme, false)

      // Toggle button click
      this.toggleBtn.addEventListener('click', () => {
        const currentTheme =
          document.documentElement.getAttribute('data-theme') || 'light'
        const newTheme = currentTheme === 'light' ? 'dark' : 'light'
        this.setTheme(newTheme, true)
      })
    }

    setTheme(theme, save = true) {
      document.documentElement.setAttribute('data-theme', theme)

      if (this.themeIcon) {
        this.themeIcon.className =
          theme === 'light' ? 'fas fa-moon' : 'fas fa-sun'
      }

      if (save) {
        localStorage.setItem('theme', theme)
      }
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 5. SEARCH FUNCTIONALITY
  // ═══════════════════════════════════════════════════════════════
  class SearchController {
    constructor() {
      this.searchInput = document.querySelector('.navbar-search-input')
      this.init()
    }

    init() {
      if (!this.searchInput) return

      // Ctrl/Cmd + K to focus
      document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
          e.preventDefault()
          this.searchInput.focus()
        }
      })

      // Search input
      this.searchInput.addEventListener('input', (e) => {
        this.handleSearch(e.target.value)
      })
    }

    handleSearch(query) {
      // Implement your search logic here
      console.log('Search query:', query)
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 6. NOTIFICATIONS (Optional)
  // ═══════════════════════════════════════════════════════════════
  class NotificationController {
    constructor() {
      this.notifBtn = document.querySelector('[data-action="notifications"]')
      this.init()
    }

    init() {
      if (!this.notifBtn) return

      this.notifBtn.addEventListener('click', () => {
        // Implement notification dropdown
        console.log('Show notifications')
      })
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 7. TOOLTIPS INITIALIZATION
  // ═══════════════════════════════════════════════════════════════
  function initTooltips() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
      ;[...tooltips].map((el) => new bootstrap.Tooltip(el))
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 8. SMOOTH SCROLLING
  // ═══════════════════════════════════════════════════════════════
  function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href')
        if (href !== '#' && !href.includes('Menu')) {
          e.preventDefault()
          const target = document.querySelector(href)
          if (target) {
            target.scrollIntoView({ behavior: 'smooth' })
          }
        }
      })
    })
  }

  // ═══════════════════════════════════════════════════════════════
  // 9. LOGOUT FUNCTIONALITY
  // ═══════════════════════════════════════════════════════════════
  function initLogout() {
    const logoutBtn = document.getElementById('logout')
    if (logoutBtn) {
      logoutBtn.addEventListener('click', function (e) {
        e.preventDefault()
        e.stopPropagation()

        // Call logout function if exists
        if (typeof logout === 'function') {
          logout()
        } else if (typeof $ !== 'undefined') {
          // Fallback: direct AJAX call
          $.ajax({
            url: 'controller.php?l=1&f=logout',
            success: function (result) {
              location.replace('index.php')
            },
          })
        }
      })
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // 10. INITIALIZE ALL
  // ═══════════════════════════════════════════════════════════════
  function init() {
    // Wait for DOM
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initAll)
    } else {
      initAll()
    }
  }

  function initAll() {
    // Initialize controllers
    new SidebarController()
    new SubmenuController()
    new ActiveMenuController()
    new ThemeController()
    new SearchController()
    new NotificationController()

    // Initialize utilities
    initTooltips()
    initSmoothScrolling()
    initLogout()

    console.log('✅ Admin Pro Dashboard initialized successfully!')
  }

  // Start initialization
  init()

  // Expose for external use
  window.AdminPro = {
    version: '3.0',
    init: initAll,
  }
})()
