<template>
  <div class="bg-white rounded-lg p-4">
    <div class="flex items-center justify-between mb-2">
      <h4 class="text-sm font-medium text-gray-900">Storage Usage</h4>
      <span class="text-sm text-gray-500">{{ percentage.toFixed(1) }}%</span>
    </div>
    
    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
      <div 
        class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
        :style="{ width: Math.min(percentage, 100) + '%' }"
      ></div>
    </div>
    
    <div class="flex justify-between text-xs text-gray-500">
      <span>{{ formatBytes(used) }} used</span>
      <span>{{ formatBytes(quota) }} total</span>
    </div>
    
    <div v-if="showDetails" class="mt-2 text-xs text-gray-500">
      Remaining: {{ formatBytes(quota - used) }}
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  used: { type: Number, default: 0 },
  quota: { type: Number, default: 1073741824 },
  percentage: { type: Number, default: 0 },
  showDetails: { type: Boolean, default: false },
})

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>
