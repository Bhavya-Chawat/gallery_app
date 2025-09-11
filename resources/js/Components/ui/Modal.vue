<template>
  <Teleport to="body">
    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div
              v-if="show"
              ref="modalRef"
              class="relative w-full max-w-md transform overflow-hidden rounded-xl bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl transition-all"
              :class="sizeClasses"
              @click.stop
            >
              <!-- Header -->
              <div v-if="$slots.header || title" class="px-6 py-4 border-b border-gray-200/50 dark:border-slate-700/50">
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <slot name="header">
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ title }}
                      </h3>
                      <p v-if="description" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ description }}
                      </p>
                    </slot>
                  </div>
                  
                  <!-- Close Button -->
                  <button
                    v-if="closable"
                    @click="handleClose"
                    class="ml-4 p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Content -->
              <div class="px-6 py-4">
                <slot />
              </div>

              <!-- Footer -->
              <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-200/50 dark:border-slate-700/50 bg-gray-50/50 dark:bg-slate-700/50">
                <slot name="footer" />
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, watch, onMounted, onUnmounted, nextTick, ref } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: String,
  description: String,
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', 'full'].includes(value)
  },
  closable: {
    type: Boolean,
    default: true
  },
  closeOnEscape: {
    type: Boolean,
    default: true
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  },
  persistent: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'opened', 'closed'])

const modalRef = ref(null)

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'max-w-xs',
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    full: 'max-w-full mx-4'
  }
  return sizes[props.size]
})

const handleClose = () => {
  if (!props.persistent) {
    emit('close')
  }
}

const handleBackdropClick = () => {
  if (props.closeOnBackdrop && !props.persistent) {
    emit('close')
  }
}

const handleEscape = (event) => {
  if (event.key === 'Escape' && props.show && props.closeOnEscape && !props.persistent) {
    handleClose()
  }
}

// Focus management
const trapFocus = (event) => {
  if (!modalRef.value || !props.show) return

  const focusableElements = modalRef.value.querySelectorAll(
    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
  )
  
  const firstElement = focusableElements[0]
  const lastElement = focusableElements[focusableElements.length - 1]

  if (event.key === 'Tab') {
    if (event.shiftKey) {
      if (document.activeElement === firstElement) {
        event.preventDefault()
        lastElement.focus()
      }
    } else {
      if (document.activeElement === lastElement) {
        event.preventDefault()
        firstElement.focus()
      }
    }
  }
}

// Watch for show changes
watch(() => props.show, async (newValue) => {
  if (newValue) {
    document.body.style.overflow = 'hidden'
    await nextTick()
    
    // Focus first focusable element
    const firstFocusable = modalRef.value?.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')
    if (firstFocusable) {
      firstFocusable.focus()
    }
    
    emit('opened')
  } else {
    document.body.style.overflow = ''
    emit('closed')
  }
})

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
  document.addEventListener('keydown', trapFocus)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
  document.removeEventListener('keydown', trapFocus)
  document.body.style.overflow = ''
})
</script>