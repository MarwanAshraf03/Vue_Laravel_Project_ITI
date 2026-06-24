<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import Cookies from 'js-cookie'
import { fetchCandidateApplications } from '@/services/jobsService'

const router = useRouter()
const applications = ref([])
const loading = ref(false)
const error = ref(null)
const filterStatus = ref('all')

onMounted(async () => {
  loading.value = true
  const result = await fetchCandidateApplications()
  if (result.ok) {
    applications.value = result.data
  } else {
    error.value = result.message || 'Failed to load applications.'
  }
  loading.value = false
})

function logout() {
  Cookies.remove('token')
  router.push({ name: 'user_login' })
}

const filtered = computed(() => {
  if (filterStatus.value === 'all') return applications.value
  return applications.value.filter((a) => a.status === filterStatus.value)
})

function formatDate(dt) {
  if (!dt) return '—'
  return new Date(dt).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const statusConfig = {
  pending:  { label: 'Pending',  cls: 'status-pending'  },
  approved: { label: 'Approved', cls: 'status-approved' },
  rejected: { label: 'Rejected', cls: 'status-rejected' },
}
</script>

<template>
  <main class="apps-shell">
    <!-- NAV -->
    <nav class="apps-nav">
      <div>
        <p class="brand">Jobify</p>
        <small class="muted">My Applications</small>
      </div>
      <div class="nav-actions">
        <RouterLink class="nav-link" :to="{ name: 'candidate_home' }">Browse Jobs</RouterLink>
        <RouterLink class="nav-link" :to="{ name: 'candidate_profile' }">Profile</RouterLink>
        <button class="logout-btn" type="button" @click="logout">Logout</button>
      </div>
    </nav>

    <!-- PAGE BODY -->
    <div class="page-body">
      <!-- HEADER ROW -->
      <div class="page-header">
        <div>
          <h1 class="page-title">My Applications</h1>
          <p class="page-subtitle">Track the status of every job you've applied to.</p>
        </div>

        <!-- FILTER PILLS -->
        <div class="filter-pills">
          <button
            v-for="opt in ['all', 'pending', 'approved', 'rejected']"
            :key="opt"
            class="pill"
            :class="{ active: filterStatus === opt }"
            @click="filterStatus = opt"
          >
            {{ opt.charAt(0).toUpperCase() + opt.slice(1) }}
          </button>
        </div>
      </div>

      <!-- LOADING -->
      <div v-if="loading" class="center-msg">
        <span class="spinner"></span>
        <p>Loading your applications…</p>
      </div>

      <!-- ERROR -->
      <div v-else-if="error" class="center-msg error-msg">{{ error }}</div>

      <!-- EMPTY -->
      <div v-else-if="filtered.length === 0" class="empty-state">
        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="empty-icon">
          <rect x="8" y="16" width="48" height="38" rx="5" stroke="currentColor" stroke-width="3"/>
          <path d="M22 16V12a10 10 0 0 1 20 0v4" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
          <path d="M22 34h20M22 42h12" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        </svg>
        <p v-if="filterStatus === 'all'">You haven't applied to any jobs yet.</p>
        <p v-else>No <strong>{{ filterStatus }}</strong> applications.</p>
        <RouterLink class="cta-btn" :to="{ name: 'candidate_home' }">Browse Jobs →</RouterLink>
      </div>

      <!-- APPLICATIONS GRID -->
      <div v-else class="apps-grid">
        <article
          v-for="app in filtered"
          :key="app.id"
          class="app-card"
          :class="app.status"
        >
          <!-- CARD ACCENT BAR -->
          <div class="accent-bar" :class="app.status"></div>

          <!-- CARD CONTENT -->
          <div class="card-body">
            <div class="card-top">
              <div class="job-info">
                <h2 class="job-title">{{ app.job_listing?.title ?? 'Job' }}</h2>
                <p class="company-name">{{ app.job_listing?.company_name ?? '—' }}</p>
                <div class="meta-row">
                  <span v-if="app.job_listing?.location" class="meta-chip">
                    📍 {{ app.job_listing.location }}
                  </span>
                  <span v-if="app.job_listing?.work_type" class="meta-chip">
                    🏢 {{ app.job_listing.work_type }}
                  </span>
                </div>
              </div>

              <!-- STATUS BADGE -->
              <span class="status-badge" :class="statusConfig[app.status]?.cls">
                {{ statusConfig[app.status]?.label ?? app.status }}
              </span>
            </div>

            <hr class="divider" />

            <!-- APPLICATION DETAILS -->
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Applied As</span>
                <span class="detail-value">{{ app.candidate_name }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Email</span>
                <span class="detail-value">{{ app.email }}</span>
              </div>
              <div class="detail-item" v-if="app.phone">
                <span class="detail-label">Phone</span>
                <span class="detail-value">{{ app.phone }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Applied On</span>
                <span class="detail-value">{{ formatDate(app.created_at) }}</span>
              </div>
              <div class="detail-item" v-if="app.reviewed_at">
                <span class="detail-label">Reviewed On</span>
                <span class="detail-value">{{ formatDate(app.reviewed_at) }}</span>
              </div>
            </div>

            <!-- MESSAGE / COVER LETTER -->
            <div v-if="app.message" class="message-block">
              <span class="detail-label">Cover Note</span>
              <p class="message-text">{{ app.message }}</p>
            </div>

            <!-- RESUME LINK -->
            <div v-if="app.resume_path" class="resume-row">
              <a
                :href="`${$env?.VITE_API_BASE_URL ?? ''}/storage/${app.resume_path}`"
                target="_blank"
                class="resume-link"
              >
                📄 View Resume
              </a>
            </div>
          </div>
        </article>
      </div>
    </div>
  </main>
</template>

<style scoped>
/* ── Shell ──────────────────────────────────────────────── */
.apps-shell {
  min-height: 100%;
  padding: 1.25rem;
  background:
    radial-gradient(circle at top left,  rgba(0, 106, 97, 0.14), transparent 28%),
    radial-gradient(circle at top right, rgba(123, 157, 255, 0.16), transparent 28%),
    linear-gradient(180deg, #f7fbff 0%, #eef4ff 100%);
  color: #0b1c30;
  font-family: 'Inter', system-ui, sans-serif;
}

/* ── Nav ────────────────────────────────────────────────── */
.apps-nav,
.page-body {
  max-width: 1100px;
  margin: 0 auto;
}

.apps-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.25rem;
  margin-bottom: 1.5rem;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 22px;
  box-shadow: 0 18px 50px rgba(11, 28, 48, 0.08);
}

.brand { margin: 0; font-size: 1.4rem; font-weight: 800; }
.muted { color: #5f6b7c; }

.nav-actions { display: flex; align-items: center; gap: 0.75rem; }

.nav-link,
.logout-btn {
  border-radius: 999px;
  padding: 0.6rem 1.1rem;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.875rem;
  transition: background 0.15s, transform 0.15s;
}

.nav-link {
  color: #0b1c30;
  background: #f3f7ff;
  border: 1px solid rgba(11, 28, 48, 0.1);
}
.nav-link:hover { background: #e4edff; transform: translateY(-1px); }

.logout-btn {
  border: none;
  background: #0b1c30;
  color: #fff;
  cursor: pointer;
}
.logout-btn:hover { background: #1a3352; transform: translateY(-1px); }

/* ── Page Header ────────────────────────────────────────── */
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2rem;
}

.page-title  { margin: 0 0 0.25rem; font-size: 1.9rem; font-weight: 800; }
.page-subtitle { margin: 0; color: #5f6b7c; }

/* ── Filter Pills ───────────────────────────────────────── */
.filter-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }

.pill {
  padding: 0.45rem 1rem;
  border-radius: 999px;
  border: 1.5px solid rgba(11,28,48,0.12);
  background: #fff;
  color: #5f6b7c;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}
.pill:hover  { background: #eef4ff; color: #0b1c30; }
.pill.active { background: #0b1c30; color: #fff; border-color: #0b1c30; }

/* ── Loading / Empty ────────────────────────────────────── */
.center-msg {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  padding: 4rem;
  color: #5f6b7c;
}

.error-msg { color: #c0392b; }

.spinner {
  width: 36px; height: 36px;
  border: 3px solid rgba(11,28,48,0.12);
  border-top-color: #0b1c30;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.empty-state {
  text-align: center;
  padding: 5rem 2rem;
  color: #5f6b7c;
}
.empty-icon { width: 64px; height: 64px; margin-bottom: 1rem; opacity: 0.45; }
.empty-state p { font-size: 1.05rem; margin-bottom: 1.5rem; }

.cta-btn {
  display: inline-block;
  padding: 0.75rem 1.75rem;
  background: #0b1c30;
  color: #fff;
  border-radius: 999px;
  text-decoration: none;
  font-weight: 700;
  transition: background 0.15s, transform 0.15s;
}
.cta-btn:hover { background: #1a3352; transform: translateY(-2px); }

/* ── Grid ───────────────────────────────────────────────── */
.apps-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 1.5rem;
}

/* ── Card ───────────────────────────────────────────────── */
.app-card {
  position: relative;
  background: #fff;
  border-radius: 18px;
  border: 1px solid rgba(11,28,48,0.08);
  box-shadow: 0 4px 20px rgba(11,28,48,0.06);
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}
.app-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 36px rgba(11,28,48,0.12);
}

/* Accent bar on left edge */
.accent-bar {
  position: absolute;
  top: 0; left: 0;
  width: 5px; height: 100%;
}
.accent-bar.pending  { background: linear-gradient(180deg, #f59e0b, #fbbf24); }
.accent-bar.approved { background: linear-gradient(180deg, #10b981, #34d399); }
.accent-bar.rejected { background: linear-gradient(180deg, #ef4444, #f87171); }

.card-body { padding: 1.4rem 1.4rem 1.4rem 1.8rem; }

.card-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
}

.job-info { flex: 1; }
.job-title { margin: 0 0 0.2rem; font-size: 1.1rem; font-weight: 700; color: #0b1c30; }
.company-name { margin: 0 0 0.5rem; font-size: 0.9rem; color: #5f6b7c; font-weight: 500; }

.meta-row { display: flex; flex-wrap: wrap; gap: 0.4rem; }
.meta-chip {
  font-size: 0.78rem;
  background: #f0f4ff;
  color: #3a5a8a;
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
  font-weight: 500;
}

/* Status Badge */
.status-badge {
  flex-shrink: 0;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.3rem 0.85rem;
  border-radius: 999px;
  letter-spacing: 0.02em;
  text-transform: uppercase;
}
.status-pending  { background: #fef3c7; color: #92400e; }
.status-approved { background: #d1fae5; color: #065f46; }
.status-rejected { background: #fee2e2; color: #991b1b; }

/* Divider */
.divider { border: none; border-top: 1px solid rgba(11,28,48,0.07); margin: 0.85rem 0; }

/* Detail Grid */
.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.65rem 1rem;
  margin-bottom: 0.75rem;
}
.detail-item  { display: flex; flex-direction: column; gap: 0.15rem; }
.detail-label { font-size: 0.72rem; font-weight: 600; color: #8fa0b3; text-transform: uppercase; letter-spacing: 0.05em; }
.detail-value { font-size: 0.88rem; color: #0b1c30; font-weight: 500; }

/* Message block */
.message-block { margin-top: 0.75rem; }
.message-text {
  margin: 0.25rem 0 0;
  font-size: 0.875rem;
  color: #3d5166;
  line-height: 1.55;
  padding: 0.6rem 0.85rem;
  background: #f7fbff;
  border-left: 3px solid rgba(11,28,48,0.15);
  border-radius: 0 8px 8px 0;
}

/* Resume */
.resume-row { margin-top: 0.85rem; }
.resume-link {
  font-size: 0.85rem;
  font-weight: 600;
  color: #2563eb;
  text-decoration: none;
  transition: color 0.15s;
}
.resume-link:hover { color: #1d4ed8; text-decoration: underline; }

/* Responsive */
@media (max-width: 680px) {
  .apps-nav { flex-direction: column; align-items: flex-start; }
  .nav-actions { width: 100%; flex-wrap: wrap; }
  .page-header { flex-direction: column; }
  .apps-grid { grid-template-columns: 1fr; }
  .detail-grid { grid-template-columns: 1fr; }
}
</style>
