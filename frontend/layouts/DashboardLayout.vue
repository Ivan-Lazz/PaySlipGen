<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="hidden md:flex md:flex-shrink-0">
      <div class="flex flex-col w-64 bg-gray-800">
        <div class="flex items-center justify-center h-16 bg-gray-900">
          <span class="text-white font-bold text-lg">Payroll System</span>
        </div>
        <div class="flex-grow flex flex-col pt-5 pb-4 overflow-y-auto">
          <nav class="flex-1 px-2 space-y-1">
            <router-link to="/dashboard" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md" :class="[$route.path === '/dashboard' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
              <svg class="mr-3 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              Dashboard
            </router-link>
            
            <router-link to="/users" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md" :class="[$route.path === '/users' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
              <svg class="mr-3 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              Users
            </router-link>
            
            <router-link to="/employees" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md" :class="[$route.path === '/employees' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
              <svg class="mr-3 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Employees
            </router-link>
            
            <router-link to="/accounts" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md" :class="[$route.path === '/accounts' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
              <svg class="mr-3 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
              Accounts
            </router-link>
            
            <router-link to="/payslips" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md" :class="[$route.path === '/payslips' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
              <svg class="mr-3 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Payslips
            </router-link>
            
            <router-link to="/reports" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md" :class="[$route.path === '/reports' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
              <svg class="mr-3 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Reports
            </router-link>
          </nav>
        </div>
        <div class="flex-shrink-0 flex border-t border-gray-700 p-4">
          <div class="flex-shrink-0 w-full group block">
            <div class="flex items-center">
              <div>
                <svg class="inline-block h-9 w-9 rounded-full text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-white">{{ user.name }}</p>
                <button @click="logout" class="text-xs font-medium text-gray-300 hover:text-white">Logout</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Main content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Navigation -->
      <header class="bg-white shadow-sm">
        <div class="flex justify-between items-center px-4 py-4 sm:px-6 lg:px-8">
          <div class="flex items-center">
            <button @click="toggleSidebar" class="md:hidden mr-4 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <h1 class="text-2xl font-semibold text-gray-900">{{ pageTitle }}</h1>
          </div>
          <div class="flex items-center space-x-4">
            <button class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
              <span class="sr-only">View notifications</span>
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span class="bg-red-500 absolute top-0 right-0 block h-2 w-2 rounded-full"></span>
            </button>
            
            <div class="ml-3 relative">
              <div>
                <button @click="toggleProfileMenu" class="flex items-center max-w-xs text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="user-menu" aria-expanded="false" aria-haspopup="true">
                  <span class="sr-only">Open user menu</span>
                  <span class="inline-block h-8 w-8 rounded-full overflow-hidden bg-gray-800">
                    <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                  </span>
                </button>
              </div>
              
              <div v-if="showProfileMenu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Settings</a>
                <a @click="logout" href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
              </div>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Mobile sidebar -->
      <div v-if="showMobileSidebar" class="md:hidden fixed inset-0 z-40 flex">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75" aria-hidden="true" @click="toggleSidebar"></div>
        
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-800">
          <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button @click="toggleSidebar" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
              <span class="sr-only">Close sidebar</span>
              <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <div class="flex-shrink-0 flex items-center px-4">
              <span class="text-white font-bold text-lg">Payroll System</span>
            </div>
            <nav class="mt-5 px-2 space-y-1">
              <router-link to="/dashboard" class="group flex items-center px-2 py-2 text-base font-medium rounded-md" :class="[$route.path === '/dashboard' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
                <svg class="mr-4 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
              </router-link>
              
              <router-link to="/users" class="group flex items-center px-2 py-2 text-base font-medium rounded-md" :class="[$route.path === '/users' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
                <svg class="mr-4 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Users
              </router-link>
              
              <router-link to="/employees" class="group flex items-center px-2 py-2 text-base font-medium rounded-md" :class="[$route.path === '/employees' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
                <svg class="mr-4 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Employees
              </router-link>
              
              <router-link to="/accounts" class="group flex items-center px-2 py-2 text-base font-medium rounded-md" :class="[$route.path === '/accounts' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
                <svg class="mr-4 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Accounts
              </router-link>
              
              <router-link to="/payslips" class="group flex items-center px-2 py-2 text-base font-medium rounded-md" :class="[$route.path === '/payslips' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
                <svg class="mr-4 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Payslips
              </router-link>
              
              <router-link to="/reports" class="group flex items-center px-2 py-2 text-base font-medium rounded-md" :class="[$route.path === '/reports' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white']">
                <svg class="mr-4 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Reports
              </router-link>
            </nav>
          </div>
          <div class="flex-shrink-0 flex border-t border-gray-700 p-4">
            <div class="flex-shrink-0 group block">
              <div class="flex items-center">
                <div>
                  <svg class="inline-block h-9 w-9 rounded-full text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-white">{{ user.name }}</p>
                  <button @click="logout" class="text-xs font-medium text-gray-300 hover:text-white">Logout</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="flex-shrink-0 w-14" aria-hidden="true">
          <!-- Force sidebar to shrink to fit close icon -->
        </div>
      </div>
      
      <!-- Page content -->
      <main class="flex-1 relative overflow-y-auto focus:outline-none">
        <div class="py-6">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <slot></slot>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

export default {
  name: 'DashboardLayout',
  props: {
    pageTitle: {
      type: String,
      default: 'Dashboard'
    }
  },
  setup() {
    const router = useRouter()
    const route = useRoute()
    
    const showMobileSidebar = ref(false)
    const showProfileMenu = ref(false)
    
    // In a real application, this would come from your auth store
    const user = ref({
      name: 'Admin User',
      email: 'admin@example.com'
    })
    
    const toggleSidebar = () => {
      showMobileSidebar.value = !showMobileSidebar.value
    }
    
    const toggleProfileMenu = () => {
      showProfileMenu.value = !showProfileMenu.value
    }
    
    const logout = () => {
      // Call your auth service logout method
      localStorage.removeItem('token')
      router.push('/login')
    }
    
    return {
      showMobileSidebar,
      showProfileMenu,
      toggleSidebar,
      toggleProfileMenu,
      logout,
      user,
      route
    }
  }
}
</script>