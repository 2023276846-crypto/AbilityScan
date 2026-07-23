<template>
  <aside class="sidebar">
    <div class="sidebar-logo">
      <img :src="'/logo-icon.png'" alt="AbilityScan" class="logo-icon-img" />
      <span>AbilityScan</span>
    </div>

    <!-- Admin Profile Info -->
    <div class="admin-profile" v-if="user">
      <div class="admin-avatar">{{ initials }}</div>
      <div class="admin-info">
        <h4 class="admin-name">{{ user.name }}</h4>
        <span class="admin-id-badge">{{ user.admin_id || 'ADM-SYSTEM' }}</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <router-link to="/admin/dashboard" class="nav-item" active-class="active">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Dashboard
      </router-link>
      <router-link to="/admin/pwd" class="nav-item" active-class="active">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Manage PWD
      </router-link>
      <router-link to="/admin/employers" class="nav-item" active-class="active">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="9" y1="16" x2="15" y2="16"/><line x1="15" y1="16" x2="15" y2="22"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/></svg>
        Manage Employers
      </router-link>
      <router-link to="/admin/reports" class="nav-item" active-class="active">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        Reports
      </router-link>
      <router-link to="/admin/monitor" class="nav-item" active-class="active">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Monitor
      </router-link>
    </nav>
    <button @click="handleLogout" class="btn-logout">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle; flex-shrink: 0;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
      Logout
    </button>
  </aside>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AdminSidebar',
  data() {
    return {
      user: null
    }
  },
  computed: {
    initials() {
      if (!this.user?.name) return 'AD'
      return this.user.name
        .split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }
  },
  mounted() {
    this.fetchUser()
  },
  methods: {
    async fetchUser() {
      try {
        const token = localStorage.getItem('token')
        if (!token) return
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/user')
        this.user = response.data
      } catch (err) {
        console.log('Failed to fetch admin user info')
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
  margin-bottom: 24px;
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

/* Admin Profile Card */
.admin-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 12px;
  margin-bottom: 24px;
}

.admin-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: var(--primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  flex-shrink: 0;
}

.admin-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.admin-name {
  font-size: 13px;
  font-weight: 700;
  color: var(--text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.admin-id-badge {
  display: inline-block;
  font-size: 10px;
  font-weight: 700;
  color: var(--primary);
  background: var(--primary-light);
  padding: 1px 6px;
  border-radius: 6px;
  width: fit-content;
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
}

.nav-item:hover {
  background: var(--primary-light);
  color: var(--primary);
}

.nav-item.active {
  background: var(--primary);
  color: white;
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
