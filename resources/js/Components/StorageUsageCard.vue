<template>
  <div class="bg-white shadow rounded-lg p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-medium text-gray-900">Storage Usage</h3>
      <div class="flex items-center text-sm text-gray-500">
        <ServerStackIcon class="h-5 w-5 mr-1" />
        {{ formatBytes(used) }} / {{ formatBytes(quota) }}
      </div>
    </div>

    <!-- Progress Bar -->
    <div class="mb-4">
      <div class="flex justify-between text-sm text-gray-600 mb-2">
        <span>{{ percentage.toFixed(1) }}% used</span>
        <span>{{ formatBytes(quota - used) }} remaining</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-3">
        <div 
          :class="[
            'h-3 rounded-full transition-all duration-500',
            percentage < 70 ? 'bg-green-500' :
            percentage < 85 ? 'bg-yellow-500' : 'bg-red-500'
          ]"
          :style="{ width: `${Math.min(percentage, 100)}%` }"
        ></div>
      </div>
    </div>

    <!-- Warning Messages -->
    <div v-if="percentage >= 90" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
      <div class="flex">
        <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Storage Almost Full</h3>
          <div class="mt-1 text-sm text-red-700">
            You're running low on storage space.
          </div>
        </div>
      </div>
    </div>
    
    <div v-else-if="percentage >= 75" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
      <div class="flex">
        <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400" />
        <div class="ml-3">
          <h3 class="text-sm font-medium text-yellow-800">Storage Warning</h3>
          <div class="mt-1 text-sm text-yellow-700">
            You're using {{ percentage.toFixed(1) }}% of your storage quota.
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div v-if="showActions" class="mt-4 flex space-x-3">
      <button
        @click="$emit('cleanup')"
        class="flex-1 bg-blue-600 text-white text-sm py-2 px-4 rounded-md hover:bg-blue-700 transition-colors"
      >
        Clean Up Files
      </button>
      <button
        @click="$emit('upgrade')"
        class="flex-1 bg-gray-600 text-white text-sm py-2 px-4 rounded-md hover:bg-gray-700 transition-colors"
      >
        Upgrade Storage
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  ServerStackIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  used: {
    type: Number,
    required: true,
    default: 0
  },
  quota: {
    type: Number,
    required: true,
    default: 1073741824
  },
  percentage: {
    type: Number,
    default: null
  },
  showActions: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['cleanup', 'upgrade'])

const percentage = computed(() => {
  if (props.percentage !== null) {
    return props.percentage
  }
  return (props.used / props.quota) * 100
})

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>
