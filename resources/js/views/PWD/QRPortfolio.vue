<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <PWDSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1>QR Portfolio</h1>
          <p>Your digital portfolio QR code for employers to scan</p>
        </div>
        <button class="theme-toggle" @click="$emit('toggleTheme')">
          {{ isDark ? '☀️ Light' : '🌙 Dark' }}
        </button>
      </div>

      <div class="content-grid">
        <!-- QR Code Card -->
        <div class="card qr-card">
          <h2>Your QR Code</h2>
          <p class="section-desc">Show this QR code to employers so they can instantly view your portfolio</p>

          <div class="qr-container">
            <div v-if="qrCode" class="qr-image-wrapper">
              <img :src="qrCode" alt="Your QR Code portfolio" class="qr-image"/>
            </div>
            <div class="qr-placeholder" v-else>
              <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted);"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12" y2="18.01"/></svg>
              <p>No QR code generated yet</p>
            </div>
          </div>

          <div class="qr-actions">
            <button @click="generateQR" class="btn-generate" :disabled="loading">
              {{ loading ? 'Generating...' : 'Generate QR Code' }}
            </button>
            <button v-if="qrCode" @click="downloadQR" class="btn-download">
              Download
            </button>
            <button v-if="qrCode" @click="printQR" class="btn-print">
              Print
            </button>
          </div>

          <div v-if="qrInfo" class="qr-info">
            <p>Generated: {{ qrInfo.generate_date }}</p>
            <p>Scan Count: {{ qrInfo.scan_count }}</p>
          </div>

          <p v-if="success" class="success-msg">{{ success }}</p>
          <p v-if="error" class="error-msg">{{ error }}</p>
        </div>

        <!-- Portfolio Preview Card -->
        <div class="card">
          <h2>Portfolio Preview</h2>
          <p class="section-desc">This is what employers see when they scan your QR code</p>

          <div class="portfolio-preview">
            <!-- Header -->
            <div class="preview-header">
              <div class="preview-avatar">{{ initials }}</div>
              <div>
                <h3>{{ profile.full_name || 'Your Name' }}</h3>
                <p>OKU No: {{ profile.oku_number || 'Not set' }}</p>
                <div :class="['preview-status', profile.status || 'pending']">
                  {{ profile.status || 'pending' }}
                </div>
              </div>
            </div>

            <!-- S-Rule: Skills -->
            <div class="preview-section">
              <h4 class="s-label">S-Rule: Skills</h4>
              <div class="tags">
                <span v-for="skill in (profile.skills || [])"
                  :key="skill" class="tag-skill">{{ skill }}</span>
                <span v-if="!profile.skills?.length" class="no-data">
                  No skills added yet
                </span>
              </div>
            </div>

            <!-- P-Rule: Physical Needs -->
            <div class="preview-section">
              <h4 class="p-label">P-Rule: Physical Needs</h4>
              <div class="tags">
                <span v-for="item in (profile.physical_needs || [])"
                  :key="item" class="tag-physical">{{ item }}</span>
                <span v-if="!profile.physical_needs?.length" class="no-data">
                  None added
                </span>
              </div>
            </div>

            <!-- T-Rule: Tech Needs -->
            <div class="preview-section">
              <h4 class="t-label">T-Rule: Tech Needs</h4>
              <div class="tags">
                <span v-for="item in (profile.tech_needs || [])"
                  :key="item" class="tag-tech">{{ item }}</span>
                <span v-if="!profile.tech_needs?.length" class="no-data">
                  None added
                </span>
              </div>
            </div>

            <!-- W-Rule: Work Arrangement -->
            <div class="preview-section">
              <h4 class="w-label">W-Rule: Work Arrangement</h4>
              <div class="tags">
                <span v-for="item in (profile.work_arrangement || [])"
                  :key="item" class="tag-work">{{ item }}</span>
                <span v-if="!profile.work_arrangement?.length" class="no-data">
                  None added
                </span>
              </div>
            </div>

            <!-- C-Rule: Sensory Needs -->
            <div class="preview-section">
              <h4 class="c-label">C-Rule: Sensory Needs</h4>
              <div class="tags">
                <span v-for="item in (profile.sensory_needs || [])"
                  :key="item" class="tag-sensory">{{ item }}</span>
                <span v-if="!profile.sensory_needs?.length" class="no-data">
                  None added
                </span>
              </div>
            </div>

            <router-link to="/pwd/profile" class="btn-edit-profile">
              Edit Profile
            </router-link>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'QRPortfolio',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      qrCode: null,
      qrInfo: null,
      profile: {},
      success: '',
      error: '',
      loading: false
    }
  },
  computed: {
    initials() {
      if (!this.profile.full_name) return '?'
      return this.profile.full_name
        .split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }
  },
  mounted() {
    this.loadProfile()
    this.loadQR()
  },
  methods: {
    async loadProfile() {
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/pwd/profile')
        if (response.data.profile) {
          this.profile = response.data.profile
        }
      } catch (err) {
        console.log('Profile not loaded')
      }
    },
    async loadQR() {
      try {
        const token = localStorage.getItem('token')
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/pwd/qr')
        if (response.data.qr_url) {
          this.qrCode = response.data.qr_url
          this.qrInfo = response.data.qr_info
        }
      } catch (err) {
        console.log('No QR yet')
      }
    },
    async generateQR() {
      this.loading = true
      this.success = ''
      this.error = ''
      try {
        const token = localStorage.getItem('token')
        const response = await axios.post('/api/pwd/qr/generate', {}, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        })
        this.qrCode = response.data.qr_url
        this.qrInfo = response.data.qr_info
        this.success = 'QR Code generated successfully!'
      } catch (err) {
        if (err.response) {
          this.error = 'Error: ' + (err.response.data.message || 'Failed to generate')
        } else {
          this.error = 'Network error. Please try again.'
        }
      }
      this.loading = false
    },
    async downloadQR() {
      try {
        const response = await fetch(this.qrCode)
        const blob = await response.blob()
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = 'AbilityScan-QR-' + (this.profile.full_name || 'Portfolio') + '.png'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        window.URL.revokeObjectURL(url)
      } catch (err) {
        this.error = 'Download failed. Please try again.'
      }
    },
    printQR() {
      const printWindow = window.open('', '_blank')
      printWindow.document.write(`
        <html>
          <head>
            <title>AbilityScan QR Portfolio</title>
            <style>
              body { text-align:center; padding:40px; font-family:sans-serif; }
              img { width:300px; height:300px; border: 2px solid #ccc; padding: 10px; }
              h2 { color: #1e40af; }
            </style>
          </head>
          <body>
            <h2>AbilityScan Digital Portfolio</h2>
            <p><strong>${this.profile.full_name || 'PWD Candidate'}</strong></p>
            <p>OKU No: ${this.profile.oku_number || '-'}</p>
            <img src="${this.qrCode}" onload="window.print()" onerror="window.print()" alt="QR Code" crossorigin="anonymous"/>
            <p>Scan to view full portfolio</p>
            <p style="font-size:12px;color:#666;">Generated by AbilityScan — UiTM FYP 2026</p>
          </body>
        </html>
      `)
      printWindow.document.close()
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
.btn-logout { padding: 12px 16px; background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; text-align: left; }
.btn-logout:hover { background: #ef4444; color: white; }

.main-content { margin-left: 240px; flex: 1; padding: 32px; }

.topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.topbar h1 { font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.topbar p { font-size: 14px; color: var(--text-muted); }

.theme-toggle { padding: 8px 16px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.theme-toggle:hover { border-color: var(--primary); color: var(--primary); }

.content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }

.card { background: var(--surface); border-radius: 16px; padding: 24px; border: 1px solid var(--border); box-shadow: var(--card-shadow); height: fit-content; }
.card h2 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
.section-desc { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }

/* QR Card */
.qr-container { display: flex; justify-content: center; margin-bottom: 20px; }
.qr-image-wrapper { padding: 16px; background: white; border-radius: 12px; border: 2px solid var(--border); }
.qr-image { width: 200px; height: 200px; display: block; }
.qr-placeholder { width: 200px; height: 200px; border: 2px dashed var(--border); border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; }
.qr-placeholder span { font-size: 48px; }
.qr-placeholder p { font-size: 13px; color: var(--text-muted); }

.qr-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }

.btn-generate { flex: 1; padding: 10px 16px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.btn-generate:hover { background: var(--primary-hover); }
.btn-generate:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-download { padding: 10px 16px; background: #10b981; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-download:hover { background: #059669; }

.btn-print { padding: 10px 16px; background: var(--surface); color: var(--text); border: 2px solid var(--border); border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-print:hover { border-color: var(--primary); color: var(--primary); }

.qr-info { background: var(--bg); border-radius: 8px; padding: 12px 16px; font-size: 13px; color: var(--text-muted); margin-bottom: 12px; }
.qr-info p { margin-bottom: 4px; }

/* Portfolio Preview */
.portfolio-preview { display: flex; flex-direction: column; gap: 14px; }

.preview-header { display: flex; align-items: center; gap: 16px; padding-bottom: 14px; border-bottom: 1px solid var(--border); }
.preview-avatar { width: 56px; height: 56px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; flex-shrink: 0; }
.preview-header h3 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.preview-header p { font-size: 13px; color: var(--text-muted); margin-bottom: 6px; }

.preview-status { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
.preview-status.pending { background: #fef9c3; color: #854d0e; }
.preview-status.approved { background: #f0fdf4; color: #166534; }
.preview-status.rejected { background: #fef2f2; color: #991b1b; }

.preview-section { display: flex; flex-direction: column; gap: 6px; }
.preview-section h4 { font-size: 12px; font-weight: 700; margin-bottom: 2px; }

/* Rule Labels */
.s-label { color: #1d4ed8; }
.p-label { color: #166534; }
.t-label { color: #7e22ce; }
.w-label { color: #c2410c; }
.c-label { color: #854d0e; }

.tags { display: flex; flex-wrap: wrap; gap: 6px; }

/* Rule Tags */
.tag-skill { padding: 3px 8px; background: #eff6ff; color: #1d4ed8; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-physical { padding: 3px 8px; background: #f0fdf4; color: #166534; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-tech { padding: 3px 8px; background: #faf5ff; color: #7e22ce; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-work { padding: 3px 8px; background: #fff7ed; color: #c2410c; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-sensory { padding: 3px 8px; background: #fef9c3; color: #854d0e; border-radius: 20px; font-size: 11px; font-weight: 600; }
.no-data { font-size: 12px; color: var(--text-muted); font-style: italic; }

.btn-edit-profile { display: inline-block; padding: 10px 20px; background: var(--primary); color: white; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; text-align: center; transition: background 0.2s; }
.btn-edit-profile:hover { background: var(--primary-hover); }

.success-msg { color: #10b981; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 16px; font-size: 13px; margin-top: 12px; }
.error-msg { color: #ef4444; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 16px; font-size: 13px; margin-top: 12px; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .content-grid { grid-template-columns: 1fr; }
}
</style>