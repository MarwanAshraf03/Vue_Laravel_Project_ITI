<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
let selectedRole = null
let isLoading = ref(false)
const router = useRouter()

function selectRole(role) {
  selectedRole = role

  document.querySelectorAll('.role-card').forEach((card) => {
    card.classList.remove('active')
  })

  document.getElementById(role + '-card').classList.add('active')

  const btn = document.getElementById('get-started-btn')
  btn.disabled = false
  btn.classList.add('active')
}

function goToSignUpPage() {
  if (!selectedRole) return
  if (isLoading.value) return
  isLoading.value = true
  router.push('/' + selectedRole + '/signup')
  isLoading.value = false
}
</script>
<template>
  <nav class="navbar navbar-jobify">
    <div class="d-flex justify-content-between align-items-center container py-3">
      <div class="fw-bold fs-4">Jobify</div>
      <div class="text-secondary">Help</div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="mb-5 text-center">
      <h1 class="fw-bold">Welcome to Jobify</h1>
      <p class="text-secondary">Choose your role to get started.</p>
    </div>

    <div class="row g-4 justify-content-center">
      <div class="col-md-4">
        <div class="role-card" id="candidate-card" @click="selectRole('candidate')">
          <div class="role-icon">
            <span class="material-symbols-outlined fs-2">person_search</span>
          </div>
          <h5>Candidate</h5>
          <p class="mb-0 text-secondary">I'm looking for my next career move.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="role-card" id="employer-card" @click="selectRole('employer')">
          <div class="role-icon">
            <span class="material-symbols-outlined fs-2">business_center</span>
          </div>
          <h5>Employer</h5>
          <p class="mb-0 text-secondary">I'm looking to hire top talent.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="role-card" id="admin-card" @click="selectRole('admin')">
          <div class="role-icon">
            <span class="material-symbols-outlined fs-2">admin_panel_settings</span>
          </div>
          <h5>Admin</h5>
          <p class="mb-0 text-secondary">I'm managing the system.</p>
        </div>
      </div>
    </div>

    <div class="mt-5 text-center">
      <button id="get-started-btn" class="cta-btn" @click="goToSignUpPage">
        {{ isLoading ? 'Loading...' : 'Get Started' }}
      </button>
    </div>
  </div>
</template>
<style scoped>
body {
  font-family: 'Inter', sans-serif;
  background: #f8f9ff;
  min-height: 100vh;
}

.navbar-jobify {
  background: #fff;
  border-bottom: 1px solid #c6c6cd;
}

.role-card {
  border: 2px solid #c6c6cd;
  border-radius: 16px;
  padding: 24px;
  text-align: center;
  cursor: pointer;
  transition: 0.3s;
  background: #fff;
}

.role-card:hover {
  transform: translateY(-4px);
  border-color: #006a61;
  box-shadow: 0 10px 30px rgba(0, 106, 97, 0.1);
}

.role-card.active {
  background: #86f2e4;
  border-color: #006a61;
  transform: translateY(-4px);
  box-shadow: 0 10px 30px rgba(0, 106, 97, 0.15);
}

.role-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #86f2e4;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px;
}

.cta-btn {
  border: none;
  padding: 12px 48px;
  border-radius: 999px;
  font-weight: 600;
  background: #adb5bd;
  color: #fff;
  cursor: not-allowed;
  transition: 0.3s;
}

.cta-btn.active {
  background: #006a61;
  cursor: pointer;
}

.cta-btn.active:hover {
  background: #005049;
}

.material-symbols-outlined {
  font-variation-settings:
    'FILL' 0,
    'wght' 400,
    'GRAD' 0,
    'opsz' 24;
}
</style>
