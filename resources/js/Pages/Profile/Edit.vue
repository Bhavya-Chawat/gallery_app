<template>
  <AppLayout>
    <Head title="Profile Settings" />

    <template #header>
      <div class="relative">
        <h2 class="font-bold text-3xl gradient-text animate-shimmer">
          Profile Settings
        </h2>
        <p class="text-slate-300 mt-2">Manage your account settings and preferences</p>
      </div>
    </template>

    <!-- Animated Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
      <!-- Animated Mesh Background -->
      <svg class="absolute inset-0 w-full h-full opacity-30" viewBox="0 0 100 100" preserveAspectRatio="none">
        <defs>
          <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(0)">
            <stop offset="0%" stop-color="#8b5cf6" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#8b5cf6" dur="8s" repeatCount="indefinite" />
            </stop>
            <stop offset="50%" stop-color="#06b6d4" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#06b6d4" dur="8s" repeatCount="indefinite" />
            </stop>
            <stop offset="100%" stop-color="#a855f7" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#a855f7" dur="8s" repeatCount="indefinite" />
            </stop>
          </linearGradient>
        </defs>
        <path d="M0,0 Q25,25 50,0 T100,0 L100,25 Q75,50 50,25 T0,25 Z" fill="url(#meshGradient)" opacity="0.1">
          <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;10,-5;0,0" dur="6s" repeatCount="indefinite" />
        </path>
        <path d="M0,25 Q25,50 50,25 T100,25 L100,50 Q75,75 50,50 T0,50 Z" fill="url(#meshGradient)" opacity="0.15">
          <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;-8,3;0,0" dur="7s" repeatCount="indefinite" />
        </path>
        <path d="M0,50 Q25,75 50,50 T100,50 L100,75 Q75,100 50,75 T0,75 Z" fill="url(#meshGradient)" opacity="0.1">
          <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;5,8;0,0" dur="9s" repeatCount="indefinite" />
        </path>
      </svg>

      <!-- Floating Particles -->
      <div class="absolute inset-0">
        <div v-for="i in 20" :key="i" 
             class="absolute w-2 h-2 rounded-full opacity-40 animate-float"
             :class="getParticleClass(i)"
             :style="getParticleStyle(i)">
        </div>
      </div>

      <!-- Gradient Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"></div>
    </div>

    <div class="min-h-screen py-12 relative">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <!-- Profile Overview -->
        <div class="glass-card rounded-2xl border border-white/20 shadow-2xl shadow-violet-500/10 hover:shadow-violet-500/20 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.1s">
          <div class="p-8">
            <div class="flex items-center justify-between mb-8">
              <div class="flex items-center space-x-8">
                <div class="relative group">
                  <div class="absolute inset-0 rounded-full bg-gradient-to-r from-violet-500 to-cyan-500 opacity-75 group-hover:opacity-100 blur-sm group-hover:blur-md transition-all duration-300"></div>
                  <UserAvatar :user="user" size="xl" class="relative z-10 ring-4 ring-white/20 hover:ring-white/40 transition-all duration-300" />
                </div>
                <div class="space-y-2">
                  <h3 class="text-2xl font-bold gradient-text">{{ user.name }}</h3>
                  <p class="text-slate-300 text-lg">{{ user.email }}</p>
                  <p class="text-slate-400 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v6a2 2 0 002 2h4a2 2 0 002-2v-6"></path>
                    </svg>
                    Member since {{ formatDate(user.created_at) }}
                  </p>
                </div>
              </div>
              <div class="text-right">
                <Link :href="route('profile.show', user.id)" class="premium-link group">
                  <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  View Public Profile
                </Link>
              </div>
            </div>

            <!-- Profile Completion -->
            <div class="glass-card-inner rounded-xl p-6">
              <div class="flex items-center justify-between mb-3">
                <span class="text-lg font-semibold text-slate-200 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Profile Completion
                </span>
                <span class="text-lg font-bold gradient-text">{{ profileCompletion }}%</span>
              </div>
              <div class="relative w-full bg-slate-700/50 rounded-full h-3 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 rounded-full"></div>
                <div 
                  class="bg-gradient-to-r from-violet-500 to-cyan-500 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-violet-500/30" 
                  :style="{ width: profileCompletion + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Avatar Upload -->
        <div class="glass-card rounded-2xl border border-white/20 shadow-2xl shadow-cyan-500/10 hover:shadow-cyan-500/20 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.2s">
          <div class="p-8">
            <h3 class="text-2xl font-bold gradient-text mb-6 flex items-center animate-shimmer">
              <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              Profile Photo
            </h3>
            <form @submit.prevent="uploadAvatar" enctype="multipart/form-data">
              <div class="flex items-center space-x-8">
                <div class="relative group">
                  <div class="absolute inset-0 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 opacity-75 group-hover:opacity-100 blur-sm transition-all duration-300"></div>
                  <UserAvatar :user="user" size="lg" class="relative z-10 ring-4 ring-white/20 hover:ring-white/40 transition-all duration-300" />
                </div>
                <div class="flex-1 space-y-4">
                  <input
                    ref="avatarInput"
                    type="file"
                    accept="image/*"
                    @change="handleAvatarChange"
                    class="hidden"
                  />
                  <button
                    type="button"
                    @click="$refs.avatarInput.click()"
                    class="premium-button-secondary group"
                  >
                    <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Change Photo
                  </button>
                  <p class="text-slate-300 text-sm">
                    JPG, PNG up to 2MB. Recommended: 400x400px
                  </p>
                </div>
              </div>
              <div v-if="avatarForm.avatar" class="mt-6">
                <button
                  type="submit"
                  :disabled="avatarForm.processing"
                  class="premium-button group"
                  :class="{ 'opacity-50 cursor-not-allowed': avatarForm.processing }"
                >
                  <svg v-if="avatarForm.processing" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                  </svg>
                  {{ avatarForm.processing ? 'Uploading...' : 'Upload Photo' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Profile Information -->
        <div class="glass-card rounded-2xl border border-white/20 shadow-2xl shadow-purple-500/10 hover:shadow-purple-500/20 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.3s">
          <div class="p-8">
            <div class="mb-6">
              <h3 class="text-2xl font-bold gradient-text flex items-center animate-shimmer">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profile Information
              </h3>
              <p class="text-slate-300 mt-2">Update your account's profile information and email address.</p>
            </div>
            <UpdateProfileInformationForm
              :must-verify-email="mustVerifyEmail"
              :status="status"
              :user="user"
            />
          </div>
        </div>

        <!-- Privacy Settings -->
        <div class="glass-card rounded-2xl border border-white/20 shadow-2xl shadow-emerald-500/10 hover:shadow-emerald-500/20 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.4s">
          <div class="p-8">
            <h3 class="text-2xl font-bold gradient-text mb-6 flex items-center animate-shimmer">
              <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
              </svg>
              Privacy Settings
            </h3>
            <form @submit.prevent="updatePrivacy" class="space-y-6">
              <div class="glass-card-inner rounded-xl p-6">
                <label class="block text-lg font-semibold text-slate-200 mb-4">
                  Profile Visibility
                </label>
                <div class="space-y-4">
                  <label class="flex items-center group cursor-pointer">
                    <input
                      v-model="privacyForm.profile_visibility"
                      type="radio"
                      value="public"
                      class="h-4 w-4 text-violet-600 focus:ring-violet-500 focus:ring-2 border-slate-300 bg-white/10"
                    />
                    <span class="ml-3 text-slate-300 group-hover:text-white transition-colors duration-200">
                      Public - Anyone can view your profile
                    </span>
                  </label>
                  <label class="flex items-center group cursor-pointer">
                    <input
                      v-model="privacyForm.profile_visibility"
                      type="radio"
                      value="private"
                      class="h-4 w-4 text-violet-600 focus:ring-violet-500 focus:ring-2 border-slate-300 bg-white/10"
                    />
                    <span class="ml-3 text-slate-300 group-hover:text-white transition-colors duration-200">
                      Private - Only you can view your profile
                    </span>
                  </label>
                </div>
              </div>

              <div class="space-y-4">
                <div class="glass-card-inner rounded-xl p-6">
                  <label class="flex items-center group cursor-pointer">
                    <input
                      v-model="privacyForm.show_email_publicly"
                      type="checkbox"
                      class="h-4 w-4 text-violet-600 focus:ring-violet-500 focus:ring-2 border-slate-300 rounded bg-white/10"
                    />
                    <span class="ml-3 text-slate-300 group-hover:text-white transition-colors duration-200">
                      Show email address on public profile
                    </span>
                  </label>
                </div>

                <div class="glass-card-inner rounded-xl p-6">
                  <label class="flex items-center group cursor-pointer">
                    <input
                      v-model="privacyForm.show_stats_publicly"
                      type="checkbox"
                      class="h-4 w-4 text-violet-600 focus:ring-violet-500 focus:ring-2 border-slate-300 rounded bg-white/10"
                    />
                    <span class="ml-3 text-slate-300 group-hover:text-white transition-colors duration-200">
                      Show statistics on public profile
                    </span>
                  </label>
                </div>

                <div class="glass-card-inner rounded-xl p-6">
                  <label class="flex items-center group cursor-pointer">
                    <input
                      v-model="privacyForm.email_notifications"
                      type="checkbox"
                      class="h-4 w-4 text-violet-600 focus:ring-violet-500 focus:ring-2 border-slate-300 rounded bg-white/10"
                    />
                    <span class="ml-3 text-slate-300 group-hover:text-white transition-colors duration-200">
                      Email notifications
                    </span>
                  </label>
                  <p class="mt-2 text-sm text-slate-400 ml-7">
                    Receive email notifications for comments, likes, and system updates.
                  </p>
                </div>
              </div>

              <div class="pt-4">
                <button
                  type="submit"
                  :disabled="privacyForm.processing"
                  class="premium-button group"
                  :class="{ 'opacity-50 cursor-not-allowed': privacyForm.processing }"
                >
                  <svg v-if="privacyForm.processing" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Save Privacy Settings
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Statistics -->
        <div class="glass-card rounded-2xl border border-white/20 shadow-2xl shadow-blue-500/10 hover:shadow-blue-500/20 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.5s">
          <div class="p-8">
            <h3 class="text-2xl font-bold gradient-text mb-6 flex items-center animate-shimmer">
              <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              Your Statistics
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
              <div class="stat-card group">
                <div class="text-3xl font-bold gradient-text group-hover:scale-110 transition-transform duration-300">{{ stats.images || 0 }}</div>
                <div class="text-slate-300 text-sm font-medium flex items-center mt-2">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  Images
                </div>
              </div>
              <div class="stat-card group">
                <div class="text-3xl font-bold gradient-text group-hover:scale-110 transition-transform duration-300">{{ stats.albums || 0 }}</div>
                <div class="text-slate-300 text-sm font-medium flex items-center mt-2">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                  </svg>
                  Albums
                </div>
              </div>
              <div class="stat-card group">
                <div class="text-3xl font-bold gradient-text group-hover:scale-110 transition-transform duration-300">{{ formatNumber(stats.views || 0) }}</div>
                <div class="text-slate-300 text-sm font-medium flex items-center mt-2">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  Total Views
                </div>
              </div>
              <div class="stat-card group">
                <div class="text-3xl font-bold gradient-text group-hover:scale-110 transition-transform duration-300">{{ formatNumber(stats.likes || 0) }}</div>
                <div class="text-slate-300 text-sm font-medium flex items-center mt-2">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                  </svg>
                  Total Likes
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Storage Usage -->
        <div class="glass-card rounded-2xl border border-white/20 shadow-2xl shadow-orange-500/10 hover:shadow-orange-500/20 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.6s">
          <div class="p-8">
            <h3 class="text-2xl font-bold gradient-text mb-6 flex items-center animate-shimmer">
              <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2M7 4h10M7 4l-.5 14a2 2 0 002 2h7a2 2 0 002-2L17 4M9 9v6m6-6v6"></path>
              </svg>
              Storage Usage
            </h3>
            <StorageUsageCard
              :used="user.storage_used_bytes"
              :quota="user.storage_quota_bytes"
              :percentage="user.storage_usage_percentage"
              :show-details="true"
            />
          </div>
        </div>

        <!-- Password Update -->
        <div class="glass-card rounded-2xl border border-white/20 shadow-2xl shadow-red-500/10 hover:shadow-red-500/20 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.7s">
          <div class="p-8">
            <div class="mb-6">
              <h3 class="text-2xl font-bold gradient-text flex items-center animate-shimmer">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                Update Password
              </h3>
              <p class="text-slate-300 mt-2">Ensure your account is using a long, random password to stay secure.</p>
            </div>
            <UpdatePasswordForm />
          </div>
        </div>

        <!-- Danger Zone -->
        <div class="glass-card rounded-2xl border border-red-500/30 shadow-2xl shadow-red-500/20 hover:shadow-red-500/30 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.8s">
          <div class="p-8">
            <div class="mb-6">
              <h3 class="text-2xl font-bold text-red-400 flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Danger Zone
              </h3>
              <p class="text-slate-300 mt-2">Permanently delete your account and all of your content.</p>
            </div>
            <DeleteUserForm />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import route from 'ziggy-js'

import AppLayout from '@/Layouts/AppLayout.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import StorageUsageCard from '@/Components/StorageUsageCard.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'

const props = defineProps({
  user: Object,
  mustVerifyEmail: Boolean,
  status: String,
  stats: {
    type: Object,
    default: () => ({}),
  },
})

// Avatar upload form
const avatarForm = useForm({
  avatar: null,
})

const avatarInput = ref(null)

// Privacy settings form  
const privacyForm = useForm({
  profile_visibility: props.user.profile_visibility,
  show_email_publicly: props.user.show_email_publicly,
  show_stats_publicly: props.user.show_stats_publicly,
  email_notifications: props.user.email_notifications,
})

// Profile completion calculation
const profileCompletion = computed(() => {
  let completion = 0
  const fields = ['name', 'email', 'bio', 'avatar_path', 'website']
  
  fields.forEach(field => {
    if (props.user[field]) completion += 20
  })
  
  return Math.round(completion)
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatNumber = (number) => {
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  } else if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number.toString()
}

const handleAvatarChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    avatarForm.avatar = file
  }
}

const uploadAvatar = () => {
  avatarForm.post(route('profile.avatar'), {
    onSuccess: () => {
      avatarForm.reset()
      avatarInput.value.value = ''
    }
  })
}

const updatePrivacy = () => {
  privacyForm.patch(route('profile.privacy'))
}

const getParticleClass = (index) => {
  const colors = ['bg-violet-500', 'bg-cyan-500', 'bg-purple-500', 'bg-emerald-500', 'bg-pink-500']
  return colors[index % colors.length]
}

const getParticleStyle = (index) => {
  const positions = [
    'top: 10%; left: 10%;',
    'top: 20%; left: 80%;',
    'top: 30%; left: 15%;',
    'top: 40%; left: 75%;',
    'top: 50%; left: 20%;',
    'top: 60%; left: 85%;',
    'top: 70%; left: 10%;',
    'top: 80%; left: 70%;',
    'top: 15%; left: 45%;',
    'top: 25%; left: 60%;',
    'top: 35%; left: 30%;',
    'top: 45%; left: 90%;',
    'top: 55%; left: 5%;',
    'top: 65%; left: 55%;',
    'top: 75%; left: 25%;',
    'top: 85%; left: 40%;',
    'top: 5%; left: 65%;',
    'top: 95%; left: 35%;',
    'top: 12%; left: 95%;',
    'top: 88%; left: 50%;'
  ]
  return positions[index - 1] + `animation-duration: ${3 + (index % 4)}s; animation-delay: ${index * 0.2}s;`
}
</script>

<style scoped>
/* Glass morphism effects */
.glass-card {
  @apply bg-white/5 backdrop-blur-xl border border-white/20;
}

.glass-card-inner {
  @apply bg-white/5 backdrop-blur-sm border border-white/10;
}

/* Gradient text effects */
.gradient-text {
  @apply bg-clip-text text-transparent bg-gradient-to-r from-violet-400 via-cyan-400 to-purple-400;
}

/* Shimmer animation */
.animate-shimmer {
  background-size: 200% 200%;
  animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

/* Premium button styles */
.premium-button {
  @apply inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 
         border border-violet-500/50 rounded-xl font-semibold text-sm text-white 
         uppercase tracking-wide hover:from-violet-500 hover:to-purple-500 
         hover:scale-105 hover:shadow-lg hover:shadow-violet-500/25 
         transition-all duration-300 backdrop-blur-sm;
}

.premium-button-secondary {
  @apply inline-flex items-center px-6 py-3 bg-white/10 backdrop-blur-sm 
         border border-white/20 rounded-xl font-semibold text-sm text-slate-200
         hover:bg-white/20 hover:border-white/30 hover:text-white hover:scale-105
         transition-all duration-300;
}

/* Premium links */
.premium-link {
  @apply inline-flex items-center text-violet-400 hover:text-violet-300 
         font-medium text-sm hover:scale-105 transition-all duration-300;
}

/* Stat cards */
.stat-card {
  @apply text-center p-6 bg-white/5 backdrop-blur-sm border border-white/10 
         rounded-xl hover:bg-white/10 hover:border-white/20 hover:scale-105 
         transition-all duration-300 cursor-pointer;
}

/* Floating animation */
.animate-float {
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  25% {
    transform: translateY(-10px) rotate(90deg);
  }
  50% {
    transform: translateY(-20px) rotate(180deg);
  }
  75% {
    transform: translateY(-10px) rotate(270deg);
  }
}

/* Fade in up animation */
.animate-fade-in-up {
  opacity: 0;
  transform: translateY(30px);
  animation: fadeInUp 0.8s ease-out forwards;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Gradient shift for mesh */
.animate-gradient-shift {
  animation: gradientShift 8s ease-in-out infinite;
}

@keyframes gradientShift {
  0%, 100% {
    stop-color: #8b5cf6;
  }
  33% {
    stop-color: #06b6d4;
  }
  66% {
    stop-color: #a855f7;
  }
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  @apply bg-slate-800/50;
}

::-webkit-scrollbar-thumb {
  @apply bg-gradient-to-b from-violet-500 to-purple-500 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
  @apply from-violet-400 to-purple-400;
}

/* Form inputs styling */
input[type="radio"], input[type="checkbox"] {
  @apply focus:ring-violet-500 focus:ring-2;
}

input[type="radio"]:checked, input[type="checkbox"]:checked {
  @apply bg-gradient-to-r from-violet-500 to-purple-500 border-violet-500;
}

/* Loading states */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>