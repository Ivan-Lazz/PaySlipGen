import axios from 'axios'

// Create axios instance with baseURL pointing to your API
const api = axios.create({
  baseURL: 'http://localhost/PaySlipGen',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Add request interceptor for auth token
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

// Add response interceptor for auth errors
api.interceptors.response.use(
  response => {
    return response
  },
  error => {
    // Handle 401 unauthorized errors
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default api