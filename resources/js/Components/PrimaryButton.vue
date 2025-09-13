<template>
    <button
        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
    >
        <slot />
    </button>
</template>
/*<template>
    <button
        class="group relative inline-flex items-center rounded-lg border border-transparent bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 px-6 py-3 text-xs font-semibold uppercase tracking-widest text-white transition-all duration-300 ease-out hover:from-violet-500 hover:via-purple-500 hover:to-cyan-500 focus:from-violet-500 focus:via-purple-500 focus:to-cyan-500 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:ring-offset-2 focus:ring-offset-slate-900 active:from-violet-700 active:via-purple-700 active:to-cyan-700 transform hover:scale-105 active:scale-95 overflow-hidden shadow-lg shadow-violet-500/25 hover:shadow-xl hover:shadow-violet-500/40"
    >
        <!-- Animated mesh background -->
        <div class="absolute inset-0 opacity-30">
            <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 via-purple-400/20 to-cyan-400/20 animate-gradient-pulse"></div>
            <div class="absolute top-0 left-0 w-full h-full">
                <div v-for="i in 8" :key="i" 
                     :class="`absolute rounded-full bg-white opacity-20 animate-float-button-${i % 4}`"
                     :style="`
                       left: ${(i * 12) % 100}%; 
                       top: ${(i * 15) % 100}%; 
                       width: ${1 + (i % 2)}px; 
                       height: ${1 + (i % 2)}px;
                       animation-delay: ${i * 0.3}s;
                     `">
                </div>
            </div>
        </div>

        <!-- Shimmer effect overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>

        <!-- Glassmorphism overlay -->
        <div class="absolute inset-0 bg-white/5 backdrop-blur-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        <!-- Pulse ring effect -->
        <div class="absolute inset-0 rounded-lg animate-pulse-ring opacity-0 group-hover:opacity-100"></div>

        <!-- Enhanced button border -->
        <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-violet-400/30 via-purple-400/30 to-cyan-400/30 p-[1px] opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <div class="w-full h-full bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 rounded-lg"></div>
        </div>

        <!-- Corner accent elements -->
        <div class="absolute top-1 left-1 w-2 h-2 opacity-0 group-hover:opacity-60 transition-opacity duration-300">
            <div class="absolute top-0 left-0 w-0.5 h-0.5 bg-white rounded-full animate-pulse"></div>
            <div class="absolute top-1 left-1 w-0.5 h-0.5 bg-cyan-200 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
        </div>
        <div class="absolute top-1 right-1 w-2 h-2 opacity-0 group-hover:opacity-60 transition-opacity duration-300">
            <div class="absolute top-0 right-0 w-0.5 h-0.5 bg-violet-200 rounded-full animate-pulse" style="animation-delay: 0.25s;"></div>
            <div class="absolute top-1 right-1 w-0.5 h-0.5 bg-purple-200 rounded-full animate-pulse" style="animation-delay: 0.75s;"></div>
        </div>

        <!-- Text content with enhanced effects -->
        <span class="relative z-10 flex items-center space-x-2 font-bold drop-shadow-sm group-hover:drop-shadow-md transition-all duration-300">
            <!-- Leading accent dot -->
            <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 group-hover:opacity-80 group-hover:animate-pulse transition-all duration-300 group-hover:scale-125"></div>
            
            <!-- Main text content -->
            <span class="group-hover:text-shadow-glow transition-all duration-300">
                <slot />
            </span>
            
            <!-- Trailing accent dot -->
            <div class="w-1.5 h-1.5 rounded-full bg-cyan-200 opacity-0 group-hover:opacity-80 group-hover:animate-pulse transition-all duration-300 group-hover:scale-125" style="animation-delay: 0.3s;"></div>
        </span>

        <!-- Click ripple effect -->
        <div class="absolute inset-0 bg-white/20 rounded-lg scale-0 group-active:scale-100 group-active:opacity-30 opacity-0 transition-all duration-200 ease-out"></div>

        <!-- Enhanced glow effects -->
        <div class="absolute -inset-1 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 rounded-lg blur-lg opacity-0 group-hover:opacity-30 transition-opacity duration-300 -z-10 animate-glow-pulse"></div>
        
        <!-- Text shimmer for slot content -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="bg-gradient-to-r from-transparent via-white/30 to-transparent bg-clip-text text-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 animate-text-shimmer">
                <slot />
            </div>
        </div>
    </button>
</template>

<style scoped>
/* Gradient pulse animation for background */
@keyframes gradient-pulse {
    0%, 100% { 
        background: linear-gradient(90deg, rgba(139, 92, 246, 0.2), rgba(168, 85, 247, 0.2), rgba(6, 182, 212, 0.2));
        transform: scale(1);
    }
    50% { 
        background: linear-gradient(90deg, rgba(168, 85, 247, 0.3), rgba(6, 182, 212, 0.3), rgba(139, 92, 246, 0.3));
        transform: scale(1.02);
    }
}

.animate-gradient-pulse {
    animation: gradient-pulse 3s ease-in-out infinite;
}

/* Floating button particles */
@keyframes float-button-0 {
    0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.2; }
    50% { transform: translateY(-8px) translateX(4px) rotate(180deg); opacity: 0.6; }
}

@keyframes float-button-1 {
    0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
    50% { transform: translateY(-6px) translateX(-3px) rotate(-180deg); opacity: 0.7; }
}

@keyframes float-button-2 {
    0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.4; }
    50% { transform: translateY(-10px) translateX(2px) rotate(270deg); opacity: 0.5; }
}

@keyframes float-button-3 {
    0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.2; }
    50% { transform: translateY(-7px) translateX(-5px) rotate(-270deg); opacity: 0.8; }
}

.animate-float-button-0 { animation: float-button-0 3s ease-in-out infinite; }
.animate-float-button-1 { animation: float-button-1 4s ease-in-out infinite; }
.animate-float-button-2 { animation: float-button-2 3.5s ease-in-out infinite; }
.animate-float-button-3 { animation: float-button-3 4.5s ease-in-out infinite; }

/* Pulse ring effect */
@keyframes pulse-ring {
    0% {
        box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(139, 92, 246, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(139, 92, 246, 0);
    }
}

.animate-pulse-ring {
    animation: pulse-ring 2s ease-out infinite;
}

/* Glow pulse animation */
@keyframes glow-pulse {
    0%, 100% { 
        opacity: 0.2; 
        transform: scale(0.95);
    }
    50% { 
        opacity: 0.4; 
        transform: scale(1.05);
    }
}

.animate-glow-pulse {
    animation: glow-pulse 2s ease-in-out infinite;
}

/* Text shimmer animation */
@keyframes text-shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.animate-text-shimmer {
    animation: text-shimmer 2s ease-in-out infinite;
}

/* Enhanced hover effects */
.group:hover .animate-float-button-0,
.group:hover .animate-float-button-1,
.group:hover .animate-float-button-2,
.group:hover .animate-float-button-3 {
    animation-duration: 2s;
}

.group:hover .animate-gradient-pulse {
    animation-duration: 2s;
}

/* Text glow effect */
.text-shadow-glow {
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

/* Focus and active states */
.group:focus-visible {
    ring-color: rgba(139, 92, 246, 0.5);
    ring-width: 3px;
    ring-offset-width: 3px;
    ring-offset-color: rgb(15, 23, 42);
}

.group:active .animate-pulse-ring {
    animation-duration: 0.3s;
}

/* Disabled state */
.group:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.group:disabled:hover {
    transform: none !important;
    box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.25), 0 4px 6px -2px rgba(139, 92, 246, 0.1) !important;
}

/* Responsive adjustments */


/* High contrast mode support */


/* Reduced motion support */
/* Loading state animation */
@keyframes loading-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.group.loading {
    animation: loading-pulse 1.5s ease-in-out infinite;
    pointer-events: none;
}


</style>