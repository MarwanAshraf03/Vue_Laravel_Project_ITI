const API_URL = import.meta.env.VITE_API_URL

export async function callApi(url, method, data, token = null) {
  try {
    const isFormData = typeof FormData !== 'undefined' && data instanceof FormData
    const response = await fetch(`${API_URL}${url}`, {
      method: method,
      headers: {
        ...(!isFormData && { 'Content-Type': 'application/json' }),
        ...(token && { Authorization: `Bearer ${token}` }),
      },
      body: data ? (isFormData ? data : JSON.stringify(data)) : null,
    })
    const responseData = await response.json()
    console.log(responseData)
    if (response.ok) {
      return { data: responseData, ok: true }
    } else {
      return {
        message: responseData.message || 'Something went wrong',
        errors: responseData.errors || null,
        ok: false,
      }
    }
  } catch (error) {
    return { message: error.message || 'Something went wrong', ok: false }
  }
}
