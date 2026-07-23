<template>
  <div class="login-page">
    <AppNavbar :isDark="isDark" @toggleTheme="$emit('toggleTheme')">
      <router-link to="/" class="btn-home">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: middle; flex-shrink: 0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Home
      </router-link>
    </AppNavbar>

    <div class="login-container">
      <div class="login-card">
        <h2>Log In Form</h2>

        <div class="role-selector">
          <button
            :class="['role-btn', role === 'pwd' ? 'active' : '']"
            @click="role = 'pwd'">
            Persons with Disabilities
          </button>
          <button
            :class="['role-btn', role === 'employer' ? 'active' : '']"
            @click="role = 'employer'">
            Employer
          </button>
          <button
            :class="['role-btn', role === 'admin' ? 'active' : '']"
            @click="role = 'admin'">
            Administrator
          </button>
        </div>

        <div class="form-group">
          <label>Username</label>
          <input
            v-model="email"
            type="email"
            placeholder="@enter your email"
            class="form-input"/>
        </div>

        <div class="form-group">
          <label>Password</label>
          <input
            v-model="password"
            type="password"
            placeholder="@enter your password"
            class="form-input"/>
        </div>

        <p v-if="error" class="error-msg">{{ error }}</p>

        <div class="form-actions">
          <button @click="handleLogin" class="btn-login" :disabled="loading">
            {{ loading ? 'Logging in...' : 'Log In' }}
          </button>
          <button @click="goToRegister" class="btn-register">Register</button>
        </div>
      </div>
    </div>

    <footer>
      <p>copyright © 2026 AbilityScan. All rights reserved. Final Year Project — UiTM</p>
    </footer>
  </div>
</template>

<script>
import axios from 'axios'
import AppNavbar from '../components/AppNavbar.vue'

export default {
  name: 'LoginPage',
  components: { AppNavbar },
  props: {
    isDark: Boolean
  },
  emits: ['toggleTheme'],
  data() {
    return {
      role: 'pwd',
      email: '',
      password: '',
      error: '',
      loading: false
    }
  },
  methods: {
    async handleLogin() {
      this.loading = true
      this.error = ''
      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password,
          role: this.role
        })
        const token = response.data.token
        localStorage.setItem('token', token)
        localStorage.setItem('role', this.role)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        if (this.role === 'pwd') {
          this.$router.push('/pwd/dashboard')
        } else if (this.role === 'employer') {
          this.$router.push('/employer/dashboard')
        } else if (this.role === 'admin') {
          this.$router.push('/admin/dashboard')
        }
      } catch (err) {
        this.error = 'Invalid credentials. Please try again.'
      }
      this.loading = false
    },
    goToRegister() {
      if (this.role === 'pwd') {
        this.$router.push('/register/pwd')
      } else if (this.role === 'employer') {
        this.$router.push('/register/employer')
      } else {
        this.error = 'Admin accounts are created by system administrator only.'
      }
    }
  }
}
</script>

<style scoped>
.login-page {
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

.login-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
}

.login-card {
  background: var(--surface);
  border-radius: 20px;
  padding: 40px;
  width: 100%;
  max-width: 500px;
  box-shadow: var(--card-shadow);
  border: 1px solid var(--border);
}

.login-card h2 {
  font-size: 24px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 24px;
  text-align: center;
}

.role-selector {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.role-btn {
  flex: 1;
  padding: 10px 8px;
  border: 2px solid var(--border);
  border-radius: 8px;
  background: var(--surface);
  color: var(--text-muted);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 100px;
}

.role-btn.active {
  border-color: var(--primary);
  background: var(--primary);
  color: white;
}

.form-group { margin-bottom: 20px; }

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
  transition: border-color 0.2s;
  outline: none;
}

.form-input:focus { border-color: var(--primary); }

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

.form-actions {
  display: flex;
  gap: 12px;
  margin-bottom: 16px;
}

.btn-login {
  flex: 1;
  padding: 12px;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-login:hover { background: var(--primary-hover); }
.btn-login:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-register {
  flex: 1;
  padding: 12px;
  background: #10b981;
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-register:hover { background: #059669; }

footer {
  text-align: center;
  padding: 20px;
  background: var(--footer-bg);
  color: var(--text-muted);
  font-size: 13px;
  border-top: 1px solid var(--border);
}
</style>