<template>
  <component
    :is="component"
    :href="href"
    :method="method"
    :as="as"
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
  href: {
    type: String,
    default: null
  },
  method: {
    type: String,
    default: 'get'
  },
  as: {
    type: String,
    default: null
  },
  active: {
    type: Boolean,
    default: false
  }
})

const component = computed(() => {
  if (props.as) {
    return props.as
  }
  
  if (props.href) {
    return Link
  }
  
  return 'button'
})

const classes = computed(() => {
  const baseClasses = [
    'block w-full pl-3 pr-4 py-2 text-left text-base font-medium transition duration-150 ease-in-out focus:outline-none rounded-lg mx-3 my-1'
  ]

  if (props.active) {
    return [
      ...baseClasses,
      'bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 text-white shadow-md'
    ].join(' ')
  }

  return [
    ...baseClasses,
    'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-100 dark:focus:bg-slate-700/50'
  ].join(' ')
})
</script>