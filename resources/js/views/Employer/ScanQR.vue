<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <EmployerSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1>📱 Scan QR Code</h1>
          <p>Scan PWD portfolio QR code to view their profile</p>
        </div>
        <button class="theme-toggle" @click="$emit('toggleTheme')">
          {{ isDark ? '☀️ Light' : '🌙 Dark' }}
        </button>
      </div>

      <div class="content-grid">
        <!-- Left: Scanner -->
        <div class="card">
          <h2>📷 QR Scanner</h2>
          <p class="section-desc">Use webcam to scan or enter OKU Number / Portfolio ID manually</p>

          <!-- Tabs -->
          <div class="scan-tabs">
            <button :class="['scan-tab', scanMode === 'camera' ? 'active' : '']"
              @click="switchMode('camera')">
              📷 Camera Scan
            </button>
            <button :class="['scan-tab', scanMode === 'manual' ? 'active' : '']"
              @click="switchMode('manual')">
              ⌨️ Manual Entry
            </button>
          </div>

          <!-- Camera Mode -->
          <div v-if="scanMode === 'camera'" class="camera-section">
            <div class="camera-container">
              <video v-show="cameraActive" ref="videoRef"
                class="camera-feed" autoplay playsinline></video>
              <canvas ref="canvasRef" class="camera-canvas"></canvas>
              <div v-if="!cameraActive" class="camera-placeholder">
                <span>📷</span>
                <p>Camera not started</p>
              </div>
              <div v-if="cameraActive" class="scan-overlay">
                <div class="scan-frame"></div>
                <p class="scan-hint">Point camera at QR code</p>
              </div>
            </div>
            <div class="camera-actions">
              <button v-if="!cameraActive" @click="startCamera" class="btn-start-camera">
                📷 Start Camera
              </button>
              <button v-else @click="stopCamera" class="btn-stop-camera">
                ⏹️ Stop Camera
              </button>
            </div>
            <div v-if="scanStatus" :class="['scan-status', scanStatus.type]">
              {{ scanStatus.message }}
            </div>
          </div>

          <!-- Manual Mode -->
          <div v-if="scanMode === 'manual'" class="manual-section">
            <div class="scanner-box">
              <div class="scanner-icon">🔍</div>
              <p class="scanner-text">Enter OKU Number or Portfolio ID</p>
            </div>
            <div class="form-group">
              <label>OKU Number or Portfolio ID</label>
              <div class="input-row">
                <input v-model="searchInput" type="text"
                  placeholder="e.g. OKU-2024-001 or 1"
                  class="form-input"
                  @keyup.enter="searchPortfolio"/>
                <button @click="searchPortfolio" class="btn-search" :disabled="loading">
                  {{ loading ? '...' : '🔍 Search' }}
                </button>
              </div>
              <p class="input-hint">
                💡 Enter OKU Number (e.g. OKU-2024-001) or Profile ID (e.g. 1)
              </p>
            </div>
          </div>

          <p v-if="error" class="error-msg">❌ {{ error }}</p>
        </div>

        <!-- Right: Profile Result -->
        <div class="card">
          <h2>👤 Candidate Profile</h2>
          <p class="section-desc">Profile details will appear here after scanning</p>

          <div v-if="!portfolio" class="empty-result">
            <span>🔍</span>
            <p>No profile loaded yet</p>
            <p class="hint">Scan a QR code or enter OKU Number / Portfolio ID</p>
          </div>

          <div v-else class="profile-result">
            <!-- Header -->
            <div class="profile-header">
              <div class="profile-avatar">{{ initials }}</div>
              <div>
                <h3>{{ portfolio.full_name }}</h3>
                <p>OKU No: {{ portfolio.oku_number }}</p>
                <div :class="['profile-status', portfolio.status]">
                  {{ portfolio.status }}
                </div>
              </div>
            </div>

            <!-- S-Rule: Skills -->
            <div class="profile-section">
              <h4 class="s-label">🛠️ S-Rule: Skills</h4>
              <div class="tags">
                <span v-for="skill in (portfolio.skills || [])"
                  :key="skill" class="tag-skill">{{ skill }}</span>
                <span v-if="!portfolio.skills?.length" class="no-data">No skills listed</span>
              </div>
            </div>

            <!-- P-Rule: Physical Needs -->
            <div class="profile-section">
              <h4 class="p-label">♿ P-Rule: Physical Needs</h4>
              <div class="tags">
                <span v-for="item in (portfolio.physical_needs || [])"
                  :key="item" class="tag-physical">{{ item }}</span>
                <span v-if="!portfolio.physical_needs?.length" class="no-data">None listed</span>
              </div>
            </div>

            <!-- T-Rule: Tech Needs -->
            <div class="profile-section">
              <h4 class="t-label">💻 T-Rule: Tech Needs</h4>
              <div class="tags">
                <span v-for="item in (portfolio.tech_needs || [])"
                  :key="item" class="tag-tech">{{ item }}</span>
                <span v-if="!portfolio.tech_needs?.length" class="no-data">None listed</span>
              </div>
            </div>

            <!-- W-Rule: Work Arrangement -->
            <div class="profile-section">
              <h4 class="w-label">🕐 W-Rule: Work Arrangement</h4>
              <div class="tags">
                <span v-for="item in (portfolio.work_arrangement || [])"
                  :key="item" class="tag-work">{{ item }}</span>
                <span v-if="!portfolio.work_arrangement?.length" class="no-data">None listed</span>
              </div>
            </div>

            <!-- C-Rule: Sensory Needs -->
            <div class="profile-section">
              <h4 class="c-label">🗣️ C-Rule: Sensory Needs</h4>
              <div class="tags">
                <span v-for="item in (portfolio.sensory_needs || [])"
                  :key="item" class="tag-sensory">{{ item }}</span>
                <span v-if="!portfolio.sensory_needs?.length" class="no-data">None listed</span>
              </div>
            </div>

            <!-- Attachments -->
            <div class="profile-section"
              v-if="portfolio.certificate_path || portfolio.video_path">
              <h4>📎 Attachments</h4>
              <div class="attachments">
                <a v-if="portfolio.certificate_path"
                  :href="'/storage/' + portfolio.certificate_path"
                  target="_blank" class="attachment-link">
                  📄 View Certificate
                </a>
                <a v-if="portfolio.video_path"
                  :href="'/storage/' + portfolio.video_path"
                  target="_blank" class="attachment-link">
                  🎥 View Skills Video
                </a>
              </div>
            </div>

            <!-- Actions -->
            <div class="profile-actions">
              <button @click="contactCandidate" class="btn-contact">
                📨 Send Message
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Scans -->
      <div class="card recent-scans" v-if="recentScans.length > 0">
        <h2>🕐 Recent Scans</h2>
        <div class="scans-list">
          <div v-for="scan in recentScans" :key="scan.id" class="scan-item"
            @click="loadScan(scan)">
            <div class="scan-avatar">{{ scan.initials }}</div>
            <div class="scan-info">
              <h4>{{ scan.full_name }}</h4>
              <p>OKU: {{ scan.oku_number }}</p>
            </div>
            <span class="scan-time">{{ scan.time }}</span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'
import jsQR from 'jsqr'

export default {
  name: 'ScanQR',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      scanMode: 'camera',
      searchInput: '',
      portfolio: null,
      recentScans: [],
      loading: false,
      error: '',
      cameraActive: false,
      scanStatus: null,
      animationFrame: null,
      stream: null
    }
  },
  computed: {
    initials() {
      if (!this.portfolio?.full_name) return '?'
      return this.portfolio.full_name
        .split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }
  },
  mounted() {
    const token = localStorage.getItem('token')
    if (!token) { this.$router.push('/login'); return }
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  },
  beforeUnmount() {
    this.stopCamera()
  },
  methods: {
    switchMode(mode) {
      this.scanMode = mode
      this.error = ''
      this.scanStatus = null
      if (mode === 'manual') this.stopCamera()
    },
    async startCamera() {
      this.error = ''
      this.scanStatus = null
      try {
        this.stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'environment' }
        })
        const video = this.$refs.videoRef
        video.srcObject = this.stream
        this.cameraActive = true
        this.scanStatus = { type: 'scanning', message: '🔍 Scanning for QR code...' }
        this.scanLoop()
      } catch (err) {
        this.error = 'Camera access denied. Please allow camera permission and try again.'
      }
    },
    stopCamera() {
      if (this.stream) {
        this.stream.getTracks().forEach(track => track.stop())
        this.stream = null
      }
      if (this.animationFrame) {
        cancelAnimationFrame(this.animationFrame)
        this.animationFrame = null
      }
      this.cameraActive = false
      this.scanStatus = null
    },
    scanLoop() {
      const video = this.$refs.videoRef
      const canvas = this.$refs.canvasRef
      if (!video || !canvas || !this.cameraActive) return
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth
        canvas.height = video.videoHeight
        const ctx = canvas.getContext('2d')
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height)
        const code = jsQR(imageData.data, imageData.width, imageData.height)
        if (code) {
          this.stopCamera()
          this.scanStatus = { type: 'success', message: '✅ QR Code detected!' }
          this.processQRData(code.data)
          return
        }
      }
      this.animationFrame = requestAnimationFrame(this.scanLoop)
    },
    async processQRData(qrData) {
    // Extract portfolio ID from URL
    // URL format: http://localhost:8000/api/portfolio/1
    const match = qrData.match(/\/portfolio\/(\d+)/)
    if (match) {
    this.searchInput = match[1]
    this.scanMode = 'manual'
    await this.searchPortfolio()
    } else {
    // Try using the full URL directly
    this.error = 'Invalid QR code. Not an AbilityScan portfolio QR.'
    }
    },
    async searchPortfolio() {
      if (!this.searchInput) {
        this.error = 'Please enter an OKU Number or Portfolio ID'
        return
      }
      this.loading = true
      this.error = ''
      this.portfolio = null
      try {
        const response = await axios.get('/api/portfolio/search', {
          params: { q: this.searchInput }
        })
        this.portfolio = response.data.profile
        const scan = {
          id: this.portfolio.id,
          full_name: this.portfolio.full_name,
          oku_number: this.portfolio.oku_number,
          initials: this.initials,
          time: new Date().toLocaleTimeString()
        }
        this.recentScans = [scan, ...this.recentScans].slice(0, 5)
      } catch (err) {
        this.error = 'Portfolio not found. Please check and try again.'
      }
      this.loading = false
    },
    loadScan(scan) {
      this.searchInput = scan.oku_number
      this.scanMode = 'manual'
      this.searchPortfolio()
    },
    contactCandidate() {
      if (this.portfolio) {
        this.$router.push({
          path: '/employer/mailbox',
          query: {
            compose: true,
            receiver_id: this.portfolio.user_id,
            receiver_name: this.portfolio.full_name
          }
        })
      }
    }
  }
}
</script>

<style scoped>
.dashboard { display: flex; min-height: 100vh; background: var(--bg); }



.main-content { margin-left: 240px; flex: 1; padding: 32px; }
.topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.topbar h1 { font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.topbar p { font-size: 14px; color: var(--text-muted); }
.theme-toggle { padding: 8px 16px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.theme-toggle:hover { border-color: var(--primary); color: var(--primary); }

.content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }

.card { background: var(--surface); border-radius: 16px; padding: 24px; border: 1px solid var(--border); box-shadow: var(--card-shadow); }
.card h2 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
.section-desc { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }

/* Scan Tabs */
.scan-tabs { display: flex; gap: 8px; margin-bottom: 20px; }
.scan-tab { flex: 1; padding: 10px; border: 2px solid var(--border); border-radius: 8px; background: var(--bg); color: var(--text-muted); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.scan-tab.active { border-color: var(--primary); background: var(--primary); color: white; }

/* Camera */
.camera-section { display: flex; flex-direction: column; gap: 16px; }
.camera-container { position: relative; width: 100%; aspect-ratio: 4/3; background: #000; border-radius: 12px; overflow: hidden; }
.camera-feed { width: 100%; height: 100%; object-fit: cover; }
.camera-canvas { display: none; }
.camera-placeholder { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; color: #666; }
.camera-placeholder span { font-size: 48px; }
.camera-placeholder p { font-size: 14px; }
.scan-overlay { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; }
.scan-frame { width: 200px; height: 200px; border: 3px solid #00ff00; border-radius: 12px; box-shadow: 0 0 0 9999px rgba(0,0,0,0.4); animation: pulse 1.5s ease-in-out infinite; }
@keyframes pulse { 0%, 100% { border-color: #00ff00; } 50% { border-color: #00cc00; opacity: 0.7; } }
.scan-hint { color: white; font-size: 13px; font-weight: 600; background: rgba(0,0,0,0.6); padding: 6px 16px; border-radius: 20px; }
.camera-actions { display: flex; gap: 8px; }
.btn-start-camera { flex: 1; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-start-camera:hover { background: var(--primary-hover); }
.btn-stop-camera { flex: 1; padding: 12px; background: #ef4444; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
.scan-status { padding: 10px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; text-align: center; }
.scan-status.scanning { background: #fef9c3; color: #854d0e; }
.scan-status.success { background: #f0fdf4; color: #166534; }
.scan-status.error { background: #fef2f2; color: #991b1b; }

/* Manual */
.manual-section { display: flex; flex-direction: column; gap: 16px; }
.scanner-box { background: var(--bg); border: 2px dashed var(--border); border-radius: 12px; padding: 24px; text-align: center; }
.scanner-icon { font-size: 40px; margin-bottom: 8px; }
.scanner-text { font-size: 14px; color: var(--text-muted); }
.form-group { margin-bottom: 0; }
.form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
.input-row { display: flex; gap: 8px; }
.form-input { flex: 1; padding: 10px 14px; border: 2px solid var(--border); border-radius: 8px; font-size: 14px; color: var(--text); background: var(--input-bg); outline: none; transition: border-color 0.2s; }
.form-input:focus { border-color: var(--primary); }
.btn-search { padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; white-space: nowrap; }
.btn-search:disabled { opacity: 0.6; cursor: not-allowed; }
.input-hint { font-size: 12px; color: var(--text-muted); margin-top: 6px; }

/* Empty Result */
.empty-result { text-align: center; padding: 40px 20px; color: var(--text-muted); }
.empty-result span { font-size: 48px; display: block; margin-bottom: 12px; }
.empty-result p { font-size: 15px; margin-bottom: 4px; }
.empty-result .hint { font-size: 13px; }

/* Profile Result */
.profile-result { display: flex; flex-direction: column; gap: 12px; }
.profile-header { display: flex; align-items: center; gap: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border); }
.profile-avatar { width: 56px; height: 56px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; flex-shrink: 0; }
.profile-header h3 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.profile-header p { font-size: 13px; color: var(--text-muted); margin-bottom: 6px; }
.profile-status { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
.profile-status.pending { background: #fef9c3; color: #854d0e; }
.profile-status.approved { background: #f0fdf4; color: #166534; }
.profile-status.rejected { background: #fef2f2; color: #991b1b; }

.profile-section { display: flex; flex-direction: column; gap: 4px; }
.profile-section h4 { font-size: 12px; font-weight: 700; margin-bottom: 2px; }

/* Rule Labels */
.s-label { color: #1d4ed8; }
.p-label { color: #166534; }
.t-label { color: #7e22ce; }
.w-label { color: #c2410c; }
.c-label { color: #854d0e; }

.tags { display: flex; flex-wrap: wrap; gap: 4px; }

/* Rule Tags */
.tag-skill { padding: 2px 8px; background: #eff6ff; color: #1d4ed8; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-physical { padding: 2px 8px; background: #f0fdf4; color: #166534; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-tech { padding: 2px 8px; background: #faf5ff; color: #7e22ce; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-work { padding: 2px 8px; background: #fff7ed; color: #c2410c; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tag-sensory { padding: 2px 8px; background: #fef9c3; color: #854d0e; border-radius: 20px; font-size: 11px; font-weight: 600; }
.no-data { font-size: 12px; color: var(--text-muted); font-style: italic; }

.attachments { display: flex; flex-direction: column; gap: 8px; }
.attachment-link { display: inline-block; padding: 8px 16px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 600; transition: all 0.2s; }
.attachment-link:hover { border-color: var(--primary); background: var(--primary-light); }

.profile-actions { margin-top: 4px; }
.btn-contact { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.btn-contact:hover { background: var(--primary-hover); }

/* Recent Scans */
.recent-scans { margin-top: 0; }
.recent-scans h2 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 16px; }
.scans-list { display: flex; flex-direction: column; gap: 8px; }
.scan-item { display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg); border-radius: 10px; border: 1px solid var(--border); cursor: pointer; transition: all 0.2s; }
.scan-item:hover { border-color: var(--primary); }
.scan-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0; }
.scan-info { flex: 1; }
.scan-info h4 { font-size: 14px; font-weight: 600; color: var(--text); }
.scan-info p { font-size: 12px; color: var(--text-muted); }
.scan-time { font-size: 12px; color: var(--text-muted); }

.error-msg { color: #ef4444; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 16px; font-size: 13px; margin-top: 12px; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .content-grid { grid-template-columns: 1fr; }
}
</style>