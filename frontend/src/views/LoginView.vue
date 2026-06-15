<script setup>
import { reactive, ref } from 'vue'
import { login } from '../services/userService'
import { useRouter } from 'vue-router'
import Cookies from 'js-cookie'

const router = useRouter()
const error = ref(null)
const loading = ref(false)

let formData = reactive({
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
    router.push({ name: 'home' })
  } else {
    error.value = result.message
  }
}

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
</script>

<template>
  <div v-if="error" class="alert alert-danger position-fixed end-0 top-0 m-3">
    {{ error }}
  </div>
  <div class="bg-blur-1"></div>
  <div class="bg-blur-2"></div>

  <div
    class="d-flex flex-column justify-content-center align-items-center min-vh-100 position-relative container"
  >
    <div style="max-width: 440px; width: 100%">
      <div class="mb-4 text-center">
        <h1 class="fw-bold display-6 mb-2">Jobify</h1>
        <p class="text-secondary">Your career momentum starts here.</p>
      </div>
      <div class="login-card p-4">
        <form method="post" @submit.prevent="handleLogin()">
          <div class="mb-3">
            <label class="form-label fw-semibold"> Email Address </label>

            <div class="position-relative">
              <span class="material-symbols-outlined input-icon"> mail </span>

              <input
                v-model="formData.email"
                type="email"
                name="email"
                class="form-control"
                placeholder="name@company.com"
              />
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between mb-2">
              <label class="form-label fw-semibold mb-0"> Password </label>

              <a href="#" class="text-decoration-none"> Forgot Password? </a>
            </div>

            <div class="position-relative">
              <span class="material-symbols-outlined input-icon"> lock </span>

              <input
                type="password"
                v-model="formData.password"
                name="password"
                id="password"
                class="form-control"
                placeholder="••••••••"
              />

              <button type="button" class="password-toggle" @click="togglePassword()">
                <span class="material-symbols-outlined" id="eyeIcon"> visibility </span>
              </button>
            </div>
          </div>
          <button
            v-if="!loading"
            type="submit"
            class="btn btn-login d-flex justify-content-center align-items-center w-100 gap-2 py-2"
          >
            Sign In
            <span class="material-symbols-outlined"> arrow_forward </span>
          </button>
          <button
            v-else
            type="button"
            class="btn btn-login d-flex justify-content-center align-items-center w-100 gap-2 py-2"
          >
            <div class="spinner-border spinner-border-sm"></div>
            Signing in...
          </button>
        </form>
      </div>
      <div class="mt-4 text-center">
        <p class="mb-0 text-secondary">
          Don't have an account?
          <a href="/user/choose-role" class="fw-bold text-decoration-none"> Join Jobify today </a>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
body {
  font-family: 'Inter', sans-serif;
  min-height: 100vh;
  background: #f8f9ff;
  color: #0b1c30;
  overflow-x: hidden;
  position: relative;
}

.bg-blur-1,
.bg-blur-2 {
  position: absolute;
  width: 400px;
  height: 400px;
  border-radius: 50%;
  filter: blur(120px);
  opacity: 0.4;
  z-index: 0;
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

.login-card {
  background: #fff;
  border: 1px solid #c6c6cd;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
}

.role-switcher {
  background: #eff4ff;
  padding: 4px;
  border-radius: 12px;
  position: relative;
}

.role-indicator {
  position: absolute;
  top: 4px;
  left: 4px;
  width: calc(33.333% - 4px);
  height: calc(100% - 8px);
  background: #dce9ff;
  border: 1px solid #c6c6cd;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.role-btn {
  position: relative;
  z-index: 2;
  border: none;
  background: transparent;
  padding: 10px;
  font-weight: 600;
  color: #666;
}

.role-btn.active {
  color: #000;
}

.form-control {
  padding-left: 48px;
}

.input-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #76777d;
}

.password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: none;
  color: #76777d;
}

.btn-login {
  background: #006a61;
  color: white;
  font-weight: 600;
}

.btn-login:hover {
  background: #005049;
  color: white;
}

.material-symbols-outlined {
  font-size: 22px;
  vertical-align: middle;
}
</style>
