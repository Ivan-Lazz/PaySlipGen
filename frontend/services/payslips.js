import api from './api'

const PayslipService = {
  /**
   * Get all payslips with pagination
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
      
      const response = await api.get(`/payslips?${queryParams.toString()}`)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch payslips' }
    }
  },
  
  /**
   * Get a single payslip by ID
   * 
   * @param {string} id - Payslip ID
   * @returns {Promise} - Response from API
   */
  async getById(id) {
    try {
      const response = await api.get(`/payslips/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch payslip' }
    }
  },
  
  /**
   * Create a new payslip
   * 
   * @param {Object} payslipData - Payslip data
   * @returns {Promise} - Response from API
   */
  async create(payslipData) {
    try {
      const response = await api.post('/payslips', payslipData)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to create payslip' }
    }
  },
  
  /**
   * Update a payslip
   * 
   * @param {string} id - Payslip ID
   * @param {Object} payslipData - Payslip data
   * @returns {Promise} - Response from API
   */
  async update(id, payslipData) {
    try {
      const response = await api.put(`/payslips/${id}`, payslipData)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to update payslip' }
    }
  },
  
  /**
   * Delete a payslip
   * 
   * @param {string} id - Payslip ID
   * @returns {Promise} - Response from API
   */
  async delete(id) {
    try {
      const response = await api.delete(`/payslips/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to delete payslip' }
    }
  },
  
  /**
   * Generate a payroll report for a date range
   * 
   * @param {string} startDate - Start date (YYYY-MM-DD)
   * @param {string} endDate - End date (YYYY-MM-DD)
   * @returns {Promise} - Response from API
   */
  async generateReport(startDate, endDate) {
    try {
      const response = await api.get(`/reports/payroll?start_date=${startDate}&end_date=${endDate}`)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to generate report' }
    }
  },
  
  /**
   * Generate a report for a specific employee
   * 
   * @param {string} employeeId - Employee ID
   * @returns {Promise} - Response from API
   */
  async generateEmployeeReport(employeeId) {
    try {
      const response = await api.get(`/reports/employee/${employeeId}`)
      return response.data
    } catch (error) {
      throw error.response?.data || { message: 'Failed to generate employee report' }
    }
  }
}

export default PayslipService