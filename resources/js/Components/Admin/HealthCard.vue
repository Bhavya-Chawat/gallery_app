<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <component :is="iconComponent" :class="statusClasses" />
      </div>
      <div class="ml-5 w-0 flex-1">
        <dl>
          <dt class="text-sm font-medium text-gray-500">{{ title }}</dt>
          <dd>
            <div :class="statusTextClasses" class="text-sm font-medium capitalize">
              {{ status }}
            </div>
            <div v-if="message" class="text-xs text-gray-500 mt-1">{{ message }}</div>
          </dd>
        </dl>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import * as HeroIcons from '@heroicons/vue/24/outline'

const props = defineProps({
  title: String,
  status: String,
  message: String,
  icon: String,
})

const iconComponent = computed(() => HeroIcons[props.icon] || HeroIcons.CheckCircleIcon)

const statusClasses = computed(() => {
  const classes = {
    healthy: 'text-green-600',
    warning: 'text-yellow-600',
    error: 'text-red-600',
    unknown: 'text-gray-400',
  }
  return `h-6 w-6 ${classes[props.status] || classes.unknown}`
})

const statusTextClasses = computed(() => {
  const classes = {
    healthy: 'text-green-900',
    warning: 'text-yellow-900', 
    error: 'text-red-900',
    unknown: 'text-gray-500',
  }
  return classes[props.status] || classes.unknown
})
</script>
