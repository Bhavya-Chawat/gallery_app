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
