<template>
  <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900/90 to-slate-800/90 backdrop-blur-xl border border-white/10 shadow-2xl hover:shadow-violet-500/20 transition-all duration-500 hover:scale-105 hover:border-violet-500/30">
    <!-- Animated background gradient -->
    <div class="absolute inset-0 bg-gradient-to-r from-violet-600/10 via-purple-600/10 to-cyan-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
    
    <!-- Floating particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-2 left-4 w-1 h-1 bg-violet-400 rounded-full animate-pulse opacity-60"></div>
      <div class="absolute top-8 right-8 w-1.5 h-1.5 bg-cyan-400 rounded-full animate-bounce opacity-40" style="animation-delay: 0.5s;"></div>
      <div class="absolute bottom-4 left-8 w-1 h-1 bg-purple-400 rounded-full animate-ping opacity-50" style="animation-delay: 1s;"></div>
    </div>

    <div class="relative p-6">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <!-- Enhanced icon container -->
          <div class="relative p-3 rounded-xl bg-gradient-to-br from-white/10 to-white/5 border border-white/10 group-hover:border-violet-400/30 transition-all duration-300">
            <component
              :is="icon"
              :class="[
                'h-7 w-7 transition-all duration-300 group-hover:scale-110',
                colorClasses.icon
              ]"
            />
            <!-- Icon glow effect -->
            <div class="absolute inset-0 rounded-xl bg-gradient-to-br opacity-0 group-hover:opacity-20 transition-opacity duration-300"
                 :class="colorClasses.glow"></div>
          </div>
        </div>
        
        <div class="ml-6 w-0 flex-1">
          <dl>
            <dt class="text-sm font-medium text-slate-300 truncate mb-2 group-hover:text-white transition-colors duration-300">
              {{ title }}
            </dt>
            <dd class="flex items-baseline">
              <!-- Enhanced value display with shimmer effect -->
              <div class="relative">
                <div :class="['text-3xl font-bold bg-gradient-to-r bg-clip-text text-transparent transition-all duration-300', colorClasses.text]">
                  {{ formattedValue }}
                </div>
                <!-- Shimmer overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -skew-x-12 opacity-0 group-hover:opacity-100 group-hover:animate-shimmer transition-opacity duration-700"></div>
              </div>
              
              <!-- Enhanced change indicator -->
              <div
                v-if="change !== undefined"
                :class="[
                  'ml-3 flex items-center px-2.5 py-1 rounded-full text-sm font-semibold transition-all duration-300 backdrop-blur-sm',
                  change >= 0 
                    ? 'text-emerald-300 bg-emerald-500/10 border border-emerald-500/20 group-hover:bg-emerald-500/20' 
                    : 'text-red-300 bg-red-500/10 border border-red-500/20 group-hover:bg-red-500/20'
                ]"
              >
                <component
                  :is="change >= 0 ? ArrowUpIcon : ArrowDownIcon"
                  :class="[
                    'h-4 w-4 mr-1 transition-transform duration-300 group-hover:scale-110',
                    change >= 0 ? 'text-emerald-400' : 'text-red-400'
                  ]"
                />
                <span class="sr-only">
                  {{ change >= 0 ? 'Increased' : 'Decreased' }} by
                </span>
                {{ Math.abs(change) }}%
              </div>
            </dd>
          </dl>
        </div>
      </div>
    </div>

    <!-- Enhanced footer section -->
    <div v-if="href" class="relative bg-gradient-to-r from-slate-800/50 to-slate-700/50 backdrop-blur-sm border-t border-white/5 px-6 py-4 group-hover:from-slate-800/70 group-hover:to-slate-700/70 transition-all duration-300">
      <div class="text-sm">
        <Link 
          :href="href" 
          :class="[
            'inline-flex items-center font-medium transition-all duration-300 group/link hover:scale-105', 
            colorClasses.link
          ]"
        >
          <span class="relative">
            View {{ title.toLowerCase() }}
            <!-- Link underline animation -->
            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r transition-all duration-300 group-hover/link:w-full"
                  :class="colorClasses.linkUnderline"></span>
          </span>
          <svg class="ml-2 h-4 w-4 transition-transform duration-300 group-hover/link:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
          <span class="sr-only"> stats</span>
        </Link>
      </div>
    </div>

    <!-- Hover border glow -->
    <div class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
         :class="colorClasses.borderGlow"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ArrowUpIcon, ArrowDownIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    required: true
  },
  icon: {
    type: Object,
    required: true
  },
  change: {
    type: Number,
    default: undefined
  },
  color: {
    type: String,
    default: 'blue'
  },
  href: {
    type: String,
    default: null
  },
  format: {
    type: String,
    default: 'number'
  }
})

const colorClasses = computed(() => {
  const colors = {
    blue: {
      icon: 'text-cyan-400 group-hover:text-cyan-300',
      text: 'from-cyan-400 via-blue-400 to-violet-400 group-hover:from-cyan-300 group-hover:via-blue-300 group-hover:to-violet-300',
      link: 'text-cyan-400 hover:text-cyan-300',
      linkUnderline: 'from-cyan-400 to-blue-400',
      glow: 'from-cyan-500/20 to-blue-500/20',
      borderGlow: 'shadow-[0_0_30px_-12px] shadow-cyan-500/30'
    },
    green: {
      icon: 'text-emerald-400 group-hover:text-emerald-300',
      text: 'from-emerald-400 via-green-400 to-teal-400 group-hover:from-emerald-300 group-hover:via-green-300 group-hover:to-teal-300',
      link: 'text-emerald-400 hover:text-emerald-300',
      linkUnderline: 'from-emerald-400 to-green-400',
      glow: 'from-emerald-500/20 to-green-500/20',
      borderGlow: 'shadow-[0_0_30px_-12px] shadow-emerald-500/30'
    },
    red: {
      icon: 'text-red-400 group-hover:text-red-300',
      text: 'from-red-400 via-rose-400 to-pink-400 group-hover:from-red-300 group-hover:via-rose-300 group-hover:to-pink-300',
      link: 'text-red-400 hover:text-red-300',
      linkUnderline: 'from-red-400 to-rose-400',
      glow: 'from-red-500/20 to-rose-500/20',
      borderGlow: 'shadow-[0_0_30px_-12px] shadow-red-500/30'
    },
    yellow: {
      icon: 'text-amber-400 group-hover:text-amber-300',
      text: 'from-amber-400 via-yellow-400 to-orange-400 group-hover:from-amber-300 group-hover:via-yellow-300 group-hover:to-orange-300',
      link: 'text-amber-400 hover:text-amber-300',
      linkUnderline: 'from-amber-400 to-yellow-400',
      glow: 'from-amber-500/20 to-yellow-500/20',
      borderGlow: 'shadow-[0_0_30px_-12px] shadow-amber-500/30'
    },
    purple: {
      icon: 'text-purple-400 group-hover:text-purple-300',
      text: 'from-purple-400 via-violet-400 to-indigo-400 group-hover:from-purple-300 group-hover:via-violet-300 group-hover:to-indigo-300',
      link: 'text-purple-400 hover:text-purple-300',
      linkUnderline: 'from-purple-400 to-violet-400',
      glow: 'from-purple-500/20 to-violet-500/20',
      borderGlow: 'shadow-[0_0_30px_-12px] shadow-purple-500/30'
    }
  }
  return colors[props.color] || colors.blue
})

const formattedValue = computed(() => {
  const value = props.value
  
  if (props.format === 'currency') {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(value)
  }
  
  if (props.format === 'percentage') {
    return `${value}%`
  }
  
  if (typeof value === 'number') {
    if (value >= 1000000) {
      return `${(value / 1000000).toFixed(1)}M`
    } else if (value >= 1000) {
      return `${(value / 1000).toFixed(1)}K`
    }
    return value.toLocaleString()
  }
  
  return value
})
</script>

<style scoped>
@keyframes shimmer {
  0% {
    transform: translateX(-100%) skewX(-12deg);
  }
  100% {
    transform: translateX(200%) skewX(-12deg);
  }
}

.animate-shimmer {
  animation: shimmer 2s ease-in-out;
}
</style>