<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <EmployerSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); flex-shrink: 0;"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Edit Job
          </h1>
          <p>Update your job vacancy details</p>
        </div>
        <button class="theme-toggle" @click="$emit('toggleTheme')">
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

      <div v-if="loadingJob" class="loading-state">
        <p style="display: flex; align-items: center; justify-content: center; gap: 8px;">
          <svg class="animate-spin" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
          Loading job details...
        </p>
      </div>

      <div v-else>
        <div class="content-grid">
          <!-- Left: Job Details -->
          <div class="card">
            <h2 style="display: flex; align-items: center; gap: 8px;">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
              Job Details
            </h2>

            <div class="form-group">
              <label>Job Title</label>
              <input v-model="form.job_title" type="text"
                placeholder="e.g. Data Entry Clerk" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Location</label>
              <select v-model="form.location" class="form-input">
                <option value="">-- Select Location --</option>
                <option>Kuala Lumpur</option>
                <option>Selangor</option>
                <option>Johor</option>
                <option>Penang</option>
                <option>Sabah</option>
                <option>Sarawak</option>
                <option>Perak</option>
                <option>Kedah</option>
                <option>Negeri Sembilan</option>
                <option>Melaka</option>
                <option>Remote</option>
              </select>
            </div>

            <div class="form-group">
              <label>Job Description</label>
              <textarea v-model="form.description"
                placeholder="Describe the job responsibilities..."
                class="form-input textarea"></textarea>
            </div>

            <div class="form-group">
              <label>Job Status</label>
              <select v-model="form.job_status" class="form-input">
                <option value="open">Open</option>
                <option value="closed">Closed</option>
              </select>
            </div>

            <div class="form-group">
              <label>Job Duration</label>
              <select v-model="form.duration" class="form-input">
                <option :value="null">No Expiration</option>
                <option :value="0">2 Minutes (Exhibition Testing)</option>
                <option :value="7">7 Days</option>
                <option :value="14">14 Days</option>
              </select>
              <small style="color: var(--text-muted); font-size: 12px; margin-top: 4px; display: block;">
                Job will automatically be marked as closed after the duration ends.
              </small>
            </div>
          </div>

          <!-- Right: 5 Rules -->
          <div class="card">

            <!-- S-Rule: Skills Required -->
            <div class="rule-section">
              <div class="rule-header s-rule">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #1e40af; flex-shrink: 0;"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                <div>
                  <h3>S-Rule: Skills Required</h3>
                  <p>Skills the candidate must have (Weight: 30%)</p>
                </div>
              </div>
              <div class="checkbox-grid">
                <label class="checkbox-item" v-for="skill in skillOptions" :key="skill">
                  <input type="checkbox" :value="skill" v-model="form.requirements"/>
                  {{ skill }}
                </label>
              </div>
            </div>

            <!-- P-Rule: Physical Facilities -->
            <div class="rule-section">
              <div class="rule-header p-rule">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #166534; flex-shrink: 0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <div>
                  <h3>P-Rule: Physical Facilities</h3>
                  <p>Physical accessibility available in office (Weight: 25%)</p>
                </div>
              </div>
              <div class="checkbox-grid">
                <label class="checkbox-item" v-for="item in physicalOptions" :key="item">
                  <input type="checkbox" :value="item" v-model="form.physical_facilities"/>
                  {{ item }}
                </label>
              </div>
            </div>

            <!-- T-Rule: Tech Support -->
            <div class="rule-section">
              <div class="rule-header t-rule">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #7e22ce; flex-shrink: 0;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                <div>
                  <h3>T-Rule: Technology Support</h3>
                  <p>Digital assistive tools provided (Weight: 20%)</p>
                </div>
              </div>
              <div class="checkbox-grid">
                <label class="checkbox-item" v-for="item in techOptions" :key="item">
                  <input type="checkbox" :value="item" v-model="form.tech_support"/>
                  {{ item }}
                </label>
              </div>
            </div>

            <!-- W-Rule: Work Arrangement -->
            <div class="rule-section">
              <div class="rule-header w-rule">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #c2410c; flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <div>
                  <h3>W-Rule: Work Arrangement</h3>
                  <p>Work flexibility offered (Weight: 15%)</p>
                </div>
              </div>
              <div class="checkbox-grid">
                <label class="checkbox-item" v-for="item in workOptions" :key="item">
                  <input type="checkbox" :value="item" v-model="form.work_arrangement"/>
                  {{ item }}
                </label>
              </div>
            </div>

            <!-- C-Rule: Sensory Support -->
            <div class="rule-section">
              <div class="rule-header c-rule">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #854d0e; flex-shrink: 0;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <div>
                  <h3>C-Rule: Communication Support</h3>
                  <p>Sensory and communication support provided (Weight: 10%)</p>
                </div>
              </div>
              <div class="checkbox-grid">
                <label class="checkbox-item" v-for="item in sensoryOptions" :key="item">
                  <input type="checkbox" :value="item" v-model="form.sensory_support"/>
                  {{ item }}
                </label>
              </div>
            </div>

          </div>
        </div>

        <p v-if="success" class="success-msg">{{ success }}</p>
        <p v-if="error" class="error-msg">{{ error }}</p>

        <div class="form-actions">
          <button @click="confirmDelete" class="btn-delete" style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
            Delete Job
          </button>
          <div class="right-actions">
            <router-link to="/employer/dashboard" class="btn-cancel">Cancel</router-link>
            <button @click="updateJob" class="btn-save" :disabled="loading" style="display: flex; align-items: center; gap: 8px;">
              <svg v-if="!loading" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              {{ loading ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'EditJob',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      jobId: null,
      loadingJob: true,
      form: {
        job_title: '',
        location: '',
        description: '',
        job_status: 'open',
        duration: null,
        requirements: [],
        physical_facilities: [],
        tech_support: [],
        work_arrangement: [],
        sensory_support: [],
      },
      skillOptions: [
        'Microsoft Office', 'Data Entry', 'Customer Service',
        'Graphic Design', 'Web Development', 'Programming',
        'Accounting', 'Marketing', 'Social Media',
        'Video Editing', 'Content Writing', 'Translation',
        'Teaching', 'Administrative', 'Logistics',
      ],
      physicalOptions: [
        'Wheelchair Ramp', 'Elevator Access',
        'Accessible Restroom', 'Parking for Disabled',
        'Accessible Entrance', 'Lowered Reception Desk',
      ],
      techOptions: [
        'Screen Reader Software', 'Braille Display',
        'Voice Recognition Software', 'Adjustable Workstation',
        'Large Monitor', 'Ergonomic Equipment',
      ],
      workOptions: [
        'Flexible Working Hours', 'Remote Work',
        'Part Time Work', 'Reduced Working Hours',
        'Work From Home', 'Hybrid Work',
      ],
      sensoryOptions: [
        'Sign Language Interpreter', 'Hearing Loop System',
        'Quiet Work Environment', 'Written Communication',
        'Visual Alerts', 'Braille Signage',
      ],
      success: '',
      error: '',
      loading: false
    }
  },
  mounted() {
    const token = localStorage.getItem('token')
    if (!token) { this.$router.push('/login'); return }
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    this.jobId = this.$route.params.id
    this.loadJob()
  },
  methods: {
    async loadJob() {
      try {
        const response = await axios.get('/api/employer/jobs/' + this.jobId)
        const job = response.data.job
        this.form.job_title = job.job_title
        this.form.location = job.location
        this.form.description = job.description
        this.form.job_status = job.job_status
        this.form.duration = job.duration
        this.form.requirements = job.requirements || []
        this.form.physical_facilities = job.physical_facilities || []
        this.form.tech_support = job.tech_support || []
        this.form.work_arrangement = job.work_arrangement || []
        this.form.sensory_support = job.sensory_support || []
      } catch (err) {
        this.error = 'Failed to load job details.'
      }
      this.loadingJob = false
    },
    async updateJob() {
      this.loading = true
      this.success = ''
      this.error = ''
      try {
        await axios.put('/api/employer/jobs/' + this.jobId, {
          job_title: this.form.job_title,
          location: this.form.location,
          description: this.form.description,
          job_status: this.form.job_status,
          duration: this.form.duration,
          requirements: this.form.requirements,
          physical_facilities: this.form.physical_facilities,
          tech_support: this.form.tech_support,
          work_arrangement: this.form.work_arrangement,
          sensory_support: this.form.sensory_support,
          office_accessibility: [
            ...this.form.physical_facilities,
            ...this.form.tech_support,
            ...this.form.work_arrangement,
            ...this.form.sensory_support
          ],
        })
        this.success = 'Job updated successfully!'
      } catch (err) {
        this.error = 'Failed to update job. Please try again.'
      }
      this.loading = false
    },
    async confirmDelete() {
      if (confirm('Are you sure you want to delete this job?')) {
        try {
          await axios.delete('/api/employer/jobs/' + this.jobId)
          this.$router.push('/employer/dashboard')
        } catch (err) {
          this.error = 'Failed to delete job.'
        }
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
  transition: all 0.2s;
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

.loading-state {
  background: var(--surface);
  border-radius: 16px;
  padding: 48px;
  text-align: center;
  border: 1px solid var(--border);
  color: var(--text-muted);
}

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
  height: fit-content;
}

.card h2 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 16px; }

.form-group { margin-bottom: 16px; }

.form-group label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
  margin-bottom: 6px;
}

.form-input {
  width: 100%;
  padding: 10px 14px;
  border: 2px solid var(--border);
  border-radius: 8px;
  font-size: 14px;
  color: var(--text);
  background: var(--input-bg);
  outline: none;
  transition: border-color 0.2s;
  font-family: 'Segoe UI', sans-serif;
}

.form-input:focus { border-color: var(--primary); }
.textarea { resize: vertical; min-height: 120px; }

/* Rule Sections */
.rule-section { margin-bottom: 24px; }

.rule-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 10px;
  margin-bottom: 12px;
}

.rule-header span { font-size: 24px; flex-shrink: 0; }
.rule-header h3 { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 2px; }
.rule-header p { font-size: 12px; color: var(--text-muted); }

.s-rule { background: #eff6ff; border: 1px solid #bfdbfe; }
.p-rule { background: #f0fdf4; border: 1px solid #bbf7d0; }
.t-rule { background: #faf5ff; border: 1px solid #e9d5ff; }
.w-rule { background: #fff7ed; border: 1px solid #fed7aa; }
.c-rule { background: #fef9c3; border: 1px solid #fde047; }

.checkbox-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.checkbox-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--text);
  cursor: pointer;
  padding: 6px 10px;
  border-radius: 6px;
  background: var(--bg);
  border: 1px solid var(--border);
  transition: all 0.2s;
}

.checkbox-item:hover { border-color: var(--primary); color: var(--primary); }
.checkbox-item input { accent-color: var(--primary); }

.success-msg {
  color: #10b981;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 10px 16px;
  font-size: 13px;
  margin-bottom: 16px;
}

.error-msg {
  color: #ef4444;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 10px 16px;
  font-size: 13px;
  margin-bottom: 16px;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.right-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.btn-delete {
  padding: 12px 24px;
  background: #fef2f2;
  color: #ef4444;
  border: 1px solid #fecaca;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-delete:hover { background: #ef4444; color: white; }

.btn-cancel {
  padding: 12px 24px;
  border: 2px solid var(--border);
  border-radius: 10px;
  color: var(--text-muted);
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-cancel:hover { border-color: var(--primary); color: var(--primary); }

.btn-save {
  padding: 12px 40px;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-save:hover { background: var(--primary-hover); }
.btn-save:disabled { opacity: 0.6; cursor: not-allowed; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .content-grid { grid-template-columns: 1fr; }
  .form-actions { flex-direction: column; gap: 12px; }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
.animate-spin {
  animation: spin 1s linear infinite;
}
</style>