<template>
  <AppLayout>
    <Head title="Dashboard" />

    <!-- Premium Animated Background System -->
    <div class="fixed inset-0 overflow-hidden -z-10">
      <!-- Enhanced Base Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        
        <!-- Advanced Animated Mesh Background -->
        <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <pattern id="dashboard-mesh" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
              <circle cx="10" cy="10" r="1.5" fill="currentColor" class="text-violet-500/40">
                <animate attributeName="r" values="1.5;3;1.5" dur="6s" repeatCount="indefinite"/>
              </circle>
              <circle cx="5" cy="15" r="1" fill="currentColor" class="text-cyan-500/30">
                <animate attributeName="r" values="1;2.5;1" dur="8s" repeatCount="indefinite"/>
              </circle>
            </pattern>
            <linearGradient id="dashboard-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
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
          
          <rect width="100" height="100" fill="url(#dashboard-mesh)"/>
          <path d="M0,20 L100,50 L100,100 L0,70 Z" fill="url(#dashboard-gradient)" opacity="0.1">
            <animateTransform 
              attributeName="transform" 
              type="translate" 
              values="0,0;10,-4;0,0" 
              dur="12s" 
              repeatCount="indefinite" />
          </path>
        </svg>
        
        <!-- Enhanced Floating Orbs -->
        <div class="absolute top-20 left-16 w-72 h-72 bg-gradient-to-r from-violet-500/15 to-purple-500/15 rounded-full blur-3xl animate-pulse-slow opacity-60"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-gradient-to-l from-cyan-500/12 to-violet-500/12 rounded-full blur-3xl animate-pulse-slow delay-1000 opacity-50"></div>
        <div class="absolute bottom-32 left-1/4 w-64 h-64 bg-gradient-to-t from-purple-500/10 to-cyan-500/10 rounded-full blur-3xl animate-pulse-slow delay-2000 opacity-40"></div>
        
        <!-- 20-Particle Float System -->
        <div class="absolute inset-0">
          <div v-for="i in 20" :key="`dash-particle-${i}`" 
               :class="[
                 'absolute w-2 h-2 rounded-full opacity-50',
                 i % 4 === 0 ? 'bg-gradient-to-r from-violet-400/30 to-purple-400/30' :
                 i % 4 === 1 ? 'bg-gradient-to-r from-cyan-400/30 to-violet-400/30' :
                 i % 4 === 2 ? 'bg-gradient-to-r from-purple-400/30 to-cyan-400/30' :
                 'bg-gradient-to-r from-violet-400/30 to-cyan-400/30',
                 'animate-float-organic'
               ]"
               :style="{
                 left: Math.random() * 100 + '%',
                 top: Math.random() * 100 + '%',
                 animationDelay: Math.random() * 10 + 's',
                 animationDuration: (7 + Math.random() * 6) + 's'
               }">
          </div>
        </div>

        <!-- Gradient Overlay Layers -->
        <div class="absolute inset-0 bg-gradient-to-br from-violet-900/20 via-transparent to-cyan-900/20"></div>
      </div>
    </div>

    <template #header>
      <div class="flex items-center justify-between animate-fade-in-down">
        <h2 class="font-black text-2xl leading-tight">
          <span class="bg-gradient-to-r from-violet-300 via-white to-cyan-300 bg-clip-text text-transparent 
                       animate-shimmer-enhanced bg-size-300 bg-pos-0 filter drop-shadow-sm">
            Dashboard
          </span>
        </h2>
        <div class="flex items-center space-x-4">
          <Link
            v-if="canUpload"
            :href="route('upload')"
            class="group relative inline-flex items-center px-6 py-3 border border-transparent rounded-xl 
                   font-bold text-sm text-white uppercase tracking-wider 
                   bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 
                   hover:from-violet-700 hover:via-purple-700 hover:to-cyan-700
                   focus:outline-none focus:ring-4 focus:ring-violet-500/30
                   transition-all duration-500 hover:scale-105 transform
                   shadow-lg hover:shadow-2xl hover:shadow-violet-500/30
                   before:absolute before:-inset-1 before:bg-gradient-to-r before:from-violet-500/20 
                   before:to-cyan-500/20 before:rounded-xl before:blur-lg before:opacity-0 
                   before:group-hover:opacity-100 before:transition-all before:duration-500"
          >
            <PlusIcon class="h-5 w-5 mr-2 group-hover:rotate-90 transition-transform duration-300" />
            Upload Images
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12 relative z-10">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <!-- Welcome Message -->
        <div class="animate-fade-in-up" style="animation-delay: 0.1s">
          <div class="relative">
            <!-- Glow effect -->
            <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 rounded-2xl blur-lg opacity-60"></div>
            
            <!-- Main card -->
            <div class="relative bg-white/8 backdrop-blur-xl border border-white/20 rounded-2xl p-8 
                        shadow-2xl hover:bg-white/12 hover:border-white/30 transition-all duration-500
                        hover:shadow-violet-500/10 hover:shadow-2xl transform hover:scale-[1.02]">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-xl 
                            flex items-center justify-center shadow-lg">
                  <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V3a1 1 0 011 1v1M7 4V3a1 1 0 011-1v0M7 4H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2h-2M7 4h10"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-white">
                    Welcome back, 
                    <span class="bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">
                      {{ user.name }}
                    </span>!
                  </h3>
                  <p class="mt-1 text-slate-300">
                    Here's what's happening with your 
                    <span class="text-violet-300 font-medium">Visual Gallery</span> 
                    today.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Storage Usage -->
        <div class="animate-fade-in-up" style="animation-delay: 0.2s">
          <StorageUsageCard
            :used="storageUsed"
            :quota="storageQuota"
            :percentage="storageUsagePercentage"
          />
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 animate-fade-in-up" 
             style="animation-delay: 0.3s">
          <div class="animate-slide-in-up" style="animation-delay: 0.4s">
            <StatCard
              title="My Images"
              :value="stats.myImages || 0"
              icon="PhotoIcon"
              :href="route('my.images')"
            />
          </div>
          <div class="animate-slide-in-up" style="animation-delay: 0.5s">
            <StatCard
              title="My Albums"
              :value="stats.myAlbums || 0"
              icon="FolderIcon"
              :href="route('my.albums')"
            />
          </div>
          <div class="animate-slide-in-up" style="animation-delay: 0.6s">
            <StatCard
              title="Total Views"
              :value="formatNumber(stats.totalViews || 0)"
              icon="EyeIcon"
            />
          </div>
          <div class="animate-slide-in-up" style="animation-delay: 0.7s">
            <StatCard
              title="Total Likes"
              :value="formatNumber(stats.totalLikes || 0)"
              icon="HeartIcon"
            />
          </div>
        </div>

        <!-- Admin Stats (if admin) -->
        <div v-if="isAdmin" 
             class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 animate-fade-in-up" 
             style="animation-delay: 0.4s">
          <div class="animate-slide-in-up" style="animation-delay: 0.5s">
            <StatCard
              title="Total Users"
              :value="stats.totalUsers || 0"
              icon="UsersIcon"
              color="green"
              :href="route('admin.users.index')"
            />
          </div>
          <div class="animate-slide-in-up" style="animation-delay: 0.6s">
            <StatCard
              title="Total Images"
              :value="stats.totalImages || 0"
              icon="PhotoIcon"
              color="green"
            />
          </div>
          <div class="animate-slide-in-up" style="animation-delay: 0.7s">
            <StatCard
              title="Pending Comments"
              :value="stats.pendingComments || 0"
              icon="ChatBubbleLeftRightIcon"
              color="yellow"
              :href="route('admin.moderation.comments')"
            />
          </div>
          <div class="animate-slide-in-up" style="animation-delay: 0.8s">
            <StatCard
              title="System Health"
              :value="systemStatus.overall || 'Unknown'"
              icon="ShieldCheckIcon"
              :color="systemStatus.color || 'gray'"
              :href="route('admin.system.index')"
            />
          </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          <!-- Recent Images -->
          <div class="animate-fade-in-up" style="animation-delay: 0.5s">
            <div class="relative group">
              <!-- Glow effect -->
              <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/20 to-purple-500/20 
                          rounded-2xl blur-lg opacity-60 group-hover:opacity-100 transition-all duration-500"></div>
              
              <!-- Main card -->
              <div class="relative bg-white/8 backdrop-blur-xl border border-white/20 rounded-2xl 
                          shadow-2xl hover:bg-white/12 hover:border-white/30 transition-all duration-500
                          hover:shadow-violet-500/10 hover:shadow-2xl transform hover:scale-[1.01]
                          overflow-hidden">
                <div class="p-8">
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 bg-gradient-to-r from-violet-500 to-purple-500 rounded-xl 
                                  flex items-center justify-center">
                        <PhotoIcon class="h-5 w-5 text-white" />
                      </div>
                      <h3 class="text-xl font-bold text-white">Recent Images</h3>
                    </div>
                    <Link
                      :href="route('my.images')"
                      class="text-sm bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent 
                             hover:from-white hover:to-white transition-all duration-300 font-semibold
                             hover:scale-105 transform"
                    >
                      View all
                    </Link>
                  </div>
                  
                  <div v-if="recentImages.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div
                      v-for="(image, index) in recentImages.slice(0, 6)"
                      :key="image.id"
                      class="group relative aspect-square bg-white/5 rounded-xl overflow-hidden 
                             hover:shadow-lg hover:shadow-violet-500/20 transition-all duration-500
                             transform hover:scale-105 border border-white/10 hover:border-white/20"
                      :style="{ animationDelay: (index * 0.1) + 's' }"
                    >
                      <Link :href="route('images.show', image.slug)">
                        <img
                          :src="getImageUrl(image, 'small')"
                          :alt="image.alt_text || image.title"
                          class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent 
                                    opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                      </Link>
                    </div>
                  </div>
                  
                  <div v-else class="text-center py-12">
                    <div class="w-16 h-16 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 
                                rounded-2xl flex items-center justify-center mx-auto mb-4">
                      <PhotoIcon class="h-8 w-8 text-slate-400" />
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">No images yet</h3>
                    <p class="text-slate-300 mb-6">Get started by uploading your first image.</p>
                    <div>
                      <Link
                        :href="route('upload')"
                        class="inline-flex items-center px-6 py-3 border border-transparent 
                               rounded-xl font-semibold text-sm text-white 
                               bg-gradient-to-r from-violet-600 to-cyan-600 
                               hover:from-violet-700 hover:to-cyan-700
                               focus:outline-none focus:ring-4 focus:ring-violet-500/30
                               transition-all duration-300 hover:scale-105 transform
                               shadow-lg hover:shadow-violet-500/30"
                      >
                        <PlusIcon class="h-5 w-5 mr-2" />
                        Upload Images
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Albums -->
          <div class="animate-fade-in-up" style="animation-delay: 0.6s">
            <div class="relative group">
              <!-- Glow effect -->
              <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-500/20 to-purple-500/20 
                          rounded-2xl blur-lg opacity-60 group-hover:opacity-100 transition-all duration-500"></div>
              
              <!-- Main card -->
              <div class="relative bg-white/8 backdrop-blur-xl border border-white/20 rounded-2xl 
                          shadow-2xl hover:bg-white/12 hover:border-white/30 transition-all duration-500
                          hover:shadow-cyan-500/10 hover:shadow-2xl transform hover:scale-[1.01]
                          overflow-hidden">
                <div class="p-8">
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 bg-gradient-to-r from-cyan-500 to-purple-500 rounded-xl 
                                  flex items-center justify-center">
                        <FolderIcon class="h-5 w-5 text-white" />
                      </div>
                      <h3 class="text-xl font-bold text-white">Recent Albums</h3>
                    </div>
                    <Link
                      :href="route('my.albums')"
                      class="text-sm bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent 
                             hover:from-white hover:to-white transition-all duration-300 font-semibold
                             hover:scale-105 transform"
                    >
                      View all
                    </Link>
                  </div>
                  
                  <div v-if="recentAlbums.length > 0" class="space-y-4">
                    <div
                      v-for="(album, index) in recentAlbums.slice(0, 4)"
                      :key="album.id"
                      class="flex items-center p-4 bg-white/5 border border-white/10 rounded-xl 
                             hover:bg-white/10 hover:border-white/20 hover:shadow-lg 
                             hover:shadow-cyan-500/10 transition-all duration-500 group
                             transform hover:scale-[1.02]"
                      :style="{ animationDelay: (index * 0.1) + 's' }"
                    >
                      <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-cyan-500/20 to-purple-500/20 
                                    rounded-xl flex items-center justify-center border border-white/10">
                          <FolderIcon class="h-6 w-6 text-cyan-400 group-hover:text-white transition-colors duration-300" />
                        </div>
                      </div>
                      <div class="ml-4 flex-1 min-w-0">
                        <Link
                          :href="route('albums.show', album.slug)"
                          class="text-base font-bold text-white hover:text-cyan-300 
                                 transition-colors duration-300"
                        >
                          {{ album.title }}
                        </Link>
                        <p class="text-sm text-slate-400 mt-1 group-hover:text-slate-300 transition-colors duration-300">
                          {{ album.images_count }} image{{ album.images_count !== 1 ? 's' : '' }}
                        </p>
                      </div>
                      <div class="text-xs text-slate-500 group-hover:text-slate-400 transition-colors duration-300">
                        {{ formatDate(album.updated_at) }}
                      </div>
                    </div>
                  </div>
                  
                  <div v-else class="text-center py-12">
                    <div class="w-16 h-16 bg-gradient-to-r from-cyan-500/20 to-purple-500/20 
                                rounded-2xl flex items-center justify-center mx-auto mb-4">
                      <FolderIcon class="h-8 w-8 text-slate-400" />
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">No albums yet</h3>
                    <p class="text-slate-300 mb-6">Create your first album to organize your images.</p>
                    <div>
                      <Link
                        :href="route('albums.create')"
                        class="inline-flex items-center px-6 py-3 border border-transparent 
                               rounded-xl font-semibold text-sm text-white 
                               bg-gradient-to-r from-cyan-600 to-purple-600 
                               hover:from-cyan-700 hover:to-purple-700
                               focus:outline-none focus:ring-4 focus:ring-cyan-500/30
                               transition-all duration-300 hover:scale-105 transform
                               shadow-lg hover:shadow-cyan-500/30"
                      >
                        <PlusIcon class="h-5 w-5 mr-2" />
                        Create Album
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity (for admins) -->
        <div v-if="isAdmin && recentActivities.length > 0" 
             class="animate-fade-in-up" style="animation-delay: 0.7s">
          <div class="relative group">
            <!-- Glow effect -->
            <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-500/20 to-violet-500/20 
                        rounded-2xl blur-lg opacity-60 group-hover:opacity-100 transition-all duration-500"></div>
            
            <!-- Main card -->
            <div class="relative bg-white/8 backdrop-blur-xl border border-white/20 rounded-2xl 
                        shadow-2xl hover:bg-white/12 hover:border-white/30 transition-all duration-500
                        hover:shadow-purple-500/10 hover:shadow-2xl transform hover:scale-[1.01]
                        overflow-hidden">
              <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                  <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-violet-500 rounded-xl 
                              flex items-center justify-center">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                  </div>
                  <h3 class="text-xl font-bold text-white">Recent Activity</h3>
                </div>
                
                <div class="flow-root">
                  <ul role="list" class="-mb-8">
                    <li
                      v-for="(activity, index) in recentActivities.slice(0, 5)"
                      :key="activity.id"
                      class="relative pb-8 animate-slide-in-up"
                      :style="{ animationDelay: (index * 0.1) + 's' }"
                    >
                      <span
                        v-if="index !== recentActivities.slice(0, 5).length - 1"
                        class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gradient-to-b 
                               from-violet-500/50 to-cyan-500/50"
                        aria-hidden="true"
                      ></span>
                      <div class="relative flex space-x-3">
                        <div class="flex-shrink-0">
                          <div class="w-8 h-8 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 
                                      rounded-full flex items-center justify-center border border-white/20">
                            <component
                              :is="getActivityIcon(activity.type)"
                              class="h-4 w-4 text-violet-400"
                            />
                          </div>
                        </div>
                        <div class="min-w-0 flex-1">
                          <p class="text-sm text-slate-300 leading-relaxed">
                            {{ activity.message }}
                            <span class="text-slate-400 font-medium">{{ formatTimeAgo(activity.timestamp) }}</span>
                          </p>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Modal -->
    <div v-if="showRequestModal" class="fixed inset-0 z-50 overflow-y-auto">
      <!-- Background overlay with blur -->
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-500"></div>
      
      <!-- Modal content -->
      <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative animate-modal-enter">
          <!-- Glow effect -->
          <div class="absolute -inset-1 bg-gradient-to-r from-violet-500/40 to-cyan-500/40 
                      rounded-3xl blur-xl opacity-60"></div>
          
          <!-- Main modal -->
          <div class="relative bg-white/10 backdrop-blur-2xl border border-white/20 
                      rounded-3xl shadow-2xl max-w-md w-full mx-4 p-8
                      hover:bg-white/15 hover:border-white/30 transition-all duration-500">
            <div class="text-center">
              <!-- Icon -->
              <div class="mx-auto flex items-center justify-center w-16 h-16 
                          bg-gradient-to-r from-violet-500 to-cyan-500 rounded-2xl mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              
              <!-- Title -->
              <h2 class="text-2xl font-black text-white mb-4">
                <span class="bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent">
                  Upload Access Required
                </span>
              </h2>
              
              <!-- Description -->
              <p class="text-slate-300 mb-8 leading-relaxed">
                You need Editor permissions to upload images. Request access from an administrator?
              </p>
              
              <!-- Buttons -->
              <div class="flex justify-center space-x-4">
                <button 
                  @click="requestAccess" 
                  :disabled="form.processing"
                  class="relative group px-6 py-3 bg-gradient-to-r from-violet-600 to-cyan-600 
                         text-white font-bold rounded-xl hover:from-violet-700 hover:to-cyan-700
                         focus:outline-none focus:ring-4 focus:ring-violet-500/30
                         transition-all duration-300 hover:scale-105 transform
                         shadow-lg hover:shadow-violet-500/30 disabled:opacity-50"
                >
                  <span v-if="form.processing" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12a8 8 0 003.657 6.746l1.021-.408A6 6 0 016 12z"></path>
                    </svg>
                    Requesting...
                  </span>
                  <span v-else>Request Access</span>
                </button>
                
                <button 
                  @click="closeModal"
                  class="px-6 py-3 bg-white/10 border border-white/20 text-white font-bold 
                         rounded-xl hover:bg-white/20 hover:border-white/30
                         focus:outline-none focus:ring-4 focus:ring-white/20
                         transition-all duration-300 hover:scale-105 transform"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatCard from '@/Components/StatCard.vue'
import StorageUsageCard from '@/Components/StorageUsageCard.vue'
import {
  PhotoIcon,
  FolderIcon,
  PlusIcon,
  EyeIcon,
  HeartIcon,
  UsersIcon,
  ChatBubbleLeftRightIcon,
  ShieldCheckIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  user: Object,
  stats: Object,
  recentImages: Array,
  recentAlbums: Array,
  recentActivities: Array,
  storageUsed: Number,
  storageQuota: Number,
  canUpload: Boolean,
  isAdmin: Boolean,
  systemStatus: Object,
})

// Reactive data
const showRequestModal = ref(false)

// Form for requesting access
const form = useForm({})

// Computed properties
const storageUsagePercentage = computed(() => {
  if (!props.storageQuota || props.storageQuota === 0) return 0
  return Math.round((props.storageUsed / props.storageQuota) * 100)
})

// Methods
const formatNumber = (number) => {
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  }
  if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number?.toString() || '0'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

const formatTimeAgo = (timestamp) => {
  const now = new Date()
  const time = new Date(timestamp)
  const diffInSeconds = Math.floor((now - time) / 1000)
  
  if (diffInSeconds < 60) return 'just now'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`
  return `${Math.floor(diffInSeconds / 86400)}d ago`
}

const getImageUrl = (image, size = 'medium') => {
  // This would typically be a helper function to generate image URLs
  return image.url || `/storage/images/${image.filename}`
}

const getActivityIcon = (type) => {
  const icons = {
    'upload': PhotoIcon,
    'album': FolderIcon,
    'user': UsersIcon,
    'comment': ChatBubbleLeftRightIcon,
    'system': ShieldCheckIcon,
  }
  return icons[type] || PhotoIcon
}

const requestAccess = () => {
  form.post(route('request.upload.access'), {
    onSuccess: () => {
      showRequestModal.value = false
      // Could show a success toast here
    },
    onError: () => {
      // Could show an error toast here
    }
  })
}

const closeModal = () => {
  showRequestModal.value = false
}

// Lifecycle
onMounted(() => {
  // Any initialization logic here
})
</script>

<style scoped>
@keyframes pulse-slow {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 0.8; }
}

@keyframes float-organic {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
  }
  33% {
    transform: translateY(-10px) translateX(5px) rotate(1deg);
  }
  66% {
    transform: translateY(5px) translateX(-5px) rotate(-1deg);
  }
}

@keyframes fade-in-down {
  0% {
    opacity: 0;
    transform: translateY(-20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
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

@keyframes shimmer-enhanced {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

@keyframes modal-enter {
  0% {
    opacity: 0;
    transform: scale(0.8) translateY(20px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.animate-float-organic {
  animation: float-organic 8s ease-in-out infinite;
}

.animate-fade-in-down {
  animation: fade-in-down 0.8s ease-out forwards;
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
  opacity: 0;
}

.animate-slide-in-up {
  animation: slide-in-up 0.6s ease-out forwards;
  opacity: 0;
}

.animate-shimmer-enhanced {
  animation: shimmer-enhanced 3s ease-in-out infinite;
  background-size: 300% 300%;
}

.animate-modal-enter {
  animation: modal-enter 0.3s ease-out forwards;
}

.bg-size-300 {
  background-size: 300% 300%;
}

.bg-pos-0 {
  background-position: 0% 50%;
}

.delay-1000 {
  animation-delay: 1s;
}

.delay-2000 {
  animation-delay: 2s;
}
</style>