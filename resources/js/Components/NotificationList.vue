
<template>
  <div
    aria-live="assertive"
    class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start z-50"
  >
    <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
      <TransitionGroup
        enter-active-class="transform ease-out duration-500 transition"
        enter-from-class="translate-y-4 opacity-0 sm:translate-y-0 sm:translate-x-4 scale-95"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-300"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95 translate-x-2"
      >
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="group relative max-w-sm w-full bg-slate-900/95 backdrop-blur-xl border shadow-2xl rounded-xl pointer-events-auto overflow-hidden animate-notification-entrance"
          :class="[
            notification.type === 'success' ? 'border-emerald-500/30 shadow-emerald-500/20' :
            notification.type === 'warning' ? 'border-amber-500/30 shadow-amber-500/20' :
            notification.type === 'error' ? 'border-rose-500/30 shadow-rose-500/20' :
            'border-cyan-500/30 shadow-cyan-500/20'
          ]"
        >
          <!-- Animated mesh background -->
          <div class="absolute inset-0 opacity-20 overflow-hidden">
            <div :class="[
              'absolute inset-0 animate-gradient-shift',
              notification.type === 'success' ? 'bg-gradient-to-br from-emerald-400/10 via-green-400/10 to-emerald-400/10' :
              notification.type === 'warning' ? 'bg-gradient-to-br from-amber-400/10 via-yellow-400/10 to-amber-400/10' :
              notification.type === 'error' ? 'bg-gradient-to-br from-rose-400/10 via-red-400/10 to-rose-400/10' :
              'bg-gradient-to-br from-cyan-400/10 via-blue-400/10 to-cyan-400/10'
            ]"></div>
          </div>

          <!-- Floating particles -->
          <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div v-for="i in 8" :key="i" 
                 :class="[
                   'absolute rounded-full opacity-0 group-hover:opacity-60 transition-opacity duration-300 animate-float-notification',
                   notification.type === 'success' ? 'bg-emerald-400' :
                   notification.type === 'warning' ? 'bg-amber-400' :
                   notification.type === 'error' ? 'bg-rose-400' :
                   'bg-cyan-400'
                 ]"
                 :style="`
                   left: ${5 + (i * 12)}%; 
                   top: ${10 + (i * 10)}%; 
                   width: ${1 + (i % 2)}px; 
                   height: ${1 + (i % 2)}px;
                   animation-delay: ${i * 0.3}s;
                 `">
            </div>
          </div>

          <!-- Shimmer effect -->
          <div :class="[
            'absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-out',
            notification.type === 'success' ? 'via-emerald-300/20' :
            notification.type === 'warning' ? 'via-amber-300/20' :
            notification.type === 'error' ? 'via-rose-300/20' :
            'via-cyan-300/20'
          ]"></div>

          <!-- Side accent bar -->
          <div :class="[
            'absolute left-0 top-0 w-1 h-full animate-pulse-bar',
            notification.type === 'success' ? 'bg-gradient-to-b from-emerald-500 to-green-500' :
            notification.type === 'warning' ? 'bg-gradient-to-b from-amber-500 to-yellow-500' :
            notification.type === 'error' ? 'bg-gradient-to-b from-rose-500 to-red-500' :
            'bg-gradient-to-b from-cyan-500 to-blue-500'
          ]"></div>

          <div class="relative p-4 z-10">
            <div class="flex items-start">
              <div class="flex-shrink-0 relative">
                <!-- Icon glow background -->
                <div :class="[
                  'absolute inset-0 rounded-full blur-md opacity-50 animate-icon-glow',
                  notification.type === 'success' ? 'bg-emerald-400/40' :
                  notification.type === 'warning' ? 'bg-amber-400/40' :
                  notification.type === 'error' ? 'bg-rose-400/40' :
                  'bg-cyan-400/40'
                ]"></div>

                <!-- Enhanced icons with animations -->
                <CheckCircleIcon
                  v-if="notification.type === 'success'"
                  class="relative h-6 w-6 text-emerald-400 animate-success-bounce drop-shadow-sm"
                />
                <ExclamationTriangleIcon
                  v-else-if="notification.type === 'warning'"
                  class="relative h-6 w-6 text-amber-400 animate-warning-shake drop-shadow-sm"
                />
                <XCircleIcon
                  v-else-if="notification.type === 'error'"
                  class="relative h-6 w-6 text-rose-400 animate-error-pulse drop-shadow-sm"
                />
                <InformationCircleIcon
                  v-else
                  class="relative h-6 w-6 text-cyan-400 animate-info-spin drop-shadow-sm"
                />
              </div>
              
              <div class="ml-4 w-0 flex-1 pt-0.5">
                <p :class="[
                  'text-sm font-semibold leading-5 transition-all duration-300',
                  notification.type === 'success' ? 'text-emerald-300' :
                  notification.type === 'warning' ? 'text-amber-300' :
                  notification.type === 'error' ? 'text-rose-300' :
                  'text-cyan-300'
                ]">
                  {{ notification.title }}
                </p>
                <p v-if="notification.message" class="mt-1 text-sm text-slate-400 leading-5">
                  {{ notification.message }}
                </p>
              </div>
              
              <div class="ml-4 flex-shrink-0 flex">
                <button
                  @click="removeNotification(notification.id)"
                  class="group relative bg-slate-800/50 backdrop-blur-sm rounded-lg inline-flex p-1.5 text-slate-400 hover:text-slate-300 hover:bg-slate-700/50 focus:outline-none focus:ring-2 focus:ring-violet-500/50 transition-all duration-300 hover:scale-110"
                >
                  <!-- Button glow effect -->
                  <div class="absolute inset-0 bg-gradient-to-r from-violet-500/20 to-purple-500/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  
                  <XMarkIcon class="relative h-4 w-4 transform group-hover:rotate-90 transition-transform duration-300" />
                  
                  <!-- Ripple effect on click -->
                  <div class="absolute inset-0 rounded-lg bg-violet-400/20 scale-0 group-active:scale-100 group-active:opacity-50 opacity-0 transition-all duration-150"></div>
                </button>
              </div>
            </div>

            <!-- Progress bar for auto-dismiss -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-800/50 overflow-hidden rounded-b-xl">
              <div :class="[
                'h-full animate-progress-countdown',
                notification.type === 'success' ? 'bg-gradient-to-r from-emerald-500 to-green-500' :
                notification.type === 'warning' ? 'bg-gradient-to-r from-amber-500 to-yellow-500' :
                notification.type === 'error' ? 'bg-gradient-to-r from-rose-500 to-red-500' :
                'bg-gradient-to-r from-cyan-500 to-blue-500'
              ]"></div>
            </div>
          </div>

          <!-- Corner accent dots -->
          <div class="absolute top-2 right-12 flex space-x-1">
            <div :class="[
              'w-1 h-1 rounded-full animate-pulse',
              notification.type === 'success' ? 'bg-emerald-400' :
              notification.type === 'warning' ? 'bg-amber-400' :
              notification.type === 'error' ? 'bg-rose-400' :
              'bg-cyan-400'
            ]"></div>
            <div :class="[
              'w-1 h-1 rounded-full animate-pulse',
              notification.type === 'success' ? 'bg-green-400' :
              notification.type === 'warning' ? 'bg-yellow-400' :
              notification.type === 'error' ? 'bg-red-400' :
              'bg-blue-400'
            ]" style="animation-delay: 0.5s;"></div>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import {
  CheckCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  InformationCircleIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()
const notifications = ref([])
let nextId = 1

const addNotification = (type, title, message = null, duration = 5000) => {
  const notification = {
    id: nextId++,
    type,
    title,
    message,
  }
  
  notifications.value.push(notification)
  
  if (duration > 0) {
    setTimeout(() => {
      removeNotification(notification.id)
    }, duration)
  }
  
  return notification.id
}

const removeNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
}

// Listen for flash messages from Inertia
onMounted(() => {
  if (page.props.flash?.success) {
    addNotification('success', 'Success', page.props.flash.success)
  }
  if (page.props.flash?.error) {
    addNotification('error', 'Error', page.props.flash.error)
  }
  if (page.props.flash?.warning) {
    addNotification('warning', 'Warning', page.props.flash.warning)
  }
  if (page.props.flash?.info) {
    addNotification('info', 'Info', page.props.flash.info)
  }
})

// Provide methods globally
defineExpose({
  addNotification,
  removeNotification,
})
</script>

<style scoped>
/* Notification entrance animation */
@keyframes notification-entrance {
  0% { 
    transform: translateY(100%) scale(0.8) rotateX(45deg);
    opacity: 0;
    filter: blur(10px);
  }
  50% {
    transform: translateY(-10%) scale(1.05) rotateX(-5deg);
    opacity: 0.8;
    filter: blur(2px);
  }
  100% { 
    transform: translateY(0) scale(1) rotateX(0deg);
    opacity: 1;
    filter: blur(0px);
  }
}

.animate-notification-entrance {
  animation: notification-entrance 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

/* Gradient shift animation */
@keyframes gradient-shift {
  0%, 100% { opacity: 0.6; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.02); }
}

.animate-gradient-shift {
  animation: gradient-shift 3s ease-in-out infinite;
}

/* Floating notification particles */
@keyframes float-notification {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  50% { transform: translateY(-12px) translateX(6px) rotate(180deg); opacity: 0.7; }
}

.animate-float-notification {
  animation: float-notification 4s ease-in-out infinite;
}

/* Icon-specific animations */
@keyframes success-bounce {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.2); }
}

@keyframes warning-shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-2px) rotate(-2deg); }
  75% { transform: translateX(2px) rotate(2deg); }
}

@keyframes error-pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.1); opacity: 0.8; }
}

@keyframes info-spin {
  0% { transform: rotate(0deg) scale(1); }
  50% { transform: rotate(180deg) scale(1.1); }
  100% { transform: rotate(360deg) scale(1); }
}

.animate-success-bounce { animation: success-bounce 2s ease-in-out infinite; }
.animate-warning-shake { animation: warning-shake 0.5s ease-in-out infinite; }
.animate-error-pulse { animation: error-pulse 1.5s ease-in-out infinite; }
.animate-info-spin { animation: info-spin 3s ease-in-out infinite; }

/* Icon glow animation */
@keyframes icon-glow {
  0%, 100% { opacity: 0.3; transform: scale(0.8); }
  50% { opacity: 0.7; transform: scale(1.2); }
}

.animate-icon-glow {
  animation: icon-glow 2s ease-in-out infinite;
}

/* Pulse bar animation */
@keyframes pulse-bar {
  0%, 100% { opacity: 0.6; transform: scaleY(1); }
  50% { opacity: 1; transform: scaleY(1.1); }
}

.animate-pulse-bar {
  animation: pulse-bar 2s ease-in-out infinite;
}

/* Progress countdown animation */
@keyframes progress-countdown {
  0% { width: 100%; }
  100% { width: 0%; }
}

.animate-progress-countdown {
  animation: progress-countdown 5s linear forwards;
}

/* Enhanced hover effects */
.group:hover .animate-float-notification {
  animation-duration: 2.5s;
}

.group:hover .animate-gradient-shift {
  animation-duration: 2s;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .max-w-sm {
    max-width: calc(100vw - 2rem);
  }
  
  .animate-notification-entrance {
    animation-duration: 0.4s;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .animate-notification-entrance,
  .animate-gradient-shift,
  .animate-float-notification,
  .animate-success-bounce,
  .animate-warning-shake,
  .animate-error-pulse,
  .animate-info-spin,
  .animate-icon-glow,
  .animate-pulse-bar,
  .animate-progress-countdown {
    animation: none !important;
    transition-duration: 0.01ms !important;
  }
}

/* High contrast mode */
@media (prefers-contrast: high) {
  .bg-slate-900\/95 {
    background-color: rgb(0 0 0 / 0.98) !important;
  }
  
  .text-slate-400 {
    color: rgb(203 213 225) !important;
  }
  
  .border-emerald-500\/30,
  .border-amber-500\/30,
  .border-rose-500\/30,
  .border-cyan-500\/30 {
    border-width: 2px !important;
  }
}

/* Focus accessibility */
button:focus-visible {
  outline: 2px solid rgba(139, 92, 246, 0.5);
  outline-offset: 2px;
}
</style>