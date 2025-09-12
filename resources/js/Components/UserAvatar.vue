<template>
  <div 
    :class="[
      'inline-flex items-center justify-center rounded-full bg-gray-500 text-white font-medium',
      sizeClasses
    ]"
  >
    <img 
      v-if="user.avatar_url" 
      :src="user.avatar_url" 
      :alt="user.name"
      class="w-full h-full rounded-full object-cover"
    />
    <span v-else>{{ initials }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  size: { type: String, default: 'md' }, // sm, md, lg
})

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm': return 'w-6 h-6 text-xs'
    case 'lg': return 'w-12 h-12 text-lg'
    default: return 'w-8 h-8 text-sm'
  }
})

const initials = computed(() => {
  if (!props.user?.name) return '?'
  
  const names = props.user.name.split(' ')
  if (names.length >= 2) {
    return (names[0][0] + names[1][0]).toUpperCase()
  }
  return names[0][0].toUpperCase()
})
</script>
