<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><circle cx="12" cy="6" r="2"/><path d="M6 12h12M12 8v6M9 20l3-6 3 6"/></svg>
            Manage PWD
          </h1>
          <p>View and manage all PWD accounts</p>
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
        <div class="stat-pill blue">Total: {{ pwds.length }}</div>
        <div class="stat-pill yellow">Pending: {{ pwds.filter(p => p.status === 'pending').length }}</div>
        <div class="stat-pill green">Approved: {{ pwds.filter(p => p.status === 'approved').length }}</div>
        <div class="stat-pill red">Rejected: {{ pwds.filter(p => p.status === 'rejected').length }}</div>
      </div>

      <!-- Search & Filter -->
      <div class="search-card">
        <div class="search-row">
          <div class="search-input-wrapper">
            <span class="search-icon">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input v-model="search" type="text"
              placeholder="Search by name, email or OKU number..."
              class="search-input" @input="filterPWDs"/>
          </div>
          <select v-model="filterStatus" @change="filterPWDs" class="filter-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
      </div>

      <!-- Results -->
      <div class="results-info">
        <p>Showing <strong>{{ filteredPWDs.length }}</strong> PWD accounts</p>
      </div>

      <div v-if="loading" class="loading-state">
        <p style="display: flex; align-items: center; justify-content: center; gap: 8px;">
          <svg class="animate-spin" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
          Loading...
        </p>
      </div>
      <div v-else-if="filteredPWDs.length === 0" class="empty-state">
        <p style="display: flex; align-items: center; justify-content: center; gap: 8px; color: var(--text-muted);">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M3 8l9 6 9-6M12 14v6M8 20h8"/></svg>
          No PWD accounts found!
        </p>
      </div>

      <div v-else class="table-card">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>OKU Number</th>
              <th>Skills (S-Rule)</th>
              <th>Status</th>
              <th>Registered</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="pwd in filteredPWDs" :key="pwd.id">
              <td>{{ pwd.id }}</td>
              <td>
                <div class="table-name">
                  <div class="table-avatar">{{ getInitials(pwd.full_name) }}</div>
                  {{ pwd.full_name }}
                </div>
              </td>
              <td>{{ pwd.user?.email }}</td>
              <td>{{ pwd.oku_number }}</td>
              <td>
                <div class="tags">
                  <span v-for="skill in (pwd.skills || []).slice(0, 2)"
                    :key="skill" class="tag-skill">{{ skill }}</span>
                  <span v-if="(pwd.skills || []).length > 2" class="tag-more">
                    +{{ pwd.skills.length - 2 }}
                  </span>
                  <span v-if="!(pwd.skills || []).length" class="no-data">None</span>
                </div>
              </td>
              <td>
                <select :value="pwd.status"
                  @change="updateStatus(pwd, $event.target.value)"
                  :class="['status-select', pwd.status]">
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                </select>
              </td>
              <td>{{ pwd.created_at?.slice(0, 10) }}</td>
              <td>
                <div class="action-btns">
                  <button @click="viewPWD(pwd)" class="btn-view" title="View">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>
                  <button @click="confirmDelete(pwd)" class="btn-delete" title="Delete">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <!-- PWD Detail Modal -->
    <div v-if="selectedPWD" class="modal-overlay" @click.self="selectedPWD = null">
      <div class="modal">
        <div class="modal-header">
          <div class="modal-profile">
            <div class="modal-avatar">{{ getInitials(selectedPWD.full_name) }}</div>
            <div>
              <h2>{{ selectedPWD.full_name }}</h2>
              <p>{{ selectedPWD.user?.email }}</p>
              <p>OKU: {{ selectedPWD.oku_number }}</p>
              <div :class="['pwd-status', selectedPWD.status]">{{ selectedPWD.status }}</div>
            </div>
          </div>
          <button @click="selectedPWD = null" class="btn-close">✕</button>
        </div>

        <div class="modal-body">

          <!-- S-Rule: Skills -->
          <div class="modal-section">
            <div class="rule-label s-rule-label" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
              S-Rule: Skills (Weight 30%)
            </div>
            <div class="tags">
              <span v-for="skill in (selectedPWD.skills || [])"
                :key="skill" class="tag-skill">{{ skill }}</span>
              <span v-if="!selectedPWD.skills?.length" class="no-data">None listed</span>
            </div>
          </div>

          <!-- P-Rule: Physical Needs -->
          <div class="modal-section">
            <div class="rule-label p-rule-label" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="6" r="2"/><path d="M6 12h12M12 8v6M9 20l3-6 3 6"/></svg>
              P-Rule: Physical Needs (Weight 25%)
            </div>
            <div class="tags">
              <span v-for="item in (selectedPWD.physical_needs || [])"
                :key="item" class="tag-physical">{{ item }}</span>
              <span v-if="!selectedPWD.physical_needs?.length" class="no-data">None listed</span>
            </div>
          </div>

          <!-- T-Rule: Tech Needs -->
          <div class="modal-section">
            <div class="rule-label t-rule-label" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              T-Rule: Tech Needs (Weight 20%)
            </div>
            <div class="tags">
              <span v-for="item in (selectedPWD.tech_needs || [])"
                :key="item" class="tag-tech">{{ item }}</span>
              <span v-if="!selectedPWD.tech_needs?.length" class="no-data">None listed</span>
            </div>
          </div>

          <!-- W-Rule: Work Arrangement -->
          <div class="modal-section">
            <div class="rule-label w-rule-label" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              W-Rule: Work Arrangement (Weight 15%)
            </div>
            <div class="tags">
              <span v-for="item in (selectedPWD.work_arrangement || [])"
                :key="item" class="tag-work">{{ item }}</span>
              <span v-if="!selectedPWD.work_arrangement?.length" class="no-data">None listed</span>
            </div>
          </div>

          <!-- C-Rule: Sensory Needs -->
          <div class="modal-section">
            <div class="rule-label c-rule-label" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              C-Rule: Sensory Needs (Weight 10%)
            </div>
            <div class="tags">
              <span v-for="item in (selectedPWD.sensory_needs || [])"
                :key="item" class="tag-sensory">{{ item }}</span>
              <span v-if="!selectedPWD.sensory_needs?.length" class="no-data">None listed</span>
            </div>
          </div>

          <!-- Attachments -->
          <div class="modal-section"
            v-if="selectedPWD.certificate_path || selectedPWD.video_path">
            <div class="rule-label" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
              Attachments
            </div>
            <div class="attachments">
              <a v-if="selectedPWD.certificate_path"
                :href="'/storage/' + selectedPWD.certificate_path"
                target="_blank" class="attachment-link" style="display: inline-flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Certificate
              </a>
              <a v-if="selectedPWD.video_path"
                :href="'/storage/' + selectedPWD.video_path"
                target="_blank" class="attachment-link" style="display: inline-flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                Video
              </a>
            </div>
          </div>

          <!-- Status Actions -->
          <div class="modal-section">
            <div class="rule-label" style="display: flex; align-items: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              Account Status
            </div>
            <div class="status-actions">
              <button @click="updateStatus(selectedPWD, 'approved')"
                class="btn-approve" :disabled="selectedPWD.status === 'approved'">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Approve
              </button>
              <button @click="updateStatus(selectedPWD, 'rejected')"
                class="btn-reject-full" :disabled="selectedPWD.status === 'rejected'">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Reject
              </button>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="selectedPWD = null" class="btn-cancel">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ManagePWD',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      pwds: [],
      filteredPWDs: [],
      search: '',
      filterStatus: '',
      selectedPWD: null,
      loading: false
    }
  },
  mounted() {
    this.loadPWDs()
  },
  methods: {
    async loadPWDs() {
      this.loading = true
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/admin/pwds')
        this.pwds = response.data.pwds || []
        this.filteredPWDs = this.pwds
      } catch (err) {
        console.log('Failed to load PWDs')
      }
      this.loading = false
    },
    filterPWDs() {
      this.filteredPWDs = this.pwds.filter(p => {
        const matchSearch = !this.search ||
          p.full_name?.toLowerCase().includes(this.search.toLowerCase()) ||
          p.user?.email?.toLowerCase().includes(this.search.toLowerCase()) ||
          p.oku_number?.toLowerCase().includes(this.search.toLowerCase())
        const matchStatus = !this.filterStatus || p.status === this.filterStatus
        return matchSearch && matchStatus
      })
    },
    async updateStatus(pwd, status) {
      try {
        await axios.put('/api/admin/pwds/' + pwd.id + '/status', { status })
        pwd.status = status
        if (this.selectedPWD && this.selectedPWD.id === pwd.id) {
          this.selectedPWD.status = status
        }
      } catch (err) { alert('Failed to update status') }
    },
    async confirmDelete(pwd) {
      if (confirm('Are you sure you want to delete ' + pwd.full_name + '?')) {
        try {
          await axios.delete('/api/admin/pwds/' + pwd.id)
          this.pwds = this.pwds.filter(p => p.id !== pwd.id)
          this.filteredPWDs = this.filteredPWDs.filter(p => p.id !== pwd.id)
        } catch (err) { alert('Failed to delete') }
      }
    },
    viewPWD(pwd) {
      this.selectedPWD = pwd
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

.topbar { display: flex; justify-between: space-between; align-items: flex-start; margin-bottom: 24px; }
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
.search-row { display: flex; gap: 12px; }

.search-input-wrapper { flex: 2; position: relative; }
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

.table-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }

.tags { display: flex; flex-wrap: wrap; gap: 4px; }

/* Rule Tags */
.tag-skill { padding: 2px 8px; background: #eff6ff; color: #1d4ed8; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-physical { padding: 2px 8px; background: #f0fdf4; color: #166534; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-tech { padding: 2px 8px; background: #faf5ff; color: #7e22ce; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-work { padding: 2px 8px; background: #fff7ed; color: #c2410c; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-sensory { padding: 2px 8px; background: #fef9c3; color: #854d0e; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-more { padding: 2px 8px; background: var(--bg); color: var(--text-muted); border-radius: 20px; font-size: 11px; border: 1px solid var(--border); }

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

.modal-avatar { width: 56px; height: 56px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; }

.modal-header h2 { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.modal-header p { font-size: 13px; color: var(--text-muted); margin-bottom: 2px; }

.pwd-status { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize; margin-top: 4px; }
.pwd-status.pending { background: #fef9c3; color: #854d0e; }
.pwd-status.approved { background: #f0fdf4; color: #166534; }
.pwd-status.rejected { background: #fef2f2; color: #991b1b; }

.btn-close { padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); cursor: pointer; }

.modal-body { padding: 24px; display: flex; flex-direction: column; gap: 16px; }

.modal-section { display: flex; flex-direction: column; gap: 8px; padding: 12px 16px; border-radius: 10px; background: var(--bg); border: 1px solid var(--border); }

/* Rule Labels */
.rule-label { font-size: 13px; font-weight: 700; margin-bottom: 4px; }
.s-rule-label { color: #1d4ed8; }
.p-rule-label { color: #166534; }
.t-rule-label { color: #7e22ce; }
.w-rule-label { color: #c2410c; }
.c-rule-label { color: #854d0e; }

.no-data { font-size: 13px; color: var(--text-muted); font-style: italic; }

.attachments { display: flex; gap: 8px; flex-wrap: wrap; }
.attachment-link { padding: 8px 16px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 600; }
.attachment-link:hover { border-color: var(--primary); }

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