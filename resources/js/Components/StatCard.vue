<template>
  <div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="p-5">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <component 
            :is="icon" 
            :class="[
              'h-6 w-6',
              colorClasses.icon
            ]"
          />
        </div>
        <div class="ml-5 w-0 flex-1">
          <dl>
            <dt class="text-sm font-medium text-gray-500 truncate">
              {{ title }}
            </dt>
            <dd class="flex items-baseline">
              <div :class="['text-2xl font-semibold', colorClasses.text]">
                {{ formattedValue }}
              </div>
              <div 
                v-if="change !== undefined"
                :class="[
                  'ml-2 flex items-baseline text-sm font-semibold',
                  change >= 0 ? 'text-green-600' : 'text-red-600'
                ]"
              >
                <ArrowUpIcon 
                  v-if="change >= 0" 
                  class="self-center flex-shrink-0 h-4 w-4 text-green-500"
                />
                <ArrowDownIcon 
                  v-else 
                  class="self-center flex-shrink-0 h-4 w-4 text-red-500"
                />
                <span class="sr-only">
                  {{ change >= 0 ? 'Increased' : 'Decreased' }} by
                </span>
                {{ Math.abs(change) }}%
              </div>
            </dd>
          </dl>
        </div>
      </div>
    </div>
    <div v-if="href" class="bg-gray-50 px-5 py-3">
      <div class="text-sm">
        <Link :href="href" :class="['font-medium', colorClasses.link]">
          View {{ title.toLowerCase() }}
          <span class="sr-only"> stats</span>
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ArrowUpIcon, ArrowDownIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    required: true
  },
  icon: {
    type: Object,
    required: true
  },
  change: {
    type: Number,
    default: undefined
  },
  color: {
    type: String,
    default: 'blue'
  },
  href: {
    type: String,
    default: null
  },
  format: {
    type: String,
    default: 'number'
  }
})

const colorClasses = computed(() => {
  const colors = {
    blue: {
      icon: 'text-blue-400',
      text: 'text-gray-900',
      link: 'text-blue-600 hover:text-blue-500'
    },
    green: {
      icon: 'text-green-400',
      text: 'text-gray-900',
      link: 'text-green-600 hover:text-green-500'
    },
    red: {
      icon: 'text-red-400',
      text: 'text-gray-900',
      link: 'text-red-600 hover:text-red-500'
    },
    yellow: {
      icon: 'text-yellow-400',
      text: 'text-gray-900',
      link: 'text-yellow-600 hover:text-yellow-500'
    }
  }
  return colors[props.color] || colors.blue
})

const formattedValue = computed(() => {
  const value = props.value
  
  if (props.format === 'currency') {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(value)
  }
  
  if (props.format === 'percentage') {
    return `${value}%`
  }
  
  if (typeof value === 'number') {
    if (value >= 1000000) {
      return `${(value / 1000000).toFixed(1)}M`
    } else if (value >= 1000) {
      return `${(value / 1000).toFixed(1)}K`
    }
    return value.toLocaleString()
  }
  
  return value
})
</script>
