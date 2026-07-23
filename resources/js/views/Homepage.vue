<template>
  <div class="home">
    <!-- Navbar -->
    <AppNavbar :isDark="isDark" @toggleTheme="$emit('toggleTheme')">
      <router-link to="/login" class="btn-nav">Login</router-link>
      <router-link to="/register/pwd" class="btn-nav btn-primary">Register</router-link>
    </AppNavbar>

    <!-- Hero Section -->
    <div class="hero">
      <h1>Welcome to <span>AbilityScan</span> Community</h1>
      <p>Empowering Abilities, Unlocking Opportunities</p>

      <div class="hero-cards">
        <div class="hero-card">
          <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); margin: 0 auto 16px auto;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <h3>For PWD</h3>
          <p>Build your digital portfolio and find matching jobs</p>
          <router-link to="/register/pwd" class="card-btn">Get Started</router-link>
        </div>
        <div class="hero-card">
          <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); margin: 0 auto 16px auto;"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="9" y1="16" x2="15" y2="16"/><line x1="15" y1="16" x2="15" y2="22"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/></svg>
          <h3>For Employers</h3>
          <p>Find qualified PWD candidates for your company</p>
          <router-link to="/register/employer" class="card-btn">Get Started</router-link>
        </div>
        <div class="hero-card">
          <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); margin: 0 auto 16px auto;"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2M7 7h4v4H7zM13 13h4v4h-4zM7 13h4v4H7zM13 7h4v4h-4z"/></svg>
          <h3>QR Portfolio</h3>
          <p>Scan QR codes to instantly view candidate profiles</p>
          <router-link to="/login" class="card-btn">Learn More</router-link>
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
import AppNavbar from '../components/AppNavbar.vue'

export default {
  name: 'HomePage',
  components: { AppNavbar },
  props: {
    isDark: Boolean
  },
  emits: ['toggleTheme']
}
</script>

<style scoped>
.home {
  min-height: 100vh;
  background: var(--bg);
  display: flex;
  flex-direction: column;
}

.btn-nav {
  padding: 8px 20px;
  border: 2px solid var(--primary);
  border-radius: 8px;
  color: var(--primary);
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s;
  background: transparent;
}

.btn-nav:hover {
  background: var(--primary);
  color: white;
}

.btn-primary {
  background: var(--primary);
  color: white !important;
}

.btn-primary:hover { background: var(--primary-hover) !important; }

.hero {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 40px;
  text-align: center;
}

.hero h1 {
  font-size: 42px;
  font-weight: 800;
  color: var(--text);
  margin-bottom: 16px;
}

.hero h1 span { color: var(--primary); }

.hero > p {
  font-size: 18px;
  color: var(--text-muted);
  margin-bottom: 48px;
}

.hero-cards {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
  justify-content: center;
}

.hero-card {
  background: var(--surface);
  border-radius: 16px;
  padding: 32px 24px;
  width: 260px;
  box-shadow: var(--card-shadow);
  border: 1px solid var(--border);
  transition: transform 0.2s;
}

.hero-card:hover { transform: translateY(-4px); }

.card-icon { font-size: 40px; margin-bottom: 16px; }

.hero-card h3 {
  font-size: 20px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 8px;
}

.hero-card p {
  font-size: 14px;
  color: var(--text-muted);
  margin-bottom: 20px;
  line-height: 1.6;
}

.card-btn {
  display: inline-block;
  padding: 10px 24px;
  background: var(--primary);
  color: white;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  transition: background 0.2s;
}

.card-btn:hover { background: var(--primary-hover); }

footer {
  text-align: center;
  padding: 20px;
  background: var(--footer-bg);
  color: var(--text-muted);
  font-size: 13px;
  border-top: 1px solid var(--border);
}
</style>