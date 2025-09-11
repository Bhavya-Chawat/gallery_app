<template>
  <label :for="for" :class="classes">
    <slot />
    <span v-if="required" class="text-red-500 ml-1">*</span>
    <span v-if="optional" class="text-gray-500 dark:text-gray-400 text-sm font-normal ml-2">(Optional)</span>
  </label>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  for: {
    type: String,
    default: null
  },
  required: {
    type: Boolean,
    default: false
  },
  optional: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  }
})

const classes = computed(() => {
  const baseClasses = [
    'block font-medium text-gray-700 dark:text-gray-300 mb-1'
  ]

  const sizeClasses = {
    sm: 'text-sm',
    md: 'text-sm',
    lg: 'text-base'
  }

  return [
    ...baseClasses,
    sizeClasses[props.size]
  ].join(' ')
})
</script>