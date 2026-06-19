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

    const rawResponse = await response.text()
    let responseData = null

    if (rawResponse) {
      try {
        responseData = JSON.parse(rawResponse)
      } catch {
        responseData = { message: rawResponse }
      }
    }

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
    console.log({ message: error.message || 'Something went wrong', ok: false })
    return { message: error.message || 'Something went wrong', ok: false }
  }
}
