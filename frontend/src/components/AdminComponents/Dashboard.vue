<script setup>
import { fetchDashboardStats } from '@/services/adminService'
import StatCard from './StatCard.vue'
import { ref } from 'vue'

const tab = defineModel('tab')

const response = await fetchDashboardStats()
const stats = ref(response.ok ? response.data : null)
const error = ref(response.ok ? '' : response.message || 'Unable to load dashboard stats')

function capitalizeWords(str) {
  if (!str) return ''
  return str
    .toLowerCase()
    .split(' ')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

function statusBadgeClass(status) {
  switch (status) {
    case 'approved':
      return 'badge-approved'
    case 'rejected':
      return 'badge-rejected'
    case 'pending':
    default:
      return 'badge-pending'
  }
}
</script>

<template>
  <div class="d-flex h-full">
    <main class="main-content flex-grow-1">
      <header class="top-navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-4">
          <div class="d-md-none d-flex align-items-center">
            <i class="fa-solid fa-bars fs-4 text-dark me-3" style="cursor: pointer"></i>
          </div>
          <span class="fs-4 fw-bold text-dark mb-0">Admin Dashboard</span>
        </div>
      </header>

      <div class="container-fluid py-4 px-3 px-md-4" style="max-width: 1280px">
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <template v-else-if="stats">
          <!-- Quick Stats Bento Grid -->
          <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
              <StatCard label="Total Users" :value="stats.users.total" icon="fa-users" accent="#006a61" />
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
              <StatCard label="Banned Users" :value="stats.users.banned" icon="fa-user-slash" accent="#ba1a1a" />
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
              <StatCard label="Pending Jobs" :value="stats.jobs.pending" icon="fa-hourglass-half" accent="#a15c00" />
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
              <StatCard label="Approved Jobs" :value="stats.jobs.approved" icon="fa-circle-check" accent="#006a61" />
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
              <StatCard label="Total Jobs" :value="stats.jobs.total" icon="fa-briefcase" accent="#0b1c30" />
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
              <StatCard label="Applications" :value="stats.applications.total" icon="fa-file-lines" accent="#0b1c30" />
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
              <StatCard label="Comments" :value="stats.comments.total" icon="fa-comments" accent="#0b1c30" />
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
              <StatCard label="Hidden Comments" :value="stats.comments.hidden" icon="fa-eye-slash" accent="#ba1a1a" />
            </div>
          </div>

          <!-- Recent Jobs -->
          <div class="dashboard-container mb-4">
            <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
              <h2 class="fs-5 fw-bold mb-0 text-dark">Recent Job Listings</h2>
              <button class="btn btn-action-dark d-flex align-items-center gap-2" @click="tab = 'jobs'">
                View All <i class="fa-solid fa-arrow-right small"></i>
              </button>
            </div>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0 text-start">
                <thead>
                  <tr class="table-light border-bottom">
                    <th class="px-4 py-3 small fw-semibold text-muted">Job Title</th>
                    <th class="px-4 py-3 small fw-semibold text-muted">Employer</th>
                    <th class="px-4 py-3 small fw-semibold text-muted">Moderation</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="job in stats.recent_jobs" :key="job.id">
                    <td class="px-4 py-3">
                      <p class="fw-semibold mb-0 text-dark">{{ capitalizeWords(job.title) }}</p>
                      <p class="small text-muted mb-0">{{ capitalizeWords(job.location) }}</p>
                    </td>
                    <td class="px-4 py-3 text-muted">{{ job.employer?.name || '—' }}</td>
                    <td class="px-4 py-3">
                      <span class="status-badge" :class="statusBadgeClass(job.moderation_status)">
                        {{ capitalizeWords(job.moderation_status) }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Recent Users -->
          <div class="dashboard-container">
            <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
              <h2 class="fs-5 fw-bold mb-0 text-dark">Recent Users</h2>
              <button class="btn btn-action-dark d-flex align-items-center gap-2" @click="tab = 'users'">
                View All <i class="fa-solid fa-arrow-right small"></i>
              </button>
            </div>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0 text-start">
                <thead>
                  <tr class="table-light border-bottom">
                    <th class="px-4 py-3 small fw-semibold text-muted">Name</th>
                    <th class="px-4 py-3 small fw-semibold text-muted">Role</th>
                    <th class="px-4 py-3 small fw-semibold text-muted">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in stats.recent_users" :key="user.id">
                    <td class="px-4 py-3">
                      <p class="fw-semibold mb-0 text-dark">{{ user.name }}</p>
                      <p class="small text-muted mb-0">{{ user.email }}</p>
                    </td>
                    <td class="px-4 py-3 text-muted">{{ capitalizeWords(user.role) }}</td>
                    <td class="px-4 py-3">
                      <span class="status-badge" :class="user.status === 'banned' ? 'badge-rejected' : 'badge-approved'">
                        {{ capitalizeWords(user.status) }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </template>
      </div>
    </main>
  </div>
</template>

<style scoped>
.main-content {
  min-height: 100vh;
}

.top-navbar {
  position: sticky;
  top: 0;
  z-index: 1020;
  background-color: #ffffff;
  border-bottom: 1px solid #c6c6cd;
  padding: 12px 24px;
}

.dashboard-container {
  background-color: #ffffff;
  border-radius: 12px;
  border: 1px solid #c6c6cd;
  overflow: hidden;
}

.btn-action-dark {
  background-color: #000000;
  border: none;
  color: #ffffff;
  font-weight: 600;
  font-size: 14px;
  padding: 8px 16px;
  border-radius: 8px;
  transition: opacity 0.2s;
}
.btn-action-dark:hover {
  color: #ffffff;
  opacity: 0.9;
}

.status-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}
.badge-approved {
  background-color: rgba(0, 106, 97, 0.12);
  color: #006a61;
}
.badge-pending {
  background-color: rgba(161, 92, 0, 0.12);
  color: #a15c00;
}
.badge-rejected {
  background-color: #ffdad6;
  color: #ba1a1a;
}
</style>
