import { callApi } from './apiService'
import Cookies from 'js-cookie'

export let currentUser = null

export async function signUpUser(role, userData) {
  const result = await callApi(`/${role}/signup`, 'POST', userData)
  return result
}

export async function login(email, password) {
  const result = await callApi('/user/login', 'POST', { email, password })
  if (result.ok) {
    currentUser = result.data.user
  }
  return result
}

export async function getCurrentUser() {
  if (currentUser) {
    return currentUser
  }
  const token = Cookies.get('token')
  if (!token) {
    return null
  }
  const result = await callApi('/user', 'GET', null, token)
  if (result.ok) {
    currentUser = result.data.user
  }
  return currentUser
}

export async function updateCandidateProfile(profileData) {
  const token = Cookies.get('token')
  const payload = new FormData()

  payload.append('_method', 'PUT')
  payload.append('name', profileData.name)
  payload.append('email', profileData.email)
  payload.append('job_title', profileData.job_title)

  if (profileData.password) {
    payload.append('password', profileData.password)
  }

  if (profileData.profile_picture_file) {
    payload.append('profile_picture', profileData.profile_picture_file)
  }

  const result = await callApi('/candidate/profile', 'POST', payload, token)
  if (result.ok) {
    currentUser = result.data.user
  }
  return result
}

export async function deleteCandidateProfile() {
  const token = Cookies.get('token')
  const result = await callApi('/candidate/profile', 'DELETE', null, token)
  if (result.ok) {
    currentUser = null
  }
  return result
}
