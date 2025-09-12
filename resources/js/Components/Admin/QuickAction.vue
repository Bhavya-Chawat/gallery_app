<template>
  <component 
    :is="href ? Link : 'button'"
    :href="href"
    @click="!href && $emit('click')"
    :disabled="loading"
    class="p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow text-left disabled:opacity-50"
  >
    <div class="flex items-center space-x-3">
      <component :is="iconComponent" class="h-8 w-8 text-gray-600" />
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-900">{{ title }}</p>
        <p class="text-xs text-gray-500">{{ loading ? 'Loading...' : description }}</p>
      </div>
    </div>
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import * as HeroIcons from '@heroicons/vue/24/outline'

const props = defineProps({
  title: String,
  description: String,
  icon: String,
  href: String,
  loading: Boolean,
})

defineEmits(['click'])

const iconComponent = computed(() => HeroIcons[props.icon] || HeroIcons.CubeIcon)
</script>
