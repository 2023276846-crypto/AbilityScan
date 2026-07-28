<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <PWDSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1>My Profile</h1>
          <p>Manage your personal information and accessibility needs</p>
        </div>
        <button class="theme-toggle" @click="$emit('toggleTheme')">
          {{ isDark ? '☀️ Light' : '🌙 Dark' }}
        </button>
      </div>

      <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
          <!-- Left: Personal Info -->
          <div class="card">
            <h2>Personal Information</h2>

            <!-- Profile Picture Upload -->
            <div class="avatar-upload-section">
              <div class="avatar-preview-container">
                <img v-if="avatarPreview" :src="avatarPreview" class="avatar-preview-img" />
                <img v-else-if="form.avatar_path" :src="'/storage/' + form.avatar_path" class="avatar-preview-img" />
                <div v-else class="avatar-placeholder-text">
                  <span>👤</span>
                </div>
              </div>
              <div class="avatar-upload-controls">
                <label class="btn-upload-avatar">
                  Choose Photo
                  <input type="file" @change="handleProfilePicture" accept="image/*" style="display: none;" />
                </label>
                <p class="file-hint">JPG or PNG (circular crop)</p>
              </div>
            </div>

            <div class="form-group">
              <label>Full Name</label>
              <input v-model="form.full_name" type="text"
                placeholder="Enter full name" class="form-input"/>
            </div>

            <div class="form-group">
              <label>OKU Number</label>
              <input v-model="form.oku_number" type="text"
                placeholder="Enter OKU number" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input v-model="form.email" type="email"
                placeholder="Enter email" class="form-input" disabled/>
            </div>

            <div class="form-group">
              <label>Location (City, State)</label>
              <input v-model="form.location" type="text"
                placeholder="e.g. Kuala Lumpur, Wilayah Persekutuan" class="form-input"/>
            </div>

            <div class="form-group">
              <label>About Me</label>
              <textarea v-model="form.about_me" rows="3"
                placeholder="Write a brief professional summary about yourself..." class="form-input textarea-input"></textarea>
            </div>

            <div class="form-group">
              <label>Upload OKU Card</label>
              <input type="file" @change="handleFile"
                accept="image/*,.pdf" class="form-input file-input"/>
              <div v-if="form.oku_card_path" class="file-status">
                OKU Card uploaded
                <a :href="'/storage/' + form.oku_card_path" target="_blank" class="view-link">View</a>
              </div>
            </div>

            <!-- Certificates Section -->
            <div class="form-group attachments-manager">
              <label>Manage Certificates</label>
              
              <!-- Existing saved certificates -->
              <div v-if="form.certificates && form.certificates.length" class="saved-attachments">
                <div v-for="(cert, idx) in form.certificates" :key="idx" class="attachment-item-saved">
                  <span class="attachment-name">📄 {{ cert.name || 'Certificate ' + (idx + 1) }}</span>
                  <div class="attachment-actions">
                    <a :href="'/storage/' + cert.path" target="_blank" class="view-link-btn">View</a>
                    <button type="button" @click="deleteExistingCertificate(idx)" class="delete-attachment-btn">✕ Delete</button>
                  </div>
                </div>
              </div>
              <div v-else class="no-attachments-text">No certificates uploaded yet.</div>

              <!-- Upload input for new certificates -->
              <label class="custom-file-upload-label">
                + Upload New Certificate(s)
                <input type="file" @change="handleNewCertificates" multiple
                  accept=".pdf,.jpg,.jpeg,.png" class="hidden-file-input"/>
              </label>

              <!-- Queue of new certificates -->
              <div v-if="newCertificatesQueue.length" class="upload-queue">
                <p class="queue-title">New certificates to upload ({{ newCertificatesQueue.length }}):</p>
                <div v-for="(file, idx) in newCertificatesQueue" :key="idx" class="queue-item">
                  <span>📎 {{ file.name }}</span>
                  <button type="button" @click="removeFromCertQueue(idx)" class="remove-queue-btn">✕</button>
                </div>
              </div>
            </div>

            <!-- Skills Videos Section -->
            <div class="form-group attachments-manager">
              <label>Manage Skills Videos</label>

              <!-- Existing saved videos -->
              <div v-if="form.videos && form.videos.length" class="saved-attachments">
                <div v-for="(vid, idx) in form.videos" :key="idx" class="attachment-item-saved">
                  <span class="attachment-name">🎥 {{ vid.name || 'Skills Video ' + (idx + 1) }}</span>
                  <div class="attachment-actions">
                    <a :href="'/storage/' + vid.path" target="_blank" class="view-link-btn">View</a>
                    <button type="button" @click="deleteExistingVideo(idx)" class="delete-attachment-btn">✕ Delete</button>
                  </div>
                </div>
              </div>
              <div v-else class="no-attachments-text">No skills videos uploaded yet.</div>

              <!-- Upload input for new videos -->
              <label class="custom-file-upload-label">
                + Upload New Skills Video(s)
                <input type="file" @change="handleNewVideos" multiple
                  accept="video/mp4,video/avi,video/mov" class="hidden-file-input"/>
              </label>

              <!-- Queue of new videos -->
              <div v-if="newVideosQueue.length" class="upload-queue">
                <p class="queue-title">New videos to upload ({{ newVideosQueue.length }}):</p>
                <div v-for="(file, idx) in newVideosQueue" :key="idx" class="queue-item">
                  <span>📎 {{ file.name }}</span>
                  <button type="button" @click="removeFromVidQueue(idx)" class="remove-queue-btn">✕</button>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label>Account Status</label>
              <div :class="['status-badge', form.status]">
                {{ form.status === 'pending' ? 'Pending Approval' :
                   form.status === 'approved' ? 'Approved' : 'Rejected' }}
              </div>
            </div>
          </div>

          <!-- Left Second Card: Career & Education Details -->
          <div class="card margin-top-card">
            <h2>Career & Education Details</h2>

            <div class="form-group">
              <label>Work Experience</label>
              <textarea v-model="form.experience" rows="4"
                placeholder="List your previous job roles, tasks, and years of experience..." class="form-input textarea-input"></textarea>
            </div>

            <!-- Dynamic Education Builder -->
            <div class="education-builder">
              <div class="section-title-row">
                <label>Education History (Primary School until latest)</label>
                <button type="button" @click="addEducation" class="btn-add-edu">+ Add Education</button>
              </div>

              <div v-if="!form.education || form.education.length === 0" class="empty-edu">
                No education history added yet. Click "+ Add Education" to add.
              </div>

              <div v-else class="edu-items-list">
                <div v-for="(edu, index) in form.education" :key="index" class="edu-item-form">
                  <div class="edu-item-header">
                    <span>Education / School #{{ index + 1 }}</span>
                    <button type="button" @click="removeEducation(index)" class="btn-remove-edu">✕ Remove</button>
                  </div>
                  <div class="edu-grid">
                    <div class="form-group">
                      <label>Level</label>
                      <select v-model="edu.level" class="form-input">
                        <option value="" disabled>Select level</option>
                        <option value="Primary School">Primary School</option>
                        <option value="Secondary School">Secondary School</option>
                        <option value="High School">High School</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Bachelor's Degree">Bachelor's Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="Other Certification">Other Certificate</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>School / Institution Name</label>
                      <input v-model="edu.school_name" type="text" placeholder="e.g. SK Subang Jaya" class="form-input" />
                    </div>
                    <div class="form-group">
                      <label>Duration / Years</label>
                      <input v-model="edu.years" type="text" placeholder="e.g. 2012 - 2017" class="form-input" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Skills & Rules -->
        <div class="card">

          <!-- S-Rule: Skills -->
          <div class="rule-section">
            <div class="rule-header s-rule">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #1e40af; flex-shrink: 0;"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
              <div>
                <h3>S-Rule: Skills</h3>
                <p>Professional skills you have (Weight: 30%)</p>
              </div>
            </div>
            <div class="checkbox-grid">
              <label class="checkbox-item" v-for="skill in skillOptions" :key="skill">
                <input type="checkbox" :value="skill" v-model="form.skills"/>
                {{ skill }}
              </label>
            </div>
          </div>

          <!-- P-Rule: Physical Needs -->
          <div class="rule-section">
            <div class="rule-header p-rule">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #166534; flex-shrink: 0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              <div>
                <h3>P-Rule: Physical Accessibility Needs</h3>
                <p>Physical building accommodations you need (Weight: 25%)</p>
              </div>
            </div>
            <div class="checkbox-grid">
              <label class="checkbox-item" v-for="item in physicalOptions" :key="item">
                <input type="checkbox" :value="item" v-model="form.physical_needs"/>
                {{ item }}
              </label>
            </div>
          </div>

          <!-- T-Rule: Tech Needs -->
          <div class="rule-section">
            <div class="rule-header t-rule">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #7e22ce; flex-shrink: 0;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              <div>
                <h3>T-Rule: Technology Support Needs</h3>
                <p>Digital assistive tools you need (Weight: 20%)</p>
              </div>
            </div>
            <div class="checkbox-grid">
              <label class="checkbox-item" v-for="item in techOptions" :key="item">
                <input type="checkbox" :value="item" v-model="form.tech_needs"/>
                {{ item }}
              </label>
            </div>
          </div>

          <!-- W-Rule: Work Arrangement -->
          <div class="rule-section">
            <div class="rule-header w-rule">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #c2410c; flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <div>
                <h3>W-Rule: Work Arrangement Needs</h3>
                <p>Work flexibility requirements (Weight: 15%)</p>
              </div>
            </div>
            <div class="checkbox-grid">
              <label class="checkbox-item" v-for="item in workOptions" :key="item">
                <input type="checkbox" :value="item" v-model="form.work_arrangement"/>
                {{ item }}
              </label>
            </div>
          </div>

          <!-- C-Rule: Sensory Needs -->
          <div class="rule-section">
            <div class="rule-header c-rule">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #854d0e; flex-shrink: 0;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              <div>
                <h3>C-Rule: Communication & Sensory Needs</h3>
                <p>Communication support you need (Weight: 10%)</p>
              </div>
            </div>
            <div class="checkbox-grid">
              <label class="checkbox-item" v-for="item in sensoryOptions" :key="item">
                <input type="checkbox" :value="item" v-model="form.sensory_needs"/>
                {{ item }}
              </label>
            </div>
          </div>

        </div>
      </div>

      <p v-if="success" class="success-msg">{{ success }}</p>
      <p v-if="error" class="error-msg">{{ error }}</p>

      <div class="form-actions">
        <button @click="saveProfile" class="btn-save" :disabled="loading">
          {{ loading ? 'Saving...' : 'Save Profile' }}
        </button>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ManageProfile',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      form: {
        full_name: '',
        oku_number: '',
        email: '',
        oku_card_path: '',
        oku_card: null,
        certificate_path: '',
        certificate: null,
        video_path: '',
        video: null,
        skills: [],
        physical_needs: [],
        tech_needs: [],
        work_arrangement: [],
        sensory_needs: [],
        accessibility_needs: [],
        status: 'pending',
        avatar_path: '',
        profile_picture: null,
        location: '',
        about_me: '',
        experience: '',
        education: [],
        certificates: [],
        videos: []
      },
      avatarPreview: null,
      newCertificatesQueue: [],
      newVideosQueue: [],
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
    this.loadProfile()
  },
  methods: {
    async loadProfile() {
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/pwd/profile')
        if (response.data.profile) {
          const p = response.data.profile
          this.form.full_name = p.full_name || ''
          this.form.oku_number = p.oku_number || ''
          this.form.oku_card_path = p.oku_card_path || ''
          this.form.certificate_path = p.certificate_path || ''
          this.form.video_path = p.video_path || ''
          this.form.skills = p.skills || []
          this.form.physical_needs = p.physical_needs || []
          this.form.tech_needs = p.tech_needs || []
          this.form.work_arrangement = p.work_arrangement || []
          this.form.sensory_needs = p.sensory_needs || []
          this.form.accessibility_needs = p.accessibility_needs || []
          this.form.status = p.status || 'pending'
          this.form.avatar_path = p.avatar_path || ''
          this.form.location = p.location || ''
          this.form.about_me = p.about_me || ''
          this.form.experience = p.experience || ''
          this.form.education = p.education || []
          this.form.certificates = p.certificates || []
          this.form.videos = p.videos || []
        }
        if (response.data.user) {
          this.form.email = response.data.user.email
        }
      } catch (err) {
        console.log('Profile not loaded yet')
      }
    },
    handleFile(event) {
      this.form.oku_card = event.target.files[0]
    },
    handleNewCertificates(event) {
      const files = Array.from(event.target.files)
      this.newCertificatesQueue.push(...files)
    },
    removeFromCertQueue(index) {
      this.newCertificatesQueue.splice(index, 1)
    },
    deleteExistingCertificate(index) {
      this.form.certificates.splice(index, 1)
    },
    handleNewVideos(event) {
      const files = Array.from(event.target.files)
      this.newVideosQueue.push(...files)
    },
    removeFromVidQueue(index) {
      this.newVideosQueue.splice(index, 1)
    },
    deleteExistingVideo(index) {
      this.form.videos.splice(index, 1)
    },
    handleProfilePicture(event) {
      const file = event.target.files[0]
      this.form.profile_picture = file
      if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
          this.avatarPreview = e.target.result
        }
        reader.readAsDataURL(file)
      }
    },
    addEducation() {
      if (!this.form.education) {
        this.form.education = []
      }
      this.form.education.push({
        level: '',
        school_name: '',
        years: ''
      })
    },
    removeEducation(index) {
      this.form.education.splice(index, 1)
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
        const formData = new FormData()
        formData.append('full_name', this.form.full_name)
        formData.append('oku_number', this.form.oku_number)
        formData.append('skills', JSON.stringify(this.form.skills))
        formData.append('physical_needs', JSON.stringify(this.form.physical_needs))
        formData.append('tech_needs', JSON.stringify(this.form.tech_needs))
        formData.append('work_arrangement', JSON.stringify(this.form.work_arrangement))
        formData.append('sensory_needs', JSON.stringify(this.form.sensory_needs))
        formData.append('accessibility_needs', JSON.stringify([
          ...this.form.physical_needs,
          ...this.form.tech_needs,
          ...this.form.work_arrangement,
          ...this.form.sensory_needs
        ]))
        formData.append('location', this.form.location || '')
        formData.append('about_me', this.form.about_me || '')
        formData.append('experience', this.form.experience || '')
        formData.append('education', JSON.stringify(this.form.education || []))
        
        formData.append('existing_certificates', JSON.stringify(this.form.certificates || []))
        formData.append('existing_videos', JSON.stringify(this.form.videos || []))

        // Append new files
        this.newCertificatesQueue.forEach((file) => {
          formData.append('new_certificates[]', file)
        })
        this.newVideosQueue.forEach((file) => {
          formData.append('new_videos[]', file)
        })

        // Compress and append profile files if present
        let okuCardFile = this.form.oku_card;
        let profilePictureFile = this.form.profile_picture;

        if (okuCardFile) {
          okuCardFile = await this.compressImage(okuCardFile);
        }
        if (profilePictureFile) {
          profilePictureFile = await this.compressImage(profilePictureFile);
        }

        if (okuCardFile) formData.append('oku_card', okuCardFile)
        if (profilePictureFile) formData.append('profile_picture', profilePictureFile)

        await axios.post('/api/pwd/profile', formData)
        this.success = 'Profile saved successfully!'
        this.newCertificatesQueue = []
        this.newVideosQueue = []
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

.card h2 {
  font-size: 16px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 16px;
}

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
}

.form-input:focus { border-color: var(--primary); }
.form-input:disabled { opacity: 0.6; cursor: not-allowed; }
.file-input { padding: 8px; cursor: pointer; }

.file-hint { font-size: 11px; color: var(--text-muted); margin-top: 4px; }

.file-status {
  font-size: 12px;
  color: #10b981;
  margin-top: 4px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.view-link { color: var(--primary); text-decoration: underline; font-size: 12px; }

.status-badge {
  display: inline-block;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
}

.status-badge.pending { background: #fef9c3; color: #854d0e; }
.status-badge.approved { background: #f0fdf4; color: #166534; }
.status-badge.rejected { background: #fef2f2; color: #991b1b; }

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

.form-actions { display: flex; justify-content: flex-end; }

.btn-save {
  padding: 12px 32px;
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

/* Avatar Upload styling */
.avatar-upload-section {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 1px dashed var(--border);
}
.avatar-preview-container {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  border: 3px solid var(--border);
  overflow: hidden;
  background: var(--bg);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
.avatar-preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.avatar-placeholder-text {
  font-size: 36px;
  color: var(--text-muted);
}
.avatar-upload-controls {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.btn-upload-avatar {
  background: var(--primary);
  color: white;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
  width: max-content;
}
.btn-upload-avatar:hover {
  background: var(--primary-hover);
}

.textarea-input {
  resize: vertical;
  min-height: 100px;
}

.margin-top-card {
  margin-top: 24px;
}

.left-column {
  display: flex;
  flex-direction: column;
}

/* Education Builder styles */
.education-builder {
  margin-top: 24px;
  border-top: 1px solid var(--border);
  padding-top: 20px;
}
.section-title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.section-title-row label {
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
  margin-bottom: 0 !important;
}
.btn-add-edu {
  padding: 6px 12px;
  background: var(--primary-light);
  color: var(--primary);
  border: 1px solid var(--primary);
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-add-edu:hover {
  background: var(--primary);
  color: white;
}
.empty-edu {
  padding: 20px;
  text-align: center;
  border: 2px dashed var(--border);
  border-radius: 8px;
  font-size: 13px;
  color: var(--text-muted);
  background: var(--bg);
}
.edu-items-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.edu-item-form {
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 16px;
  background: var(--bg);
}
.edu-item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  font-weight: 700;
  color: var(--text-muted);
  margin-bottom: 12px;
  border-bottom: 1px solid var(--border);
  padding-bottom: 8px;
}
.btn-remove-edu {
  background: none;
  border: none;
  color: #ef4444;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}
.btn-remove-edu:hover {
  text-decoration: underline;
}
.edu-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 120px;
  gap: 12px;
}

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .content-grid { grid-template-columns: 1fr; }
  .edu-grid {
    grid-template-columns: 1fr;
  }
}

/* Multiple attachments manager styles */
.attachments-manager {
  border-top: 1px solid var(--border);
  padding-top: 16px;
  margin-top: 16px;
}
.saved-attachments {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 12px;
}
.attachment-item-saved {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 12px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 8px;
}
.attachment-name {
  font-size: 13px;
  font-weight: 500;
  color: var(--text);
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
  max-width: 70%;
}
.attachment-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}
.view-link-btn {
  font-size: 12px;
  color: var(--primary);
  text-decoration: underline;
  cursor: pointer;
}
.delete-attachment-btn {
  background: none;
  border: none;
  color: #ef4444;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}
.delete-attachment-btn:hover {
  text-decoration: underline;
}
.no-attachments-text {
  font-size: 12px;
  color: var(--text-muted);
  font-style: italic;
  margin-bottom: 12px;
}
.custom-file-upload-label {
  display: inline-block;
  padding: 8px 16px;
  background: var(--bg);
  border: 1px dashed var(--primary);
  color: var(--primary);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
  width: max-content;
}
.custom-file-upload-label:hover {
  background: var(--primary-light);
}
.hidden-file-input {
  display: none;
}
.upload-queue {
  margin-top: 12px;
  padding: 8px 12px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 8px;
}
.queue-title {
  font-size: 11px;
  font-weight: 700;
  color: var(--text-muted);
  margin-bottom: 6px;
}
.queue-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  color: var(--text);
  padding: 4px 0;
  border-bottom: 1px solid var(--border);
}
.queue-item:last-child {
  border-bottom: none;
}
.remove-queue-btn {
  background: none;
  border: none;
  color: var(--text-muted);
  font-size: 12px;
  cursor: pointer;
}
.remove-queue-btn:hover {
  color: #ef4444;
}
</style>