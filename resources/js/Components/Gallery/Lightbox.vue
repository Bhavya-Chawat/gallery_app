<template>
  <Teleport to="body">
    <!-- Enhanced Premium Backdrop with Advanced Animations -->
    <div
      class="fixed inset-0 z-[100] flex items-center justify-center backdrop-blur-xl cursor-default"
      @click="handleBackdropClick"
      @keydown="handleKeydown"
      tabindex="0"
      ref="lightboxRef"
      style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.96) 0%, rgba(30, 27, 75, 0.96) 25%, rgba(15, 23, 42, 0.98) 50%, rgba(30, 27, 75, 0.96) 75%, rgba(15, 23, 42, 0.96) 100%);"
    >
      <!-- Advanced Animated Mesh Background -->
      <div class="absolute inset-0 opacity-40 overflow-hidden">
        <svg class="w-full h-full animate-mesh-flow" viewBox="0 0 200 100" preserveAspectRatio="none">
          <defs>
            <linearGradient id="meshGrad" x1="0%" y1="0%" x2="100%" y2="100%" gradientUnits="objectBoundingBox">
              <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0.4">
                <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="10s" repeatCount="indefinite"/>
                <animate attributeName="stop-opacity" values="0.4;0.6;0.3;0.4" dur="8s" repeatCount="indefinite"/>
              </stop>
              <stop offset="35%" style="stop-color:#06b6d4;stop-opacity:0.3">
                <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="10s" repeatCount="indefinite"/>
                <animate attributeName="stop-opacity" values="0.3;0.5;0.2;0.3" dur="12s" repeatCount="indefinite"/>
              </stop>
              <stop offset="65%" style="stop-color:#a855f7;stop-opacity:0.35">
                <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#06b6d4;#a855f7" dur="10s" repeatCount="indefinite"/>
                <animate attributeName="stop-opacity" values="0.35;0.55;0.25;0.35" dur="9s" repeatCount="indefinite"/>
              </stop>
              <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:0.4">
                <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="10s" repeatCount="indefinite"/>
                <animate attributeName="stop-opacity" values="0.4;0.6;0.3;0.4" dur="11s" repeatCount="indefinite"/>
              </stop>
            </linearGradient>
            
            <filter id="meshBlur" x="-50%" y="-50%" width="200%" height="200%">
              <feGaussianBlur in="SourceGraphic" stdDeviation="2">
                <animate attributeName="stdDeviation" values="2;4;2" dur="8s" repeatCount="indefinite"/>
              </feGaussianBlur>
            </filter>
          </defs>
          
          <rect width="100%" height="100%" fill="url(#meshGrad)" filter="url(#meshBlur)"/>
          <path d="M0,0 Q50,40 100,0 T200,0 L200,100 Q150,60 100,100 T0,100 Z" fill="url(#meshGrad)" opacity="0.6" filter="url(#meshBlur)">
            <animateTransform attributeName="transform" type="translate" values="0,0; 10,5; -5,3; 0,0" dur="15s" repeatCount="indefinite"/>
          </path>
          <path d="M0,100 Q50,60 100,100 T200,100 L200,0 Q150,40 100,0 T0,0 Z" fill="url(#meshGrad)" opacity="0.4" filter="url(#meshBlur)">
            <animateTransform attributeName="transform" type="translate" values="0,0; -8,-3; 6,-2; 0,0" dur="18s" repeatCount="indefinite"/>
          </path>
        </svg>
      </div>

      <!-- Enhanced Floating Particle System -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div v-for="i in 25" :key="`particle-${i}`" 
             class="absolute rounded-full animate-float-organic"
             :class="[
               i % 5 === 0 ? 'w-2 h-2' : i % 3 === 0 ? 'w-1.5 h-1.5' : 'w-1 h-1',
               i % 4 === 0 ? 'bg-violet-400/60' : 
               i % 4 === 1 ? 'bg-cyan-400/50' : 
               i % 4 === 2 ? 'bg-purple-400/55' : 'bg-violet-300/45'
             ]"
             :style="{
               left: Math.random() * 100 + '%',
               top: Math.random() * 100 + '%',
               animationDelay: Math.random() * 12 + 's',
               animationDuration: (10 + Math.random() * 8) + 's',
               filter: `blur(${Math.random() * 0.5 + 0.5}px)`
             }">
        </div>
      </div>

      <!-- Premium Loading Indicator -->
      <div
        v-if="loading"
        class="absolute inset-0 flex items-center justify-center z-10 bg-slate-900/80 backdrop-blur-sm"
      >
        <div class="relative">
          <div class="w-20 h-20 border-4 border-violet-500/30 border-t-violet-500 rounded-full animate-spin shadow-lg"></div>
          <div class="absolute inset-2 w-16 h-16 border-4 border-transparent border-t-cyan-400 rounded-full animate-spin animation-delay-300"></div>
          <div class="absolute inset-4 w-12 h-12 border-2 border-transparent border-t-purple-400 rounded-full animate-spin animation-delay-600"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-8 h-8 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-full animate-pulse"></div>
          </div>
        </div>
      </div>

      <!-- Enhanced Premium Lightbox Modal -->
      <div 
        class="relative bg-gradient-to-br from-slate-900/95 via-slate-800/95 to-slate-900/95 backdrop-blur-3xl rounded-3xl shadow-2xl border border-white/20 max-w-7xl w-full max-h-[96vh] overflow-hidden transition-all duration-700 ease-out animate-modal-entrance"
        :class="loading ? 'opacity-0 scale-95 blur-sm' : 'opacity-100 scale-100'"
        style="box-shadow: 0 25px 80px -12px rgba(139, 92, 246, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.1);"
      >
        
        <!-- Enhanced Premium Header -->
        <div class="flex items-center justify-between p-6 border-b border-white/20 bg-gradient-to-r from-slate-800/60 via-slate-700/60 to-slate-800/60 backdrop-blur-xl relative overflow-hidden">
          <!-- Header background effect -->
          <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 animate-shimmer-flow"></div>
          
          <div class="flex items-center space-x-4 min-w-0 flex-1 relative z-10">
            <!-- Enhanced Image counter -->
            <span v-if="images && images.length > 1" 
                  class="text-sm font-bold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent flex-shrink-0 px-4 py-2 rounded-full bg-gradient-to-r from-white/10 to-white/5 border border-white/20 backdrop-blur-sm shadow-lg animate-glow-pulse">
              {{ currentIndex + 1 }} of {{ images.length }}
            </span>
            <div class="h-8 w-px bg-gradient-to-b from-violet-500/60 via-cyan-500/60 to-purple-500/60 flex-shrink-0 animate-color-shift" 
                 v-if="images && images.length > 1"></div>
            <h3 class="text-xl font-black bg-gradient-to-r from-white via-slate-100 to-white bg-clip-text text-transparent truncate animate-title-shimmer drop-shadow-sm">
              {{ currentImage?.title || 'Untitled Image' }}
            </h3>
          </div>
          
          <!-- Enhanced Premium Header Actions -->
          <div class="flex items-center space-x-3 flex-shrink-0 relative z-10">
            <!-- Premium Zoom Controls -->
            <div class="flex items-center space-x-2 bg-gradient-to-r from-white/10 to-white/5 backdrop-blur-xl border border-white/20 rounded-2xl p-3 shadow-lg hover:shadow-xl transition-all duration-300 hover:bg-gradient-to-r hover:from-white/15 hover:to-white/10">
              <button
                @click="zoomOut"
                :disabled="zoomLevel <= 0.5"
                class="p-2 rounded-xl bg-gradient-to-br from-white/15 to-white/5 hover:from-violet-500/30 hover:to-violet-500/20 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 hover:scale-110 group shadow-md hover:shadow-lg"
                title="Zoom Out (-)"
              >
                <MinusIcon class="h-4 w-4 text-slate-200 group-hover:text-violet-200 transition-colors drop-shadow-sm" />
              </button>
              <div class="px-3 py-1.5 bg-slate-800/70 backdrop-blur-sm rounded-lg border border-white/10 min-w-[3.5rem] text-center">
                <span class="text-xs text-slate-200 font-mono font-bold">{{ Math.round(zoomLevel * 100) }}%</span>
              </div>
              <button
                @click="zoomIn"
                :disabled="zoomLevel >= 3"
                class="p-2 rounded-xl bg-gradient-to-br from-white/15 to-white/5 hover:from-cyan-500/30 hover:to-cyan-500/20 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 hover:scale-110 group shadow-md hover:shadow-lg"
                title="Zoom In (+)"
              >
                <PlusIcon class="h-4 w-4 text-slate-200 group-hover:text-cyan-200 transition-colors drop-shadow-sm" />
              </button>
              <button
                @click="resetZoom"
                class="px-3 py-2 rounded-xl bg-gradient-to-br from-white/15 to-white/5 hover:from-purple-500/30 hover:to-purple-500/20 transition-all duration-300 hover:scale-105 shadow-md hover:shadow-lg"
                title="Reset Zoom (0)"
              >
                <span class="text-xs text-slate-200 font-mono font-bold drop-shadow-sm">1:1</span>
              </button>
            </div>

            <div class="h-10 w-px bg-gradient-to-b from-transparent via-white/30 to-transparent shadow-sm"></div>

            <!-- Premium Navigation Controls -->
            <div v-if="images && images.length > 1" 
                 class="flex items-center space-x-2 bg-gradient-to-r from-white/10 to-white/5 backdrop-blur-xl border border-white/20 rounded-2xl p-3 shadow-lg hover:shadow-xl transition-all duration-300">
              <button
                @click="previousImage"
                :disabled="currentIndex === 0"
                class="p-2 rounded-xl bg-gradient-to-br from-white/15 to-white/5 hover:from-violet-500/30 hover:to-violet-500/20 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 hover:scale-110 group shadow-md hover:shadow-lg"
                title="Previous (←)"
              >
                <ChevronLeftIcon class="h-5 w-5 text-slate-200 group-hover:text-violet-200 transition-colors drop-shadow-sm" />
              </button>
              <button
                @click="nextImage"
                :disabled="currentIndex >= images.length - 1"
                class="p-2 rounded-xl bg-gradient-to-br from-white/15 to-white/5 hover:from-cyan-500/30 hover:to-cyan-500/20 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 hover:scale-110 group shadow-md hover:shadow-lg"
                title="Next (→)"
              >
                <ChevronRightIcon class="h-5 w-5 text-slate-200 group-hover:text-cyan-200 transition-colors drop-shadow-sm" />
              </button>
            </div>

            <div class="h-10 w-px bg-gradient-to-b from-transparent via-white/30 to-transparent shadow-sm"></div>
            
            <!-- FIXED: Custom Actions Dropdown with Teleport -->
            <div class="relative" ref="dropdownRef">
              <button 
                @click="toggleActionsDropdown"
                class="p-3 rounded-2xl bg-gradient-to-br from-white/15 to-white/5 hover:from-purple-500/30 hover:to-purple-500/20 backdrop-blur-xl border border-white/20 hover:border-purple-400/40 transition-all duration-300 hover:scale-110 group shadow-lg hover:shadow-xl" 
                title="More Actions"
              >
                <EllipsisVerticalIcon class="h-5 w-5 text-slate-200 group-hover:text-purple-200 transition-colors drop-shadow-sm" />
              </button>
              
              <!-- Teleported Dropdown Menu -->
              <Teleport to="body">
                <div 
                  v-if="showActionsDropdown" 
                  class="fixed z-[200] min-w-[220px]"
                  :style="actionsDropdownStyle"
                  @click.stop
                >
                  <div class="bg-slate-900/98 backdrop-blur-3xl border-2 border-white/30 rounded-3xl shadow-2xl overflow-hidden animate-dropdown-entrance"
                       style="box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(255, 255, 255, 0.2); backdrop-filter: blur(40px);">
                    <!-- Dropdown background effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-violet-500/15 via-transparent to-cyan-500/15"></div>
                    
                    <div class="relative z-10">
                      <button v-if="currentImage?.id" @click="viewFullPage" 
                              class="w-full flex items-center px-5 py-4 text-slate-200 hover:bg-gradient-to-r hover:from-violet-500/25 hover:to-violet-500/15 hover:text-violet-200 transition-all duration-300 border-b border-white/10 text-left group">
                        <EyeIcon class="h-5 w-5 mr-4 group-hover:scale-110 transition-transform duration-300" />
                        <span class="font-medium">View Full Page</span>
                      </button>
                      <button
                        v-if="currentImage?.can?.update"
                        @click="editImage"
                        class="w-full flex items-center px-5 py-4 text-slate-200 hover:bg-gradient-to-r hover:from-cyan-500/25 hover:to-cyan-500/15 hover:text-cyan-200 transition-all duration-300 border-b border-white/10 text-left group"
                      >
                        <PencilIcon class="h-5 w-5 mr-4 group-hover:scale-110 transition-transform duration-300" />
                        <span class="font-medium">Edit Image</span>
                      </button>
                      <button
                        v-if="currentImage?.can?.download"
                        @click="downloadImage"
                        class="w-full flex items-center px-5 py-4 text-slate-200 hover:bg-gradient-to-r hover:from-purple-500/25 hover:to-purple-500/15 hover:text-purple-200 transition-all duration-300 border-b border-white/10 text-left group"
                      >
                        <ArrowDownTrayIcon class="h-5 w-5 mr-4 group-hover:scale-110 transition-transform duration-300" />
                        <span class="font-medium">Download</span>
                      </button>
                      <button @click="copyLink" 
                              class="w-full flex items-center px-5 py-4 text-slate-200 hover:bg-gradient-to-r hover:from-violet-500/25 hover:to-violet-500/15 hover:text-violet-200 transition-all duration-300 border-b border-white/10 text-left group">
                        <LinkIcon class="h-5 w-5 mr-4 group-hover:scale-110 transition-transform duration-300" />
                        <span class="font-medium">Copy Link</span>
                      </button>
                      <button @click="shareImage" 
                              class="w-full flex items-center px-5 py-4 text-slate-200 hover:bg-gradient-to-r hover:from-cyan-500/25 hover:to-cyan-500/15 hover:text-cyan-200 transition-all duration-300 text-left group">
                        <ShareIcon class="h-5 w-5 mr-4 group-hover:scale-110 transition-transform duration-300" />
                        <span class="font-medium">Share</span>
                      </button>
                    </div>
                  </div>
                </div>
              </Teleport>
            </div>
            
            <!-- Enhanced Premium Close Button -->
            <button
              @click="$emit('close')"
              class="p-3 rounded-2xl bg-gradient-to-br from-red-500/20 to-red-500/10 hover:from-red-500/30 hover:to-red-500/20 backdrop-blur-xl border border-red-400/30 hover:border-red-400/50 transition-all duration-300 hover:scale-110 group shadow-lg hover:shadow-xl"
              title="Close (Esc)"
            >
              <XMarkIcon class="h-5 w-5 text-red-200 group-hover:text-red-100 transition-colors drop-shadow-sm" />
            </button>
          </div>
        </div>

        <!-- Enhanced Main Content Area -->
        <div class="flex flex-col lg:flex-row" :style="{ height: contentHeight }">
          <!-- Premium Image Display Area -->
          <div 
            class="flex-1 flex items-center justify-center bg-gradient-to-br from-slate-900/80 via-slate-800/80 to-slate-900/80 relative overflow-hidden group cursor-default"
            @wheel="handleWheel"
            @mousedown="startPan"
            @mousemove="handlePan"
            @mouseup="endPan"
            @mouseleave="endPan"
            ref="imageContainer"
            :class="{ 'cursor-grab': !isPanning && zoomLevel > 1, 'cursor-grabbing': isPanning }"
          >
            <!-- Image container with enhanced effects -->
            <div class="relative">
              <img
                v-if="currentImage"
                :src="currentImageUrl"
                :alt="currentImage.alt_text || currentImage.title"
                class="max-w-none transition-all duration-500 ease-out select-none rounded-2xl shadow-2xl border border-white/20"
                :style="imageStyle"
                @click.stop
                @load="handleImageLoad"
                @error="handleImageError"
                draggable="false"
                style="filter: drop-shadow(0 25px 50px rgba(0, 0, 0, 0.5));"
              >
              
              <!-- Image loading overlay -->
              <div v-if="loading" 
                   class="absolute inset-0 bg-slate-800/80 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <div class="text-slate-300 text-sm animate-pulse">Loading image...</div>
              </div>
            </div>
            
            <!-- Enhanced Navigation Overlay Buttons -->
            <button
              v-if="images && images.length > 1 && currentIndex > 0"
              @click="previousImage"
              class="absolute left-8 top-1/2 -translate-y-1/2 p-5 rounded-3xl bg-gradient-to-br from-black/40 to-black/60 hover:from-violet-500/40 hover:to-violet-500/60 backdrop-blur-xl border border-white/20 hover:border-violet-400/60 text-white transition-all duration-300 opacity-0 group-hover:opacity-100 hover:scale-110 shadow-2xl group-nav"
              title="Previous Image (←)"
            >
              <ChevronLeftIcon class="h-10 w-10 drop-shadow-lg" />
            </button>
            
            <button
              v-if="images && images.length > 1 && currentIndex < images.length - 1"
              @click="nextImage"
              class="absolute right-8 top-1/2 -translate-y-1/2 p-5 rounded-3xl bg-gradient-to-br from-black/40 to-black/60 hover:from-cyan-500/40 hover:to-cyan-500/60 backdrop-blur-xl border border-white/20 hover:border-cyan-400/60 text-white transition-all duration-300 opacity-0 group-hover:opacity-100 hover:scale-110 shadow-2xl group-nav"
              title="Next Image (→)"
            >
              <ChevronRightIcon class="h-10 w-10 drop-shadow-lg" />
            </button>

            <!-- Enhanced Image Info Overlay -->
            <div 
              v-if="showInfo && currentImage"
              class="absolute top-8 left-8 bg-gradient-to-br from-black/60 to-black/80 backdrop-blur-2xl border border-white/30 text-white px-6 py-4 rounded-2xl text-sm animate-fade-in shadow-2xl"
              style="backdrop-filter: blur(20px);"
            >
              <div class="text-violet-300 font-bold mb-1">{{ getImageDimensions(currentImage) }}</div>
              <div class="text-cyan-300 font-medium">{{ getImageSize(currentImage) }}</div>
            </div>
          </div>

          <!-- Enhanced Premium Sidebar -->
          <div 
            v-if="showSidebar && currentImage"
            class="lg:w-80 bg-gradient-to-b from-slate-800/90 via-slate-700/90 to-slate-800/90 backdrop-blur-3xl border-t lg:border-t-0 lg:border-l border-white/20 overflow-y-auto transition-all duration-500 relative"
            :class="{ 'absolute inset-x-0 bottom-0 top-auto max-h-1/2 lg:relative lg:max-h-none animate-slide-up-enhanced': isMobile }"
          >
            <!-- Sidebar background effect -->
            <div class="absolute inset-0 bg-gradient-to-b from-violet-500/5 via-transparent to-cyan-500/5"></div>
            
            <div class="p-6 relative z-10">
              <!-- Enhanced Mobile Toggle Button -->
              <button
                v-if="isMobile"
                @click="showSidebar = false"
                class="absolute top-4 right-4 p-2.5 rounded-2xl bg-gradient-to-br from-white/15 to-white/5 hover:from-red-500/30 hover:to-red-500/20 backdrop-blur-xl border border-white/20 hover:border-red-400/40 transition-all duration-300 lg:hidden group shadow-lg hover:shadow-xl z-20"
              >
                <XMarkIcon class="h-5 w-5 text-slate-200 group-hover:text-red-200 transition-colors" />
              </button>

              <!-- Enhanced Owner Information -->
              <div v-if="currentImage.owner" class="flex items-center space-x-4 mb-8 animate-fade-in-up-enhanced">
                <div class="w-14 h-14 bg-gradient-to-br from-violet-500 via-purple-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-xl border-2 border-white/20">
                  <span class="text-lg font-black text-white drop-shadow-sm">
                    {{ currentImage.owner.name?.charAt(0)?.toUpperCase() || '?' }}
                  </span>
                </div>
                <div>
                  <p class="text-lg font-bold bg-gradient-to-r from-slate-100 to-slate-200 bg-clip-text text-transparent">
                    {{ currentImage.owner.name || 'Unknown User' }}
                  </p>
                  <p class="text-sm text-slate-400 font-medium">{{ formatDate(currentImage.created_at) }}</p>
                </div>
              </div>

              <!-- Enhanced Caption Section -->
              <div v-if="currentImage.caption" class="mb-8 animate-fade-in-up-enhanced animation-delay-200">
                <h4 class="text-sm font-bold bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent mb-3 uppercase tracking-wider">Caption</h4>
                <p class="text-slate-200 text-sm leading-relaxed bg-gradient-to-br from-white/10 to-white/5 p-5 rounded-2xl border border-white/20 backdrop-blur-sm shadow-lg">
                  {{ currentImage.caption }}
                </p>
              </div>

              <!-- Enhanced Action Buttons -->
              <div class="flex items-center space-x-3 mb-8 animate-fade-in-up-enhanced animation-delay-300">
                <button
                  v-if="currentImage.can?.download"
                  @click="downloadImage"
                  class="flex items-center space-x-3 px-5 py-3 rounded-2xl bg-gradient-to-r from-violet-500/25 to-purple-500/25 border border-violet-400/40 text-slate-100 hover:from-violet-500/35 hover:to-purple-500/35 hover:border-violet-400/60 hover:text-white transition-all duration-300 text-sm font-bold backdrop-blur-sm hover:scale-105 shadow-lg hover:shadow-xl"
                >
                  <ArrowDownTrayIcon class="h-5 w-5" />
                  <span>Download</span>
                </button>
              </div>

              <!-- Enhanced Metadata Section -->
              <div class="space-y-4 text-sm border-t border-white/20 pt-8 animate-fade-in-up-enhanced animation-delay-400">
                <h4 class="font-bold bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent text-base uppercase tracking-wider mb-6">Details</h4>
                
                <div class="grid grid-cols-1 gap-3">
                  <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-gradient-to-r from-white/8 to-white/4 border border-white/15 backdrop-blur-sm">
                    <span class="text-slate-400 font-medium">Dimensions</span>
                    <span class="text-slate-100 font-bold">{{ getImageDimensions(currentImage) }}</span>
                  </div>
                  
                  <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-gradient-to-r from-white/8 to-white/4 border border-white/15 backdrop-blur-sm">
                    <span class="text-slate-400 font-medium">Size</span>
                    <span class="text-slate-100 font-bold">{{ getImageSize(currentImage) }}</span>
                  </div>
                  
                  <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-gradient-to-r from-white/8 to-white/4 border border-white/15 backdrop-blur-sm">
                    <span class="text-slate-400 font-medium">Views</span>
                    <span class="text-slate-100 font-bold">{{ formatCount(currentImage.views_count || 0) }}</span>
                  </div>

                  <div v-if="currentImage.camera_make" class="flex items-center justify-between py-3 px-4 rounded-xl bg-gradient-to-r from-white/8 to-white/4 border border-white/15 backdrop-blur-sm">
                    <span class="text-slate-400 font-medium">Camera</span>
                    <span class="text-slate-100 font-bold">{{ currentImage.camera_make }} {{ currentImage.camera_model || '' }}</span>
                  </div>

                  <div v-if="currentImage.mime_type" class="flex items-center justify-between py-3 px-4 rounded-xl bg-gradient-to-r from-white/8 to-white/4 border border-white/15 backdrop-blur-sm">
                    <span class="text-slate-400 font-medium">Format</span>
                    <span class="text-slate-100 font-bold">{{ currentImage.mime_type?.split('/')?.pop()?.toUpperCase() || 'Unknown' }}</span>
                  </div>
                </div>
              </div>

              <!-- Enhanced Tags Section -->
              <div v-if="currentImage.tags && currentImage.tags.length" class="mt-8 animate-fade-in-up-enhanced animation-delay-500">
                <h4 class="text-base font-bold bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent mb-4 uppercase tracking-wider">Tags</h4>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="tag in currentImage.tags"
                    :key="tag.id"
                    @click="searchByTag(tag.name)"
                    class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-500/25 to-cyan-500/25 text-cyan-200 border border-cyan-400/40 hover:from-blue-500/35 hover:to-cyan-500/35 hover:border-cyan-400/60 hover:text-cyan-100 transition-all duration-300 backdrop-blur-sm hover:scale-105 shadow-lg hover:shadow-xl"
                  >
                    {{ tag.name }}
                  </button>
                </div>
              </div>

              <!-- Enhanced Album Information -->
              <div v-if="currentImage.album" class="mt-8 animate-fade-in-up-enhanced animation-delay-600">
                <h4 class="text-base font-bold bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent mb-4 uppercase tracking-wider">Album</h4>
                <button
                  @click="goToAlbum"
                  class="flex items-center space-x-3 text-sm bg-gradient-to-r from-purple-500/25 to-violet-500/25 border border-purple-400/40 px-5 py-3 rounded-2xl hover:from-purple-500/35 hover:to-violet-500/35 hover:border-purple-400/60 text-purple-200 hover:text-purple-100 transition-all duration-300 backdrop-blur-sm hover:scale-105 shadow-lg hover:shadow-xl font-bold"
                >
                  <span>{{ currentImage.album.title }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Mobile Bottom Controls -->
        <div v-if="isMobile" class="lg:hidden border-t border-white/20 bg-gradient-to-r from-slate-800/70 via-slate-700/70 to-slate-800/70 backdrop-blur-xl p-5 relative overflow-hidden">
          <!-- Mobile controls background effect -->
          <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5"></div>
          
          <div class="flex items-center justify-between relative z-10">
            <button
              @click="showSidebar = !showSidebar"
              class="flex items-center space-x-3 px-5 py-3 rounded-2xl bg-gradient-to-br from-white/15 to-white/5 border border-white/20 text-slate-200 hover:from-violet-500/25 hover:to-violet-500/15 hover:border-violet-400/50 hover:text-violet-200 transition-all duration-300 backdrop-blur-sm shadow-lg hover:shadow-xl"
            >
              <InformationCircleIcon class="h-5 w-5" />
              <span class="text-sm font-bold">Info</span>
            </button>

            <div class="flex items-center space-x-3">
              <button
                @click="shareImage"
                class="p-3 rounded-2xl bg-gradient-to-br from-white/15 to-white/5 border border-white/20 text-slate-200 hover:from-cyan-500/25 hover:to-cyan-500/15 hover:border-cyan-400/50 hover:text-cyan-200 transition-all duration-300 backdrop-blur-sm hover:scale-110 shadow-lg hover:shadow-xl"
              >
                <ShareIcon class="h-5 w-5" />
              </button>

              <button
                v-if="currentImage?.can?.download"
                @click="downloadImage"
                class="p-3 rounded-2xl bg-gradient-to-br from-white/15 to-white/5 border border-white/20 text-slate-200 hover:from-purple-500/25 hover:to-purple-500/15 hover:border-purple-400/50 hover:text-purple-200 transition-all duration-300 backdrop-blur-sm hover:scale-110 shadow-lg hover:shadow-xl"
              >
                <ArrowDownTrayIcon class="h-5 w-5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Keyboard Shortcuts Help -->
      <div 
        v-if="showHelp"
        class="absolute bottom-6 left-6 bg-gradient-to-br from-black/90 to-black/95 backdrop-blur-3xl border-2 border-white/30 text-white px-8 py-6 rounded-3xl text-sm space-y-3 animate-fade-in-enhanced shadow-2xl max-w-xs"
        style="backdrop-filter: blur(25px);"
      >
        <div class="font-black mb-4 bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent text-base">
          Keyboard Shortcuts
        </div>
        <div class="space-y-2 text-slate-200">
          <div class="flex justify-between"><span>← →</span><span class="text-slate-400">Navigate</span></div>
          <div class="flex justify-between"><span>+ -</span><span class="text-slate-400">Zoom</span></div>
          <div class="flex justify-between"><span>0</span><span class="text-slate-400">Reset zoom</span></div>
          <div class="flex justify-between"><span>F</span><span class="text-slate-400">Fullscreen</span></div>
          <div class="flex justify-between"><span>I</span><span class="text-slate-400">Toggle info</span></div>
          <div class="flex justify-between"><span>H</span><span class="text-slate-400">Toggle help</span></div>
          <div class="flex justify-between"><span>Esc</span><span class="text-slate-400">Close</span></div>
        </div>
      </div>

      <!-- Enhanced Help Toggle -->
      <div class="absolute bottom-6 right-6 flex flex-col items-end space-y-4">
        <div 
          v-if="showHelpNotice"
          class="bg-gradient-to-br from-black/90 to-black/95 backdrop-blur-3xl border-2 border-violet-400/40 text-violet-200 px-5 py-3 rounded-2xl text-sm animate-glow-pulse shadow-2xl font-bold"
          style="backdrop-filter: blur(25px);"
        >
          Press H for help
        </div>
        
        <button
          @click="toggleHelp"
          class="p-4 rounded-3xl bg-gradient-to-br from-black/60 to-black/80 hover:from-violet-500/40 hover:to-violet-500/60 backdrop-blur-3xl border-2 border-white/30 hover:border-violet-400/60 text-white transition-all duration-300 hover:scale-110 shadow-2xl group"
          title="Keyboard Shortcuts (H)"
          style="backdrop-filter: blur(25px);"
        >
          <QuestionMarkCircleIcon class="h-7 w-7 group-hover:text-violet-200 transition-colors drop-shadow-lg" />
        </button>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  XMarkIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ArrowDownTrayIcon,
  PencilIcon,
  EllipsisVerticalIcon,
  PlusIcon,
  MinusIcon,
  EyeIcon,
  LinkIcon,
  ShareIcon,
  InformationCircleIcon,
  QuestionMarkCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  images: {
    type: Array,
    required: true,
    default: () => []
  },
  initialIndex: {
    type: Number,
    default: 0,
  },
})

const emit = defineEmits(['close', 'next', 'prev'])

// Refs
const lightboxRef = ref(null)
const imageContainer = ref(null)
const currentIndex = ref(props.initialIndex)
const loading = ref(true)
const showSidebar = ref(true)
const showInfo = ref(false)
const showHelp = ref(false)
const isFullscreen = ref(false)
const showHelpNotice = ref(true)

// Custom dropdown refs
const showActionsDropdown = ref(false)
const dropdownRef = ref(null)
const actionsDropdownStyle = ref({})

// Zoom and Pan
const zoomLevel = ref(1)
const panX = ref(0)
const panY = ref(0)
const isPanning = ref(false)
const lastPanPoint = ref({ x: 0, y: 0 })

// Responsive
const isMobile = ref(false)

// Computed
const currentImage = computed(() => {
  if (!props.images || props.images.length === 0) return null
  return props.images[currentIndex.value] || null
})

const currentImageUrl = computed(() => {
  if (!currentImage.value) return '/images/placeholder.jpg'
  
  const img = currentImage.value
  if (img.storage_path) {
    return `http://localhost:9000/gallery-images/${img.storage_path}`
  }
  return img.url || '/images/placeholder.jpg'
})

const contentHeight = computed(() => {
  return isFullscreen.value ? '100vh' : 'calc(96vh - 4rem)'
})

const imageStyle = computed(() => ({
  transform: `scale(${zoomLevel.value}) translate(${panX.value}px, ${panY.value}px)`,
  transformOrigin: 'center center',
}))

// Watch for changes with proper validation
watch(() => props.initialIndex, (newIndex) => {
  if (newIndex >= 0 && newIndex < props.images.length) {
    currentIndex.value = newIndex
    resetZoomAndPan()
  }
})

watch(currentImage, () => {
  loading.value = true
  resetZoomAndPan()
}, { immediate: true })

// Methods
const resetZoomAndPan = () => {
  zoomLevel.value = 1
  panX.value = 0
  panY.value = 0
}

const handleImageLoad = () => {
  loading.value = false
}

const handleImageError = () => {
  loading.value = false
  console.error('Failed to load image:', currentImageUrl.value)
}

// Custom dropdown methods
const toggleActionsDropdown = () => {
  if (showActionsDropdown.value) {
    showActionsDropdown.value = false
  } else {
    showActionsDropdown.value = true
    nextTick(() => {
      updateActionsDropdownPosition()
    })
  }
}

const updateActionsDropdownPosition = () => {
  if (dropdownRef.value) {
    const rect = dropdownRef.value.getBoundingClientRect()
    actionsDropdownStyle.value = {
      top: rect.bottom + 8 + 'px',
      right: (window.innerWidth - rect.right) + 'px'
    }
  }
}

const closeActionsDropdown = () => {
  showActionsDropdown.value = false
}

// Helper methods for image info
const getImageDimensions = (image) => {
  if (!image) return 'Unknown'
  if (image.width && image.height) {
    return `${image.width} × ${image.height}`
  }
  return image.dimensions || 'Unknown'
}

const getImageSize = (image) => {
  if (!image) return 'Unknown'
  if (image.formatted_size) {
    return image.formatted_size
  }
  if (image.size_bytes) {
    return formatBytes(image.size_bytes)
  }
  return 'Unknown'
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}

const formatDate = (date) => {
  if (!date) return 'Unknown'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatCount = (count) => {
  if (!count || count === 0) return '0'
  if (count >= 1000000) return Math.floor(count / 1000000) + 'M'
  if (count >= 1000) return Math.floor(count / 1000) + 'K'
  return count.toString()
}

const handleBackdropClick = (event) => {
  if (event.target === event.currentTarget) {
    closeActionsDropdown()
    emit('close')
  }
}

const handleKeydown = (event) => {
  // Close dropdown on any key press
  closeActionsDropdown()
  
  switch (event.key) {
    case 'Escape':
      emit('close')
      break
    case 'ArrowLeft':
      if (currentIndex.value > 0) previousImage()
      break
    case 'ArrowRight':
      if (currentIndex.value < props.images.length - 1) nextImage()
      break
    case '+':
    case '=':
      zoomIn()
      break
    case '-':
      zoomOut()
      break
    case '0':
      resetZoom()
      break
    case 'f':
    case 'F':
      toggleFullscreen()
      break
    case 'i':
    case 'I':
      showInfo.value = !showInfo.value
      break
    case 'h':
    case 'H':
      toggleHelp()
      break
  }
}

const previousImage = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
    emit('prev')
  }
}

const nextImage = () => {
  if (currentIndex.value < props.images.length - 1) {
    currentIndex.value++
    emit('next')
  }
}

const zoomIn = () => {
  if (zoomLevel.value < 3) {
    zoomLevel.value = Math.min(3, zoomLevel.value + 0.25)
  }
}

const zoomOut = () => {
  if (zoomLevel.value > 0.5) {
    zoomLevel.value = Math.max(0.5, zoomLevel.value - 0.25)
    if (zoomLevel.value <= 1) {
      panX.value = 0
      panY.value = 0
    }
  }
}

const resetZoom = () => {
  zoomLevel.value = 1
  panX.value = 0
  panY.value = 0
}

const handleWheel = (event) => {
  event.preventDefault()
  if (event.deltaY < 0) {
    zoomIn()
  } else {
    zoomOut()
  }
}

const startPan = (event) => {
  if (zoomLevel.value > 1) {
    isPanning.value = true
    lastPanPoint.value = { x: event.clientX, y: event.clientY }
  }
}

const handlePan = (event) => {
  if (isPanning.value && zoomLevel.value > 1) {
    const deltaX = event.clientX - lastPanPoint.value.x
    const deltaY = event.clientY - lastPanPoint.value.y
    
    panX.value += deltaX
    panY.value += deltaY
    
    lastPanPoint.value = { x: event.clientX, y: event.clientY }
  }
}

const endPan = () => {
  isPanning.value = false
}

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    lightboxRef.value?.requestFullscreen?.()
    isFullscreen.value = true
  } else {
    document.exitFullscreen()
    isFullscreen.value = false
  }
}

// Action methods with dropdown closing
const viewFullPage = () => {
  closeActionsDropdown()
  if (currentImage.value?.slug) {
    emit('close')
    router.visit(route('images.show', currentImage.value.slug))
  } else if (currentImage.value?.id) {
    emit('close')
    router.visit(route('images.show', currentImage.value.id))
  }
}

const editImage = () => {
  closeActionsDropdown()
  if (currentImage.value?.id) {
    emit('close')
    router.visit(route('images.edit', currentImage.value.id))
  }
}

const downloadImage = () => {
  closeActionsDropdown()
  if (!currentImage.value?.id) return
  
  const link = document.createElement('a')
  link.href = route('images.download', currentImage.value.id)
  link.download = currentImage.value.original_filename || 'image'
  link.click()
}

const copyLink = () => {
  closeActionsDropdown()
  if (!currentImage.value?.id) return
  
  try {
    const imageRoute = route('images.show', currentImage.value.id)
    const fullUrl = imageRoute.startsWith('http') ? imageRoute : (window.location.origin + imageRoute)
    
    navigator.clipboard.writeText(fullUrl).then(() => {
      alert('Link copied to clipboard!')
    }).catch(() => {
      prompt('Copy this link:', fullUrl)
    })
  } catch (error) {
    console.error('Failed to copy link:', error)
  }
}

const shareImage = () => {
  closeActionsDropdown()
  if (!currentImage.value?.id) return
  
  if (navigator.share) {
    navigator.share({
      title: currentImage.value.title,
      text: currentImage.value.caption,
      url: window.location.origin + route('images.show', currentImage.value.id),
    }).catch(() => {
      copyLink()
    })
  } else {
    copyLink()
  }
}

const searchByTag = (tagName) => {
  emit('close')
  router.visit(route('gallery.index', { tag: tagName }))
}

const goToAlbum = () => {
  if (currentImage.value?.album?.slug) {
    emit('close')
    router.visit(route('albums.show', currentImage.value.album.slug))
  }
}

const toggleHelp = () => {
  showHelp.value = !showHelp.value
  showHelpNotice.value = false
}

const handleResize = () => {
  isMobile.value = window.innerWidth < 1024
  // Close dropdown on resize
  closeActionsDropdown()
}

onMounted(() => {
  // Set initial mobile state
  isMobile.value = window.innerWidth < 1024
  
  // Hide help notice after 3 seconds
  setTimeout(() => {
    showHelpNotice.value = false
  }, 3000)
  
  // Focus the lightbox for keyboard events
  nextTick(() => {
    lightboxRef.value?.focus()
  })
  
  // Prevent body scroll
  document.body.style.overflow = 'hidden'
  
  // Add event listeners
  window.addEventListener('resize', handleResize)
  
  // Add click outside listener for custom dropdown
  document.addEventListener('click', (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
      closeActionsDropdown()
    }
  })
  
  document.addEventListener('fullscreenchange', () => {
    isFullscreen.value = !!document.fullscreenElement
  })
})

onUnmounted(() => {
  // Restore body scroll
  document.body.style.overflow = ''
  
  // Remove event listeners
  window.removeEventListener('resize', handleResize)
  
  // Exit fullscreen if active
  if (document.fullscreenElement) {
    document.exitFullscreen()
  }
})
</script>

<style scoped>
/* Enhanced Premium Animations */
@keyframes mesh-flow {
  0%, 100% { 
    transform: translate(0, 0) rotate(0deg) scale(1); 
    opacity: 0.4;
  }
  25% { 
    transform: translate(20px, -15px) rotate(1deg) scale(1.02); 
    opacity: 0.6;
  }
  50% { 
    transform: translate(-10px, 10px) rotate(-0.5deg) scale(0.98); 
    opacity: 0.8;
  }
  75% { 
    transform: translate(15px, -5px) rotate(0.5deg) scale(1.01); 
    opacity: 0.5;
  }
}

@keyframes float-organic {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg) scale(1);
    opacity: 0.4;
  }
  25% { 
    transform: translateY(-25px) translateX(15px) rotate(90deg) scale(1.1);
    opacity: 0.8;
  }
  50% { 
    transform: translateY(-15px) translateX(-20px) rotate(180deg) scale(0.9);
    opacity: 1;
  }
  75% { 
    transform: translateY(-30px) translateX(10px) rotate(270deg) scale(1.05);
    opacity: 0.6;
  }
}

@keyframes modal-entrance {
  from {
    opacity: 0;
    transform: translateY(60px) scale(0.9);
    filter: blur(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0px);
  }
}

@keyframes dropdown-entrance {
  from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes shimmer-flow {
  0% { background-position: -200% 0; }
  50% { background-position: 0% 0; }
  100% { background-position: 200% 0; }
}

@keyframes title-shimmer {
  0% { background-position: -300% center; }
  50% { background-position: 0% center; }
  100% { background-position: 300% center; }
}

@keyframes glow-pulse {
  0%, 100% { 
    box-shadow: 0 0 20px rgba(139, 92, 246, 0.3);
    border-color: rgba(255, 255, 255, 0.2);
  }
  50% { 
    box-shadow: 0 0 30px rgba(6, 182, 212, 0.4);
    border-color: rgba(255, 255, 255, 0.3);
  }
}

@keyframes color-shift {
  0%, 100% { background: linear-gradient(to bottom, rgba(139, 92, 246, 0.6), rgba(6, 182, 212, 0.6), rgba(168, 85, 247, 0.6)); }
  50% { background: linear-gradient(to bottom, rgba(6, 182, 212, 0.6), rgba(168, 85, 247, 0.6), rgba(139, 92, 246, 0.6)); }
}

@keyframes slide-up-enhanced {
  from {
    opacity: 0;
    transform: translateY(100px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes fade-in-enhanced {
  from { 
    opacity: 0; 
    transform: translateY(20px);
  }
  to { 
    opacity: 1; 
    transform: translateY(0);
  }
}

@keyframes fade-in-up-enhanced {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.98);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Animation Classes */
.animate-mesh-flow {
  animation: mesh-flow 25s ease-in-out infinite;
}

.animate-float-organic {
  animation: float-organic 12s ease-in-out infinite;
}

.animate-modal-entrance {
  animation: modal-entrance 0.8s ease-out;
}

.animate-dropdown-entrance {
  animation: dropdown-entrance 0.4s ease-out;
}

.animate-shimmer-flow {
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  background-size: 200% 100%;
  animation: shimmer-flow 4s ease-in-out infinite;
}

.animate-title-shimmer {
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
  background-size: 300% 100%;
  animation: title-shimmer 5s ease-in-out infinite;
}

.animate-glow-pulse {
  animation: glow-pulse 3s ease-in-out infinite;
}

.animate-color-shift {
  animation: color-shift 8s ease-in-out infinite;
}

.animate-slide-up-enhanced {
  animation: slide-up-enhanced 0.8s ease-out;
}

.animate-fade-in-enhanced {
  animation: fade-in-enhanced 0.6s ease-out;
}

.animate-fade-in-up-enhanced {
  animation: fade-in-up-enhanced 0.8s ease-out forwards;
  opacity: 0;
}

/* Enhanced Timing */
.animation-delay-200 { animation-delay: 0.2s; }
.animation-delay-300 { animation-delay: 0.3s; }
.animation-delay-400 { animation-delay: 0.4s; }
.animation-delay-500 { animation-delay: 0.5s; }
.animation-delay-600 { animation-delay: 0.6s; }

/* Enhanced Interactions */
.group-nav:hover {
  transform: scale(1.1) translateX(5px);
}

.group-nav:hover .h-10 {
  transform: scale(1.1);
}

/* Enhanced Scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(30, 41, 59, 0.2);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(45deg, #8b5cf6, #06b6d4);
  border-radius: 10px;
  border: 2px solid transparent;
  background-clip: content-box;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(45deg, #7c3aed, #0891b2);
  background-clip: content-box;
}

/* Enhanced Backdrop Effects */
.backdrop-blur-3xl {
  backdrop-filter: blur(60px);
  -webkit-backdrop-filter: blur(60px);
}

/* Enhanced dropdown positioning */
:deep(.dropdown-content) {
  position: fixed !important;
  z-index: 200 !important;
}

/* Ensure dropdown appears above everything */
:global(.dropdown-portal) {
  z-index: 200 !important;
}

:global([data-teleport-target="body"]) {
  z-index: 200 !important;
}
</style>
