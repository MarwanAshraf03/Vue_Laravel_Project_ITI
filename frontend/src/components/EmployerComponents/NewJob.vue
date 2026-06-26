<script setup>
import { ref } from 'vue'
import JobPostForm from './JobPostForm.vue'
import { newJobListing } from '@/services/jobsService.js'

const tab = defineModel('tab')
const job = ref({
  title: '',
  description: '',
  category: '',
  industry: '',
  location: '',
  work_type: 'remote',
  deadline: '',
  salary_min: 0,
  salary_max: 1,
  experience_level: '',
  technologies: '',
  requirements: '',
  benefits: '',
  company_logo: null,
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
  job.value.deadline = formatToDMY(job.value.deadline)
  const response = await newJobListing(job.value)
  if (response.ok) {
    tab.value = 'dashboard'
  } else {
    alert(response.message || 'Failed to create job listing.')
  }
}
</script>
<template>
  <JobPostForm action="Post" :handleSubmit="handleSubmit" v-model:job="job" />
</template>
