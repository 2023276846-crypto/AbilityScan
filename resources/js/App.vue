<template>
  <div :class="isDark ? 'dark-mode' : 'light-mode'" id="app-wrapper">
    <!-- Floating Hamburger Button for Mobile -->
    <button 
      class="mobile-hamburger" 
      v-if="!isAuthPage"
      @click="toggleMobileMenu"
    >
      <span v-if="!isMobileMenuOpen">☰</span>
      <span v-else>✖</span>
    </button>

    <!-- Overlay when sidebar is open on mobile -->
    <div 
      class="mobile-overlay" 
      v-if="isMobileMenuOpen" 
      @click="toggleMobileMenu"
    ></div>

    <router-view :isDark="isDark" @toggleTheme="toggleTheme" />

    <!-- Toast Notifications Container -->
    <div class="toast-container">
      <transition-group name="toast-fade">
        <div 
          v-for="t in toastState.toasts" 
          :key="t.id" 
          :class="['toast-item', t.type]"
        >
          <!-- Custom Premium SVG Icons -->
          <div class="toast-icon-wrapper">
            <svg v-if="t.type === 'success'" class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <svg v-else-if="t.type === 'error'" class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <svg v-else-if="t.type === 'warning'" class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
              <line x1="12" y1="9" x2="12" y2="13"></line>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <svg v-else class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
          </div>
          <div class="toast-content">
            <span class="toast-title">{{ getToastTitle(t.type) }}</span>
            <span class="toast-message">{{ t.message }}</span>
          </div>
          <button class="toast-close-btn" @click="removeToast(t.id)">
            ✕
          </button>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script>
import { toastState, toast } from './services/toast';
export default {
  name: 'App',
  data() {
    return {
      isDark: localStorage.getItem('theme') === 'dark',
      isMobileMenuOpen: false
    }
  },
  computed: {
    toastState() {
      return toastState;
    },
    isAuthPage() {
      // Hide hamburger on public/auth pages that don't have a sidebar
      const path = this.$route.path;
      return path === '/' || path === '/login' || path.startsWith('/register');
    }
  },
  watch: {
    // Automatically close the mobile menu when navigating
    $route() {
      if (this.isMobileMenuOpen) {
        this.toggleMobileMenu();
      }
    }
  },
  methods: {
    toggleTheme() {
      this.isDark = !this.isDark
      localStorage.setItem('theme', this.isDark ? 'dark' : 'light')
    },
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;
      if (this.isMobileMenuOpen) {
        document.body.classList.add('mobile-menu-open');
      } else {
        document.body.classList.remove('mobile-menu-open');
      }
    },
    removeToast(id) {
      toast.remove(id);
    },
    getToastTitle(type) {
      if (type === 'success') return 'Success';
      if (type === 'error') return 'Error';
      if (type === 'warning') return 'Warning';
      return 'Notification';
    }
  },
  mounted() {
    const saved = localStorage.getItem('theme')
    this.isDark = saved === 'dark'
  }
}
</script>

<style>
/* ===== GLOBAL LIGHT MODE ===== */
.light-mode {
  --bg: #f8fafc;
  --surface: #ffffff;
  --navbar-bg: #ffffff;
  --text: #1e293b;
  --text-muted: #64748b;
  --border: #e2e8f0;
  --primary: #1e40af;
  --primary-hover: #1e3a8a;
  --primary-light: #eff6ff;
  --input-bg: #ffffff;
  --card-shadow: 0 4px 24px rgba(0,0,0,0.08);
  --footer-bg: #ffffff;
}

/* ===== GLOBAL DARK MODE ===== */
.dark-mode {
  --bg: #090d16;
  --surface: #121b2d;
  --navbar-bg: #121b2d;
  --text: #f1f5f9;
  --text-muted: #94a3b8;
  --border: #1e293b;
  --primary: #3b82f6;
  --primary-hover: #2563eb;
  --primary-light: #1e3a5f;
  --input-bg: #090d16;
  --card-shadow: 0 4px 24px rgba(0,0,0,0.3);
  --footer-bg: #121b2d;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  font-family: 'Segoe UI', sans-serif;
  background: var(--bg);
  color: var(--text);
  transition: background 0.3s, color 0.3s;
}

#app-wrapper {
  min-height: 100vh;
}

/* ===== MOBILE NAVIGATION FIXES ===== */
.mobile-hamburger {
  display: none;
  position: fixed;
  top: 24px;
  left: 24px;
  z-index: 10000;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 8px;
  width: 44px;
  height: 44px;
  font-size: 20px;
  cursor: pointer;
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.mobile-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(2px);
  z-index: 9998;
}

@media (max-width: 1024px) {
  .mobile-hamburger {
    display: flex !important;
  }
  
  /* Hide sidebar globally on mobile/tablet by default when menu is closed */
  .sidebar {
    display: none !important;
  }

  body.mobile-menu-open .sidebar {
    display: flex !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    height: 100vh !important;
    width: 260px !important;
    z-index: 9999 !important;
    background: var(--surface) !important;
    box-shadow: 2px 0 15px rgba(0,0,0,0.5) !important;
    transform: translateX(0) !important;
    transition: transform 0.3s ease !important;
  }

  .main-content {
    margin-left: 0 !important;
    padding-left: 20px !important;
    padding-right: 20px !important;
  }
  
  /* Adjust main-content padding to not overlap with the hamburger button */
  .main-content .topbar {
    padding-left: 56px !important; /* Push content to the right of the hamburger */
  }

  /* When menu is open, show overlay and sidebar */
  body.mobile-menu-open {
    overflow: hidden; /* Prevent background scrolling */
  }

  body.mobile-menu-open .mobile-overlay {
    display: block !important;
  }
}

/* ===== TOAST NOTIFICATIONS ===== */
.toast-container {
  position: fixed;
  top: 24px;
  right: 24px;
  z-index: 100001; /* Above mobile hamburger and overlays */
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-width: 400px;
  width: calc(100% - 48px);
}

.toast-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
  backdrop-filter: blur(16px) saturate(180%);
  -webkit-backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.08);
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  overflow: hidden;
}

/* Light / Dark Mode Toast Themes */
.light-mode .toast-item {
  box-shadow: 0 10px 30px rgba(30, 41, 59, 0.08);
  border: 1px solid rgba(226, 232, 240, 0.8);
}

.light-mode .toast-item.success {
  background: rgba(240, 253, 244, 0.95);
  border-left: 5px solid #10b981;
}
.light-mode .toast-item.error {
  background: rgba(254, 242, 242, 0.95);
  border-left: 5px solid #ef4444;
}
.light-mode .toast-item.warning {
  background: rgba(255, 251, 235, 0.95);
  border-left: 5px solid #f59e0b;
}
.light-mode .toast-item.info {
  background: rgba(239, 246, 255, 0.95);
  border-left: 5px solid #3b82f6;
}

.dark-mode .toast-item {
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.dark-mode .toast-item.success {
  background: rgba(16, 185, 129, 0.12);
  border-left: 5px solid #10b981;
}
.dark-mode .toast-item.error {
  background: rgba(239, 68, 68, 0.12);
  border-left: 5px solid #ef4444;
}
.dark-mode .toast-item.warning {
  background: rgba(245, 158, 11, 0.12);
  border-left: 5px solid #f59e0b;
}
.dark-mode .toast-item.info {
  background: rgba(59, 130, 246, 0.12);
  border-left: 5px solid #3b82f6;
}

/* Icons styling */
.toast-icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  flex-shrink: 0;
}

.toast-item.success .toast-icon-wrapper {
  background: rgba(16, 185, 129, 0.15);
  color: #10b981;
}
.toast-item.error .toast-icon-wrapper {
  background: rgba(239, 68, 68, 0.15);
  color: #ef4444;
}
.toast-item.warning .toast-icon-wrapper {
  background: rgba(245, 158, 11, 0.15);
  color: #f59e0b;
}
.toast-item.info .toast-icon-wrapper {
  background: rgba(59, 130, 246, 0.15);
  color: #3b82f6;
}

.toast-icon {
  width: 18px;
  height: 18px;
}

/* Toast Text Content */
.toast-content {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
}

.toast-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--text);
}

.toast-message {
  font-size: 13px;
  color: var(--text-muted);
  line-height: 1.4;
}

.toast-close-btn {
  background: transparent;
  border: none;
  color: var(--text-muted);
  font-size: 14px;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s, transform 0.2s;
  align-self: flex-start;
  margin-top: -2px;
}

.toast-close-btn:hover {
  color: var(--text);
  transform: scale(1.1);
}

/* Toast Animation transitions */
.toast-fade-enter-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.toast-fade-leave-active {
  transition: all 0.3s cubic-bezier(0.85, 0, 0.15, 1);
  position: absolute; /* for smooth list layout transition */
}
.toast-fade-enter-from {
  opacity: 0;
  transform: translateX(50px) scale(0.9);
}
.toast-fade-leave-to {
  opacity: 0;
  transform: translateX(100px) scale(0.9);
}
.toast-fade-move {
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>