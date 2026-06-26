import { callApi } from './apiService'
import Cookies from 'js-cookie'

export async function fetchJobs(filters = {}) {
  const params = new URLSearchParams()
  Object.entries(filters).forEach(([key, value]) => {
    if (value) params.append(key, value)
  })
  const url = params.toString() ? `/jobs?${params.toString()}` : '/jobs'
  const result = await callApi(url, 'GET')
  if (result.ok && !Array.isArray(result.data)) {
    return { ...result, data: [] }
  }
  return result
}

export async function fetchJob(id) {
  const result = await callApi(`/jobs/${id}`, 'GET')
  return result
}

export async function fetchEmployerJobs() {
  const token = Cookies.get('token')
  const result = await callApi('/employer/jobs', 'GET', null, token)
  if (result.ok && !Array.isArray(result.data)) {
    return { ...result, data: [] }
  }
  return result
}

export async function fetchEmployerApplications() {
  const token = Cookies.get('token')
  const result = await callApi('/employer/applications', 'GET', null, token)
  if (result.ok && !Array.isArray(result.data)) {
    return { ...result, data: [] }
  }
  return result
}

export async function fetchCandidateApplications() {
  const token = Cookies.get('token')
  const result = await callApi('/candidate/applications', 'GET', null, token)
  if (result.ok && !Array.isArray(result.data)) {
    return { ...result, data: [] }
  }
  return result
}

export async function createJob(jobData) {
  const result = await callApi('/jobs', 'POST', jobData)
  return result
}

export async function newJobListing(jobData) {
  const token = Cookies.get('token')

  const result = await callApi('/jobs/create', 'POST', jobData, token)
  return result
}

export async function updateJobListing(jobData) {
  const token = Cookies.get('token')
  const result = await callApi(`/jobs/update/${jobData.id}`, 'PATCH', jobData, token)
  return result
}

export async function deleteJobListing(jobId) {
  const token = Cookies.get('token')
  const result = await callApi(`/jobs/${jobId}`, 'DELETE', null, token)
  return result
}

export async function reviewApplication(applicationId, status) {
  const token = Cookies.get('token')
  const result = await callApi(`/employer/applications/${applicationId}/${status}`, 'PATCH', null, token)
  return result
}

export async function payApplication(applicationId, paymentData) {
  const token = Cookies.get('token')
  const result = await callApi(`/employer/applications/${applicationId}/pay`, 'POST', paymentData, token)
  return result
}

export async function applyToJob(jobId, applicationData) {
  const token = Cookies.get('token')
  const payload = new FormData()

  if (applicationData.candidate_name)
    payload.append('candidate_name', applicationData.candidate_name)
  if (applicationData.email) payload.append('email', applicationData.email)
  if (applicationData.phone) payload.append('phone', applicationData.phone)
  if (applicationData.message) payload.append('message', applicationData.message)
  if (applicationData.resume) payload.append('resume', applicationData.resume)

  const result = await callApi(`/jobs/${jobId}/apply`, 'POST', payload, token)
  return result
}
