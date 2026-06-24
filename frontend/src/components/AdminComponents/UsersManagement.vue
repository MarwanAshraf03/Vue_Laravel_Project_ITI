<script setup>
import { banUser, deleteAdminUser, fetchAdminUsers, unbanUser } from '@/services/adminService'
import { onMounted, ref } from 'vue'

const emit = defineEmits(['toggle-sidebar'])

const users = ref([])
const loading = ref(true)
const message = ref('')
const error = ref('')
const roleFilter = ref('')
const search = ref('')

const roleFilters = [
  { value: '', label: 'All Roles' },
  { value: 'candidate', label: 'Candidates' },
  { value: 'employer', label: 'Employers' },
  { value: 'admin', label: 'Admins' },
]

function capitalizeWords(str) {
  if (!str) return ''
  return str
    .toLowerCase()
    .split(' ')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

async function loadUsers() {
  loading.value = true
  error.value = ''
  const response = await fetchAdminUsers({
    role: roleFilter.value || undefined,
    search: search.value || undefined,
  })

  if (response.ok) {
    users.value = Array.isArray(response.data?.data) ? response.data.data : []
  } else {
    error.value = response.message || 'Unable to load users.'
  }

  loading.value = false
}

function setRoleFilter(value) {
  roleFilter.value = value
  loadUsers()
}

let searchTimeout
function handleSearchInput() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(loadUsers, 350)
}

onMounted(loadUsers)

async function handleBan(userId) {
  message.value = ''
  error.value = ''
  const response = await banUser(userId)

  if (response.ok) {
    message.value = 'User banned successfully.'
    await loadUsers()
  } else {
    error.value = response.message || 'Unable to ban user.'
  }
}

async function handleUnban(userId) {
  message.value = ''
  error.value = ''
  const response = await unbanUser(userId)

  if (response.ok) {
    message.value = 'User unbanned successfully.'
    await loadUsers()
  } else {
    error.value = response.message || 'Unable to unban user.'
  }
}

async function handleDelete(userId) {
  message.value = ''
  error.value = ''
  const response = await deleteAdminUser(userId)

  if (response.ok) {
    users.value = users.value.filter((u) => u.id !== userId)
    message.value = 'User deleted successfully.'
  } else {
    error.value = response.message || 'Unable to delete user.'
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
          <span class="fs-4 fw-bold text-dark mb-0">User Management</span>
        </div>
      </header>

      <div class="container-fluid py-4 px-3 px-md-4" style="max-width: 1280px">
        <div v-if="message || error" class="mb-3">
          <div v-if="message" class="alert alert-success mb-2">{{ message }}</div>
          <div v-if="error" class="alert alert-danger mb-0">{{ error }}</div>
        </div>

        <div class="dashboard-container">
          <div class="p-4 d-flex justify-content-between align-items-center border-bottom flex-wrap gap-3">
            <h2 class="fs-5 fw-bold mb-0 text-dark">Users</h2>
            <div class="d-flex gap-2 flex-wrap align-items-center">
              <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass text-muted small"></i>
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search name or email..."
                  @input="handleSearchInput"
                />
              </div>
              <button
                v-for="f in roleFilters"
                :key="f.value"
                class="btn btn-filter"
                :class="{ 'btn-filter-active': roleFilter === f.value }"
                @click="setRoleFilter(f.value)"
              >
                {{ f.label }}
              </button>
            </div>
          </div>

          <div v-if="loading" class="p-5 text-center text-muted">Loading users...</div>
          <div v-else-if="users.length === 0" class="p-5 text-center text-muted">No users found.</div>
          <div v-else class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-start">
              <thead>
                <tr class="table-light border-bottom">
                  <th class="px-4 py-3 small fw-semibold text-muted">User</th>
                  <th class="px-4 py-3 small fw-semibold text-muted">Role</th>
                  <th class="px-4 py-3 small fw-semibold text-muted">Status</th>
                  <th class="px-4 py-3 small fw-semibold text-muted">Joined</th>
                  <th class="px-4 py-3 small fw-semibold text-muted text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
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
                  <td class="px-4 py-3 text-muted">
                    {{ new Date(user.created_at).toLocaleDateString('en-GB') }}
                  </td>
                  <td class="px-4 py-3 text-end">
                    <div class="d-flex justify-content-end gap-2">
                      <button
                        v-if="user.role !== 'admin' && user.status !== 'banned'"
                        class="btn-table-action btn-table-reject"
                        title="Ban user"
                        @click="handleBan(user.id)"
                      >
                        <i class="fa-solid fa-ban"></i>
                      </button>
                      <button
                        v-if="user.status === 'banned'"
                        class="btn-table-action btn-table-approve"
                        title="Unban user"
                        @click="handleUnban(user.id)"
                      >
                        <i class="fa-solid fa-rotate-left"></i>
                      </button>
                      <button
                        v-if="user.role !== 'admin'"
                        class="btn-table-action btn-table-delete"
                        title="Delete user"
                        @click="handleDelete(user.id)"
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

.search-bar {
  background-color: #e5eeff;
  border: 1px solid #c6c6cd;
  border-radius: 50px;
  padding: 6px 16px;
  display: flex;
  align-items: center;
  width: 240px;
}
.search-bar input {
  background: transparent;
  border: none;
  outline: none;
  font-size: 14px;
  width: 100%;
  margin-left: 8px;
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
