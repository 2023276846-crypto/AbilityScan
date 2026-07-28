<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <EmployerSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); flex-shrink: 0;"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="15" y1="22" x2="15" y2="16"/><path d="M9 16h6"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M8 10h.01"/><path d="M16 10h.01"/></svg>
            Company Profile
          </h1>
          <p>Manage your company information</p>
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

      <div class="content-grid">
        <!-- Left: Company Info -->
        <div class="card">
          <h2 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Company Information
          </h2>

          <!-- Company Logo Upload -->
          <div class="logo-upload-container">
            <div class="logo-preview-wrapper" @click="$refs.logoInput.click()">
              <img v-if="logoPreview" :src="logoPreview" class="company-logo-preview" />
              <img v-else-if="form.logo_path" :src="'/storage/' + form.logo_path" class="company-logo-preview" />
              <div v-else class="company-logo-placeholder">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                <span>Upload Logo</span>
              </div>
              <div class="logo-hover-overlay">
                <span>Change Logo</span>
              </div>
            </div>
            <input type="file" ref="logoInput" @change="handleLogoUpload" accept="image/*" style="display: none;" />
            <div class="logo-upload-hint">Click logo to upload/change image</div>
          </div>

          <div class="form-group">
            <label>Person In Charge Name</label>
            <input v-model="form.name" type="text"
              placeholder="Enter person in charge name" class="form-input"/>
          </div>

          <div class="form-group">
            <label>Company Name</label>
            <input v-model="form.company_name" type="text"
              placeholder="Enter company name" class="form-input"/>
          </div>

          <div class="form-group">
            <label>Company Email</label>
            <input v-model="form.company_email" type="email"
              placeholder="Enter company email" class="form-input"/>
          </div>

          <div class="form-group">
            <label>Contact Number</label>
            <input v-model="form.contact_number" type="text"
              placeholder="Enter contact number" class="form-input"/>
          </div>

          <div class="form-group">
            <label>SSM Number</label>
            <input v-model="form.ssm_number" type="text"
              placeholder="Enter SSM number" class="form-input"/>
          </div>

          <div class="form-group">
            <label>Industry Type</label>
            <select v-model="form.industry_type" class="form-input">
              <option value="">-- Select Industry --</option>
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

          <div class="form-group">
            <label>Company Address</label>
            <textarea v-model="form.company_address"
              placeholder="Enter company address"
              class="form-input textarea"></textarea>
          </div>

          <div class="form-group">
            <label>About Us</label>
            <textarea v-model="form.about_us" rows="4"
              placeholder="Tell candidates about your company history, culture, and core mission..."
              class="form-input textarea-input"></textarea>
          </div>

          <div class="form-group">
            <label>Account Status</label>
            <div :class="['status-badge', form.status]" style="display: inline-flex; align-items: center; gap: 6px;">
              <svg v-if="form.status === 'pending'" class="animate-spin" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <svg v-else-if="form.status === 'approved'" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <svg v-else viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              <span>
                {{ form.status === 'pending' ? 'Pending Approval' :
                   form.status === 'approved' ? 'Approved' : 'Rejected' }}
              </span>
            </div>
          </div>

          <!-- Company Stats -->
          <div class="stats-section">
            <h2 style="display: flex; align-items: center; gap: 8px;">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
              Company Stats
            </h2>
            <div class="stats-grid">
              <div class="stat-item">
                <span class="stat-num">{{ stats.totalJobs }}</span>
                <span class="stat-label">Jobs Posted</span>
              </div>
              <div class="stat-item">
                <span class="stat-num">{{ stats.openJobs }}</span>
                <span class="stat-label">Open Jobs</span>
              </div>
              <div class="stat-item">
                <span class="stat-num">{{ stats.totalApplications }}</span>
                <span class="stat-label">Applications</span>
              </div>
              <div class="stat-item">
                <span class="stat-num">{{ stats.accepted }}</span>
                <span class="stat-label">Accepted</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: 5-Rule Accessibility -->
        <div class="card">

          <!-- P-Rule: Physical Facilities -->
          <div class="rule-section">
            <div class="rule-header p-rule">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #166534; flex-shrink: 0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              <div>
                <h3>P-Rule: Physical Facilities</h3>
                <p>Physical building accommodations available (Weight: 25%)</p>
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
                <p>Digital assistive tools available (Weight: 20%)</p>
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
                <p>Sensory and communication support (Weight: 10%)</p>
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
        <button @click="saveProfile" class="btn-save" :disabled="loading" style="display: flex; align-items: center; gap: 8px;">
          <svg v-if="!loading" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          {{ loading ? 'Saving...' : 'Save Profile' }}
        </button>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'CompanyProfile',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      form: {
        name: '',
        company_name: '',
        company_email: '',
        contact_number: '',
        ssm_number: '',
        industry_type: '',
        company_address: '',
        accessibility: [],
        physical_facilities: [],
        tech_support: [],
        work_arrangement: [],
        sensory_support: [],
        status: 'pending',
        logo_path: '',
        logo: null,
        about_us: ''
      },
      logoPreview: null,
      stats: {
        totalJobs: 0,
        openJobs: 0,
        totalApplications: 0,
        accepted: 0
      },
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
    this.loadProfile()
  },
  methods: {
    async loadProfile() {
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/employer/profile')
        if (response.data.profile) {
          const p = response.data.profile
          this.form.company_name = p.company_name || ''
          this.form.company_email = p.company_email || ''
          this.form.contact_number = p.contact_number || ''
          this.form.ssm_number = p.ssm_number || ''
          this.form.industry_type = p.industry_type || ''
          this.form.company_address = p.company_address || ''
          this.form.status = p.status || 'pending'
          this.form.logo_path = p.logo_path || ''
          this.form.about_us = p.about_us || ''

          // Load existing accessibility into 4 categories
          const allAcc = p.accessibility || []
          this.form.physical_facilities = allAcc.filter(i =>
            this.physicalOptions.includes(i))
          this.form.tech_support = allAcc.filter(i =>
            this.techOptions.includes(i))
          this.form.work_arrangement = allAcc.filter(i =>
            this.workOptions.includes(i))
          this.form.sensory_support = allAcc.filter(i =>
            this.sensoryOptions.includes(i))
        }
        if (response.data.user) {
          this.form.name = response.data.user.name
        }
        if (response.data.stats) {
          this.stats = response.data.stats
        }
      } catch (err) {
        console.log('Profile not loaded')
      }
    },
    handleLogoUpload(event) {
      const file = event.target.files[0]
      this.form.logo = file
      if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
          this.logoPreview = e.target.result
        }
        reader.readAsDataURL(file)
      }
    },
    compressImage(file, maxWidth = 1000, maxHeight = 1000, quality = 0.8) {
      return new Promise((resolve) => {
        if (!file || !file.type.startsWith('image/')) {
          resolve(file);
          return;
        }
        const reader = new FileReader();
        reader.onload = (event) => {
          const img = new Image();
          img.onload = () => {
            const canvas = document.createElement('canvas');
            let width = img.width;
            let height = img.height;

            if (width > height) {
              if (width > maxWidth) {
                height = Math.round((height * maxWidth) / width);
                width = maxWidth;
              }
            } else {
              if (height > maxHeight) {
                width = Math.round((width * maxHeight) / height);
                height = maxHeight;
              }
            }

            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            canvas.toBlob((blob) => {
              if (blob) {
                const compressedFile = new File([blob], file.name, {
                  type: 'image/jpeg',
                  lastModified: Date.now()
                });
                resolve(compressedFile);
              } else {
                resolve(file);
              }
            }, 'image/jpeg', quality);
          };
          img.onerror = () => resolve(file);
          img.src = event.target.result;
        };
        reader.onerror = () => resolve(file);
        reader.readAsDataURL(file);
      });
    },
    async saveProfile() {
      this.loading = true
      this.success = ''
      this.error = ''
      try {
        // Combine all 4 rules into accessibility array
        const combinedAccessibility = [
          ...this.form.physical_facilities,
          ...this.form.tech_support,
          ...this.form.work_arrangement,
          ...this.form.sensory_support
        ]

        const formData = new FormData()
        formData.append('name', this.form.name)
        formData.append('company_name', this.form.company_name)
        formData.append('company_email', this.form.company_email)
        formData.append('contact_number', this.form.contact_number)
        formData.append('ssm_number', this.form.ssm_number)
        formData.append('industry_type', this.form.industry_type)
        formData.append('company_address', this.form.company_address)
        formData.append('accessibility', JSON.stringify(combinedAccessibility))
        formData.append('about_us', this.form.about_us || '')

        let logoFile = this.form.logo;
        if (logoFile) {
          logoFile = await this.compressImage(logoFile);
        }

        if (logoFile) {
          formData.append('logo', logoFile)
        }

        await axios.post('/api/employer/profile', formData)
        this.success = 'Profile saved successfully!'
        this.logoPreview = null
        this.loadProfile()
      } catch (err) {
        this.error = 'Failed to save profile. Please try again.'
      }
      this.loading = false
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

.content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }

.card { background: var(--surface); border-radius: 16px; padding: 24px; border: 1px solid var(--border); box-shadow: var(--card-shadow); height: fit-content; }
.card h2 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 16px; }

.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; }

.form-input { width: 100%; padding: 10px 14px; border: 2px solid var(--border); border-radius: 8px; font-size: 14px; color: var(--text); background: var(--input-bg); outline: none; transition: border-color 0.2s; font-family: 'Segoe UI', sans-serif; }
.form-input:focus { border-color: var(--primary); }
.textarea { resize: vertical; min-height: 100px; }

.status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; }
.status-badge.pending { background: #fef9c3; color: #854d0e; }
.status-badge.approved { background: #f0fdf4; color: #166534; }
.status-badge.rejected { background: #fef2f2; color: #991b1b; }

/* Stats */
.stats-section { margin-top: 24px; }
.stats-section h2 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 16px; }
.stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.stat-item { background: var(--bg); border-radius: 12px; padding: 16px; text-align: center; border: 1px solid var(--border); }
.stat-num { display: block; font-size: 24px; font-weight: 700; color: var(--primary); margin-bottom: 4px; }
.stat-label { font-size: 12px; color: var(--text-muted); }

/* Rule Sections */
.rule-section { margin-bottom: 24px; }

.rule-header { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; margin-bottom: 12px; }
.rule-header span { font-size: 24px; flex-shrink: 0; }
.rule-header h3 { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 2px; }
.rule-header p { font-size: 12px; color: var(--text-muted); }

.p-rule { background: #f0fdf4; border: 1px solid #bbf7d0; }
.t-rule { background: #faf5ff; border: 1px solid #e9d5ff; }
.w-rule { background: #fff7ed; border: 1px solid #fed7aa; }
.c-rule { background: #fef9c3; border: 1px solid #fde047; }

.checkbox-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }

.checkbox-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text); cursor: pointer; padding: 6px 10px; border-radius: 6px; background: var(--bg); border: 1px solid var(--border); transition: all 0.2s; }
.checkbox-item:hover { border-color: var(--primary); color: var(--primary); }
.checkbox-item input { accent-color: var(--primary); }

.success-msg { color: #10b981; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 16px; font-size: 13px; margin-bottom: 16px; }
.error-msg { color: #ef4444; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 16px; font-size: 13px; margin-bottom: 16px; }

.form-actions { display: flex; justify-content: flex-end; }
.btn-save { padding: 12px 32px; background: var(--primary); color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.btn-save:hover { background: var(--primary-hover); }
.btn-save:disabled { opacity: 0.6; cursor: not-allowed; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .content-grid { grid-template-columns: 1fr; }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
.animate-spin {
  animation: spin 1s linear infinite;
}

/* Company Logo upload styles */
.logo-upload-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 24px;
  gap: 8px;
}
.logo-preview-wrapper {
  width: 120px;
  height: 120px;
  border-radius: 12px;
  border: 2px dashed var(--border);
  overflow: hidden;
  position: relative;
  cursor: pointer;
  background: var(--bg);
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}
.logo-preview-wrapper:hover {
  border-color: var(--primary);
}
.company-logo-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.company-logo-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: var(--text-muted);
  font-weight: 600;
}
.company-logo-placeholder svg {
  color: var(--text-muted);
}
.logo-hover-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  opacity: 0;
  transition: opacity 0.2s;
}
.logo-preview-wrapper:hover .logo-hover-overlay {
  opacity: 1;
}
.logo-upload-hint {
  font-size: 11px;
  color: var(--text-muted);
}
.textarea-input {
  resize: vertical;
}
</style>