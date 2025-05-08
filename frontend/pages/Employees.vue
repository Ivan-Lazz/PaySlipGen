<template>
  <DashboardLayout pageTitle="Employees">
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between p-6 border-b border-gray-200">
        <div class="flex-1">
          <h2 class="text-lg font-medium text-gray-900">Employee List</h2>
          <p class="mt-1 text-sm text-gray-500">Manage your employees and their information</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
          <div class="relative">
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Search employees..."
              class="form-input pl-10"
              @input="debounceSearch"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
          <button @click="openAddModal" class="btn btn-primary">
            <svg class="h-5 w-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Employee
          </button>
        </div>
      </div>

      <!-- Loading indicator -->
      <div v-if="loading" class="flex justify-center items-center p-12">
        <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>

      <!-- Employee Table -->
      <div v-else-if="employees.length === 0" class="p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No employees found</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new employee.</p>
        <div class="mt-6">
          <button @click="openAddModal" type="button" class="btn btn-primary">
            <svg class="h-5 w-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Employee
          </button>
        </div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Employee ID
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Name
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Contact
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email
              </th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="employee in employees" :key="employee.employee_id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ employee.employee_id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ employee.firstname }} {{ employee.lastname }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ employee.contact_number }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ employee.email }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button @click="openEditModal(employee)" class="text-blue-600 hover:text-blue-900 mr-3">
                  Edit
                </button>
                <button @click="confirmDelete(employee)" class="text-red-600 hover:text-red-900">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="employees.length > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
          <button @click="prevPage" :disabled="currentPage === 1" class="btn btn-secondary" :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
            Previous
          </button>
          <button @click="nextPage" :disabled="currentPage === totalPages" class="btn btn-secondary" :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }">
            Next
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span>
              to
              <span class="font-medium">{{ Math.min(currentPage * perPage, totalItems) }}</span>
              of
              <span class="font-medium">{{ totalItems }}</span>
              results
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button @click="prevPage" :disabled="currentPage === 1" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
              
              <span v-for="page in paginationRange" :key="page">
                <button
                  v-if="page !== '...'"
                  @click="goToPage(page)"
                  :class="[
                    page === currentPage ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                  ]"
                >
                  {{ page }}
                </button>
                <span v-else class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                  ...
                </span>
              </span>
              
              <button @click="nextPage" :disabled="currentPage === totalPages" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Employee Modal -->
    <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  {{ isEditing ? 'Edit Employee' : 'Add New Employee' }}
                </h3>
                <div class="mt-4">
                  <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                      <label for="firstname" class="form-label">First Name</label>
                      <input type="text" id="firstname" v-model="formData.firstname" class="form-input" required />
                    </div>
                    <div>
                      <label for="lastname" class="form-label">Last Name</label>
                      <input type="text" id="lastname" v-model="formData.lastname" class="form-input" required />
                    </div>
                    <div>
                      <label for="contact_number" class="form-label">Contact Number</label>
                      <input type="text" id="contact_number" v-model="formData.contact_number" class="form-input" required />
                    </div>
                    <div>
                      <label for="email" class="form-label">Email Address</label>
                      <input type="email" id="email" v-model="formData.email" class="form-input" required />
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="submitForm" type="button" class="btn btn-primary ml-3">
              {{ isEditing ? 'Update' : 'Save' }}
            </button>
            <button @click="closeModal" type="button" class="btn btn-secondary">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Delete Employee
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Are you sure you want to delete {{ selectedEmployee?.firstname }} {{ selectedEmployee?.lastname }}? This action cannot be undone.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="deleteEmployee" type="button" class="btn btn-danger ml-3">
              Delete
            </button>
            <button @click="cancelDelete" type="button" class="btn btn-secondary">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Toast -->
    <div v-if="successMessage" class="fixed bottom-0 right-0 m-6 bg-green-50 border-l-4 border-green-500 p-4 max-w-md">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-green-700">{{ successMessage }}</p>
        </div>
        <div class="ml-auto pl-3">
          <div class="-mx-1.5 -my-1.5">
            <button @click="successMessage = ''" class="inline-flex text-green-500 focus:outline-none focus:text-green-700">
              <span class="sr-only">Dismiss</span>
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Toast -->
    <div v-if="errorMessage" class="fixed bottom-0 right-0 m-6 bg-red-50 border-l-4 border-red-500 p-4 max-w-md">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700">{{ errorMessage }}</p>
        </div>
        <div class="ml-auto pl-3">
          <div class="-mx-1.5 -my-1.5">
            <button @click="errorMessage = ''" class="inline-flex text-red-500 focus:outline-none focus:text-red-700">
              <span class="sr-only">Dismiss</span>
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import DashboardLayout from '../layouts/DashboardLayout.vue'
import EmployeeService from '../services/employees'

export default {
  name: 'Employees',
  components: {
    DashboardLayout
  },
  setup() {
    const employees = ref([])
    const loading = ref(true)
    const searchTerm = ref('')
    const searchTimeout = ref(null)
    const currentPage = ref(1)
    const perPage = ref(10)
    const totalItems = ref(0)
    const totalPages = ref(1)
    
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const isEditing = ref(false)
    const selectedEmployee = ref(null)
    const successMessage = ref('')
    const errorMessage = ref('')
    
    const formData = reactive({
      firstname: '',
      lastname: '',
      contact_number: '',
      email: ''
    })
    
    // Load employees when component mounts
    onMounted(() => {
      fetchEmployees()
    })
    
    // Fetch employees from API
    const fetchEmployees = async () => {
      loading.value = true
      
      try {
        const response = await EmployeeService.getAll({
          page: currentPage.value,
          records_per_page: perPage.value,
          search: searchTerm.value
        })
        
        if (response.status === 'success') {
          employees.value = response.data.data
          totalItems.value = response.data.total
          totalPages.value = response.data.last_page
        } else {
          errorMessage.value = response.message || 'Failed to fetch employees'
        }
      } catch (error) {
        console.error('Error fetching employees:', error)
        errorMessage.value = error.message || 'Failed to fetch employees'
        
        // Set some sample data for development
        employees.value = [
          {
            employee_id: '202300001',
            firstname: 'John',
            lastname: 'Doe',
            contact_number: '09123456789',
            email: 'john@example.com'
          },
          {
            employee_id: '202300002',
            firstname: 'Jane',
            lastname: 'Smith',
            contact_number: '09234567890',
            email: 'jane@example.com'
          }
        ]
        totalItems.value = 2
        totalPages.value = 1
      } finally {
        loading.value = false
      }
    }
    
    // Calculate pagination range
    const paginationRange = computed(() => {
      const range = []
      const maxVisiblePages = 5
      
      if (totalPages.value <= maxVisiblePages) {
        // Show all pages if total pages are less than max visible
        for (let i = 1; i <= totalPages.value; i++) {
          range.push(i)
        }
      } else {
        // Complex pagination logic for many pages
        let start = Math.max(1, currentPage.value - Math.floor(maxVisiblePages / 2))
        let end = Math.min(totalPages.value, start + maxVisiblePages - 1)
        
        // Adjust start if end is at max pages
        if (end === totalPages.value) {
          start = Math.max(1, end - maxVisiblePages + 1)
        }
        
        // Add first page if not included
        if (start > 1) {
          range.push(1)
          if (start > 2) {
            range.push('...')
          }
        }
        
        // Add middle pages
        for (let i = start; i <= end; i++) {
          range.push(i)
        }
        
        // Add last page if not included
        if (end < totalPages.value) {
          if (end < totalPages.value - 1) {
            range.push('...')
          }
          range.push(totalPages.value)
        }
      }
      
      return range
    })
    
    // Pagination methods
    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--
        fetchEmployees()
      }
    }
    
    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++
        fetchEmployees()
      }
    }
    
    const goToPage = (page) => {
      if (page !== currentPage.value) {
        currentPage.value = page
        fetchEmployees()
      }
    }
    
    // Search with debounce
    const debounceSearch = () => {
      if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
      }
      
      searchTimeout.value = setTimeout(() => {
        currentPage.value = 1 // Reset to first page
        fetchEmployees()
      }, 300)
    }
    
    // Modal handlers
    const openAddModal = () => {
      isEditing.value = false
      resetForm()
      showModal.value = true
    }
    
    const openEditModal = (employee) => {
      isEditing.value = true
      selectedEmployee.value = employee
      
      // Set form data
      formData.firstname = employee.firstname
      formData.lastname = employee.lastname
      formData.contact_number = employee.contact_number
      formData.email = employee.email
      
      showModal.value = true
    }
    
    const closeModal = () => {
      showModal.value = false
      resetForm()
    }
    
    const resetForm = () => {
      formData.firstname = ''
      formData.lastname = ''
      formData.contact_number = ''
      formData.email = ''
      selectedEmployee.value = null
    }
    
    // Submit form
    const submitForm = async () => {
      if (!formData.firstname || !formData.lastname || !formData.contact_number || !formData.email) {
        errorMessage.value = 'Please fill in all fields'
        return
      }
      
      try {
        if (isEditing.value) {
          // Update employee
          const response = await EmployeeService.update(selectedEmployee.value.employee_id, formData)
          
          if (response.status === 'success') {
            successMessage.value = 'Employee updated successfully'
            setTimeout(() => { successMessage.value = '' }, 3000)
          } else {
            errorMessage.value = response.message || 'Failed to update employee'
            setTimeout(() => { errorMessage.value = '' }, 3000)
          }
        } else {
          // Create employee
          const response = await EmployeeService.create(formData)
          
          if (response.status === 'success') {
            successMessage.value = 'Employee created successfully'
            setTimeout(() => { successMessage.value = '' }, 3000)
          } else {
            errorMessage.value = response.message || 'Failed to create employee'
            setTimeout(() => { errorMessage.value = '' }, 3000)
          }
        }
        
        closeModal()
        fetchEmployees()
      } catch (error) {
        console.error('Error submitting form:', error)
        errorMessage.value = error.message || 'An error occurred'
        setTimeout(() => { errorMessage.value = '' }, 3000)
      }
    }
    
    // Delete employee
    const confirmDelete = (employee) => {
      selectedEmployee.value = employee
      showDeleteModal.value = true
    }
    
    const cancelDelete = () => {
      showDeleteModal.value = false
      selectedEmployee.value = null
    }
    
    const deleteEmployee = async () => {
      if (!selectedEmployee.value) return
      
      try {
        const response = await EmployeeService.delete(selectedEmployee.value.employee_id)
        
        if (response.status === 'success') {
          successMessage.value = 'Employee deleted successfully'
          setTimeout(() => { successMessage.value = '' }, 3000)
        } else {
          errorMessage.value = response.message || 'Failed to delete employee'
          setTimeout(() => { errorMessage.value = '' }, 3000)
        }
        
        showDeleteModal.value = false
        selectedEmployee.value = null
        fetchEmployees()
      } catch (error) {
        console.error('Error deleting employee:', error)
        errorMessage.value = error.message || 'An error occurred'
        setTimeout(() => { errorMessage.value = '' }, 3000)
      }
    }
    
    return {
      employees,
      loading,
      searchTerm,
      currentPage,
      perPage,
      totalItems,
      totalPages,
      paginationRange,
      prevPage,
      nextPage,
      goToPage,
      debounceSearch,
      showModal,
      showDeleteModal,
      isEditing,
      selectedEmployee,
      formData,
      successMessage,
      errorMessage,
      openAddModal,
      openEditModal,
      closeModal,
      submitForm,
      confirmDelete,
      cancelDelete,
      deleteEmployee
    }
  }
}
</script>