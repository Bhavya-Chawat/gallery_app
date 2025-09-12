<template>
  <div :class="sizeClasses">
    <img
      v-if="user.avatar_path"
      :src="avatarUrl"
      :alt="user.name"
      class="w-full h-full object-cover rounded-full"
    />
    <div v-else class="w-full h-full bg-gray-300 rounded-full flex items-center justify-center">
      <span :class="textSizeClasses" class="font-medium text-gray-600">
        {{ initials }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

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
    lg: 'h-16 w-16',
    xl: 'h-20 w-20',
  }
  return sizes[props.size]
})

const textSizeClasses = computed(() => {
  const sizes = {
    xs: 'text-xs',
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-xl',
    xl: 'text-2xl',
  }
  return sizes[props.size]
})

const avatarUrl = computed(() => {
  return props.user.avatar_path 
    ? `http://localhost:9000/gallery-images/${props.user.avatar_path}`
    : null
})

const initials = computed(() => {
  return props.user.name
    ?.split(' ')
    .map(word => word[0])
    .slice(0, 2)
    .join('')
    .toUpperCase() || '??'
})
</script>
