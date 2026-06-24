<script setup>
import { ref } from 'vue'
import SideBar from '@/components/AdminComponents/SideBar.vue'
import Dashboard from '@/components/AdminComponents/Dashboard.vue'
import JobsModeration from '@/components/AdminComponents/JobsModeration.vue'
import UsersManagement from '@/components/AdminComponents/UsersManagement.vue'
import CommentsModeration from '@/components/AdminComponents/CommentsModeration.vue'

const activeTab = ref('dashboard')
const isMobileSidebarOpen = ref(false)

function toggleMobileSidebar() {
  isMobileSidebarOpen.value = !isMobileSidebarOpen.value
}
</script>

<template>
  <div class="admin-layout d-flex">
    <!-- Sidebar component -->
    <SideBar v-model:tab="activeTab" v-model:isMobileOpen="isMobileSidebarOpen" />

    <!-- Backdrop for mobile sidebar -->
    <div
      v-if="isMobileSidebarOpen"
      class="sidebar-backdrop d-md-none"
      @click="isMobileSidebarOpen = false"
    ></div>

    <!-- Main Content Area -->
    <div class="content-container flex-grow-1 overflow-auto">
      <Suspense>
        <template #default>
          <component
            :is="
              activeTab === 'dashboard'
                ? Dashboard
                : activeTab === 'jobs'
                  ? JobsModeration
                  : activeTab === 'users'
                    ? UsersManagement
                    : CommentsModeration
            "
            v-model:tab="activeTab"
            @toggle-sidebar="toggleMobileSidebar"
          />
        </template>
        <template #fallback>
          <div class="loading-panel d-flex align-items-center justify-content-center">
            <div class="text-center">
              <div class="spinner-border text-teal mb-3" role="status"></div>
              <p class="text-muted fw-semibold">Loading console panel...</p>
            </div>
          </div>
        </template>
      </Suspense>
    </div>
  </div>
</template>

<style scoped>
.admin-layout {
  min-height: 100vh;
  background-color: #f8f9ff;
  color: #0b1c30;
  display: flex;
}

.content-container {
  height: 100vh;
  display: flex;
  flex-direction: column;
}

.sidebar-backdrop {
  position: fixed;
  inset: 0;
  background-color: rgba(11, 28, 48, 0.4);
  z-index: 1025;
}

.loading-panel {
  flex-grow: 1;
  min-height: 200px;
}

.text-teal {
  color: #006a61;
}
</style>
