<template>
  <div class="dashboard">
    <PWDSidebar />

    <main class="main-content">
      <div class="topbar">
        <div>
          <router-link to="/pwd/jobs" class="btn-back">← Back to Jobs</router-link>
          <h1>Job Compatibility Analysis</h1>
          <p>5-Rule analysis of how your profile matches this job</p>
        </div>
        <button class="theme-toggle" @click="$emit('toggleTheme')">
          {{ isDark ? '☀️ Light' : '🌙 Dark' }}
        </button>
      </div>

      <div v-if="loading" class="loading-state">
        <p>⏳ Loading compatibility details...</p>
      </div>

      <div v-else>
        <!-- Job Info -->
        <div class="card job-card">
          <div class="job-header">
            <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); flex-shrink: 0; margin-right: 4px;"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            <div>
              <h2>{{ job.job_title || 'Job Title' }}</h2>
              <p>{{ job.employer?.company_name || 'Company' }} • {{ job.location }}</p>
              <div :class="['job-status', job.job_status]">{{ job.job_status }}</div>
            </div>
          </div>
          <p class="job-desc">{{ job.description || 'No description available.' }}</p>
        </div>

        <!-- Overall Score -->
        <div class="card score-card">
          <h2>Overall Compatibility Score</h2>
          <div class="score-display">
            <div :class="['score-circle', getScoreClass(match.match_score)]">
              <span class="score-number">{{ match.match_score }}%</span>
              <span class="score-label">Fit</span>
            </div>
            <div class="score-breakdown">
              <!-- S-Rule -->
              <div class="score-item">
                <div class="score-item-header">
                  <span>S-Rule — Skills Match</span>
                  <span :class="getScoreClass(match.s_score)">{{ match.s_score }}%</span>
                </div>
                <div class="score-bar">
                  <div class="score-fill" :style="{ width: match.s_score + '%', background: getBarColor(match.s_score) }"></div>
                </div>
                <p class="score-weight">Weight: 30% → Contribution: {{ Math.round(match.s_score * 0.30) }} pts</p>
              </div>
              <!-- P-Rule -->
              <div class="score-item">
                <div class="score-item-header">
                  <span>P-Rule — Physical Accessibility</span>
                  <span :class="getScoreClass(match.p_score)">{{ match.p_score }}%</span>
                </div>
                <div class="score-bar">
                  <div class="score-fill" :style="{ width: match.p_score + '%', background: getBarColor(match.p_score) }"></div>
                </div>
                <p class="score-weight">Weight: 25% → Contribution: {{ Math.round(match.p_score * 0.25) }} pts</p>
              </div>
              <!-- T-Rule -->
              <div class="score-item">
                <div class="score-item-header">
                  <span>T-Rule — Technology Support</span>
                  <span :class="getScoreClass(match.t_score)">{{ match.t_score }}%</span>
                </div>
                <div class="score-bar">
                  <div class="score-fill" :style="{ width: match.t_score + '%', background: getBarColor(match.t_score) }"></div>
                </div>
                <p class="score-weight">Weight: 20% → Contribution: {{ Math.round(match.t_score * 0.20) }} pts</p>
              </div>
              <!-- W-Rule -->
              <div class="score-item">
                <div class="score-item-header">
                  <span>W-Rule — Work Arrangement</span>
                  <span :class="getScoreClass(match.w_score)">{{ match.w_score }}%</span>
                </div>
                <div class="score-bar">
                  <div class="score-fill" :style="{ width: match.w_score + '%', background: getBarColor(match.w_score) }"></div>
                </div>
                <p class="score-weight">Weight: 15% → Contribution: {{ Math.round(match.w_score * 0.15) }} pts</p>
              </div>
              <!-- C-Rule -->
              <div class="score-item">
                <div class="score-item-header">
                  <span>C-Rule — Communication & Sensory</span>
                  <span :class="getScoreClass(match.c_score)">{{ match.c_score }}%</span>
                </div>
                <div class="score-bar">
                  <div class="score-fill" :style="{ width: match.c_score + '%', background: getBarColor(match.c_score) }"></div>
                </div>
                <p class="score-weight">Weight: 10% → Contribution: {{ Math.round(match.c_score * 0.10) }} pts</p>
              </div>
            </div>
          </div>

          <!-- Score Legend -->
          <div class="score-legend">
            <div class="legend-item"><span class="legend-dot green"></span> 80-100% Excellent</div>
            <div class="legend-item"><span class="legend-dot blue"></span> 60-79% Good</div>
            <div class="legend-item"><span class="legend-dot orange"></span> 40-59% Fair</div>
            <div class="legend-item"><span class="legend-dot red"></span> 0-39% Poor</div>
          </div>
        </div>

        <!-- 5 Rules Detail -->
        <div class="rules-grid">

          <!-- S-Rule -->
          <div class="rule-card">
            <div class="rule-header s-rule">
              <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #1e40af; flex-shrink: 0;"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
              <div>
                <h3>S-Rule: Skills Match</h3>
                <p>Weight 30% — Professional skills</p>
              </div>
              <div :class="['rule-score', getScoreClass(match.s_score)]">{{ match.s_score }}%</div>
            </div>
            <div class="rule-body">
              <div class="rule-section">
                <h4 class="matched-title">Matched Skills</h4>
                <div class="tags">
                  <span v-for="item in match.matched_skills" :key="item" class="tag-matched">✓ {{ item }}</span>
                  <span v-if="!match.matched_skills?.length" class="no-data">None matched</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="missing-title">Missing Skills</h4>
                <div class="tags">
                  <span v-for="item in match.missing_skills" :key="item" class="tag-missing">✗ {{ item }}</span>
                  <span v-if="!match.missing_skills?.length" class="no-data">None missing</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="info-title">Job Requirements</h4>
                <div class="tags">
                  <span v-for="item in job.requirements" :key="item" class="tag-info">{{ item }}</span>
                  <span v-if="!job.requirements?.length" class="no-data">No requirements listed</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="info-title">Your Profile Skills</h4>
                <div class="tags">
                  <span v-for="item in profile.skills" :key="item" class="tag-skill">{{ item }}</span>
                  <span v-if="!profile.skills?.length" class="no-data">No skills listed</span>
                </div>
              </div>
            </div>
          </div>

          <!-- P-Rule -->
          <div class="rule-card">
            <div class="rule-header p-rule">
              <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #166534; flex-shrink: 0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              <div>
                <h3>P-Rule: Physical Accessibility</h3>
                <p>Weight 25% — Building facilities</p>
              </div>
              <div :class="['rule-score', getScoreClass(match.p_score)]">{{ match.p_score }}%</div>
            </div>
            <div class="rule-body">
              <div class="rule-section">
                <h4 class="matched-title">Matched Physical Facilities</h4>
                <div class="tags">
                  <span v-for="item in match.matched_physical" :key="item" class="tag-matched">✓ {{ item }}</span>
                  <span v-if="!match.matched_physical?.length" class="no-data">None matched</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="missing-title">Missing Physical Facilities</h4>
                <div class="tags">
                  <span v-for="item in match.missing_physical" :key="item" class="tag-missing">✗ {{ item }}</span>
                  <span v-if="!match.missing_physical?.length" class="no-data">None missing</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="info-title">Office Accessibility Provided</h4>
                <div class="tags">
                  <span v-for="item in job.physical_facilities" :key="item" class="tag-info">{{ item }}</span>
                  <span v-if="!job.physical_facilities?.length" class="no-data">None listed</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="info-title">Your Accessibility Needs</h4>
                <div class="tags">
                  <span v-for="item in profile.physical_needs" :key="item" class="tag-need">{{ item }}</span>
                  <span v-if="!profile.physical_needs?.length" class="no-data">No needs listed</span>
                </div>
              </div>
            </div>
          </div>

          <!-- T-Rule -->
          <div class="rule-card">
            <div class="rule-header t-rule">
              <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #7e22ce; flex-shrink: 0;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              <div>
                <h3>T-Rule: Technology Support</h3>
                <p>Weight 20% — Digital assistive tools</p>
              </div>
              <div :class="['rule-score', getScoreClass(match.t_score)]">{{ match.t_score }}%</div>
            </div>
            <div class="rule-body">
              <div class="rule-section">
                <h4 class="matched-title">Matched Assistive Tech</h4>
                <div class="tags">
                  <span v-for="item in match.matched_tech" :key="item" class="tag-matched">✓ {{ item }}</span>
                  <span v-if="!match.matched_tech?.length" class="no-data">None matched</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="missing-title">Missing Assistive Tech</h4>
                <div class="tags">
                  <span v-for="item in match.missing_tech" :key="item" class="tag-missing">✗ {{ item }}</span>
                  <span v-if="!match.missing_tech?.length" class="no-data">None missing</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="info-title">Office Assistive Tech Provided</h4>
                <div class="tags">
                  <span v-for="item in job.tech_support" :key="item" class="tag-info">{{ item }}</span>
                  <span v-if="!job.tech_support?.length" class="no-data">None listed</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="info-title">Your Assistive Tech Needs</h4>
                <div class="tags">
                  <span v-for="item in profile.tech_needs" :key="item" class="tag-need">{{ item }}</span>
                  <span v-if="!profile.tech_needs?.length" class="no-data">No needs listed</span>
                </div>
              </div>
            </div>
          </div>

          <!-- W-Rule -->
          <div class="rule-card">
            <div class="rule-header w-rule">
              <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #c2410c; flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <div>
                <h3>W-Rule: Work Arrangement</h3>
                <p>Weight 15% — Work flexibility</p>
              </div>
              <div :class="['rule-score', getScoreClass(match.w_score)]">{{ match.w_score }}%</div>
            </div>
            <div class="rule-body">
              <div class="rule-section">
                <h4 class="matched-title">Matched Work Arrangements</h4>
                <div class="tags">
                  <span v-for="item in match.matched_work" :key="item" class="tag-matched">✓ {{ item }}</span>
                  <span v-if="!match.matched_work?.length" class="no-data">None matched</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="missing-title">Missing Work Arrangements</h4>
                <div class="tags">
                  <span v-for="item in match.missing_work" :key="item" class="tag-missing">✗ {{ item }}</span>
                  <span v-if="!match.missing_work?.length" class="no-data">None missing</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="info-title">Office Work Arrangements Offered</h4>
                <div class="tags">
                  <span v-for="item in job.work_arrangement" :key="item" class="tag-info">{{ item }}</span>
                  <span v-if="!job.work_arrangement?.length" class="no-data">None listed</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="info-title">Your Work Arrangement Needs</h4>
                <div class="tags">
                  <span v-for="item in profile.work_arrangement" :key="item" class="tag-need">{{ item }}</span>
                  <span v-if="!profile.work_arrangement?.length" class="no-data">No needs listed</span>
                </div>
              </div>
            </div>
          </div>

          <!-- C-Rule -->
          <div class="rule-card">
            <div class="rule-header c-rule">
              <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #854d0e; flex-shrink: 0;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              <div>
                <h3>C-Rule: Communication & Sensory</h3>
                <p>Weight 10% — Sensory support</p>
              </div>
              <div :class="['rule-score', getScoreClass(match.c_score)]">{{ match.c_score }}%</div>
            </div>
            <div class="rule-body">
              <div class="rule-section">
                <h4 class="matched-title">Matched Sensory & Communication</h4>
                <div class="tags">
                  <span v-for="item in match.matched_sensory" :key="item" class="tag-matched">✓ {{ item }}</span>
                  <span v-if="!match.matched_sensory?.length" class="no-data">None matched</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="missing-title">Missing Sensory & Communication</h4>
                <div class="tags">
                  <span v-for="item in match.missing_sensory" :key="item" class="tag-missing">✗ {{ item }}</span>
                  <span v-if="!match.missing_sensory?.length" class="no-data">None missing</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="info-title">Office Sensory Support Provided</h4>
                <div class="tags">
                  <span v-for="item in job.sensory_support" :key="item" class="tag-info">{{ item }}</span>
                  <span v-if="!job.sensory_support?.length" class="no-data">None listed</span>
                </div>
              </div>
              <div class="rule-section">
                <h4 class="info-title">Your Sensory & Communication Needs</h4>
                <div class="tags">
                  <span v-for="item in profile.sensory_needs" :key="item" class="tag-need">{{ item }}</span>
                  <span v-if="!profile.sensory_needs?.length" class="no-data">No needs listed</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Apply Section -->
        <div class="card apply-card">
          <div class="apply-content">
            <div>
              <h3>Ready to Apply?</h3>
              <p>Your overall match score is <strong>{{ match.match_score }}%</strong></p>
            </div>
            <button @click="applyJob"
              :class="['btn-apply', applied ? 'applied' : '']"
              :disabled="applied || applying">
              {{ applied ? '✅ Already Applied' : applying ? 'Applying...' : '📨 Apply Now' }}
            </button>
          </div>
          <p v-if="applySuccess" class="success-msg">✅ {{ applySuccess }}</p>
          <p v-if="applyError" class="error-msg">❌ {{ applyError }}</p>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'MatchExplanation',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      job: {},
      profile: {},
      match: {
        match_score: 0,
        s_score: 0, p_score: 0, t_score: 0, w_score: 0, c_score: 0,
        matched_skills: [], matched_physical: [], matched_tech: [],
        matched_work: [], matched_sensory: [],
        missing_skills: [], missing_physical: [], missing_tech: [],
        missing_work: [], missing_sensory: [],
      },
      applied: false,
      applying: false,
      applySuccess: '',
      applyError: '',
      loading: true
    }
  },
  mounted() {
    this.loadData()
  },
  methods: {
    async loadData() {
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const jobId = this.$route.params.id
        const [jobRes, appRes] = await Promise.all([
          axios.get('/api/pwd/jobs/' + jobId),
          axios.get('/api/pwd/applications')
        ])
        this.job = jobRes.data.job || {}
        this.match = jobRes.data.match || this.match
        this.profile = jobRes.data.profile || {}
        const apps = appRes.data.applications || []
        this.applied = apps.some(a => a.job?.id == jobId)
      } catch (err) {
        console.log('Failed to load data')
      }
      this.loading = false
    },
    async applyJob() {
      this.applying = true
      this.applyError = ''
      try {
        await axios.post('/api/pwd/apply', { job_id: this.job.id })
        this.applied = true
        this.applySuccess = 'Application submitted successfully!'
      } catch (err) {
        this.applyError = err.response?.data?.message || 'Failed to apply.'
      }
      this.applying = false
    },
    getScoreClass(score) {
      if (score >= 80) return 'score-excellent'
      if (score >= 60) return 'score-good'
      if (score >= 40) return 'score-fair'
      return 'score-poor'
    },
    getBarColor(score) {
      if (score >= 80) return '#10b981'
      if (score >= 60) return '#3b82f6'
      if (score >= 40) return '#f59e0b'
      return '#ef4444'
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
.dashboard { display: flex; min-height: 100vh; background: var(--bg); }

.sidebar {
  width: 240px; background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex; flex-direction: column;
  padding: 24px 16px; position: fixed;
  height: 100vh; z-index: 50;
}

.sidebar-logo { display: flex; align-items: center; gap: 10px; font-size: 18px; font-weight: 700; color: var(--primary); margin-bottom: 32px; padding: 0 8px; }
.sidebar-nav { display: flex; flex-direction: column; gap: 4px; flex: 1; }
.nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; }
.nav-item:hover { background: var(--primary-light); color: var(--primary); }
.nav-item.active { background: var(--primary); color: white; }
.btn-logout { padding: 12px 16px; background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; text-align: left; }
.btn-logout:hover { background: #ef4444; color: white; }

.main-content { margin-left: 240px; flex: 1; padding: 32px; }

.topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.btn-back { display: inline-block; color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 600; margin-bottom: 8px; }
.topbar h1 { font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.topbar p { font-size: 14px; color: var(--text-muted); }
.theme-toggle { padding: 8px 16px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer; }

.loading-state { background: var(--surface); border-radius: 16px; padding: 48px; text-align: center; border: 1px solid var(--border); color: var(--text-muted); }

.card { background: var(--surface); border-radius: 16px; padding: 24px; border: 1px solid var(--border); box-shadow: var(--card-shadow); margin-bottom: 24px; }
.card h2 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 16px; }

/* Job Card */
.job-header { display: flex; align-items: center; gap: 16px; margin-bottom: 12px; }
.job-icon { font-size: 40px; }
.job-header h2 { font-size: 20px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.job-header p { font-size: 14px; color: var(--text-muted); margin-bottom: 6px; }
.job-status { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize; }
.job-status.open { background: #f0fdf4; color: #166534; }
.job-status.closed { background: #fef2f2; color: #991b1b; }
.job-desc { font-size: 14px; color: var(--text-muted); line-height: 1.6; }

/* Score Card */
.score-display { display: flex; gap: 32px; align-items: flex-start; margin-bottom: 20px; }

.score-circle { width: 120px; height: 120px; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0; border: 6px solid; }
.score-circle.score-excellent { border-color: #10b981; background: #f0fdf4; }
.score-circle.score-good { border-color: #3b82f6; background: #eff6ff; }
.score-circle.score-fair { border-color: #f59e0b; background: #fef9c3; }
.score-circle.score-poor { border-color: #ef4444; background: #fef2f2; }

.score-number { font-size: 28px; font-weight: 800; }
.score-circle.score-excellent .score-number { color: #166534; }
.score-circle.score-good .score-number { color: #1d4ed8; }
.score-circle.score-fair .score-number { color: #854d0e; }
.score-circle.score-poor .score-number { color: #991b1b; }
.score-label { font-size: 12px; color: var(--text-muted); font-weight: 600; }

.score-breakdown { flex: 1; display: flex; flex-direction: column; gap: 12px; }
.score-item { display: flex; flex-direction: column; gap: 4px; }
.score-item-header { display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; color: var(--text); }

.score-excellent { color: #166534 !important; }
.score-good { color: #1d4ed8 !important; }
.score-fair { color: #854d0e !important; }
.score-poor { color: #991b1b !important; }

.score-bar { height: 8px; background: var(--bg); border-radius: 10px; overflow: hidden; border: 1px solid var(--border); }
.score-fill { height: 100%; border-radius: 10px; transition: width 0.5s ease; min-width: 3px; }
.score-weight { font-size: 11px; color: var(--text-muted); }

.score-legend { display: flex; gap: 16px; flex-wrap: wrap; padding: 12px 16px; background: var(--bg); border-radius: 8px; border: 1px solid var(--border); }
.legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text); }
.legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.legend-dot.green { background: #10b981; }
.legend-dot.blue { background: #3b82f6; }
.legend-dot.orange { background: #f59e0b; }
.legend-dot.red { background: #ef4444; }

/* Rules Grid */
.rules-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }

.rule-card { background: var(--surface); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: var(--card-shadow); }

.rule-header { display: flex; align-items: center; gap: 12px; padding: 16px 20px; }
.s-rule { background: #eff6ff; border-bottom: 1px solid #bfdbfe; }
.p-rule { background: #f0fdf4; border-bottom: 1px solid #bbf7d0; }
.t-rule { background: #faf5ff; border-bottom: 1px solid #e9d5ff; }
.w-rule { background: #fff7ed; border-bottom: 1px solid #fed7aa; }
.c-rule { background: #fef9c3; border-bottom: 1px solid #fde047; }

.rule-icon { font-size: 28px; flex-shrink: 0; }
.rule-header h3 { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 2px; }
.rule-header p { font-size: 12px; color: var(--text-muted); }
.rule-score { margin-left: auto; font-size: 20px; font-weight: 800; flex-shrink: 0; }

.rule-body { padding: 16px 20px; display: flex; flex-direction: column; gap: 12px; }
.rule-section { display: flex; flex-direction: column; gap: 6px; }

.matched-title { font-size: 12px; font-weight: 700; color: #166534; }
.missing-title { font-size: 12px; font-weight: 700; color: #991b1b; }
.info-title { font-size: 12px; font-weight: 700; color: var(--text-muted); }

.tags { display: flex; flex-wrap: wrap; gap: 4px; }

.tag-matched { padding: 3px 8px; background: #f0fdf4; color: #166534; border-radius: 20px; font-size: 11px; font-weight: 600; border: 1px solid #bbf7d0; }
.tag-missing { padding: 3px 8px; background: #fef2f2; color: #991b1b; border-radius: 20px; font-size: 11px; font-weight: 600; border: 1px solid #fecaca; }
.tag-info { padding: 3px 8px; background: #faf5ff; color: #7e22ce; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-skill { padding: 3px 8px; background: var(--primary-light); color: var(--primary); border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-need { padding: 3px 8px; background: #fff7ed; color: #c2410c; border-radius: 20px; font-size: 11px; font-weight: 600; }
.no-data { font-size: 12px; color: var(--text-muted); font-style: italic; }

/* Apply Card */
.apply-content { display: flex; justify-content: space-between; align-items: center; }
.apply-content h3 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.apply-content p { font-size: 13px; color: var(--text-muted); }

.btn-apply { padding: 12px 32px; background: var(--primary); color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; }
.btn-apply:hover { background: var(--primary-hover); }
.btn-apply.applied { background: #10b981; cursor: not-allowed; }
.btn-apply:disabled { opacity: 0.7; cursor: not-allowed; }

.success-msg { color: #10b981; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 16px; font-size: 13px; margin-top: 12px; }
.error-msg { color: #ef4444; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 16px; font-size: 13px; margin-top: 12px; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .rules-grid { grid-template-columns: 1fr; }
  .score-display { flex-direction: column; align-items: center; }
}
</style>