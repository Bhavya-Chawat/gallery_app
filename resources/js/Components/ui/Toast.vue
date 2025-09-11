<template>
  <teleport to="body">
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <transition-group
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
        tag="div"
      >
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="max-w-sm w-full bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl shadow-lg rounded-xl pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
        >
          <div class="p-4">
            <div class="flex items-start">
              <!-- Icon -->
              <div class="flex-shrink-0">
                <CheckCircleIcon
                  v-if="toast.type === 'success'"
                  class="h-6 w-6 text-emerald-500"
                />
                <XCircleIcon
                  v-else-if="toast.type === 'error'"
                  class="h-6 w-6 text-red-500"
                />
                <ExclamationTriangleIcon
                  v-else-if="toast.type === 'warning'"
                  class="h-6 w-6 text-amber-500"
                />
                <InformationCircleIcon
                  v-else
                  class="h-6 w-6 text-blue-500"
                />
              </div>
              
              <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ toast.title }}
                </p>
                <p v-if="toast.message" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  {{ toast.message }}
                </p>
              </div>
              
              <div class="ml-4 flex-shrink-0 flex">
                <button
                  @click="removeToast(toast.id)"
                  class="bg-white/50 dark:bg-slate-700/50 rounded-md inline-flex text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                  <span class="sr-only">Close</span>
                  <XMarkIcon class="h-5 w-5" />
                </button>
              </div>
            </div>
            
            <!-- Action Button -->
            <div v-if="toast.action" class="mt-3">
              <button
                @click="handleAction(toast)"
                class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 transition-colors duration-200"
              >
                {{ toast.action.label }}
              </button>
            </div>
          </div>
          
          <!-- Progress Bar -->
          <div v-if="toast.autoRemove && toast.duration" class="h-1 bg-gray-200 dark:bg-slate-600">
            <div 
              class="h-full transition-all duration-100 ease-linear"
              :class="{
                'bg-emerald-500': toast.type === 'success',
                'bg-red-500': toast.type === 'error',
                'bg-amber-500': toast.type === 'warning',
                'bg-blue-500': toast.type === 'info'
              }"
              :style="{ width: `${toast.progress || 0}%` }"
            />
          </div>
        </div>
      </transition-group>
    </div>
  </teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const toasts = ref([])
let toastId = 0

const addToast = (toast) => {
  const id = ++toastId
  const newToast = {
    id,
    type: toast.type || 'info',
    title: toast.title,
    message: toast.message,
    action: toast.action,
    duration: toast.duration || 5000,
    autoRemove: toast.autoRemove !== false,
    progress: 100
  }

  toasts.value.push(newToast)

  if (newToast.autoRemove) {
    startProgress(newToast)
  }

  return id
}

const removeToast = (id) => {
  const index = toasts.value.findIndex(toast => toast.id === id)
  if (index > -1) {
    toasts.value.splice(index, 1)
  }
}

const startProgress = (toast) => {
  const interval = 50 // Update every 50ms
  const decrement = (100 * interval) / toast.duration
  
  const timer = setInterval(() => {
    toast.progress -= decrement
    
    if (toast.progress <= 0) {
      clearInterval(timer)
      removeToast(toast.id)
    }
  }, interval)
}

const handleAction = (toast) => {
  if (toast.action && toast.action.handler) {
    toast.action.handler()
  }
  removeToast(toast.id)
}

// Global toast methods
window.toast = {
  success: (title, message, options = {}) => addToast({ type: 'success', title, message, ...options }),
  error: (title, message, options = {}) => addToast({ type: 'error', title, message, ...options }),
  warning: (title, message, options = {}) => addToast({ type: 'warning', title, message, ...options }),
  info: (title, message, options = {}) => addToast({ type: 'info', title, message, ...options })
}

// Listen for custom toast events
const handleToastEvent = (event) => {
  addToast(event.detail)
}

onMounted(() => {
  document.addEventListener('show-toast', handleToastEvent)
})

onUnmounted(() => {
  document.removeEventListener('show-toast', handleToastEvent)
})
</script>