 <template>
  <component 
    :is="href ? Link : 'button'"
    :href="href"
    @click="!href && $emit('click')"
    :disabled="loading"
    class="group relative overflow-hidden p-6 bg-gradient-to-br from-slate-900/60 via-slate-800/60 to-slate-900/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-violet-500/10 hover:shadow-2xl hover:shadow-violet-500/20 transition-all duration-500 text-left disabled:opacity-50 hover:scale-105 hover:border-violet-500/30 active:scale-95"
  >
    <!-- Animated Background Mesh -->
    <div class="absolute inset-0 opacity-15">
      <svg class="w-full h-full animate-float" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="quickaction-mesh" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect x="10" y="10" width="80" height="80" fill="none" stroke="url(#quickaction-mesh)" stroke-width="0.3" opacity="0.4" rx="8"/>
        <circle cx="20" cy="20" r="1.5" fill="url(#quickaction-mesh)" opacity="0.6"/>
        <circle cx="80" cy="20" r="1.5" fill="url(#quickaction-mesh)" opacity="0.6"/>
        <circle cx="80" cy="80" r="1.5" fill="url(#quickaction-mesh)" opacity="0.6"/>
        <circle cx="20" cy="80" r="1.5" fill="url(#quickaction-mesh)" opacity="0.6"/>
        <line x1="50" y1="10" x2="50" y2="90" stroke="url(#quickaction-mesh)" stroke-width="0.2" opacity="0.3"/>
        <line x1="10" y1="50" x2="90" y2="50" stroke="url(#quickaction-mesh)" stroke-width="0.2" opacity="0.3"/>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full opacity-50 animate-float-particle-1" style="left: 15%; top: 20%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-cyan-400 to-purple-400 rounded-full opacity-60 animate-float-particle-2" style="left: 75%; top: 25%;"></div>
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-purple-400 to-violet-400 rounded-full opacity-40 animate-float-particle-3" style="left: 25%; top: 75%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full opacity-70 animate-float-particle-4" style="left: 80%; top: 70%;"></div>
    </div>

    <!-- Loading Shimmer Overlay -->
    <div v-if="loading" class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent animate-shimmer-loading"></div>

    <!-- Main Content -->
    <div class="relative z-10 flex items-center space-x-4">
      <!-- Enhanced Icon Container -->
      <div class="relative flex-shrink-0">
        <!-- Icon Glow Effect -->
        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/20 via-cyan-500/20 to-purple-500/20 blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        
        <!-- Icon Background -->
        <div class="relative p-3 rounded-xl bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-sm border border-white/10 transition-all duration-300 group-hover:scale-110 group-hover:border-violet-500/30 group-hover:bg-gradient-to-br group-hover:from-violet-500/10 group-hover:to-cyan-500/10">
          <component 
            :is="iconComponent" 
            :class="[
              'h-8 w-8 transition-all duration-300',
              loading ? 'text-slate-400 animate-pulse' : 'text-slate-300 group-hover:text-white group-hover:drop-shadow-lg'
            ]" 
          />
          
          <!-- Loading Spinner Overlay -->
          <div v-if="loading" class="absolute inset-0 flex items-center justify-center">
            <div class="w-6 h-6 border-2 border-slate-400 border-t-violet-400 rounded-full animate-spin"></div>
          </div>
        </div>
      </div>
      
      <!-- Enhanced Text Content -->
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold bg-gradient-to-r from-slate-100 to-slate-200 bg-clip-text text-transparent group-hover:from-white group-hover:to-slate-100 transition-all duration-300 animate-shimmer-text tracking-wide">
          {{ title }}
        </p>
        <p class="text-xs mt-1 transition-all duration-300 tracking-wide leading-relaxed" :class="[
          loading 
            ? 'text-slate-400 animate-pulse' 
            : 'text-slate-400 group-hover:text-slate-300'
        ]">
          {{ loading ? 'Loading...' : description }}
        </p>
        
        <!-- Progress indicator for loading -->
        <div v-if="loading" class="mt-2 w-full bg-slate-700 rounded-full h-1 overflow-hidden">
          <div class="bg-gradient-to-r from-violet-500 to-cyan-500 h-full rounded-full animate-progress-bar"></div>
        </div>
      </div>

      <!-- Action Arrow -->
      <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
        <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </div>
    </div>

    <!-- Enhanced Hover Border Effect -->
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 group-hover:from-violet-500/10 group-hover:via-cyan-500/10 group-hover:to-purple-500/10 transition-all duration-700 pointer-events-none"></div>
    
    <!-- Ripple Effect on Click -->
    <div class="absolute inset-0 rounded-2xl bg-white/5 opacity-0 group-active:opacity-100 transition-opacity duration-150 pointer-events-none"></div>
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import * as HeroIcons from '@heroicons/vue/24/outline'

const props = defineProps({
  title: String,
  description: String,
  icon: String,
  href: String,
  loading: Boolean,
})

defineEmits(['click'])

const iconComponent = computed(() => HeroIcons[props.icon] || HeroIcons.CubeIcon)
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  25% { transform: translateY(-3px) rotate(0.5deg); }
  50% { transform: translateY(-6px) rotate(0deg); }
  75% { transform: translateY(-3px) rotate(-0.5deg); }
}

@keyframes float-particle-1 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.5; }
  25% { transform: translate(8px, -8px); opacity: 0.8; }
  50% { transform: translate(15px, 3px); opacity: 0.3; }
  75% { transform: translate(3px, 12px); opacity: 0.6; }
}

@keyframes float-particle-2 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.6; }
  33% { transform: translate(-12px, 8px); opacity: 0.8; }
  66% { transform: translate(8px, -4px); opacity: 0.4; }
}

@keyframes float-particle-3 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.4; }
  40% { transform: translate(12px, -12px); opacity: 0.7; }
  80% { transform: translate(-8px, 8px); opacity: 0.2; }
}

@keyframes float-particle-4 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.7; }
  30% { transform: translate(-10px, -6px); opacity: 0.9; }
  70% { transform: translate(6px, 10px); opacity: 0.3; }
}

@keyframes shimmer-text {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes shimmer-loading {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

@keyframes progress-bar {
  0% { width: 0%; }
  50% { width: 70%; }
  100% { width: 100%; }
}

@keyframes gradient-shift {
  0%, 100% { stop-color: #8b5cf6; }
  25% { stop-color: #06b6d4; }
  50% { stop-color: #a855f7; }
  75% { stop-color: #8b5cf6; }
}

.animate-float {
  animation: float 8s ease-in-out infinite;
}

.animate-float-particle-1 {
  animation: float-particle-1 6s ease-in-out infinite;
}

.animate-float-particle-2 {
  animation: float-particle-2 8s ease-in-out infinite 1s;
}

.animate-float-particle-3 {
  animation: float-particle-3 10s ease-in-out infinite 2s;
}

.animate-float-particle-4 {
  animation: float-particle-4 7s ease-in-out infinite 3s;
}

.animate-shimmer-text {
  background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  background-size: 200% 100%;
  animation: shimmer-text 4s ease-in-out infinite;
}

.animate-shimmer-loading {
  animation: shimmer-loading 2s ease-in-out infinite;
}

.animate-progress-bar {
  animation: progress-bar 2s ease-in-out infinite;
}

.animate-gradient-shift {
  animation: gradient-shift 8s ease-in-out infinite;
}
</style>