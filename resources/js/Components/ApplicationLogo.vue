<template>
    <div class="relative inline-block group">
      <!-- Animated Background Glow -->
      <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700">
        <div class="absolute inset-0 bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 rounded-2xl blur-xl animate-pulse-glow"></div>
      </div>
      
      <!-- Floating Ring Animations -->
      <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
        <div class="absolute inset-0 border-2 border-violet-400/30 rounded-2xl animate-ring-expand"></div>
        <div class="absolute inset-0 border-2 border-cyan-400/30 rounded-2xl animate-ring-expand-delayed"></div>
      </div>
  
      <svg 
        viewBox="0 0 316 316" 
        xmlns="http://www.w3.org/2000/svg"
        class="relative z-10 transition-all duration-500 group-hover:scale-110 drop-shadow-2xl"
      >
        <defs>
          <!-- Primary Gradient -->
          <linearGradient id="primaryGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:1" class="animate-gradient-shift-1">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="6s" repeatCount="indefinite"/>
            </stop>
            <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:1" class="animate-gradient-shift-2">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="6s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" style="stop-color:#a855f7;stop-opacity:1" class="animate-gradient-shift-3">
              <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#06b6d4;#a855f7" dur="6s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
  
          <!-- Shine Effect Gradient -->
          <linearGradient id="shineGradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:transparent"/>
            <stop offset="50%" style="stop-color:rgba(255,255,255,0.6)"/>
            <stop offset="100%" style="stop-color:transparent"/>
            <animateTransform 
              attributeName="gradientTransform" 
              type="translate" 
              values="-100 0; 400 0; -100 0" 
              dur="4s" 
              repeatCount="indefinite"
            />
          </linearGradient>
  
          <!-- Glow Filter -->
          <filter id="glow" x="-50%" y="-50%" width="200%" height="200%">
            <feGaussianBlur stdDeviation="4" result="coloredBlur"/>
            <feMerge> 
              <feMergeNode in="coloredBlur"/>
              <feMergeNode in="SourceGraphic"/>
            </feMerge>
          </filter>
  
          <!-- Drop Shadow -->
          <filter id="dropshadow" x="-50%" y="-50%" width="200%" height="200%">
            <feGaussianBlur in="SourceAlpha" stdDeviation="8"/>
            <feOffset dx="0" dy="8" result="offset"/>
            <feComponentTransfer>
              <feFuncA type="linear" slope="0.3"/>
            </feComponentTransfer>
            <feMerge> 
              <feMergeNode/>
              <feMergeNode in="SourceGraphic"/>
            </feMerge>
          </filter>
        </defs>
  
        <!-- Main Logo Path with Enhanced Effects -->
        <path
          d="M305.8 81.125C305.77 80.995 305.69 80.885 305.65 80.755C305.56 80.525 305.49 80.285 305.37 80.075C305.29 79.935 305.17 79.815 305.07 79.685C304.94 79.515 304.83 79.325 304.68 79.175C304.55 79.045 304.39 78.955 304.25 78.845C304.09 78.715 303.95 78.575 303.77 78.475L251.32 48.275C249.97 47.495 248.31 47.495 246.96 48.275L194.51 78.475C194.33 78.575 194.19 78.725 194.03 78.845C193.89 78.955 193.73 79.045 193.6 79.175C193.45 79.325 193.34 79.515 193.21 79.685C193.11 79.815 192.99 79.935 192.91 80.075C192.79 80.285 192.71 80.525 192.63 80.755C192.58 80.875 192.51 80.995 192.48 81.125C192.38 81.495 192.33 81.875 192.33 82.265V139.625L148.62 164.795V52.575C148.62 52.185 148.57 51.805 148.47 51.435C148.44 51.305 148.36 51.195 148.32 51.065C148.23 50.835 148.16 50.595 148.04 50.385C147.96 50.245 147.84 50.125 147.74 49.995C147.61 49.825 147.5 49.635 147.35 49.485C147.22 49.355 147.06 49.265 146.92 49.155C146.76 49.025 146.62 48.885 146.44 48.785L93.99 18.585C92.64 17.805 90.98 17.805 89.63 18.585L37.18 48.785C37 48.885 36.86 49.035 36.7 49.155C36.56 49.265 36.4 49.355 36.27 49.485C36.12 49.635 36.01 49.825 35.88 49.995C35.78 50.125 35.66 50.245 35.58 50.385C35.46 50.595 35.38 50.835 35.3 51.065C35.25 51.185 35.18 51.305 35.15 51.435C35.05 51.805 35 52.185 35 52.575V232.235C35 233.795 35.84 235.245 37.19 236.025L142.1 296.425C142.33 296.555 142.58 296.635 142.82 296.725C142.93 296.765 143.04 296.835 143.16 296.865C143.53 296.965 143.9 297.015 144.28 297.015C144.66 297.015 145.03 296.965 145.4 296.865C145.5 296.835 145.59 296.775 145.69 296.745C145.95 296.655 146.21 296.565 146.45 296.435L251.36 236.035C252.72 235.255 253.55 233.815 253.55 232.245V174.885L303.81 145.945C305.17 145.165 306 143.725 306 142.155V82.265C305.95 81.875 305.89 81.495 305.8 81.125ZM144.2 227.205L100.57 202.515L146.39 176.135L196.66 147.195L240.33 172.335L208.29 190.625L144.2 227.205ZM244.75 114.995V164.795L226.39 154.225L201.03 139.625V89.825L219.39 100.395L244.75 114.995ZM249.12 57.105L292.81 82.265L249.12 107.425L205.43 82.265L249.12 57.105ZM114.49 184.425L96.13 194.995V85.305L121.49 70.705L139.85 60.135V169.815L114.49 184.425ZM91.76 27.425L135.45 52.585L91.76 77.745L48.07 52.585L91.76 27.425ZM43.67 60.135L62.03 70.705L87.39 85.305V202.545V202.555V202.565C87.39 202.735 87.44 202.895 87.46 203.055C87.49 203.265 87.49 203.485 87.55 203.695V203.705C87.6 203.875 87.69 204.035 87.76 204.195C87.84 204.375 87.89 204.575 87.99 204.745C87.99 204.745 87.99 204.755 88 204.755C88.09 204.905 88.22 205.035 88.33 205.175C88.45 205.335 88.55 205.495 88.69 205.635L88.7 205.645C88.82 205.765 88.98 205.855 89.12 205.965C89.28 206.085 89.42 206.225 89.59 206.325C89.6 206.325 89.6 206.325 89.61 206.335C89.62 206.335 89.62 206.345 89.63 206.345L139.87 234.775V285.065L43.67 229.705V60.135ZM244.75 229.705L148.58 285.075V234.775L219.8 194.115L244.75 179.875V229.705ZM297.2 139.625L253.49 164.795V114.995L278.85 100.395L297.21 89.825V139.625H297.2Z"
          fill="url(#primaryGradient)"
          filter="url(#dropshadow)"
          class="animate-logo-float"
        >
          <!-- Hover Animation -->
          <animateTransform 
            attributeName="transform" 
            type="rotate" 
            values="0 158 158; 360 158 158" 
            dur="20s" 
            repeatCount="indefinite"
            class="opacity-0 group-hover:opacity-100"
          />
        </path>
  
        <!-- Shine Overlay Effect -->
        <path
          d="M305.8 81.125C305.77 80.995 305.69 80.885 305.65 80.755C305.56 80.525 305.49 80.285 305.37 80.075C305.29 79.935 305.17 79.815 305.07 79.685C304.94 79.515 304.83 79.325 304.68 79.175C304.55 79.045 304.39 78.955 304.25 78.845C304.09 78.715 303.95 78.575 303.77 78.475L251.32 48.275C249.97 47.495 248.31 47.495 246.96 48.275L194.51 78.475C194.33 78.575 194.19 78.725 194.03 78.845C193.89 78.955 193.73 79.045 193.6 79.175C193.45 79.325 193.34 79.515 193.21 79.685C193.11 79.815 192.99 79.935 192.91 80.075C192.79 80.285 192.71 80.525 192.63 80.755C192.58 80.875 192.51 80.995 192.48 81.125C192.38 81.495 192.33 81.875 192.33 82.265V139.625L148.62 164.795V52.575C148.62 52.185 148.57 51.805 148.47 51.435C148.44 51.305 148.36 51.195 148.32 51.065C148.23 50.835 148.16 50.595 148.04 50.385C147.96 50.245 147.84 50.125 147.74 49.995C147.61 49.825 147.5 49.635 147.35 49.485C147.22 49.355 147.06 49.265 146.92 49.155C146.76 49.025 146.62 48.885 146.44 48.785L93.99 18.585C92.64 17.805 90.98 17.805 89.63 18.585L37.18 48.785C37 48.885 36.86 49.035 36.7 49.155C36.56 49.265 36.4 49.355 36.27 49.485C36.12 49.635 36.01 49.825 35.88 49.995C35.78 50.125 35.66 50.245 35.58 50.385C35.46 50.595 35.38 50.835 35.3 51.065C35.25 51.185 35.18 51.305 35.15 51.435C35.05 51.805 35 52.185 35 52.575V232.235C35 233.795 35.84 235.245 37.19 236.025L142.1 296.425C142.33 296.555 142.58 296.635 142.82 296.725C142.93 296.765 143.04 296.835 143.16 296.865C143.53 296.965 143.9 297.015 144.28 297.015C144.66 297.015 145.03 296.965 145.4 296.865C145.5 296.835 145.59 296.775 145.69 296.745C145.95 296.655 146.21 296.565 146.45 296.435L251.36 236.035C252.72 235.255 253.55 233.815 253.55 232.245V174.885L303.81 145.945C305.17 145.165 306 143.725 306 142.155V82.265C305.95 81.875 305.89 81.495 305.8 81.125ZM144.2 227.205L100.57 202.515L146.39 176.135L196.66 147.195L240.33 172.335L208.29 190.625L144.2 227.205ZM244.75 114.995V164.795L226.39 154.225L201.03 139.625V89.825L219.39 100.395L244.75 114.995ZM249.12 57.105L292.81 82.265L249.12 107.425L205.43 82.265L249.12 57.105ZM114.49 184.425L96.13 194.995V85.305L121.49 70.705L139.85 60.135V169.815L114.49 184.425ZM91.76 27.425L135.45 52.585L91.76 77.745L48.07 52.585L91.76 27.425ZM43.67 60.135L62.03 70.705L87.39 85.305V202.545V202.555V202.565C87.39 202.735 87.44 202.895 87.46 203.055C87.49 203.265 87.49 203.485 87.55 203.695V203.705C87.6 203.875 87.69 204.035 87.76 204.195C87.84 204.375 87.89 204.575 87.99 204.745C87.99 204.745 87.99 204.755 88 204.755C88.09 204.905 88.22 205.035 88.33 205.175C88.45 205.335 88.55 205.495 88.69 205.635L88.7 205.645C88.82 205.765 88.98 205.855 89.12 205.965C89.28 206.085 89.42 206.225 89.59 206.325C89.6 206.325 89.6 206.325 89.61 206.335C89.62 206.335 89.62 206.345 89.63 206.345L139.87 234.775V285.065L43.67 229.705V60.135ZM244.75 229.705L148.58 285.075V234.775L219.8 194.115L244.75 179.875V229.705ZM297.2 139.625L253.49 164.795V114.995L278.85 100.395L297.21 89.825V139.625H297.2Z"
          fill="url(#shineGradient)"
          opacity="0.5"
          class="animate-shine-sweep"
        />
  
        <!-- Floating Particles Around Logo -->
        <g class="animate-particles-float opacity-60">
          <circle cx="80" cy="80" r="2" fill="#8b5cf6" opacity="0.6">
            <animate attributeName="cy" values="80; 60; 80" dur="3s" repeatCount="indefinite"/>
            <animate attributeName="opacity" values="0.6; 1; 0.6" dur="3s" repeatCount="indefinite"/>
          </circle>
          <circle cx="240" cy="100" r="1.5" fill="#06b6d4" opacity="0.5">
            <animate attributeName="cy" values="100; 80; 100" dur="4s" repeatCount="indefinite"/>
            <animate attributeName="opacity" values="0.5; 0.9; 0.5" dur="4s" repeatCount="indefinite"/>
          </circle>
          <circle cx="300" cy="200" r="2.5" fill="#a855f7" opacity="0.7">
            <animate attributeName="cy" values="200; 180; 200" dur="5s" repeatCount="indefinite"/>
            <animate attributeName="opacity" values="0.7; 1; 0.7" dur="5s" repeatCount="indefinite"/>
          </circle>
          <circle cx="40" cy="250" r="1.8" fill="#8b5cf6" opacity="0.4">
            <animate attributeName="cy" values="250; 230; 250" dur="3.5s" repeatCount="indefinite"/>
            <animate attributeName="opacity" values="0.4; 0.8; 0.4" dur="3.5s" repeatCount="indefinite"/>
          </circle>
        </g>
      </svg>
  
      <!-- Glassmorphism Badge -->
      <div class="absolute -bottom-2 -right-2 opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-2 group-hover:translate-y-0">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-full px-3 py-1 shadow-2xl">
          <span class="text-xs font-medium bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent">
            Gallery
          </span>
        </div>
      </div>
    </div>
  </template>
  
  <style scoped>
  @keyframes pulse-glow {
    0%, 100% { 
      opacity: 0.3;
      transform: scale(0.95);
    }
    50% { 
      opacity: 0.6;
      transform: scale(1.05);
    }
  }
  
  @keyframes ring-expand {
    0% {
      transform: scale(1);
      opacity: 1;
    }
    100% {
      transform: scale(1.4);
      opacity: 0;
    }
  }
  
  @keyframes ring-expand-delayed {
    0% {
      transform: scale(1);
      opacity: 1;
    }
    100% {
      transform: scale(1.6);
      opacity: 0;
    }
  }
  
  @keyframes logo-float {
    0%, 100% { 
      transform: translateY(0px);
    }
    50% { 
      transform: translateY(-5px);
    }
  }
  
  @keyframes shine-sweep {
    0% { 
      opacity: 0;
      transform: translateX(-100%);
    }
    50% { 
      opacity: 0.8;
    }
    100% { 
      opacity: 0;
      transform: translateX(100%);
    }
  }
  
  @keyframes particles-float {
    0%, 100% { 
      transform: translateY(0px) rotate(0deg);
    }
    33% { 
      transform: translateY(-10px) rotate(120deg);
    }
    66% { 
      transform: translateY(-5px) rotate(240deg);
    }
  }
  
  .animate-pulse-glow {
    animation: pulse-glow 3s ease-in-out infinite;
  }
  
  .animate-ring-expand {
    animation: ring-expand 2s ease-out infinite;
  }
  
  .animate-ring-expand-delayed {
    animation: ring-expand-delayed 2s ease-out infinite;
    animation-delay: 0.5s;
  }
  
  .animate-logo-float {
    animation: logo-float 4s ease-in-out infinite;
  }
  
  .animate-shine-sweep {
    animation: shine-sweep 4s ease-in-out infinite;
  }
  
  .animate-particles-float {
    animation: particles-float 6s ease-in-out infinite;
  }
  
  /* Hover-triggered animations */
  .group:hover .animate-logo-float {
    animation-duration: 2s;
  }
  
  .group:hover .animate-shine-sweep {
    animation-duration: 2s;
  }
  </style>
