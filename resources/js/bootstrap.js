import axios from 'axios'
import { router } from '@inertiajs/vue3'

// Configure axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true

// CSRF token handling
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
    console.error('CSRF token not found: check that <meta name="csrf-token"> exists in your blade template.')
}

// Base API URL
axios.defaults.baseURL = window.location.origin

// Response interceptor for handling auth errors
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      router.visit('/login')
    }
    
    if (error.response?.status === 403) {
      // Handle forbidden - maybe show toast
      console.warn('Access forbidden:', error.response.data?.message)
    }
    
    return Promise.reject(error)
  }
)

// Request interceptor for progress indication
axios.interceptors.request.use((config) => {
  // Could add loading state here
  return config
})

window.axios = axios