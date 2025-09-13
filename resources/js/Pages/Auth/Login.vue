<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import route from 'ziggy-js';

// Props expected by Breeze
defineProps({
    canResetPassword: Boolean,
    status: String,
});

// Form setup
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// Submit handler
const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
  <GuestLayout>
    <Head title="Log in" />

    <!-- Enhanced Animated Background -->
    <div class="fixed inset-0 overflow-hidden">
      <!-- Base Background -->
      <div class="absolute inset-0 bg-slate-900">
        <!-- Animated Mesh Background -->
        <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <linearGradient id="mesh-gradient-login" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" class="text-violet-500">
                <animate attributeName="stop-color" 
                  values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" 
                  dur="8s" 
                  repeatCount="indefinite" />
              </stop>
              <stop offset="100%" class="text-cyan-500">
                <animate attributeName="stop-color" 
                  values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" 
                  dur="8s" 
                  repeatCount="indefinite" />
              </stop>
            </linearGradient>
          </defs>
          <path d="M0,0 L100,20 L100,100 L0,80 Z" fill="url(#mesh-gradient-login)" opacity="0.1">
            <animateTransform 
              attributeName="transform" 
              type="translate" 
              values="0,0;10,-5;0,0" 
              dur="10s" 
              repeatCount="indefinite" />
          </path>
        </svg>
        
        <!-- Floating Orbs -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-gradient-to-r from-violet-500/20 to-purple-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-gradient-to-l from-cyan-500/15 to-violet-500/15 rounded-full blur-3xl animate-pulse delay-700"></div>
        <div class="absolute bottom-20 left-1/3 w-80 h-80 bg-gradient-to-t from-purple-500/10 to-cyan-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        
        <!-- Particle System -->
        <div class="absolute inset-0">
          <div v-for="i in 15" :key="i" 
               :class="`absolute w-2 h-2 bg-gradient-to-r from-violet-400/30 to-cyan-400/30 rounded-full animate-float`"
               :style="{
                 left: Math.random() * 100 + '%',
                 top: Math.random() * 100 + '%',
                 animationDelay: Math.random() * 5 + 's',
                 animationDuration: (3 + Math.random() * 4) + 's'
               }">
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full animate-fade-in-up">
        
        <!-- Premium Glassmorphism Card -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl hover:bg-white/10 transition-all duration-500">
          
          <!-- Header -->
          <div class="text-center mb-8">
            <!-- Logo/Brand -->
            <div class="flex justify-center mb-6">
              <div class="w-16 h-16 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-2xl flex items-center justify-center animate-pulse">
                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>

            <h2 class="text-3xl font-extrabold">
              <span class="bg-gradient-to-r from-violet-400 via-white to-cyan-400 bg-clip-text text-transparent animate-shimmer bg-size-200 bg-pos-0">
                Welcome Back
              </span>
            </h2>
            <p class="mt-3 text-slate-300">
              Sign in to your Visual Gallery account
            </p>
            <p class="mt-2 text-sm text-slate-400">
              Don't have an account?
              <Link :href="route('register')" class="font-semibold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300 ml-1">
                Create one here
              </Link>
            </p>
          </div>

          <!-- Status Message -->
          <div v-if="status" class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl">
            <p class="text-sm text-green-400 text-center">{{ status }}</p>
          </div>

          <!-- Login Form -->
          <form class="space-y-6" @submit.prevent="submit">
            
            <!-- Email Field -->
            <div class="space-y-2">
              <InputLabel for="email" value="Email Address" class="text-slate-300 font-medium" />
              <div class="relative">
                <TextInput
                  id="email"
                  type="email"
                  v-model="form.email"
                  class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300"
                  placeholder="Enter your email address"
                  required
                  autofocus
                  autocomplete="username"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                  </svg>
                </div>
              </div>
              <InputError class="text-red-400 text-sm" :message="form.errors.email" />
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
              <InputLabel for="password" value="Password" class="text-slate-300 font-medium" />
              <div class="relative">
                <TextInput
                  id="password"
                  type="password"
                  v-model="form.password"
                  class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300"
                  placeholder="Enter your password"
                  required
                  autocomplete="current-password"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
              </div>
              <InputError class="text-red-400 text-sm" :message="form.errors.password" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
              <label class="flex items-center">
                <Checkbox v-model:checked="form.remember" class="rounded border-white/20 text-violet-500 focus:ring-violet-500 focus:ring-offset-slate-900" />
                <span class="ml-2 text-sm text-slate-300">Remember me</span>
              </label>

              <div>
                <Link
                  v-if="canResetPassword"
                  :href="route('password.request')"
                  class="text-sm bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300"
                >
                  Forgot password?
                </Link>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
              <PrimaryButton
                type="submit"
                :disabled="form.processing"
                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-violet-600 to-cyan-600 hover:from-violet-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:opacity-50 transition-all duration-300 hover:scale-[1.02] shadow-lg hover:shadow-2xl hover:shadow-violet-500/25"
              >
                <span v-if="form.processing" class="absolute left-0 inset-y-0 flex items-center pl-3">
                  <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </span>
                {{ form.processing ? 'Signing in...' : 'Sign In' }}
              </PrimaryButton>
            </div>
          </form>

          <!-- Footer Links -->
          <div class="mt-8 text-center">
            <p class="text-sm text-slate-400">
              New to Visual Gallery?
              <Link :href="route('register')" class="font-semibold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300 ml-1">
                Create your account
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

<style scoped>
/* Enhanced Animations */
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
    opacity: 0.5;
  }
  25% {
    transform: translateY(-12px) translateX(8px);
    opacity: 0.8;
  }
  50% {
    transform: translateY(-6px) translateX(-4px);
    opacity: 1;
  }
  75% {
    transform: translateY(-18px) translateX(-10px);
    opacity: 0.6;
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}
.animate-shimmer {
  animation: shimmer 3s ease-in-out infinite;
}

@keyframes fade-in-up {
  0% {
    opacity: 0;
    transform: translateY(24px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in-up {
  animation: fade-in-up 0.8s ease 0s 1 both;
}

.bg-size-200 {
  background-size: 200% 200%;
}
.bg-pos-0 {
  background-position: 0% 50%;
}

/* Input focus effects */
.focus\:border-violet-500:focus {
  border-color: rgb(139 92 246);
}
.focus\:ring-violet-500\/20:focus {
  --tw-ring-color: rgb(139 92 246 / 0.2);
}
</style>
/*<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import route from 'ziggy-js';

// Props expected by Breeze
defineProps({
    canResetPassword: Boolean,
    status: String,
});

// Form setup
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// Submit handler
const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
  <GuestLayout>
    <Head title="Log in" />

    <!-- Premium Animated Background System -->
    <div class="fixed inset-0 overflow-hidden -z-10">
      <!-- Enhanced Base Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        
        <!-- Advanced Animated Mesh Background -->
        <svg class="absolute inset-0 w-full h-full opacity-25" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <pattern id="mesh-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
              <circle cx="10" cy="10" r="2" fill="currentColor" class="text-violet-500/40">
                <animate attributeName="r" values="2;4;2" dur="6s" repeatCount="indefinite"/>
              </circle>
              <circle cx="5" cy="5" r="1.5" fill="currentColor" class="text-cyan-500/30">
                <animate attributeName="r" values="1.5;3;1.5" dur="8s" repeatCount="indefinite"/>
              </circle>
            </pattern>
            <linearGradient id="mesh-gradient-enhanced" x1="0%" y1="0%" x2="100%" y2="100%">
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
          
          <rect width="100" height="100" fill="url(#mesh-pattern)"/>
          <path d="M0,0 L100,30 L100,100 L0,70 Z" fill="url(#mesh-gradient-enhanced)" opacity="0.15">
            <animateTransform 
              attributeName="transform" 
              type="translate" 
              values="0,0;15,-8;0,0" 
              dur="12s" 
              repeatCount="indefinite" />
          </path>
          <path d="M0,100 L100,80 L100,0 L0,20 Z" fill="url(#mesh-gradient-enhanced)" opacity="0.1">
            <animateTransform 
              attributeName="transform" 
              type="translate" 
              values="0,0;-10,5;0,0" 
              dur="10s" 
              repeatCount="indefinite" />
          </path>
        </svg>
        
        <!-- Enhanced Floating Orbs with Layered Effects -->
        <div class="absolute top-10 left-10 w-80 h-80 bg-gradient-to-r from-violet-500/25 to-purple-500/25 rounded-full blur-3xl animate-pulse-slow opacity-60"></div>
        <div class="absolute top-20 right-16 w-96 h-96 bg-gradient-to-l from-cyan-500/20 to-violet-500/20 rounded-full blur-3xl animate-pulse-slow delay-1000 opacity-70"></div>
        <div class="absolute bottom-16 left-1/4 w-72 h-72 bg-gradient-to-t from-purple-500/15 to-cyan-500/15 rounded-full blur-3xl animate-pulse-slow delay-2000 opacity-50"></div>
        <div class="absolute bottom-32 right-1/3 w-60 h-60 bg-gradient-to-br from-violet-500/20 to-purple-500/20 rounded-full blur-2xl animate-pulse-slow delay-3000 opacity-60"></div>
        
        <!-- Enhanced 20-Particle Float System -->
        <div class="absolute inset-0">
          <div v-for="i in 20" :key="`particle-${i}`" 
               :class="[
                 'absolute w-3 h-3 rounded-full opacity-70',
                 i % 4 === 0 ? 'bg-gradient-to-r from-violet-400/40 to-purple-400/40' :
                 i % 4 === 1 ? 'bg-gradient-to-r from-cyan-400/40 to-violet-400/40' :
                 i % 4 === 2 ? 'bg-gradient-to-r from-purple-400/40 to-cyan-400/40' :
                 'bg-gradient-to-r from-violet-400/40 to-cyan-400/40',
                 'animate-float-organic'
               ]"
               :style="{
                 left: Math.random() * 100 + '%',
                 top: Math.random() * 100 + '%',
                 animationDelay: Math.random() * 10 + 's',
                 animationDuration: (6 + Math.random() * 6) + 's'
               }">
            <div class="w-full h-full rounded-full animate-ping opacity-30"></div>
          </div>
        </div>

        <!-- Gradient Overlay Layers -->
        <div class="absolute inset-0 bg-gradient-to-br from-violet-900/30 via-transparent to-cyan-900/30"></div>
        <div class="absolute inset-0 bg-gradient-to-tl from-purple-900/20 via-transparent to-violet-900/20"></div>
      </div>
    </div>

    <!-- Main Content Container -->
    <div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full animate-fade-in-up-enhanced">
        
        <!-- Premium Multi-Layer Glassmorphism Card -->
        <div class="relative">
          <!-- Background glow effect -->
          <div class="absolute -inset-1 bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 rounded-3xl blur-xl opacity-60 animate-pulse-glow"></div>
          
          <!-- Main card -->
          <div class="relative bg-white/8 backdrop-blur-2xl border border-white/20 rounded-3xl p-10 shadow-2xl 
                      hover:bg-white/12 hover:border-white/30 transition-all duration-700 
                      hover:shadow-violet-500/20 hover:shadow-3xl transform hover:scale-[1.02]
                      before:absolute before:inset-0 before:bg-gradient-to-br before:from-white/5 before:to-transparent before:rounded-3xl">
            
            <!-- Header Section with Enhanced Logo -->
            <div class="text-center mb-10 space-y-6">
              <!-- Enhanced Logo with Multiple Layers -->
              <div class="flex justify-center mb-8">
                <div class="relative">
                  <!-- Background glow -->
                  <div class="absolute -inset-2 bg-gradient-to-r from-violet-500/40 to-cyan-500/40 rounded-2xl blur-lg animate-pulse"></div>
                  
                  <!-- Main logo container -->
                  <div class="relative w-20 h-20 bg-gradient-to-br from-violet-500 via-purple-500 to-cyan-500 rounded-2xl 
                              flex items-center justify-center transform hover:scale-110 transition-all duration-500
                              shadow-lg shadow-violet-500/30 hover:shadow-2xl hover:shadow-violet-500/50">
                    <svg class="h-10 w-10 text-white filter drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Enhanced Title with Multi-Layer Gradients -->
              <div class="space-y-4">
                <h2 class="text-4xl font-black leading-tight">
                  <span class="bg-gradient-to-r from-violet-300 via-white to-cyan-300 bg-clip-text text-transparent 
                               animate-shimmer-enhanced bg-size-300 bg-pos-0 filter drop-shadow-sm">
                    Welcome Back
                  </span>
                </h2>
                
                <p class="text-lg text-slate-200 font-medium">
                  Sign in to your 
                  <span class="bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent font-bold">
                    Visual Gallery
                  </span> 
                  account
                </p>
                
                <p class="text-sm text-slate-400 leading-relaxed">
                  Don't have an account?
                  <Link :href="route('register')" 
                    class="font-bold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent 
                           hover:from-white hover:to-white transition-all duration-300 ml-1 
                           hover:scale-105 inline-block transform">
                    Create one here
                  </Link>
                </p>
              </div>
            </div>

            <!-- Enhanced Status Message -->
            <div v-if="status" class="mb-8 animate-slide-in-down">
              <div class="p-4 bg-emerald-500/15 border border-emerald-400/30 rounded-2xl backdrop-blur-sm
                          shadow-lg shadow-emerald-500/10">
                <p class="text-sm text-emerald-300 text-center font-medium">{{ status }}</p>
              </div>
            </div>

            <!-- Enhanced Login Form -->
            <form class="space-y-8" @submit.prevent="submit">
              
              <!-- Email Field with Enhanced Styling -->
              <div class="space-y-3">
                <InputLabel for="email" value="Email Address" 
                  class="text-slate-200 font-semibold text-sm tracking-wide" />
                <div class="relative group">
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 
                              rounded-xl blur opacity-0 group-focus-within:opacity-100 transition-all duration-300"></div>
                  <div class="relative">
                    <TextInput
                      id="email"
                      type="email"
                      v-model="form.email"
                      class="w-full px-5 py-4 bg-white/8 border border-white/20 rounded-xl text-white 
                             placeholder-slate-400 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/30 
                             transition-all duration-300 backdrop-blur-sm hover:bg-white/10 
                             focus:bg-white/10 focus:transform focus:scale-[1.02] font-medium"
                      placeholder="Enter your email address"
                      required
                      autofocus
                      autocomplete="username"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                      <svg class="h-5 w-5 text-slate-400 group-focus-within:text-violet-400 transition-colors duration-300" 
                           fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                      </svg>
                    </div>
                  </div>
                </div>
                <InputError class="text-red-400 text-sm font-medium" :message="form.errors.email" />
              </div>

              <!-- Password Field with Enhanced Styling -->
              <div class="space-y-3">
                <InputLabel for="password" value="Password" 
                  class="text-slate-200 font-semibold text-sm tracking-wide" />
                <div class="relative group">
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 
                              rounded-xl blur opacity-0 group-focus-within:opacity-100 transition-all duration-300"></div>
                  <div class="relative">
                    <TextInput
                      id="password"
                      type="password"
                      v-model="form.password"
                      class="w-full px-5 py-4 bg-white/8 border border-white/20 rounded-xl text-white 
                             placeholder-slate-400 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/30 
                             transition-all duration-300 backdrop-blur-sm hover:bg-white/10 
                             focus:bg-white/10 focus:transform focus:scale-[1.02] font-medium"
                      placeholder="Enter your password"
                      required
                      autocomplete="current-password"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                      <svg class="h-5 w-5 text-slate-400 group-focus-within:text-violet-400 transition-colors duration-300" 
                           fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                      </svg>
                    </div>
                  </div>
                </div>
                <InputError class="text-red-400 text-sm font-medium" :message="form.errors.password" />
              </div>

              <!-- Remember Me & Forgot Password with Enhanced Styling -->
              <div class="flex items-center justify-between py-2">
                <label class="flex items-center group cursor-pointer">
                  <div class="relative">
                    <Checkbox v-model:checked="form.remember" 
                      class="rounded-md border-white/30 bg-white/10 text-violet-500 
                             focus:ring-violet-500/30 focus:ring-offset-0 hover:bg-white/15 
                             transition-all duration-300" />
                  </div>
                  <span class="ml-3 text-sm text-slate-300 group-hover:text-white transition-colors duration-300 font-medium">
                    Remember me
                  </span>
                </label>

                <div>
                  <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent 
                           hover:from-white hover:to-white transition-all duration-300 font-semibold
                           hover:scale-105 transform inline-block"
                  >
                    Forgot password?
                  </Link>
                </div>
              </div>

              <!-- Enhanced Submit Button with Multi-Layer Effects -->
              <div class="pt-4">
                <div class="relative group">
                  <!-- Button glow effect -->
                  <div class="absolute -inset-1 bg-gradient-to-r from-violet-500/50 via-purple-500/50 to-cyan-500/50 
                              rounded-2xl blur-lg opacity-60 group-hover:opacity-100 transition-all duration-500"></div>
                  
                  <PrimaryButton
                    type="submit"
                    :disabled="form.processing"
                    class="relative w-full flex justify-center items-center py-4 px-6 
                           text-base font-bold rounded-xl text-white 
                           bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 
                           hover:from-violet-700 hover:via-purple-700 hover:to-cyan-700 
                           focus:outline-none focus:ring-4 focus:ring-violet-500/30 focus:ring-offset-0 
                           disabled:opacity-50 disabled:cursor-not-allowed
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
                      <span>Signing in...</span>
                    </span>
                    <span v-else class="flex items-center gap-3">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                      </svg>
                      <span>Sign In</span>
                    </span>
                  </PrimaryButton>
                </div>
              </div>
            </form>

            <!-- Enhanced Footer Links -->
            <div class="mt-10 text-center space-y-4">
              <div class="w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
              <p class="text-sm text-slate-400 leading-relaxed">
                New to 
                <span class="bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent font-bold">
                  Visual Gallery
                </span>?
                <Link :href="route('register')" 
                  class="font-bold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent 
                         hover:from-white hover:to-white transition-all duration-300 ml-1
                         hover:scale-105 transform inline-block">
                  Create your account
                </Link>
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
    transform: translateY(-20px) translateX(10px) rotate(90deg);
    opacity: 0.8;
  }
  50% {
    transform: translateY(-10px) translateX(-15px) rotate(180deg);
    opacity: 1;
  }
  75% {
    transform: translateY(-25px) translateX(5px) rotate(270deg);
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

@keyframes pulse-slow {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.05); }
}

@keyframes pulse-glow {
  0%, 100% { opacity: 0.5; }
  50% { opacity: 1; }
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

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.animate-pulse-glow {
  animation: pulse-glow 3s ease-in-out infinite;
}

.animate-slide-in-down {
  animation: slide-in-down 0.6s ease-out 0s 1 both;
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

/* Form Enhancement */
input:focus {
  outline: none;
}

input:autofill,
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0 30px rgba(255, 255, 255, 0.1) inset !important;
  -webkit-text-fill-color: white !important;
}
</style>