<template>
  <div class="group relative overflow-hidden bg-gradient-to-br from-slate-900/50 via-slate-800/50 to-slate-900/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-violet-500/10 p-6 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/20 hover:border-violet-500/30">
    <!-- Animated Background Mesh -->
    <div class="absolute inset-0 opacity-20">
      <svg class="w-full h-full animate-float" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="mesh-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#8b5cf6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <path d="M20,20 L80,20 L80,80 L20,80 Z" fill="none" stroke="url(#mesh-gradient)" stroke-width="0.5" opacity="0.3"/>
        <circle cx="20" cy="20" r="2" fill="url(#mesh-gradient)" opacity="0.6"/>
        <circle cx="80" cy="20" r="2" fill="url(#mesh-gradient)" opacity="0.6"/>
        <circle cx="80" cy="80" r="2" fill="url(#mesh-gradient)" opacity="0.6"/>
        <circle cx="20" cy="80" r="2" fill="url(#mesh-gradient)" opacity="0.6"/>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full opacity-60 animate-float-1"></div>
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-cyan-400 to-purple-400 rounded-full opacity-40 animate-float-2" style="left: 70%; top: 30%;"></div>
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-purple-400 to-violet-400 rounded-full opacity-50 animate-float-3" style="left: 30%; top: 70%;"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 flex items-center">
      <div class="flex-shrink-0 relative">
        <!-- Icon Glow Effect -->
        <div class="absolute inset-0 rounded-full blur-sm opacity-50" :class="iconGlowClasses"></div>
        <div class="relative p-3 rounded-xl backdrop-blur-sm border border-white/10 transition-all duration-300 group-hover:scale-110" :class="iconBgClasses">
          <component :is="iconComponent" :class="statusClasses" />
        </div>
      </div>
      
      <div class="ml-6 w-0 flex-1">
        <dl>
          <dt class="text-sm font-semibold bg-gradient-to-r from-slate-200 to-slate-300 bg-clip-text text-transparent animate-shimmer tracking-wide">
            {{ title }}
          </dt>
          <dd class="mt-2">
            <div :class="statusTextClasses" class="text-lg font-bold capitalize transition-all duration-300 group-hover:scale-105 inline-block">
              {{ status }}
            </div>
            <div v-if="message" class="text-xs text-slate-400 mt-2 leading-relaxed tracking-wide transition-all duration-300 group-hover:text-slate-300">
              {{ message }}
            </div>
          </dd>
        </dl>
      </div>
    </div>

    <!-- Enhanced Hover Border Effect -->
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 group-hover:from-violet-500/20 group-hover:via-cyan-500/20 group-hover:to-purple-500/20 transition-all duration-700 pointer-events-none"></div>
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
    healthy: 'text-emerald-400',
    warning: 'text-amber-400',
    error: 'text-rose-400',
    unknown: 'text-slate-400',
  }
  return `h-6 w-6 transition-all duration-300 ${classes[props.status] || classes.unknown}`
})

const statusTextClasses = computed(() => {
  const classes = {
    healthy: 'text-emerald-300 drop-shadow-sm',
    warning: 'text-amber-300 drop-shadow-sm',
    error: 'text-rose-300 drop-shadow-sm',
    unknown: 'text-slate-300 drop-shadow-sm',
  }
  return classes[props.status] || classes.unknown
})

const iconBgClasses = computed(() => {
  const classes = {
    healthy: 'bg-emerald-500/10 border-emerald-500/20',
    warning: 'bg-amber-500/10 border-amber-500/20',
    error: 'bg-rose-500/10 border-rose-500/20',
    unknown: 'bg-slate-500/10 border-slate-500/20',
  }
  return classes[props.status] || classes.unknown
})

const iconGlowClasses = computed(() => {
  const classes = {
    healthy: 'bg-emerald-500/30',
    warning: 'bg-amber-500/30',
    error: 'bg-rose-500/30',
    unknown: 'bg-slate-500/30',
  }
  return classes[props.status] || classes.unknown
})
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  25% { transform: translateY(-5px) rotate(1deg); }
  50% { transform: translateY(-10px) rotate(0deg); }
  75% { transform: translateY(-5px) rotate(-1deg); }
}

@keyframes float-1 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.6; }
  25% { transform: translate(10px, -10px); opacity: 0.8; }
  50% { transform: translate(20px, 5px); opacity: 0.4; }
  75% { transform: translate(5px, 15px); opacity: 0.7; }
}

@keyframes float-2 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.4; }
  33% { transform: translate(-15px, 10px); opacity: 0.6; }
  66% { transform: translate(10px, -5px); opacity: 0.3; }
}

@keyframes float-3 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.5; }
  40% { transform: translate(15px, -15px); opacity: 0.7; }
  80% { transform: translate(-10px, 10px); opacity: 0.3; }
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-float-1 {
  animation: float-1 8s ease-in-out infinite;
}

.animate-float-2 {
  animation: float-2 10s ease-in-out infinite 2s;
}

.animate-float-3 {
  animation: float-3 12s ease-in-out infinite 4s;
}

.animate-shimmer {
  background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer 3s ease-in-out infinite;
}

.animate-gradient-shift {
  animation: gradient-shift 8s ease-in-out infinite;
}

@keyframes gradient-shift {
  0%, 100% { stop-color: #8b5cf6; }
  50% { stop-color: #06b6d4; }
}
</style>