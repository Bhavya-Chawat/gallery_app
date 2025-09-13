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
/*<template>
  <div
    :class="[
      'relative inline-flex items-center justify-center rounded-full font-medium transition-all duration-500 ease-out hover:scale-110 hover:shadow-2xl group cursor-pointer overflow-hidden',
      sizeClasses,
      'bg-gradient-to-br from-violet-500 via-purple-500 to-cyan-500',
      'shadow-lg hover:shadow-violet-500/25',
      'ring-2 ring-white/10 hover:ring-white/20',
      'backdrop-blur-xl',
      'animate-pulse hover:animate-none'
    ]"
  >
    <!-- Animated background gradient overlay -->
    <div 
      class="absolute inset-0 rounded-full bg-gradient-to-br from-violet-400/20 via-purple-400/20 to-cyan-400/20 animate-gradient-shift opacity-0 group-hover:opacity-100 transition-opacity duration-700"
    ></div>
    
    <!-- Floating particles effect -->
    <div class="absolute inset-0 rounded-full overflow-hidden">
      <div 
        v-for="i in 3" 
        :key="i"
        :class="[
          'absolute w-1 h-1 bg-white/30 rounded-full',
          `animate-float-${i}`
        ]"
        :style="{
          left: `${20 + i * 25}%`,
          top: `${15 + i * 20}%`,
          animationDelay: `${i * 0.8}s`
        }"
      ></div>
    </div>
    
    <!-- Main content -->
    <div class="relative z-10 w-full h-full flex items-center justify-center">
      <img
        v-if="user.avatar_url"
        :src="user.avatar_url"
        :alt="user.name"
        class="w-full h-full rounded-full object-cover transition-transform duration-500 group-hover:scale-105 ring-1 ring-white/20"
      />
      <span 
        v-else 
        :class="[
          'font-bold tracking-wide bg-gradient-to-br from-white via-slate-100 to-slate-200 bg-clip-text text-transparent',
          'drop-shadow-lg group-hover:drop-shadow-xl transition-all duration-300',
          'animate-shimmer'
        ]"
      >
        {{ initials }}
      </span>
    </div>
    
    <!-- Hover glow effect -->
    <div class="absolute inset-0 rounded-full bg-gradient-to-br from-violet-400/0 via-purple-400/0 to-cyan-400/0 group-hover:from-violet-400/10 group-hover:via-purple-400/10 group-hover:to-cyan-400/10 transition-all duration-700 animate-pulse-slow"></div>
    
    <!-- Status indicator for online users (optional enhancement) -->
    <div 
      v-if="user.is_online"
      :class="[
        'absolute -bottom-0 -right-0 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 shadow-lg',
        'ring-2 ring-slate-900 transition-all duration-300 group-hover:scale-110',
        statusIndicatorSize,
        'animate-bounce'
      ]"
      style="animation-duration: 2s;"
    ></div>
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

const statusIndicatorSize = computed(() => {
  switch (props.size) {
    case 'sm': return 'w-2 h-2'
    case 'lg': return 'w-4 h-4'
    default: return 'w-3 h-3'
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

<style scoped>
@keyframes gradient-shift {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

@keyframes shimmer {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

@keyframes float-1 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.4; }
  33% { transform: translateY(-10px) translateX(5px) rotate(120deg); opacity: 0.8; }
  66% { transform: translateY(-5px) translateX(-3px) rotate(240deg); opacity: 0.6; }
}

@keyframes float-2 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  33% { transform: translateY(-8px) translateX(-4px) rotate(90deg); opacity: 0.7; }
  66% { transform: translateY(-12px) translateX(6px) rotate(180deg); opacity: 0.5; }
}

@keyframes float-3 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.5; }
  33% { transform: translateY(-6px) translateX(8px) rotate(150deg); opacity: 0.9; }
  66% { transform: translateY(-15px) translateX(-2px) rotate(300deg); opacity: 0.4; }
}

@keyframes pulse-slow {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.8; }
}

.animate-gradient-shift {
  background-size: 200% 200%;
  animation: gradient-shift 6s ease infinite;
}

.animate-shimmer {
  background-size: 200% auto;
  animation: shimmer 3s linear infinite;
}

.animate-float-1 {
  animation: float-1 4s ease-in-out infinite;
}

.animate-float-2 {
  animation: float-2 5s ease-in-out infinite;
}

.animate-float-3 {
  animation: float-3 3.5s ease-in-out infinite;
}

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}
</style>