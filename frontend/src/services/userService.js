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
  console.log(result)
  if (result.ok) {
    currentUser = result.data.user
  }
  // return result
}
