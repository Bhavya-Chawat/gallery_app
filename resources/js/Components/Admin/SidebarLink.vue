<template>
  <Link
    :href="href"
    :class="[
      'group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-500 overflow-hidden',
      active
        ? 'bg-gradient-to-r from-violet-500/20 via-cyan-500/20 to-purple-500/20 text-white shadow-lg shadow-violet-500/20 border border-violet-500/30 backdrop-blur-sm'
        : 'text-slate-300 hover:bg-gradient-to-r hover:from-slate-800/50 hover:via-slate-700/50 hover:to-slate-800/50 hover:text-white hover:shadow-lg hover:shadow-violet-500/10 hover:border hover:border-white/10 hover:backdrop-blur-sm hover:scale-105'
    ]"
  >
    <!-- Animated Background Mesh for Active State -->
    <div v-if="active" class="absolute inset-0 opacity-20">
      <svg class="w-full h-full animate-float" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="sidebar-mesh" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="6s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="6s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect x="5" y="20" width="90" height="60" fill="none" stroke="url(#sidebar-mesh)" stroke-width="0.5" opacity="0.4" rx="4"/>
        <circle cx="15" cy="30" r="1" fill="url(#sidebar-mesh)" opacity="0.6"/>
        <circle cx="85" cy="30" r="1" fill="url(#sidebar-mesh)" opacity="0.6"/>
        <circle cx="85" cy="70" r="1" fill="url(#sidebar-mesh)" opacity="0.6"/>
        <circle cx="15" cy="70" r="1" fill="url(#sidebar-mesh)" opacity="0.6"/>
        <line x1="50" y1="20" x2="50" y2="80" stroke="url(#sidebar-mesh)" stroke-width="0.3" opacity="0.3"/>
      </svg>
    </div>

    <!-- Floating Particles for Active State -->
    <div v-if="active" class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full opacity-60 animate-float-sidebar-1" style="left: 20%; top: 30%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-cyan-400 to-purple-400 rounded-full opacity-50 animate-float-sidebar-2" style="left: 70%; top: 40%;"></div>
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-purple-400 to-violet-400 rounded-full opacity-40 animate-float-sidebar-3" style="left: 30%; top: 70%;"></div>
    </div>

    <!-- Hover Shimmer Effect -->
    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000 pointer-events-none"></div>

    <!-- Icon Container with Enhanced Styling -->
    <div class="relative flex-shrink-0 mr-3">
      <!-- Icon Glow Effect for Active State -->
      <div v-if="active" class="absolute inset-0 rounded-lg bg-gradient-to-r from-violet-500/30 via-cyan-500/30 to-purple-500/30 blur-sm opacity-75 animate-pulse-glow"></div>
      
      <!-- Icon Background -->
      <div :class="[
        'relative p-1.5 rounded-lg transition-all duration-300',
        active 
          ? 'bg-white/10 backdrop-blur-sm border border-white/20' 
          : 'group-hover:bg-white/5 group-hover:backdrop-blur-sm group-hover:border group-hover:border-white/10'
      ]">
        <component 
          :is="iconComponent" 
          :class="[
            'h-5 w-5 transition-all duration-300',
            active 
              ? 'text-white drop-shadow-lg animate-icon-pulse' 
              : 'text-slate-400 group-hover:text-white group-hover:drop-shadow-md group-hover:scale-110'
          ]" 
        />
      </div>
    </div>

    <!-- Enhanced Text Content -->
    <div class="relative z-10 flex-1 min-w-0">
      <span :class="[
        'transition-all duration-300 tracking-wide',
        active 
          ? 'text-white font-semibold animate-shimmer-active' 
          : 'text-slate-300 group-hover:text-white group-hover:font-medium'
      ]">
        <slot />
      </span>
    </div>

    <!-- Active State Indicator -->
    <div v-if="active" class="absolute right-2 top-1/2 transform -translate-y-1/2">
      <div class="w-2 h-2 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full animate-pulse-dot"></div>
    </div>

    <!-- Hover Border Gradient -->
    <div :class="[
      'absolute inset-0 rounded-xl pointer-events-none transition-all duration-500',
      active 
        ? 'bg-gradient-to-r from-violet-500/10 via-cyan-500/10 to-purple-500/10' 
        : 'bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 group-hover:from-violet-500/5 group-hover:via-cyan-500/5 group-hover:to-purple-500/5'
    ]"></div>

    <!-- Click Ripple Effect -->
    <div class="absolute inset-0 rounded-xl bg-white/10 opacity-0 group-active:opacity-100 transition-opacity duration-150 pointer-events-none"></div>
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

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  25% { transform: translateY(-2px) rotate(0.5deg); }
  50% { transform: translateY(-4px) rotate(0deg); }
  75% { transform: translateY(-2px) rotate(-0.5deg); }
}

@keyframes float-sidebar-1 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.6; }
  25% { transform: translate(6px, -6px); opacity: 0.8; }
  50% { transform: translate(12px, 2px); opacity: 0.4; }
  75% { transform: translate(2px, 8px); opacity: 0.7; }
}

@keyframes float-sidebar-2 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.5; }
  33% { transform: translate(-8px, 6px); opacity: 0.7; }
  66% { transform: translate(6px, -3px); opacity: 0.3; }
}

@keyframes float-sidebar-3 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.4; }
  40% { transform: translate(8px, -8px); opacity: 0.6; }
  80% { transform: translate(-6px, 6px); opacity: 0.2; }
}

@keyframes shimmer-active {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes pulse-glow {
  0%, 100% { opacity: 0.75; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.05); }
}

@keyframes pulse-dot {
  0%, 100% { opacity: 0.8; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.2); }
}

@keyframes icon-pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

@keyframes gradient-shift {
  0%, 100% { stop-color: #8b5cf6; }
  25% { stop-color: #06b6d4; }
  50% { stop-color: #a855f7; }
  75% { stop-color: #8b5cf6; }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-float-sidebar-1 {
  animation: float-sidebar-1 5s ease-in-out infinite;
}

.animate-float-sidebar-2 {
  animation: float-sidebar-2 7s ease-in-out infinite 1s;
}

.animate-float-sidebar-3 {
  animation: float-sidebar-3 6s ease-in-out infinite 2s;
}

.animate-shimmer-active {
  background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  background-size: 200% 100%;
  animation: shimmer-active 3s ease-in-out infinite;
}

.animate-pulse-glow {
  animation: pulse-glow 2s ease-in-out infinite;
}

.animate-pulse-dot {
  animation: pulse-dot 2s ease-in-out infinite;
}

.animate-icon-pulse {
  animation: icon-pulse 3s ease-in-out infinite;
}

.animate-gradient-shift {
  animation: gradient-shift 6s ease-in-out infinite;
}
</style>