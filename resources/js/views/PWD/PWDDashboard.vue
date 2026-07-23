<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <PWDSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <div class="topbar">
        <div>
          <h1>Welcome back, {{ user.name || 'PWD User' }}!</h1>
          <p>Here's your job matching overview</p>
        </div>
        <button class="theme-toggle" @click="$emit('toggleTheme')">
          {{ isDark ? '☀️ Light' : '🌙 Dark' }}
        </button>
      </div>

      <!-- Status Banner -->
      <div :class="['status-banner', profileStatus]">
        <span v-if="profileStatus === 'pending'" style="display: inline-flex; align-items: center; gap: 8px;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          Your account is pending admin approval.
        </span>
        <span v-else-if="profileStatus === 'approved'" style="display: inline-flex; align-items: center; gap: 8px;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          Your account is approved! You can apply for jobs.
        </span>
        <span v-else-if="profileStatus === 'rejected'" style="display: inline-flex; align-items: center; gap: 8px;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Your account was rejected. Please contact admin.
        </span>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon" style="color: var(--primary);">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
          </div>
          <div class="stat-info">
            <h3>{{ stats.totalJobs }}</h3>
            <p>Jobs Available</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="color: var(--primary);">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><path d="M2 12h20"/></svg>
          </div>
          <div class="stat-info">
            <h3>{{ stats.matchedJobs }}</h3>
            <p>Matched Jobs</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="color: var(--primary);">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          </div>
          <div class="stat-info">
            <h3>{{ stats.applications }}</h3>
            <p>Applications Sent</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="color: #10b981;">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div class="stat-info">
            <h3>{{ stats.accepted }}</h3>
            <p>Accepted</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="section">
        <h2>Quick Actions</h2>
        <div class="actions-grid">
          <router-link to="/pwd/profile" class="action-card">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <p>Update Profile</p>
          </router-link>
          <router-link to="/pwd/qr" class="action-card">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2M7 7h4v4H7zM13 13h4v4h-4zM7 13h4v4H7zM13 7h4v4h-4z"/></svg>
            <p>View QR Portfolio</p>
          </router-link>
          <router-link to="/pwd/jobs" class="action-card">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <p>Search Jobs</p>
          </router-link>
          <router-link to="/pwd/mailbox" class="action-card">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <p>Check Mailbox</p>
          </router-link>
        </div>
      </div>

      <!-- Recent Applications -->
      <div class="section">
        <h2>Recent Applications</h2>
        <div v-if="applications.length === 0" class="empty-state">
          <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted); margin-bottom: 16px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <p>No applications yet. Start searching for jobs!</p>
          <router-link to="/pwd/jobs" class="btn-primary">Search Jobs</router-link>
        </div>
        <div v-else class="applications-list">
          <div v-for="app in applications" :key="app.id" class="application-item">
            <div class="app-info">
              <h4>{{ app.job_title }}</h4>
              <p>{{ app.company_name }} • {{ app.location }}</p>
            </div>
            <div class="app-score">
              <span class="score">{{ app.match_score }}%</span>
              <p>Match</p>
            </div>
            <div :class="['app-status', app.application_status]">
              {{ app.application_status }}
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'PWDDashboard',
  props: {
    isDark: Boolean
  },
  emits: ['toggleTheme'],
  data() {
    return {
      user: {},
      profileStatus: 'pending',
      stats: {
        totalJobs: 0,
        matchedJobs: 0,
        applications: 0,
        accepted: 0
      },
      applications: []
    }
  },
  mounted() {
    this.loadDashboard()
  },
  methods: {
    async loadDashboard() {
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        // Load user
        const userResponse = await axios.get('/api/user')
        this.user = userResponse.data

        // Load dashboard stats
        const dashResponse = await axios.get('/api/pwd/dashboard')
        const data = dashResponse.data

        this.profileStatus = data.profile?.status || 'pending'

        this.stats.totalJobs = await axios.get('/api/pwd/jobs')
          .then(r => r.data.jobs?.length || 0).catch(() => 0)

        this.stats.applications = data.stats?.totalApplications || 0
        this.stats.accepted = data.stats?.accepted || 0
        this.stats.matchedJobs = data.stats?.pending || 0

        // Load recent applications
        const appResponse = await axios.get('/api/pwd/applications')
        const apps = appResponse.data.applications || []
        this.applications = apps.slice(0, 5).map(app => ({
          id: app.id,
          job_title: app.job?.job_title || 'Unknown Job',
          company_name: app.job?.employer?.company_name || 'Company',
          location: app.job?.location || '',
          match_score: app.match_score || 0,
          application_status: app.application_status
        }))

      } catch (err) {
        this.$router.push('/login')
      }
    },
    async handleLogout() {
      try {
        await axios.post('/api/logout')
      } catch (err) {}
      localStorage.removeItem('token')
      localStorage.removeItem('role')
      this.$router.push('/login')
    }
  }
}
</script>

<style scoped>
.dashboard {
  display: flex;
  min-height: 100vh;
  background: var(--bg);
}

/* Sidebar */
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

/* Main Content */
.main-content {
  margin-left: 240px;
  flex: 1;
  padding: 32px;
}

/* Topbar */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}

.topbar h1 {
  font-size: 24px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 4px;
}

.topbar p {
  font-size: 14px;
  color: var(--text-muted);
}

.theme-toggle {
  padding: 8px 16px;
  border: 2px solid var(--border);
  border-radius: 8px;
  background: var(--surface);
  color: var(--text);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.theme-toggle:hover {
  border-color: var(--primary);
  color: var(--primary);
}

/* Status Banner */
.status-banner {
  padding: 14px 20px;
  border-radius: 10px;
  margin-bottom: 24px;
  font-size: 14px;
  font-weight: 600;
}

.status-banner.pending {
  background: #fef9c3;
  color: #854d0e;
  border: 1px solid #fde047;
}

.status-banner.approved {
  background: #f0fdf4;
  color: #166534;
  border: 1px solid #bbf7d0;
}

.status-banner.rejected {
  background: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 32px;
}

.stat-card {
  background: var(--surface);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  border: 1px solid var(--border);
  box-shadow: var(--card-shadow);
}

.stat-icon { font-size: 32px; }

.stat-info h3 {
  font-size: 28px;
  font-weight: 700;
  color: var(--primary);
}

.stat-info p {
  font-size: 13px;
  color: var(--text-muted);
}

/* Sections */
.section {
  margin-bottom: 32px;
}

.section h2 {
  font-size: 18px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 16px;
}

/* Quick Actions */
.actions-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.action-card {
  background: var(--surface);
  border-radius: 16px;
  padding: 24px;
  text-align: center;
  text-decoration: none;
  border: 1px solid var(--border);
  box-shadow: var(--card-shadow);
  transition: transform 0.2s;
}

.action-card:hover { transform: translateY(-4px); }

.action-card span { font-size: 32px; }

.action-card p {
  font-size: 14px;
  font-weight: 600;
  color: var(--text);
  margin-top: 8px;
}

/* Applications */
.empty-state {
  background: var(--surface);
  border-radius: 16px;
  padding: 40px;
  text-align: center;
  border: 1px solid var(--border);
}

.empty-state p {
  color: var(--text-muted);
  margin-bottom: 16px;
  font-size: 15px;
}

.btn-primary {
  display: inline-block;
  padding: 10px 24px;
  background: var(--primary);
  color: white;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
}

.applications-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.application-item {
  background: var(--surface);
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border: 1px solid var(--border);
}

.app-info h4 {
  font-size: 15px;
  font-weight: 600;
  color: var(--text);
  margin-bottom: 4px;
}

.app-info p {
  font-size: 13px;
  color: var(--text-muted);
}

.app-score {
  text-align: center;
}

.score {
  font-size: 20px;
  font-weight: 700;
  color: var(--primary);
}

.app-score p {
  font-size: 12px;
  color: var(--text-muted);
}

.app-status {
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.app-status.pending {
  background: #fef9c3;
  color: #854d0e;
}

.app-status.accepted {
  background: #f0fdf4;
  color: #166534;
}

.app-status.rejected {
  background: #fef2f2;
  color: #991b1b;
}

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .actions-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>