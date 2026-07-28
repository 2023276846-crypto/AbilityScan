<template>
  <aside class="sidebar">
    <div class="sidebar-logo">
      <img :src="'/logo-icon.png'" alt="AbilityScan" class="logo-icon-img" />
      <span>AbilityScan</span>
    </div>
    <nav class="sidebar-nav">
      <router-link to="/pwd/dashboard" class="nav-item" active-class="active">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Dashboard
      </router-link>
      <router-link to="/pwd/profile" class="nav-item" active-class="active">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        My Profile
      </router-link>
      <router-link to="/pwd/qr" class="nav-item" active-class="active">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2M7 7h4v4H7zM13 13h4v4h-4zM7 13h4v4H7zM13 7h4v4h-4z"/></svg>
        QR Portfolio
      </router-link>
      <router-link to="/pwd/jobs" class="nav-item" active-class="active">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        Search Jobs
      </router-link>
      <router-link to="/pwd/mailbox" class="nav-item" active-class="active">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        Mailbox
        <span v-if="unreadCount > 0" class="nav-badge">{{ unreadCount }}</span>
      </router-link>
      <button @click="handleLogout" class="btn-logout" style="margin-top: 16px;">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle; flex-shrink: 0;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Logout
      </button>
    </nav>
  </aside>
</template>

<script>
import axios from 'axios'

export default {
  name: 'PWDSidebar',
  data() {
    return {
      unreadCount: 0,
      intervalId: null
    }
  },
  mounted() {
    this.fetchUnreadCount()
    this.intervalId = setInterval(this.fetchUnreadCount, 15000)
  },
  beforeUnmount() {
    if (this.intervalId) {
      clearInterval(this.intervalId)
    }
  },
  methods: {
    async fetchUnreadCount() {
      try {
        const token = localStorage.getItem('token')
        if (!token) return
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/messages')
        this.unreadCount = response.data.unread_count || 0
      } catch (err) {
        console.log('Failed to fetch unread count')
      }
    },
    async handleLogout() {
      try { await axios.post('/api/logout') } catch (err) {}
      localStorage.removeItem('token')
      localStorage.removeItem('role')
      this.$router.push('/login')
    }
  }
}
</script>

<style scoped>
.sidebar {
  width: 240px;
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  padding: 24px 16px;
  position: fixed;
  height: 100vh;
  z-index: 50;
  top: 0;
  left: 0;
}

.sidebar-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 18px;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 32px;
  padding: 0 8px;
}

.logo-icon-img {
  width: 44px;
  height: 44px;
  object-fit: contain;
  border-radius: 50%;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  background: white;
  border: 1px solid var(--border);
  padding: 2px;
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 4px;
  flex: 1;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 10px;
  color: var(--text-muted);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
  position: relative;
}

.nav-item:hover {
  background: var(--primary-light);
  color: var(--primary);
}

.nav-item.active {
  background: var(--primary);
  color: white;
}

.nav-badge {
  background: #ef4444;
  color: white;
  border-radius: 20px;
  padding: 1px 6px;
  font-size: 11px;
  margin-left: auto;
}

.btn-logout {
  padding: 12px 16px;
  background: #fef2f2;
  color: #ef4444;
  border: 1px solid #fecaca;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  text-align: left;
}

.btn-logout:hover {
  background: #ef4444;
  color: white;
}
</style>
