<script setup>
import Cookies from 'js-cookie'
import { useRouter } from 'vue-router'

const router = useRouter()
function logout() {
  Cookies.remove('token')
  router.push({ name: 'user_login' })
}

const tab = defineModel('tab')
</script>

<template>
  <!-- <div class="d-flex"> -->
  <aside class="sidebar d-flex flex-column p-3 gap-1">
    <div class="mb-4 px-2">
      <div class="d-flex align-items-center gap-2 mb-2">
        <div
          class="bg-dark text-white rounded d-flex align-items-center justify-content-center"
          style="width: 32px; height: 32px"
        >
          <i class="fa-solid fa-rocket fs-6"></i>
        </div>
        <span class="fw-bold fs-5 text-dark">CareerMomentum</span>
      </div>
      <div class="d-flex flex-column mt-3">
        <span class="text-success fw-bold small" style="color: #006a61 !important"
          >Employer Hub</span
        >
        <span class="text-muted small">Recruitment Manager</span>
      </div>
    </div>

    <nav class="d-flex flex-column gap-1 flex-grow-1">
      <a
        class="nav-link-custom"
        :class="{ active: tab === 'dashboard' }"
        href="#"
        v-on:click.prevent="tab = 'dashboard'"
      >
        <i class="fa-solid fa-chart-pie"></i>
        <span>Dashboard</span>
      </a>
      <a
        class="nav-link-custom"
        :class="{ active: tab === 'input_post' }"
        href="#"
        v-on:click.prevent="tab = 'input_post'"
      >
        <i class="fa-solid fa-square-plus"></i>
        <span>Post a Job</span>
      </a>
      <a class="nav-link-custom" href="#" v-on:click.prevent="">
        <i class="fa-solid fa-users"></i>
        <span>Applications</span>
      </a>
    </nav>

    <div class="mt-auto pt-3 border-top border-light">
      <a class="nav-link-custom" href="#">
        <i class="fa-solid fa-circle-question"></i>
        <span>Help Center</span>
      </a>
      <!-- <a class="nav-link-custom" @click="logout()"> -->
      <button class="nav-link-custom" @click="logout">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Log Out</span>
      </button>
      <!-- </a> -->
    </div>
  </aside>
  <!-- </div> -->
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
