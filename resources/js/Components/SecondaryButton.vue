<template>
  <component
    :is="component"
    :href="href"
    :type="type"
    :disabled="disabled"
    :class="classes"
    v-bind="$attrs"
  >
    <slot />
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value)
  },
  href: {
    type: String,
    default: null
  },
  disabled: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  variant: {
    type: String,
    default: 'outline',
    validator: (value) => ['outline', 'ghost', 'solid'].includes(value)
  }
})

const component = computed(() => {
  if (props.href) {
    return props.href.startsWith('http') ? 'a' : Link
  }
  return 'button'
})

const classes = computed(() => {
  const baseClasses = [
    'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed'
  ]

  // Size classes
  const sizeClasses = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-4 py-2 text-sm',
    lg: 'px-6 py-3 text-base',
    xl: 'px-8 py-4 text-lg'
  }

  // Variant classes
  const variantClasses = {
    outline: [
      'border-2 border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800',
      'hover:bg-gray-50 dark:hover:bg-slate-700 hover:border-gray-400 dark:hover:border-slate-500 hover:scale-105',
      'active:bg-gray-100 dark:active:bg-slate-600 active:scale-95',
      'disabled:hover:bg-white dark:disabled:hover:bg-slate-800 disabled:hover:scale-100'
    ],
    ghost: [
      'text-gray-700 dark:text-gray-300 bg-transparent',
      'hover:bg-gray-100 dark:hover:bg-slate-700 hover:scale-105',
      'active:bg-gray-200 dark:active:bg-slate-600 active:scale-95',
      'disabled:hover:bg-transparent disabled:hover:scale-100'
    ],
    solid: [
      'bg-gray-600 text-white shadow-md',
      'hover:bg-gray-700 hover:shadow-lg hover:scale-105',
      'active:bg-gray-800 active:scale-95',
      'disabled:hover:bg-gray-600 disabled:hover:shadow-md disabled:hover:scale-100'
    ]
  }

  return [
    ...baseClasses,
    sizeClasses[props.size],
    ...variantClasses[props.variant]
  ].join(' ')
})
</script>
