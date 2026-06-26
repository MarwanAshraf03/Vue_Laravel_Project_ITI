<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import Cookies from 'js-cookie'
import { getCurrentUser } from '@/services/userService'
import { getUserApplications } from '@/services/applicationService'

const router = useRouter()
const currentUser = ref(null)
const applications = ref([])
const loading = ref(false)
const error = ref('')

onMounted(async () => {
  currentUser.value = await getCurrentUser()
  if (currentUser.value?.role !== 'candidate') {
    router.push({ name: 'home' })
    return
  }
  await loadApplications()
})

async function loadApplications() {
  loading.value = true
  error.value = ''
  const result = await getUserApplications()
  loading.value = false

  if (result.ok) {
    applications.value = result.data.applications
  } else {
    error.value = result.message || 'Failed to load applications'
  }
}

function logout() {
  Cookies.remove('token')
  router.push({ name: 'user_login' })
}
</script>

<template>
  <main class="applications-shell">
    <nav class="applications-nav">
      <div>
        <p class="brand">Jobify</p>
        <small class="muted">My Applications</small>
      </div>

      <div class="nav-actions">
        <RouterLink class="nav-link" :to="{ name: 'candidate_home' }">Jobs</RouterLink>
        <RouterLink class="nav-link" :to="{ name: 'candidate_profile' }">Profile</RouterLink>
        <button class="logout-btn" type="button" @click="logout">Logout</button>
      </div>
    </nav>

    <div class="content-wrapper">
      <div class="applications-section">
        <h1>My Job Applications</h1>

        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div v-if="loading" class="loading-state">Loading applications...</div>
        <div v-else-if="applications.length === 0" class="empty-state">
          You haven't applied to any jobs yet. <RouterLink :to="{ name: 'candidate_home' }">Browse jobs</RouterLink> to get started.
        </div>
        <div v-else class="applications-grid">
          <div v-for="application in applications" :key="application.id" class="application-card">
            <div class="card-header">
              <h3>{{ application.job_listing?.title || 'Unknown Job' }}</h3>
              <span class="status-badge">Applied</span>
            </div>
            <div class="card-body">
              <div class="application-detail">
                <span class="label">Company:</span>
                <span class="value">{{ application.job_listing?.company || 'N/A' }}</span>
              </div>
              <div class="application-detail">
                <span class="label">Location:</span>
                <span class="value">{{ application.job_listing?.location || 'N/A' }}</span>
              </div>
              <div class="application-detail">
                <span class="label">Applied on:</span>
                <span class="value">{{ new Date(application.created_at).toLocaleDateString() }}</span>
              </div>
              <div v-if="application.message" class="application-detail">
                <span class="label">Message:</span>
                <span class="value">{{ application.message }}</span>
              </div>
              <div v-if="application.resume_path" class="application-detail">
                <span class="label">Resume:</span>
                <a :href="`/storage/${application.resume_path}`" target="_blank" class="resume-link">View Resume</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.applications-shell {
  min-height: 100%;
  padding: 1.25rem;
  background:
    radial-gradient(circle at top left, rgba(0, 106, 97, 0.16), transparent 28%),
    radial-gradient(circle at top right, rgba(123, 157, 255, 0.18), transparent 28%),
    linear-gradient(180deg, #f7fbff 0%, #eef4ff 100%);
  color: #0b1c30;
}

.applications-nav,
.content-wrapper {
  max-width: 1200px;
  margin: 0 auto;
}

.applications-nav {
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

.nav-link,
.logout-btn {
  border-radius: 999px;
  padding: 0.75rem 1.1rem;
  text-decoration: none;
  font-weight: 700;
}

.nav-link {
  color: #0b1c30;
  background: #f3f7ff;
  border: 1px solid rgba(11, 28, 48, 0.08);
}

.logout-btn {
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

.empty-state a {
  color: #006a61;
  font-weight: 600;
}

.applications-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.application-card {
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(11, 28, 48, 0.08);
  border-radius: 28px;
  padding: 1.5rem;
  box-shadow: 0 24px 60px rgba(11, 28, 48, 0.08);
  transition: transform 0.2s, box-shadow 0.2s;
}

.application-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 28px 70px rgba(11, 28, 48, 0.12);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(11, 28, 48, 0.08);
}

.card-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #0b1c30;
}

.status-badge {
  background: #006a61;
  color: #fff;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
}

.card-body {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.application-detail {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.label {
  color: #5f6b7c;
  font-size: 0.9rem;
  font-weight: 500;
}

.value {
  color: #0b1c30;
  font-weight: 600;
  text-align: right;
}

.resume-link {
  color: #006a61;
  text-decoration: none;
  font-weight: 600;
}

.resume-link:hover {
  text-decoration: underline;
}

@media (max-width: 900px) {
  .applications-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 720px) {
  .applications-nav {
    flex-direction: column;
    align-items: flex-start;
  }

  .nav-actions {
    width: 100%;
    justify-content: flex-start;
    flex-wrap: wrap;
  }
}
</style>
