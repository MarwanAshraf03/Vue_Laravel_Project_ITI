import { callApi } from './apiService'
import Cookies from 'js-cookie'

export async function getAllApplications() {
  const token = Cookies.get('token')
  const result = await callApi('/applications', 'GET', null, token)
  return result
}

export async function getUserApplications() {
  const token = Cookies.get('token')
  const result = await callApi('/user/applications', 'GET', null, token)
  return result
}

export async function deleteApplication(applicationId) {
  const token = Cookies.get('token')
  const result = await callApi(`/applications/${applicationId}`, 'DELETE', null, token)
  return result
}
