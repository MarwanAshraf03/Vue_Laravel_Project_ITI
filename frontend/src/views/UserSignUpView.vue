<script setup>
import { defineProps } from 'vue'
import { signUpUser } from '@/services/userService'

const props = defineProps({
  role: String,
})
function togglePassword() {
  const password = document.getElementById('password')
  const eye = document.getElementById('eyeIcon')

  if (password.type === 'password') {
    password.type = 'text'
    eye.textContent = 'visibility_off'
  } else {
    password.type = 'password'
    eye.textContent = 'visibility'
  }
}

async function submitForm(e) {
  e.preventDefault()
  let email = document.getElementById('email').value
  let password = document.getElementById('password').value
  let name = document.getElementById('name').value
  let job = null
  if (props.role === 'candidate') {
    job = document.getElementById('job').value
  }
  // const response = await fetch('http://localhost:8000/api/' + props.role + '/signup', {
  //   method: 'POST',
  //   headers: {
  //     'Content-Type': 'application/json',
  //   },
  //   body: JSON.stringify({
  //     email,
  //     password,
  //     name,
  //     job_title: job,
  //   }),
  // })

  const data = await signUpUser(props.role, {
    email,
    password,
    name,
    job_title: job,
  })

  console.log(data)
}
</script>
<template>
  <nav class="navbar navbar-jobify">
    <div class="container py-2">
      <span class="fw-bold fs-3">Jobify</span>

      <div class="d-flex align-items-center gap-3">
        <span class="text-secondary"> Already have an account? </span>

        <a href="/user/login" class="text-decoration-none fw-semibold"> Log In </a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <h1 class="display-5 fw-bold mb-3">Take the next step in your career</h1>

        <p v-if="role === 'candidate'" class="lead mb-4 text-secondary">
          Create your candidate profile to unlock personalized job recommendations and streamlined
          applications.
        </p>
        <p v-else-if="role === 'admin'" class="lead mb-4 text-secondary">
          Create your admin profile to get access to system administration features.
        </p>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label"> Full Name </label>

            <input
              id="name"
              type="text"
              class="form-control"
              name="name"
              placeholder="Alex Johnson"
            />
          </div>

          <div class="mb-3">
            <label class="form-label"> Email Address </label>

            <input
              id="email"
              type="email"
              class="form-control"
              name="email"
              placeholder="alex@example.com"
            />
          </div>

          <div v-if="role === 'candidate'" class="mb-3">
            <label class="form-label"> Professional Headline </label>

            <input
              id="job"
              type="text"
              class="form-control"
              name="job"
              placeholder="Senior Frontend Engineer | UI Designer"
            />
          </div>

          <div class="password-wrapper mb-4">
            <label class="form-label"> Password </label>

            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              placeholder="Min. 8 characters"
            />

            <button type="button" class="password-toggle" @click="togglePassword()">
              <span id="eyeIcon" class="material-symbols-outlined"> visibility </span>
            </button>
          </div>

          <button type="submit" class="signup-btn w-100" @click="submitForm">
            Create {{ role === 'candidate' ? 'Candidate' : 'Admin' }} Account
          </button>
        </form>
      </div>

      <div class="col-lg-6">
        <div class="benefits-wrapper">
          <div class="row g-3">
            <div class="col-12">
              <div class="benefit-card">
                <div class="icon-box bg-success-subtle mb-3">
                  <span class="material-symbols-outlined"> notifications_active </span>
                </div>

                <h5>Instant Job Alerts</h5>

                <p class="mb-0 text-secondary">
                  Be the first to know. We notify you the moment a role matching your profile hits
                  the market.
                </p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="benefit-card h-100">
                <div class="icon-box bg-primary-subtle mb-3">
                  <span class="material-symbols-outlined"> bolt </span>
                </div>

                <h5>Easy Apply</h5>

                <p class="mb-0 text-secondary">
                  One-click applications using your stored profile and resumes.
                </p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="benefit-card h-100">
                <div class="icon-box bg-info-subtle mb-3">
                  <span class="material-symbols-outlined"> insights </span>
                </div>

                <h5>Smart Matches</h5>

                <p class="mb-0 text-secondary">
                  AI-powered recommendations tailored to your skills and goals.
                </p>
              </div>
            </div>

            <div class="col-12">
              <div class="testimonial">
                <img
                  src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=1200"
                  alt="Professional"
                />

                <div class="testimonial-overlay"></div>

                <div class="testimonial-content">
                  <p class="fst-italic">
                    "Jobify helped me find my current role in less than two weeks. The profile setup
                    was seamless!"
                  </p>

                  <small> — Sarah Chen, Senior Product Designer </small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
body {
  font-family: 'Inter', sans-serif;
  background: #f8f9ff;
  color: #0b1c30;
}

.navbar-jobify {
  background: #fff;
  border-bottom: 1px solid #c6c6cd;
}

.benefits-wrapper {
  background: #eff4ff;
  border-radius: 40px;
  padding: 2rem;
  height: 100%;
}

.benefit-card {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(10px);
  border-radius: 24px;
  padding: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  transition: 0.3s;
}

.benefit-card:hover {
  transform: translateY(-4px);
}

.icon-box {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.signup-btn {
  background: #006a61;
  color: white;
  border: none;
  padding: 0.9rem;
  font-weight: 600;
  border-radius: 12px;
}

.signup-btn:hover {
  background: #005049;
}

.testimonial {
  border-radius: 24px;
  overflow: hidden;
  position: relative;
}

.testimonial img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.testimonial-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent);
}

.testimonial-content {
  position: absolute;
  bottom: 1.5rem;
  left: 1.5rem;
  right: 1.5rem;
  color: white;
}

.password-wrapper {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 12px;
  top: 38px;
  border: none;
  background: none;
}

footer {
  border-top: 1px solid #c6c6cd;
  background: #eff4ff;
}
</style>
