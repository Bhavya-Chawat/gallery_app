<template>
  <div class="flex-shrink-0">
    <img
      v-if="user.avatar_url"
      class="rounded-full"
      :class="sizeClasses"
      :src="user.avatar_url"
      :alt="user.name"
    >
    <div
      v-else
      class="rounded-full bg-gray-300 flex items-center justify-center"
      :class="sizeClasses"
    >
      <UserIcon class="text-gray-500" :class="iconSizeClasses" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { UserIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value),
  },
})

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'h-6 w-6',
    sm: 'h-8 w-8',
    md: 'h-10 w-10',
    lg: 'h-12 w-12',
    xl: 'h-16 w-16',
  }
  return sizes[props.size]
})

const iconSizeClasses = computed(() => {
  const sizes = {
    xs: 'h-3 w-3',
    sm: 'h-4 w-4',
    md: 'h-5 w-5',
    lg: 'h-6 w-6',
    xl: 'h-8 w-8',
  }
  return sizes[props.size]
})
</script>
