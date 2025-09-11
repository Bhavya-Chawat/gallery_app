<template>
  <component
    :is="tag"
    :href="href"
    :to="to"
    :type="type"
    :disabled="disabled || loading"
    :class="buttonClasses"
    @click="handleClick"
  >
    <div v-if="loading" class="absolute inset-0 flex items-center justify-center">
      <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
    </div>
    
    <div :class="{ 'opacity-0': loading }" class="flex items-center justify-center space-x-2">
      <slot name="icon-left" />
      <span v-if="$slots.default"><slot /></span>
      <slot name="icon-right" />
    </div>
  </component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'outline', 'ghost', 'danger', 'success'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  fullWidth: {
    type: Boolean,
    default: false
  },
  rounded: {
    type: String,
    default: 'md',
    validator: (value) => ['none', 'sm', 'md', 'lg', 'full'].includes(value)
  },
  // For different element types
  tag: {
    type: String,
    default: 'button'
  },
  href: String,
  to: String,
  type: {
    type: String,
    default: 'button'
  }
})

const emit = defineEmits(['click'])

const handleClick = (event) => {
  if (!props.disabled && !props.loading) {
    emit('click', event)
  }
}

const buttonClasses = computed(() => {
  const classes = ['relative inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-slate-900']
  
  // Size variants
  const sizeClasses = {
    xs: 'px-2.5 py-1.5 text-xs',
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-2 text-sm',
    lg: 'px-6 py-3 text-base',
    xl: 'px-8 py-4 text-lg'
  }
  
  // Rounded variants
  const roundedClasses = {
    none: 'rounded-none',
    sm: 'rounded-sm',
    md: 'rounded-lg',
    lg: 'rounded-xl',
    full: 'rounded-full'
  }
  
  // Variant styles
  const variantClasses = {
    primary: 'bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 text-white shadow-sm hover:shadow-lg hover:scale-105 hover:from-blue-700 hover:via-cyan-600 hover:to-blue-500',
    secondary: 'bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-sm hover:shadow-lg hover:scale-105 hover:from-indigo-600 hover:via-purple-600 hover:to-pink-600',
    outline: 'border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 hover:border-gray-400 dark:hover:border-gray-500',
    ghost: 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700',
    danger: 'bg-red-600 text-white shadow-sm hover:bg-red-700 hover:shadow-lg hover:scale-105',
    success: 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 hover:shadow-lg hover:scale-105'
  }
  
  // Disabled states
  if (props.disabled || props.loading) {
    classes.push('opacity-50 cursor-not-allowed')
  } else {
    classes.push('cursor-pointer')
  }
  
  // Full width
  if (props.fullWidth) {
    classes.push('w-full')
  }
  
  classes.push(sizeClasses[props.size])
  classes.push(roundedClasses[props.rounded])
  classes.push(variantClasses[props.variant])
  
  return classes.join(' ')
})
</script>