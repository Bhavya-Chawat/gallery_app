<template>
  <Link
    :href="href"
    :class="[
      'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150',
      active
        ? 'bg-gray-900 text-white'
        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
    ]"
    @click="handleClick"
  >
    <component
      :is="iconComponent"
      class="mr-3 h-5 w-5 flex-shrink-0"
      :class="active ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300'"
    />
    <span class="flex-1">
      <slot />
    </span>
    <span
      v-if="badge && badge > 0"
      class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
    >
      {{ badge }}
    </span>
  </Link>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  ChartBarIcon,
  ChartPieIcon,
  CogIcon,
  UsersIcon,
  PhotoIcon,
  FolderIcon,
  ShieldCheckIcon,
  ChatBubbleLeftRightIcon,
  FlagIcon,
  WrenchScrewdriverIcon,
  DocumentArrowDownIcon,
  HomeIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  href: {
    type: String,
    default: '#',
  },
  active: {
    type: Boolean,
    default: false,
  },
  icon: {
    type: String,
    required: true,
  },
  badge: {
    type: Number,
    default: null,
  },
})

const emit = defineEmits(['click'])

const iconComponents = {
  ChartBarIcon,
  ChartPieIcon,
  CogIcon,
  UsersIcon,
  PhotoIcon,
  FolderIcon,
  ShieldCheckIcon,
  ChatBubbleLeftRightIcon,
  FlagIcon,
  WrenchScrewdriverIcon,
  DocumentArrowDownIcon,
  HomeIcon,
}

const iconComponent = computed(() => {
  return iconComponents[props.icon] || ChartBarIcon
})

const handleClick = (event) => {
  if (props.href === '#') {
    event.preventDefault()
    emit('click')
  }
}
</script>
