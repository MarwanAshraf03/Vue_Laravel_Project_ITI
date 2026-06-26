<script setup>
import { deleteAdminComment, fetchAdminComments, hideComment, unhideComment } from '@/services/adminService'
import { onMounted, ref } from 'vue'

const emit = defineEmits(['toggle-sidebar'])

const comments = ref([])
const loading = ref(true)
const message = ref('')
const error = ref('')
const statusFilter = ref('')

const filters = [
  { value: '', label: 'All' },
  { value: 'visible', label: 'Visible' },
  { value: 'hidden', label: 'Hidden' },
]

async function loadComments() {
  loading.value = true
  error.value = ''
  const response = await fetchAdminComments(statusFilter.value ? { status: statusFilter.value } : {})

  if (response.ok) {
    comments.value = Array.isArray(response.data?.data) ? response.data.data : []
  } else {
    error.value = response.message || 'Unable to load comments.'
  }

  loading.value = false
}

function setFilter(value) {
  statusFilter.value = value
  loadComments()
}

onMounted(loadComments)

async function handleHide(commentId) {
  message.value = ''
  error.value = ''
  const response = await hideComment(commentId)

  if (response.ok) {
    message.value = 'Comment hidden.'
    await loadComments()
  } else {
    error.value = response.message || 'Unable to hide comment.'
  }
}

async function handleUnhide(commentId) {
  message.value = ''
  error.value = ''
  const response = await unhideComment(commentId)

  if (response.ok) {
    message.value = 'Comment restored.'
    await loadComments()
  } else {
    error.value = response.message || 'Unable to restore comment.'
  }
}

async function handleDelete(commentId) {
  message.value = ''
  error.value = ''
  const response = await deleteAdminComment(commentId)

  if (response.ok) {
    comments.value = comments.value.filter((c) => c.id !== commentId)
    message.value = 'Comment deleted successfully.'
  } else {
    error.value = response.message || 'Unable to delete comment.'
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
          <span class="fs-4 fw-bold text-dark mb-0">Comment Moderation</span>
        </div>
      </header>

      <div class="container-fluid py-4 px-3 px-md-4" style="max-width: 1280px">
        <div v-if="message || error" class="mb-3">
          <div v-if="message" class="alert alert-success mb-2">{{ message }}</div>
          <div v-if="error" class="alert alert-danger mb-0">{{ error }}</div>
        </div>

        <div class="dashboard-container">
          <div class="p-4 d-flex justify-content-between align-items-center border-bottom flex-wrap gap-3">
            <h2 class="fs-5 fw-bold mb-0 text-dark">Comments</h2>
            <div class="d-flex gap-2 flex-wrap">
              <button
                v-for="f in filters"
                :key="f.value"
                class="btn btn-filter"
                :class="{ 'btn-filter-active': statusFilter === f.value }"
                @click="setFilter(f.value)"
              >
                {{ f.label }}
              </button>
            </div>
          </div>

          <div v-if="loading" class="p-5 text-center text-muted">Loading comments...</div>
          <div v-else-if="comments.length === 0" class="p-5 text-center text-muted">No comments found.</div>
          <div v-else class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-start">
              <thead>
                <tr class="table-light border-bottom">
                  <th class="px-4 py-3 small fw-semibold text-muted">Author</th>
                  <th class="px-4 py-3 small fw-semibold text-muted" style="width: 35%">Comment</th>
                  <th class="px-4 py-3 small fw-semibold text-muted">Job</th>
                  <th class="px-4 py-3 small fw-semibold text-muted">Status</th>
                  <th class="px-4 py-3 small fw-semibold text-muted text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="comment in comments" :key="comment.id">
                  <td class="px-4 py-3">
                    <p class="fw-semibold mb-0 text-dark">{{ comment.user?.name || 'Unknown' }}</p>
                    <p class="small text-muted mb-0">{{ comment.user?.email }}</p>
                  </td>
                  <td class="px-4 py-3 text-dark">{{ comment.content }}</td>
                  <td class="px-4 py-3 text-muted">{{ comment.job_listing?.title || '—' }}</td>
                  <td class="px-4 py-3">
                    <span class="status-badge" :class="comment.status === 'hidden' ? 'badge-rejected' : 'badge-approved'">
                      {{ comment.status === 'hidden' ? 'Hidden' : 'Visible' }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-end">
                    <div class="d-flex justify-content-end gap-2">
                      <button
                        v-if="comment.status !== 'hidden'"
                        class="btn-table-action btn-table-reject"
                        title="Hide comment"
                        @click="handleHide(comment.id)"
                      >
                        <i class="fa-solid fa-eye-slash"></i>
                      </button>
                      <button
                        v-else
                        class="btn-table-action btn-table-approve"
                        title="Restore comment"
                        @click="handleUnhide(comment.id)"
                      >
                        <i class="fa-solid fa-eye"></i>
                      </button>
                      <button
                        class="btn-table-action btn-table-delete"
                        title="Delete comment"
                        @click="handleDelete(comment.id)"
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
.badge-rejected {
  background-color: #ffdad6;
  color: #ba1a1a;
}
</style>
