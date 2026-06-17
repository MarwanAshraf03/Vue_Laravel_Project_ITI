<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import Cookies from 'js-cookie'
import { fetchJob } from '@/services/jobsService'
import { getCurrentUser } from '@/services/userService'
import ApplyModal from '@/components/ApplyModal.vue'

const route = useRoute()
const router = useRouter()
const job = ref(null)
const loading = ref(true)
const isModalOpen = ref(false)
const currentUser = ref(null)

onMounted(async () => {
  currentUser.value = await getCurrentUser()
  const result = await fetchJob(route.params.id)
  if (result.ok) {
    job.value = result.data
  }
  loading.value = false
})

function logout() {
  Cookies.remove('token')
  router.push({ name: 'user_login' })
}
</script>

<template>
  <main class="candidate-shell">
    <nav class="candidate-nav">
      <div>
        <RouterLink to="/candidate/home" class="back-link">← Back to Jobs</RouterLink>
        <p class="brand">Jobify</p>
      </div>

      <div class="nav-actions">
        <RouterLink class="nav-link" :to="{ name: 'candidate_profile' }">Profile</RouterLink>
        <button class="logout-btn" type="button" @click="logout">Logout</button>
      </div>
    </nav>

    <div v-if="loading" class="loading-state">Loading job details...</div>
    <div v-else-if="!job" class="error-state">Job not found.</div>
    <div v-else class="job-details-wrapper">
      <article class="job-detail-card">
        <div class="job-header">
          <h1>{{ job.title }}</h1>
          <span class="company-name">{{ job.company }}</span>
          <div class="job-meta">
            <span>{{ job.location }}</span>
            <span>${{ job.salary_min }} - ${{ job.salary_max }}</span>
            <span>{{ job.experience_level }}</span>
            <span>{{ job.category }}</span>
            <span>{{ job.industry }}</span>
          </div>
        </div>

        <div class="job-description">
          <h2>Job Description</h2>
          <p>{{ job.description }}</p>
        </div>

        <button class="apply-btn" @click="isModalOpen = true">Apply Now</button>
      </article>
    </div>

    <ApplyModal v-if="job" :job="job" :is-open="isModalOpen" @close="isModalOpen = false" />
  </main>
</template>

<style scoped>
.candidate-shell {
  min-height: 100%;
  padding: 1.25rem;
  background:
    radial-gradient(circle at top left, rgba(0, 106, 97, 0.16), transparent 28%),
    radial-gradient(circle at top right, rgba(123, 157, 255, 0.18), transparent 28%),
    linear-gradient(180deg, #f7fbff 0%, #eef4ff 100%);
  color: #0b1c30;
}

.candidate-nav,
.job-details-wrapper {
  max-width: 900px;
  margin: 0 auto;
}

.candidate-nav {
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

.back-link {
  display: block;
  margin-bottom: 0.25rem;
  color: #0b1c30;
  text-decoration: none;
  font-weight: 600;
}

.brand {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 800;
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

.job-detail-card {
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgba(11, 28, 48, 0.1);
  border-radius: 20px;
  padding: 2.5rem;
  box-shadow: 0 24px 60px rgba(11, 28, 48, 0.08);
}

.job-header {
  margin-bottom: 2rem;
}

.job-header h1 {
  margin: 0 0 0.5rem 0;
  font-size: 2.2rem;
}

.company-name {
  display: block;
  font-size: 1.2rem;
  color: #5f6b7c;
  margin-bottom: 1rem;
}

.job-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.job-meta span {
  background: #f3f7ff;
  padding: 0.4rem 0.9rem;
  border-radius: 999px;
  font-size: 0.9rem;
  color: #0b1c30;
}

.job-description h2 {
  margin-top: 0;
  margin-bottom: 1rem;
  font-size: 1.4rem;
}

.job-description p {
  line-height: 1.7;
  color: #334155;
  font-size: 1.05rem;
}

.apply-btn {
  margin-top: 2rem;
  padding: 1rem 2rem;
  background: #0b1c30;
  color: white;
  border: none;
  border-radius: 999px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}

.apply-btn:hover {
  background: #1a3a5c;
}

.loading-state,
.error-state {
  text-align: center;
  padding: 4rem;
  color: #5f6b7c;
  font-size: 1.2rem;
}

@media (max-width: 720px) {
  .candidate-nav {
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
