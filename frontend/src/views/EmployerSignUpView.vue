<template>
  <div class="d-flex flex-column min-vh-100 bg-light">
    <header class="border-bottom bg-white">
      <div class="container py-3 d-flex justify-content-between align-items-center">
        <h3 class="fw-bold mb-0">Jobify</h3>

        <div class="d-flex align-items-center gap-3">
          <button class="btn btn-teal">Log In</button>
        </div>
      </div>
    </header>

    <main class="container py-5 flex-grow-1">
      <div class="row g-4">
        <div class="col-lg-6">
          <div class="pe-lg-4">
            <h1 class="display-4 fw-bold mb-3">Hire your next visionary.</h1>

            <p class="lead text-muted mb-4">
              Access the global network of top-tier talent and scale your team with precision.
            </p>

            <form @submit.prevent="submit">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Company Name</label>
                  <input
                    v-model="form.companyName"
                    type="text"
                    class="form-control"
                    placeholder="e.g. Acme Corp"
                  />
                </div>
                <div class="col-12">
                  <label class="form-label">Work Email</label>
                  <input
                    v-model="form.email"
                    type="email"
                    class="form-control"
                    placeholder="name@company.com"
                  />
                </div>

                <div class="col-12">
                  <label class="form-label">Description</label>
                  <input
                    v-model="form.description"
                    type="text"
                    class="form-control"
                    placeholder="company description"
                  />
                </div>

                <div class="col-12">
                  <label class="form-label">Phone Number</label>
                  <input
                    v-model="form.phoneNumber"
                    type="text"
                    class="form-control"
                    placeholder="01111111111"
                  />
                </div>

                <div class="col-12">
                  <label class="form-label">Password</label>
                  <input
                    v-model="form.password"
                    type="password"
                    class="form-control"
                    placeholder="Min. 8 characters"
                  />
                </div>

                <div class="col-12 mt-5">
                  <button type="submit" class="btn btn-teal w-100 py-3 fw-semibold">
                    Create Employer Account
                  </button>

                  <p class="small text-muted text-center mt-3 mb-0">
                    By signing up, you agree to our
                    <a href="#">Terms of Service</a>
                    and
                    <a href="#">Privacy Policy</a>.
                  </p>
                </div>
              </div>
            </form>

            <div class="row g-3 mt-4 d-lg-none">
              <div class="col-6">
                <div class="card h-100">
                  <div class="card-body">
                    <h5 class="text-success mb-1">50,000+</h5>
                    <small class="text-muted">Active Employers</small>
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="card h-100">
                  <div class="card-body">
                    <h5 class="text-success mb-1">2.4M</h5>
                    <small class="text-muted">Monthly Applicants</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6 d-none d-lg-block">
          <div class="row g-4">
            <div class="col-12">
              <div class="hero-card text-white p-4 rounded-4">
                <h2 class="display-5 fw-bold">50k+</h2>
                <p class="mb-0">Companies hiring on Jobify</p>
              </div>
            </div>

            <div class="col-12 mb-3">
              <div class="glass-card rounded-4 p-4">
                <div class="d-flex align-items-center mb-3">
                  <div
                    class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center quote-icon"
                  >
                    ❝
                  </div>

                  <div class="ms-3">
                    <div class="fw-semibold">Sarah Jenkins</div>
                    <small class="text-muted"> Head of Talent @ TechFlow </small>
                  </div>
                </div>

                <p class="fst-italic mb-0">
                  "Jobify reduced our time-to-hire by 40% in the first quarter. The quality of
                  candidates is unmatched in the industry."
                </p>
              </div>
            </div>

            <div class="col-6">
              <div class="card stat-card h-100">
                <div class="card-body">
                  <div class="fs-2 mb-3">⚡</div>
                  <h5>12 Hours</h5>
                  <small class="text-muted"> Average first response </small>
                </div>
              </div>
            </div>

            <div class="col-6">
              <div class="card stat-card h-100">
                <div class="card-body">
                  <div class="fs-2 mb-3">👥</div>
                  <h5>85%</h5>
                  <small class="text-muted"> Candidate match rate </small>
                </div>
              </div>
            </div>

            <div class="col-12 mt-3">
              <div class="image-card">
                <img
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuAEEFnWPI-VNvQ4Wq9L80tmFj7jpGrFmKEbBvctO3Kf4KkOq8l9_ZOyFkArCrTJ8MuXX9i0K7sVxlArB6ZcLUu2BXd6TcPfqdnoTb97FlElVWmgvC-RqH1VnHpHNYf44dOZM5RLL00eGC_731Ds033im2mkVi74IeusP92vU73ZiekEwn8OXfybzB8bh_R9nfOwdlPYvxzbVXvWhQ8YW-j8cDrqt4r_glfBIi9Fcu3Ce8yQG27ewFpxPHTgGZyMwc_omtXr-uvqi3Y"
                  class="w-100 h-100 object-fit-cover"
                />

                <div class="overlay">Empowering global teams since 2018.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive } from 'vue'

const form = reactive({
  companyName: '',
  email: '',
  password: '',
  description: '',
  phoneNumber: '',
})

const submit = () => {
  console.log(form)
}
</script>

<style scoped>
.btn-teal {
  background: #0d9488;
  color: white;
  border: none;
}

.btn-teal:hover {
  background: #0b7f74;
  color: white;
}

.hero-card {
  background: #131b2e;
  min-height: 250px;
  display: flex;
  flex-direction: column;
  justify-content: end;
}

.glass-card {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(12px);
  border: 1px solid #e2e8f0;
}

.stat-card {
  transition: 0.3s;
}

.stat-card:hover {
  transform: translateY(-4px);
}

.quote-icon {
  width: 40px;
  height: 40px;
}

.image-card {
  position: relative;
  height: 250px;
  overflow: hidden;
  border-radius: 1rem;
}

.overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: end;
  padding: 1.5rem;
  color: white;
  font-weight: 600;
  background: linear-gradient(to top, rgba(19, 27, 46, 0.85), transparent);
}
</style>
