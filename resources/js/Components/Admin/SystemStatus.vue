<template>
  <div class="space-y-3">
    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
      System Status
    </h4>
    
    <div class="space-y-2">
      <StatusIndicator
        label="Database"
        :status="systemHealth.database?.status || 'unknown'"
        :message="systemHealth.database?.message"
      />
      <StatusIndicator
        label="Storage"
        :status="systemHealth.storage?.status || 'unknown'"
        :message="systemHealth.storage?.message"
      />
      <StatusIndicator
        label="Queue"
        :status="systemHealth.queue?.status || 'unknown'"
        :message="systemHealth.queue?.message"
      />
      <StatusIndicator
        label="Cache"
        :status="systemHealth.cache?.status || 'unknown'"
        :message="systemHealth.cache?.message"
      />
    </div>

    <!-- Quick Stats -->
    <div class="pt-3 border-t border-gray-700">
      <div class="space-y-2 text-xs text-gray-400">
        <div class="flex justify-between">
          <span>Active Users</span>
          <span>{{ stats.activeUsers || 0 }}</span>
        </div>
        <div class="flex justify-between">
          <span>Total Images</span>
          <span>{{ stats.totalImages || 0 }}</span>
        </div>
        <div class="flex justify-between">
          <span>Storage Used</span>
          <span>{{ formatBytes(stats.storageUsed || 0) }}</span>
        </div>
      </div>
    </div>

    <!-- Last Updated -->
    <div class="text-xs text-gray-500">
      Updated {{ formatTimeAgo(lastUpdate) }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

import StatusIndicator from './StatusIndicator.vue'

const page = usePage()

const systemHealth = computed(() => page.props.systemHealth || {})
const stats = computed(() => page.props.systemStats || {})

const lastUpdate = ref(new Date())
let updateInterval = null

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatTimeAgo = (date) => {
  const seconds = Math.floor((new Date() - date) / 1000)
  
  if (seconds < 60) return `${seconds}s ago`
  if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`
  if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`
  return `${Math.floor(seconds / 86400)}d ago`
}

onMounted(() => {
  // Update the timestamp every 30 seconds
  updateInterval = setInterval(() => {
    lastUpdate.value = new Date()
  }, 30000)
})

onUnmounted(() => {
  if (updateInterval) {
    clearInterval(updateInterval)
  }
})
</script>
