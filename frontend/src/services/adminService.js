import { callApi } from './apiService'
import Cookies from 'js-cookie'

function authToken() {
  return Cookies.get('token')
}

// ---------- Dashboard ----------

export async function fetchDashboardStats() {
  return callApi('/admin/dashboard/stats', 'GET', null, authToken())
}

// ---------- Jobs moderation ----------

export async function fetchAdminJobs(filters = {}) {
  const params = new URLSearchParams()
  Object.entries(filters).forEach(([key, value]) => {
    if (value) params.append(key, value)
  })
  const url = params.toString() ? `/admin/jobs?${params.toString()}` : '/admin/jobs'
  return callApi(url, 'GET', null, authToken())
}

export async function approveJob(jobId) {
  return callApi(`/admin/jobs/${jobId}/approve`, 'PATCH', null, authToken())
}

export async function rejectJob(jobId, rejectionReason = '') {
  return callApi(`/admin/jobs/${jobId}/reject`, 'PATCH', { rejection_reason: rejectionReason }, authToken())
}

export async function resetJobReview(jobId) {
  return callApi(`/admin/jobs/${jobId}/reset-review`, 'PATCH', null, authToken())
}

export async function deleteAdminJob(jobId) {
  return callApi(`/admin/jobs/${jobId}`, 'DELETE', null, authToken())
}

// ---------- Users management ----------

export async function fetchAdminUsers(filters = {}) {
  const params = new URLSearchParams()
  Object.entries(filters).forEach(([key, value]) => {
    if (value) params.append(key, value)
  })
  const url = params.toString() ? `/admin/users?${params.toString()}` : '/admin/users'
  return callApi(url, 'GET', null, authToken())
}

export async function banUser(userId) {
  return callApi(`/admin/users/${userId}/ban`, 'PATCH', null, authToken())
}

export async function unbanUser(userId) {
  return callApi(`/admin/users/${userId}/unban`, 'PATCH', null, authToken())
}

export async function deleteAdminUser(userId) {
  return callApi(`/admin/users/${userId}`, 'DELETE', null, authToken())
}

// ---------- Comments moderation ----------

export async function fetchAdminComments(filters = {}) {
  const params = new URLSearchParams()
  Object.entries(filters).forEach(([key, value]) => {
    if (value) params.append(key, value)
  })
  const url = params.toString() ? `/admin/comments?${params.toString()}` : '/admin/comments'
  return callApi(url, 'GET', null, authToken())
}

export async function hideComment(commentId) {
  return callApi(`/admin/comments/${commentId}/hide`, 'PATCH', null, authToken())
}

export async function unhideComment(commentId) {
  return callApi(`/admin/comments/${commentId}/unhide`, 'PATCH', null, authToken())
}

export async function deleteAdminComment(commentId) {
  return callApi(`/admin/comments/${commentId}`, 'DELETE', null, authToken())
}
