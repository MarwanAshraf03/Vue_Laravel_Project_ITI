import { callApi } from './apiService'
import Cookies from 'js-cookie'

export async function fetchJobs(filters = {}) {
  const params = new URLSearchParams()
  Object.entries(filters).forEach(([key, value]) => {
    if (value) params.append(key, value)
  })
  const url = params.toString() ? `/jobs?${params.toString()}` : '/jobs'
  const result = await callApi(url, 'GET')
  return result
}

export async function fetchJob(id) {
  const result = await callApi(`/jobs/${id}`, 'GET')
  return result
}

export async function applyToJob(jobId, applicationData) {
  const token = Cookies.get('token')
  const payload = new FormData()

  if (applicationData.candidate_name) payload.append('candidate_name', applicationData.candidate_name)
  if (applicationData.email) payload.append('email', applicationData.email)
  if (applicationData.phone) payload.append('phone', applicationData.phone)
  if (applicationData.message) payload.append('message', applicationData.message)
  if (applicationData.resume) payload.append('resume', applicationData.resume)

  const result = await callApi(`/jobs/${jobId}/apply`, 'POST', payload, token)
  return result
}
