<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="9" y1="16" x2="15" y2="16"/><line x1="15" y1="16" x2="15" y2="22"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/></svg>
            Manage Employers
          </h1>
          <p>View and manage all employer accounts</p>
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

      <!-- Stats Row -->
      <div class="stats-row">
        <div class="stat-pill blue">Total: {{ employers.length }}</div>
        <div class="stat-pill yellow">Pending: {{ employers.filter(e => e.status === 'pending').length }}</div>
        <div class="stat-pill green">Approved: {{ employers.filter(e => e.status === 'approved').length }}</div>
        <div class="stat-pill red">Rejected: {{ employers.filter(e => e.status === 'rejected').length }}</div>
      </div>

      <!-- Search & Filter -->
      <div class="search-card">
        <div class="search-row">
          <div class="search-input-wrapper">
            <span class="search-icon">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input v-model="search" type="text"
              placeholder="Search by company name, email or SSM..."
              class="search-input" @input="filterEmployers"/>
          </div>
          <select v-model="filterStatus" @change="filterEmployers" class="filter-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
          <select v-model="filterIndustry" @change="filterEmployers" class="filter-select">
            <option value="">All Industries</option>
            <option>Technology</option>
            <option>Healthcare</option>
            <option>Education</option>
            <option>Manufacturing</option>
            <option>Retail</option>
            <option>Finance</option>
            <option>Construction</option>
            <option>Food & Beverage</option>
            <option>Logistics</option>
            <option>Others</option>
          </select>
        </div>
      </div>

      <!-- Results -->
      <div class="results-info">
        <p>Showing <strong>{{ filteredEmployers.length }}</strong> employer accounts</p>
      </div>

      <div v-if="loading" class="loading-state">
        <p style="display: flex; align-items: center; justify-content: center; gap: 8px;">
          <svg class="animate-spin" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
          Loading...
        </p>
      </div>
      <div v-else-if="filteredEmployers.length === 0" class="empty-state">
        <p style="display: flex; align-items: center; justify-content: center; gap: 8px; color: var(--text-muted);">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M3 8l9 6 9-6M12 14v6M8 20h8"/></svg>
          No employer accounts found!
        </p>
      </div>

      <div v-else class="table-card">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Company</th>
              <th>Email</th>
              <th>Industry</th>
              <th>SSM Number</th>
              <th>P-Rule Facilities</th>
              <th>Status</th>
              <th>Registered</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="emp in filteredEmployers" :key="emp.id">
              <td>{{ emp.id }}</td>
              <td>
                <div class="table-name">
                  <div class="table-avatar">
                    <img v-if="emp.logo_path" :src="'/storage/' + emp.logo_path" />
                    <span v-else>{{ getInitials(emp.company_name) }}</span>
                  </div>
                  <div>
                    <div>{{ emp.company_name }}</div>
                    <div class="table-sub">{{ emp.contact_number }}</div>
                  </div>
                </div>
              </td>
              <td>{{ emp.company_email }}</td>
              <td>{{ emp.industry_type }}</td>
              <td>{{ emp.ssm_number }}</td>
              <td>
                <div class="tags">
                  <span v-for="acc in (emp.accessibility || []).slice(0, 2)"
                    :key="acc" class="tag-physical">{{ acc }}</span>
                  <span v-if="(emp.accessibility || []).length > 2" class="tag-more">
                    +{{ emp.accessibility.length - 2 }}
                  </span>
                  <span v-if="!(emp.accessibility || []).length" class="no-data">None</span>
                </div>
              </td>
              <td>
                <select :value="emp.status"
                  @change="updateStatus(emp, $event.target.value)"
                  :class="['status-select', emp.status]">
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                </select>
              </td>
              <td>{{ emp.created_at?.slice(0, 10) }}</td>
              <td>
                <div class="action-btns">
                  <button @click="viewEmployer(emp)" class="btn-view" title="View">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>
                  <button @click="confirmDelete(emp)" class="btn-delete" title="Delete">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <!-- Employer Detail Modal -->
    <div v-if="selectedEmployer" class="modal-overlay" @click.self="selectedEmployer = null">
      <div class="modal">
        <div class="modal-header">
          <div class="modal-profile">
            <img v-if="selectedEmployer.logo_path" :src="'/storage/' + selectedEmployer.logo_path" class="modal-logo-img" />
            <div v-else class="modal-avatar">{{ getInitials(selectedEmployer.company_name) }}</div>
            <div>
              <h2>{{ selectedEmployer.company_name }}</h2>
              <p>{{ selectedEmployer.company_email }}</p>
              <p>SSM: {{ selectedEmployer.ssm_number }}</p>
              <div :class="['emp-status', selectedEmployer.status]">
                {{ selectedEmployer.status }}
              </div>
            </div>
          </div>
          <button @click="selectedEmployer = null" class="btn-close">✕</button>
        </div>

        <div class="modal-body">

          <!-- About Us -->
          <div class="modal-section" v-if="selectedEmployer.about_us">
            <div class="section-title" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              About Us
            </div>
            <div style="font-size: 13px; color: var(--text); line-height: 1.5; white-space: pre-line;">
              {{ selectedEmployer.about_us }}
            </div>
          </div>

          <!-- Company Details -->
          <div class="modal-section">
            <div class="section-title" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
              Company Details
            </div>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Industry</span>
                <span>{{ selectedEmployer.industry_type }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Contact</span>
                <span>{{ selectedEmployer.contact_number }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Address</span>
                <span>{{ selectedEmployer.company_address }}</span>
              </div>
            </div>
          </div>

          <!-- P-Rule: Physical Facilities -->
          <div class="modal-section">
            <div class="rule-label p-rule-label" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="6" r="2"/><path d="M6 12h12M12 8v6M9 20l3-6 3 6"/></svg>
              P-Rule: Physical Facilities (Weight 25%)
            </div>
            <div class="tags">
              <span v-for="item in getPhysicalFacilities(selectedEmployer)"
                :key="item" class="tag-physical">{{ item }}</span>
              <span v-if="!getPhysicalFacilities(selectedEmployer).length"
                class="no-data">None listed</span>
            </div>
          </div>

          <!-- T-Rule: Tech Support -->
          <div class="modal-section">
            <div class="rule-label t-rule-label" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              T-Rule: Technology Support (Weight 20%)
            </div>
            <div class="tags">
              <span v-for="item in getTechSupport(selectedEmployer)"
                :key="item" class="tag-tech">{{ item }}</span>
              <span v-if="!getTechSupport(selectedEmployer).length"
                class="no-data">None listed</span>
            </div>
          </div>

          <!-- W-Rule: Work Arrangement -->
          <div class="modal-section">
            <div class="rule-label w-rule-label" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              W-Rule: Work Arrangement (Weight 15%)
            </div>
            <div class="tags">
              <span v-for="item in getWorkArrangement(selectedEmployer)"
                :key="item" class="tag-work">{{ item }}</span>
              <span v-if="!getWorkArrangement(selectedEmployer).length"
                class="no-data">None listed</span>
            </div>
          </div>

          <!-- C-Rule: Sensory Support -->
          <div class="modal-section">
            <div class="rule-label c-rule-label" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              C-Rule: Communication Support (Weight 10%)
            </div>
            <div class="tags">
              <span v-for="item in getSensorySupport(selectedEmployer)"
                :key="item" class="tag-sensory">{{ item }}</span>
              <span v-if="!getSensorySupport(selectedEmployer).length"
                class="no-data">None listed</span>
            </div>
          </div>

          <!-- Status Actions -->
          <div class="modal-section">
            <div class="section-title" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              Account Status
            </div>
            <div class="status-actions">
              <button @click="updateStatus(selectedEmployer, 'approved')"
                class="btn-approve" :disabled="selectedEmployer.status === 'approved'">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Approve
              </button>
              <button @click="updateStatus(selectedEmployer, 'rejected')"
                class="btn-reject-full" :disabled="selectedEmployer.status === 'rejected'">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Reject
              </button>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="selectedEmployer = null" class="btn-cancel">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ManageEmployer',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      employers: [],
      filteredEmployers: [],
      search: '',
      filterStatus: '',
      filterIndustry: '',
      selectedEmployer: null,
      loading: false,
      // P-Rule options
      physicalOptions: [
        'Wheelchair Ramp', 'Elevator Access',
        'Accessible Restroom', 'Parking for Disabled',
        'Accessible Entrance', 'Lowered Reception Desk',
      ],
      // T-Rule options
      techOptions: [
        'Screen Reader Software', 'Braille Display',
        'Voice Recognition Software', 'Adjustable Workstation',
        'Large Monitor', 'Ergonomic Equipment',
      ],
      // W-Rule options
      workOptions: [
        'Flexible Working Hours', 'Remote Work',
        'Part Time Work', 'Reduced Working Hours',
        'Work From Home', 'Hybrid Work',
      ],
      // C-Rule options
      sensoryOptions: [
        'Sign Language Interpreter', 'Hearing Loop System',
        'Quiet Work Environment', 'Written Communication',
        'Visual Alerts', 'Braille Signage',
      ],
    }
  },
  mounted() {
    this.loadEmployers()
  },
  methods: {
    async loadEmployers() {
      this.loading = true
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/admin/employers')
        this.employers = response.data.employers || []
        this.filteredEmployers = this.employers
      } catch (err) {
        console.log('Failed to load employers')
      }
      this.loading = false
    },
    filterEmployers() {
      this.filteredEmployers = this.employers.filter(e => {
        const matchSearch = !this.search ||
          e.company_name?.toLowerCase().includes(this.search.toLowerCase()) ||
          e.company_email?.toLowerCase().includes(this.search.toLowerCase()) ||
          e.ssm_number?.toLowerCase().includes(this.search.toLowerCase())
        const matchStatus = !this.filterStatus || e.status === this.filterStatus
        const matchIndustry = !this.filterIndustry || e.industry_type === this.filterIndustry
        return matchSearch && matchStatus && matchIndustry
      })
    },
    // Extract P-Rule items from accessibility array
    getPhysicalFacilities(emp) {
      const all = emp.accessibility || []
      return all.filter(item => this.physicalOptions.includes(item))
    },
    // Extract T-Rule items
    getTechSupport(emp) {
      const all = emp.accessibility || []
      return all.filter(item => this.techOptions.includes(item))
    },
    // Extract W-Rule items
    getWorkArrangement(emp) {
      const all = emp.accessibility || []
      return all.filter(item => this.workOptions.includes(item))
    },
    // Extract C-Rule items
    getSensorySupport(emp) {
      const all = emp.accessibility || []
      return all.filter(item => this.sensoryOptions.includes(item))
    },
    async updateStatus(emp, status) {
      try {
        await axios.put('/api/admin/employers/' + emp.id + '/status', { status })
        emp.status = status
        if (this.selectedEmployer && this.selectedEmployer.id === emp.id) {
          this.selectedEmployer.status = status
        }
      } catch (err) { alert('Failed to update status') }
    },
    async confirmDelete(emp) {
      if (confirm('Are you sure you want to delete ' + emp.company_name + '?')) {
        try {
          await axios.delete('/api/admin/employers/' + emp.id)
          this.employers = this.employers.filter(e => e.id !== emp.id)
          this.filteredEmployers = this.filteredEmployers.filter(e => e.id !== emp.id)
        } catch (err) { alert('Failed to delete') }
      }
    },
    viewEmployer(emp) {
      this.selectedEmployer = emp
    },
    getInitials(name) {
      if (!name) return '?'
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }
  }
}
</script>

<style scoped>
.dashboard { display: flex; min-height: 100vh; background: var(--bg); }



.main-content { margin-left: 240px; flex: 1; padding: 32px; }
.topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.topbar h1 { font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
.topbar h1 svg { color: var(--primary); }
.topbar p { font-size: 14px; color: var(--text-muted); }
.theme-toggle { padding: 8px 16px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.theme-toggle:hover { border-color: var(--primary); color: var(--primary); }

.stats-row { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.stat-pill { padding: 8px 20px; border-radius: 20px; font-size: 13px; font-weight: 600; }
.stat-pill.blue { background: #eff6ff; color: #1d4ed8; }
.stat-pill.yellow { background: #fef9c3; color: #854d0e; }
.stat-pill.green { background: #f0fdf4; color: #166534; }
.stat-pill.red { background: #fef2f2; color: #991b1b; }

.search-card { background: var(--surface); border-radius: 16px; padding: 20px; margin-bottom: 16px; border: 1px solid var(--border); }
.search-row { display: flex; gap: 12px; flex-wrap: wrap; }
.search-input-wrapper { flex: 2; position: relative; min-width: 200px; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); }
.search-input { width: 100%; padding: 10px 14px 10px 40px; border: 2px solid var(--border); border-radius: 8px; font-size: 14px; color: var(--text); background: var(--input-bg); outline: none; }
.search-input:focus { border-color: var(--primary); }
.filter-select { padding: 10px 14px; border: 2px solid var(--border); border-radius: 8px; font-size: 14px; color: var(--text); background: var(--input-bg); outline: none; min-width: 140px; }

.results-info { margin-bottom: 16px; font-size: 14px; color: var(--text-muted); }
.loading-state, .empty-state { background: var(--surface); border-radius: 16px; padding: 48px; text-align: center; border: 1px solid var(--border); color: var(--text-muted); }

.table-card { background: var(--surface); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: var(--card-shadow); }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 700; color: var(--text-muted); background: var(--bg); border-bottom: 1px solid var(--border); text-transform: uppercase; }
.data-table td { padding: 14px 16px; font-size: 13px; color: var(--text); border-bottom: 1px solid var(--border); vertical-align: middle; }
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: var(--bg); }

.table-name { display: flex; align-items: center; gap: 8px; font-weight: 600; }
.table-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; overflow: hidden; -webkit-backface-visibility: hidden; -moz-backface-visibility: hidden; -webkit-transform: translate3d(0, 0, 0); -moz-transform: translate3d(0, 0, 0); }
.table-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.table-sub { font-size: 11px; color: var(--text-muted); font-weight: 400; }

.tags { display: flex; flex-wrap: wrap; gap: 4px; }

/* Rule Tags */
.tag-physical { padding: 2px 8px; background: #f0fdf4; color: #166534; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-tech { padding: 2px 8px; background: #faf5ff; color: #7e22ce; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-work { padding: 2px 8px; background: #fff7ed; color: #c2410c; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-sensory { padding: 2px 8px; background: #fef9c3; color: #854d0e; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-more { padding: 2px 8px; background: var(--bg); color: var(--text-muted); border-radius: 20px; font-size: 11px; border: 1px solid var(--border); }
.no-data { font-size: 12px; color: var(--text-muted); font-style: italic; }

.status-select { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; outline: none; }
.status-select.pending { background: #fef9c3; color: #854d0e; }
.status-select.approved { background: #f0fdf4; color: #166534; }
.status-select.rejected { background: #fef2f2; color: #991b1b; }

.action-btns { display: flex; gap: 6px; }
.btn-view { padding: 6px 10px; border: 1px solid var(--border); border-radius: 6px; background: var(--surface); cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; color: var(--text); }
.btn-view:hover { border-color: var(--primary); color: var(--primary); }
.btn-delete { padding: 6px 10px; border: 1px solid #fecaca; border-radius: 6px; background: #fef2f2; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; color: #ef4444; }
.btn-delete:hover { background: #ef4444; color: white; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
.modal { background: var(--surface); border-radius: 20px; width: 100%; max-width: 600px; max-height: 85vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }

.modal-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 24px; border-bottom: 1px solid var(--border); }
.modal-profile { display: flex; align-items: center; gap: 16px; }
.modal-logo-img { width: 56px; height: 56px; border-radius: 12px; object-fit: cover; border: 1px solid var(--border); }
.modal-avatar { width: 56px; height: 56px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; overflow: hidden; -webkit-backface-visibility: hidden; -moz-backface-visibility: hidden; -webkit-transform: translate3d(0, 0, 0); -moz-transform: translate3d(0, 0, 0); }
.modal-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.modal-header h2 { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.modal-header p { font-size: 13px; color: var(--text-muted); margin-bottom: 2px; }

.emp-status { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize; margin-top: 4px; }
.emp-status.pending { background: #fef9c3; color: #854d0e; }
.emp-status.approved { background: #f0fdf4; color: #166534; }
.emp-status.rejected { background: #fef2f2; color: #991b1b; }

.btn-close { padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); cursor: pointer; }

.modal-body { padding: 24px; display: flex; flex-direction: column; gap: 16px; }

.modal-section { display: flex; flex-direction: column; gap: 8px; padding: 12px 16px; border-radius: 10px; background: var(--bg); border: 1px solid var(--border); }

.section-title { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 4px; }

/* Rule Labels */
.rule-label { font-size: 13px; font-weight: 700; margin-bottom: 4px; }
.p-rule-label { color: #166534; }
.t-rule-label { color: #7e22ce; }
.w-rule-label { color: #c2410c; }
.c-rule-label { color: #854d0e; }

.detail-grid { display: flex; flex-direction: column; gap: 8px; }
.detail-item { display: flex; gap: 12px; font-size: 13px; color: var(--text); }
.detail-label { font-weight: 700; color: var(--text-muted); min-width: 80px; }

.status-actions { display: flex; gap: 12px; }
.btn-approve { padding: 10px 24px; background: #10b981; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
.btn-approve:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-reject-full { padding: 10px 24px; background: #ef4444; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
.btn-reject-full:disabled { opacity: 0.5; cursor: not-allowed; }

.modal-footer { display: flex; justify-content: flex-end; padding: 16px 24px; border-top: 1px solid var(--border); }
.btn-cancel { padding: 10px 24px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 14px; font-weight: 600; cursor: pointer; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .table-card { overflow-x: auto; }
}
</style>