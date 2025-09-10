<template>
  <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
      <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
          <component
            :is="iconComponent"
            class="h-6 w-6 text-gray-400"
          />
        </div>
        <div class="min-w-0 flex-1">
          <h3 class="text-sm font-medium text-gray-900">
            {{ title }}
          </h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ description }}
          </p>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <button
        @click="$emit('execute')"
        :disabled="loading"
        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed"
      >
        <svg
          v-if="loading"
          class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        {{ loading ? 'Running...' : 'Execute' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  TrashIcon,
  DocumentIcon,
  CircleStackIcon,
  ServerIcon,
  ArchiveBoxIcon,
  HeartIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
  icon: {
    type: String,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['execute'])

const iconComponents = {
  TrashIcon,
  DocumentIcon,
  CircleStackIcon,
  ServerIcon,
  ArchiveBoxIcon,
  HeartIcon,
}

const iconComponent = computed(() => {
  return iconComponents[props.icon] || DocumentIcon
})
</script>
