<script setup>
import { fetchJobs } from '@/services/jobsService'
import { nextTick, ref, Suspense } from 'vue'

const tab = defineModel('tab')
const jobId = defineModel('jobId')
const response = await fetchJobs()
const jobs = ref(response.data ? response.data : response)

function capitalizeWords(str) {
  if (!str) return ''
  return str
    .toLowerCase()
    .split(' ')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

async function handleEditJob(id) {
  jobId.value = id
  tab.value = 'update_post'

  // Wait for Vue to finish updating the tab state
  await nextTick()

  // This will now print perfectly: { jobId: 4, tab: 'update_job' }
  console.log({ jobId: jobId.value, tab: tab.value })
}
</script>

<template>
  <div class="d-flex">
    <!-- Main Content Canvas -->
    <main class="main-content flex-grow-1">
      <!-- TopAppBar Component -->
      <header class="top-navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-4">
          <div class="d-md-none d-flex align-items-center">
            <i class="fa-solid fa-bars fs-4 text-dark me-3" style="cursor: pointer"></i>
          </div>
          <span class="fs-4 fw-bold text-dark mb-0">Dashboard</span>
          <!-- <div class="search-bar d-none d-sm-flex">
            <i class="fa-solid fa-magnifying-glass text-muted small"></i>
            <input type="text" placeholder="Search listings..." />
          </div> -->
        </div>

        <!-- <div class="d-flex align-items-center gap-3">
          <button class="btn btn-link p-2 text-muted position-relative shadow-none">
            <i class="fa-solid fa-bell fs-5"></i>
          </button>
          <button class="btn btn-link p-2 text-muted shadow-none">
            <i class="fa-solid fa-gear fs-5"></i>
          </button>
          <div class="rounded-circle border overflow-hidden" style="width: 40px; height: 40px">
            <img
              alt="Employer profile avatar"
              class="w-100 h-100 object-fit-cover"
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuApWX_WkyB5lP3DRjYRhHsRShzY7u5GrxEHqUeWaYx-5ivWBD9PyQhQgI9Vel7r1IZcig6QaTrTb4rEmzrpzfj9pVHBSNB7O05v2bxgNlxkNJmvN23nI_A9hn4_KHIiZG7a5AlHU4A0AiFCTVPxPtwfkcG7XNZm1DC84tudmrpnT6N0P4Od8FdoUgfiN9Z0Nr2Py4CuPQ8dHq_R1hUI4d8LLxQxvqhNdAm9s80R-jOamPUrph2y32_oPBjVSwYRkvNWiO2PeSGPyN4"
            />
          </div>
        </div> -->
      </header>

      <!-- Dashboard Body Content -->
      <div class="container-fluid py-4 px-3 px-md-4" style="max-width: 1280px">
        <!-- Quick Stats Bento Grid -->
        <!-- <div class="row g-3 mb-4">
          <div class="col-12 col-md-6">
            <div class="bento-card">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <p class="small fw-semibold text-muted text-uppercase tracking-wider mb-0">
                  Applicants
                </p>
                <i class="fa-solid fa-users text-primary fs-5"></i>
              </div>
              <p class="display-6 fw-bold mb-0">284</p>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <div class="bento-card">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <p class="small fw-semibold text-muted text-uppercase tracking-wider mb-0">
                  Job Views
                </p>
                <i class="fa-solid fa-eye text-muted fs-5"></i>
              </div>
              <p class="display-6 fw-bold mb-0">4.2k</p>
              <p class="small text-muted fw-semibold mb-0 mt-1">
                <i class="fa-solid fa-arrow-up me-1"></i>12% increase
              </p>
            </div>
          </div>
        </div> -->

        <!-- Main Job Listings Section -->
        <div class="dashboard-container">
          <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
            <h2 class="fs-5 fw-bold mb-0 text-dark">Active Job Listings</h2>
            <div class="d-flex gap-2">
              <button class="btn btn-action-outline d-flex align-items-center gap-2">
                <i class="fa-solid fa-filter small"></i> Filter
              </button>
              <button
                class="btn btn-action-dark d-flex align-items-center gap-2"
                v-on:click="tab = 'input_post'"
              >
                <i class="fa-solid fa-plus small"></i> Post a Job
              </button>
            </div>
          </div>

          <!-- Table Container -->
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-start">
              <thead>
                <tr class="table-light border-bottom">
                  <th class="px-4 py-3 small fw-semibold text-muted" style="width: 45%">
                    Job Title
                  </th>
                  <th class="px-4 py-3 small fw-semibold text-muted">Date Posted</th>
                  <th class="px-4 py-3 small fw-semibold text-muted">Applicants</th>
                  <th class="px-4 py-3 small fw-semibold text-muted">Status</th>
                  <th class="px-4 py-3 small fw-semibold text-muted text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- Row 1: Draft State example -->
                <tr
                  v-for="job in jobs"
                  :key="job.id"
                  :style="{ opacity: new Date(job.deadline) < new Date() ? '0.75' : '1' }"
                >
                  <!-- <td class="px-4 py-3">
                    {{ JSON.stringify(job) }}
                  </td> -->
                  <td class="px-4 py-3">
                    <p class="fw-semibold mb-0 text-dark">
                      {{ capitalizeWords(job.title) || 'Job Title' }}
                    </p>
                    <p class="small text-muted mb-0">
                      {{ capitalizeWords(job.industry) || 'Marketing' }} &bull;
                      {{ capitalizeWords(job.location) || 'Full Time' }}
                    </p>
                  </td>
                  <td class="px-4 py-3 text-muted">
                    {{ new Date(job.created_at).toLocaleDateString('en-GB') || '—' }}
                  </td>
                  <td class="px-4 py-3 fw-bold">0</td>
                  <td class="px-4 py-3 fw-bold">
                    {{ new Date(job.deadline) < new Date() ? 'In Active' : 'Active' }}
                  </td>
                  <td class="px-4 py-3 text-end">
                    <div class="d-flex justify-content-end gap-2">
                      <button
                        class="btn-table-action"
                        title="Edit"
                        v-on:click="() => handleEditJob(job.id)"
                      >
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                      <button class="btn-table-action btn-table-delete" title="Delete">
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <!-- <tr>
                  <td class="px-4 py-3">
                    <p class="fw-semibold mb-0 text-dark">Marketing Coordinator</p>
                    <p class="small text-muted mb-0">Marketing &bull; London &bull; Full-time</p>
                  </td>
                  <td class="px-4 py-3 text-muted">—</td>
                  <td class="px-4 py-3 fw-bold">0</td>
                  <td class="px-4 py-3 text-end">
                    <div class="d-flex justify-content-end gap-2">
                      <button class="btn-table-action" title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                      <button class="btn-table-action btn-table-delete" title="Delete">
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </div>
                  </td>
                </tr> -->
                <!-- Row 2: Active / Closed opacity state simulation -->
                <!-- <tr style="opacity: 0.75">
                  <td class="px-4 py-3">
                    <p class="fw-semibold mb-0 text-dark">Sales Executive</p>
                    <p class="small text-muted mb-0">Sales &bull; Berlin &bull; Full-time</p>
                  </td>
                  <td class="px-4 py-3 text-muted">Sep 15, 2023</td>
                  <td class="px-4 py-3 fw-bold">86</td>
                  <td class="px-4 py-3 text-end">
                    <div class="d-flex justify-content-end gap-2">
                      <button class="btn-table-action" title="Refresh">
                        <i class="fa-solid fa-rotate-right"></i>
                      </button>
                      <button class="btn-table-action btn-table-delete" title="Delete">
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </div>
                  </td>
                </tr> -->
              </tbody>
            </table>
          </div>

          <!-- Table Footer Pagination -->
          <div class="p-3 bg-light d-flex justify-content-between align-items-center border-top">
            <p class="small text-muted mb-0">Showing 1-4 of 12 active listings</p>
            <nav>
              <ul class="pagination pagination-sm mb-0 gap-1">
                <li class="page-item disabled">
                  <a class="page-link border rounded text-muted px-2 py-1" href="#"
                    ><i class="fa-solid fa-chevron-left"></i
                  ></a>
                </li>
                <li class="page-item active">
                  <a
                    class="page-link border rounded px-3 py-1 bg-dark border-dark text-white fw-medium"
                    href="#"
                    >1</a
                  >
                </li>
                <li class="page-item">
                  <a class="page-link border rounded text-dark px-3 py-1 bg-white" href="#">2</a>
                </li>
                <li class="page-item">
                  <a class="page-link border rounded text-dark px-3 py-1 bg-white" href="#">3</a>
                </li>
                <li class="page-item">
                  <a class="page-link border rounded text-dark px-2 py-1 bg-white" href="#"
                    ><i class="fa-solid fa-chevron-right"></i
                  ></a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </main>
  </div>
  <Suspense>
    <template #fallback> Loading... </template>
  </Suspense>

  <!-- Bootstrap 5 JS Bundle -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->
  <!-- </html> -->
</template>

<!-- </Suspense> -->

<style scoped>
body {
  font-family: 'Inter', sans-serif;
  background-color: #f8f9ff;
  color: #0b1c30;
}

/* Sidebar Styling */
.sidebar {
  width: 260px;
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  background-color: #ffffff;
  border-right: 1px solid #c6c6cd;
  z-index: 1030;
}

.nav-link-custom {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  color: #45464d;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.2s;
}

.nav-link-custom:hover {
  background-color: #e5eeff;
  color: #0b1c30;
}

.nav-link-custom.active {
  background-color: #86f2e4;
  color: #005049;
  font-weight: 600;
}

/* Main Content Wrapper */
.main-content {
  margin-left: 260px;
  min-height: 100vh;
}

/* Top Navbar */
.top-navbar {
  position: sticky;
  top: 0;
  z-index: 1020;
  background-color: #ffffff;
  border-bottom: 1px solid #c6c6cd;
  padding: 12px 24px;
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

/* Bento Cards */
.bento-card {
  background-color: #ffffff;
  border: 1px solid #c6c6cd;
  border-radius: 12px;
  padding: 24px;
}

/* Listing Dashboard Container */
.dashboard-container {
  background-color: #ffffff;
  border-radius: 12px;
  border: 1px solid #c6c6cd;
  overflow: hidden;
}

/* Action Buttons Utilities */
.btn-quick-post {
  background-color: #006a61;
  color: #ffffff;
  font-weight: 700;
  border: none;
  border-radius: 8px;
  padding: 12px;
  transition: all 0.2s;
}
.btn-quick-post:hover {
  background-color: #005049;
  color: #ffffff;
  opacity: 0.9;
}

.btn-action-outline {
  background-color: #e5eeff;
  border: none;
  color: #0b1c30;
  font-weight: 600;
  font-size: 14px;
  padding: 8px 16px;
  border-radius: 8px;
  transition: background-color 0.2s;
}
.btn-action-outline:hover {
  background-color: #dce9ff;
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

.btn-table-action {
  background: none;
  border: none;
  color: #45464d;
  padding: 6px 10px;
  border-radius: 6px;
  transition: all 0.2s;
}
.btn-table-action:hover {
  background-color: #d3e4fe;
}
.btn-table-delete:hover {
  background-color: #ffdad6;
  color: #ba1a1a;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

@media (max-width: 767.98px) {
  .sidebar {
    display: none !important;
  }
  .main-content {
    margin-left: 0;
  }
}
</style>
