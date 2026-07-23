<template>
  <div class="dashboard">
    <PWDSidebar />

    <main class="main-content">
      <div class="topbar">
        <div>
          <h1>Mailbox</h1>
          <p>Your messages and job applications</p>
        </div>
        <button class="theme-toggle" @click="$emit('toggleTheme')">
          {{ isDark ? '☀️ Light' : '🌙 Dark' }}
        </button>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button :class="['tab', activeTab === 'inbox' ? 'active' : '']" @click="activeTab = 'inbox'">
          Inbox
          <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
        </button>
        <button :class="['tab', activeTab === 'applications' ? 'active' : '']" @click="activeTab = 'applications'">
          My Applications
        </button>
        <button :class="['tab', activeTab === 'sent' ? 'active' : '']" @click="activeTab = 'sent'">
          Sent
        </button>
      </div>

      <!-- Inbox Tab -->
      <div v-if="activeTab === 'inbox'">
        <div v-if="inbox.length === 0" class="empty-state">
          <p>No messages yet.</p>
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

      <!-- Applications Tab -->
      <div v-if="activeTab === 'applications'">
        <div v-if="applications.length === 0" class="empty-state">
          <p>No applications yet. Start applying for jobs!</p>
          <router-link to="/pwd/jobs" class="btn-primary">Search Jobs</router-link>
        </div>
        <div v-else class="applications-list">
          <div v-for="app in applications" :key="app.id" class="application-item">
            <div class="app-info">
              <h4>{{ app.job?.job_title || 'Job' }}</h4>
              <p>
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 4px; color: var(--text-muted);"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><line x1="9" y1="16" x2="15" y2="16"/><line x1="15" y1="16" x2="15" y2="22"/></svg>
                {{ app.job?.employer?.company_name || 'Company' }}
              </p>
              <p>
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 4px; color: var(--text-muted);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ app.job?.location }}
              </p>
            </div>
            <div class="app-right">
              <div :class="['app-status', app.application_status]">
                {{ app.application_status }}
              </div>
              <p class="app-date">{{ app.created_at?.slice(0, 10) }}</p>
              <button @click="messageEmployer(app.job?.employer)" class="btn-msg" style="margin-top: 8px;">
                Message Employer
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sent Tab -->
      <div v-if="activeTab === 'sent'">
        <div v-if="sent.length === 0" class="empty-state">
          <p>No sent messages yet.</p>
        </div>
        <div v-else class="messages-list">
          <div v-for="msg in sent" :key="msg.id" class="message-item" @click="openMessage(msg)">
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
          <div v-if="activeConversation.loading" class="chat-loading">
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
              <button @click="sendQuickReply" class="btn-send" :disabled="activeConversation.sending || !activeConversation.replyBody.trim()">
                {{ activeConversation.sending ? 'Sending...' : 'Send Reply' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reply/Compose Modal -->
    <div v-if="showCompose" class="modal-overlay" @click.self="showCompose = false">
      <div class="modal">
        <div class="modal-header">
          <h2>{{ replyTo ? 'Reply' : 'New Message' }}</h2>
          <button @click="showCompose = false" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>To</label>
            <input v-model="compose.receiver_name" type="text" class="form-input" disabled/>
          </div>
          <div class="form-group">
            <label>Subject</label>
            <input v-model="compose.subject" type="text" class="form-input" placeholder="Subject..."/>
          </div>
          <div class="form-group">
            <label>Message</label>
            <textarea v-model="compose.body" class="form-input textarea" placeholder="Type your message..."></textarea>
          </div>
          <p v-if="sendSuccess" class="success-msg">Message sent!</p>
          <p v-if="sendError" class="error-msg">{{ sendError }}</p>
        </div>
        <div class="modal-footer">
          <button @click="showCompose = false" class="btn-cancel">Cancel</button>
          <button @click="sendMessage" class="btn-send" :disabled="sending">
            {{ sending ? 'Sending...' : 'Send' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'PWDMailbox',
  props: { isDark: Boolean },
  emits: ['toggleTheme'],
  data() {
    return {
      activeTab: 'inbox',
      inbox: [],
      sent: [],
      applications: [],
      unreadCount: 0,
      selectedMessage: null,
      showCompose: false,
      replyTo: null,
      compose: { receiver_id: null, receiver_name: '', subject: '', body: '' },
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
  watch: {
    '$route'(to, from) {
      this.checkQueryParams()
    }
  },
  mounted() {
    this.loadAll()
    this.checkQueryParams()
  },
  methods: {
    async loadAll() {
      this.loading = true
      try {
        const token = localStorage.getItem('token')
        if (!token) { this.$router.push('/login'); return }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        const [msgRes, appRes] = await Promise.all([
          axios.get('/api/messages'),
          axios.get('/api/pwd/applications')
        ])
        this.inbox = msgRes.data.inbox || []
        this.sent = msgRes.data.sent || []
        this.unreadCount = msgRes.data.unread_count || 0
        this.applications = appRes.data.applications || []
      } catch (err) {
        console.log('Failed to load mailbox')
      }
      this.loading = false
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
        this.loadAll()
        this.scrollToBottom()
      } catch (err) {
        alert('Failed to send reply. Please try again.')
      }
      this.activeConversation.sending = false
    },
    messageEmployer(employer) {
      if (!employer) return
      this.replyTo = null
      this.compose = {
        receiver_id: employer.user_id,
        receiver_name: employer.company_name,
        subject: 'Inquiry regarding Job Application',
        body: ''
      }
      this.showCompose = true
      this.activeTab = 'inbox'
    },
    checkQueryParams() {
      const query = this.$route.query
      if (query.tab) {
        this.activeTab = query.tab
      }
      if (query.compose === 'true' || query.compose === true) {
        this.activeTab = 'inbox'
        this.replyTo = null
        this.compose = {
          receiver_id: query.receiver_id ? parseInt(query.receiver_id) : null,
          receiver_name: query.receiver_name || '',
          subject: query.subject || 'Inquiry regarding Job Vacancy',
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
      this.showCompose = true
    },
    async sendMessage() {
      this.sending = true
      this.sendSuccess = ''
      this.sendError = ''
      try {
        await axios.post('/api/messages', {
          receiver_id: this.compose.receiver_id,
          subject: this.compose.subject,
          body: this.compose.body,
        })
        this.sendSuccess = 'Message sent successfully!'
        setTimeout(() => {
          this.showCompose = false
          this.loadAll()
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
  position: relative;
}

.nav-item:hover { background: var(--primary-light); color: var(--primary); }
.nav-item.active { background: var(--primary); color: white; }

.nav-badge {
  background: #ef4444;
  color: white;
  border-radius: 20px;
  padding: 1px 6px;
  font-size: 11px;
  margin-left: auto;
}

.btn-logout {
  padding: 12px 16px;
  background: #fef2f2;
  color: #ef4444;
  border: 1px solid #fecaca;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
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
}

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

.empty-state {
  background: var(--surface);
  border-radius: 16px;
  padding: 48px;
  text-align: center;
  border: 1px solid var(--border);
  color: var(--text-muted);
  font-size: 15px;
}

.btn-primary {
  display: inline-block;
  margin-top: 16px;
  padding: 10px 24px;
  background: var(--primary);
  color: white;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
}

/* Messages */
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
  position: relative;
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

/* Applications */
.applications-list { display: flex; flex-direction: column; gap: 12px; }

.application-item {
  background: var(--surface);
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: 1px solid var(--border);
}

.application-item h4 { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.application-item p { font-size: 13px; color: var(--text-muted); margin-bottom: 2px; }

.app-right { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }

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

.modal-body { padding: 24px; }
.msg-body { font-size: 14px; color: var(--text); line-height: 1.8; white-space: pre-wrap; }

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

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
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

.btn-msg {
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

.btn-msg:hover {
  background: var(--primary);
  color: white;
}
</style>