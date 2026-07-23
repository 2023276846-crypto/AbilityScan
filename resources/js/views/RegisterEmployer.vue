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
        <h2>Register Page Employer Form</h2>

        <div class="form-grid">
          <!-- Left Column -->
          <div class="form-col">
            <div class="form-group">
              <label>Person In Charge Name</label>
              <input v-model="form.name" type="text"
                placeholder="@enter person in charge name" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Company Name</label>
              <input v-model="form.company_name" type="text"
                placeholder="@enter company name" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Company Address</label>
              <textarea v-model="form.company_address"
                placeholder="@enter company address"
                class="form-input textarea"></textarea>
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
          </div>

          <!-- Right Column -->
          <div class="form-col">
            <div class="form-group">
              <label>SSM Number</label>
              <input v-model="form.ssm_number" type="text"
                placeholder="@enter SSM number" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Company Email</label>
              <input v-model="form.email" type="email"
                placeholder="@enter company email" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Contact Number</label>
              <input v-model="form.contact_number" type="text"
                placeholder="@enter contact number" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Password</label>
              <input v-model="form.password" type="password"
                placeholder="@enter password" class="form-input"/>
            </div>

            <div class="form-group">
              <label>Confirm Password</label>
              <input v-model="form.password_confirmation" type="password"
                placeholder="@confirm password" class="form-input"/>
            </div>
          </div>
        </div>

        <!-- Accessibility Section -->
        <div class="accessibility-section">
          <h3>Office Accessibility Features</h3>
          <p class="section-desc">Select all accessibility features available in your office:</p>
          <div class="checkbox-grid">
            <label class="checkbox-item" v-for="feature in accessibilityOptions" :key="feature">
              <input type="checkbox" :value="feature" v-model="form.accessibility"/>
              <span class="checkmark"></span>
              {{ feature }}
            </label>
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
  name: 'RegisterEmployer',
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
        company_name: '',
        company_address: '',
        industry_type: '',
        ssm_number: '',
        contact_number: '',
        accessibility: [],
      },
      accessibilityOptions: [
        'Wheelchair Ramp',
        'Elevator Access',
        'Accessible Restroom',
        'Parking for Disabled',
        'Screen Reader Software',
        'Hearing Loop System',
        'Braille Signage',
        'Adjustable Workstation',
        'Sign Language Interpreter',
        'Flexible Working Hours',
      ],
      error: '',
      success: '',
      loading: false
    }
  },
  methods: {
    async handleRegister() {
      this.loading = true
      this.error = ''
      this.success = ''

      if (this.form.password !== this.form.password_confirmation) {
        this.error = 'Passwords do not match!'
        this.loading = false
        return
      }

      if (!this.form.industry_type) {
        this.error = 'Please select an industry type!'
        this.loading = false
        return
      }

      try {
        const response = await axios.post('/api/register/employer', {
          name: this.form.name,
          email: this.form.email,
          password: this.form.password,
          company_name: this.form.company_name,
          company_address: this.form.company_address,
          industry_type: this.form.industry_type,
          ssm_number: this.form.ssm_number,
          contact_number: this.form.contact_number,
          accessibility: this.form.accessibility,
        })

        localStorage.setItem('token', response.data.token)
        localStorage.setItem('role', 'employer')
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`

        this.success = 'Registration successful! Redirecting...'
        setTimeout(() => {
          this.$router.push('/employer/dashboard')
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
  max-width: 800px;
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
  margin-bottom: 24px;
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
  font-family: 'Segoe UI', sans-serif;
}

.form-input:focus { border-color: var(--primary); }

.textarea {
  resize: vertical;
  min-height: 90px;
}

/* Accessibility Section */
.accessibility-section {
  background: var(--primary-light);
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  border: 1px solid var(--border);
}

.accessibility-section h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 8px;
}

.section-desc {
  font-size: 13px;
  color: var(--text-muted);
  margin-bottom: 16px;
}

.checkbox-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 12px;
}

.checkbox-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  color: var(--text);
  cursor: pointer;
  padding: 8px 12px;
  border-radius: 8px;
  background: var(--surface);
  border: 1px solid var(--border);
  transition: all 0.2s;
}

.checkbox-item:hover {
  border-color: var(--primary);
  color: var(--primary);
}

.checkbox-item input[type="checkbox"] {
  width: 16px;
  height: 16px;
  accent-color: var(--primary);
  cursor: pointer;
}

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
  .checkbox-grid { grid-template-columns: 1fr; }
}
</style>