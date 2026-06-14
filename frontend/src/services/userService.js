import { callApi } from './apiService'

export async function signUpUser(role, userData) {
  const result = await callApi(`/${role}/signup`, 'POST', userData)
  return result

  //   try {
  //     const response = await fetch(`${API_URL}/${role}/signup`, {
  //       method: 'POST',
  //       headers: {
  //         'Content-Type': 'application/json',
  //       },
  //       body: JSON.stringify(userData),
  //     })
  //     const data = await response.json()
  //     return { data, success: true, error: null }
  //   } catch (error) {
  //     return { error: 'Error signing up', message: error.message, success: false }
  //   }
}
