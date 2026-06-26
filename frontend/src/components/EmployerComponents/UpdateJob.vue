<script setup>
import { ref } from 'vue'
import JobPostForm from './JobPostForm.vue'
import { fetchJob, updateJobListing } from '@/services/jobsService.js'

const props = defineProps(['jobId'])
const response = await fetchJob(props.jobId)
const tab = defineModel('tab')

const jobData = response.data

const formatToYMD = (dateValue) => {
  if (!dateValue) return ''
  const date = new Date(dateValue)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  return `${year}-${month}-${day}`
}

const formatToDMY = (dateValue) => {
  if (!dateValue) return ''
  const date = new Date(dateValue)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  return `${day}-${month}-${year}`
}

const job = ref({
  ...jobData,
  deadline: formatToYMD(jobData?.deadline),
  work_type: jobData?.work_type || 'remote',
  technologies: jobData?.technologies || '',
  requirements: jobData?.requirements || '',
  benefits: jobData?.benefits || '',
  company_logo: jobData?.company_logo || null,
})

const handleSubmit = async function () {
  job.value.deadline = formatToDMY(job.value.deadline)
  const response = await updateJobListing(job.value)
  if (response.ok) {
    tab.value = 'dashboard'
  } else {
    alert(response.message || 'Failed to update job listing.')
  }
}
</script>
<template>
  <JobPostForm action="Update" :handleSubmit="handleSubmit" v-model:job="job" />
</template>
