<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Admin Dashboard
          </h1>
          <p>System overview and management</p>
        </div>
        <button class="theme-toggle" @click="$emit('toggleTheme')" style="display: flex; align-items: center; gap: 6px;">
          <span v-if="isDark" style="display: flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.07" x2="5.64" y2="17.66"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            Light
          </span>
          <span v-else style="display: flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            Dark
          </span>
        </button>
      </div>

      <!-- Stats Grid -->
      <div class="stats-grid">
        <div class="stat-card blue">
          <div class="stat-icon" style="color: #1d4ed8; display: flex; align-items: center;">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="6" r="2"/><path d="M6 12h12M12 8v6M9 20l3-6 3 6"/></svg>
          </div>
          <div class="stat-info">
            <h3>{{ stats.totalPWD }}</h3>
            <p>Total PWD</p>
            <span class="stat-sub">{{ stats.pendingPWD }} pending</span>
          </div>
        </div>
        <div class="stat-card green">
          <div class="stat-icon" style="color: #166534; display: flex; align-items: center;">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="9" y1="16" x2="15" y2="16"/><line x1="15" y1="16" x2="15" y2="22"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/></svg>
          </div>
          <div class="stat-info">
            <h3>{{ stats.totalEmployers }}</h3>
            <p>Total Employers</p>
            <span class="stat-sub">{{ stats.pendingEmployers }} pending</span>
          </div>
        </div>
        <div class="stat-card purple">
          <div class="stat-icon" style="color: #7e22ce; display: flex; align-items: center;">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
          </div>
          <div class="stat-info">
            <h3>{{ stats.totalJobs }}</h3>
            <p>Total Jobs</p>
          </div>
        </div>
        <div class="stat-card orange">
          <div class="stat-icon" style="color: #c2410c; display: flex; align-items: center;">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
          </div>
          <div class="stat-info">
            <h3>{{ stats.totalApplications }}</h3>
            <p>Total Applications</p>
          </div>
        </div>
      </div>

      <!-- Pending Approvals -->
      <div class="content-grid">
        <!-- Pending PWD -->
        <div class="card">
          <div class="card-header">
            <h2>
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="header-icon"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              Pending PWD Approvals
            </h2>
            <router-link to="/admin/pwd" class="btn-viewall">View All</router-link>
          </div>
          <div v-if="recentPWD.filter(p => p.status === 'pending').length === 0" class="empty-state">
            <p style="display: flex; align-items: center; justify-content: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              No pending approvals
            </p>
          </div>
          <div v-else class="list">
            <div v-for="pwd in recentPWD.filter(p => p.status === 'pending')"
              :key="pwd.id" class="list-item">
              <div class="list-avatar">{{ getInitials(pwd.full_name) }}</div>
              <div class="list-info">
                <h4>{{ pwd.full_name }}</h4>
                <p>{{ pwd.user?.email }}</p>
                <p>OKU: {{ pwd.oku_number }}</p>
              </div>
              <div class="list-actions">
                <button @click="approvePWD(pwd)" class="btn-approve" title="Approve" style="display: flex; align-items: center; justify-content: center;">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </button>
                <button @click="rejectPWD(pwd)" class="btn-reject" title="Reject" style="display: flex; align-items: center; justify-content: center;">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Employers -->
        <div class="card">
          <div class="card-header">
            <h2>
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="header-icon"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              Pending Employer Approvals
            </h2>
            <router-link to="/admin/employers" class="btn-viewall">View All</router-link>
          </div>
          <div v-if="recentEmployers.filter(e => e.status === 'pending').length === 0" class="empty-state">
            <p style="display: flex; align-items: center; justify-content: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              No pending approvals
            </p>
          </div>
          <div v-else class="list">
            <div v-for="emp in recentEmployers.filter(e => e.status === 'pending')"
              :key="emp.id" class="list-item">
              <div class="list-avatar">{{ getInitials(emp.company_name) }}</div>
              <div class="list-info">
                <h4>{{ emp.company_name }}</h4>
                <p>{{ emp.user?.email }}</p>
                <p>SSM: {{ emp.ssm_number }}</p>
              </div>
              <div class="list-actions">
                <button @click="approveEmployer(emp)" class="btn-approve" title="Approve" style="display: flex; align-items: center; justify-content: center;">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </button>
                <button @click="rejectEmployer(emp)" class="btn-reject" title="Reject" style="display: flex; align-items: center; justify-content: center;">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="section">
        <h2>Quick Actions</h2>
        <div class="actions-grid">
          <router-link to="/admin/pwd" class="action-card">
            <span class="action-icon blue">
              <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="6" r="2"/><path d="M6 12h12M12 8v6M9 20l3-6 3 6"/></svg>
            </span>
            <p>Manage PWD</p>
          </router-link>
          <router-link to="/admin/employers" class="action-card">
            <span class="action-icon green">
              <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="9" y1="16" x2="15" y2="16"/><line x1="15" y1="16" x2="15" y2="22"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/></svg>
            </span>
            <p>Manage Employers</p>
          </router-link>
          <router-link to="/admin/reports" class="action-card">
            <span class="action-icon purple">
              <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </span>
            <p>View Reports</p>
          </router-link>
          <router-link to="/admin/monitor" class="action-card">
            <span class="action-icon orange">
              <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </span>
            <p>Monitor System</p>
          </router-link>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AdminDashboard',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      stats: {
        totalPWD: 0,
        totalEmployers: 0,
        totalJobs: 0,
        totalApplications: 0,
        pendingPWD: 0,
        pendingEmployers: 0,
        approvedPWD: 0,
        approvedEmployers: 0,
      },
      recentPWD: [],
      recentEmployers: []
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
        const response = await axios.get('/api/admin/dashboard')
        this.stats = response.data.stats || this.stats
        this.recentPWD = response.data.recentPWD || []
        this.recentEmployers = response.data.recentEmployers || []
      } catch (err) {
        this.$router.push('/login')
      }
    },
    async approvePWD(pwd) {
      try {
        await axios.put('/api/admin/pwds/' + pwd.id + '/status', { status: 'approved' })
        pwd.status = 'approved'
        this.stats.pendingPWD--
        this.stats.approvedPWD++
      } catch (err) { alert('Failed to approve') }
    },
    async rejectPWD(pwd) {
      try {
        await axios.put('/api/admin/pwds/' + pwd.id + '/status', { status: 'rejected' })
        pwd.status = 'rejected'
        this.stats.pendingPWD--
      } catch (err) { alert('Failed to reject') }
    },
    async approveEmployer(emp) {
      try {
        await axios.put('/api/admin/employers/' + emp.id + '/status', { status: 'approved' })
        emp.status = 'approved'
        this.stats.pendingEmployers--
        this.stats.approvedEmployers++
      } catch (err) { alert('Failed to approve') }
    },
    async rejectEmployer(emp) {
      try {
        await axios.put('/api/admin/employers/' + emp.id + '/status', { status: 'rejected' })
        emp.status = 'rejected'
        this.stats.pendingEmployers--
      } catch (err) { alert('Failed to reject') }
    },
    getInitials(name) {
      if (!name) return '?'
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
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

.topbar h1 { font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
.topbar h1 svg { color: var(--primary); }
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

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  border-radius: 16px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: var(--card-shadow);
}

.stat-card.blue { background: #eff6ff; border: 1px solid #bfdbfe; }
.stat-card.green { background: #f0fdf4; border: 1px solid #bbf7d0; }
.stat-card.purple { background: #faf5ff; border: 1px solid #e9d5ff; }
.stat-card.orange { background: #fff7ed; border: 1px solid #fed7aa; }

.stat-icon { display: flex; align-items: center; justify-content: center; }

.stat-card.blue .stat-info h3 { color: #1d4ed8; }
.stat-card.green .stat-info h3 { color: #166534; }
.stat-card.purple .stat-info h3 { color: #7e22ce; }
.stat-card.orange .stat-info h3 { color: #c2410c; }

.stat-info h3 { font-size: 28px; font-weight: 700; }
.stat-info p { font-size: 13px; color: var(--text-muted); }
.stat-sub { font-size: 11px; color: #ef4444; font-weight: 600; }

.content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 24px;
}

.card {
  background: var(--surface);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid var(--border);
  box-shadow: var(--card-shadow);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.card-header h2 { font-size: 15px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 6px; }
.card-header h2 svg.header-icon { color: var(--text-muted); }

.btn-viewall {
  font-size: 13px;
  color: var(--primary);
  text-decoration: none;
  font-weight: 600;
}

.empty-state {
  padding: 24px;
  text-align: center;
  color: #10b981;
  font-size: 14px;
  font-weight: 600;
}

.list { display: flex; flex-direction: column; gap: 12px; }

.list-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: var(--bg);
  border-radius: 10px;
  border: 1px solid var(--border);
}

.list-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  flex-shrink: 0;
}

.list-info { flex: 1; }
.list-info h4 { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 2px; }
.list-info p { font-size: 12px; color: var(--text-muted); margin-bottom: 1px; }

.list-actions { display: flex; gap: 6px; }

.btn-approve {
  padding: 6px 10px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.btn-approve:hover { background: #10b981; }

.btn-reject {
  padding: 6px 10px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.btn-reject:hover { background: #ef4444; }

.section { margin-bottom: 32px; }
.section h2 { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 16px; }

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
.action-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  margin: 0 auto;
  transition: all 0.2s;
}
.action-icon.blue { background: #eff6ff; color: #1d4ed8; }
.action-icon.green { background: #f0fdf4; color: #166534; }
.action-icon.purple { background: #faf5ff; color: #7e22ce; }
.action-icon.orange { background: #fff7ed; color: #c2410c; }
.action-card:hover .action-icon.blue { background: #1d4ed8; color: white; }
.action-card:hover .action-icon.green { background: #166534; color: white; }
.action-card:hover .action-icon.purple { background: #7e22ce; color: white; }
.action-card:hover .action-icon.orange { background: #c2410c; color: white; }
.action-card p { font-size: 14px; font-weight: 600; color: var(--text); margin-top: 12px; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .content-grid { grid-template-columns: 1fr; }
  .actions-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>