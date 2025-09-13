<template>
  <div class="group relative overflow-hidden bg-gradient-to-br from-slate-900/60 via-slate-800/60 to-slate-900/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-violet-500/10 p-6 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/20 hover:border-violet-500/30">
    <!-- Animated Background Mesh -->
    <div class="absolute inset-0 opacity-15">
      <svg class="w-full h-full animate-float" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="stat-mesh" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect x="15" y="15" width="70" height="70" fill="none" stroke="url(#stat-mesh)" stroke-width="0.4" opacity="0.4" rx="8"/>
        <circle cx="25" cy="25" r="1.5" fill="url(#stat-mesh)" opacity="0.6"/>
        <circle cx="75" cy="25" r="1.5" fill="url(#stat-mesh)" opacity="0.6"/>
        <circle cx="75" cy="75" r="1.5" fill="url(#stat-mesh)" opacity="0.6"/>
        <circle cx="25" cy="75" r="1.5" fill="url(#stat-mesh)" opacity="0.6"/>
        <line x1="50" y1="15" x2="50" y2="85" stroke="url(#stat-mesh)" stroke-width="0.3" opacity="0.3"/>
        <line x1="15" y1="50" x2="85" y2="50" stroke="url(#stat-mesh)" stroke-width="0.3" opacity="0.3"/>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full opacity-50 animate-float-stat-1" style="left: 20%; top: 25%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-cyan-400 to-purple-400 rounded-full opacity-60 animate-float-stat-2" style="left: 75%; top: 30%;"></div>
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-purple-400 to-violet-400 rounded-full opacity-40 animate-float-stat-3" style="left: 30%; top: 70%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full opacity-70 animate-float-stat-4" style="left: 80%; top: 75%;"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 flex items-center">
      <!-- Enhanced Icon Container -->
      <div class="flex-shrink-0 relative">
        <!-- Icon Glow Effect -->
        <div class="absolute inset-0 rounded-xl blur-sm opacity-50 transition-all duration-500 group-hover:opacity-75" :class="iconGlowClasses"></div>
        
        <!-- Icon Background -->
        <div class="relative p-3 rounded-xl backdrop-blur-sm border border-white/10 transition-all duration-300 group-hover:scale-110 group-hover:border-white/20" :class="iconBgClasses">
          <component :is="iconComponent" :class="iconClasses" />
        </div>
      </div>
      
      <!-- Enhanced Content -->
      <div class="ml-6 w-0 flex-1">
        <dl>
          <dt class="text-sm font-semibold bg-gradient-to-r from-slate-200 to-slate-300 bg-clip-text text-transparent animate-shimmer-title tracking-wide truncate">
            {{ title }}
          </dt>
          <dd class="mt-2">
            <div class="text-2xl font-bold transition-all duration-300 group-hover:scale-105 inline-block" :class="valueTextClasses">
              {{ value }}
            </div>
            <div v-if="subtitle" class="text-sm text-slate-400 mt-1 transition-all duration-300 group-hover:text-slate-300 leading-relaxed">
              {{ subtitle }}
            </div>
          </dd>
        </dl>
      </div>
    </div>

    <!-- Enhanced Link Section -->
    <div v-if="href" class="relative z-10 mt-6 pt-4 border-t border-white/10">
      <Link 
        :href="href" 
        class="group/link inline-flex items-center text-sm font-medium transition-all duration-300 hover:scale-105" 
        :class="linkClasses"
      >
        <span class="bg-gradient-to-r bg-clip-text text-transparent animate-shimmer-link" :class="linkGradientClasses">
          View details
        </span>
        <svg class="ml-2 w-4 h-4 transition-all duration-300 group-hover/link:translate-x-1 group-hover/link:scale-110" :class="linkIconClasses" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </Link>
    </div>

    <!-- Enhanced Hover Border Effect -->
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 group-hover:from-violet-500/10 group-hover:via-cyan-500/10 group-hover:to-purple-500/10 transition-all duration-700 pointer-events-none"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import * as HeroIcons from '@heroicons/vue/24/outline'

const props = defineProps({
  title: String,
  value: [String, Number],
  subtitle: String,
  icon: String,
  color: { type: String, default: 'blue' },
  href: String,
})

const iconComponent = computed(() => HeroIcons[props.icon] || HeroIcons.ChartBarIcon)

const iconClasses = computed(() => {
  const colors = {
    blue: 'text-cyan-400',
    green: 'text-emerald-400',
    purple: 'text-violet-400',
    yellow: 'text-amber-400',
    red: 'text-rose-400',
  }
  return `h-8 w-8 transition-all duration-300 group-hover:drop-shadow-lg ${colors[props.color] || colors.blue}`
})

const iconBgClasses = computed(() => {
  const colors = {
    blue: 'bg-cyan-500/10 border-cyan-500/20',
    green: 'bg-emerald-500/10 border-emerald-500/20',
    purple: 'bg-violet-500/10 border-violet-500/20',
    yellow: 'bg-amber-500/10 border-amber-500/20',
    red: 'bg-rose-500/10 border-rose-500/20',
  }
  return colors[props.color] || colors.blue
})

const iconGlowClasses = computed(() => {
  const colors = {
    blue: 'bg-cyan-500/30',
    green: 'bg-emerald-500/30',
    purple: 'bg-violet-500/30',
    yellow: 'bg-amber-500/30',
    red: 'bg-rose-500/30',
  }
  return colors[props.color] || colors.blue
})

const valueTextClasses = computed(() => {
  const colors = {
    blue: 'text-cyan-300 drop-shadow-sm',
    green: 'text-emerald-300 drop-shadow-sm',
    purple: 'text-violet-300 drop-shadow-sm',
    yellow: 'text-amber-300 drop-shadow-sm',
    red: 'text-rose-300 drop-shadow-sm',
  }
  return colors[props.color] || colors.blue
})

const linkClasses = computed(() => {
  const colors = {
    blue: 'hover:text-cyan-300',
    green: 'hover:text-emerald-300',
    purple: 'hover:text-violet-300',
    yellow: 'hover:text-amber-300',
    red: 'hover:text-rose-300',
  }
  return colors[props.color] || colors.blue
})

const linkGradientClasses = computed(() => {
  const colors = {
    blue: 'from-cyan-400 to-cyan-300',
    green: 'from-emerald-400 to-emerald-300',
    purple: 'from-violet-400 to-violet-300',
    yellow: 'from-amber-400 to-amber-300',
    red: 'from-rose-400 to-rose-300',
  }
  return colors[props.color] || colors.blue
})

const linkIconClasses = computed(() => {
  const colors = {
    blue: 'text-cyan-400',
    green: 'text-emerald-400',
    purple: 'text-violet-400',
    yellow: 'text-amber-400',
    red: 'text-rose-400',
  }
  return colors[props.color] || colors.blue
})
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  25% { transform: translateY(-4px) rotate(1deg); }
  50% { transform: translateY(-8px) rotate(0deg); }
  75% { transform: translateY(-4px) rotate(-1deg); }
}

@keyframes float-stat-1 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.5; }
  25% { transform: translate(8px, -8px); opacity: 0.8; }
  50% { transform: translate(15px, 4px); opacity: 0.3; }
  75% { transform: translate(4px, 12px); opacity: 0.6; }
}

@keyframes float-stat-2 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.6; }
  33% { transform: translate(-10px, 8px); opacity: 0.8; }
  66% { transform: translate(8px, -4px); opacity: 0.4; }
}

@keyframes float-stat-3 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.4; }
  40% { transform: translate(12px, -12px); opacity: 0.7; }
  80% { transform: translate(-8px, 8px); opacity: 0.2; }
}

@keyframes float-stat-4 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.7; }
  30% { transform: translate(-8px, -6px); opacity: 0.9; }
  70% { transform: translate(6px, 8px); opacity: 0.3; }
}

@keyframes shimmer-title {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes shimmer-link {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes gradient-shift {
  0%, 100% { stop-color: #8b5cf6; }
  25% { stop-color: #06b6d4; }
  50% { stop-color: #a855f7; }
  75% { stop-color: #8b5cf6; }
}

.animate-float {
  animation: float 7s ease-in-out infinite;
}

.animate-float-stat-1 {
  animation: float-stat-1 6s ease-in-out infinite;
}

.animate-float-stat-2 {
  animation: float-stat-2 8s ease-in-out infinite 1s;
}

.animate-float-stat-3 {
  animation: float-stat-3 9s ease-in-out infinite 2s;
}

.animate-float-stat-4 {
  animation: float-stat-4 7s ease-in-out infinite 3s;
}

.animate-shimmer-title {
  background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer-title 3s ease-in-out infinite;
}

.animate-shimmer-link {
  background-size: 200% 100%;
  animation: shimmer-link 4s ease-in-out infinite;
}

.animate-gradient-shift {
  animation: gradient-shift 8s ease-in-out infinite;
}
</style>
