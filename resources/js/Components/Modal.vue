<template>
  <teleport to="body">
    <transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-show="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" scroll-region>
        <!-- Background Overlay -->
        <transition
          enter-active-class="ease-out duration-300"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="ease-in duration-200"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div
            v-show="show"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm"
            @click="close"
          />
        </transition>

        <!-- Modal Content -->
        <transition
          enter-active-class="ease-out duration-300"
          enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to-class="opacity-100 translate-y-0 sm:scale-100"
          leave-active-class="ease-in duration-200"
          leave-from-class="opacity-100 translate-y-0 sm:scale-100"
          leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <div
            v-show="show"
            class="mb-6 bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-xl shadow-2xl transform transition-all sm:w-full sm:mx-auto"
            :class="maxWidthClass"
          >
            <!-- Header -->
            <div v-if="$slots.header || closeable" class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-slate-700">
              <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                <slot name="header" />
              </div>
              
              <button
                v-if="closeable"
                @click="close"
                class="rounded-lg p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors duration-200"
              >
                <span class="sr-only">Close</span>
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
              <slot />
            </div>

            <!-- Footer -->
            <div v-if="$slots.footer" class="flex items-center justify-end px-6 py-4 bg-gray-50/50 dark:bg-slate-700/50 rounded-b-xl space-x-3">
              <slot name="footer" />
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  maxWidth: {
    type: String,
    default: '2xl',
    validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'].includes(value)
  },
  closeable: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close'])

const maxWidthClass = computed(() => {
  const maxWidthClasses = {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
    '3xl': 'sm:max-w-3xl',
    '4xl': 'sm:max-w-4xl',
    '5xl': 'sm:max-w-5xl',
    '6xl': 'sm:max-w-6xl',
    '7xl': 'sm:max-w-7xl'
  }
  
  return maxWidthClasses[props.maxWidth] || 'sm:max-w-2xl'
})

const close = () => {
  if (props.closeable) {
    emit('close')
  }
}

const closeOnEscape = (e) => {
  if (e.key === 'Escape' && props.show) {
    close()
  }
}

// Focus trap
const focusableElementsSelector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
let focusableElements = []
let firstFocusableElement = null
let lastFocusableElement = null

const trapFocus = (e) => {
  if (e.key !== 'Tab') return

  if (e.shiftKey) {
    if (document.activeElement === firstFocusableElement) {
      lastFocusableElement.focus()
      e.preventDefault()
    }
  } else {
    if (document.activeElement === lastFocusableElement) {
      firstFocusableElement.focus()
      e.preventDefault()
    }
  }
}

watch(() => props.show, (show) => {
  if (show) {
    document.body.style.overflow = 'hidden'
    
    // Set up focus trap
    setTimeout(() => {
      const modal = document.querySelector('.fixed.inset-0.overflow-y-auto')
      if (modal) {
        focusableElements = modal.querySelectorAll(focusableElementsSelector)
        firstFocusableElement = focusableElements[0]
        lastFocusableElement = focusableElements[focusableElements.length - 1]
        
        if (firstFocusableElement) {
          firstFocusableElement.focus()
        }
      }
    }, 100)
  } else {
    document.body.style.overflow = 'auto'
  }
})

onMounted(() => {
  document.addEventListener('keydown', closeOnEscape)
  document.addEventListener('keydown', trapFocus)
})

onUnmounted(() => {
  document.removeEventListener('keydown', closeOnEscape)
  document.removeEventListener('keydown', trapFocus)
  document.body.style.overflow = 'auto'
})
</script>