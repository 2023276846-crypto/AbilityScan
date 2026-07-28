<template>
  <div class="register-page">
    <!-- Navbar -->
    <AppNavbar :isDark="isDark" @toggleTheme="$emit('toggleTheme')">
      <router-link to="/" class="btn-home">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: middle; flex-shrink: 0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Home
      </router-link>
    </AppNavbar>

    <!-- Register Form -->
    <div class="register-container">
      <div class="register-card">
        <h2>Register Page PWD Form</h2>

        <div class="form-grid">
          <!-- Left Column -->
          <div class="form-col">
            <div class="form-group">
              <label>Full Name</label>
              <input v-model="form.full_name" type="text"
                placeholder="@enter your full name" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Username</label>
              <input v-model="form.name" type="text"
                placeholder="@enter your username" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Password</label>
              <input v-model="form.password" type="password"
                placeholder="@enter your password" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Confirm Password</label>
              <input v-model="form.password_confirmation" type="password"
                placeholder="@confirm your password" class="form-input"/>
            </div>
          </div>

          <!-- Right Column -->
          <div class="form-col">
            <div class="form-group">
              <label>IC Number</label>
              <input v-model="form.ic_number" type="text"
                placeholder="@enter your IC number" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input v-model="form.email" type="email"
                placeholder="@enter your email" class="form-input"/>
            </div>

            <div class="form-group">
              <label>OKU Number</label>
              <input v-model="form.oku_number" type="text"
                placeholder="@enter your OKU number" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Upload OKU Card</label>
              <input type="file" @change="handleFile"
                accept="image/*,.pdf" class="form-input file-input"/>
            </div>
          </div>
        </div>

        <!-- Error & Success -->
        <p v-if="error" class="error-msg">{{ error }}</p>
        <p v-if="success" class="success-msg">{{ success }}</p>

        <!-- Buttons -->
        <div class="form-actions">
          <router-link to="/login" class="btn-back">← Back to Login</router-link>
          <button @click="handleRegister" class="btn-register" :disabled="loading">
            {{ loading ? 'Registering...' : 'Register' }}
          </button>
        </div>

        <div class="login-link">
          Already have an account?
          <router-link to="/login">Login here</router-link>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <p>copyright © 2026 AbilityScan. All rights reserved. Final Year Project — UiTM</p>
    </footer>
  </div>
</template>

<script>
import axios from 'axios'
import AppNavbar from '../components/AppNavbar.vue'

export default {
  name: 'RegisterPWD',
  components: { AppNavbar },
  props: {
    isDark: Boolean
  },
  emits: ['toggleTheme'],
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        full_name: '',
        ic_number: '',
        oku_number: '',
        oku_card: null,
      },
      error: '',
      success: '',
      loading: false
    }
  },
  methods: {
    compressImage(file, maxWidth = 1000, maxHeight = 1000, quality = 0.8) {
      return new Promise((resolve) => {
        if (!file || !file.type.startsWith('image/')) {
          resolve(file);
          return;
        }
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
          const img = new Image();
          img.src = event.target.result;
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
        };
        reader.onerror = () => resolve(file);
      });
    },
    handleFile(event) {
      this.form.oku_card = event.target.files[0]
    },
    async handleRegister() {
      this.loading = true
      this.error = ''
      this.success = ''

      if (this.form.password !== this.form.password_confirmation) {
        this.error = 'Passwords do not match!'
        this.loading = false
        return
      }

      try {
        const formData = new FormData()
        formData.append('name', this.form.name)
        formData.append('email', this.form.email)
        formData.append('password', this.form.password)
        formData.append('full_name', this.form.full_name)
        formData.append('oku_number', this.form.oku_number)

        let okuCardFile = this.form.oku_card;
        if (okuCardFile) {
          okuCardFile = await this.compressImage(okuCardFile);
        }

        if (okuCardFile) {
          formData.append('oku_card', okuCardFile)
        }

        const response = await axios.post('/api/register/pwd', formData)
        localStorage.setItem('token', response.data.token)
        localStorage.setItem('role', 'pwd')
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`

        this.success = 'Registration successful! Redirecting...'
        setTimeout(() => {
          this.$router.push('/pwd/dashboard')
        }, 1500)

      } catch (err) {
        if (err.response && err.response.data.errors) {
          const errors = err.response.data.errors
          this.error = Object.values(errors)[0][0]
        } else {
          this.error = 'Registration failed. Please try again.'
        }
      }
      this.loading = false
    }
  }
}
</script>

<style scoped>
.register-page {
  min-height: 100vh;
  background: var(--bg);
  display: flex;
  flex-direction: column;
}

.btn-home {
  padding: 8px 20px;
  background: var(--primary-light);
  border-radius: 8px;
  color: var(--primary);
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-home:hover {
  background: var(--primary);
  color: white;
}

.register-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
}

.register-card {
  background: var(--surface);
  border-radius: 20px;
  padding: 40px;
  width: 100%;
  max-width: 750px;
  box-shadow: var(--card-shadow);
  border: 1px solid var(--border);
}

.register-card h2 {
  font-size: 24px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 28px;
  text-align: center;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 20px;
}

.form-group { margin-bottom: 18px; }

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: var(--text);
  margin-bottom: 8px;
}

.form-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid var(--border);
  border-radius: 10px;
  font-size: 14px;
  color: var(--text);
  background: var(--input-bg);
  outline: none;
  transition: border-color 0.2s;
}

.form-input:focus { border-color: var(--primary); }

.file-input { padding: 10px; cursor: pointer; }

.error-msg {
  color: #ef4444;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 10px 16px;
  font-size: 13px;
  margin-bottom: 16px;
  text-align: center;
}

.success-msg {
  color: #10b981;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 10px 16px;
  font-size: 13px;
  margin-bottom: 16px;
  text-align: center;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  gap: 12px;
}

.btn-back {
  padding: 12px 24px;
  border: 2px solid var(--border);
  border-radius: 10px;
  color: var(--text-muted);
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-back:hover {
  border-color: var(--primary);
  color: var(--primary);
}

.btn-register {
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

.btn-register:hover { background: var(--primary-hover); }
.btn-register:disabled { opacity: 0.6; cursor: not-allowed; }

.login-link {
  text-align: center;
  font-size: 14px;
  color: var(--text-muted);
}

.login-link a {
  color: var(--primary);
  font-weight: 600;
  text-decoration: none;
}

.login-link a:hover { text-decoration: underline; }

footer {
  text-align: center;
  padding: 20px;
  background: var(--footer-bg);
  color: var(--text-muted);
  font-size: 13px;
  border-top: 1px solid var(--border);
}

@media (max-width: 640px) {
  .form-grid { grid-template-columns: 1fr; }
  .form-actions { flex-direction: column; }
}
</style>