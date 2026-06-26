<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import Cookies from 'js-cookie'
import { getCurrentUser } from '@/services/userService'
import { getAllApplications, deleteApplication } from '@/services/applicationService'

const router = useRouter()
const currentUser = ref(null)
const applications = ref([])
const loading = ref(false)
const error = ref('')

onMounted(async () => {
  currentUser.value = await getCurrentUser()
  if (currentUser.value?.role !== 'admin') {
    router.push({ name: 'home' })
    return
  }
  await loadApplications()
})

async function loadApplications() {
  loading.value = true
  error.value = ''
  const result = await getAllApplications()
  loading.value = false

  if (result.ok) {
    applications.value = result.data.applications
  } else {
    error.value = result.message || 'Failed to load applications'
  }
}

async function handleDelete(applicationId) {
  if (!confirm('Are you sure you want to delete this application?')) {
    return
  }

  const result = await deleteApplication(applicationId)
  if (result.ok) {
    applications.value = applications.value.filter(app => app.id !== applicationId)
  } else {
    error.value = result.message || 'Failed to delete application'
  }
}

function logout() {
  Cookies.remove('token')
  router.push({ name: 'user_login' })
}
</script>

<template>
  <main class="admin-shell">
    <nav class="admin-nav">
      <div>
        <p class="brand">Jobify</p>
        <small class="muted">Admin workspace</small>
      </div>

      <div class="nav-actions">
        <button class="logout-btn" type="button" @click="logout">Logout</button>
      </div>
    </nav>

    <div class="content-wrapper">
      <div class="applications-section">
        <h1>Job Applications</h1>

        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div v-if="loading" class="loading-state">Loading applications...</div>
        <div v-else-if="applications.length === 0" class="empty-state">
          No applications found.
        </div>
        <div v-else class="applications-table">
          <table>
            <thead>
              <tr>
                <th>Candidate</th>
                <th>Email</th>
                <th>Job</th>
                <th>Phone</th>
                <th>Message</th>
                <th>Resume</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="application in applications" :key="application.id">
                <td>{{ application.candidate_name }}</td>
                <td>{{ application.email }}</td>
                <td>{{ application.job_listing?.title || 'N/A' }}</td>
                <td>{{ application.phone || 'N/A' }}</td>
                <td>{{ application.message || 'N/A' }}</td>
                <td>
                  <a v-if="application.resume_path" :href="`/storage/${application.resume_path}`" target="_blank" class="resume-link">View Resume</a>
                  <span v-else>N/A</span>
                </td>
                <td>
                  <button class="delete-btn" @click="handleDelete(application.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.admin-shell {
  min-height: 100%;
  padding: 1.25rem;
  background:
    radial-gradient(circle at top left, rgba(0, 106, 97, 0.16), transparent 28%),
    radial-gradient(circle at top right, rgba(123, 157, 255, 0.18), transparent 28%),
    linear-gradient(180deg, #f7fbff 0%, #eef4ff 100%);
  color: #0b1c30;
}

.admin-nav,
.content-wrapper {
  max-width: 1400px;
  margin: 0 auto;
}

.admin-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.25rem;
  margin-bottom: 1.25rem;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 22px;
  box-shadow: 0 18px 50px rgba(11, 28, 48, 0.08);
}

.brand {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 800;
}

.muted {
  color: #5f6b7c;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.logout-btn {
  border-radius: 999px;
  padding: 0.75rem 1.1rem;
  text-decoration: none;
  font-weight: 700;
  border: none;
  background: #0b1c30;
  color: #fff;
  cursor: pointer;
}

.applications-section {
  width: 100%;
}

.applications-section h1 {
  margin-top: 0;
  color: #0b1c30;
}

.alert {
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.alert-danger {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.loading-state,
.empty-state {
  text-align: center;
  padding: 3rem;
  color: #5f6b7c;
  font-size: 1.1rem;
}

.applications-table {
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 28px;
  padding: 1.5rem;
  box-shadow: 0 24px 60px rgba(11, 28, 48, 0.08);
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f3f7ff;
}

th,
td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid rgba(11, 28, 48, 0.08);
}

th {
  font-weight: 700;
  color: #0b1c30;
}

tbody tr:hover {
  background: rgba(0, 106, 97, 0.05);
}

.resume-link {
  color: #006a61;
  text-decoration: none;
  font-weight: 600;
}

.resume-link:hover {
  text-decoration: underline;
}

.delete-btn {
  padding: 0.5rem 1rem;
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.delete-btn:hover {
  background: #fecaca;
}

@media (max-width: 900px) {
  .applications-table {
    padding: 1rem;
  }
  
  th,
  td {
    padding: 0.75rem 0.5rem;
    font-size: 0.9rem;
  }
}
</style>
