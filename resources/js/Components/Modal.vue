<template>
  <teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
      <!-- Backdrop -->
      <div 
        class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
        @click="$emit('close')"
      ></div>
      
      <!-- Modal -->
      <div class="flex min-h-full items-center justify-center p-4">
        <div 
          class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 transform transition-all"
          @click.stop
        >
          <slot />
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
defineProps({
  show: { type: Boolean, default: false },
})

defineEmits(['close'])
</script>
/*<template>
  <teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
      <!-- Enhanced Backdrop with animations -->
      <div 
        class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-all duration-500 ease-out animate-fade-in"
        @click="$emit('close')"
      >
        <!-- Animated mesh background -->
        <div class="absolute inset-0 opacity-30 overflow-hidden">
          <svg class="w-full h-full animate-mesh-float" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#8b5cf6" class="animate-gradient-shift">
                  <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
                </stop>
                <stop offset="100%" stop-color="#06b6d4" class="animate-gradient-shift">
                  <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
                </stop>
              </linearGradient>
            </defs>
            <path d="M0,100 Q100,50 200,100 T400,100 L400,400 L0,400 Z" fill="url(#meshGradient)" opacity="0.2">
              <animateTransform attributeName="transform" type="translate" values="0,0;30,-15;0,0" dur="10s" repeatCount="indefinite"/>
            </path>
            <path d="M0,200 Q150,150 300,200 T600,200 L400,400 L0,400 Z" fill="url(#meshGradient)" opacity="0.1">
              <animateTransform attributeName="transform" type="translate" values="0,0;-20,10;0,0" dur="12s" repeatCount="indefinite"/>
            </path>
          </svg>
        </div>

        <!-- Floating particles system -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
          <div v-for="i in 20" :key="i" 
               :class="`absolute rounded-full opacity-40 animate-float-particle-${i % 5}`"
               :style="`
                 left: ${(i * 5) % 100}%; 
                 top: ${(i * 7) % 100}%; 
                 width: ${2 + (i % 3)}px; 
                 height: ${2 + (i % 3)}px;
                 background: linear-gradient(45deg, 
                   ${i % 3 === 0 ? '#8b5cf6' : i % 3 === 1 ? '#06b6d4' : '#a855f7'}, 
                   ${i % 3 === 0 ? '#06b6d4' : i % 3 === 1 ? '#a855f7' : '#8b5cf6'}
                 );
                 animation-delay: ${i * 0.3}s;
               `">
          </div>
        </div>
      </div>
      
      <!-- Modal Container -->
      <div class="flex min-h-full items-center justify-center p-4 animate-fade-in-up">
        <div 
          class="relative bg-slate-900/95 backdrop-blur-xl border border-violet-500/20 rounded-2xl shadow-2xl shadow-violet-500/10 max-w-2xl w-full mx-4 transform transition-all duration-500 ease-out animate-modal-entrance overflow-hidden"
          @click.stop
        >
          <!-- Glassmorphism overlay -->
          <div class="absolute inset-0 bg-gradient-to-br from-slate-800/50 via-slate-900/50 to-slate-800/50 backdrop-blur-sm rounded-2xl"></div>
          
          <!-- Animated border gradient -->
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 p-[1px] animate-border-gradient">
            <div class="w-full h-full bg-slate-900/95 backdrop-blur-xl rounded-2xl"></div>
          </div>

          <!-- Corner accent elements -->
          <div class="absolute top-2 left-2 w-3 h-3 opacity-60">
            <div class="absolute top-0 left-0 w-1 h-1 bg-violet-400 rounded-full animate-pulse"></div>
            <div class="absolute top-1 left-1 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
          </div>
          <div class="absolute top-2 right-2 w-3 h-3 opacity-60">
            <div class="absolute top-0 right-0 w-1 h-1 bg-purple-400 rounded-full animate-pulse" style="animation-delay: 0.25s;"></div>
            <div class="absolute top-1 right-1 w-0.5 h-0.5 bg-violet-400 rounded-full animate-pulse" style="animation-delay: 0.75s;"></div>
          </div>

          <!-- Shimmer effect overlay -->
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full animate-shimmer-modal rounded-2xl pointer-events-none"></div>

          <!-- Content wrapper with enhanced styling -->
          <div class="relative z-10 text-slate-300">
            <!-- Inner glow effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 rounded-2xl animate-inner-glow"></div>
            
            <!-- Main content slot -->
            <div class="relative z-20">
              <slot />
            </div>
          </div>

          <!-- Bottom accent bar -->
          <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-violet-500/30 via-purple-500/30 to-cyan-500/30 animate-pulse-bar"></div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
defineProps({
  show: { type: Boolean, default: false },
})

defineEmits(['close'])
</script>

<style scoped>
/* Fade in animation for backdrop */
@keyframes fade-in {
  0% { opacity: 0; }
  100% { opacity: 1; }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out forwards;
}

/* Fade in up animation for modal */
@keyframes fade-in-up {
  0% { 
    opacity: 0; 
    transform: translateY(20px) scale(0.95);
  }
  100% { 
    opacity: 1; 
    transform: translateY(0) scale(1);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.4s ease-out forwards;
}

/* Modal entrance animation */
@keyframes modal-entrance {
  0% { 
    opacity: 0; 
    transform: scale(0.8) rotateX(10deg);
    filter: blur(10px);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.02) rotateX(-2deg);
    filter: blur(2px);
  }
  100% { 
    opacity: 1; 
    transform: scale(1) rotateX(0deg);
    filter: blur(0px);
  }
}

.animate-modal-entrance {
  animation: modal-entrance 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

/* Mesh floating animation */
@keyframes mesh-float {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(20px, -10px) rotate(1deg); }
  66% { transform: translate(-15px, 15px) rotate(-1deg); }
}

.animate-mesh-float {
  animation: mesh-float 20s ease-in-out infinite;
}

/* Floating particles */
@keyframes float-particle-0 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  50% { transform: translateY(-30px) translateX(15px) rotate(180deg); opacity: 0.8; }
}

@keyframes float-particle-1 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.4; }
  50% { transform: translateY(-25px) translateX(-12px) rotate(-180deg); opacity: 0.7; }
}

@keyframes float-particle-2 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.5; }
  50% { transform: translateY(-35px) translateX(8px) rotate(270deg); opacity: 0.9; }
}

@keyframes float-particle-3 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  50% { transform: translateY(-20px) translateX(-18px) rotate(-270deg); opacity: 0.6; }
}

@keyframes float-particle-4 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.4; }
  50% { transform: translateY(-40px) translateX(5px) rotate(90deg); opacity: 0.8; }
}

.animate-float-particle-0 { animation: float-particle-0 6s ease-in-out infinite; }
.animate-float-particle-1 { animation: float-particle-1 7s ease-in-out infinite; }
.animate-float-particle-2 { animation: float-particle-2 8s ease-in-out infinite; }
.animate-float-particle-3 { animation: float-particle-3 9s ease-in-out infinite; }
.animate-float-particle-4 { animation: float-particle-4 5s ease-in-out infinite; }

/* Border gradient animation */
@keyframes border-gradient {
  0%, 100% { 
    background: linear-gradient(45deg, rgba(139, 92, 246, 0.2), rgba(168, 85, 247, 0.2), rgba(6, 182, 212, 0.2));
  }
  33% { 
    background: linear-gradient(45deg, rgba(168, 85, 247, 0.3), rgba(6, 182, 212, 0.3), rgba(139, 92, 246, 0.3));
  }
  66% { 
    background: linear-gradient(45deg, rgba(6, 182, 212, 0.25), rgba(139, 92, 246, 0.25), rgba(168, 85, 247, 0.25));
  }
}

.animate-border-gradient {
  animation: border-gradient 4s ease-in-out infinite;
}

/* Shimmer effect for modal */
@keyframes shimmer-modal {
  0% { transform: translateX(-100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateX(100%); opacity: 0; }
}

.animate-shimmer-modal {
  animation: shimmer-modal 3s ease-in-out infinite;
  animation-delay: 2s;
}

/* Inner glow animation */
@keyframes inner-glow {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 0.6; }
}

.animate-inner-glow {
  animation: inner-glow 4s ease-in-out infinite;
}

/* Pulse bar animation */
@keyframes pulse-bar {
  0%, 100% { 
    opacity: 0.3; 
    transform: scaleX(1);
  }
  50% { 
    opacity: 0.7; 
    transform: scaleX(1.02);
  }
}

.animate-pulse-bar {
  animation: pulse-bar 3s ease-in-out infinite;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .animate-modal-entrance {
    animation-duration: 0.4s;
  }
  
  .animate-fade-in-up {
    animation-duration: 0.3s;
  }
  
  .max-w-2xl {
    max-width: calc(100vw - 2rem);
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .animate-modal-entrance,
  .animate-fade-in-up,
  .animate-fade-in,
  .animate-mesh-float,
  .animate-float-particle-0,
  .animate-float-particle-1,
  .animate-float-particle-2,
  .animate-float-particle-3,
  .animate-float-particle-4,
  .animate-border-gradient,
  .animate-shimmer-modal,
  .animate-inner-glow,
  .animate-pulse-bar {
    animation: none !important;
    transition-duration: 0.01ms !important;
  }
  
  .backdrop-blur-xl,
  .backdrop-blur-sm {
    backdrop-filter: none !important;
  }
}

/* High contrast mode */
@media (prefers-contrast: high) {
  .bg-slate-900\/95 {
    background-color: rgb(0 0 0 / 0.98) !important;
  }
  
  .border-violet-500\/20 {
    border-color: rgb(139 92 246 / 0.8) !important;
    border-width: 2px !important;
  }
  
  .text-slate-300 {
    color: white !important;
  }
}

/* Focus trap for accessibility */
.modal-content:focus {
  outline: 2px solid rgba(139, 92, 246, 0.5);
  outline-offset: -2px;
}
</style>