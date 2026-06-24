<script setup>
import Cookies from 'js-cookie'
import { useRouter } from 'vue-router'

const router = useRouter()
function logout() {
  isMobileOpen.value = false
  Cookies.remove('token')
  router.push({ name: 'user_login' })
}

const tab = defineModel('tab')
const isMobileOpen = defineModel('isMobileOpen', { default: false })
</script>

<template>
  <aside class="sidebar d-flex flex-column p-3 gap-1" :class="{ 'sidebar-mobile-open': isMobileOpen }">
    <div class="mb-4 px-2">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="d-flex align-items-center gap-2">
          <div
            class="bg-dark text-white rounded d-flex align-items-center justify-content-center"
            style="width: 32px; height: 32px"
          >
            <i class="fa-solid fa-rocket fs-6"></i>
          </div>
          <span class="fw-bold fs-5 text-dark">CareerMomentum</span>
        </div>
        <button
          class="btn d-md-none border-0 text-muted p-1"
          @click="isMobileOpen = false"
          aria-label="Close menu"
        >
          <i class="fa-solid fa-xmark fs-5"></i>
        </button>
      </div>
      <div class="d-flex flex-column mt-3">
        <span class="text-success fw-bold small" style="color: #006a61 !important">Admin Console</span>
        <span class="text-muted small">Platform Control</span>
      </div>
    </div>

    <nav class="d-flex flex-column gap-1 flex-grow-1">
      <a
        class="nav-link-custom"
        :class="{ active: tab === 'dashboard' }"
        href="#"
        v-on:click.prevent="tab = 'dashboard'; isMobileOpen = false"
      >
        <i class="fa-solid fa-chart-pie"></i>
        <span>Dashboard</span>
      </a>
      <a
        class="nav-link-custom"
        :class="{ active: tab === 'jobs' }"
        href="#"
        v-on:click.prevent="tab = 'jobs'; isMobileOpen = false"
      >
        <i class="fa-solid fa-briefcase"></i>
        <span>Jobs</span>
      </a>
      <a
        class="nav-link-custom"
        :class="{ active: tab === 'users' }"
        href="#"
        v-on:click.prevent="tab = 'users'; isMobileOpen = false"
      >
        <i class="fa-solid fa-users"></i>
        <span>Users</span>
      </a>
      <a
        class="nav-link-custom"
        :class="{ active: tab === 'comments' }"
        href="#"
        v-on:click.prevent="tab = 'comments'; isMobileOpen = false"
      >
        <i class="fa-solid fa-comments"></i>
        <span>Comments</span>
      </a>
    </nav>

    <div class="mt-auto pt-3 border-top border-light">
      <a class="nav-link-custom" href="#">
        <i class="fa-solid fa-circle-question"></i>
        <span>Help Center</span>
      </a>
      <button class="nav-link-custom" @click="logout">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Log Out</span>
      </button>
    </div>
  </aside>
</template>

<style scoped>
body {
  font-family: 'Inter', sans-serif;
  background-color: #f8f9ff;
  color: #0b1c30;
}

.sidebar {
  width: 260px;
  height: 100vh;
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
  background: none;
  border: none;
  text-align: left;
  width: 100%;
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

@media (max-width: 767.98px) {
  .sidebar {
    position: fixed;
    top: 0;
    left: -260px;
    height: 100vh;
    transition: left 0.3s ease;
    box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
  }
  .sidebar.sidebar-mobile-open {
    left: 0;
    display: flex !important;
  }
}
</style>
