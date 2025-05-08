import api from './api'

const EmployeeService = {
  /**
   * Get all employees with pagination
   * 
   * @param {Object} params - Pagination and search parameters
   * @param {number} params.page - Page number
   * @param {number} params.records_per_page - Records per page
   * @param {string} params.search - Search term
   * @returns {Promise} - Response from API
   */
  async getAll(params = {}) {
    try {
      const queryParams = new URLSearchParams()
      
      if (params.page) queryParams.append('page', params.page)
      if (params.records_per_page) queryParams.append('records_per_page', params.records_per_page)
      if (params.search) queryParams.append('search', params.search)
      
      const response = await api.get(`/employees?${queryParams.toString()}`)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch employees' }
    }
  },
  
  /**
   * Get a single employee by ID
   * 
   * @param {string} id - Employee ID
   * @returns {Promise} - Response from API
   */
  async getById(id) {
    try {
      const response = await api.get(`/employees/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch employee' }
    }
  },
  
  /**
   * Create a new employee
   * 
   * @param {Object} employeeData - Employee data
   * @returns {Promise} - Response from API
   */
  async create(employeeData) {
    try {
      const response = await api.post('/employees', employeeData)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to create employee' }
    }
  },
  
  /**
   * Update an employee
   * 
   * @param {string} id - Employee ID
   * @param {Object} employeeData - Employee data
   * @returns {Promise} - Response from API
   */
  async update(id, employeeData) {
    try {
      const response = await api.put(`/employees/${id}`, employeeData)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to update employee' }
    }
  },
  
  /**
   * Delete an employee
   * 
   * @param {string} id - Employee ID
   * @returns {Promise} - Response from API
   */
  async delete(id) {
    try {
      const response = await api.delete(`/employees/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to delete employee' }
    }
  },
  
  /**
   * Get employee's bank accounts
   * 
   * @param {string} id - Employee ID
   * @returns {Promise} - Response from API
   */
  async getBankAccounts(id) {
    try {
      // This would typically be a separate endpoint in the API
      // For now, we'll use the getById method which includes banking information
      const response = await this.getById(id)
      
      if (response.status === 'success' && response.data.banking) {
        return {
          status: 'success',
          data: response.data.banking
        }
      }
      
      return {
        status: 'error',
        message: 'No banking information found'
      }
    } catch (error) {
      throw error
    }
  }
}

export default EmployeeService