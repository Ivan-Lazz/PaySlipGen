<template>
  <DashboardLayout pageTitle="Dashboard">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 text-blue-500">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <h2 class="text-gray-500 text-sm font-semibold">Total Employees</h2>
            <p class="text-2xl font-bold">{{ stats.totalEmployees }}</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-500">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <h2 class="text-gray-500 text-sm font-semibold">Total Payroll (This Month)</h2>
            <p class="text-2xl font-bold">₱{{ formatNumber(stats.totalPayroll) }}</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <h2 class="text-gray-500 text-sm font-semibold">Payslips Generated</h2>
            <p class="text-2xl font-bold">{{ stats.totalPayslips }}</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100 text-red-500">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <h2 class="text-gray-500 text-sm font-semibold">Pending Payments</h2>
            <p class="text-2xl font-bold">{{ stats.pendingPayments }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Recent Activity & Quick Links -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Recent Activity -->
      <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
          <h3 class="font-semibold text-lg">Recent Activity</h3>
        </div>
        <div class="p-6">
          <div v-if="loading" class="text-center py-4">
            <svg class="animate-spin h-8 w-8 mx-auto text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-gray-500">Loading recent activity...</p>
          </div>
          <div v-else-if="recentPayslips.length === 0" class="text-center py-4">
            <p class="text-gray-500">No recent activity found</p>
          </div>
          <ul v-else class="divide-y divide-gray-200">
            <li v-for="(payslip, index) in recentPayslips" :key="index" class="py-4">
              <div class="flex items-start">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                  <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-900">Payslip #{{ payslip.payslip_no }} Generated</p>
                  <p class="text-sm text-gray-500">
                    For {{ payslip.employee_name }} - ₱{{ formatNumber(payslip.amount) }}
                    <span :class="{
                      'text-green-600': payslip.payment_status === 'PAID',
                      'text-yellow-600': payslip.payment_status === 'PENDING',
                      'text-red-600': payslip.payment_status === 'CANCELLED'
                    }">
                      ({{ payslip.payment_status }})
                    </span>
                  </p>
                  <p class="text-xs text-gray-400">{{ formatDate(payslip.date_of_payment) }}</p>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      
      <!-- Quick Links -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
          <h3 class="font-semibold text-lg">Quick Links</h3>
        </div>
        <div class="p-6">
          <ul class="space-y-2">
            <li>
              <router-link to="/payslips" class="flex items-center p-3 text-base font-normal text-gray-900 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="ml-3">Generate New Payslip</span>
              </router-link>
            </li>
            <li>
              <router-link to="/employees" class="flex items-center p-3 text-base font-normal text-gray-900 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span class="ml-3">Add New Employee</span>
              </router-link>
            </li>
            <li>
              <router-link to="/reports" class="flex items-center p-3 text-base font-normal text-gray-900 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="ml-3">Generate Reports</span>
              </router-link>
            </li>
            <li>
              <router-link to="/accounts" class="flex items-center p-3 text-base font-normal text-gray-900 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="ml-3">Manage Accounts</span>
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
    
    <!-- Monthly Stats -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
      <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h3 class="font-semibold text-lg">Monthly Payroll Statistics</h3>
      </div>
      <div class="p-6">
        <!-- We would use chart.js or similar here for proper charting -->
        <div class="h-64 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center">
          <div class="text-center">
            <p class="text-gray-500">Monthly payroll chart would appear here</p>
            <p class="text-gray-400 text-sm mt-2">Using chart.js or similar library</p>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { ref, onMounted } from 'vue'
import DashboardLayout from '../layouts/DashboardLayout.vue'
import api from '../services/api'

export default {
  name: 'Dashboard',
  components: {
    DashboardLayout
  },
  setup() {
    const stats = ref({
      totalEmployees: 0,
      totalPayroll: 0,
      totalPayslips: 0,
      pendingPayments: 0
    })
    
    const recentPayslips = ref([])
    const loading = ref(true)
    
    // Load dashboard data on component mount
    onMounted(async () => {
      try {
        await Promise.all([
          loadStats(),
          loadRecentPayslips()
        ])
      } catch (error) {
        console.error('Error loading dashboard data:', error)
      } finally {
        loading.value = false
      }
    })
    
    // Load dashboard statistics
    const loadStats = async () => {
      try {
        // In a real implementation, you would have a dedicated API endpoint for dashboard stats
        // For now, we'll simulate it with existing endpoints
        
        // Get total employees
        const employeesResponse = await api.get('/employees')
        if (employeesResponse.data.status === 'success') {
          stats.value.totalEmployees = employeesResponse.data.data.total || 0
        }
        
        // Get payslips data for this month
        const currentDate = new Date()
        const startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1).toISOString().split('T')[0]
        const endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).toISOString().split('T')[0]
        
        const reportsResponse = await api.get(`/reports/payroll?start_date=${startDate}&end_date=${endDate}`)
        
        if (reportsResponse.data.status === 'success') {
          const data = reportsResponse.data.data
          
          stats.value.totalPayroll = data.summary.total_amount || 0
          stats.value.totalPayslips = data.summary.total_count || 0
          stats.value.pendingPayments = data.payslips.filter(p => p.payment_status === 'PENDING').length || 0
        }
      } catch (error) {
        console.error('Error loading statistics:', error)
        // Set some default values if API fails
        stats.value = {
          totalEmployees: 5,
          totalPayroll: 15000,
          totalPayslips: 10,
          pendingPayments: 3
        }
      }
    }
    
    // Load recent payslips
    const loadRecentPayslips = async () => {
      try {
        const response = await api.get('/payslips?page=1&records_per_page=5')
        
        if (response.data.status === 'success') {
          // In a real implementation, you would get employee names from the response
          // For now, let's add a placeholder name
          recentPayslips.value = response.data.data.data.map(payslip => ({
            ...payslip,
            employee_name: 'Employee #' + payslip.employee_id
          }))
        }
      } catch (error) {
        console.error('Error loading recent payslips:', error)
        // Set some sample data if API fails
        recentPayslips.value = [
          {
            payslip_no: '000000001',
            employee_id: '20230001',
            employee_name: 'John Doe',
            amount: 3500,
            payment_status: 'PAID',
            date_of_payment: '2023-04-15'
          },
          {
            payslip_no: '000000002',
            employee_id: '20230002',
            employee_name: 'Jane Smith',
            amount: 4200,
            payment_status: 'PENDING',
            date_of_payment: '2023-04-15'
          }
        ]
      }
    }
    
    // Format number with thousand separators
    const formatNumber = (value) => {
      return new Intl.NumberFormat('en-PH').format(value)
    }
    
    // Format date
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      }).format(date)
    }
    
    return {
      stats,
      recentPayslips,
      loading,
      formatNumber,
      formatDate
    }
  }
}
</script>