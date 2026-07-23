<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            Generate Reports
          </h1>
          <p>System statistics and analytics</p>
        </div>
        <div class="topbar-actions">
          <button @click="printReport" class="btn-print">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print Report
          </button>
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
      </div>

      <div v-if="loading" class="loading-state">
        <p style="display: flex; align-items: center; justify-content: center; gap: 8px;">
          <svg class="animate-spin" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
          Loading reports...
        </p>
      </div>

      <div v-else id="report-content">
        <!-- Summary Stats -->
        <div class="stats-grid">
          <div class="stat-card blue">
            <div class="stat-icon" style="color: #1d4ed8; display: flex; align-items: center;">
              <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="6" r="2"/><path d="M6 12h12M12 8v6M9 20l3-6 3 6"/></svg>
            </div>
            <div class="stat-info">
              <h3>{{ totalPWD }}</h3>
              <p>Total PWD</p>
            </div>
          </div>
          <div class="stat-card green">
            <div class="stat-icon" style="color: #166534; display: flex; align-items: center;">
              <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="9" y1="16" x2="15" y2="16"/><line x1="15" y1="16" x2="15" y2="22"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/></svg>
            </div>
            <div class="stat-info">
              <h3>{{ totalEmployers }}</h3>
              <p>Total Employers</p>
            </div>
          </div>
          <div class="stat-card purple">
            <div class="stat-icon" style="color: #7e22ce; display: flex; align-items: center;">
              <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
            <div class="stat-info">
              <h3>{{ totalJobs }}</h3>
              <p>Total Jobs</p>
            </div>
          </div>
          <div class="stat-card orange">
            <div class="stat-icon" style="color: #c2410c; display: flex; align-items: center;">
              <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
            </div>
            <div class="stat-info">
              <h3>{{ totalApplications }}</h3>
              <p>Total Applications</p>
            </div>
          </div>
        </div>

        <!-- Hiring Trends Section -->
        <div class="card full-card">
          <div class="card-header">
            <h2>
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
              Hiring Trends
            </h2>
            <div class="period-tabs">
              <button :class="['period-tab', hiringPeriod === 'weekly' ? 'active' : '']"
                @click="changePeriod('weekly')">Weekly</button>
              <button :class="['period-tab', hiringPeriod === 'monthly' ? 'active' : '']"
                @click="changePeriod('monthly')">Monthly</button>
              <button :class="['period-tab', hiringPeriod === 'yearly' ? 'active' : '']"
                @click="changePeriod('yearly')">Yearly</button>
            </div>
          </div>
          <div class="charts-row">
            <!-- Line/Bar Chart -->
            <div class="chart-wrapper large">
              <h4>Applications Over Time</h4>
              <canvas ref="hiringChart"></canvas>
            </div>
            <!-- Pie Chart -->
            <div class="chart-wrapper small">
              <h4>Application Status</h4>
              <canvas ref="statusPieChart"></canvas>
              <div class="pie-legend">
                <div class="legend-item">
                  <span class="legend-dot green"></span>
                  <span>Accepted: {{ reports.applicationsByStatus?.accepted || 0 }}</span>
                </div>
                <div class="legend-item">
                  <span class="legend-dot yellow"></span>
                  <span>Pending: {{ reports.applicationsByStatus?.pending || 0 }}</span>
                </div>
                <div class="legend-item">
                  <span class="legend-dot red"></span>
                  <span>Rejected: {{ reports.applicationsByStatus?.rejected || 0 }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- PWD & Employer Pie Charts -->
        <div class="content-grid">
          <div class="card">
            <h2>
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><circle cx="12" cy="6" r="2"/><path d="M6 12h12M12 8v6M9 20l3-6 3 6"/></svg>
              PWD Account Status
            </h2>
            <div class="pie-section">
              <canvas ref="pwdPieChart"></canvas>
              <div class="pie-legend">
                <div class="legend-item">
                  <span class="legend-dot green"></span>
                  <span>Approved: {{ reports.pwdByStatus?.approved || 0 }}</span>
                </div>
                <div class="legend-item">
                  <span class="legend-dot yellow"></span>
                  <span>Pending: {{ reports.pwdByStatus?.pending || 0 }}</span>
                </div>
                <div class="legend-item">
                  <span class="legend-dot red"></span>
                  <span>Rejected: {{ reports.pwdByStatus?.rejected || 0 }}</span>
                </div>
              </div>
            </div>
            <div class="chart-bars">
              <div class="bar-item" v-for="(val, key) in reports.pwdByStatus" :key="key">
                <div class="bar-label">
                  <span class="capitalize">{{ key }}</span>
                  <span class="bar-value">{{ val }}</span>
                </div>
                <div class="bar-track">
                  <div :class="['bar-fill', key === 'approved' ? 'green' : key === 'pending' ? 'yellow' : 'red']"
                    :style="{ width: getPercent(val, totalPWD) + '%' }"></div>
                </div>
                <span class="bar-percent">{{ getPercent(val, totalPWD) }}%</span>
              </div>
            </div>
          </div>

          <div class="card">
            <h2>
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="9" y1="16" x2="15" y2="16"/><line x1="15" y1="16" x2="15" y2="22"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/></svg>
              Employer Account Status
            </h2>
            <div class="pie-section">
              <canvas ref="employerPieChart"></canvas>
              <div class="pie-legend">
                <div class="legend-item">
                  <span class="legend-dot green"></span>
                  <span>Approved: {{ reports.employerByStatus?.approved || 0 }}</span>
                </div>
                <div class="legend-item">
                  <span class="legend-dot yellow"></span>
                  <span>Pending: {{ reports.employerByStatus?.pending || 0 }}</span>
                </div>
                <div class="legend-item">
                  <span class="legend-dot red"></span>
                  <span>Rejected: {{ reports.employerByStatus?.rejected || 0 }}</span>
                </div>
              </div>
            </div>
            <div class="chart-bars">
              <div class="bar-item" v-for="(val, key) in reports.employerByStatus" :key="key">
                <div class="bar-label">
                  <span class="capitalize">{{ key }}</span>
                  <span class="bar-value">{{ val }}</span>
                </div>
                <div class="bar-track">
                  <div :class="['bar-fill', key === 'approved' ? 'green' : key === 'pending' ? 'yellow' : 'red']"
                    :style="{ width: getPercent(val, totalEmployers) + '%' }"></div>
                </div>
                <span class="bar-percent">{{ getPercent(val, totalEmployers) }}%</span>
              </div>
            </div>
          </div>

          <!-- Jobs Pie -->
          <div class="card">
            <h2>
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
              Job Vacancy Status
            </h2>
            <div class="pie-section">
              <canvas ref="jobsPieChart"></canvas>
              <div class="pie-legend">
                <div class="legend-item">
                  <span class="legend-dot green"></span>
                  <span>Open: {{ reports.jobsByStatus?.open || 0 }}</span>
                </div>
                <div class="legend-item">
                  <span class="legend-dot red"></span>
                  <span>Closed: {{ reports.jobsByStatus?.closed || 0 }}</span>
                </div>
              </div>
            </div>
            <div class="chart-bars">
              <div class="bar-item" v-for="(val, key) in reports.jobsByStatus" :key="key">
                <div class="bar-label">
                  <span class="capitalize">{{ key }}</span>
                  <span class="bar-value">{{ val }}</span>
                </div>
                <div class="bar-track">
                  <div :class="['bar-fill', key === 'open' ? 'green' : 'red']"
                    :style="{ width: getPercent(val, totalJobs) + '%' }"></div>
                </div>
                <span class="bar-percent">{{ getPercent(val, totalJobs) }}%</span>
              </div>
            </div>
          </div>

          <!-- Top Skills -->
          <div class="card">
            <h2>
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
              Top PWD Skills
            </h2>
            <div v-if="Object.keys(reports.topSkills || {}).length === 0" class="empty-data">
              <p>No skills data yet</p>
            </div>
            <div v-else class="top-list">
              <div v-for="(count, skill) in reports.topSkills" :key="skill" class="top-item">
                <span class="top-rank">{{ getRank(skill, reports.topSkills) }}</span>
                <span class="top-name">{{ skill }}</span>
                <span class="top-count">{{ count }} PWD</span>
                <div class="top-bar">
                  <div class="top-fill"
                    :style="{ width: getPercent(count, Math.max(...Object.values(reports.topSkills))) + '%' }">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'
import { Chart, registerables } from 'chart.js'
Chart.register(...registerables)

export default {
  name: 'GenerateReports',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      reports: {},
      hiringPeriod: 'monthly',
      loading: false,
      charts: {}
    }
  },
  computed: {
    totalPWD() {
      const s = this.reports.pwdByStatus || {}
      return (s.pending || 0) + (s.approved || 0) + (s.rejected || 0)
    },
    totalEmployers() {
      const s = this.reports.employerByStatus || {}
      return (s.pending || 0) + (s.approved || 0) + (s.rejected || 0)
    },
    totalJobs() {
      const s = this.reports.jobsByStatus || {}
      return (s.open || 0) + (s.closed || 0)
    },
    totalApplications() {
      const s = this.reports.applicationsByStatus || {}
      return (s.pending || 0) + (s.accepted || 0) + (s.rejected || 0)
    }
  },
  mounted() {
    this.loadReports()
  },
  beforeUnmount() {
    Object.values(this.charts).forEach(c => c.destroy())
  },
  methods: {
    async loadReports() {
      this.loading = true
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/admin/reports')
        this.reports = response.data.reports || {}
      } catch (err) {
        console.log('Failed to load reports')
      }
      this.loading = false
      this.$nextTick(() => {
        this.renderAllCharts()
      })
    },
    renderAllCharts() {
      this.renderPieChart('pwdPieChart', [
        this.reports.pwdByStatus?.approved || 0,
        this.reports.pwdByStatus?.pending || 0,
        this.reports.pwdByStatus?.rejected || 0,
      ], ['Approved', 'Pending', 'Rejected'])

      this.renderPieChart('employerPieChart', [
        this.reports.employerByStatus?.approved || 0,
        this.reports.employerByStatus?.pending || 0,
        this.reports.employerByStatus?.rejected || 0,
      ], ['Approved', 'Pending', 'Rejected'])

      this.renderPieChart('jobsPieChart', [
        this.reports.jobsByStatus?.open || 0,
        this.reports.jobsByStatus?.closed || 0,
      ], ['Open', 'Closed'], ['#10b981', '#ef4444'])

      this.renderPieChart('statusPieChart', [
        this.reports.applicationsByStatus?.accepted || 0,
        this.reports.applicationsByStatus?.pending || 0,
        this.reports.applicationsByStatus?.rejected || 0,
      ], ['Accepted', 'Pending', 'Rejected'])

      this.renderHiringChart()
    },
    renderPieChart(ref, data, labels, colors = ['#10b981', '#f59e0b', '#ef4444']) {
      if (this.charts[ref]) this.charts[ref].destroy()
      const canvas = this.$refs[ref]
      if (!canvas) return
      this.charts[ref] = new Chart(canvas, {
        type: 'doughnut',
        data: {
          labels,
          datasets: [{
            data,
            backgroundColor: colors,
            borderWidth: 2,
            borderColor: '#fff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: { display: false }
          }
        }
      })
    },
    renderHiringChart() {
      if (this.charts.hiringChart) this.charts.hiringChart.destroy()
      const canvas = this.$refs.hiringChart
      if (!canvas) return

      const data = this.getHiringData()
      this.charts.hiringChart = new Chart(canvas, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [
            {
              label: 'Applications',
              data: data.applications,
              backgroundColor: '#6366f1',
              borderRadius: 6,
            },
            {
              label: 'Accepted',
              data: data.accepted,
              backgroundColor: '#10b981',
              borderRadius: 6,
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: { position: 'top' }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 1 }
            }
          }
        }
      })
    },
    getHiringData() {
      const hiringTrends = this.reports.hiringTrends || {}
      const period = this.hiringPeriod

      if (hiringTrends[period]) {
        return hiringTrends[period]
      }

      // Generate placeholder labels if no data
      if (period === 'weekly') {
        return {
          labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
          applications: [0, 0, 0, 0, 0, 0, 0],
          accepted: [0, 0, 0, 0, 0, 0, 0]
        }
      } else if (period === 'monthly') {
        return {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          applications: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
          accepted: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        }
      } else {
        const currentYear = new Date().getFullYear()
        return {
          labels: [currentYear - 2, currentYear - 1, currentYear].map(String),
          applications: [0, 0, 0],
          accepted: [0, 0, 0]
        }
      }
    },
    changePeriod(period) {
      this.hiringPeriod = period
      this.$nextTick(() => this.renderHiringChart())
    },
    getPercent(value, total) {
      if (!total || !value) return 0
      return Math.round((value / total) * 100)
    },
    getRank(key, obj) {
      return Object.keys(obj).indexOf(key) + 1
    },
    printReport() {
      window.print()
    },

  }
}
</script>

<style scoped>
.dashboard {
  display: flex;
  min-height: 100vh;
  background: var(--bg);
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

.sidebar-nav { display: flex; flex-direction: column; gap: 4px; flex: 1; }

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
  text-align: left;
}

.btn-logout:hover { background: #ef4444; color: white; }

.main-content { margin-left: 240px; flex: 1; padding: 32px; }

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}

.topbar h1 { font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
.topbar h1 svg { color: var(--primary); }
.topbar p { font-size: 14px; color: var(--text-muted); }
.topbar-actions { display: flex; gap: 12px; align-items: center; }

.btn-print {
  padding: 8px 16px;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
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
}

.loading-state {
  background: var(--surface);
  border-radius: 16px;
  padding: 48px;
  text-align: center;
  border: 1px solid var(--border);
  color: var(--text-muted);
}

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

/* Full Width Card */
.full-card {
  background: var(--surface);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid var(--border);
  box-shadow: var(--card-shadow);
  margin-bottom: 24px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-header h2 { font-size: 16px; font-weight: 700; color: var(--text); }

.period-tabs { display: flex; gap: 6px; }

.period-tab {
  padding: 6px 16px;
  border: 2px solid var(--border);
  border-radius: 20px;
  background: var(--bg);
  color: var(--text-muted);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.period-tab.active {
  border-color: var(--primary);
  background: var(--primary);
  color: white;
}

.charts-row {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 24px;
  align-items: start;
}

.chart-wrapper h4 {
  font-size: 13px;
  font-weight: 700;
  color: var(--text-muted);
  margin-bottom: 12px;
  text-align: center;
}

.chart-wrapper.large canvas { max-height: 280px; }
.chart-wrapper.small canvas { max-height: 220px; max-width: 220px; margin: 0 auto; display: block; }

/* Pie Section */
.pie-section {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
}

.pie-section canvas { max-height: 160px; max-width: 160px; flex-shrink: 0; }

.pie-legend { display: flex; flex-direction: column; gap: 8px; }

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--text);
}

.legend-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  flex-shrink: 0;
}

.legend-dot.green { background: #10b981; }
.legend-dot.yellow { background: #f59e0b; }
.legend-dot.red { background: #ef4444; }
.legend-dot.blue { background: #3b82f6; }

/* Content Grid */
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

.card h2 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
.card h2 svg { color: var(--primary); }

/* Bar Chart */
.chart-bars { display: flex; flex-direction: column; gap: 12px; }

.bar-item { display: flex; flex-direction: column; gap: 4px; }

.bar-label {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  color: var(--text);
  font-weight: 600;
}

.bar-value { color: var(--text-muted); }

.bar-track {
  height: 10px;
  background: var(--bg);
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid var(--border);
}

.bar-fill {
  height: 100%;
  border-radius: 10px;
  transition: width 0.5s ease;
  min-width: 4px;
}

.bar-fill.green { background: #10b981; }
.bar-fill.yellow { background: #f59e0b; }
.bar-fill.red { background: #ef4444; }

.bar-percent { font-size: 12px; color: var(--text-muted); text-align: right; }

.capitalize { text-transform: capitalize; }

/* Top List */
.top-list { display: flex; flex-direction: column; gap: 12px; }

.top-item {
  display: grid;
  grid-template-columns: 24px 1fr auto 100px;
  align-items: center;
  gap: 10px;
}

.top-rank {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
}

.top-name { font-size: 13px; font-weight: 600; color: var(--text); }
.top-count { font-size: 12px; color: var(--text-muted); white-space: nowrap; }

.top-bar {
  height: 8px;
  background: var(--bg);
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid var(--border);
}

.top-fill {
  height: 100%;
  background: var(--primary);
  border-radius: 10px;
  transition: width 0.5s ease;
  min-width: 4px;
}

.empty-data {
  padding: 24px;
  text-align: center;
  color: var(--text-muted);
  font-size: 14px;
}

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .content-grid { grid-template-columns: 1fr; }
  .charts-row { grid-template-columns: 1fr; }
}

@media print {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .btn-print, .theme-toggle, .btn-logout { display: none; }
}
</style>