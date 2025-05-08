import api from './api'

const AuthService = {
  /**
   * Login user and store token
   * 
   * @param {Object} credentials - User credentials
   * @param {string} credentials.email - User email
   * @param {string} credentials.password - User password 
   * @returns {Promise} - Response from API
   */
  async login(credentials) {
    try {
      const response = await api.post('/login', credentials)
      
      if (response.data.status === 'success' && response.data.data.token) {
        localStorage.setItem('token', response.data.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.data.user))
      }
      
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Login failed' }
    }
  },
  
  /**
   * Logout user and remove token
   * 
   * @returns {Promise} - Response from API
   */
  async logout() {
    try {
      const response = await api.post('/logout')
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      return response.data
    } catch (error) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      throw error.response?.data || { message: 'Logout failed' }
    }
  },
  
  /**
   * Check if user is logged in
   * 
   * @returns {boolean} - Whether user is logged in
   */
  isLoggedIn() {
    return !!localStorage.getItem('token')
  },
  
  /**
   * Get current user information
   * 
   * @returns {Object|null} - User object or null if not logged in
   */
  getCurrentUser() {
    const userStr = localStorage.getItem('user')
    if (!userStr) return null
    
    try {
      return JSON.parse(userStr)
    } catch (e) {
      return null
    }
  },
  
  /**
   * Get auth token
   * 
   * @returns {string|null} - Auth token or null if not logged in
   */
  getToken() {
    return localStorage.getItem('token')
  },
  
  /**
   * Verify auth token
   * 
   * @returns {Promise} - Response from API
   */
  async verifyToken() {
    try {
      const response = await api.post('/verify')
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Token verification failed' }
    }
  }
}

export default AuthService