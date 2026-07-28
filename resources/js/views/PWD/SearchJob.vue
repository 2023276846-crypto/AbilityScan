<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <PWDSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1>Search Jobs</h1>
          <p>Find jobs that match your skills and accessibility needs</p>
        </div>
        <button class="theme-toggle" @click="$emit('toggleTheme')">
          {{ isDark ? '☀️ Light' : '🌙 Dark' }}
        </button>
      </div>

      <!-- Search & Filter -->
      <div class="search-card">
        <div class="search-row">
          <div class="search-input-wrapper">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="search-icon-svg" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input v-model="search" type="text"
              placeholder="Search job title, company..."
              class="search-input" @input="filterJobs"/>
          </div>
          <select v-model="filterIndustry" @change="filterJobs" class="filter-select">
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
          <select v-model="filterLocation" @change="filterJobs" class="filter-select">
            <option value="">All Locations</option>
            <option>Kuala Lumpur</option>
            <option>Selangor</option>
            <option>Johor</option>
            <option>Penang</option>
            <option>Sabah</option>
            <option>Sarawak</option>
            <option>Perak</option>
            <option>Kedah</option>
            <option>Terengganu</option>
            <option>Kelantan</option>
            <option>Pahang</option>
            <option>Remote</option>
          </select>
        </div>
      </div>

      <!-- Results Count -->
      <div class="results-info">
        <p>Showing <strong>{{ filteredJobs.length }}</strong> jobs</p>
      </div>

      <!-- Job Cards -->
      <div v-if="loading" class="loading-state">
        <p>⏳ Loading jobs...</p>
      </div>

      <div v-else-if="filteredJobs.length === 0" class="empty-state">
        <p>No jobs found. Try different search terms!</p>
      </div>

      <div v-else class="jobs-list">
        <div v-for="job in filteredJobs" :key="job.id" class="job-card">

          <!-- Job Header -->
          <div class="job-header">
            <div class="job-info">
              <h3>{{ job.job_title }}</h3>
              <p class="company">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 4px; color: var(--text-muted);"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="9" y1="16" x2="15" y2="16"/><line x1="15" y1="16" x2="15" y2="22"/></svg>
                {{ job.employer?.company_name || 'Company' }}
              </p>
              <p class="location">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 4px; color: var(--text-muted);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ job.location }}
              </p>
            </div>
            <div class="job-meta">
              <!-- Match Score -->
              <div :class="['match-badge', getMatchClass(job.match_score)]">
                <span class="score">{{ job.match_score }}%</span>
                <span class="score-label">Match</span>
              </div>
              <div :class="['job-status', job.job_status]">{{ job.job_status }}</div>
            </div>
          </div>

          <p class="job-desc">{{ job.description?.slice(0, 150) }}...</p>

          <!-- 5-Rule Mini Scores -->
          <div class="mini-scores">
            <div class="mini-score-item">
              <span class="mini-label s-label">Skills (S)</span>
              <div class="mini-bar">
                <div class="mini-fill" :style="{ width: job.s_score + '%', background: getBarColor(job.s_score) }"></div>
              </div>
              <span class="mini-val">{{ job.s_score }}%</span>
            </div>
            <div class="mini-score-item">
              <span class="mini-label p-label">Physical (P)</span>
              <div class="mini-bar">
                <div class="mini-fill" :style="{ width: job.p_score + '%', background: getBarColor(job.p_score) }"></div>
              </div>
              <span class="mini-val">{{ job.p_score }}%</span>
            </div>
            <div class="mini-score-item">
              <span class="mini-label t-label">Tech (T)</span>
              <div class="mini-bar">
                <div class="mini-fill" :style="{ width: job.t_score + '%', background: getBarColor(job.t_score) }"></div>
              </div>
              <span class="mini-val">{{ job.t_score }}%</span>
            </div>
            <div class="mini-score-item">
              <span class="mini-label w-label">Flex (W)</span>
              <div class="mini-bar">
                <div class="mini-fill" :style="{ width: job.w_score + '%', background: getBarColor(job.w_score) }"></div>
              </div>
              <span class="mini-val">{{ job.w_score }}%</span>
            </div>
            <div class="mini-score-item">
              <span class="mini-label c-label">Sensory (C)</span>
              <div class="mini-bar">
                <div class="mini-fill" :style="{ width: job.c_score + '%', background: getBarColor(job.c_score) }"></div>
              </div>
              <span class="mini-val">{{ job.c_score }}%</span>
            </div>
          </div>

          <!-- S-Rule: Skills Required -->
          <div class="job-tags" v-if="job.requirements?.length > 0">
            <span class="tag-label s-label">S-Rule Skills:</span>
            <span v-for="req in job.requirements.slice(0, 3)" :key="req" class="tag-skill">
              {{ req }}
            </span>
            <span v-if="job.requirements.length > 3" class="tag-more">
              +{{ job.requirements.length - 3 }}
            </span>
          </div>

          <!-- P-Rule: Physical Facilities -->
          <div class="job-tags" v-if="job.physical_facilities?.length > 0">
            <span class="tag-label p-label">P-Rule:</span>
            <span v-for="item in job.physical_facilities.slice(0, 2)" :key="item" class="tag-physical">
              {{ item }}
            </span>
            <span v-if="job.physical_facilities.length > 2" class="tag-more">
              +{{ job.physical_facilities.length - 2 }}
            </span>
          </div>

          <!-- T-Rule: Tech Support -->
          <div class="job-tags" v-if="job.tech_support?.length > 0">
            <span class="tag-label t-label">T-Rule:</span>
            <span v-for="item in job.tech_support.slice(0, 2)" :key="item" class="tag-tech">
              {{ item }}
            </span>
            <span v-if="job.tech_support.length > 2" class="tag-more">
              +{{ job.tech_support.length - 2 }}
            </span>
          </div>

          <!-- W-Rule: Work Arrangement -->
          <div class="job-tags" v-if="job.work_arrangement?.length > 0">
            <span class="tag-label w-label">W-Rule:</span>
            <span v-for="item in job.work_arrangement.slice(0, 2)" :key="item" class="tag-work">
              {{ item }}
            </span>
            <span v-if="job.work_arrangement.length > 2" class="tag-more">
              +{{ job.work_arrangement.length - 2 }}
            </span>
          </div>

          <!-- C-Rule: Sensory Support -->
          <div class="job-tags" v-if="job.sensory_support?.length > 0">
            <span class="tag-label c-label">C-Rule:</span>
            <span v-for="item in job.sensory_support.slice(0, 2)" :key="item" class="tag-sensory">
              {{ item }}
            </span>
            <span v-if="job.sensory_support.length > 2" class="tag-more">
              +{{ job.sensory_support.length - 2 }}
            </span>
          </div>

          <div class="job-footer">
            <router-link :to="'/pwd/jobs/' + job.id + '/explanation'" class="btn-match">
              View Match
            </router-link>
            <button @click="contactEmployer(job.employer)" class="btn-contact-job">
              Contact
            </button>
            <button @click="viewJob(job)" class="btn-view">Details</button>
            <button @click="applyJob(job)" class="btn-apply" :disabled="job.applied">
              {{ job.applied ? 'Applied' : 'Apply Now' }}
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Job Detail Modal -->
    <div v-if="selectedJob" class="modal-overlay" @click.self="selectedJob = null">
      <div class="modal">
        <div class="modal-header">
          <div>
            <h2>{{ selectedJob.job_title }}</h2>
            <p>
              <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 4px; color: var(--text-muted);"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="9" y1="16" x2="15" y2="16"/><line x1="15" y1="16" x2="15" y2="22"/></svg>
              {{ selectedJob.employer?.company_name }}
            </p>
            <p>
              <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 4px; color: var(--text-muted);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              {{ selectedJob.location }}
            </p>
            <div :class="['match-badge-sm', getMatchClass(selectedJob.match_score)]">
              {{ selectedJob.match_score }}% Match
            </div>
          </div>
          <button @click="selectedJob = null" class="btn-close">✕</button>
        </div>

        <div class="modal-body">
          <div class="modal-section">
            <h4>Job Description</h4>
            <p>{{ selectedJob.description }}</p>
          </div>

          <!-- S-Rule -->
          <div class="modal-section" v-if="selectedJob.requirements?.length > 0">
            <h4 class="s-label">S-Rule: Skills Required</h4>
            <div class="tags">
              <span v-for="req in selectedJob.requirements" :key="req" class="tag-skill">
                {{ req }}
              </span>
            </div>
          </div>

          <!-- P-Rule -->
          <div class="modal-section" v-if="selectedJob.physical_facilities?.length > 0">
            <h4 class="p-label">P-Rule: Physical Facilities</h4>
            <div class="tags">
              <span v-for="item in selectedJob.physical_facilities" :key="item" class="tag-physical">
                {{ item }}
              </span>
            </div>
          </div>

          <!-- T-Rule -->
          <div class="modal-section" v-if="selectedJob.tech_support?.length > 0">
            <h4 class="t-label">T-Rule: Technology Support</h4>
            <div class="tags">
              <span v-for="item in selectedJob.tech_support" :key="item" class="tag-tech">
                {{ item }}
              </span>
            </div>
          </div>

          <!-- W-Rule -->
          <div class="modal-section" v-if="selectedJob.work_arrangement?.length > 0">
            <h4 class="w-label">W-Rule: Work Arrangement</h4>
            <div class="tags">
              <span v-for="item in selectedJob.work_arrangement" :key="item" class="tag-work">
                {{ item }}
              </span>
            </div>
          </div>

          <!-- C-Rule -->
          <div class="modal-section" v-if="selectedJob.sensory_support?.length > 0">
            <h4 class="c-label">C-Rule: Communication Support</h4>
            <div class="tags">
              <span v-for="item in selectedJob.sensory_support" :key="item" class="tag-sensory">
                {{ item }}
              </span>
            </div>
          </div>

          <!-- Company Profile Section -->
          <div class="modal-section company-profile-section" v-if="selectedJob.employer">
            <h4 style="display: flex; align-items: center; gap: 8px;">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="15" y1="22" x2="15" y2="16"/></svg>
              About {{ selectedJob.employer.company_name }}
            </h4>
            <div class="company-card-brief">
              <img v-if="selectedJob.employer.logo_path" :src="'/storage/' + selectedJob.employer.logo_path" class="company-card-logo" />
              <div v-else class="company-card-initials">{{ getInitials(selectedJob.employer.company_name) }}</div>
              <div class="company-card-meta">
                <span v-if="selectedJob.employer.industry_type" class="company-meta-badge">🏢 {{ selectedJob.employer.industry_type }}</span>
                <span v-if="selectedJob.employer.company_email" class="company-meta-badge">✉️ {{ selectedJob.employer.company_email }}</span>
                <span v-if="selectedJob.employer.company_address" class="company-meta-address">📍 {{ selectedJob.employer.company_address }}</span>
              </div>
            </div>
            <p v-if="selectedJob.employer.about_us" class="company-about-desc">
              {{ selectedJob.employer.about_us }}
            </p>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="selectedJob = null" class="btn-cancel">Close</button>
          <router-link :to="'/pwd/jobs/' + selectedJob.id + '/explanation'" class="btn-match-modal">
            View Match
          </router-link>
          <button @click="contactEmployer(selectedJob.employer)" class="btn-contact-modal-job">
            Contact Employer
          </button>
          <button @click="applyJob(selectedJob)" class="btn-apply-modal"
            :disabled="selectedJob.applied">
            {{ selectedJob.applied ? 'Applied' : 'Apply Now' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { toast } from '../../services/toast'

export default {
  name: 'SearchJob',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      jobs: [],
      filteredJobs: [],
      search: '',
      filterIndustry: '',
      filterLocation: '',
      selectedJob: null,
      loading: false
    }
  },
  mounted() {
    this.loadJobs()
  },
  methods: {
    async loadJobs() {
      this.loading = true
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/pwd/jobs')
        this.jobs = response.data.jobs || []
        this.filteredJobs = this.jobs
      } catch (err) {
        console.log('Failed to load jobs')
        this.jobs = []
        this.filteredJobs = []
      }
      this.loading = false
    },
    filterJobs() {
      this.filteredJobs = this.jobs.filter(job => {
        const matchSearch = !this.search ||
          job.job_title.toLowerCase().includes(this.search.toLowerCase()) ||
          (job.employer?.company_name || '').toLowerCase().includes(this.search.toLowerCase())
        const matchIndustry = !this.filterIndustry ||
          (job.employer?.industry_type || '') === this.filterIndustry
        const matchLocation = !this.filterLocation ||
          job.location.toLowerCase().includes(this.filterLocation.toLowerCase())
        return matchSearch && matchIndustry && matchLocation
      })
    },
    getMatchClass(score) {
      if (score >= 80) return 'match-excellent'
      if (score >= 60) return 'match-good'
      if (score >= 40) return 'match-fair'
      return 'match-poor'
    },
    getBarColor(score) {
      if (score >= 80) return '#10b981'
      if (score >= 60) return '#3b82f6'
      if (score >= 40) return '#f59e0b'
      return '#ef4444'
    },
    viewJob(job) {
      this.selectedJob = job
    },
    async applyJob(job) {
      try {
        await axios.post('/api/pwd/apply', { job_id: job.id })
        job.applied = true
        if (this.selectedJob && this.selectedJob.id === job.id) {
          this.selectedJob.applied = true
        }
        toast.success('Application sent successfully!')
      } catch (err) {
        if (err.response?.data?.message) {
          toast.error(err.response.data.message)
        } else {
          toast.error('Failed to apply. Please try again.')
        }
      }
    },
    contactEmployer(employer) {
      if (!employer) return
      this.selectedJob = null
      this.$router.push({
        path: '/pwd/mailbox',
        query: {
          compose: true,
          receiver_id: employer.user_id,
          receiver_name: employer.company_name
        }
      })
    },
    async handleLogout() {
      try { await axios.post('/api/logout') } catch (err) {}
      localStorage.removeItem('token')
      localStorage.removeItem('role')
      this.$router.push('/login')
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

.sidebar { width: 240px; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; padding: 24px 16px; position: fixed; height: 100vh; z-index: 50; }
.sidebar-logo { display: flex; align-items: center; gap: 10px; font-size: 18px; font-weight: 700; color: var(--primary); margin-bottom: 32px; padding: 0 8px; }
.sidebar-nav { display: flex; flex-direction: column; gap: 4px; flex: 1; }
.nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; }
.nav-item:hover { background: var(--primary-light); color: var(--primary); }
.nav-item.active { background: var(--primary); color: white; }
.btn-logout { padding: 12px 16px; background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; text-align: left; }
.btn-logout:hover { background: #ef4444; color: white; }

.main-content { margin-left: 240px; flex: 1; padding: 32px; }

.topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.topbar h1 { font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.topbar p { font-size: 14px; color: var(--text-muted); }

.theme-toggle { padding: 8px 16px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.theme-toggle:hover { border-color: var(--primary); color: var(--primary); }

.search-card { background: var(--surface); border-radius: 16px; padding: 20px; margin-bottom: 20px; border: 1px solid var(--border); box-shadow: var(--card-shadow); }
.search-row { display: flex; gap: 12px; flex-wrap: wrap; }
.search-input-wrapper { flex: 2; position: relative; min-width: 200px; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; }
.search-input { width: 100%; padding: 10px 14px 10px 40px; border: 2px solid var(--border); border-radius: 8px; font-size: 14px; color: var(--text); background: var(--input-bg); outline: none; transition: border-color 0.2s; }
.search-input:focus { border-color: var(--primary); }
.filter-select { flex: 1; min-width: 140px; padding: 10px 14px; border: 2px solid var(--border); border-radius: 8px; font-size: 14px; color: var(--text); background: var(--input-bg); outline: none; cursor: pointer; }

.results-info { margin-bottom: 16px; font-size: 14px; color: var(--text-muted); }

.loading-state, .empty-state { background: var(--surface); border-radius: 16px; padding: 48px; text-align: center; border: 1px solid var(--border); color: var(--text-muted); font-size: 15px; }

.jobs-list { display: flex; flex-direction: column; gap: 16px; }

.job-card { background: var(--surface); border-radius: 16px; padding: 20px; border: 1px solid var(--border); box-shadow: var(--card-shadow); transition: transform 0.2s; }
.job-card:hover { transform: translateY(-2px); }

.job-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.job-info h3 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.company, .location { font-size: 13px; color: var(--text-muted); margin-bottom: 2px; }

.job-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }

/* Match Badge */
.match-badge { display: flex; flex-direction: column; align-items: center; padding: 8px 14px; border-radius: 10px; min-width: 70px; }
.match-excellent { background: #f0fdf4; border: 1px solid #bbf7d0; }
.match-good { background: #eff6ff; border: 1px solid #bfdbfe; }
.match-fair { background: #fef9c3; border: 1px solid #fde047; }
.match-poor { background: #fef2f2; border: 1px solid #fecaca; }

.score { font-size: 20px; font-weight: 800; }
.match-excellent .score { color: #166534; }
.match-good .score { color: #1d4ed8; }
.match-fair .score { color: #854d0e; }
.match-poor .score { color: #991b1b; }

.score-label { font-size: 10px; font-weight: 600; color: var(--text-muted); }

.match-badge-sm { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-top: 4px; }
.match-badge-sm.match-excellent { background: #f0fdf4; color: #166534; }
.match-badge-sm.match-good { background: #eff6ff; color: #1d4ed8; }
.match-badge-sm.match-fair { background: #fef9c3; color: #854d0e; }
.match-badge-sm.match-poor { background: #fef2f2; color: #991b1b; }

.job-status { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize; }
.job-status.open { background: #f0fdf4; color: #166534; }
.job-status.closed { background: #fef2f2; color: #991b1b; }

.job-desc { font-size: 13px; color: var(--text-muted); margin-bottom: 12px; line-height: 1.6; }

/* Mini Scores */
.mini-scores { display: flex; flex-direction: column; gap: 4px; margin-bottom: 12px; padding: 10px 12px; background: var(--bg); border-radius: 8px; border: 1px solid var(--border); }
.mini-score-item { display: flex; align-items: center; gap: 8px; }
.mini-label { font-size: 11px; font-weight: 700; width: 50px; flex-shrink: 0; }
.mini-bar { flex: 1; height: 6px; background: var(--border); border-radius: 10px; overflow: hidden; }
.mini-fill { height: 100%; border-radius: 10px; transition: width 0.5s ease; min-width: 2px; }
.mini-val { font-size: 11px; font-weight: 600; color: var(--text-muted); width: 32px; text-align: right; }

/* Rule Labels */
.s-label { color: #1d4ed8; }
.p-label { color: #166534; }
.t-label { color: #7e22ce; }
.w-label { color: #c2410c; }
.c-label { color: #854d0e; }

.job-tags { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; margin-bottom: 6px; }
.tag-label { font-size: 11px; font-weight: 700; }

/* Rule Tags */
.tag-skill { padding: 2px 8px; background: #eff6ff; color: #1d4ed8; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-physical { padding: 2px 8px; background: #f0fdf4; color: #166534; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-tech { padding: 2px 8px; background: #faf5ff; color: #7e22ce; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-work { padding: 2px 8px; background: #fff7ed; color: #c2410c; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-sensory { padding: 2px 8px; background: #fef9c3; color: #854d0e; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-more { padding: 2px 8px; background: var(--bg); color: var(--text-muted); border-radius: 20px; font-size: 11px; border: 1px solid var(--border); }

.job-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border); }

.btn-match { padding: 8px 20px; background: var(--primary-light); color: var(--primary); border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; display: flex; align-items: center; transition: all 0.2s; }
.btn-match:hover { background: var(--primary); color: white; }

.btn-view { padding: 8px 20px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-view:hover { border-color: var(--primary); color: var(--primary); }

.btn-apply { padding: 8px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.btn-apply:hover { background: var(--primary-hover); }
.btn-apply:disabled { opacity: 0.6; cursor: not-allowed; background: #10b981; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
.modal { background: var(--surface); border-radius: 20px; width: 100%; max-width: 600px; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }

.modal-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 24px; border-bottom: 1px solid var(--border); }
.modal-header h2 { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.modal-header p { font-size: 13px; color: var(--text-muted); margin-bottom: 2px; }

.btn-close { padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); cursor: pointer; font-size: 14px; }

.modal-body { padding: 24px; }
.modal-section { margin-bottom: 16px; }
.modal-section h4 { font-size: 13px; font-weight: 700; margin-bottom: 8px; }
.modal-section p { font-size: 14px; color: var(--text-muted); line-height: 1.6; }
.tags { display: flex; flex-wrap: wrap; gap: 6px; }

.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 20px 24px; border-top: 1px solid var(--border); align-items: center; }

.btn-cancel { padding: 10px 24px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 14px; font-weight: 600; cursor: pointer; }

.btn-match-modal { padding: 10px 24px; background: var(--primary-light); color: var(--primary); border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
.btn-match-modal:hover { background: var(--primary); color: white; }

.btn-apply-modal { padding: 10px 24px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.btn-apply-modal:hover { background: var(--primary-hover); }
.btn-apply-modal:disabled { opacity: 0.6; cursor: not-allowed; background: #10b981; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .search-row { flex-direction: column; }
  .job-footer { flex-wrap: wrap; }
}

.btn-contact-job {
  padding: 8px 12px;
  background: var(--primary-light);
  color: var(--primary);
  border: 1px solid var(--primary);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-contact-job:hover {
  background: var(--primary);
  color: white;
}

.btn-contact-modal-job {
  padding: 10px 24px;
  background: var(--primary-light);
  color: var(--primary);
  border: 1px solid var(--primary);
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-contact-modal-job:hover {
  background: var(--primary);
  color: white;
}

/* Company profile styling inside job details modal */
.company-profile-section {
  border-top: 1px solid var(--border);
  padding-top: 16px;
  margin-top: 16px;
}
.company-card-brief {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 12px;
  background: var(--bg);
  padding: 12px;
  border-radius: 12px;
  border: 1px solid var(--border);
}
.company-card-logo {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  object-fit: cover;
  border: 1px solid var(--border);
}
.company-card-initials {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  background: var(--primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: 700;
}
.company-card-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.company-meta-badge {
  font-size: 11px;
  font-weight: 600;
  color: var(--text-muted);
}
.company-meta-address {
  font-size: 11px;
  color: var(--text-muted);
  word-break: break-all;
}
.company-about-desc {
  font-size: 13px !important;
  color: var(--text) !important;
  line-height: 1.5 !important;
  white-space: pre-line;
}
</style>