<script setup>
import { ref } from 'vue'
import JobPostForm from './JobPostForm.vue'
import { newJobListing } from '@/services/jobsService'

const tab = defineModel('tab')
const job = ref({
  title: '',
  description: '',
  category: '',
  industry: '',
  location: 'remote',
  deadline: '',
  salary_min: 0,
  salary_max: 1,
  experience_level: '',
})

const formatToDMY = (dateValue) => {
  if (!dateValue) return ''

  const date = new Date(dateValue)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0') // Months are 0-indexed
  const year = date.getFullYear()

  return `${day}-${month}-${year}`
}

const handleSubmit = async function () {
  // Build a payload with the deadline in the format Laravel expects (d-m-Y)
  // without mutating the reactive ref so re-submissions still work.
  const payload = {
    ...job.value,
    deadline: formatToDMY(job.value.deadline),
  }
  console.log('[NewJob] Sending payload:', JSON.stringify(payload))
  const response = await newJobListing(payload)
  console.log('[NewJob] Response:', response)

  if (!response.ok) {
    console.error('[NewJob] Validation errors:', response.errors)
    alert('Submission failed:\n' + JSON.stringify(response.errors, null, 2))
    return
  }

  tab.value = 'dashboard'
}
</script>
<template>
  <JobPostForm action="Post" :handleSubmit="handleSubmit" v-model:job="job" />
</template>
