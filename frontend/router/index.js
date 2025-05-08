import { createRouter, createWebHistory } from 'vue-router'
import AuthService from '../services/auth'

// Lazy load components
const Login = () => import('../pages/Login.vue')
const Dashboard = () => import('../pages/Dashboard.vue')
const Users = () => import('../pages/Users.vue')
const Employees = () => import('../pages/Employees.vue')
const Accounts = () => import('../pages/Accounts.vue')
const Payslips = () => import('../pages/Payslips.vue')
const Reports = () => import('../pages/Reports.vue')

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresAuth: false }
  },
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/users',
    name: 'Users',
    component: Users,
    meta: { requiresAuth: true }
  },
  {
    path: '/employees',
    name: 'Employees',
    component: Employees,
    meta: { requiresAuth: true }
  },
  {
    path: '/accounts',
    name: 'Accounts',
    component: Accounts,
    meta: { requiresAuth: true }
  },
  {
    path: '/payslips',
    name: 'Payslips',
    component: Payslips,
    meta: { requiresAuth: true }
  },
  {
    path: '/reports',
    name: 'Reports',
    component: Reports,
    meta: { requiresAuth: true }
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/dashboard'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard for auth
router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const isLoggedIn = AuthService.isLoggedIn()
  
  if (requiresAuth && !isLoggedIn) {
    next('/login')
  } else if (to.path === '/login' && isLoggedIn) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router