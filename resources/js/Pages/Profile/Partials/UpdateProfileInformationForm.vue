<template>
  <section class="relative">
    <!-- Premium Animated Background -->
    <div class="absolute inset-0 overflow-hidden -z-10 rounded-3xl">
      <!-- Enhanced Base Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        
        <!-- Animated Mesh Background -->
        <svg class="absolute inset-0 w-full h-full opacity-10" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <pattern id="profile-mesh" x="0" y="0" width="15" height="15" patternUnits="userSpaceOnUse">
              <circle cx="7.5" cy="7.5" r="1" fill="currentColor" class="text-violet-500/30">
                <animate attributeName="r" values="1;2;1" dur="4s" repeatCount="indefinite"/>
              </circle>
            </pattern>
            <linearGradient id="profile-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" style="stop-color:#8b5cf6">
                <animate attributeName="stop-color" 
                  values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" 
                  dur="6s" 
                  repeatCount="indefinite" />
              </stop>
              <stop offset="100%" style="stop-color:#06b6d4">
                <animate attributeName="stop-color" 
                  values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" 
                  dur="6s" 
                  repeatCount="indefinite" />
              </stop>
            </linearGradient>
          </defs>
          
          <rect width="100" height="100" fill="url(#profile-mesh)"/>
          <path d="M0,10 L100,30 L100,100 L0,80 Z" fill="url(#profile-gradient)" opacity="0.05">
            <animateTransform 
              attributeName="transform" 
              type="translate" 
              values="0,0;5,-2;0,0" 
              dur="8s" 
              repeatCount="indefinite" />
          </path>
        </svg>
        
        <!-- Floating Orbs -->
        <div class="absolute top-10 left-10 w-32 h-32 bg-gradient-to-r from-violet-500/10 to-purple-500/10 rounded-full blur-2xl animate-pulse-slow"></div>
        <div class="absolute bottom-10 right-10 w-40 h-40 bg-gradient-to-l from-cyan-500/8 to-violet-500/8 rounded-full blur-2xl animate-pulse-slow delay-1000"></div>
        
        <!-- Particle System -->
        <div class="absolute inset-0">
          <div v-for="i in 12" :key="`profile-particle-${i}`" 
               :class="[
                 'absolute w-1 h-1 rounded-full opacity-30',
                 i % 3 === 0 ? 'bg-gradient-to-r from-violet-400/20 to-purple-400/20' :
                 i % 3 === 1 ? 'bg-gradient-to-r from-cyan-400/20 to-violet-400/20' :
                 'bg-gradient-to-r from-purple-400/20 to-cyan-400/20',
                 'animate-float-gentle'
               ]"
               :style="{
                 left: Math.random() * 100 + '%',
                 top: Math.random() * 100 + '%',
                 animationDelay: Math.random() * 8 + 's',
                 animationDuration: (4 + Math.random() * 4) + 's'
               }">
          </div>
        </div>

        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-violet-900/5 via-transparent to-cyan-900/5"></div>
      </div>
    </div>

    <!-- Main Content Container -->
    <div class="relative bg-white/8 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl hover:bg-white/12 hover:border-white/30 transition-all duration-500 hover:shadow-violet-500/10 hover:shadow-2xl">
      
      <header class="mb-8 animate-fade-in-up">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-violet-300 via-white to-cyan-300 bg-clip-text text-transparent animate-shimmer bg-size-200 bg-pos-0">
              Profile Information
            </h2>
          </div>
        </div>
        <p class="text-slate-300 leading-relaxed">
          Update your account's profile information and email address for your Clone Fest Gallery platform experience.
        </p>
      </header>

      <form @submit.prevent="updateProfile" class="space-y-8 animate-fade-in-up animation-delay-200">
        
        <!-- Name & Email Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="group">
            <label for="name" class="block text-sm font-semibold text-slate-200 mb-2 group-focus-within:text-violet-300 transition-colors duration-300">
              Name
            </label>
            <div class="relative">
              <TextInput
                id="name"
                type="text"
                class="w-full px-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-400/50 hover:bg-white/15 hover:border-white/30 transition-all duration-300"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
              />
              <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 rounded-xl blur opacity-0 group-focus-within:opacity-100 transition-opacity duration-500 -z-10"></div>
            </div>
            <InputError class="mt-2 text-red-400" :message="form.errors.name" />
          </div>

          <div class="group">
            <label for="email" class="block text-sm font-semibold text-slate-200 mb-2 group-focus-within:text-cyan-300 transition-colors duration-300">
              Email
            </label>
            <div class="relative">
              <TextInput
                id="email"
                type="email"
                class="w-full px-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-400/50 hover:bg-white/15 hover:border-white/30 transition-all duration-300"
                v-model="form.email"
                required
                autocomplete="username"
              />
              <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-500/20 to-purple-500/20 rounded-xl blur opacity-0 group-focus-within:opacity-100 transition-opacity duration-500 -z-10"></div>
            </div>
            <InputError class="mt-2 text-red-400" :message="form.errors.email" />
          </div>
        </div>

        <!-- Bio Section -->
        <div class="group">
          <label for="bio" class="block text-sm font-semibold text-slate-200 mb-2 group-focus-within:text-purple-300 transition-colors duration-300">
            Bio
          </label>
          <div class="relative">
            <textarea
              id="bio"
              v-model="form.bio"
              rows="3"
              class="w-full px-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-400/50 hover:bg-white/15 hover:border-white/30 transition-all duration-300 resize-none"
              placeholder="Tell people about your photography journey and vision..."
            ></textarea>
            <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-500/20 to-violet-500/20 rounded-xl blur opacity-0 group-focus-within:opacity-100 transition-opacity duration-500 -z-10"></div>
          </div>
          <div class="flex justify-between items-center mt-2">
            <InputError class="text-red-400" :message="form.errors.bio" />
            <p class="text-sm text-slate-400">{{ (form.bio || '').length }}/1000 characters</p>
          </div>
        </div>

        <!-- Website Section -->
        <div class="group">
          <label for="website" class="block text-sm font-semibold text-slate-200 mb-2 group-focus-within:text-violet-300 transition-colors duration-300">
            Website
          </label>
          <div class="relative">
            <TextInput
              id="website"
              type="url"
              class="w-full px-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-400/50 hover:bg-white/15 hover:border-white/30 transition-all duration-300"
              v-model="form.website"
              placeholder="https://your-portfolio.com"
              autocomplete="url"
            />
            <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 rounded-xl blur opacity-0 group-focus-within:opacity-100 transition-opacity duration-500 -z-10"></div>
          </div>
          <InputError class="mt-2 text-red-400" :message="form.errors.website" />
        </div>

        <!-- Social Links Section -->
        <div>
          <div class="mb-4">
            <h3 class="text-lg font-semibold bg-gradient-to-r from-cyan-300 to-purple-300 bg-clip-text text-transparent">
              Social Links
            </h3>
            <p class="text-sm text-slate-400 mt-1">Connect your social profiles to showcase your work</p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="group">
              <label for="twitter" class="block text-sm font-medium text-slate-300 mb-2 group-focus-within:text-blue-300 transition-colors duration-300">
                Twitter
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                </div>
                <TextInput
                  id="twitter"
                  type="text"
                  class="w-full pl-10 pr-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 hover:bg-white/15 hover:border-white/30 transition-all duration-300"
                  v-model="form.social_links.twitter"
                  placeholder="@username"
                />
                <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500/20 to-cyan-500/20 rounded-xl blur opacity-0 group-focus-within:opacity-100 transition-opacity duration-500 -z-10"></div>
              </div>
            </div>
            
            <div class="group">
              <label for="instagram" class="block text-sm font-medium text-slate-300 mb-2 group-focus-within:text-pink-300 transition-colors duration-300">
                Instagram
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987s11.987-5.367 11.987-11.987C24.004 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.348-1.051-2.348-2.348s1.051-2.348 2.348-2.348 2.348 1.051 2.348 2.348-1.051 2.348-2.348 2.348zm7.718 0c-1.297 0-2.348-1.051-2.348-2.348s1.051-2.348 2.348-2.348 2.348 1.051 2.348 2.348-1.051 2.348-2.348 2.348z"/>
                  </svg>
                </div>
                <TextInput
                  id="instagram"
                  type="text"
                  class="w-full pl-10 pr-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-400/50 hover:bg-white/15 hover:border-white/30 transition-all duration-300"
                  v-model="form.social_links.instagram"
                  placeholder="@username"
                />
                <div class="absolute -inset-0.5 bg-gradient-to-r from-pink-500/20 to-purple-500/20 rounded-xl blur opacity-0 group-focus-within:opacity-100 transition-opacity duration-500 -z-10"></div>
              </div>
            </div>
            
            <div class="group">
              <label for="linkedin" class="block text-sm font-medium text-slate-300 mb-2 group-focus-within:text-blue-300 transition-colors duration-300">
                LinkedIn
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                  </svg>
                </div>
                <TextInput
                  id="linkedin"
                  type="text"
                  class="w-full pl-10 pr-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400/50 hover:bg-white/15 hover:border-white/30 transition-all duration-300"
                  v-model="form.social_links.linkedin"
                  placeholder="linkedin.com/in/username"
                />
                <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500/20 to-cyan-500/20 rounded-xl blur opacity-0 group-focus-within:opacity-100 transition-opacity duration-500 -z-10"></div>
              </div>
            </div>
          </div>
          <InputError class="mt-3 text-red-400" :message="form.errors.social_links" />
        </div>

        <!-- Email Notifications -->
        <div class="p-6 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl hover:bg-white/10 hover:border-white/20 transition-all duration-300">
          <div class="flex items-start gap-4">
            <div class="flex items-center h-5 mt-1">
              <input
                v-model="form.email_notifications"
                type="checkbox"
                class="h-5 w-5 bg-white/10 border-white/30 rounded text-violet-500 focus:ring-violet-500/50 focus:ring-offset-0 focus:ring-2 transition-all duration-300"
              />
            </div>
            <div class="flex-1">
              <label class="text-base font-medium text-white cursor-pointer">
                Email Notifications
              </label>
              <p class="text-sm text-slate-400 mt-1">
                Receive email notifications for comments, likes, and gallery platform updates to stay connected with your photography community.
              </p>
            </div>
          </div>
        </div>

        <!-- Email Verification Notice -->
        <div v-if="mustVerifyEmail && user.email_verified_at === null" class="p-6 bg-amber-500/10 border border-amber-500/30 rounded-2xl backdrop-blur-xl">
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-amber-300 font-semibold mb-2">Email Verification Required</h3>
              <p class="text-amber-200/80 text-sm mb-3">
                Your email address is unverified. Please verify your email to access all Clone Fest Gallery platform features.
              </p>
              <Link
                :href="$route('verification.send')"
                method="post"
                as="button"
                class="inline-flex items-center px-4 py-2 bg-amber-500/20 border border-amber-400/30 text-amber-300 font-medium rounded-xl hover:bg-amber-500/30 hover:border-amber-400/50 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all duration-300"
              >
                Resend Verification Email
              </Link>
            </div>
          </div>

          <div
            v-show="status === 'verification-link-sent'"
            class="mt-4 p-3 bg-green-500/20 border border-green-400/30 rounded-xl"
          >
            <p class="text-green-300 text-sm font-medium">
              A new verification link has been sent to your email address.
            </p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-6 pt-4 animate-fade-in-up animation-delay-400">
          <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-violet-500/40 to-cyan-500/40 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <PrimaryButton 
              :disabled="form.processing" 
              class="relative px-8 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 text-white font-semibold rounded-xl hover:from-violet-700 hover:to-cyan-700 focus:outline-none focus:ring-4 focus:ring-violet-500/30 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-violet-500/30 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="form.processing" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12a8 8 0 003.657 6.746l1.021-.408A6 6 0 016 12z"></path>
                </svg>
                Saving Changes...
              </span>
              <span v-else>Save Changes</span>
            </PrimaryButton>
          </div>

          <Transition
            enter-active-class="transition ease-in-out duration-300"
            enter-from-class="opacity-0 transform translate-x-2"
            leave-active-class="transition ease-in-out duration-300"
            leave-to-class="opacity-0 transform translate-x-2"
          >
            <div v-if="form.recentlySuccessful" class="flex items-center gap-2 text-green-400 font-medium">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              Profile updated successfully!
            </div>
          </Transition>
        </div>
      </form>
    </div>
  </section>
</template>

<script setup>
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'

defineProps({
  mustVerifyEmail: Boolean,
  status: String,
  user: Object,
})

const user = usePage().props.auth.user

const form = useForm({
  name: user.name || '',
  email: user.email || '',
  bio: user.bio || '',
  website: user.website || '',
  social_links: {
    twitter: user.social_links?.twitter || '',
    instagram: user.social_links?.instagram || '',
    linkedin: user.social_links?.linkedin || '',
  },
  email_notifications: user.email_notifications || false,
})

const updateProfile = () => {
  form.patch('/profile', {
    preserveScroll: true,
  })
}
</script>

<style scoped>
@keyframes pulse-slow {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 0.9; }
}

@keyframes float-gentle {
  0%, 100% {
    transform: translateY(0px) translateX(0px);
  }
  50% {
    transform: translateY(-8px) translateX(4px);
  }
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes fade-in-up {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-pulse-slow {
  animation: pulse-slow 3s ease-in-out infinite;
}

.animate-float-gentle {
  animation: float-gentle 6s ease-in-out infinite;
}

.animate-shimmer {
  animation: shimmer 3s ease-in-out infinite;
  background-size: 200% 200%;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
  opacity: 0;
}

.bg-size-200 {
  background-size: 200% 200%;
}

.bg-pos-0 {
  background-position: 0% 50%;
}

.animation-delay-200 {
  animation-delay: 0.2s;
}

.animation-delay-400 {
  animation-delay: 0.4s;
}

.delay-1000 {
  animation-delay: 1s;
}
</style>