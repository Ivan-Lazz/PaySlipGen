<template>
  <DashboardLayout pageTitle="Payslip Generator">
    <div class="flex flex-col md:flex-row gap-6">
      <!-- Left side - Input form -->
      <div class="w-full md:w-1/2 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Payslip Information</h2>
        
        <form @submit.prevent="generatePayslip" class="space-y-4">
          <div>
            <label class="form-label">Agent Name</label>
            <input v-model="payslipData.agent_name" type="text" class="form-input" required>
          </div>
          
          <div>
            <label class="form-label">Employee ID</label>
            <select v-model="payslipData.employee_id" class="form-input" @change="loadEmployeeDetails" required>
              <option value="">Select Employee</option>
              <option v-for="employee in employees" :key="employee.employee_id" :value="employee.employee_id">
                {{ employee.employee_id }} - {{ employee.firstname }} {{ employee.lastname }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="form-label">Bank Account</label>
            <select v-model="payslipData.bank_acct" class="form-input" :disabled="!payslipData.employee_id" required>
              <option value="">Select Bank Account</option>
              <option v-for="account in bankAccounts" :key="account.bank_account" :value="account.bank_account">
                {{ account.preferred_bank }} - {{ account.bank_account }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="form-label">Bank Details/Bank Holder</label>
            <input v-model="bankDetails" type="text" class="form-input bg-gray-100" readonly>
          </div>
          
          <div>
            <label class="form-label">Person In Charge</label>
            <input v-model="payslipData.person_in_charge" type="text" class="form-input bg-gray-100" readonly>
          </div>
          
          <div>
            <label class="form-label">Date</label>
            <input v-model="payslipData.date_of_payment" type="date" class="form-input" required>
          </div>
          
          <div>
            <label class="form-label">Cutoff Date</label>
            <input v-model="payslipData.cutoff_date" type="date" class="form-input" required>
          </div>
          
          <div>
            <label class="form-label">Salary</label>
            <input v-model.number="payslipData.salary" type="number" step="0.01" min="0" class="form-input" required @input="calculateTotal">
          </div>
          
          <div>
            <label class="form-label">Bonus</label>
            <input v-model.number="payslipData.bonus" type="number" step="0.01" min="0" class="form-input" @input="calculateTotal">
          </div>
          
          <div>
            <label class="form-label">Total Salary</label>
            <input v-model.number="payslipData.amount" type="number" step="0.01" min="0" class="form-input bg-gray-100" readonly>
          </div>
          
          <div>
            <label class="form-label">Payment Status</label>
            <select v-model="payslipData.payment_status" class="form-input" required>
              <option value="PENDING">Pending</option>
              <option value="PAID">Paid</option>
              <option value="CANCELLED">Cancelled</option>
            </select>
          </div>
          
          <div class="pt-4">
            <button type="submit" class="btn btn-primary">Generate Payslip</button>
            <button type="button" class="btn btn-secondary ml-2" @click="resetForm">Reset</button>
          </div>
        </form>
      </div>
      
      <!-- Right side - Payslip preview -->
      <div class="w-full md:w-1/2">
        <div v-if="showPreview" class="bg-white rounded-lg shadow-md p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold">Payslip Preview</h2>
            <div class="space-x-2">
              <button @click="printPayslip" class="btn btn-primary">Print</button>
              <button @click="downloadPDF" class="btn btn-success">Download</button>
            </div>
          </div>
          
          <div id="payslip-preview" class="border border-gray-200 p-6 rounded-lg">
            <!-- Company Header -->
            <div class="text-center mb-6">
              <h3 class="text-xl font-bold">COMPANY NAME</h3>
              <p class="text-sm">123 Business Street, City, Country</p>
              <p class="text-sm">Phone: (123) 456-7890</p>
            </div>
            
            <div class="border-b-2 border-gray-300 mb-4"></div>
            
            <h4 class="text-lg font-bold text-center mb-4">PAYSLIP</h4>
            
            <div class="border-b border-gray-300 mb-4"></div>
            
            <!-- Employee Details -->
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div>
                <p class="text-sm"><span class="font-semibold">Employee ID:</span> {{ payslipData.employee_id }}</p>
                <p class="text-sm"><span class="font-semibold">Agent Name:</span> {{ payslipData.agent_name }}</p>
                <p class="text-sm"><span class="font-semibold">Bank Account:</span> {{ payslipData.bank_acct }}</p>
                <p class="text-sm"><span class="font-semibold">Bank Holder:</span> {{ bankDetails }}</p>
              </div>
              <div>
                <p class="text-sm"><span class="font-semibold">Date:</span> {{ formattedDate(payslipData.date_of_payment) }}</p>
                <p class="text-sm"><span class="font-semibold">Cutoff Date:</span> {{ formattedDate(payslipData.cutoff_date) }}</p>
                <p class="text-sm"><span class="font-semibold">Person In Charge:</span> {{ payslipData.person_in_charge }}</p>
                <p class="text-sm"><span class="font-semibold">Status:</span> 
                  <span :class="{
                    'text-green-600': payslipData.payment_status === 'PAID',
                    'text-yellow-600': payslipData.payment_status === 'PENDING',
                    'text-red-600': payslipData.payment_status === 'CANCELLED'
                  }">{{ payslipData.payment_status }}</span>
                </p>
              </div>
            </div>
            
            <!-- Payment Table -->
            <div class="mb-6">
              <div class="bg-gray-100 px-4 py-2 font-semibold border-t border-b border-gray-300 grid grid-cols-2">
                <div>Description</div>
                <div class="text-right">Amount</div>
              </div>
              
              <div class="px-4 py-2 border-b border-gray-200 grid grid-cols-2">
                <div>Salary</div>
                <div class="text-right">PHP {{ formatCurrency(payslipData.salary) }}</div>
              </div>
              
              <div class="px-4 py-2 border-b border-gray-200 grid grid-cols-2">
                <div>Bonus</div>
                <div class="text-right">PHP {{ formatCurrency(payslipData.bonus || 0) }}</div>
              </div>
              
              <div class="bg-gray-100 px-4 py-2 font-semibold grid grid-cols-2">
                <div>Total</div>
                <div class="text-right">PHP {{ formatCurrency(payslipData.amount) }}</div>
              </div>
            </div>
            
            <div class="mt-8 pt-4 border-t border-gray-300 text-center text-sm text-gray-500">
              <p>This is an electronically generated payslip and does not require a signature.</p>
              <p>Payslip No: {{ payslipNo }}</p>
            </div>
          </div>
        </div>
        
        <div v-else class="bg-white rounded-lg shadow-md p-6 h-full flex items-center justify-center">
          <p class="text-gray-500 text-center">
            Fill the form and click "Generate Payslip" to see the preview
          </p>
        </div>
      </div>
    </div>
    
    <!-- Success Message -->
    <div v-if="successMessage" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <div class="flex items-center justify-center mb-4 text-green-500">
          <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <h3 class="text-lg font-bold text-center mb-2">Success!</h3>
        <p class="text-center mb-4">{{ successMessage }}</p>
        <div class="flex justify-center">
          <button @click="successMessage = ''" class="btn btn-primary w-full">Close</button>
        </div>
      </div>
    </div>
    
    <!-- Error Message -->
    <div v-if="errorMessage" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <div class="flex items-center justify-center mb-4 text-red-500">
          <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-bold text-center mb-2">Error</h3>
        <p class="text-center mb-4">{{ errorMessage }}</p>
        <div class="flex justify-center">
          <button @click="errorMessage = ''" class="btn btn-danger w-full">Close</button>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import DashboardLayout from '../layouts/DashboardLayout.vue'
import api from '../services/api'
import AuthService from '../services/auth'
import html2canvas from 'html2canvas'
import { jsPDF } from 'jspdf'

export default {
  name: 'Payslips',
  components: {
    DashboardLayout
  },
  setup() {
    // State
    const employees = ref([])
    const bankAccounts = ref([])
    const showPreview = ref(false)
    const successMessage = ref('')
    const errorMessage = ref('')
    const payslipNo = ref('')
    const bankDetails = ref('')
    
    // Current user
    const currentUser = AuthService.getCurrentUser()
    
    // Payslip form data
    const payslipData = reactive({
      employee_id: '',
      agent_name: '',
      bank_acct: '',
      salary: 0,
      bonus: 0,
      amount: 0,
      person_in_charge: currentUser ? currentUser.firstname + ' ' + currentUser.lastname : 'Admin User',
      cutoff_date: new Date().toISOString().substr(0, 10),
      date_of_payment: new Date().toISOString().substr(0, 10),
      payment_status: 'PENDING'
    })
    
    // Load employees on component mount
    onMounted(async () => {
      try {
        await loadEmployees()
      } catch (error) {
        errorMessage.value = 'Failed to load employees. Please try again.'
        console.error(error)
      }
    })
    
    // Load employees from API
    const loadEmployees = async () => {
      try {
        const response = await api.get('/employees')
        if (response.data.status === 'success') {
          employees.value = response.data.data.data
        }
      } catch (error) {
        console.error('Error loading employees:', error)
        throw error
      }
    }
    
    // Load employee details when ID changes
    const loadEmployeeDetails = async () => {
      if (!payslipData.employee_id) {
        bankAccounts.value = []
        return
      }
      
      try {
        const response = await api.get(`/employees/${payslipData.employee_id}`)
        if (response.data.status === 'success') {
          const employee = response.data.data
          
          // Set agent name to employee name
          payslipData.agent_name = `${employee.firstname} ${employee.lastname}`
          
          // Load bank accounts
          if (employee.banking && employee.banking.length > 0) {
            bankAccounts.value = employee.banking
          } else {
            bankAccounts.value = []
          }
        }
      } catch (error) {
        console.error('Error loading employee details:', error)
        errorMessage.value = 'Failed to load employee details. Please try again.'
      }
    }
    
    // Watch for bank account changes
    const updateBankDetails = computed(() => {
      const selectedAccount = bankAccounts.value.find(account => account.bank_account === payslipData.bank_acct)
      
      if (selectedAccount) {
        bankDetails.value = selectedAccount.bank_details || `${selectedAccount.preferred_bank} - ${selectedAccount.bank_account}`
        return bankDetails.value
      }
      
      bankDetails.value = ''
      return ''
    })
    
    // Calculate total amount
    const calculateTotal = () => {
      const salary = parseFloat(payslipData.salary) || 0
      const bonus = parseFloat(payslipData.bonus) || 0
      payslipData.amount = salary + bonus
    }
    
    // Format currency
    const formatCurrency = (value) => {
      return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(value)
    }
    
    // Format date
    const formattedDate = (dateString) => {
      if (!dateString) return ''
      
      const date = new Date(dateString)
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      }).format(date)
    }
    
    // Generate payslip
    const generatePayslip = async () => {
      if (!payslipData.employee_id || !payslipData.bank_acct) {
        errorMessage.value = 'Please fill all required fields.'
        return
      }
      
      try {
        const response = await api.post('/payslips', {
          employee_id: payslipData.employee_id,
          bank_acct: payslipData.bank_acct,
          salary: payslipData.salary,
          bonus: payslipData.bonus || 0,
          amount: payslipData.amount,
          person_in_charge: payslipData.person_in_charge,
          cutoff_date: payslipData.cutoff_date,
          date_of_payment: payslipData.date_of_payment,
          payment_status: payslipData.payment_status
        })
        
        if (response.data.status === 'success') {
          successMessage.value = 'Payslip generated successfully!'
          payslipNo.value = response.data.data.payslip_no
          showPreview.value = true
        }
      } catch (error) {
        console.error('Error generating payslip:', error)
        errorMessage.value = error.response?.data?.message || 'Failed to generate payslip. Please try again.'
      }
    }
    
    // Reset form
    const resetForm = () => {
      Object.assign(payslipData, {
        employee_id: '',
        agent_name: '',
        bank_acct: '',
        salary: 0,
        bonus: 0,
        amount: 0,
        person_in_charge: currentUser ? currentUser.firstname + ' ' + currentUser.lastname : 'Admin User',
        cutoff_date: new Date().toISOString().substr(0, 10),
        date_of_payment: new Date().toISOString().substr(0, 10),
        payment_status: 'PENDING'
      })
      
      bankAccounts.value = []
      showPreview.value = false
      payslipNo.value = ''
    }
    
    // Print payslip
    const printPayslip = () => {
      const printContent = document.getElementById('payslip-preview')
      const originalContents = document.body.innerHTML
      
      document.body.innerHTML = printContent.innerHTML
      
      window.print()
      
      document.body.innerHTML = originalContents
      window.location.reload()
    }
    
    // Download payslip as PDF
    const downloadPDF = async () => {
      const element = document.getElementById('payslip-preview')
      
      try {
        const canvas = await html2canvas(element, {
          scale: 2
        })
        
        const imgData = canvas.toDataURL('image/png')
        const pdf = new jsPDF('p', 'mm', 'a4')
        
        const imgWidth = 210
        const imgHeight = canvas.height * imgWidth / canvas.width
        
        pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight)
        
        const filename = `Payslip_${payslipData.employee_id}_${new Date().toISOString().slice(0, 10)}.pdf`
        pdf.save(filename)
      } catch (error) {
        console.error('Error generating PDF:', error)
        errorMessage.value = 'Failed to generate PDF. Please try again.'
      }
    }
    
    return {
      employees,
      bankAccounts,
      payslipData,
      showPreview,
      successMessage,
      errorMessage,
      payslipNo,
      bankDetails,
      loadEmployeeDetails,
      calculateTotal,
      generatePayslip,
      resetForm,
      formatCurrency,
      formattedDate,
      printPayslip,
      downloadPDF,
      updateBankDetails
    }
  }
}
</script>