<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
      <CogIcon class="h-5 w-5 text-gray-400" />
    </div>
    
    <div class="space-y-3">
      <button
        v-for="action in actions"
        :key="action.id"
        @click="handleAction(action)"
        :disabled="action.disabled || loading[action.id]"
        :class="[
          'w-full flex items-center justify-between px-4 py-2 text-sm rounded-md transition-colors',
          action.variant === 'danger' 
            ? 'bg-red-50 text-red-700 hover:bg-red-100 disabled:opacity-50'
            : 'bg-gray-50 text-gray-700 hover:bg-gray-100 disabled:opacity-50'
        ]"
      >
        <span class="flex items-center">
          <component :is="action.icon" class="h-4 w-4 mr-2" />
          {{ action.label }}
        </span>
        <div v-if="loading[action.id]" class="animate-spin h-4 w-4 border-2 border-gray-300 border-t-gray-600 rounded-full"></div>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import {
  CogIcon,
  TrashIcon,
  ArrowPathIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  title: {
    type: String,
    default: 'Quick Actions'
  },
  actions: {
    type: Array,
    default: () => [
      {
        id: 'cache_clear',
        label: 'Clear Cache',
        icon: ArrowPathIcon,
        action: 'clearCache',
        variant: 'default'
      },
      {
        id: 'maintenance',
        label: 'Maintenance Mode',
        icon: ExclamationTriangleIcon,
        action: 'toggleMaintenance',
        variant: 'warning'
      }
    ]
  }
})

const loading = ref({})

const handleAction = async (action) => {
  loading.value[action.id] = true
  
  try {
    if (action.href) {
      router.visit(action.href)
    } else {
      console.log('Action:', action.action)
    }
  } catch (error) {
    console.error('Action failed:', error)
  } finally {
    loading.value[action.id] = false
  }
}
</script>
