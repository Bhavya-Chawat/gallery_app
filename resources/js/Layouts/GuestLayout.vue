<template>
  <div class="min-h-screen bg-slate-900 relative overflow-hidden">
    <!-- Animated Mesh Background -->
    <div class="absolute inset-0 opacity-30">
      <svg class="w-full h-full animate-mesh-float" viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="meshGradient1" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(45)">
            <stop offset="0%" class="animate-gradient-shift-1">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift-2">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
          <linearGradient id="meshGradient2" x1="100%" y1="0%" x2="0%" y2="100%" gradientTransform="rotate(-45)">
            <stop offset="0%" class="animate-gradient-shift-3">
              <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#06b6d4;#a855f7" dur="10s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift-4">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="10s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        
        <!-- Animated mesh patterns -->
        <path d="M0,150 Q200,100 400,150 T800,150 L800,0 L0,0 Z" fill="url(#meshGradient1)" opacity="0.4">
          <animateTransform attributeName="transform" type="translate" values="0,0;100,30;0,0" dur="15s" repeatCount="indefinite"/>
        </path>
        <path d="M0,300 Q300,250 600,300 T1200,300 L1200,150 L0,150 Z" fill="url(#meshGradient2)" opacity="0.3">
          <animateTransform attributeName="transform" type="translate" values="0,0;-80,20;0,0" dur="12s" repeatCount="indefinite"/>
        </path>
        <path d="M0,450 Q150,400 300,450 T600,450 L600,300 L0,300 Z" fill="url(#meshGradient1)" opacity="0.2">
          <animateTransform attributeName="transform" type="translate" values="0,0;60,-10;0,0" dur="18s" repeatCount="indefinite"/>
        </path>
      </svg>
    </div>

    <!-- Floating Particles System -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <div 
        v-for="i in 20" 
        :key="`particle-${i}`"
        :class="[
          'absolute rounded-full opacity-60',
          i <= 8 ? 'w-1 h-1' : i <= 16 ? 'w-2 h-2' : 'w-0.5 h-0.5',
          'bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400',
          `animate-float-particle-${(i % 5) + 1}`
        ]"
        :style="{
          left: `${Math.random() * 100}%`,
          top: `${Math.random() * 100}%`,
          animationDelay: `${Math.random() * 12}s`,
          animationDuration: `${10 + Math.random() * 6}s`
        }"
      ></div>
    </div>

    <!-- Simple navigation -->
    <nav class="relative z-50 bg-slate-900/80 backdrop-blur-xl shadow-2xl border-b border-white/10">
      <div class="absolute inset-0 bg-gradient-to-r from-violet-600/10 via-purple-600/5 to-cyan-600/10"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex justify-between h-16">
          <div class="flex items-center group">
            <Link :href="route('welcome')" class="text-xl font-bold bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent hover:scale-110 transition-all duration-500 animate-shimmer-text hover:drop-shadow-lg">
              {{ $page.props.app?.name || 'Gallery' }}
            </Link>
          </div>
          
          <div class="flex items-center space-x-4">
            <Link :href="route('gallery.index')" class="relative text-slate-300 hover:text-white transition-all duration-300 hover:scale-105 hover:-translate-y-1 group">
              <span class="relative z-10">Browse Gallery</span>
              <div class="absolute inset-0 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10 blur-sm"></div>
            </Link>
            <template v-if="$page.props.auth?.user">
              <Link :href="route('dashboard')" class="relative text-slate-300 hover:text-white transition-all duration-300 hover:scale-105 hover:-translate-y-1 group">
                <span class="relative z-10">Dashboard</span>
                <div class="absolute inset-0 bg-gradient-to-r from-violet-500/20 to-purple-500/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10 blur-sm"></div>
              </Link>
              <Link v-if="$page.props.auth.user.roles?.some(r => r.slug === 'admin')" :href="route('admin.system.index')" class="relative text-slate-300 hover:text-white transition-all duration-300 hover:scale-105 hover:-translate-y-1 group">
                <span class="relative z-10">Admin</span>
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-cyan-500/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10 blur-sm"></div>
              </Link>
            </template>
            <template v-else>
              <Link
                v-if="$page.props.can?.login"
                :href="route('login')"
                class="text-sm text-slate-300 hover:text-white underline decoration-violet-500/50 hover:decoration-violet-400 underline-offset-4 transition-all duration-300 hover:scale-105"
              >
                Log in
              </Link>
              <Link
                v-if="$page.props.can?.register"
                :href="route('register')"
                class="relative px-4 py-2 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 text-white text-sm rounded-xl hover:from-violet-500 hover:via-purple-500 hover:to-cyan-500 transition-all duration-500 hover:scale-110 hover:shadow-2xl hover:shadow-violet-500/25 group overflow-hidden"
              >
                <span class="relative z-10 font-medium">Register</span>
                <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 via-purple-400/20 to-cyan-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="absolute -inset-1 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 rounded-xl blur opacity-30 group-hover:opacity-70 transition-opacity duration-300 -z-10"></div>
              </Link>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main class="relative z-30 animate-fade-in-up" style="animation-delay: 0.2s;">
      <slot />
    </main>

    <!-- Footer -->
    <div class="relative z-30 animate-fade-in-up" style="animation-delay: 0.4s;">
      <Footer />
    </div>
  </div>
</template>

<style scoped>
/* Ensure no margins */
body {
  margin: 0;
  padding: 0;
}

@keyframes shimmer-text {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

@keyframes mesh-float {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
  }
  33% { 
    transform: translateY(-30px) translateX(20px) rotate(1deg); 
  }
  66% { 
    transform: translateY(-15px) translateX(-10px) rotate(-0.5deg); 
  }
}

@keyframes fade-in-up {
  0% { 
    opacity: 0; 
    transform: translateY(40px); 
  }
  100% { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

@keyframes float-particle-1 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.6; 
  }
  25% { 
    transform: translateY(-120px) translateX(60px) rotate(90deg); 
    opacity: 1; 
  }
  50% { 
    transform: translateY(-80px) translateX(-40px) rotate(180deg); 
    opacity: 0.8; 
  }
  75% { 
    transform: translateY(-160px) translateX(20px) rotate(270deg); 
    opacity: 0.9; 
  }
}

@keyframes float-particle-2 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.5; 
  }
  25% { 
    transform: translateY(-100px) translateX(-50px) rotate(120deg); 
    opacity: 0.9; 
  }
  50% { 
    transform: translateY(-140px) translateX(30px) rotate(240deg); 
    opacity: 0.7; 
  }
  75% { 
    transform: translateY(-70px) translateX(-20px) rotate(360deg); 
    opacity: 0.8; 
  }
}

@keyframes float-particle-3 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.7; 
  }
  33% { 
    transform: translateY(-90px) translateX(80px) rotate(150deg); 
    opacity: 0.4; 
  }
  66% { 
    transform: translateY(-180px) translateX(-30px) rotate(300deg); 
    opacity: 1; 
  }
}

@keyframes float-particle-4 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.4; 
  }
  50% { 
    transform: translateY(-130px) translateX(-60px) rotate(180deg); 
    opacity: 0.8; 
  }
}

@keyframes float-particle-5 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.6; 
  }
  20% { 
    transform: translateY(-60px) translateX(40px) rotate(72deg); 
    opacity: 0.9; 
  }
  40% { 
    transform: translateY(-120px) translateX(-20px) rotate(144deg); 
    opacity: 0.5; 
  }
  60% { 
    transform: translateY(-90px) translateX(70px) rotate(216deg); 
    opacity: 0.8; 
  }
  80% { 
    transform: translateY(-150px) translateX(-10px) rotate(288deg); 
    opacity: 0.7; 
  }
}

.animate-shimmer-text {
  background-size: 200% auto;
  animation: shimmer-text 4s linear infinite;
}

.animate-mesh-float {
  animation: mesh-float 20s ease-in-out infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 1s ease-out forwards;
}

.animate-float-particle-1 { animation: float-particle-1 12s ease-in-out infinite; }
.animate-float-particle-2 { animation: float-particle-2 15s ease-in-out infinite; }
.animate-float-particle-3 { animation: float-particle-3 10s ease-in-out infinite; }
.animate-float-particle-4 { animation: float-particle-4 14s ease-in-out infinite; }
.animate-float-particle-5 { animation: float-particle-5 16s ease-in-out infinite; }

/* Enhanced hover effects */
.nav-link-enhanced {
  position: relative;
  overflow: hidden;
}

.nav-link-enhanced::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(6, 182, 212, 0.1));
  border-radius: 0.5rem;
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: -1;
}

.nav-link-enhanced:hover::before {
  opacity: 1;
}
</style>

<script setup>
import { Link } from '@inertiajs/vue3'
import route from 'ziggy-js'
import Footer from '@/Components/Footer.vue'
</script>