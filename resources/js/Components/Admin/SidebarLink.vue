<template>
  <Link 
    :href="href"
    :class="[
      'flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors',
      active 
        ? 'bg-blue-100 text-blue-700' 
        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
    ]"
  >
    <component :is="iconComponent" class="mr-3 h-5 w-5" />
    <slot />
  </Link>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import * as HeroIcons from '@heroicons/vue/24/outline'

const props = defineProps({
  href: String,
  active: Boolean,
  icon: String,
})

const iconComponent = computed(() => HeroIcons[props.icon] || HeroIcons.HomeIcon)
</script>
