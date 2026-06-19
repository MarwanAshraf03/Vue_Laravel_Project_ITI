<script setup>
// #[Fillable(['title', 'description', 'category', 'industry', 'location', 'deadline', 'salary_min', 'salary_max', 'experience_level'])]
import { newJobListing } from '@/services/jobsService'
import { ref } from 'vue'

const props = defineProps(['action', 'handleSubmit'])
const job = defineModel('job')

// const job = ref({
//   title: '',
//   description: '',
//   category: '',
//   industry: '',
//   location: 'remote',
//   deadline: '',
//   salary_min: 0,
//   salary_max: 1,
//   experience_level: '',
// })

const formatToDMY = (dateValue) => {
  if (!dateValue) return ''

  const date = new Date(dateValue)

  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0') // Months are 0-indexed
  const year = date.getFullYear()

  return `${day}-${month}-${year}`
}

// const handleSubmit = async function () {
//   job.value.deadline = formatToDMY(job.value.deadline)
//   console.log({ job: job.value })
//   const response = await newJobListing(job.value)
//   console.log(response.data)
// }
</script>

<template>
  <body>
    <div class="d-flex">
      <main class="main-content flex-grow-1">
        <header class="top-navbar d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center d-md-none">
            <i
              class="fa-solid fa-bars fs-4 text-dark cursor-pointer me-3"
              data-bs-toggle="collapse"
              data-bs-target="#mobileSidebar"
              style="cursor: pointer"
            ></i>
            <span class="fw-bold fs-4 text-dark">CareerMomentum</span>
          </div>
          <div class="d-none d-md-flex align-items-center">
            <h1 class="fs-5 fw-bold mb-0 text-dark">{{ props.action }} a Job</h1>
          </div>
          <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link p-2 text-muted position-relative style-none">
              <i class="fa-solid fa-bell fs-5"></i>
              <span
                class="position-absolute top-2 start-75 translate-middle p-1 bg-danger border border-light rounded-circle"
              ></span>
            </button>
            <button class="btn btn-link p-2 text-muted">
              <i class="fa-solid fa-gear fs-5"></i>
            </button>
            <div class="rounded-circle border overflow-hidden" style="width: 40px; height: 40px">
              <img
                alt="Employer profile avatar"
                class="w-100 h-100 object-fit-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDcrfiult4sHcY5RRVwpjo3M5J3YJP-dMkeibsXf7VNUMNLw5fy3r8ESteF7_0mEMcfiG_JRxhTi7JuWFkw5tTTKp3d20Q8Q7ZY5p05uMp99IoKIxZ2voPWo-5oHpWYo6W3U0A-HkPM7l9ydYLnXwZZbyBYcRNsfZLvc7S-ia0y--jWGsTAQV3FnLiRwbojEwuTcJzGJquL0k4iA32ATdC7jkb6j2EFRxXRvom0RkTCNIOcoLFGGOxD6ycxMFc5_SyGRaN-CQyIRCE"
              />
            </div>
          </div>
        </header>

        <div class="container-fluid py-4 px-3 px-md-5" style="max-width: 960px">
          <div class="job-card">
            <div class="decorative-header">
              <div>
                <h2 class="fs-4 fw-bold mb-1">Define the Future</h2>
                <p class="small text-white-50 mb-0">
                  Outline the requirements and role for your next top talent.
                </p>
              </div>
            </div>

            <form @submit.prevent="props.handleSubmit" class="p-4" id="job-post-form">
              <div class="mb-4">
                <label class="form-label small fw-semibold text-muted" for="job-title"
                  >Job Title</label
                >
                <input
                  v-model="job.title"
                  class="form-control"
                  id="job-title"
                  name="job-title"
                  placeholder="e.g. Senior Full Stack Engineer"
                  type="text"
                  required
                />
              </div>

              <div class="row g-4 mb-4">
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold text-muted" for="category"
                    >Category</label
                  >
                  <input
                    v-model="job.category"
                    class="form-control"
                    id="job-category"
                    name="job-category"
                    placeholder="e.g. Programming"
                    type="text"
                    required
                  />
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label d-block small fw-semibold text-muted">Work Type</label>
                  <div class="segmented-control">
                    <input
                      v-model="job.location"
                      checked=""
                      id="remote"
                      name="work-type"
                      type="radio"
                      value="remote"
                    />
                    <label for="remote">Remote</label>

                    <input
                      v-model="job.location"
                      id="onsite"
                      name="work-type"
                      type="radio"
                      value="onsite"
                    />
                    <label for="onsite">Onsite</label>

                    <input
                      v-model="job.location"
                      id="hybrid"
                      name="work-type"
                      type="radio"
                      value="hybrid"
                    />
                    <label for="hybrid">Hybrid</label>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label small fw-semibold text-muted" for="description"
                  >Job Description</label
                >
                <div class="border rounded-3 overflow-hidden">
                  <div class="d-flex align-items-center gap-2 px-3 py-2 bg-light border-bottom">
                    <button class="btn btn-sm btn-light py-0 px-1 text-muted" type="button">
                      <i class="fa-solid fa-bold"></i>
                    </button>
                    <button class="btn btn-sm btn-light py-0 px-1 text-muted" type="button">
                      <i class="fa-solid fa-italic"></i>
                    </button>
                    <button class="btn btn-sm btn-light py-0 px-1 text-muted" type="button">
                      <i class="fa-solid fa-list-ul"></i>
                    </button>
                    <button class="btn btn-sm btn-light py-0 px-1 text-muted" type="button">
                      <i class="fa-solid fa-link"></i>
                    </button>
                  </div>
                  <textarea
                    v-model="job.description"
                    class="form-control border-0 rounded-0"
                    id="description"
                    name="description"
                    placeholder="Enter the role responsibilities, perks, and expectations..."
                    rows="8"
                    style="resize: none; box-shadow: none"
                  ></textarea>
                </div>
              </div>

              <div class="row g-4 mb-4">
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold text-muted" for="industry"
                    >Industry</label
                  >
                  <input
                    v-model="job.industry"
                    class="form-control"
                    id="industry"
                    name="industry"
                    placeholder="e.g. Food Corporation"
                    type="text"
                    required
                  />
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold text-muted" for="experience_level"
                    >Experience Level</label
                  >
                  <input
                    v-model="job.experience_level"
                    class="form-control"
                    id="experience_level"
                    name="experience_level"
                    placeholder="e.g. Junior"
                    type="text"
                    required
                  />
                </div>
              </div>

              <div class="row g-4 mb-4">
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold text-muted" for="category"
                    >Min Salary</label
                  >
                  <input
                    v-model="job.salary_min"
                    class="form-control"
                    id="salary_min"
                    name="salary_min"
                    placeholder="5000$"
                    type="number"
                    required
                  />
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold text-muted" for="category"
                    >Max Salary</label
                  >
                  <input
                    v-model="job.salary_max"
                    class="form-control"
                    id="salary_max"
                    name="salary_max"
                    placeholder="10,000$"
                    type="number"
                    required
                  />
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label small fw-semibold text-muted" for="deadline"
                  >Application Deadline</label
                >
                <div class="input-group">
                  <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="fa-solid fa-calendar-days"></i>
                  </span>
                  <input
                    v-model="job.deadline"
                    class="form-control border-start-0 ps-0 text-muted"
                    id="deadline"
                    type="date"
                    required
                  />
                </div>
                <p class="form-text small text-muted mt-1">
                  Specify when you will stop accepting new applications for this position.
                </p>
              </div>
              <div
                class="pt-3 d-flex flex-column-reverse flex-sm-row justify-content-end align-items-center gap-3 border-top"
              >
                <button
                  class="btn btn-primary-custom w-100 w-sm-auto"
                  type="submit"
                  id="submit-btn"
                >
                  {{ props.action }} Job Now
                </button>
              </div>
            </form>
          </div>

          <div class="pro-tip-box mt-4">
            <i class="fa-solid fa-circle-info fs-5 mt-1" style="color: #006a61"></i>
            <div>
              <h4 class="fs-6 fw-bold mb-1" style="color: #005049">
                Pro Tip: Complete Profiles Perform Better
              </h4>
              <p class="small mb-0 text-secondary" style="opacity: 0.85">
                Jobs with detailed tech stacks and clear work type designations receive 40% more
                qualified applicants in the first 48 hours.
              </p>
            </div>
          </div>
        </div>
      </main>
    </div>
  </body>
</template>

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

/* Form Card */
.job-card {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
  border: 1px solid #c6c6cd;
  overflow: hidden;
}

.decorative-header {
  background-color: #000000;
  height: 128px;
  color: #ffffff;
  display: flex;
  align-items: center;
  padding: 0 32px;
}

/* Custom Segmented Control */
.segmented-control {
  background-color: #e5eeff;
  padding: 4px;
  border-radius: 12px;
  display: inline-flex;
  width: 100%;
  max-width: 320px;
}

.segmented-control input[type='radio'] {
  display: none;
}

.segmented-control label {
  flex: 1;
  text-align: center;
  padding: 8px 12px;
  cursor: pointer;
  border-radius: 8px;
  font-size: 14px;
  color: #45464d;
  font-weight: 500;
  transition: all 0.2s;
  margin-bottom: 0;
}

.segmented-control input[type='radio']:checked + label {
  background-color: #86f2e4;
  color: #006f66;
  font-weight: 600;
}

/* Form Custom Controls */
.form-control:focus,
.form-select:focus {
  border-color: #006a61;
  box-shadow: 0 0 0 0.25rem rgba(0, 106, 97, 0.15);
}

/* Tech Tag Container */
.tech-container {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items: center;
  padding: 8px;
  border: 1px solid #ced4da;
  border-radius: 8px;
  background-color: #ffffff;
  min-height: 48px;
}

.tech-tag {
  background-color: #e5eeff;
  color: #131b2e;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.tech-tag button {
  background: none;
  border: none;
  padding: 0;
  color: #45464d;
  cursor: pointer;
  font-size: 12px;
  line-height: 1;
}

.tech-tag button:hover {
  color: #ba1a1a;
}

.tech-input {
  border: none;
  outline: none;
  flex-grow: 1;
  min-width: 120px;
  font-size: 14px;
  padding: 4px;
}

/* Custom Utilities */
.btn-primary-custom {
  background-color: #006a61;
  color: #ffffff;
  border: none;
  font-weight: 600;
  padding: 10px 24px;
  border-radius: 8px;
  transition: all 0.2s;
}

.btn-primary-custom:hover {
  background-color: #005049;
  color: #ffffff;
  transform: scale(0.98);
}

.btn-secondary-custom {
  background: none;
  border: none;
  color: #45464d;
  font-weight: 600;
  padding: 10px 24px;
  border-radius: 8px;
}

.btn-secondary-custom:hover {
  background-color: #e5eeff;
}

.pro-tip-box {
  background-color: rgba(134, 242, 228, 0.2);
  border-radius: 8px;
  padding: 16px;
  display: flex;
  gap: 16px;
}

/* Responsive Design Rules */
@media (max-width: 767.98px) {
  .sidebar {
    display: none !important;
  }

  .main-content {
    margin-left: 0;
  }

  .segmented-control {
    max-width: 100%;
  }
}
</style>
