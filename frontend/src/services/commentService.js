import { callApi } from './apiService'
import Cookies from 'js-cookie'

export async function fetchJobComments(jobId) {
  return await callApi(`/jobs/${jobId}/comments`, 'GET')
}

export async function postComment(jobId, content) {
  const token = Cookies.get('token')
  return await callApi(`/jobs/${jobId}/comments`, 'POST', { content }, token)
}

export async function deleteComment(commentId) {
  const token = Cookies.get('token')
  return await callApi(`/comments/${commentId}`, 'DELETE', null, token)
}
