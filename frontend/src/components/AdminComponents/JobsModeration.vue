<script setup>
import { approveJob, deleteAdminJob, fetchAdminJobs, rejectJob } from '@/services/adminService'
import { onMounted, ref } from 'vue'

const emit = defineEmits(['toggle-sidebar'])

const jobs = ref([])
const loading = ref(true)
const message = ref('')
const error = ref('')
const activeFilter = ref('')
const rejectingJobId = ref(null)
const rejectionReason = ref('')

const filters = [
  { value: '', label: 'All' },
  { value: 'pending', label: 'Pending' },
  { value: 'approved', label: 'Approved' },
  { value: 'rejected', label: 'Rejected' },
]

function capitalizeWords(str) {
  if (!str) return ''
  return str
    .toLowerCase()
    .split(' ')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

async function loadJobs() {
  loading.value = true
  error.value = ''
  const response = await fetchAdminJobs(
    activeFilter.value ? { moderation_status: activeFilter.value } : {},
  )

  if (response.ok) {
    jobs.value = Array.isArray(response.data?.data) ? response.data.data : []
  } else {
    error.value = response.message || 'Unable to load job listings.'
  }

  loading.value = false
}

function setFilter(value) {
  activeFilter.value = value
  loadJobs()
}

onMounted(loadJobs)

async function handleApprove(jobId) {
  message.value = ''
  error.value = ''
  const response = await approveJob(jobId)

  if (response.ok) {
    message.value = 'Job approved successfully.'
    await loadJobs()
  } else {
    error.value = response.message || 'Unable to approve job.'
  }
}

function openRejectPrompt(jobId) {
  rejectingJobId.value = jobId
  rejectionReason.value = ''
}

function cancelReject() {
  rejectingJobId.value = null
  rejectionReason.value = ''
}

async function confirmReject() {
  message.value = ''
  error.value = ''
  const response = await rejectJob(rejectingJobId.value, rejectionReason.value)

  if (response.ok) {
    message.value = 'Job rejected.'
    cancelReject()
    await loadJobs()
  } else {
    error.value = response.message || 'Unable to reject job.'
  }
}

async function handleDelete(jobId) {
  message.value = ''
  error.value = ''
  const response = await deleteAdminJob(jobId)

  if (response.ok) {
    jobs.value = jobs.value.filter((job) => job.id !== jobId)
    message.value = 'Job deleted successfully.'
  } else {
    error.value = response.message || 'Unable to delete job.'
  }
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
          <div class="d-md-none d-flex align-items-center" @click="emit('toggle-sidebar')" style="cursor: pointer">
            <i class="fa-solid fa-bars fs-4 text-dark me-3"></i>
          </div>
          <span class="fs-4 fw-bold text-dark mb-0">Job Moderation</span>
        </div>
      </header>

      <div class="container-fluid py-4 px-3 px-md-4" style="max-width: 1280px">
        <div v-if="message || error" class="mb-3">
          <div v-if="message" class="alert alert-success mb-2">{{ message }}</div>
          <div v-if="error" class="alert alert-danger mb-0">{{ error }}</div>
        </div>

        <div class="dashboard-container">
          <div class="p-4 d-flex justify-content-between align-items-center border-bottom flex-wrap gap-3">
            <h2 class="fs-5 fw-bold mb-0 text-dark">Job Listings</h2>
            <div class="d-flex gap-2 flex-wrap">
              <button
                v-for="f in filters"
                :key="f.value"
                class="btn btn-filter"
                :class="{ 'btn-filter-active': activeFilter === f.value }"
                @click="setFilter(f.value)"
              >
                {{ f.label }}
              </button>
            </div>
          </div>

          <div v-if="loading" class="p-5 text-center text-muted">Loading job listings...</div>
          <div v-else-if="jobs.length === 0" class="p-5 text-center text-muted">No job listings found.</div>
          <div v-else class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-start">
              <thead>
                <tr class="table-light border-bottom">
                  <th class="px-4 py-3 small fw-semibold text-muted" style="width: 35%">Job Title</th>
                  <th class="px-4 py-3 small fw-semibold text-muted">Employer</th>
                  <th class="px-4 py-3 small fw-semibold text-muted">Moderation</th>
                  <th class="px-4 py-3 small fw-semibold text-muted">Listing</th>
                  <th class="px-4 py-3 small fw-semibold text-muted text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="job in jobs" :key="job.id">
                  <td class="px-4 py-3">
                    <p class="fw-semibold mb-0 text-dark">{{ capitalizeWords(job.title) }}</p>
                    <p class="small text-muted mb-0">
                      {{ capitalizeWords(job.industry) }} &bull; {{ capitalizeWords(job.location) }}
                    </p>
                    <p v-if="job.rejection_reason" class="small text-danger mb-0 mt-1">
                      <i class="fa-solid fa-circle-exclamation me-1"></i>{{ job.rejection_reason }}
                    </p>
                  </td>
                  <td class="px-4 py-3 text-muted">
                    {{ job.employer?.name || '—' }}
                    <div class="small">{{ job.employer?.email }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="status-badge" :class="statusBadgeClass(job.moderation_status)">
                      {{ capitalizeWords(job.moderation_status) }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-muted">{{ capitalizeWords(job.status) }}</td>
                  <td class="px-4 py-3 text-end">
                    <div class="d-flex justify-content-end gap-2">
                      <button
                        v-if="job.moderation_status !== 'approved'"
                        class="btn-table-action btn-table-approve"
                        title="Approve"
                        @click="handleApprove(job.id)"
                      >
                        <i class="fa-solid fa-check"></i>
                      </button>
                      <button
                        v-if="job.moderation_status !== 'rejected'"
                        class="btn-table-action btn-table-reject"
                        title="Reject"
                        @click="openRejectPrompt(job.id)"
                      >
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                      <button
                        class="btn-table-action btn-table-delete"
                        title="Delete"
                        @click="handleDelete(job.id)"
                      >
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>

    <!-- Reject reason modal -->
    <div v-if="rejectingJobId" class="modal-backdrop-custom" @click.self="cancelReject">
      <div class="modal-box">
        <h3 class="fs-6 fw-bold mb-3">Reject job listing</h3>
        <label class="form-label small fw-semibold">Reason (optional, shown to employer)</label>
        <textarea
          v-model="rejectionReason"
          class="form-control mb-3"
          rows="3"
          placeholder="e.g. Listing did not meet platform guidelines"
        ></textarea>
        <div class="d-flex justify-content-end gap-2">
          <button class="btn-secondary-custom" @click="cancelReject">Cancel</button>
          <button class="btn-table-reject-solid" @click="confirmReject">Reject Job</button>
        </div>
      </div>
    </div>
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

.btn-filter {
  background-color: #e5eeff;
  border: none;
  color: #0b1c30;
  font-weight: 600;
  font-size: 13px;
  padding: 6px 14px;
  border-radius: 999px;
  transition: background-color 0.2s;
}
.btn-filter:hover {
  background-color: #dce9ff;
}
.btn-filter-active {
  background-color: #86f2e4;
  color: #005049;
}

.btn-table-action {
  background: none;
  border: none;
  color: #45464d;
  padding: 6px 10px;
  border-radius: 6px;
  transition: all 0.2s;
}
.btn-table-approve:hover {
  background-color: rgba(0, 106, 97, 0.12);
  color: #006a61;
}
.btn-table-reject:hover {
  background-color: #ffe9c7;
  color: #a15c00;
}
.btn-table-delete:hover {
  background-color: #ffdad6;
  color: #ba1a1a;
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

.modal-backdrop-custom {
  position: fixed;
  inset: 0;
  background: rgba(11, 28, 48, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  padding: 1rem;
}
.modal-box {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 24px 60px rgba(11, 28, 48, 0.2);
}

.btn-secondary-custom {
  background: none;
  border: none;
  color: #45464d;
  font-weight: 600;
  padding: 10px 20px;
  border-radius: 8px;
}
.btn-secondary-custom:hover {
  background-color: #e5eeff;
}

.btn-table-reject-solid {
  background-color: #ba1a1a;
  color: #fff;
  border: none;
  font-weight: 600;
  padding: 10px 20px;
  border-radius: 8px;
}
.btn-table-reject-solid:hover {
  opacity: 0.9;
}
</style>
