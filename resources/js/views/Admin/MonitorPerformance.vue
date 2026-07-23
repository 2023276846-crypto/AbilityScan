<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); flex-shrink: 0;"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            Monitor Performance
          </h1>
          <p>System performance and 5-rule matching algorithm metrics</p>
        </div>
        <div class="topbar-actions">
          <span class="last-updated">Last updated: {{ lastUpdated }}</span>
          <button @click="loadData" class="btn-refresh" style="display: flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
            Refresh
          </button>
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
      </div>

      <div v-if="loading" class="loading-state">
        <p style="display: flex; align-items: center; justify-content: center; gap: 8px;">
          <svg class="animate-spin" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
          Loading performance data...
        </p>
      </div>

      <div v-else>
        <!-- System Health -->
        <div class="section-title" style="display: flex; align-items: center; gap: 6px;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted);"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          System Health
        </div>
        <div class="health-grid">
          <div :class="['health-card', getHealthClass('matchRate', health.matchRate)]">
            <div class="health-icon" style="color: currentColor; display: flex; align-items: center;">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
            </div>
            <div class="health-info">
              <h3>{{ health.matchRate }}</h3>
              <p>Avg. Matches</p>
              <span class="health-badge">
                <span class="health-dot"></span>
                {{ getHealthLabel('matchRate', health.matchRate) }}
              </span>
            </div>
          </div>
          <div :class="['health-card', getHealthClass('acceptanceRate', health.acceptanceRate)]">
            <div class="health-icon" style="color: currentColor; display: flex; align-items: center;">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="health-info">
              <h3>{{ health.acceptanceRate }}%</h3>
              <p>Acceptance Rate</p>
              <span class="health-badge">
                <span class="health-dot"></span>
                {{ getHealthLabel('acceptanceRate', health.acceptanceRate) }}
              </span>
            </div>
          </div>
          <div :class="['health-card', getHealthClass('profileCompletion', health.profileCompletion)]">
            <div class="health-icon" style="color: currentColor; display: flex; align-items: center;">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="health-info">
              <h3>{{ health.profileCompletion }}%</h3>
              <p>Profile Completion</p>
              <span class="health-badge">
                <span class="health-dot"></span>
                {{ getHealthLabel('profileCompletion', health.profileCompletion) }}
              </span>
            </div>
          </div>
          <div :class="['health-card', getHealthClass('approvalRate', health.approvalRate)]">
            <div class="health-icon" style="color: currentColor; display: flex; align-items: center;">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6M18 9h1.5a2.5 2.5 0 0 0 0-5H18M4 22h16M10 14.66V17c0 .55-.45 1-1 1H4v2h16v-2h-5c-.55 0-1-.45-1-1v-2.34M12 2a6 6 0 0 1 6 6v5a6 6 0 0 1-6 6 6 6 0 0 1-6-6V8a6 6 0 0 1 6-6z"/></svg>
            </div>
            <div class="health-info">
              <h3>{{ health.approvalRate }}%</h3>
              <p>Approval Rate</p>
              <span class="health-badge">
                <span class="health-dot"></span>
                {{ getHealthLabel('approvalRate', health.approvalRate) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Performance Charts -->
        <div class="content-grid">
          <div class="card">
            <h2 style="display: flex; align-items: center; gap: 8px;">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
              Matching Performance
            </h2>
            <canvas ref="matchingChart" class="performance-pie-chart"></canvas>
          </div>
          <div class="card">
            <h2 style="display: flex; align-items: center; gap: 8px;">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
              User Growth
            </h2>
            <canvas ref="growthChart" class="performance-bar-chart"></canvas>
          </div>
        </div>

        <!-- Key Metrics & 5-Rule Algorithm -->
        <div class="content-grid">
          <!-- Key Metrics -->
          <div class="card">
            <h2 style="display: flex; align-items: center; gap: 8px;">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
              Key Metrics
            </h2>
            <div class="metrics-list">
              <div class="metric-item">
                <div class="metric-icon" style="color: #166534; display: flex; align-items: center;">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="metric-info">
                  <h4>Total PWD Registered</h4>
                  <p>{{ metrics.totalPWD }} accounts</p>
                </div>
                <div class="metric-trend up">Active</div>
              </div>
              <div class="metric-item">
                <div class="metric-icon" style="color: var(--primary); display: flex; align-items: center;">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="15" y1="22" x2="15" y2="16"/><path d="M9 16h6"/></svg>
                </div>
                <div class="metric-info">
                  <h4>Total Employers</h4>
                  <p>{{ metrics.totalEmployers }} companies</p>
                </div>
                <div class="metric-trend up">Active</div>
              </div>
              <div class="metric-item">
                <div class="metric-icon" style="color: #c2410c; display: flex; align-items: center;">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div class="metric-info">
                  <h4>Open Job Vacancies</h4>
                  <p>{{ metrics.openJobs }} positions</p>
                </div>
                <div class="metric-trend up">Open</div>
              </div>
              <div class="metric-item">
                <div class="metric-icon" style="color: #7e22ce; display: flex; align-items: center;">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div class="metric-info">
                  <h4>Total Applications</h4>
                  <p>{{ metrics.totalApplications }} sent</p>
                </div>
                <div class="metric-trend up">Growing</div>
              </div>
              <div class="metric-item">
                <div class="metric-icon" style="color: #10b981; display: flex; align-items: center;">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="metric-info">
                  <h4>Successful Hires</h4>
                  <p>{{ metrics.acceptedApplications }} hired</p>
                </div>
                <div class="metric-trend up">Success</div>
              </div>
              <div class="metric-item">
                <div class="metric-icon" style="color: #854d0e; display: flex; align-items: center;">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12" y2="18.01"/></svg>
                </div>
                <div class="metric-info">
                  <h4>QR Scans</h4>
                  <p>{{ metrics.totalScans }} scans</p>
                </div>
                <div class="metric-trend up">Used</div>
              </div>
            </div>
          </div>

          <!-- 5-Rule Algorithm Performance -->
          <div class="card">
            <h2 style="display: flex; align-items: center; gap: 8px;">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
              5-Rule Algorithm Performance
            </h2>
            <div class="algo-stats">

              <!-- S-Rule -->
              <div class="algo-item">
                <div class="algo-header">
                  <span class="algo-rule-label s-label" style="display: inline-flex; align-items: center; gap: 4px;">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    S-Rule — Skills Match
                  </span>
                  <span class="algo-score">{{ algoStats.sScore }}%</span>
                </div>
                <div class="algo-bar">
                  <div class="algo-fill s-fill" :style="{ width: algoStats.sScore + '%' }"></div>
                </div>
                <p class="algo-desc">Weight: 30% — Matches PWD skills with job requirements</p>
              </div>

              <!-- P-Rule -->
              <div class="algo-item">
                <div class="algo-header">
                  <span class="algo-rule-label p-label" style="display: inline-flex; align-items: center; gap: 4px;">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    P-Rule — Physical Accessibility
                  </span>
                  <span class="algo-score">{{ algoStats.pScore }}%</span>
                </div>
                <div class="algo-bar">
                  <div class="algo-fill p-fill" :style="{ width: algoStats.pScore + '%' }"></div>
                </div>
                <p class="algo-desc">Weight: 25% — Matches physical needs with office facilities</p>
              </div>

              <!-- T-Rule -->
              <div class="algo-item">
                <div class="algo-header">
                  <span class="algo-rule-label t-label" style="display: inline-flex; align-items: center; gap: 4px;">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    T-Rule — Technology Support
                  </span>
                  <span class="algo-score">{{ algoStats.tScore }}%</span>
                </div>
                <div class="algo-bar">
                  <div class="algo-fill t-fill" :style="{ width: algoStats.tScore + '%' }"></div>
                </div>
                <p class="algo-desc">Weight: 20% — Matches tech needs with available tools</p>
              </div>

              <!-- W-Rule -->
              <div class="algo-item">
                <div class="algo-header">
                  <span class="algo-rule-label w-label" style="display: inline-flex; align-items: center; gap: 4px;">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    W-Rule — Work Arrangement
                  </span>
                  <span class="algo-score">{{ algoStats.wScore }}%</span>
                </div>
                <div class="algo-bar">
                  <div class="algo-fill w-fill" :style="{ width: algoStats.wScore + '%' }"></div>
                </div>
                <p class="algo-desc">Weight: 15% — Matches work flexibility needs</p>
              </div>

              <!-- C-Rule -->
              <div class="algo-item">
                <div class="algo-header">
                  <span class="algo-rule-label c-label" style="display: inline-flex; align-items: center; gap: 4px;">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    C-Rule — Communication & Sensory
                  </span>
                  <span class="algo-score">{{ algoStats.cScore }}%</span>
                </div>
                <div class="algo-bar">
                  <div class="algo-fill c-fill" :style="{ width: algoStats.cScore + '%' }"></div>
                </div>
                <p class="algo-desc">Weight: 10% — Matches sensory and communication needs</p>
              </div>

              <!-- Overall Score -->
              <div class="algo-item">
                <div class="algo-header">
                  <span class="algo-rule-label overall-label" style="display: inline-flex; align-items: center; gap: 4px;">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                    Overall Match Score
                  </span>
                  <span class="algo-score">{{ algoStats.overallScore }}%</span>
                </div>
                <div class="algo-bar">
                  <div class="algo-fill overall-fill"
                    :style="{ width: algoStats.overallScore + '%' }"></div>
                </div>
                <p class="algo-desc">S(30%) + P(25%) + T(20%) + W(15%) + C(10%) = 100%</p>
              </div>

              <!-- Summary -->
              <div class="algo-summary">
                <div class="algo-sum-item">
                  <span>Total Matches Generated</span>
                  <strong>{{ metrics.totalApplications }}</strong>
                </div>
                <div class="algo-sum-item">
                  <span>High Score Matches (≥70%)</span>
                  <strong>{{ metrics.highScoreMatches }}</strong>
                </div>
                <div class="algo-sum-item">
                  <span>Average Match Score</span>
                  <strong>{{ metrics.avgMatchScore }}%</strong>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Algorithm Configuration -->
        <div class="card algo-config-card">
          <h2 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Algorithm Configuration
          </h2>
          <p class="section-desc">Adjust the 5-layer rule weights. Total must equal 100%.</p>
          <div class="config-grid">
            <div class="input-group">
              <label class="s-label">S-Rule (Skills)</label>
              <input type="number" v-model.number="algoWeights.s_weight" min="0" max="100" class="config-input" />
            </div>
            <div class="input-group">
              <label class="p-label">P-Rule (Physical)</label>
              <input type="number" v-model.number="algoWeights.p_weight" min="0" max="100" class="config-input" />
            </div>
            <div class="input-group">
              <label class="t-label">T-Rule (Tech)</label>
              <input type="number" v-model.number="algoWeights.t_weight" min="0" max="100" class="config-input" />
            </div>
            <div class="input-group">
              <label class="w-label">W-Rule (Work)</label>
              <input type="number" v-model.number="algoWeights.w_weight" min="0" max="100" class="config-input" />
            </div>
            <div class="input-group">
              <label class="c-label">C-Rule (Sensory)</label>
              <input type="number" v-model.number="algoWeights.c_weight" min="0" max="100" class="config-input" />
            </div>
          </div>
          
          <div class="config-summary">
            <span>Total Weight: <strong :class="{'error-text': totalWeight !== 100}">{{ totalWeight }}%</strong></span>
            <span v-if="totalWeight !== 100" class="error-text ms-2">Must equal 100%</span>
          </div>

          <div class="config-actions">
            <button class="btn-save" @click="saveWeights" :disabled="totalWeight !== 100 || savingWeights" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
              <svg v-if="!savingWeights" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              {{ savingWeights ? 'Saving...' : 'Save Configuration' }}
            </button>
            <button class="btn-restart" @click="restartAlgorithm" :disabled="recalculating" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
              <svg v-if="!recalculating" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
              {{ recalculating ? 'Recalculating...' : 'Restart Algorithm Machine' }}
            </button>
          </div>
          <p v-if="configMessage" :class="['config-message', configMessageType]">{{ configMessage }}</p>
        </div>

        <!-- Recent Activity -->
        <div class="card">
          <h2 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Recent Activity
          </h2>
          <div v-if="recentActivity.length === 0" class="empty-state">
            <p>No recent activity</p>
          </div>
          <div v-else class="activity-list">
            <div v-for="activity in recentActivity" :key="activity.id" class="activity-item">
              <div :class="['activity-icon', activity.type]">{{ activity.icon }}</div>
              <div class="activity-info">
                <p>{{ activity.message }}</p>
                <span>{{ activity.time }}</span>
              </div>
              <div :class="['activity-badge', activity.type]">{{ activity.type }}</div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'
import { Chart, registerables } from 'chart.js'
Chart.register(...registerables)

export default {
  name: 'MonitorPerformance',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      loading: false,
      lastUpdated: '',
      health: {
        matchRate: 0,
        acceptanceRate: 0,
        profileCompletion: 0,
        approvalRate: 0,
      },
      metrics: {
        totalPWD: 0,
        totalEmployers: 0,
        openJobs: 0,
        totalApplications: 0,
        acceptedApplications: 0,
        totalScans: 0,
        highScoreMatches: 0,
        avgMatchScore: 0,
      },
      algoStats: {
        sScore: 0,
        pScore: 0,
        tScore: 0,
        wScore: 0,
        cScore: 0,
        overallScore: 0,
      },
      algoWeights: { s_weight: 30, p_weight: 25, t_weight: 20, w_weight: 15, c_weight: 10 },
      savingWeights: false,
      recalculating: false,
      configMessage: '',
      configMessageType: '',
      recentActivity: [],
      charts: {}
    }
  },
  computed: {
    totalWeight() {
      return (this.algoWeights.s_weight || 0) + 
             (this.algoWeights.p_weight || 0) + 
             (this.algoWeights.t_weight || 0) + 
             (this.algoWeights.w_weight || 0) + 
             (this.algoWeights.c_weight || 0)
    }
  },
  mounted() {
    this.loadData()
    this.fetchWeights()
  },
  beforeUnmount() {
    Object.values(this.charts).forEach(c => c.destroy())
  },
  methods: {
    async loadData() {
      this.loading = true
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/admin/monitor')
        const data = response.data
        this.health = data.health || this.health
        this.metrics = data.metrics || this.metrics
        // Map old algoStats to new 5-rule format
        const algo = data.algoStats || {}
        this.algoStats = {
          sScore: algo.sScore ?? algo.sRulesScore ?? 0,
          pScore: algo.pScore ?? 0,
          tScore: algo.tScore ?? 0,
          wScore: algo.wScore ?? 0,
          cScore: algo.cScore ?? 0,
          overallScore: algo.overallScore ?? 0,
        }
        this.recentActivity = data.recentActivity || []
        this.lastUpdated = new Date().toLocaleTimeString()
      } catch (err) {
        console.log('Failed to load monitor data')
      }
      this.loading = false
      setTimeout(() => {
        this.renderCharts()
      }, 150)
    },
    async fetchWeights() {
      try {
        const response = await axios.get('/api/admin/algorithm-weights')
        if (response.data.weights) {
          this.algoWeights = response.data.weights
        }
      } catch (e) { console.error('Failed to fetch weights') }
    },
    async saveWeights() {
      if (this.totalWeight !== 100) return
      this.savingWeights = true
      this.configMessage = ''
      try {
        await axios.post('/api/admin/algorithm-weights', this.algoWeights)
        this.configMessage = 'Weights saved successfully!'
        this.configMessageType = 'success'
        this.loadData()
      } catch (e) {
        this.configMessage = e.response?.data?.message || 'Failed to save weights'
        this.configMessageType = 'error'
      }
      this.savingWeights = false
      setTimeout(() => this.configMessage = '', 3000)
    },
    async restartAlgorithm() {
      if (!confirm('Are you sure you want to recalculate all existing matches?')) return
      this.recalculating = true
      this.configMessage = 'Recalculating matches... please wait.'
      this.configMessageType = 'info'
      try {
        const response = await axios.post('/api/admin/algorithm-recalculate')
        this.configMessage = response.data.message || 'Successfully recalculated matches!'
        this.configMessageType = 'success'
        this.loadData()
      } catch (e) {
        this.configMessage = 'Failed to recalculate matches.'
        this.configMessageType = 'error'
      }
      this.recalculating = false
      setTimeout(() => this.configMessage = '', 5000)
    },
    renderCharts() {
      if (this.charts.matchingChart) this.charts.matchingChart.destroy()
      const matchCanvas = this.$refs.matchingChart
      if (matchCanvas) {
        this.charts.matchingChart = new Chart(matchCanvas, {
          type: 'doughnut',
          data: {
            labels: ['Accepted', 'Pending', 'Rejected'],
            datasets: [{
              data: [
                this.metrics.acceptedApplications,
                this.metrics.totalApplications - this.metrics.acceptedApplications - (this.metrics.rejectedApplications || 0),
                this.metrics.rejectedApplications || 0
              ],
              backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
              borderWidth: 2,
              borderColor: '#fff'
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
          }
        })
      }

      if (this.charts.growthChart) this.charts.growthChart.destroy()
      const growthCanvas = this.$refs.growthChart
      if (growthCanvas) {
        this.charts.growthChart = new Chart(growthCanvas, {
          type: 'bar',
          data: {
            labels: ['PWD', 'Employers', 'Jobs', 'Applications'],
            datasets: [{
              label: 'Total Count',
              data: [
                this.metrics.totalPWD,
                this.metrics.totalEmployers,
                this.metrics.openJobs,
                this.metrics.totalApplications
              ],
              backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#3b82f6'],
              borderRadius: 8,
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
          }
        })
      }
    },
    getHealthClass(key, value) {
      if (key === 'matchRate') {
        if (value >= 3.0) return 'health-good'
        if (value >= 1.0) return 'health-ok'
        return 'health-poor'
      }
      if (key === 'acceptanceRate') {
        if (value >= 40) return 'health-good'
        if (value >= 20) return 'health-ok'
        return 'health-poor'
      }
      if (value >= 70) return 'health-good'
      if (value >= 40) return 'health-ok'
      return 'health-poor'
    },
    getHealthLabel(key, value) {
      if (key === 'matchRate') {
        if (value >= 3.0) return 'Good'
        if (value >= 1.0) return 'Fair'
        return 'Poor'
      }
      if (key === 'acceptanceRate') {
        if (value >= 40) return 'Good'
        if (value >= 20) return 'Fair'
        return 'Poor'
      }
      if (value >= 70) return 'Good'
      if (value >= 40) return 'Fair'
      return 'Poor'
    },

  }
}
</script>

<style scoped>
.dashboard { display: flex; min-height: 100vh; background: var(--bg); }



.main-content { margin-left: 240px; flex: 1; padding: 32px; }
.topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.topbar h1 { font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.topbar p { font-size: 14px; color: var(--text-muted); }
.topbar-actions { display: flex; gap: 12px; align-items: center; }
.last-updated { font-size: 12px; color: var(--text-muted); }
.btn-refresh { padding: 8px 16px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
.theme-toggle { padding: 8px 16px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer; }

.loading-state { background: var(--surface); border-radius: 16px; padding: 48px; text-align: center; border: 1px solid var(--border); color: var(--text-muted); }

.section-title { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 16px; }

/* Health Grid */
.health-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
.health-card { border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; border: 1px solid transparent; }
.health-good { background: #f0fdf4; border-color: #bbf7d0; }
.health-ok { background: #fef9c3; border-color: #fde047; }
.health-poor { background: #fef2f2; border-color: #fecaca; }
.health-icon { font-size: 32px; }
.health-good .health-info h3 { color: #166534; }
.health-ok .health-info h3 { color: #854d0e; }
.health-poor .health-info h3 { color: #991b1b; }
.health-info h3 { font-size: 28px; font-weight: 700; margin-bottom: 2px; }
.health-info p { font-size: 13px; color: var(--text-muted); margin-bottom: 4px; }
.health-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 12px;
  text-transform: uppercase;
}
.health-good .health-badge { background: #dcfce7; color: #166534; }
.health-ok .health-badge { background: #fef9c3; color: #854d0e; }
.health-poor .health-badge { background: #fee2e2; color: #991b1b; }

.health-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
}

/* Content Grid */
.content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }

.performance-pie-chart {
  max-height: 240px;
  max-width: 240px;
  margin: 0 auto;
  display: block;
}

.performance-bar-chart {
  max-height: 250px;
  display: block;
}

.card { background: var(--surface); border-radius: 16px; padding: 24px; border: 1px solid var(--border); box-shadow: var(--card-shadow); margin-bottom: 24px; }
.card h2 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 20px; }

/* Metrics */
.metrics-list { display: flex; flex-direction: column; gap: 12px; }
.metric-item { display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg); border-radius: 10px; border: 1px solid var(--border); }
.metric-icon { font-size: 24px; flex-shrink: 0; }
.metric-info { flex: 1; }
.metric-info h4 { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 2px; }
.metric-info p { font-size: 12px; color: var(--text-muted); }
.metric-trend { font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.metric-trend.up { background: #f0fdf4; color: #166534; }

/* 5-Rule Algorithm */
.algo-stats { display: flex; flex-direction: column; gap: 16px; }
.algo-item { display: flex; flex-direction: column; gap: 4px; }
.algo-header { display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 600; color: var(--text); }
.algo-rule-label { font-size: 12px; font-weight: 700; }
.algo-score { color: var(--primary); font-size: 13px; font-weight: 700; }

/* Rule Label Colors */
.s-label { color: #1d4ed8; }
.p-label { color: #166534; }
.t-label { color: #7e22ce; }
.w-label { color: #c2410c; }
.c-label { color: #854d0e; }
.overall-label { color: var(--primary); }

.algo-bar { height: 10px; background: var(--bg); border-radius: 10px; overflow: hidden; border: 1px solid var(--border); }
.algo-fill { height: 100%; border-radius: 10px; transition: width 0.5s ease; min-width: 4px; }

/* Rule Fill Colors */
.s-fill { background: #3b82f6; }
.p-fill { background: #10b981; }
.t-fill { background: #8b5cf6; }
.w-fill { background: #f59e0b; }
.c-fill { background: #f97316; }
.overall-fill { background: var(--primary); }

.algo-desc { font-size: 11px; color: var(--text-muted); }

.algo-summary { background: var(--bg); border-radius: 10px; padding: 16px; border: 1px solid var(--border); display: flex; flex-direction: column; gap: 8px; margin-top: 4px; }
.algo-sum-item { display: flex; justify-content: space-between; font-size: 13px; color: var(--text); }
.algo-sum-item strong { color: var(--primary); font-weight: 700; }

/* Activity */
.activity-list { display: flex; flex-direction: column; gap: 10px; }
.activity-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: var(--bg); border-radius: 10px; border: 1px solid var(--border); }
.activity-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
.activity-icon.pwd { background: #eff6ff; }
.activity-icon.employer { background: #f0fdf4; }
.activity-icon.job { background: #faf5ff; }
.activity-icon.application { background: #fff7ed; }
.activity-info { flex: 1; }
.activity-info p { font-size: 13px; color: var(--text); font-weight: 500; }
.activity-info span { font-size: 12px; color: var(--text-muted); }
.activity-badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
.activity-badge.pwd { background: #eff6ff; color: #1d4ed8; }
.activity-badge.employer { background: #f0fdf4; color: #166534; }
.activity-badge.job { background: #faf5ff; color: #7e22ce; }
.activity-badge.application { background: #fff7ed; color: #c2410c; }

.empty-state { padding: 32px; text-align: center; color: var(--text-muted); font-size: 14px; }

/* Algorithm Config */
.algo-config-card { margin-top: 24px; }
.config-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 16px; margin-bottom: 16px; }
.input-group { display: flex; flex-direction: column; gap: 4px; }
.input-group label { font-size: 12px; font-weight: 700; }
.config-input { padding: 10px; border: 2px solid var(--border); border-radius: 8px; font-size: 14px; text-align: center; }
.config-input:focus { border-color: var(--primary); outline: none; }
.config-summary { font-size: 14px; margin-bottom: 20px; padding: 10px; background: var(--bg); border-radius: 8px; text-align: center; }
.error-text { color: #ef4444; }
.ms-2 { margin-left: 8px; }
.config-actions { display: flex; gap: 12px; }
.btn-save { flex: 1; padding: 12px; background: #10b981; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s; }
.btn-save:hover:not(:disabled) { background: #059669; }
.btn-save:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-restart { flex: 1; padding: 12px; background: #ef4444; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s; }
.btn-restart:hover:not(:disabled) { background: #dc2626; }
.btn-restart:disabled { opacity: 0.5; cursor: not-allowed; }
.config-message { margin-top: 12px; padding: 10px; border-radius: 8px; font-size: 13px; text-align: center; }
.config-message.success { background: #f0fdf4; color: #166534; }
.config-message.error { background: #fef2f2; color: #991b1b; }
.config-message.info { background: #eff6ff; color: #1d4ed8; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .health-grid { grid-template-columns: repeat(2, 1fr); }
  .content-grid { grid-template-columns: 1fr; }
  .config-actions { flex-direction: column; }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
.animate-spin {
  animation: spin 1s linear infinite;
}
</style>