<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <component :is="iconComponent" :class="iconClasses" />
      </div>
      <div class="ml-5 w-0 flex-1">
        <dl>
          <dt class="text-sm font-medium text-gray-500 truncate">{{ title }}</dt>
          <dd>
            <div class="text-lg font-medium text-gray-900">{{ value }}</div>
            <div v-if="subtitle" class="text-sm text-gray-500">{{ subtitle }}</div>
          </dd>
        </dl>
      </div>
    </div>
    
    <div v-if="href" class="mt-3">
      <Link :href="href" class="text-sm text-blue-600 hover:text-blue-500">
        View details →
      </Link>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import * as HeroIcons from '@heroicons/vue/24/outline'

const props = defineProps({
  title: String,
  value: [String, Number],
  subtitle: String,
  icon: String,
  color: { type: String, default: 'blue' },
  href: String,
})

const iconComponent = computed(() => HeroIcons[props.icon] || HeroIcons.ChartBarIcon)

const iconClasses = computed(() => {
  const colors = {
    blue: 'text-blue-600',
    green: 'text-green-600', 
    purple: 'text-purple-600',
    yellow: 'text-yellow-600',
    red: 'text-red-600',
  }
  return `h-8 w-8 ${colors[props.color] || colors.blue}`
})
</script>
