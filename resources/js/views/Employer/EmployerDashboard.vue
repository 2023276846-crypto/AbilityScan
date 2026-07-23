<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <EmployerSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <div class="topbar">
        <div>
          <h1>Welcome, {{ user.name || 'Employer' }}!</h1>
          <p>Manage your job postings and find the right candidates</p>
        </div>
        <button class="theme-toggle" @click="$emit('toggleTheme')">
          {{ isDark ? '☀️ Light' : '🌙 Dark' }}
        </button>
      </div>

      <!-- Status Banner -->
      <div :class="['status-banner', profileStatus]">
        <span v-if="profileStatus === 'pending'" style="display: inline-flex; align-items: center; gap: 8px;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          Your company account is pending admin approval.
        </span>
        <span v-else-if="profileStatus === 'approved'" style="display: inline-flex; align-items: center; gap: 8px;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          Your company account is approved! You can post jobs.
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
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4z"/></svg>
          </div>
          <div class="stat-info">
            <h3>{{ stats.totalJobs }}</h3>
            <p>Jobs Posted</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="color: var(--primary);">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="stat-info">
            <h3>{{ stats.totalApplications }}</h3>
            <p>Applications</p>
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
        <div class="stat-card">
          <div class="stat-icon" style="color: var(--primary);">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2M7 7h4v4H7zM13 13h4v4h-4zM7 13h4v4H7zM13 7h4v4h-4z"/></svg>
          </div>
          <div class="stat-info">
            <h3>{{ stats.qrScans }}</h3>
            <p>QR Scans</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="section">
        <h2>Quick Actions</h2>
        <div class="actions-grid">
          <router-link to="/employer/post-job" class="action-card">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4z"/></svg>
            <p>Post New Job</p>
          </router-link>
          <router-link to="/employer/candidates" class="action-card">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <p>Search Candidates</p>
          </router-link>
          <router-link to="/employer/scan-qr" class="action-card">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2M7 7h4v4H7zM13 13h4v4h-4zM7 13h4v4H7zM13 7h4v4h-4z"/></svg>
            <p>Scan QR Code</p>
          </router-link>
          <router-link to="/employer/mailbox" class="action-card">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <p>Check Mailbox</p>
          </router-link>
        </div>
      </div>

      <!-- Recent Job Postings -->
      <div class="section">
        <div class="section-header">
          <h2>Recent Job Postings</h2>
          <router-link to="/employer/post-job" class="btn-add">+ Post New Job</router-link>
        </div>
        <div v-if="jobs.length === 0" class="empty-state">
          <p>No jobs posted yet.</p>
          <router-link to="/employer/post-job" class="btn-primary">Post Your First Job</router-link>
        </div>
        <div v-else class="jobs-list">
          <div v-for="job in jobs" :key="job.id" class="job-item">
            <div class="job-info">
              <h4>{{ job.job_title }}</h4>
              <p>
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 4px; color: var(--text-muted);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ job.location }}
              </p>
            </div>
            <div class="job-stats">
              <span>
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 4px; color: var(--text-muted);"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                {{ job.applications_count || 0 }} applicants
              </span>
            </div>
            <div :class="['job-status', job.job_status]">
              {{ job.job_status }}
            </div>
            <div class="job-actions">
              <router-link :to="'/employer/edit-job/' + job.id" class="btn-edit">Edit</router-link>
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
  name: 'EmployerDashboard',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      user: {},
      profileStatus: 'pending',
      stats: {
        totalJobs: 0,
        totalApplications: 0,
        accepted: 0,
        qrScans: 0
      },
      jobs: []
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
        const response = await axios.get('/api/employer/dashboard')
        this.user = response.data.user || {}
        this.profileStatus = response.data.status || 'pending'
        this.stats = response.data.stats || this.stats
        this.jobs = response.data.jobs || []
      } catch (err) {
        this.$router.push('/login')
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
.dashboard {
  display: flex;
  min-height: 100vh;
  background: var(--bg);
}

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

.nav-item:hover { background: var(--primary-light); color: var(--primary); }
.nav-item.active { background: var(--primary); color: white; }

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

.btn-logout:hover { background: #ef4444; color: white; }

.main-content {
  margin-left: 240px;
  flex: 1;
  padding: 32px;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}

.topbar h1 { font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.topbar p { font-size: 14px; color: var(--text-muted); }

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

.theme-toggle:hover { border-color: var(--primary); color: var(--primary); }

.status-banner {
  padding: 14px 20px;
  border-radius: 10px;
  margin-bottom: 24px;
  font-size: 14px;
  font-weight: 600;
}

.status-banner.pending { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
.status-banner.approved { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.status-banner.rejected { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

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
.stat-info h3 { font-size: 28px; font-weight: 700; color: var(--primary); }
.stat-info p { font-size: 13px; color: var(--text-muted); }

.section { margin-bottom: 32px; }

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section h2 { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 16px; }
.section-header h2 { margin-bottom: 0; }

.btn-add {
  padding: 8px 16px;
  background: var(--primary);
  color: white;
  border-radius: 8px;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
}

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
.action-card p { font-size: 14px; font-weight: 600; color: var(--text); margin-top: 8px; }

.empty-state {
  background: var(--surface);
  border-radius: 16px;
  padding: 40px;
  text-align: center;
  border: 1px solid var(--border);
}

.empty-state p { color: var(--text-muted); margin-bottom: 16px; font-size: 15px; }

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

.jobs-list { display: flex; flex-direction: column; gap: 12px; }

.job-item {
  background: var(--surface);
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border: 1px solid var(--border);
}

.job-info h4 { font-size: 15px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
.job-info p { font-size: 13px; color: var(--text-muted); }

.job-stats { font-size: 13px; color: var(--text-muted); }

.job-status {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.job-status.open { background: #f0fdf4; color: #166534; }
.job-status.closed { background: #fef2f2; color: #991b1b; }

.btn-edit {
  padding: 6px 14px;
  border: 2px solid var(--border);
  border-radius: 8px;
  color: var(--text);
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-edit:hover { border-color: var(--primary); color: var(--primary); }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .actions-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>