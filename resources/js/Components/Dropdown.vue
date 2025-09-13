<template>
  <div class="relative">
    <div @click="open = !open">
      <slot name="trigger" />
    </div>

    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-show="open"
        class="absolute z-50 mt-2 rounded-md shadow-lg"
        :class="[widthClass, alignmentClasses]"
        style="display: none;"
        @click="open = false"
      >
        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
          <slot name="content" />
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  align: {
    type: String,
    default: 'right',
  },
  width: {
    type: String,
    default: '48',
  },
})

const open = ref(false)

const widthClass = computed(() => {
  return {
    '48': 'w-48',
  }[props.width.toString()]
})

const alignmentClasses = computed(() => {
  if (props.align === 'left') {
    return 'origin-top-left left-0'
  } else if (props.align === 'right') {
    return 'origin-top-right right-0'
  } else {
    return 'origin-top'
  }
})

const closeOnEscape = (e) => {
  if (open.value && e.key === 'Escape') {
    open.value = false
  }
}

onMounted(() => document.addEventListener('keydown', closeOnEscape))
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape))
</script>
/*<template>
  <div class="relative">
    <div @click="open = !open">
      <slot name="trigger" />
    </div>

    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="transform opacity-0 scale-95 translate-y-2"
      enter-to-class="transform opacity-100 scale-100 translate-y-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="transform opacity-100 scale-100 translate-y-0"
      leave-to-class="transform opacity-0 scale-95 translate-y-2"
    >
      <div
        v-show="open"
        class="absolute z-50 mt-3 overflow-hidden group"
        :class="[widthClass, alignmentClasses]"
        style="display: none;"
        @click="open = false"
      >
        <!-- Animated Background Container -->
        <div class="relative bg-gradient-to-br from-slate-800/95 via-slate-700/95 to-slate-800/95 backdrop-blur-2xl rounded-2xl border border-white/10 shadow-2xl"
             style="box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.05);">
          
          <!-- Animated Mesh Background -->
          <div class="absolute inset-0 opacity-10 pointer-events-none rounded-2xl overflow-hidden">
            <svg class="w-full h-full animate-mesh-shift" viewBox="0 0 100 100" preserveAspectRatio="none">
              <defs>
                <linearGradient id="meshGradDropdown" x1="0%" y1="0%" x2="100%" y2="100%" gradientUnits="objectBoundingBox">
                  <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0.4">
                    <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
                  </stop>
                  <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:0.3">
                    <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
                  </stop>
                  <stop offset="100%" style="stop-color:#a855f7;stop-opacity:0.4">
                    <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#06b6d4;#a855f7" dur="8s" repeatCount="indefinite"/>
                  </stop>
                </linearGradient>
              </defs>
              <rect width="100%" height="100%" fill="url(#meshGradDropdown)"/>
              <path d="M0,20 Q50,0 100,20 L100,80 Q50,100 0,80 Z" fill="url(#meshGradDropdown)" opacity="0.3">
                <animateTransform attributeName="transform" type="translate" values="0,0; 3,2; 0,0" dur="12s" repeatCount="indefinite"/>
              </path>
            </svg>
          </div>

          <!-- Floating Particles -->
          <div class="absolute inset-0 overflow-hidden pointer-events-none rounded-2xl">
            <div v-for="i in 6" :key="i" 
                 class="absolute w-1 h-1 rounded-full animate-float-particle opacity-60"
                 :style="{
                   left: Math.random() * 100 + '%',
                   top: Math.random() * 100 + '%',
                   background: ['#8b5cf6', '#06b6d4', '#a855f7'][Math.floor(Math.random() * 3)],
                   animationDelay: Math.random() * 8 + 's',
                   animationDuration: (6 + Math.random() * 4) + 's'
                 }">
            </div>
          </div>

          <!-- Enhanced Border Glow -->
          <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 blur-sm"></div>
          </div>

          <!-- Content Container -->
          <div class="relative z-10 py-2">
            <slot name="content" />
          </div>

          <!-- Animated Corner Accents -->
          <div class="absolute top-2 left-2 w-4 h-4 border-l-2 border-t-2 border-violet-400/30 rounded-tl-lg opacity-0 group-hover:opacity-100 transition-all duration-300 animate-corner-glow"></div>
          <div class="absolute top-2 right-2 w-4 h-4 border-r-2 border-t-2 border-cyan-400/30 rounded-tr-lg opacity-0 group-hover:opacity-100 transition-all duration-300 animate-corner-glow-delayed"></div>
          <div class="absolute bottom-2 left-2 w-4 h-4 border-l-2 border-b-2 border-purple-400/30 rounded-bl-lg opacity-0 group-hover:opacity-100 transition-all duration-300 animate-corner-glow"></div>
          <div class="absolute bottom-2 right-2 w-4 h-4 border-r-2 border-b-2 border-violet-400/30 rounded-br-lg opacity-0 group-hover:opacity-100 transition-all duration-300 animate-corner-glow-delayed"></div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  align: {
    type: String,
    default: 'right',
  },
  width: {
    type: String,
    default: '48',
  },
})

const open = ref(false)

const widthClass = computed(() => {
  return {
    '48': 'w-48',
  }[props.width.toString()]
})

const alignmentClasses = computed(() => {
  if (props.align === 'left') {
    return 'origin-top-left left-0'
  } else if (props.align === 'right') {
    return 'origin-top-right right-0'
  } else {
    return 'origin-top'
  }
})

const closeOnEscape = (e) => {
  if (open.value && e.key === 'Escape') {
    open.value = false
  }
}

onMounted(() => document.addEventListener('keydown', closeOnEscape))
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape))
</script>

<style scoped>
@keyframes mesh-shift {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(15px, -15px) rotate(1deg); }
  66% { transform: translate(-10px, 10px) rotate(-1deg); }
}

@keyframes float-particle {
  0%, 100% { 
    transform: translateY(0px) rotate(0deg);
    opacity: 0.4;
  }
  50% { 
    transform: translateY(-15px) rotate(180deg);
    opacity: 0.8;
  }
}

@keyframes corner-glow {
  0%, 100% { 
    opacity: 0.3;
    transform: scale(1);
  }
  50% { 
    opacity: 0.7;
    transform: scale(1.1);
  }
}

@keyframes corner-glow-delayed {
  0%, 100% { 
    opacity: 0.3;
    transform: scale(1);
  }
  50% { 
    opacity: 0.7;
    transform: scale(1.1);
  }
}

@keyframes dropdown-entrance {
  0% {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.animate-mesh-shift {
  animation: mesh-shift 15s ease-in-out infinite;
}

.animate-float-particle {
  animation: float-particle 8s ease-in-out infinite;
}

.animate-corner-glow {
  animation: corner-glow 2s ease-in-out infinite;
}

.animate-corner-glow-delayed {
  animation: corner-glow-delayed 2s ease-in-out infinite;
  animation-delay: 0.5s;
}

/* Enhanced entrance animation */
.group {
  animation: dropdown-entrance 0.3s ease-out;
}

/* Hover effects */
.group:hover {
  transform: translateY(-1px);
}

/* Custom scrollbar for dropdown content if needed */
.group :deep(*) {
  scrollbar-width: thin;
  scrollbar-color: rgba(139, 92, 246, 0.3) transparent;
}

.group :deep(*::-webkit-scrollbar) {
  width: 6px;
}

.group :deep(*::-webkit-scrollbar-track) {
  background: transparent;
}

.group :deep(*::-webkit-scrollbar-thumb) {
  background: rgba(139, 92, 246, 0.3);
  border-radius: 3px;
}

.group :deep(*::-webkit-scrollbar-thumb:hover) {
  background: rgba(139, 92, 246, 0.5);
}
</style>