<template>
  <nav class="navbar">
    <div class="logo">
      <img :src="'/logo-icon.png'" alt="AbilityScan" class="logo-icon-img" />
      <span class="logo-text">AbilityScan</span>
    </div>

    <div class="nav-right">
      <!-- Theme Toggle -->
      <button class="theme-toggle" @click="$emit('toggleTheme')" :title="isDark ? 'Switch to Light' : 'Switch to Dark'">
        <span v-if="isDark">☀️ Light</span>
        <span v-else>🌙 Dark</span>
      </button>

      <!-- Slot for extra buttons -->
      <slot />
    </div>
  </nav>
</template>

<script>
export default {
  name: 'AppNavbar',
  props: {
    isDark: Boolean
  },
  emits: ['toggleTheme']
}
</script>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 40px;
  background: var(--navbar-bg);
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  position: sticky;
  top: 0;
  z-index: 100;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
}

.logo-icon-img {
  width: 44px;
  height: 44px;
  object-fit: contain;
  border-radius: 50%;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  background: white;
  border: 1px solid var(--border);
  padding: 2px;
}

.logo-text {
  font-size: 20px;
  font-weight: 700;
  color: var(--primary);
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

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

.theme-toggle:hover {
  border-color: var(--primary);
  color: var(--primary);
}
</style>