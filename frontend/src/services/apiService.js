const API_URL = import.meta.env.VITE_API_URL

export async function callApi(url, method, data, token = null) {
  try {
    const response = await fetch(`${API_URL}${url}`, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        ...(token && { Authorization: `Bearer ${token}` }),
      },
      body: data ? JSON.stringify(data) : null,
    })
    const responseData = await response.json()
    if (response.ok) {
      return { data: responseData, ok: true }
    } else {
      return { message: responseData.message || 'Something went wrong', ok: false }
    }
  } catch (error) {
    return { message: error.message || 'Something went wrong', ok: false }
  }
}
