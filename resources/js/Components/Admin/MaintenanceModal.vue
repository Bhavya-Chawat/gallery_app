<template>
  <Modal v-model="localOpen" title="System Maintenance" size="lg">
    <div class="space-y-6">
      <p class="text-sm text-gray-600">
        Select maintenance tasks to run. These operations may take some time to complete.
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <MaintenanceCard
          title="Clear Caches"
          description="Clear application, route, config, and view caches"
          icon="TrashIcon"
          :loading="loadingStates.clearCache"
          @execute="executeTask('clear-cache', 'all')"
        />

        <MaintenanceCard
          title="Cleanup Temp Files"
          description="Remove temporary files older than 24 hours"
          icon="DocumentIcon"
          :loading="loadingStates.cleanupTemp"
          @execute="executeTask('maintenance', 'cleanup_temp')"
        />

        <MaintenanceCard
          title="Recalculate Storage"
          description="Recalculate storage usage for all users"
          icon="CircleStackIcon"
          :loading="loadingStates.recalculateStorage"
          @execute="executeTask('maintenance', 'recalculate_storage')"
        />

        <MaintenanceCard
          title="Optimize Database"
          description="Run database optimization and cleanup"
          icon="ServerIcon"
          :loading="loadingStates.optimizeDatabase"
          @execute="executeTask('maintenance', 'optimize_database')"
        />

        <MaintenanceCard
          title="Cleanup Old Logs"
          description="Remove audit logs and view counts older than 6 months"
          icon="ArchiveBoxIcon"
          :loading="loadingStates.cleanupLogs"
          @execute="executeTask('maintenance', 'cleanup_logs')"
        />

        <MaintenanceCard
          title="System Health Check"
          description="Run comprehensive system health diagnostics"
          icon="HeartIcon"
          :loading="loadingStates.healthCheck"
          @execute="runHealthCheck"
        />
      </div>

      <!-- Results -->
      <div v-if="results.length > 0" class="border-t pt-4">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Results</h4>
        <div class="space-y-2">
          <div
            v-for="result in results"
            :key="result.id"
            class="flex items-center p-3 rounded-lg"
            :class="result.success ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'"
          >
            <CheckCircleIcon v-if="result.success" class="h-5 w-5 mr-2" />
            <XCircleIcon v-else class="h-5 w-5 mr-2" />
            <span class="text-sm">{{ result.message }}</span>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end space-x-3">
        <button
          @click="localOpen = false"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
        >
          Close
        </button>
        <button
          @click="clearResults"
          v-if="results.length > 0"
          class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 border border-blue-300 rounded-md hover:bg-blue-200"
        >
          Clear Results
        </button>
      </div>
    </template>
  </Modal>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import  route  from 'ziggy-js'
import {
  CheckCircleIcon,
  XCircleIcon,
} from '@heroicons/vue/24/outline'

import Modal from '@/Components/Modal.vue'
import MaintenanceCard from './MaintenanceCard.vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue'])

const localOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const loadingStates = reactive({
  clearCache: false,
  cleanupTemp: false,
  recalculateStorage: false,
  optimizeDatabase: false,
  cleanupLogs: false,
  healthCheck: false,
})

const results = ref([])
let resultId = 1

const executeTask = async (endpoint, task) => {
  const taskKey = task.replace('_', '')
  loadingStates[taskKey] = true

  try {
    const response = await router.post(
      route(`admin.system.${endpoint}`),
      endpoint === 'maintenance' ? { task } : { type: task },
      {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
          results.value.push({
            id: resultId++,
            success: true,
            message: page.props.flash?.success || `${task} completed successfully`,
          })
        },
        onError: (errors) => {
          results.value.push({
            id: resultId++,
            success: false,
            message: errors.message || `${task} failed`,
          })
        },
      }
    )
  } catch (error) {
    results.value.push({
      id: resultId++,
      success: false,
      message: error.message || `${task} failed`,
    })
  } finally {
    loadingStates[taskKey] = false
  }
}

const runHealthCheck = async () => {
  loadingStates.healthCheck = true

  try {
    const response = await fetch('/api/health')
    const data = await response.json()
    
    results.value.push({
      id: resultId++,
      success: data.status === 'healthy',
      message: data.status === 'healthy' 
        ? 'System health check passed' 
        : `Health check failed: ${data.error}`,
    })
  } catch (error) {
    results.value.push({
      id: resultId++,
      success: false,
      message: `Health check failed: ${error.message}`,
    })
  } finally {
    loadingStates.healthCheck = false
  }
}

const clearResults = () => {
  results.value = []
}
</script>
