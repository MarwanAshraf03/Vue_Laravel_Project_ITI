<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import Cookies from 'js-cookie'
import { getCurrentUser } from '@/services/userService'
import { fetchJobs } from '@/services/jobsService'
import JobCard from '@/components/JobCard.vue'
import Filters from '@/components/Filters.vue'
import ApplyModal from '@/components/ApplyModal.vue'

const router = useRouter()
const currentUser = ref(null)
const jobs = ref([])
const loading = ref(false)
const filters = ref({
  search: '',
  category: '',
  location: '',
  industry: '',
  experience: '',
  posted: '',
  min_salary: '',
  max_salary: '',
})
const selectedJob = ref(null)
const isModalOpen = ref(false)

onMounted(async () => {
  currentUser.value = await getCurrentUser()
  await loadJobs()
})

async function loadJobs() {
  loading.value = true
  const result = await fetchJobs(filters.value)
  if (result.ok) {
    jobs.value = result.data
  }
  loading.value = false
}

watch(
  filters,
  () => {
    loadJobs()
  },
  { deep: true },
)

function logout() {
  Cookies.remove('token')
  router.push({ name: 'user_login' })
}

function openApplyModal(job) {
  selectedJob.value = job
  isModalOpen.value = true
}
</script>

<template>
  <main class="candidate-shell">
    <nav class="candidate-nav">
      <div>
        <p class="brand">Jobify</p>
        <small class="muted">Candidate workspace</small>
      </div>

      <div class="nav-actions">
        <RouterLink class="nav-link" :to="{ name: 'candidate_profile' }">Profile</RouterLink>
        <button class="logout-btn" type="button" @click="logout">Logout</button>
      </div>
    </nav>

    <div class="content-wrapper">
      <Filters v-model:filters="filters" />

      <div class="jobs-section">
        <h2>Available Jobs</h2>

        <div v-if="loading" class="loading-state">Loading jobs...</div>
        <div v-else-if="jobs.length === 0" class="empty-state">
          No jobs found matching your filters.
        </div>
        <div v-else class="jobs-grid">
          <JobCard v-for="job in jobs" :key="job.id" :job="job" @apply="openApplyModal(job)" />
        </div>
      </div>
    </div>

    <ApplyModal
      v-if="selectedJob"
      :job="selectedJob"
      :is-open="isModalOpen"
      @close="isModalOpen = false"
    />
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
.content-wrapper {
  max-width: 1200px;
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

.content-wrapper {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 1.5rem;
  align-items: start;
}

.jobs-section {
  width: 100%;
}

.jobs-section h2 {
  margin-top: 0;
  color: #0b1c30;
}

.jobs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.loading-state,
.empty-state {
  text-align: center;
  padding: 3rem;
  color: #5f6b7c;
  font-size: 1.1rem;
}

@media (max-width: 900px) {
  .content-wrapper {
    grid-template-columns: 1fr;
  }
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
