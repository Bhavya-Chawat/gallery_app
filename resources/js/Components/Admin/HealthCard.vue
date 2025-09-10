<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <div :class="[
          'rounded-full p-3',
          status === 'healthy' ? 'bg-green-100' :
          status === 'warning' ? 'bg-yellow-100' : 'bg-red-100'
        ]">
          <CheckCircleIcon 
            v-if="status === 'healthy'"
            class="h-6 w-6 text-green-600"
          />
          <ExclamationTriangleIcon 
            v-else-if="status === 'warning'"
            class="h-6 w-6 text-yellow-600"
          />
          <XCircleIcon 
            v-else
            class="h-6 w-6 text-red-600"
          />
        </div>
      </div>
      <div class="ml-4">
        <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
        <p :class="[
          'text-sm',
          status === 'healthy' ? 'text-green-600' :
          status === 'warning' ? 'text-yellow-600' : 'text-red-600'
        ]">
          {{ message }}
        </p>
      </div>
    </div>
    
    <div v-if="metrics && metrics.length" class="mt-4 grid grid-cols-2 gap-4">
      <div 
        v-for="metric in metrics"
        :key="metric.key"
        class="text-center"
      >
        <div class="text-2xl font-semibold text-gray-900">
          {{ metric.value }}
        </div>
        <div class="text-sm text-gray-500">
          {{ metric.label }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  CheckCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  title: {
    type: String,
    default: 'System Health'
  },
  status: {
    type: String,
    default: 'healthy', // 'healthy', 'warning', ```ror'
    validator: (value) => ['healthy', 'warning', 'error'].includes(value)
  },
  message: {
    type: String,
    default: 'All systems operational'
  },
  metrics: {
    type: Array,
    default: () => []
  }
})
</script>
