<script setup>
// import { ref } from 'vue'
import { ref } from 'vue'
import JobPostForm from './JobPostForm.vue'
import { fetchJob, updateJobListing } from '@/services/jobsService.js'
// const jobId = defineProps(jobId)
const props = defineProps(['jobId'])
const response = await fetchJob(props.jobId)
const job = ref(response.data)
const tab = defineModel('tab')

const formatToYMD = (dateValue) => {
  if (!dateValue) return ''

  const date = new Date(dateValue)

  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0') // Months are 0-indexed
  const year = date.getFullYear()

  return `${year}-${month}-${day}`
}

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
  console.log({ job: job.value })
  const response = await updateJobListing(job.value)
  console.log(response.data)
  tab.value = 'dashboard'
}

job.value.deadline = formatToYMD(job.value.deadline)
</script>
<template>
  <JobPostForm action="Update" :handleSubmit="handleSubmit" v-model:job="job" />
</template>
