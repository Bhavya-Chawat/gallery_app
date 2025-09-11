<template>
  <Link
    :href="href"
    :class="classes"
    v-bind="$attrs"
  >
    <slot />
  </Link>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  href: {
    type: String,
    required: true
  },
  active: {
    type: Boolean,
    default: false
  }
})

const classes = computed(() => {
  const baseClasses = [
    'inline-flex items-center px-4 py-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none rounded-lg'
  ]

  if (props.active) {
    return [
      ...baseClasses,
      'bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 text-white shadow-md'
    ].join(' ')
  }

  return [
    ...baseClasses,
    'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 focus:text-gray-700 dark:focus:text-gray-200 focus:bg-gray-100 dark:focus:bg-slate-700/50'
  ].join(' ')
})
</script>
