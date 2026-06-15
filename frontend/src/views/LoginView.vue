<script setup>
import { reactive, ref } from 'vue'
import { login } from '../services/userService'
import { RouterLink, useRouter } from 'vue-router'
import Cookies from 'js-cookie'

const router = useRouter()
const error = ref(null)
const loading = ref(false)
const passwordFieldType = ref('password')

const formData = reactive({
  email: '',
  password: '',
})

async function handleLogin() {
  error.value = null
  if (!formData.email || !formData.password) {
    error.value = 'Please fill in all fields'
    return
  }
  loading.value = true
  const result = await login(formData.email, formData.password)
  loading.value = false
  if (result.ok) {
    Cookies.set('token', result.data.token)
    router.push(result.data.user.role === 'candidate' ? { name: 'candidate_home' } : { name: 'home' })
  } else {
    error.value = result.message
  }
}

function togglePassword() {
  passwordFieldType.value = passwordFieldType.value === 'password' ? 'text' : 'password'
}
</script>

<template>
  <!-- Fixed alert -->
  <div v-if="error" class="alert alert-danger floating-alert">
    {{ error }}
  </div>

  <!-- Blur backgrounds - fixed to viewport, no overflow -->
  <div class="bg-blur-1"></div>
  <div class="bg-blur-2"></div>

  <!-- Simple centered content -->
  <div class="login-content">
    <div class="login-card-wrapper">
      <div class="text-center mb-4">
        <h1 class="fw-bold display-6 mb-2">Jobify</h1>
        <p class="text-secondary">Your career momentum starts here.</p>
      </div>

      <div class="login-card p-4">
        <form @submit.prevent="handleLogin">
          <div class="mb-3">
            <label class="form-label fw-semibold">Email Address</label>
            <div class="position-relative">
              <span class="material-symbols-outlined input-icon">mail</span>
              <input
                v-model="formData.email"
                type="email"
                class="form-control"
                placeholder="name@company.com"
                required
              />
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between mb-2">
              <label class="form-label fw-semibold mb-0">Password</label>
              <a href="#" class="text-decoration-none small">Forgot Password?</a>
            </div>
            <div class="position-relative">
              <span class="material-symbols-outlined input-icon">lock</span>
              <input
                v-model="formData.password"
                :type="passwordFieldType"
                id="password"
                class="form-control"
                placeholder="••••••••"
                required
              />
              <button
                type="button"
                class="password-toggle"
                @click="togglePassword"
                tabindex="-1"
              >
                <span class="material-symbols-outlined">
                  {{ passwordFieldType === 'password' ? 'visibility' : 'visibility_off' }}
                </span>
              </button>
            </div>
          </div>

          <button
            type="submit"
            class="btn btn-login w-100 py-2 d-flex justify-content-center align-items-center gap-2"
            :disabled="loading"
          >
            <span v-if="!loading">
              Sign In
              <span class="material-symbols-outlined">arrow_forward</span>
            </span>
            <span v-else>
              <span class="spinner-border spinner-border-sm me-2"></span>
              Signing in...
            </span>
          </button>
        </form>
      </div>

      <div class="mt-4 text-center">
        <p class="mb-0 text-secondary">
          Don't have an account?
          <RouterLink to="/user/select-role" class="fw-bold text-decoration-none">
            Join Jobify today
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* No overflow, no full-height tricks - just natural flow */
.login-content {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem 1.5rem;
  flex: 1 0 auto;
}

.login-card-wrapper {
  max-width: 440px;
  width: 100%;
}

/* Fixed blur backgrounds - no overflow possible */
.bg-blur-1,
.bg-blur-2 {
  position: fixed;
  width: 400px;
  height: 400px;
  border-radius: 50%;
  filter: blur(120px);
  opacity: 0.4;
  z-index: 0;
  pointer-events: none;
}

.bg-blur-1 {
  background: #e5eeff;
  top: -150px;
  left: -150px;
}

.bg-blur-2 {
  background: #d3e4fe;
  bottom: -150px;
  right: -150px;
}

.floating-alert {
  position: fixed;
  top: 1rem;
  right: 1rem;
  z-index: 1050;
  max-width: 400px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.login-card {
  background: #ffffff;
  border: 1px solid #e2e4e8;
  border-radius: 24px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
  transition: box-shadow 0.2s ease;
}

.login-card:hover {
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
}

.form-control {
  padding-left: 48px;
  border-radius: 12px;
  border: 1px solid #ced4da;
  transition: all 0.2s;
}

.form-control:focus {
  border-color: #006a61;
  box-shadow: 0 0 0 3px rgba(0, 106, 97, 0.1);
  outline: none;
}

.input-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  font-size: 22px;
  pointer-events: none;
}

.password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: none;
  color: #6c757d;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.password-toggle:hover {
  background-color: rgba(0, 0, 0, 0.05);
  color: #006a61;
}

.btn-login {
  background: #006a61;
  color: white;
  font-weight: 600;
  border: none;
  border-radius: 40px;
  padding: 12px;
  transition: all 0.2s;
}

.btn-login:hover:not(:disabled) {
  background: #005049;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 106, 97, 0.2);
}

.btn-login:active:not(:disabled) {
  transform: translateY(0);
}

.btn-login:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 576px) {
  .login-content {
    padding: 1rem;
  }
  .login-card {
    padding: 1.25rem !important;
  }
  .display-6 {
    font-size: 2rem;
  }
}

@media (max-height: 700px) {
  .login-content {
    padding: 1rem;
  }
}
</style>