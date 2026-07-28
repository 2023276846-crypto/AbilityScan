<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <EmployerSidebar />

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); flex-shrink: 0;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Search Candidates
          </h1>
          <p>Find PWD candidates that match your job requirements</p>
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

      <!-- Search & Filter -->
      <div class="search-card">
        <div class="search-row">
          <div class="search-input-wrapper">
            <span class="search-icon">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input v-model="search" type="text"
              placeholder="Search by name or skill..."
              class="search-input"
              @input="filterCandidates"/>
          </div>
          <select v-model="filterSkill" @change="filterCandidates" class="filter-select">
            <option value="">All Skills</option>
            <option>Microsoft Office</option>
            <option>Data Entry</option>
            <option>Customer Service</option>
            <option>Graphic Design</option>
            <option>Web Development</option>
            <option>Programming</option>
            <option>Accounting</option>
            <option>Marketing</option>
            <option>Social Media</option>
            <option>Video Editing</option>
            <option>Content Writing</option>
            <option>Administrative</option>
          </select>
          <select v-model="filterStatus" @change="filterCandidates" class="filter-select">
            <option value="">All Status</option>
            <option value="approved">Approved</option>
            <option value="pending">Pending</option>
          </select>
          <select v-model="selectedJobId" @change="filterCandidates" class="filter-select job-match-select">
            <option value="">Match with Job (None)</option>
            <option v-for="job in jobs" :key="job.id" :value="job.id">
              Match: {{ job.job_title }}
            </option>
          </select>
        </div>
      </div>

      <!-- Results -->
      <div class="results-info">
        <p>Showing <strong>{{ filteredCandidates.length }}</strong> candidates</p>
      </div>

      <div v-if="loading" class="loading-state">
        <p style="display: flex; align-items: center; justify-content: center; gap: 8px;">
          <svg class="animate-spin" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
          Loading candidates...
        </p>
      </div>

      <div v-else-if="filteredCandidates.length === 0" class="empty-state">
        <p style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
          <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted); opacity: 0.5;"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
          No candidates found!
        </p>
      </div>

      <div v-else class="candidates-grid">
        <div v-for="candidate in filteredCandidates" :key="candidate.id" class="candidate-card">
          <!-- Header -->
          <div class="candidate-header">
            <img v-if="candidate.avatar_path" :src="'/storage/' + candidate.avatar_path" class="candidate-avatar-img" />
            <div v-else class="candidate-avatar">{{ getInitials(candidate.full_name) }}</div>
            <div class="candidate-info">
              <h3>{{ candidate.full_name }}</h3>
              <p v-if="candidate.location" class="candidate-location">📍 {{ candidate.location }}</p>
              <p>OKU No: {{ candidate.oku_number }}</p>
              <div :class="['candidate-status', candidate.status]">
                {{ candidate.status }}
              </div>
            </div>
            <!-- Match Score Badge -->
            <div v-if="selectedJobId && candidate.match_score !== undefined" :class="['match-badge-circle', getScoreClass(candidate.match_score)]">
              <span class="match-percent">{{ candidate.match_score }}%</span>
              <span class="match-text">Match</span>
            </div>
          </div>

          <!-- S-Rule: Skills -->
          <div class="candidate-section">
            <h4 class="rule-tag s-tag" style="display: flex; align-items: center; gap: 4px;">
              <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
              S-Rule: Skills
            </h4>
            <div class="tags">
              <span v-for="skill in (candidate.skills || []).slice(0, 3)"
                :key="skill" class="tag-skill">{{ skill }}</span>
              <span v-if="(candidate.skills || []).length > 3" class="tag-more">
                +{{ candidate.skills.length - 3 }} more
              </span>
              <span v-if="!candidate.skills || candidate.skills.length === 0" class="no-data">
                No skills listed
              </span>
            </div>
          </div>

          <!-- P-Rule: Physical Needs -->
          <div class="candidate-section">
            <h4 class="rule-tag p-tag" style="display: flex; align-items: center; gap: 4px;">
              <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              P-Rule: Physical Needs
            </h4>
            <div class="tags">
              <span v-for="item in (candidate.physical_needs || []).slice(0, 2)"
                :key="item" class="tag-physical">{{ item }}</span>
              <span v-if="(candidate.physical_needs || []).length > 2" class="tag-more">
                +{{ candidate.physical_needs.length - 2 }} more
              </span>
              <span v-if="!candidate.physical_needs || candidate.physical_needs.length === 0"
                class="no-data">None listed</span>
            </div>
          </div>

          <!-- T-Rule: Tech Needs -->
          <div class="candidate-section">
            <h4 class="rule-tag t-tag" style="display: flex; align-items: center; gap: 4px;">
              <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              T-Rule: Tech Needs
            </h4>
            <div class="tags">
              <span v-for="item in (candidate.tech_needs || []).slice(0, 2)"
                :key="item" class="tag-tech">{{ item }}</span>
              <span v-if="(candidate.tech_needs || []).length > 2" class="tag-more">
                +{{ candidate.tech_needs.length - 2 }} more
              </span>
              <span v-if="!candidate.tech_needs || candidate.tech_needs.length === 0"
                class="no-data">None listed</span>
            </div>
          </div>

          <!-- W-Rule: Work Arrangement -->
          <div class="candidate-section">
            <h4 class="rule-tag w-tag" style="display: flex; align-items: center; gap: 4px;">
              <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              W-Rule: Work Arrangement
            </h4>
            <div class="tags">
              <span v-for="item in (candidate.work_arrangement || []).slice(0, 2)"
                :key="item" class="tag-work">{{ item }}</span>
              <span v-if="(candidate.work_arrangement || []).length > 2" class="tag-more">
                +{{ candidate.work_arrangement.length - 2 }} more
              </span>
              <span v-if="!candidate.work_arrangement || candidate.work_arrangement.length === 0"
                class="no-data">None listed</span>
            </div>
          </div>

          <!-- C-Rule: Sensory Needs -->
          <div class="candidate-section">
            <h4 class="rule-tag c-tag" style="display: flex; align-items: center; gap: 4px;">
              <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              C-Rule: Sensory Needs
            </h4>
            <div class="tags">
              <span v-for="item in (candidate.sensory_needs || []).slice(0, 2)"
                :key="item" class="tag-sensory">{{ item }}</span>
              <span v-if="(candidate.sensory_needs || []).length > 2" class="tag-more">
                +{{ candidate.sensory_needs.length - 2 }} more
              </span>
              <span v-if="!candidate.sensory_needs || candidate.sensory_needs.length === 0"
                class="no-data">None listed</span>
            </div>
          </div>

          <!-- Attachments -->
          <div class="candidate-attachments"
            v-if="(candidate.certificates && candidate.certificates.length) || (candidate.videos && candidate.videos.length)">
            <!-- Certificates -->
            <template v-if="candidate.certificates && candidate.certificates.length">
              <a v-for="(cert, idx) in candidate.certificates.slice(0, 2)" :key="'c-' + idx"
                :href="'/storage/' + cert.path"
                target="_blank" class="attachment-badge" style="display: flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Cert
              </a>
              <span v-if="candidate.certificates.length > 2" class="tag-more">
                +{{ candidate.certificates.length - 2 }}
              </span>
            </template>
            <!-- Videos -->
            <template v-if="candidate.videos && candidate.videos.length">
              <a v-for="(vid, idx) in candidate.videos.slice(0, 1)" :key="'v-' + idx"
                :href="'/storage/' + vid.path"
                target="_blank" class="attachment-badge" style="display: flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7a2 2 0 0 0-2.45-1.45L16 7V5a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2l4.55 1.45A2 2 0 0 0 23 17V7z"/></svg>
                Video
              </a>
              <span v-if="candidate.videos.length > 1" class="tag-more">
                +{{ candidate.videos.length - 1 }}
              </span>
            </template>
          </div>

          <!-- Actions -->
          <div class="candidate-actions">
            <button @click="viewCandidate(candidate)" class="btn-view" style="display: flex; align-items: center; justify-content: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              View Profile
            </button>
            <button @click="contactCandidate(candidate)" class="btn-contact" style="display: flex; align-items: center; justify-content: center; gap: 6px;">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              Contact
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Candidate Detail Modal -->
    <div v-if="selectedCandidate" class="modal-overlay" @click.self="selectedCandidate = null">
      <div class="modal">
        <div class="modal-header">
          <div class="modal-profile">
            <img v-if="selectedCandidate.avatar_path" :src="'/storage/' + selectedCandidate.avatar_path" class="modal-avatar-img" />
            <div v-else class="modal-avatar">{{ getInitials(selectedCandidate.full_name) }}</div>
            <div>
              <h2>{{ selectedCandidate.full_name }}</h2>
              <p v-if="selectedCandidate.location" class="modal-location">📍 {{ selectedCandidate.location }}</p>
              <p>OKU No: {{ selectedCandidate.oku_number }}</p>
              <div :class="['candidate-status', selectedCandidate.status]">
                {{ selectedCandidate.status }}
              </div>
            </div>
          </div>
          <button @click="selectedCandidate = null" class="btn-close">✕</button>
        </div>

        <div class="modal-body">
          <!-- Optional Tabs if job selected -->
          <div v-if="selectedJobId" class="modal-tabs">
            <button 
              type="button"
              :class="['tab-btn', activeTab === 'compatibility' ? 'active' : '']"
              @click="activeTab = 'compatibility'">
              📊 Compatibility Report
            </button>
            <button 
              type="button"
              :class="['tab-btn', activeTab === 'profile' ? 'active' : '']"
              @click="activeTab = 'profile'">
              👤 Profile Details
            </button>
          </div>

          <!-- Compatibility Tab Content -->
          <div v-if="selectedJobId && activeTab === 'compatibility' && modalMatchDetails" class="match-analysis-section">
            <h3 class="analysis-header-title">Compatibility Analysis vs {{ selectedJob.job_title }}</h3>
            <div class="analysis-summary">
              <div :class="['analysis-circle', getScoreClass(modalMatchDetails.match_score)]">
                <span class="analysis-score-number">{{ modalMatchDetails.match_score }}%</span>
                <span class="analysis-score-label">Fit</span>
              </div>
              <div class="analysis-bars">
                <!-- S-Rule -->
                <div class="bar-row">
                  <div class="bar-header">
                    <span>S-Rule: Skills Match</span>
                    <span :class="getScoreClass(modalMatchDetails.s_score)">{{ modalMatchDetails.s_score }}%</span>
                  </div>
                  <div class="bar-track">
                    <div class="bar-fill" :style="{ width: modalMatchDetails.s_score + '%', background: getBarColor(modalMatchDetails.s_score) }"></div>
                  </div>
                </div>
                <!-- P-Rule -->
                <div class="bar-row">
                  <div class="bar-header">
                    <span>P-Rule: Physical Accessibility</span>
                    <span :class="getScoreClass(modalMatchDetails.p_score)">{{ modalMatchDetails.p_score }}%</span>
                  </div>
                  <div class="bar-track">
                    <div class="bar-fill" :style="{ width: modalMatchDetails.p_score + '%', background: getBarColor(modalMatchDetails.p_score) }"></div>
                  </div>
                </div>
                <!-- T-Rule -->
                <div class="bar-row">
                  <div class="bar-header">
                    <span>T-Rule: Technology Support</span>
                    <span :class="getScoreClass(modalMatchDetails.t_score)">{{ modalMatchDetails.t_score }}%</span>
                  </div>
                  <div class="bar-track">
                    <div class="bar-fill" :style="{ width: modalMatchDetails.t_score + '%', background: getBarColor(modalMatchDetails.t_score) }"></div>
                  </div>
                </div>
                <!-- W-Rule -->
                <div class="bar-row">
                  <div class="bar-header">
                    <span>W-Rule: Work Arrangement</span>
                    <span :class="getScoreClass(modalMatchDetails.w_score)">{{ modalMatchDetails.w_score }}%</span>
                  </div>
                  <div class="bar-track">
                    <div class="bar-fill" :style="{ width: modalMatchDetails.w_score + '%', background: getBarColor(modalMatchDetails.w_score) }"></div>
                  </div>
                </div>
                <!-- C-Rule -->
                <div class="bar-row">
                  <div class="bar-header">
                    <span>C-Rule: Sensory & Comm.</span>
                    <span :class="getScoreClass(modalMatchDetails.c_score)">{{ modalMatchDetails.c_score }}%</span>
                  </div>
                  <div class="bar-track">
                    <div class="bar-fill" :style="{ width: modalMatchDetails.c_score + '%', background: getBarColor(modalMatchDetails.c_score) }"></div>
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
                      <span v-for="item in modalMatchDetails.matchedSkills" :key="item" class="tag-matched">✓ {{ item }}</span>
                      <span v-if="!modalMatchDetails.matchedSkills.length" class="no-data-sm">None matched</span>
                    </div>
                  </div>
                  <div>
                    <span class="detail-subtitle danger">Missing Skills (Required by Job)</span>
                    <div class="tags">
                      <span v-for="item in modalMatchDetails.missingSkills" :key="item" class="tag-missing">✗ {{ item }}</span>
                      <span v-if="!modalMatchDetails.missingSkills.length" class="no-data-sm">None missing</span>
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
                      <span v-for="item in modalMatchDetails.matchedPhysical" :key="item" class="tag-matched">✓ {{ item }}</span>
                      <span v-if="!modalMatchDetails.matchedPhysical.length" class="no-data-sm">None matched</span>
                    </div>
                  </div>
                  <div>
                    <span class="detail-subtitle danger">Missing Facilities (Needed by Candidate)</span>
                    <div class="tags">
                      <span v-for="item in modalMatchDetails.missingPhysical" :key="item" class="tag-missing">✗ {{ item }}</span>
                      <span v-if="!modalMatchDetails.missingPhysical.length" class="no-data-sm">None missing</span>
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
                      <span v-for="item in modalMatchDetails.matchedTech" :key="item" class="tag-matched">✓ {{ item }}</span>
                      <span v-if="!modalMatchDetails.matchedTech.length" class="no-data-sm">None matched</span>
                    </div>
                  </div>
                  <div>
                    <span class="detail-subtitle danger">Missing Tech Support (Needed by Candidate)</span>
                    <div class="tags">
                      <span v-for="item in modalMatchDetails.missingTech" :key="item" class="tag-missing">✗ {{ item }}</span>
                      <span v-if="!modalMatchDetails.missingTech.length" class="no-data-sm">None missing</span>
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
                      <span v-for="item in modalMatchDetails.matchedWork" :key="item" class="tag-matched">✓ {{ item }}</span>
                      <span v-if="!modalMatchDetails.matchedWork.length" class="no-data-sm">None matched</span>
                    </div>
                  </div>
                  <div>
                    <span class="detail-subtitle danger">Missing Arrangements (Needed by Candidate)</span>
                    <div class="tags">
                      <span v-for="item in modalMatchDetails.missingWork" :key="item" class="tag-missing">✗ {{ item }}</span>
                      <span v-if="!modalMatchDetails.missingWork.length" class="no-data-sm">None missing</span>
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
                      <span v-for="item in modalMatchDetails.matchedSensory" :key="item" class="tag-matched">✓ {{ item }}</span>
                      <span v-if="!modalMatchDetails.matchedSensory.length" class="no-data-sm">None matched</span>
                    </div>
                  </div>
                  <div>
                    <span class="detail-subtitle danger">Missing Support (Needed by Candidate)</span>
                    <div class="tags">
                      <span v-for="item in modalMatchDetails.missingSensory" :key="item" class="tag-missing">✗ {{ item }}</span>
                      <span v-if="!modalMatchDetails.missingSensory.length" class="no-data-sm">None missing</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Profile Tab Content (shown if activeTab is 'profile' or no job selected) -->
          <div v-else>
            <!-- About Me -->
            <div v-if="selectedCandidate.about_me" class="modal-profile-section-card">
              <h4>About Me</h4>
              <p class="modal-section-text">{{ selectedCandidate.about_me }}</p>
            </div>

            <!-- Education Timeline -->
            <div v-if="selectedCandidate.education && selectedCandidate.education.length" class="modal-profile-section-card">
              <h4>🎓 Education Background</h4>
              <div class="edu-timeline">
                <div v-for="(edu, idx) in selectedCandidate.education" :key="idx" class="edu-timeline-item">
                  <div class="timeline-dot"></div>
                  <div class="timeline-content">
                    <span class="edu-level-badge">{{ edu.level }}</span>
                    <h5>{{ edu.school_name }}</h5>
                    <span class="edu-duration">{{ edu.years }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Work Experience -->
            <div v-if="selectedCandidate.experience" class="modal-profile-section-card">
              <h4>💼 Work Experience</h4>
              <p class="modal-section-text">{{ selectedCandidate.experience }}</p>
            </div>

            <!-- S-Rule -->
            <div class="modal-section">
              <h4 class="rule-tag s-tag" style="display: flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                S-Rule: Skills
              </h4>
              <div class="tags">
                <span v-for="skill in (selectedCandidate.skills || [])"
                  :key="skill" class="tag-skill">{{ skill }}</span>
                <span v-if="!selectedCandidate.skills?.length" class="no-data">None</span>
              </div>
            </div>

            <!-- P-Rule -->
            <div class="modal-section">
              <h4 class="rule-tag p-tag" style="display: flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                P-Rule: Physical Needs
              </h4>
              <div class="tags">
                <span v-for="item in (selectedCandidate.physical_needs || [])"
                  :key="item" class="tag-physical">{{ item }}</span>
                <span v-if="!selectedCandidate.physical_needs?.length" class="no-data">None</span>
              </div>
            </div>

            <!-- T-Rule -->
            <div class="modal-section">
              <h4 class="rule-tag t-tag" style="display: flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                T-Rule: Tech Needs
              </h4>
              <div class="tags">
                <span v-for="item in (selectedCandidate.tech_needs || [])"
                  :key="item" class="tag-tech">{{ item }}</span>
                <span v-if="!selectedCandidate.tech_needs?.length" class="no-data">None</span>
              </div>
            </div>

            <!-- W-Rule -->
            <div class="modal-section">
              <h4 class="rule-tag w-tag" style="display: flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                W-Rule: Work Arrangement
              </h4>
              <div class="tags">
                <span v-for="item in (selectedCandidate.work_arrangement || [])"
                  :key="item" class="tag-work">{{ item }}</span>
                <span v-if="!selectedCandidate.work_arrangement?.length" class="no-data">None</span>
              </div>
            </div>

            <!-- C-Rule -->
            <div class="modal-section">
              <h4 class="rule-tag c-tag" style="display: flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                C-Rule: Sensory Needs
              </h4>
              <div class="tags">
                <span v-for="item in (selectedCandidate.sensory_needs || [])"
                  :key="item" class="tag-sensory">{{ item }}</span>
                <span v-if="!selectedCandidate.sensory_needs?.length" class="no-data">None</span>
              </div>
            </div>

            <!-- Attachments -->
            <div class="modal-section"
              v-if="(selectedCandidate.certificates && selectedCandidate.certificates.length) || (selectedCandidate.videos && selectedCandidate.videos.length)">
              <h4 style="display: flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                Attachments
              </h4>
              <div class="attachments">
                <!-- Certificates -->
                <template v-if="selectedCandidate.certificates && selectedCandidate.certificates.length">
                  <a v-for="(cert, idx) in selectedCandidate.certificates" :key="'mc-' + idx"
                    :href="'/storage/' + cert.path"
                    target="_blank" class="attachment-link" style="display: flex; align-items: center; gap: 6px;">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    {{ cert.name || 'Certificate ' + (idx + 1) }}
                  </a>
                </template>
                <!-- Videos -->
                <template v-if="selectedCandidate.videos && selectedCandidate.videos.length">
                  <a v-for="(vid, idx) in selectedCandidate.videos" :key="'mv-' + idx"
                    :href="'/storage/' + vid.path"
                    target="_blank" class="attachment-link" style="display: flex; align-items: center; gap: 6px;">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7a2 2 0 0 0-2.45-1.45L16 7V5a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2l4.55 1.45A2 2 0 0 0 23 17V7z"/></svg>
                    {{ vid.name || 'Skills Video ' + (idx + 1) }}
                  </a>
                </template>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="selectedCandidate = null" class="btn-cancel">Close</button>
          <button @click="contactCandidate(selectedCandidate)" class="btn-contact-modal" style="display: flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Send Message
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'SearchCandidates',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      candidates: [],
      filteredCandidates: [],
      jobs: [],
      selectedJobId: '',
      weights: { s_weight: 30, p_weight: 25, t_weight: 20, w_weight: 15, c_weight: 10 },
      search: '',
      filterSkill: '',
      filterStatus: '',
      selectedCandidate: null,
      activeTab: 'compatibility',
      loading: false
    }
  },
  computed: {
    selectedJob() {
      if (!this.selectedJobId) return null
      return this.jobs.find(j => j.id == this.selectedJobId) || null
    },
    modalMatchDetails() {
      if (!this.selectedCandidate || !this.selectedJob) return null
      return this.calculateDetailedMatch(this.selectedCandidate, this.selectedJob)
    }
  },
  mounted() {
    this.loadCandidates()
    this.loadJobs()
    this.loadWeights()
  },
  methods: {
    async loadCandidates() {
      this.loading = true
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const response = await axios.get('/api/employer/candidates')
        this.candidates = response.data.candidates || []
        this.filterCandidates()
      } catch (err) {
        console.log('Failed to load candidates')
        this.candidates = []
        this.filteredCandidates = []
      }
      this.loading = false
    },
    async loadJobs() {
      try {
        const response = await axios.get('/api/employer/jobs')
        this.jobs = response.data.jobs || []
      } catch (err) {
        console.log('Failed to load jobs')
        this.jobs = []
      }
    },
    async loadWeights() {
      try {
        const response = await axios.get('/api/admin/algorithm-weights')
        this.weights = response.data.weights || { s_weight: 30, p_weight: 25, t_weight: 20, w_weight: 15, c_weight: 10 }
      } catch (err) {
        console.log('Failed to load algorithm weights, using default')
      }
    },
    calculateMatchScore(candidate, job) {
      if (!job) return 0

      // S-Rule: Skills Match (Weight 30%)
      const candSkills = candidate.skills || []
      const jobReqs = job.requirements || []
      let sScore = 100
      if (jobReqs.length > 0) {
        const matched = candSkills.filter(s => jobReqs.includes(s))
        sScore = Math.round((matched.length / jobReqs.length) * 100)
      }

      // P-Rule: Physical Accessibility (Weight 25%)
      const candPhys = candidate.physical_needs || []
      const jobPhys = job.physical_facilities || []
      let pScore = 100
      if (candPhys.length > 0) {
        const matched = candPhys.filter(p => jobPhys.includes(p))
        pScore = Math.round((matched.length / candPhys.length) * 100)
      }

      // T-Rule: Technology Support (Weight 20%)
      const candTech = candidate.tech_needs || []
      const jobTech = job.tech_support || []
      let tScore = 100
      if (candTech.length > 0) {
        const matched = candTech.filter(t => jobTech.includes(t))
        tScore = Math.round((matched.length / candTech.length) * 100)
      }

      // W-Rule: Work Arrangement (Weight 15%)
      const candWork = candidate.work_arrangement || []
      const jobWork = job.work_arrangement || []
      let wScore = 100
      if (candWork.length > 0) {
        const matched = candWork.filter(w => jobWork.includes(w))
        wScore = Math.round((matched.length / candWork.length) * 100)
      }

      // C-Rule: Communication & Sensory (Weight 10%)
      const candSens = candidate.sensory_needs || []
      const jobSens = job.sensory_support || []
      let cScore = 100
      if (candSens.length > 0) {
        const matched = candSens.filter(c => jobSens.includes(c))
        cScore = Math.round((matched.length / candSens.length) * 100)
      }

      const sw = this.weights.s_weight !== undefined ? this.weights.s_weight : 30
      const pw = this.weights.p_weight !== undefined ? this.weights.p_weight : 25
      const tw = this.weights.t_weight !== undefined ? this.weights.t_weight : 20
      const ww = this.weights.w_weight !== undefined ? this.weights.w_weight : 15
      const cw = this.weights.c_weight !== undefined ? this.weights.c_weight : 10

      const finalScore = Math.round(
        (sScore * (sw / 100)) +
        (pScore * (pw / 100)) +
        (tScore * (tw / 100)) +
        (wScore * (ww / 100)) +
        (cScore * (cw / 100))
      )

      return finalScore
    },
    calculateDetailedMatch(candidate, job) {
      if (!job) return null

      // S-Rule: Skills Match (Weight 30%)
      const candSkills = candidate.skills || []
      const jobReqs = job.requirements || []
      let sScore = 100
      let matchedSkills = []
      let missingSkills = []
      if (jobReqs.length > 0) {
        matchedSkills = candSkills.filter(s => jobReqs.includes(s))
        missingSkills = jobReqs.filter(s => !candSkills.includes(s))
        sScore = Math.round((matchedSkills.length / jobReqs.length) * 100)
      }

      // P-Rule: Physical Accessibility (Weight 25%)
      const candPhys = candidate.physical_needs || []
      const jobPhys = job.physical_facilities || []
      let pScore = 100
      let matchedPhysical = []
      let missingPhysical = []
      if (candPhys.length > 0) {
        matchedPhysical = candPhys.filter(p => jobPhys.includes(p))
        missingPhysical = candPhys.filter(p => !jobPhys.includes(p))
        pScore = Math.round((matchedPhysical.length / candPhys.length) * 100)
      }

      // T-Rule: Technology Support (Weight 20%)
      const candTech = candidate.tech_needs || []
      const jobTech = job.tech_support || []
      let tScore = 100
      let matchedTech = []
      let missingTech = []
      if (candTech.length > 0) {
        matchedTech = candTech.filter(t => jobTech.includes(t))
        missingTech = candTech.filter(t => !jobTech.includes(t))
        tScore = Math.round((matchedTech.length / candTech.length) * 100)
      }

      // W-Rule: Work Arrangement (Weight 15%)
      const candWork = candidate.work_arrangement || []
      const jobWork = job.work_arrangement || []
      let wScore = 100
      let matchedWork = []
      let missingWork = []
      if (candWork.length > 0) {
        matchedWork = candWork.filter(w => jobWork.includes(w))
        missingWork = candWork.filter(w => !jobWork.includes(w))
        wScore = Math.round((matchedWork.length / candWork.length) * 100)
      }

      // C-Rule: Communication & Sensory (Weight 10%)
      const candSens = candidate.sensory_needs || []
      const jobSens = job.sensory_support || []
      let cScore = 100
      let matchedSensory = []
      let missingSensory = []
      if (candSens.length > 0) {
        matchedSensory = candSens.filter(c => jobSens.includes(c))
        missingSensory = candSens.filter(c => !jobSens.includes(c))
        cScore = Math.round((matchedSensory.length / candSens.length) * 100)
      }

      const sw = this.weights.s_weight !== undefined ? this.weights.s_weight : 30
      const pw = this.weights.p_weight !== undefined ? this.weights.p_weight : 25
      const tw = this.weights.t_weight !== undefined ? this.weights.t_weight : 20
      const ww = this.weights.w_weight !== undefined ? this.weights.w_weight : 15
      const cw = this.weights.c_weight !== undefined ? this.weights.c_weight : 10

      const finalScore = Math.round(
        (sScore * (sw / 100)) +
        (pScore * (pw / 100)) +
        (tScore * (tw / 100)) +
        (wScore * (ww / 100)) +
        (cScore * (cw / 100))
      )

      return {
        match_score: finalScore,
        s_score: sScore,
        p_score: pScore,
        t_score: tScore,
        w_score: wScore,
        c_score: cScore,
        matchedSkills, missingSkills, jobReqs, candSkills,
        matchedPhysical, missingPhysical, jobPhys, candPhys,
        matchedTech, missingTech, jobTech, candTech,
        matchedWork, missingWork, jobWork, candWork,
        matchedSensory, missingSensory, jobSens, candSens
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
    filterCandidates() {
      let result = this.candidates.filter(c => {
        const matchSearch = !this.search ||
          c.full_name.toLowerCase().includes(this.search.toLowerCase()) ||
          (c.skills || []).some(s => s.toLowerCase().includes(this.search.toLowerCase()))
        const matchSkill = !this.filterSkill ||
          (c.skills || []).includes(this.filterSkill)
        const matchStatus = !this.filterStatus ||
          c.status === this.filterStatus
        return matchSearch && matchSkill && matchStatus
      })

      if (this.selectedJobId) {
        const selectedJob = this.jobs.find(j => j.id === this.selectedJobId)
        if (selectedJob) {
          result = result.map(c => {
            return {
              ...c,
              match_score: this.calculateMatchScore(c, selectedJob)
            }
          })
          result.sort((a, b) => b.match_score - a.match_score)
        }
      } else {
        result = result.map(c => {
          const { match_score, ...rest } = c
          return rest
        })
      }

      this.filteredCandidates = result
    },
    getInitials(name) {
      if (!name) return '?'
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    },
    viewCandidate(candidate) {
      this.selectedCandidate = candidate
      this.activeTab = 'profile'
    },
    contactCandidate(candidate) {
      if (!candidate) return
      this.selectedCandidate = null
      this.$router.push({
        path: '/employer/mailbox',
        query: {
          compose: true,
          receiver_id: candidate.user_id,
          receiver_name: candidate.full_name
        }
      })
    }
  }
}
</script>

<style scoped>
.dashboard { display: flex; min-height: 100vh; background: var(--bg); }

.job-match-select {
  border-color: var(--primary) !important;
  font-weight: 600;
}

.match-badge-circle {
  margin-left: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 3px solid;
  font-size: 11px;
  font-weight: 700;
  flex-shrink: 0;
  box-shadow: var(--card-shadow);
  transition: all 0.2s ease;
}

.match-badge-circle.score-excellent { border-color: #10b981; background: #f0fdf4; color: #166534; }
.match-badge-circle.score-good { border-color: #3b82f6; background: #eff6ff; color: #1d4ed8; }
.match-badge-circle.score-fair { border-color: #f59e0b; background: #fef9c3; color: #854d0e; }
.match-badge-circle.score-poor { border-color: #ef4444; background: #fef2f2; color: #991b1b; }

.match-percent { font-size: 12px; font-weight: 800; }
.match-text { font-size: 8px; text-transform: uppercase; margin-top: -2px; opacity: 0.8; }

.main-content { margin-left: 240px; flex: 1; padding: 32px; }

.topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.topbar h1 { font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.topbar p { font-size: 14px; color: var(--text-muted); }

.theme-toggle { padding: 8px 16px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.theme-toggle:hover { border-color: var(--primary); color: var(--primary); }

.search-card { background: var(--surface); border-radius: 16px; padding: 20px; margin-bottom: 20px; border: 1px solid var(--border); box-shadow: var(--card-shadow); }
.search-row { display: flex; gap: 12px; flex-wrap: wrap; }

.search-input-wrapper { flex: 2; position: relative; min-width: 200px; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); }

.search-input { width: 100%; padding: 10px 14px 10px 40px; border: 2px solid var(--border); border-radius: 8px; font-size: 14px; color: var(--text); background: var(--input-bg); outline: none; transition: border-color 0.2s; }
.search-input:focus { border-color: var(--primary); }

.filter-select { flex: 1; min-width: 140px; padding: 10px 14px; border: 2px solid var(--border); border-radius: 8px; font-size: 14px; color: var(--text); background: var(--input-bg); outline: none; cursor: pointer; }

.results-info { margin-bottom: 16px; font-size: 14px; color: var(--text-muted); }

.loading-state, .empty-state { background: var(--surface); border-radius: 16px; padding: 48px; text-align: center; border: 1px solid var(--border); color: var(--text-muted); font-size: 15px; }

.candidates-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }

.candidate-card { background: var(--surface); border-radius: 16px; padding: 20px; border: 1px solid var(--border); box-shadow: var(--card-shadow); transition: transform 0.2s; display: flex; flex-direction: column; gap: 10px; }
.candidate-card:hover { transform: translateY(-2px); }

.candidate-header { display: flex; align-items: center; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border); }

.candidate-avatar { width: 48px; height: 48px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; flex-shrink: 0; }

.candidate-info h3 { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 2px; }
.candidate-info p { font-size: 12px; color: var(--text-muted); margin-bottom: 4px; }

.candidate-status { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
.candidate-status.pending { background: #fef9c3; color: #854d0e; }
.candidate-status.approved { background: #f0fdf4; color: #166534; }
.candidate-status.rejected { background: #fef2f2; color: #991b1b; }

.candidate-section h4 { font-size: 11px; font-weight: 700; margin-bottom: 4px; }

/* Rule Tags */
.rule-tag { font-size: 11px; font-weight: 700; padding: 2px 0; margin-bottom: 4px; }
.s-tag { color: #1d4ed8; }
.p-tag { color: #166534; }
.t-tag { color: #7e22ce; }
.w-tag { color: #c2410c; }
.c-tag { color: #854d0e; }

.tags { display: flex; flex-wrap: wrap; gap: 4px; }

/* Rule Tags */
.tag-skill { padding: 2px 7px; background: #eff6ff; color: #1d4ed8; border-radius: 20px; font-size: 10px; font-weight: 600; }
.tag-physical { padding: 2px 7px; background: #f0fdf4; color: #166534; border-radius: 20px; font-size: 10px; font-weight: 600; }
.tag-tech { padding: 2px 7px; background: #faf5ff; color: #7e22ce; border-radius: 20px; font-size: 10px; font-weight: 600; }
.tag-work { padding: 2px 7px; background: #fff7ed; color: #c2410c; border-radius: 20px; font-size: 10px; font-weight: 600; }
.tag-sensory { padding: 2px 7px; background: #fef9c3; color: #854d0e; border-radius: 20px; font-size: 10px; font-weight: 600; }

.tag-more { padding: 2px 7px; background: var(--bg); color: var(--text-muted); border-radius: 20px; font-size: 10px; font-weight: 600; border: 1px solid var(--border); }
.no-data { font-size: 11px; color: var(--text-muted); font-style: italic; }

.candidate-attachments { display: flex; gap: 6px; flex-wrap: wrap; }

.attachment-badge { padding: 3px 8px; background: var(--bg); border: 1px solid var(--border); border-radius: 20px; font-size: 10px; font-weight: 600; color: var(--primary); text-decoration: none; transition: all 0.2s; }
.attachment-badge:hover { border-color: var(--primary); background: var(--primary-light); }

.candidate-actions { display: flex; gap: 8px; margin-top: auto; }

.btn-view { flex: 1; padding: 8px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-view:hover { border-color: var(--primary); color: var(--primary); }

.btn-contact { flex: 1; padding: 8px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.btn-contact:hover { background: var(--primary-hover); }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
.modal { background: var(--surface); border-radius: 20px; width: 100%; max-width: 560px; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }

.modal-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 24px; border-bottom: 1px solid var(--border); }
.modal-profile { display: flex; align-items: center; gap: 16px; }

.modal-avatar { width: 56px; height: 56px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; }

.modal-header h2 { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.modal-header p { font-size: 13px; color: var(--text-muted); margin-bottom: 4px; }

.btn-close { padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); cursor: pointer; font-size: 14px; }

.modal-body { padding: 24px; display: flex; flex-direction: column; gap: 16px; }
.modal-section h4 { font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 8px; }

.attachments { display: flex; flex-direction: column; gap: 8px; }
.attachment-link { display: inline-block; padding: 8px 16px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 600; }
.attachment-link:hover { border-color: var(--primary); }

.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 20px 24px; border-top: 1px solid var(--border); }

.btn-cancel { padding: 10px 24px; border: 2px solid var(--border); border-radius: 8px; background: var(--surface); color: var(--text); font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-contact-modal { padding: 10px 24px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-contact-modal:hover { background: var(--primary-hover); }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .candidates-grid { grid-template-columns: 1fr; }
  .search-row { flex-direction: column; }
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

/* Candidate Avatar Image */
.candidate-avatar-img {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  border: 1px solid var(--border);
  flex-shrink: 0;
}
.candidate-location {
  font-size: 11px !important;
  color: var(--primary) !important;
  font-weight: 600;
  margin-bottom: 2px !important;
}

/* Modal Avatar & Location */
.modal-avatar-img {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--border);
  flex-shrink: 0;
}
.modal-location {
  font-size: 13px !important;
  color: var(--primary) !important;
  font-weight: 600;
  margin-bottom: 4px !important;
}

/* Modal extra details cards */
.modal-profile-section-card {
  padding: 14px;
  background: var(--bg);
  border-radius: 12px;
  border: 1px solid var(--border);
  margin-bottom: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.modal-profile-section-card h4 {
  font-size: 12px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 2px;
}
.modal-section-text {
  font-size: 12px;
  line-height: 1.5;
  color: var(--text);
}
.edu-timeline {
  display: flex;
  flex-direction: column;
  gap: 10px;
  position: relative;
  padding-left: 8px;
}
.edu-timeline-item {
  display: flex;
  gap: 12px;
}
.timeline-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--primary);
  margin-top: 4px;
  flex-shrink: 0;
}
.timeline-content {
  display: flex;
  flex-direction: column;
  gap: 1px;
}
.timeline-content h5 {
  font-size: 12px;
  font-weight: 600;
  color: var(--text);
}
.edu-level-badge {
  font-size: 9px;
  font-weight: 700;
  color: var(--primary);
  text-transform: uppercase;
}
.edu-duration {
  font-size: 10px;
  color: var(--text-muted);
}
</style>