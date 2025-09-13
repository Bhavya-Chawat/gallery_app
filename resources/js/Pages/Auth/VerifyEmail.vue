
<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  status: {
    type: String,
  },
});

const form = useForm({});

const submit = () => {
  form.post('/email/verification-notification');
};

const verificationLinkSent = computed(
  () => props.status === 'verification-link-sent',
);
</script>

<template>
  <GuestLayout>
    <Head title="Email Verification" />

    <!-- Premium Animated Background System -->
    <div class="fixed inset-0 overflow-hidden -z-10">
      <!-- Enhanced Base Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        
        <!-- Advanced Animated Mesh Background -->
        <svg class="absolute inset-0 w-full h-full opacity-25" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <pattern id="mesh-pattern-verify" x="0" y="0" width="25" height="25" patternUnits="userSpaceOnUse">
              <circle cx="12.5" cy="12.5" r="2.5" fill="currentColor" class="text-violet-500/30">
                <animate attributeName="r" values="2.5;4.5;2.5" dur="5s" repeatCount="indefinite"/>
              </circle>
              <circle cx="6" cy="6" r="1.8" fill="currentColor" class="text-cyan-500/25">
                <animate attributeName="r" values="1.8;3.2;1.8" dur="7s" repeatCount="indefinite"/>
              </circle>
            </pattern>
            <linearGradient id="mesh-gradient-verify" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" style="stop-color:#8b5cf6">
                <animate attributeName="stop-color" 
                  values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" 
                  dur="8s" 
                  repeatCount="indefinite" />
              </stop>
              <stop offset="50%" style="stop-color:#a855f7">
                <animate attributeName="stop-color" 
                  values="#a855f7;#8b5cf6;#06b6d4;#a855f7" 
                  dur="8s" 
                  repeatCount="indefinite" />
              </stop>
              <stop offset="100%" style="stop-color:#06b6d4">
                <animate attributeName="stop-color" 
                  values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" 
                  dur="8s" 
                  repeatCount="indefinite" />
              </stop>
            </linearGradient>
          </defs>
          
          <rect width="100" height="100" fill="url(#mesh-pattern-verify)"/>
          <path d="M0,10 L100,40 L100,100 L0,80 Z" fill="url(#mesh-gradient-verify)" opacity="0.12">
            <animateTransform 
              attributeName="transform" 
              type="translate" 
              values="0,0;12,-6;0,0" 
              dur="11s" 
              repeatCount="indefinite" />
          </path>
          <path d="M0,100 L100,70 L100,0 L0,30 Z" fill="url(#mesh-gradient-verify)" opacity="0.08">
            <animateTransform 
              attributeName="transform" 
              type="translate" 
              values="0,0;-8,4;0,0" 
              dur="9s" 
              repeatCount="indefinite" />
          </path>
        </svg>
        
        <!-- Enhanced Floating Orbs -->
        <div class="absolute top-16 left-12 w-64 h-64 bg-gradient-to-r from-violet-500/20 to-purple-500/20 rounded-full blur-3xl animate-pulse-slow opacity-70"></div>
        <div class="absolute top-32 right-20 w-80 h-80 bg-gradient-to-l from-cyan-500/15 to-violet-500/15 rounded-full blur-3xl animate-pulse-slow delay-1000 opacity-60"></div>
        <div class="absolute bottom-24 left-1/3 w-56 h-56 bg-gradient-to-t from-purple-500/12 to-cyan-500/12 rounded-full blur-3xl animate-pulse-slow delay-2000 opacity-50"></div>
        
        <!-- 20-Particle Float System -->
        <div class="absolute inset-0">
          <div v-for="i in 20" :key="`particle-${i}`" 
               :class="[
                 'absolute w-2.5 h-2.5 rounded-full opacity-60',
                 i % 4 === 0 ? 'bg-gradient-to-r from-violet-400/35 to-purple-400/35' :
                 i % 4 === 1 ? 'bg-gradient-to-r from-cyan-400/35 to-violet-400/35' :
                 i % 4 === 2 ? 'bg-gradient-to-r from-purple-400/35 to-cyan-400/35' :
                 'bg-gradient-to-r from-violet-400/35 to-cyan-400/35',
                 'animate-float-organic'
               ]"
               :style="{
                 left: Math.random() * 100 + '%',
                 top: Math.random() * 100 + '%',
                 animationDelay: Math.random() * 8 + 's',
                 animationDuration: (5 + Math.random() * 5) + 's'
               }">
            <div class="w-full h-full rounded-full animate-ping opacity-40"></div>
          </div>
        </div>

        <!-- Gradient Overlay Layers -->
        <div class="absolute inset-0 bg-gradient-to-br from-violet-900/25 via-transparent to-cyan-900/25"></div>
        <div class="absolute inset-0 bg-gradient-to-tl from-purple-900/15 via-transparent to-violet-900/15"></div>
      </div>
    </div>

    <!-- Main Content Container -->
    <div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-lg w-full animate-fade-in-up-enhanced">
        
        <!-- Premium Multi-Layer Glassmorphism Card -->
        <div class="relative">
          <!-- Background glow effect -->
          <div class="absolute -inset-1 bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 rounded-3xl blur-xl opacity-70 animate-pulse-glow"></div>
          
          <!-- Main card -->
          <div class="relative bg-white/8 backdrop-blur-2xl border border-white/20 rounded-3xl p-10 shadow-2xl 
                      hover:bg-white/12 hover:border-white/30 transition-all duration-700 
                      hover:shadow-violet-500/20 hover:shadow-3xl transform hover:scale-[1.01]
                      before:absolute before:inset-0 before:bg-gradient-to-br before:from-white/5 before:to-transparent before:rounded-3xl">
            
            <!-- Header Section with Enhanced Icon -->
            <div class="text-center mb-10 space-y-6">
              <!-- Enhanced Email Icon -->
              <div class="flex justify-center mb-8">
                <div class="relative">
                  <!-- Background glow -->
                  <div class="absolute -inset-3 bg-gradient-to-r from-violet-500/40 to-cyan-500/40 rounded-2xl blur-lg animate-pulse"></div>
                  
                  <!-- Main icon container -->
                  <div class="relative w-20 h-20 bg-gradient-to-br from-violet-500 via-purple-500 to-cyan-500 rounded-2xl 
                              flex items-center justify-center transform hover:scale-110 transition-all duration-500
                              shadow-lg shadow-violet-500/30 hover:shadow-2xl hover:shadow-violet-500/50">
                    <svg class="h-10 w-10 text-white filter drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Enhanced Title -->
              <h2 class="text-4xl font-black leading-tight">
                <span class="bg-gradient-to-r from-violet-300 via-white to-cyan-300 bg-clip-text text-transparent 
                             animate-shimmer-enhanced bg-size-300 bg-pos-0 filter drop-shadow-sm">
                  Verify Your Email
                </span>
              </h2>
            </div>

            <!-- Enhanced Main Message -->
            <div class="mb-8 space-y-4 animate-slide-in-up" style="animation-delay: 0.2s">
              <div class="p-6 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-sm
                          hover:bg-white/8 hover:border-white/15 transition-all duration-500">
                <div class="text-slate-200 text-base leading-relaxed">
                  Thanks for signing up! Before getting started, could you verify your
                  email address by clicking on the link we just emailed to you? If you
                  didn't receive the email, we will gladly send you another.
                </div>
              </div>
            </div>

            <!-- Enhanced Success Message -->
            <div v-if="verificationLinkSent" class="mb-8 animate-slide-in-down">
              <div class="p-5 bg-emerald-500/15 border border-emerald-400/30 rounded-2xl backdrop-blur-sm
                          shadow-lg shadow-emerald-500/10 hover:bg-emerald-500/20 transition-all duration-500">
                <div class="flex items-center gap-3">
                  <svg class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <p class="text-emerald-300 font-medium text-base">
                    A new verification link has been sent to the email address you
                    provided during registration.
                  </p>
                </div>
              </div>
            </div>

            <!-- Enhanced Form -->
            <form @submit.prevent="submit" class="animate-slide-in-up" style="animation-delay: 0.4s">
              <div class="mt-8 flex items-center justify-between gap-6">
                
                <!-- Enhanced Primary Button -->
                <div class="relative group flex-1">
                  <!-- Button glow effect -->
                  <div class="absolute -inset-1 bg-gradient-to-r from-violet-500/50 via-purple-500/50 to-cyan-500/50 
                              rounded-2xl blur-lg opacity-60 group-hover:opacity-100 transition-all duration-500"></div>
                  
                  <PrimaryButton
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                    class="relative w-full flex justify-center items-center py-4 px-6 
                           text-base font-bold rounded-xl text-white 
                           bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 
                           hover:from-violet-700 hover:via-purple-700 hover:to-cyan-700 
                           focus:outline-none focus:ring-4 focus:ring-violet-500/30 focus:ring-offset-0 
                           disabled:cursor-not-allowed
                           transition-all duration-500 hover:scale-[1.05] 
                           shadow-xl hover:shadow-2xl hover:shadow-violet-500/30
                           transform active:scale-[0.98]
                           border border-white/10 hover:border-white/20"
                  >
                    <span v-if="form.processing" class="flex items-center gap-3">
                      <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <span>Sending...</span>
                    </span>
                    <span v-else class="flex items-center gap-3">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                      </svg>
                      <span>Resend Verification Email</span>
                    </span>
                  </PrimaryButton>
                </div>

                <!-- Enhanced Log Out Link -->
                <Link
                  href="/logout"
                  method="post"
                  as="button"
                  class="group relative px-6 py-4 rounded-xl text-base font-semibold text-slate-300 
                         hover:text-white transition-all duration-300 
                         hover:bg-white/5 hover:scale-105 transform
                         border border-white/10 hover:border-white/20
                         focus:outline-none focus:ring-4 focus:ring-slate-500/20 focus:ring-offset-0
                         backdrop-blur-sm"
                >
                  <span class="flex items-center gap-2">
                    <svg class="h-5 w-5 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Log Out</span>
                  </span>
                </Link>
              </div>
            </form>

            <!-- Enhanced Footer -->
            <div class="mt-10 text-center space-y-4 animate-slide-in-up" style="animation-delay: 0.6s">
              <div class="w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
              <p class="text-sm text-slate-400 leading-relaxed">
                Having trouble? Check your spam folder or contact our 
                <span class="bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent font-semibold">
                  support team
                </span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

<style scoped>
/* Enhanced Premium Animations */
@keyframes float-organic {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
    opacity: 0.4;
  }
  25% {
    transform: translateY(-18px) translateX(8px) rotate(90deg);
    opacity: 0.8;
  }
  50% {
    transform: translateY(-8px) translateX(-12px) rotate(180deg);
    opacity: 1;
  }
  75% {
    transform: translateY(-22px) translateX(4px) rotate(270deg);
    opacity: 0.6;
  }
}

@keyframes shimmer-enhanced {
  0% { background-position: -300% 0; }
  50% { background-position: 0% 0; }
  100% { background-position: 300% 0; }
}

@keyframes fade-in-up-enhanced {
  0% {
    opacity: 0;
    transform: translateY(40px) scale(0.95);
    filter: blur(10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0px);
  }
}

@keyframes slide-in-up {
  0% {
    opacity: 0;
    transform: translateY(30px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-in-down {
  0% {
    opacity: 0;
    transform: translateY(-20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse-slow {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.05); }
}

@keyframes pulse-glow {
  0%, 100% { opacity: 0.5; }
  50% { opacity: 1; }
}

/* Animation Classes */
.animate-float-organic {
  animation: float-organic 8s ease-in-out infinite;
}

.animate-shimmer-enhanced {
  animation: shimmer-enhanced 4s ease-in-out infinite;
}

.animate-fade-in-up-enhanced {
  animation: fade-in-up-enhanced 1s ease-out 0s 1 both;
}

.animate-slide-in-up {
  animation: slide-in-up 0.8s ease-out 0s 1 both;
}

.animate-slide-in-down {
  animation: slide-in-down 0.6s ease-out 0s 1 both;
}

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.animate-pulse-glow {
  animation: pulse-glow 3s ease-in-out infinite;
}

/* Background Sizing */
.bg-size-300 {
  background-size: 300% 100%;
}

.bg-pos-0 {
  background-position: 0% 50%;
}

/* Enhanced Focus States */
.focus\:ring-violet-500\/30:focus {
  box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.3);
}

.focus\:ring-slate-500\/20:focus {
  box-shadow: 0 0 0 4px rgba(100, 116, 139, 0.2);
}

/* Enhanced Hover Effects */
.hover\:shadow-3xl:hover {
  box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
}

.hover\:shadow-violet-500\/20:hover {
  box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.2);
}

.hover\:shadow-violet-500\/30:hover {
  box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.3);
}

.hover\:shadow-violet-500\/50:hover {
  box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.5);
}

/* Backdrop Blur Enhancement */
.backdrop-blur-2xl {
  backdrop-filter: blur(40px);
  -webkit-backdrop-filter: blur(40px);
}

/* Custom Scroll Styling */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: rgba(30, 41, 59, 0.1);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(45deg, #8b5cf6, #06b6d4);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(45deg, #7c3aed, #0891b2);
}

/* Button Enhancement */
button:not(:disabled):active {
  transform: scale(0.98);
}

/* Link Enhancement */
a:hover {
  text-decoration: none;
}

/* Icon Rotation */
.group:hover .group-hover\:rotate-12 {
  transform: rotate(12deg);
}
</style>