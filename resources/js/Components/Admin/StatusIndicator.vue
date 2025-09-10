<template>
  <div class="flex items-center justify-between">
    <span class="text-xs text-gray-300">{{ label }}</span>
    <div class="flex items-center space-x-2">
      <div
        class="w-2 h-2 rounded-full"
        :class="statusClasses"
        :title="message"
      ></div>
      <span
        class="text-xs capitalize"
        :class="statusTextClasses"
      >
        {{ status }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: {
    type: String,
    required: true,
  },
  status: {
    type: String,
    required: true,
    validator: (value) => ['healthy', 'warning', 'error', 'unknown'].includes(value),
  },
  message: {
    type: String,
    default: '',
  },
})

const statusClasses = computed(() => {
  switch (props.status) {
    case 'healthy':
      return 'bg-green-400'
    case 'warning':
      return 'bg-yellow-400'
    case 'error':
      return 'bg-red-400'
    default:
      return 'bg-gray-400'
  }
})

const statusTextClasses = computed(() => {
  switch (props.status) {
    case 'healthy':
      return 'text-green-400'
    case 'warning':
      return 'text-yellow-400'
    case 'error':
      return 'text-red-400'
    default:
      return 'text-gray-400'
  }
})
</script>
