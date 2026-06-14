const API_URL = import.meta.env.VITE_API_URL

export async function callApi(url, method, data) {
  try {
    const response = await fetch(`${API_URL}${url}`, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    })
    const responseData = await response.json()
    return { data: responseData, ok: true }
  } catch (error) {
    return { message: error.message, ok: false }
  }
}
