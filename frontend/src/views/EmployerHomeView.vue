<script setup>
import ApplicationList from '@/components/EmployerComponents/ApplicationList.vue'
import JobListingList from '@/components/EmployerComponents/JobListingList.vue'
import NewJob from '@/components/EmployerComponents/NewJob.vue'
import SideBar from '@/components/EmployerComponents/SideBar.vue'
import UpdateJob from '@/components/EmployerComponents/UpdateJob.vue'
import { ref } from 'vue'

const jobId = ref('')
const tab = ref('dashboard')
</script>

<template>
  <div class="d-flex">
    <SideBar v-model:tab="tab" />

    <div class="flex-grow-1">
      <NewJob v-model:tab="tab" v-if="tab === 'input_post'" />

      <Suspense v-else-if="tab === 'update_post'">
        <template #default>
          <UpdateJob v-model:tab="tab" :jobId="jobId" />
        </template>

        <template #fallback>
          <div class="container-fluid py-5 text-center">
            <div class="spinner-border text-success" role="status">
              <span class="visually-hidden">Loading listings...</span>
            </div>
            <p class="text-muted mt-2 small">Loading active job listings...</p>
          </div>
        </template>
      </Suspense>

      <Suspense v-else-if="tab === 'dashboard'">
        <template #default>
          <JobListingList v-model:tab="tab" v-model:jobId="jobId" />
        </template>

        <template #fallback>
          <div class="container-fluid py-5 text-center">
            <div class="spinner-border text-success" role="status">
              <span class="visually-hidden">Loading listings...</span>
            </div>
            <p class="text-muted mt-2 small">Loading active job listings...</p>
          </div>
        </template>
      </Suspense>

      <Suspense v-else-if="tab === 'applications'">
        <template #default>
          <ApplicationList />
        </template>

        <template #fallback>
          <div class="container-fluid py-5 text-center">
            <div class="spinner-border text-success" role="status">
              <span class="visually-hidden">Loading applications...</span>
            </div>
            <p class="text-muted mt-2 small">Loading candidate applications...</p>
          </div>
        </template>
      </Suspense>
    </div>
  </div>
</template>
