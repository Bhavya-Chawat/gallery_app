<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import route from 'ziggy-js';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <!-- Enhanced Animated Background -->
        <div class="fixed inset-0 overflow-hidden">
          <!-- Base Background -->
          <div class="absolute inset-0 bg-slate-900">
            <!-- Animated Mesh Background -->
            <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
              <defs>
                <linearGradient id="mesh-gradient-register" x1="0%" y1="0%" x2="100%" y2="100%">
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
              <path d="M0,0 L100,20 L100,100 L0,80 Z" fill="url(#mesh-gradient-register)" opacity="0.1">
                <animateTransform 
                  attributeName="transform" 
                  type="translate" 
                  values="0,0;10,-5;0,0" 
                  dur="10s" 
                  repeatCount="indefinite" />
              </path>
            </svg>
            
            <!-- Floating Orbs -->
            <div class="absolute top-20 right-10 w-72 h-72 bg-gradient-to-r from-cyan-500/20 to-violet-500/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-40 left-20 w-96 h-96 bg-gradient-to-l from-purple-500/15 to-cyan-500/15 rounded-full blur-3xl animate-pulse delay-700"></div>
            <div class="absolute bottom-20 right-1/3 w-80 h-80 bg-gradient-to-t from-violet-500/10 to-purple-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
            
            <!-- Particle System -->
            <div class="absolute inset-0">
              <div v-for="i in 15" :key="i" 
                   :class="`absolute w-2 h-2 bg-gradient-to-r from-cyan-400/30 to-violet-400/30 rounded-full animate-float`"
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
                  <div class="w-16 h-16 bg-gradient-to-r from-cyan-500 to-violet-500 rounded-2xl flex items-center justify-center animate-pulse">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>

                <h2 class="text-3xl font-extrabold">
                  <span class="bg-gradient-to-r from-cyan-400 via-white to-violet-400 bg-clip-text text-transparent animate-shimmer bg-size-200 bg-pos-0">
                    Join Visual Gallery
                  </span>
                </h2>
                <p class="mt-3 text-slate-300">
                  Create your account to start sharing amazing photography
                </p>
                <p class="mt-2 text-sm text-slate-400">
                  Already have an account?
                  <Link :href="route('login')" class="font-semibold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300 ml-1">
                    Sign in here
                  </Link>
                </p>
              </div>

              <!-- Registration Form -->
              <form class="space-y-6" @submit.prevent="submit">
                
                <!-- Name Field -->
                <div class="space-y-2">
                  <InputLabel for="name" value="Full Name" class="text-slate-300 font-medium" />
                  <div class="relative">
                    <TextInput
                      id="name"
                      type="text"
                      v-model="form.name"
                      class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all duration-300"
                      placeholder="Enter your full name"
                      required
                      autofocus
                      autocomplete="name"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                      <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </div>
                  </div>
                  <InputError class="text-red-400 text-sm" :message="form.errors.name" />
                </div>

                <!-- Email Field -->
                <div class="space-y-2">
                  <InputLabel for="email" value="Email Address" class="text-slate-300 font-medium" />
                  <div class="relative">
                    <TextInput
                      id="email"
                      type="email"
                      v-model="form.email"
                      class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all duration-300"
                      placeholder="Enter your email address"
                      required
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
                      class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all duration-300"
                      placeholder="Create a secure password"
                      required
                      autocomplete="new-password"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                      <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                      </svg>
                    </div>
                  </div>
                  <InputError class="text-red-400 text-sm" :message="form.errors.password" />
                </div>

                <!-- Confirm Password Field -->
                <div class="space-y-2">
                  <InputLabel for="password_confirmation" value="Confirm Password" class="text-slate-300 font-medium" />
                  <div class="relative">
                    <TextInput
                      id="password_confirmation"
                      type="password"
                      v-model="form.password_confirmation"
                      class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all duration-300"
                      placeholder="Confirm your password"
                      required
                      autocomplete="new-password"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                      <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                  </div>
                  <InputError class="text-red-400 text-sm" :message="form.errors.password_confirmation" />
                </div>

                <!-- Terms Notice -->
                <div class="text-sm text-slate-400 text-center">
                  By creating an account, you agree to our
                  <a href="#" class="bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300">
                    Terms of Service
                  </a>
                  and
                  <a href="#" class="bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300">
                    Privacy Policy
                  </a>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                  <PrimaryButton
                    type="submit"
                    :disabled="form.processing"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-cyan-600 to-violet-600 hover:from-cyan-700 hover:to-violet-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:opacity-50 transition-all duration-300 hover:scale-[1.02] shadow-lg hover:shadow-2xl hover:shadow-cyan-500/25"
                  >
                    <span v-if="form.processing" class="absolute left-0 inset-y-0 flex items-center pl-3">
                      <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                    </span>
                    {{ form.processing ? 'Creating Account...' : 'Create Account' }}
                  </PrimaryButton>
                </div>
              </form>

              <!-- Footer Links -->
              <div class="mt-8 text-center">
                <p class="text-sm text-slate-400">
                  Already part of our community?
                  <Link :href="route('login')" class="font-semibold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300 ml-1">
                    Sign in to your account
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
.focus\:border-cyan-500:focus {
  border-color: rgb(6 182 212);
}
.focus\:ring-cyan-500\/20:focus {
  --tw-ring-color: rgb(6 182 212 / 0.2);
}
</style>
