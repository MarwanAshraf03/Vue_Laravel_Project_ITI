<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import Cookies from 'js-cookie'
import SideBar from '@/components/AdminComponents/SideBar.vue'
import Dashboard from '@/components/AdminComponents/Dashboard.vue'
import JobsModeration from '@/components/AdminComponents/JobsModeration.vue'
import UsersManagement from '@/components/AdminComponents/UsersManagement.vue'
import CommentsModeration from '@/components/AdminComponents/CommentsModeration.vue'

const tab = ref('dashboard')
const isMobileOpen = ref(false)
</script>

<template>
  <div class="admin-layout">
    <SideBar v-model:tab="tab" v-model:isMobileOpen="isMobileOpen" />

    <!-- Mobile overlay -->
    <div
      v-if="isMobileOpen"
      class="mobile-overlay"
      @click="isMobileOpen = false"
    ></div>

    <div class="admin-main">
      <Suspense>
        <Dashboard
          v-if="tab === 'dashboard'"
          v-model:tab="tab"
          @toggle-sidebar="isMobileOpen = !isMobileOpen"
        />
        <JobsModeration
          v-else-if="tab === 'jobs'"
          @toggle-sidebar="isMobileOpen = !isMobileOpen"
        />
        <UsersManagement
          v-else-if="tab === 'users'"
          @toggle-sidebar="isMobileOpen = !isMobileOpen"
        />
        <CommentsModeration
          v-else-if="tab === 'comments'"
          @toggle-sidebar="isMobileOpen = !isMobileOpen"
        />
        <template #fallback>
          <div class="loading-panel">Loading...</div>
        </template>
      </Suspense>
    </div>
  </div>
</template>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f8f9ff;
  font-family: 'Inter', sans-serif;
}

.admin-main {
  flex: 1;
  min-width: 0;
  overflow-y: auto;
}

.mobile-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 1025;
}

.loading-panel {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  color: #5f6b7c;
  font-size: 1.1rem;
}
</style>
