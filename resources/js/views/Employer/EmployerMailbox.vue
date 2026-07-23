<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <EmployerSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); flex-shrink: 0;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Mailbox
          </h1>
          <p>Manage applications and messages from PWD candidates</p>
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

      <!-- Tabs -->
      <div class="tabs">
        <button :class="['tab', activeTab === 'applications' ? 'active' : '']"
          @click="activeTab = 'applications'">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Applications
          <span v-if="pendingCount > 0" class="badge">{{ pendingCount }}</span>
        </button>
        <button :class="['tab', activeTab === 'messages' ? 'active' : '']"
          @click="activeTab = 'messages'">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          Messages
          <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
        </button>
        <button :class="['tab', activeTab === 'sent' ? 'active' : '']"
          @click="activeTab = 'sent'">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Sent
        </button>
      </div>

      <!-- Applications Tab -->
      <div v-if="activeTab === 'applications'">
        <div class="filter-bar">
          <select v-model="filterStatus" @change="filterApplications" class="filter-select">
            <option value="">All Applications</option>
            <option value="pending">Pending</option>
            <option value="accepted">Accepted</option>
            <option value="rejected">Rejected</option>
          </select>
          <select v-model="filterJob" @change="filterApplications" class="filter-select">
            <option value="">All Jobs</option>
            <option v-for="job in jobs" :key="job.id" :value="job.id">
              {{ job.job_title }}
            </option>
          </select>
        </div>

        <div v-if="filteredApplications.length === 0" class="empty-state">
          <p style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted); opacity: 0.5;"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            No applications yet.
          </p>
        </div>

        <div v-else class="applications-list">
          <div v-for="app in filteredApplications" :key="app.id" class="application-item">
            <div class="app-avatar">
              {{ getInitials(app.pwd?.full_name) }}
            </div>
            <div class="app-info">
              <h4>{{ app.pwd?.full_name || 'PWD Candidate' }}</h4>
              <p>OKU: {{ app.pwd?.oku_number }}</p>
              <p class="app-job" style="display: flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                {{ app.job?.job_title }}
              </p>
            </div>
            <div class="app-skills" v-if="app.pwd?.skills">
              <span v-for="skill in (app.pwd.skills || []).slice(0, 2)"
                :key="skill" class="tag-skill">
                {{ skill }}
              </span>
            </div>
            <!-- Match Score Badge for Application -->
            <div v-if="app.match_score !== undefined" :class="['match-badge-circle-sm', getScoreClass(app.match_score)]" style="margin-left: auto; margin-right: 12px; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 50%; border: 2.5px solid; font-size: 10px; font-weight: 700;">
              <span>{{ app.match_score }}%</span>
              <span style="font-size: 7px; text-transform: uppercase; margin-top: -2px; opacity: 0.8;">Match</span>
            </div>

            <div class="app-right">
              <div :class="['app-status', app.application_status]">
                {{ app.application_status }}
              </div>
              <p class="app-date">{{ app.created_at?.slice(0, 10) }}</p>
              <div class="app-actions">
                <button v-if="app.application_status === 'pending'"
                  @click="updateStatus(app, 'accepted')"
                  class="btn-accept" style="display: flex; align-items: center; gap: 4px;">
                  <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  Accept
                </button>
                <button v-if="app.application_status === 'pending'"
                  @click="updateStatus(app, 'rejected')"
                  class="btn-reject" style="display: flex; align-items: center; gap: 4px;">
                  <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  Reject
                </button>
                <button @click="viewProfile(app)" class="btn-view" style="display: flex; align-items: center; gap: 4px;">
                  <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  View
                </button>
                <button @click="messageCandidate(app.pwd)" class="btn-msg" style="display: flex; align-items: center; gap: 4px;">
                  <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                  Message
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Messages Tab -->
      <div v-if="activeTab === 'messages'">
        <div class="compose-bar">
          <h3 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Inbox
          </h3>
        </div>
        <div v-if="inbox.length === 0" class="empty-state">
          <p style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted); opacity: 0.5;"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            No messages yet.
          </p>
        </div>
        <div v-else class="messages-list">
          <div v-for="msg in inbox" :key="msg.id"
            :class="['message-item', !msg.is_read ? 'unread' : '']"
            @click="openMessage(msg)">
            <div class="msg-avatar">{{ getInitials(msg.sender_name) }}</div>
            <div class="msg-info">
              <div class="msg-header">
                <h4>{{ msg.sender_name }}</h4>
                <span class="msg-time">{{ formatDate(msg.created_at) }}</span>
              </div>
              <p class="msg-subject">{{ msg.subject }}</p>
              <p class="msg-preview">{{ msg.body.slice(0, 80) }}...</p>
            </div>
            <div v-if="!msg.is_read" class="unread-dot"></div>
          </div>
        </div>
      </div>

      <!-- Sent Tab -->
      <div v-if="activeTab === 'sent'">
        <div class="compose-bar">
          <h3 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            Sent Messages
          </h3>
        </div>
        <div v-if="sent.length === 0" class="empty-state">
          <p style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted); opacity: 0.5;"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            No sent messages yet.
          </p>
        </div>
        <div v-else class="messages-list">
          <div v-for="msg in sent" :key="msg.id" class="message-item"
            @click="openMessage(msg)">
            <div class="msg-avatar">{{ getInitials(msg.receiver_name) }}</div>
            <div class="msg-info">
              <div class="msg-header">
                <h4>To: {{ msg.receiver_name }}</h4>
                <span class="msg-time">{{ formatDate(msg.created_at) }}</span>
              </div>
              <p class="msg-subject">{{ msg.subject }}</p>
              <p class="msg-preview">{{ msg.body.slice(0, 80) }}...</p>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Profile Modal -->
    <div v-if="selectedProfile" class="modal-overlay" @click.self="selectedProfile = null">
      <div class="modal">
        <div class="modal-header">
          <div class="modal-profile">
            <div class="modal-avatar">{{ getInitials(selectedProfile.full_name) }}</div>
            <div>
              <h2>{{ selectedProfile.full_name }}</h2>
              <p>OKU No: {{ selectedProfile.oku_number }}</p>
            </div>
          </div>
          <button @click="selectedProfile = null" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <!-- Optional Tabs if application/job selected -->
          <div v-if="selectedApplication" class="modal-tabs">
            <button 
              type="button"
              :class="['tab-btn', modalActiveTab === 'compatibility' ? 'active' : '']"
              @click="modalActiveTab = 'compatibility'">
              📊 Compatibility Report
            </button>
            <button 
              type="button"
              :class="['tab-btn', modalActiveTab === 'profile' ? 'active' : '']"
              @click="modalActiveTab = 'profile'">
              👤 Profile Details
            </button>
          </div>

          <!-- Compatibility Tab Content -->
          <div v-if="selectedApplication && modalActiveTab === 'compatibility'" class="match-analysis-section">
            <h3 class="analysis-header-title">Compatibility Analysis vs {{ selectedApplication.job?.job_title }}</h3>
            <div class="analysis-summary">
              <div :class="['analysis-circle', getScoreClass(selectedApplication.match_score)]">
                <span class="analysis-score-number">{{ selectedApplication.match_score }}%</span>
                <span class="analysis-score-label">Fit</span>
              </div>
              <div class="analysis-bars">
                <!-- S-Rule -->
                <div class="bar-row">
                  <div class="bar-header">
                    <span>S-Rule: Skills Match</span>
                    <span :class="getScoreClass(selectedApplication.s_score)">{{ selectedApplication.s_score }}%</span>
                  </div>
                  <div class="bar-track">
                    <div class="bar-fill" :style="{ width: selectedApplication.s_score + '%', background: getBarColor(selectedApplication.s_score) }"></div>
                  </div>
                </div>
                <!-- P-Rule -->
                <div class="bar-row">
                  <div class="bar-header">
                    <span>P-Rule: Physical Accessibility</span>
                    <span :class="getScoreClass(selectedApplication.p_score)">{{ selectedApplication.p_score }}%</span>
                  </div>
                  <div class="bar-track">
                    <div class="bar-fill" :style="{ width: selectedApplication.p_score + '%', background: getBarColor(selectedApplication.p_score) }"></div>
                  </div>
                </div>
                <!-- T-Rule -->
                <div class="bar-row">
                  <div class="bar-header">
                    <span>T-Rule: Technology Support</span>
                    <span :class="getScoreClass(selectedApplication.t_score)">{{ selectedApplication.t_score }}%</span>
                  </div>
                  <div class="bar-track">
                    <div class="bar-fill" :style="{ width: selectedApplication.t_score + '%', background: getBarColor(selectedApplication.t_score) }"></div>
                  </div>
                </div>
                <!-- W-Rule -->
                <div class="bar-row">
                  <div class="bar-header">
                    <span>W-Rule: Work Arrangement</span>
                    <span :class="getScoreClass(selectedApplication.w_score)">{{ selectedApplication.w_score }}%</span>
                  </div>
                  <div class="bar-track">
                    <div class="bar-fill" :style="{ width: selectedApplication.w_score + '%', background: getBarColor(selectedApplication.w_score) }"></div>
                  </div>
                </div>
                <!-- C-Rule -->
                <div class="bar-row">
                  <div class="bar-header">
                    <span>C-Rule: Sensory & Comm.</span>
                    <span :class="getScoreClass(selectedApplication.c_score)">{{ selectedApplication.c_score }}%</span>
                  </div>
                  <div class="bar-track">
                    <div class="bar-fill" :style="{ width: selectedApplication.c_score + '%', background: getBarColor(selectedApplication.c_score) }"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Detailed Rule Breakdown -->
            <div class="analysis-rule-details">
              <!-- S-Rule Details -->
              <div class="rule-detail-card s-details">
                <h5>S-Rule: Skills Match Details (Weight 30%)</h5>
                <div class="detail-lists">
                  <div>
                    <span class="detail-subtitle success">Matched Skills</span>
                    <div class="tags">
                      <span v-for="item in (selectedApplication.pwd?.skills || []).filter(s => (selectedApplication.job?.requirements || []).includes(s))" :key="item" class="tag-matched">✓ {{ item }}</span>
                      <span v-if="!(selectedApplication.pwd?.skills || []).filter(s => (selectedApplication.job?.requirements || []).includes(s)).length" class="no-data-sm">None matched</span>
                    </div>
                  </div>
                  <div>
                    <span class="detail-subtitle danger">Missing Skills (Required by Job)</span>
                    <div class="tags">
                      <span v-for="item in (selectedApplication.job?.requirements || []).filter(s => !(selectedApplication.pwd?.skills || []).includes(s))" :key="item" class="tag-missing">✗ {{ item }}</span>
                      <span v-if="!(selectedApplication.job?.requirements || []).filter(s => !(selectedApplication.pwd?.skills || []).includes(s)).length" class="no-data-sm">None missing</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- P-Rule Details -->
              <div class="rule-detail-card p-details">
                <h5>P-Rule: Physical Accessibility Details (Weight 25%)</h5>
                <div class="detail-lists">
                  <div>
                    <span class="detail-subtitle success">Matched Facilities</span>
                    <div class="tags">
                      <span v-for="item in (selectedApplication.pwd?.physical_needs || []).filter(p => (selectedApplication.job?.physical_facilities || []).includes(p))" :key="item" class="tag-matched">✓ {{ item }}</span>
                      <span v-if="!(selectedApplication.pwd?.physical_needs || []).filter(p => (selectedApplication.job?.physical_facilities || []).includes(p)).length" class="no-data-sm">None matched</span>
                    </div>
                  </div>
                  <div>
                    <span class="detail-subtitle danger">Missing Facilities (Needed by Candidate)</span>
                    <div class="tags">
                      <span v-for="item in (selectedApplication.pwd?.physical_needs || []).filter(p => !(selectedApplication.job?.physical_facilities || []).includes(p))" :key="item" class="tag-missing">✗ {{ item }}</span>
                      <span v-if="!(selectedApplication.pwd?.physical_needs || []).filter(p => !(selectedApplication.job?.physical_facilities || []).includes(p)).length" class="no-data-sm">None missing</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- T-Rule Details -->
              <div class="rule-detail-card t-details">
                <h5>T-Rule: Technology Support Details (Weight 20%)</h5>
                <div class="detail-lists">
                  <div>
                    <span class="detail-subtitle success">Matched Tech Support</span>
                    <div class="tags">
                      <span v-for="item in (selectedApplication.pwd?.tech_needs || []).filter(t => (selectedApplication.job?.tech_support || []).includes(t))" :key="item" class="tag-matched">✓ {{ item }}</span>
                      <span v-if="!(selectedApplication.pwd?.tech_needs || []).filter(t => (selectedApplication.job?.tech_support || []).includes(t)).length" class="no-data-sm">None matched</span>
                    </div>
                  </div>
                  <div>
                    <span class="detail-subtitle danger">Missing Tech Support (Needed by Candidate)</span>
                    <div class="tags">
                      <span v-for="item in (selectedApplication.pwd?.tech_needs || []).filter(t => !(selectedApplication.job?.tech_support || []).includes(t))" :key="item" class="tag-missing">✗ {{ item }}</span>
                      <span v-if="!(selectedApplication.pwd?.tech_needs || []).filter(t => !(selectedApplication.job?.tech_support || []).includes(t)).length" class="no-data-sm">None missing</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- W-Rule Details -->
              <div class="rule-detail-card w-details">
                <h5>W-Rule: Work Arrangement Details (Weight 15%)</h5>
                <div class="detail-lists">
                  <div>
                    <span class="detail-subtitle success">Matched Work Arrangements</span>
                    <div class="tags">
                      <span v-for="item in (selectedApplication.pwd?.work_arrangement || []).filter(w => (selectedApplication.job?.work_arrangement || []).includes(w))" :key="item" class="tag-matched">✓ {{ item }}</span>
                      <span v-if="!(selectedApplication.pwd?.work_arrangement || []).filter(w => (selectedApplication.job?.work_arrangement || []).includes(w)).length" class="no-data-sm">None matched</span>
                    </div>
                  </div>
                  <div>
                    <span class="detail-subtitle danger">Missing Arrangements (Needed by Candidate)</span>
                    <div class="tags">
                      <span v-for="item in (selectedApplication.pwd?.work_arrangement || []).filter(w => !(selectedApplication.job?.work_arrangement || []).includes(w))" :key="item" class="tag-missing">✗ {{ item }}</span>
                      <span v-if="!(selectedApplication.pwd?.work_arrangement || []).filter(w => !(selectedApplication.job?.work_arrangement || []).includes(w)).length" class="no-data-sm">None missing</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- C-Rule Details -->
              <div class="rule-detail-card c-details">
                <h5>C-Rule: Sensory & Comm. Details (Weight 10%)</h5>
                <div class="detail-lists">
                  <div>
                    <span class="detail-subtitle success">Matched Support</span>
                    <div class="tags">
                      <span v-for="item in (selectedApplication.pwd?.sensory_needs || []).filter(c => (selectedApplication.job?.sensory_support || []).includes(c))" :key="item" class="tag-matched">✓ {{ item }}</span>
                      <span v-if="!(selectedApplication.pwd?.sensory_needs || []).filter(c => (selectedApplication.job?.sensory_support || []).includes(c)).length" class="no-data-sm">None matched</span>
                    </div>
                  </div>
                  <div>
                    <span class="detail-subtitle danger">Missing Support (Needed by Candidate)</span>
                    <div class="tags">
                      <span v-for="item in (selectedApplication.pwd?.sensory_needs || []).filter(c => !(selectedApplication.job?.sensory_support || []).includes(c))" :key="item" class="tag-missing">✗ {{ item }}</span>
                      <span v-if="!(selectedApplication.pwd?.sensory_needs || []).filter(c => !(selectedApplication.job?.sensory_support || []).includes(c)).length" class="no-data-sm">None missing</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Profile Tab Content -->
          <div v-else>
            <div class="modal-section">
              <h4 style="display: flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Skills
              </h4>
              <div class="tags">
                <span v-for="skill in (selectedProfile.skills || [])"
                  :key="skill" class="tag-skill">{{ skill }}</span>
                <span v-if="!selectedProfile.skills?.length" class="no-data-sm">None listed</span>
              </div>
            </div>
            <div class="modal-section" style="margin-top: 16px;">
              <h4 style="display: flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Accessibility Needs
              </h4>
              <div class="tags">
                <span v-for="need in (selectedProfile.accessibility_needs || [])"
                  :key="need" class="tag-acc">{{ need }}</span>
                <span v-if="!selectedProfile.accessibility_needs?.length" class="no-data-sm">None listed</span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="selectedProfile = null" class="btn-cancel">Close</button>
          <button @click="messageCandidate(selectedProfile)" class="btn-msg-modal" style="display: flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Send Message
          </button>
        </div>
      </div>
    </div>

    <!-- Threaded Conversation Modal -->
    <div v-if="showConversationModal" class="modal-overlay" @click.self="closeConversationModal">
      <div class="modal conversation-modal">
        <div class="modal-header">
          <div class="conversation-header-info">
            <div class="modal-avatar">{{ getInitials(activeConversation.other_user.name) }}</div>
            <div>
              <h2>Conversation with {{ activeConversation.other_user.name }}</h2>
              <p class="subtitle">Thread history of messages</p>
            </div>
          </div>
          <button @click="closeConversationModal" class="btn-close">✕</button>
        </div>

        <div class="modal-body chat-body" ref="chatBody">
          <div v-if="activeConversation.loading" class="chat-loading" style="display: flex; align-items: center; gap: 8px; justify-content: center;">
            <svg class="animate-spin" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
            Loading messages...
          </div>
          <div v-else-if="activeConversation.messages.length === 0" class="chat-empty">
            No messages in this conversation.
          </div>
          <div v-else class="chat-thread">
            <div v-for="cMsg in activeConversation.messages" :key="cMsg.id" 
              :class="['chat-bubble-wrapper', cMsg.is_mine ? 'mine' : 'theirs']">
              <div class="chat-bubble">
                <div class="chat-bubble-subject" v-if="cMsg.subject && cMsg.subject !== 'No Subject' && cMsg.subject !== 'Job Opportunity from AbilityScan'">
                  <strong>Subj: {{ cMsg.subject }}</strong>
                </div>
                <p class="chat-bubble-body">{{ cMsg.body }}</p>
                <span class="chat-bubble-time">{{ formatDate(cMsg.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer conversation-footer">
          <div class="quick-reply-box">
            <textarea v-model="activeConversation.replyBody" 
              class="form-input quick-reply-input" 
              placeholder="Type a message to reply..."
              @keydown.enter.prevent="sendQuickReply"></textarea>
            <div class="quick-reply-actions">
              <button @click="sendQuickReply" class="btn-send" :disabled="activeConversation.sending || !activeConversation.replyBody.trim()" style="display: flex; align-items: center; gap: 8px;">
                <svg v-if="!activeConversation.sending" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                {{ activeConversation.sending ? 'Sending...' : 'Send Reply' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Compose Modal -->
    <div v-if="showCompose" class="modal-overlay" @click.self="showCompose = false">
      <div class="modal">
        <div class="modal-header">
          <h2 style="display: flex; align-items: center; gap: 8px;">
            <svg v-if="replyTo" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
            <svg v-else viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            {{ replyTo ? 'Reply' : 'New Message' }}
          </h2>
          <button @click="showCompose = false" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>To</label>
            <input v-model="compose.receiver_name" type="text"
              class="form-input" placeholder="Recipient name" disabled/>
          </div>
          <div class="form-group">
            <label>Subject</label>
            <input v-model="compose.subject" type="text"
              class="form-input" placeholder="Subject..."/>
          </div>
          <div class="form-group">
            <label>Message</label>
            <textarea v-model="compose.body" class="form-input textarea"
              placeholder="Type your message here..."></textarea>
          </div>
          <p v-if="sendSuccess" class="success-msg">{{ sendSuccess }}</p>
          <p v-if="sendError" class="error-msg">{{ sendError }}</p>
        </div>
        <div class="modal-footer">
          <button @click="showCompose = false" class="btn-cancel">Cancel</button>
          <button @click="sendMessage" class="btn-send" :disabled="sending" style="display: flex; align-items: center; gap: 8px;">
            <svg v-if="!sending" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            {{ sending ? 'Sending...' : 'Send Message' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'EmployerMailbox',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      activeTab: 'messages',
      // Applications
      applications: [],
      filteredApplications: [],
      jobs: [],
      filterStatus: '',
      filterJob: '',
      selectedProfile: null,
      selectedApplication: null,
      modalActiveTab: 'compatibility',
      // Messages
      inbox: [],
      sent: [],
      unreadCount: 0,
      selectedMessage: null,
      showCompose: false,
      replyTo: null,
      compose: {
        receiver_id: null,
        receiver_name: '',
        subject: '',
        body: ''
      },
      sending: false,
      sendSuccess: '',
      sendError: '',
      loading: false,
      showConversationModal: false,
      activeConversation: {
        messages: [],
        other_user: {},
        loading: false,
        sending: false,
        replyBody: ''
      }
    }
  },
  computed: {
    pendingCount() {
      return this.applications.filter(a => a.application_status === 'pending').length
    }
  },
  watch: {
    '$route'(to, from) {
      this.checkQueryParams()
    }
  },
  mounted() {
    this.loadApplications()
    this.loadMessages()
    this.checkQueryParams()
  },
  methods: {
    async loadApplications() {
      this.loading = true
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/employer/applications')
        this.applications = response.data.applications || []
        this.filteredApplications = this.applications
        this.jobs = response.data.jobs || []
      } catch (err) {
        console.log('Failed to load applications')
      }
      this.loading = false
    },
    async loadMessages() {
      try {
        const token = localStorage.getItem('token')
        if (!token) return
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/messages')
        this.inbox = response.data.inbox || []
        this.sent = response.data.sent || []
        this.unreadCount = response.data.unread_count || 0
      } catch (err) {
        console.log('Failed to load messages')
      }
    },
    filterApplications() {
      this.filteredApplications = this.applications.filter(app => {
        const matchStatus = !this.filterStatus ||
          app.application_status === this.filterStatus
        const matchJob = !this.filterJob ||
          app.job_id === this.filterJob
        return matchStatus && matchJob
      })
    },
    async updateStatus(app, status) {
      try {
        await axios.put('/api/employer/applications/' + app.id, {
          application_status: status
        })
        app.application_status = status
      } catch (err) {
        alert('Failed to update status')
      }
    },
    viewProfile(appOrPwd) {
      if (appOrPwd && appOrPwd.pwd) {
        this.selectedProfile = appOrPwd.pwd
        this.selectedApplication = appOrPwd
        this.modalActiveTab = 'compatibility'
      } else {
        this.selectedProfile = appOrPwd
        this.selectedApplication = null
        this.modalActiveTab = 'profile'
      }
    },
    getScoreClass(score) {
      if (score >= 80) return 'score-excellent'
      if (score >= 60) return 'score-good'
      if (score >= 40) return 'score-fair'
      return 'score-poor'
    },
    getBarColor(score) {
      if (score >= 80) return '#10b981'
      if (score >= 60) return '#3b82f6'
      if (score >= 40) return '#f59e0b'
      return '#ef4444'
    },
    messageCandidate(pwd) {
      if (!pwd) return
      this.selectedProfile = null
      this.replyTo = null
      this.compose = {
        receiver_id: pwd.user_id,
        receiver_name: pwd.full_name,
        subject: 'Job Opportunity from AbilityScan',
        body: ''
      }
      this.showCompose = true
      this.activeTab = 'messages'
    },
    openCompose() {
      this.replyTo = null
      this.compose = { receiver_id: null, receiver_name: '', subject: '', body: '' }
      this.sendSuccess = ''
      this.sendError = ''
      this.showCompose = true
    },
    async openMessage(msg) {
      const otherUserId = msg.sender_id || msg.receiver_id
      const otherUserName = msg.sender_name || msg.receiver_name
      
      this.showConversationModal = true
      this.activeConversation.other_user = { id: otherUserId, name: otherUserName }
      this.activeConversation.messages = []
      this.activeConversation.loading = true
      this.activeConversation.replyBody = ''

      // Mark local message read
      if (!msg.is_read) {
        msg.is_read = true
        this.unreadCount = Math.max(0, this.unreadCount - 1)
        axios.put('/api/messages/' + msg.id + '/read').catch(() => {})
      }

      try {
        const response = await axios.get('/api/messages/conversation/' + otherUserId)
        this.activeConversation.messages = (response.data.messages || []).reverse()
        this.scrollToBottom()
      } catch (err) {
        console.error('Failed to load conversation', err)
      } finally {
        this.activeConversation.loading = false
      }
    },
    closeConversationModal() {
      this.showConversationModal = false
      this.activeConversation.messages = []
      this.activeConversation.other_user = {}
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.chatBody
        if (container) {
          container.scrollTop = container.scrollHeight
        }
      })
    },
    async sendQuickReply() {
      const body = this.activeConversation.replyBody.trim()
      if (!body) return
      this.activeConversation.sending = true
      try {
        const otherUserId = this.activeConversation.other_user.id
        await axios.post('/api/messages', {
          receiver_id: otherUserId,
          subject: 'Re: Message Thread',
          body: body
        })
        this.activeConversation.replyBody = ''
        const response = await axios.get('/api/messages/conversation/' + otherUserId)
        this.activeConversation.messages = (response.data.messages || []).reverse()
        this.loadMessages()
        this.scrollToBottom()
      } catch (err) {
        alert('Failed to send reply. Please try again.')
      }
      this.activeConversation.sending = false
    },
    checkQueryParams() {
      const query = this.$route.query
      if (query.tab) {
        this.activeTab = query.tab
      }
      if (query.compose === 'true' || query.compose === true) {
        this.activeTab = 'messages'
        this.replyTo = null
        this.compose = {
          receiver_id: query.receiver_id ? parseInt(query.receiver_id) : null,
          receiver_name: query.receiver_name || '',
          subject: query.subject || 'Job Opportunity from AbilityScan',
          body: ''
        }
        this.showCompose = true
      }
    },
    replyMessage(msg) {
      this.replyTo = msg
      this.compose = {
        receiver_id: msg.sender_id,
        receiver_name: msg.sender_name,
        subject: 'Re: ' + msg.subject,
        body: ''
      }
      this.selectedMessage = null
      this.sendSuccess = ''
      this.sendError = ''
      this.showCompose = true
    },
    async sendMessage() {
      if (!this.compose.receiver_id) {
        this.sendError = 'Please select a recipient!'
        return
      }
      this.sending = true
      this.sendSuccess = ''
      this.sendError = ''
      try {
        await axios.post('/api/messages', {
          receiver_id: this.compose.receiver_id,
          subject: this.compose.subject || 'No Subject',
          body: this.compose.body,
        })
        this.sendSuccess = 'Message sent successfully!'
        setTimeout(() => {
          this.showCompose = false
          this.loadMessages()
        }, 1500)
      } catch (err) {
        this.sendError = 'Failed to send message. Please try again.'
      }
      this.sending = false
    },
    async deleteMessage(msg) {
      if (confirm('Delete this message?')) {
        try {
          await axios.delete('/api/messages/' + msg.id)
          this.inbox = this.inbox.filter(m => m.id !== msg.id)
          this.sent = this.sent.filter(m => m.id !== msg.id)
          this.selectedMessage = null
        } catch (err) { alert('Failed to delete') }
      }
    },
    getInitials(name) {
      if (!name) return '?'
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    },
    formatDate(date) {
      if (!date) return ''
      return new Date(date).toLocaleDateString('en-MY', {
        day: 'numeric', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
      })
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

.tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  border-bottom: 2px solid var(--border);
}

.tab {
  padding: 10px 24px;
  border: none;
  background: none;
  color: var(--text-muted);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  border-bottom: 3px solid transparent;
  margin-bottom: -2px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}

.tab:hover { color: var(--primary); }
.tab.active { color: var(--primary); border-bottom-color: var(--primary); }

.badge {
  background: #ef4444;
  color: white;
  border-radius: 20px;
  padding: 1px 7px;
  font-size: 11px;
}

.filter-bar { display: flex; gap: 12px; margin-bottom: 20px; }

.filter-select {
  padding: 10px 14px;
  border: 2px solid var(--border);
  border-radius: 8px;
  font-size: 14px;
  color: var(--text);
  background: var(--input-bg);
  outline: none;
  cursor: pointer;
  min-width: 160px;
}

.empty-state {
  background: var(--surface);
  border-radius: 16px;
  padding: 48px;
  text-align: center;
  border: 1px solid var(--border);
  color: var(--text-muted);
  font-size: 15px;
}

.applications-list { display: flex; flex-direction: column; gap: 12px; }

.application-item {
  background: var(--surface);
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  border: 1px solid var(--border);
  box-shadow: var(--card-shadow);
}

.app-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: 700;
  flex-shrink: 0;
}

.app-info { flex: 1; }
.app-info h4 { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 2px; }
.app-info p { font-size: 13px; color: var(--text-muted); margin-bottom: 2px; }
.app-job { color: var(--primary) !important; font-weight: 600 !important; }

.app-skills { display: flex; flex-wrap: wrap; gap: 4px; }

.tag-skill {
  padding: 3px 8px;
  background: var(--primary-light);
  color: var(--primary);
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
}

.tag-acc {
  padding: 3px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
}

.app-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 6px;
}

.app-status {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.app-status.pending { background: #fef9c3; color: #854d0e; }
.app-status.accepted { background: #f0fdf4; color: #166534; }
.app-status.rejected { background: #fef2f2; color: #991b1b; }

.app-date { font-size: 12px; color: var(--text-muted); }
.app-actions { display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }

.btn-accept {
  padding: 6px 12px;
  background: #10b981;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.btn-reject {
  padding: 6px 12px;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.btn-view {
  padding: 6px 12px;
  border: 2px solid var(--border);
  border-radius: 6px;
  background: var(--surface);
  color: var(--text);
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.btn-msg {
  padding: 6px 12px;
  background: var(--primary-light);
  color: var(--primary);
  border: 1px solid var(--primary);
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

/* Messages */
.compose-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.compose-bar h3 { font-size: 16px; font-weight: 700; color: var(--text); }

.btn-compose {
  padding: 8px 20px;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.messages-list { display: flex; flex-direction: column; gap: 8px; }

.message-item {
  background: var(--surface);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid var(--border);
  cursor: pointer;
  transition: all 0.2s;
}

.message-item:hover { border-color: var(--primary); transform: translateY(-1px); }
.message-item.unread { border-left: 4px solid var(--primary); background: var(--primary-light); }

.msg-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: var(--primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  font-weight: 700;
  flex-shrink: 0;
}

.msg-info { flex: 1; min-width: 0; }

.msg-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2px;
}

.msg-header h4 { font-size: 14px; font-weight: 700; color: var(--text); }
.msg-time { font-size: 12px; color: var(--text-muted); }
.msg-subject { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 2px; }
.msg-preview { font-size: 12px; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.unread-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: var(--primary);
  flex-shrink: 0;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
  padding: 20px;
}

.modal {
  background: var(--surface);
  border-radius: 20px;
  width: 100%;
  max-width: 560px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 24px;
  border-bottom: 1px solid var(--border);
}

.modal-profile { display: flex; align-items: center; gap: 16px; }

.modal-avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: var(--primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  font-weight: 700;
}

.modal-header h2 { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.modal-header p { font-size: 13px; color: var(--text-muted); margin-bottom: 2px; }
.msg-date { font-size: 12px; color: var(--text-muted); }

.btn-close {
  padding: 8px 12px;
  border: 1px solid var(--border);
  border-radius: 8px;
  background: var(--surface);
  color: var(--text);
  cursor: pointer;
}

.modal-body { padding: 24px; display: flex; flex-direction: column; gap: 20px; }
.msg-body { font-size: 14px; color: var(--text); line-height: 1.8; white-space: pre-wrap; }

.modal-section h4 { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
.tags { display: flex; flex-wrap: wrap; gap: 6px; }

.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; }

.form-input {
  width: 100%;
  padding: 10px 14px;
  border: 2px solid var(--border);
  border-radius: 8px;
  font-size: 14px;
  color: var(--text);
  background: var(--input-bg);
  outline: none;
  font-family: 'Segoe UI', sans-serif;
}

.form-input:focus { border-color: var(--primary); }
.textarea { resize: vertical; min-height: 120px; }

.success-msg {
  color: #10b981;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 10px 16px;
  font-size: 13px;
}

.error-msg {
  color: #ef4444;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 10px 16px;
  font-size: 13px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 16px 24px;
  border-top: 1px solid var(--border);
  align-items: center;
}

.btn-delete {
  padding: 10px 20px;
  background: #fef2f2;
  color: #ef4444;
  border: 1px solid #fecaca;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  margin-right: auto;
}

.btn-cancel {
  padding: 10px 24px;
  border: 2px solid var(--border);
  border-radius: 8px;
  background: var(--surface);
  color: var(--text);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}

.btn-reply {
  padding: 10px 24px;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}

.btn-send {
  padding: 10px 24px;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}

.btn-send:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-msg-modal {
  padding: 10px 24px;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .application-item { flex-direction: column; align-items: flex-start; }
  .filter-bar { flex-direction: column; }
}

/* Conversation / Chat styles */
.conversation-modal {
  max-width: 650px !important;
  display: flex;
  flex-direction: column;
  height: 80vh;
}

.conversation-header-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.chat-body {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  background: var(--bg);
  display: flex;
  flex-direction: column;
}

.chat-loading, .chat-empty {
  margin: auto;
  color: var(--text-muted);
  font-size: 14px;
}

.chat-thread {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.chat-bubble-wrapper {
  display: flex;
  width: 100%;
}

.chat-bubble-wrapper.mine {
  justify-content: flex-end;
}

.chat-bubble-wrapper.theirs {
  justify-content: flex-start;
}

.chat-bubble {
  max-width: 75%;
  padding: 12px 16px;
  border-radius: 16px;
  font-size: 14px;
  line-height: 1.5;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  position: relative;
}

.chat-bubble-wrapper.mine .chat-bubble {
  background: var(--primary);
  color: white;
  border-bottom-right-radius: 4px;
}

.chat-bubble-wrapper.theirs .chat-bubble {
  background: var(--surface);
  color: var(--text);
  border-bottom-left-radius: 4px;
  border: 1px solid var(--border);
}

.chat-bubble-subject {
  font-size: 12px;
  opacity: 0.95;
  margin-bottom: 6px;
  border-bottom: 1px solid rgba(255,255,255,0.2);
  padding-bottom: 4px;
}

.chat-bubble-wrapper.theirs .chat-bubble-subject {
  border-bottom-color: var(--border);
}

.chat-bubble-body {
  margin: 0;
  white-space: pre-wrap;
}

.chat-bubble-time {
  display: block;
  font-size: 10px;
  opacity: 0.7;
  text-align: right;
  margin-top: 6px;
}

.conversation-footer {
  padding: 16px 20px;
  border-top: 1px solid var(--border);
}

.quick-reply-box {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.quick-reply-input {
  min-height: 60px !important;
  resize: none;
}

.quick-reply-actions {
  display: flex;
  justify-content: flex-end;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
.animate-spin {
  animation: spin 1s linear infinite;
}

/* Modal Tabs */
.modal-tabs {
  display: flex;
  gap: 8px;
  border-bottom: 2px solid var(--border);
  padding-bottom: 12px;
  margin-bottom: 8px;
}
.tab-btn {
  flex: 1;
  padding: 10px;
  border: none;
  background: transparent;
  color: var(--text-muted);
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.2s ease;
}
.tab-btn:hover {
  background: var(--bg);
  color: var(--text);
}
.tab-btn.active {
  background: var(--primary-light);
  color: var(--primary);
}

/* Compatibility Report styling inside Modal */
.match-analysis-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.analysis-header-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 4px;
}
.analysis-summary {
  display: flex;
  gap: 20px;
  align-items: center;
  background: var(--bg);
  padding: 16px;
  border-radius: 12px;
  border: 1px solid var(--border);
}
.analysis-circle {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  border: 5px solid;
  transition: all 0.2s ease;
}
.analysis-circle.score-excellent { border-color: #10b981; background: #f0fdf4; }
.analysis-circle.score-good { border-color: #3b82f6; background: #eff6ff; }
.analysis-circle.score-fair { border-color: #f59e0b; background: #fef9c3; }
.analysis-circle.score-poor { border-color: #ef4444; background: #fef2f2; }

.match-badge-circle-sm.score-excellent { border-color: #10b981; background: #f0fdf4; color: #166534; }
.match-badge-circle-sm.score-good { border-color: #3b82f6; background: #eff6ff; color: #1d4ed8; }
.match-badge-circle-sm.score-fair { border-color: #f59e0b; background: #fef9c3; color: #854d0e; }
.match-badge-circle-sm.score-poor { border-color: #ef4444; background: #fef2f2; color: #991b1b; }

.analysis-score-number {
  font-size: 20px;
  font-weight: 800;
}
.analysis-circle.score-excellent .analysis-score-number { color: #166534; }
.analysis-circle.score-good .analysis-score-number { color: #1d4ed8; }
.analysis-circle.score-fair .analysis-score-number { color: #854d0e; }
.analysis-circle.score-poor .analysis-score-number { color: #991b1b; }
.analysis-score-label {
  font-size: 10px;
  color: var(--text-muted);
  font-weight: 600;
  text-transform: uppercase;
}
.analysis-bars {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.bar-row {
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.bar-header {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  font-weight: 600;
  color: var(--text);
}
.bar-track {
  height: 6px;
  background: var(--surface);
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid var(--border);
}
.bar-fill {
  height: 100%;
  border-radius: 10px;
  transition: width 0.5s ease;
}
.analysis-rule-details {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.rule-detail-card {
  background: var(--bg);
  border-radius: 12px;
  border: 1px solid var(--border);
  padding: 12px;
}
.rule-detail-card h5 {
  font-size: 12px;
  font-weight: 700;
  margin-bottom: 8px;
}
.s-details h5 { color: #1d4ed8; }
.p-details h5 { color: #166534; }
.t-details h5 { color: #7e22ce; }
.w-details h5 { color: #c2410c; }
.c-details h5 { color: #854d0e; }

.detail-lists {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.detail-subtitle {
  display: block;
  font-size: 11px;
  font-weight: 700;
  margin-bottom: 4px;
  text-transform: uppercase;
}
.detail-subtitle.success { color: #166534; }
.detail-subtitle.danger { color: #991b1b; }

.tag-matched {
  padding: 2px 7px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 600;
  border: 1px solid #bbf7d0;
}
.tag-missing {
  padding: 2px 7px;
  background: #fef2f2;
  color: #991b1b;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 600;
  border: 1px solid #fecaca;
}
.no-data-sm {
  font-size: 10px;
  color: var(--text-muted);
  font-style: italic;
}
</style>