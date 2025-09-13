<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25"
    >
        <slot />
    </button>
</template>
/* <script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="group relative inline-flex items-center rounded-lg border border-violet-500/30 bg-slate-800/50 backdrop-blur-sm px-6 py-3 text-xs font-semibold uppercase tracking-widest text-slate-300 shadow-lg shadow-slate-900/25 transition-all duration-300 ease-out hover:bg-gradient-to-r hover:from-slate-700/50 hover:to-slate-800/50 hover:text-white hover:border-violet-400/50 hover:shadow-xl hover:shadow-violet-500/20 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:opacity-25 transform hover:scale-105 active:scale-95 overflow-hidden"
    >
        <!-- Animated mesh background -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-700/20 via-slate-600/20 to-slate-700/20 group-hover:from-violet-400/10 group-hover:via-purple-400/10 group-hover:to-cyan-400/10 animate-secondary-gradient transition-all duration-500"></div>
            <div class="absolute top-0 left-0 w-full h-full">
                <div v-for="i in 6" :key="i" 
                     :class="`absolute rounded-full bg-slate-400 group-hover:bg-violet-400 opacity-20 group-hover:opacity-40 animate-float-secondary-${i % 3} transition-colors duration-300`"
                     :style="`
                       left: ${(i * 16) % 100}%; 
                       top: ${(i * 20) % 100}%; 
                       width: ${1 + (i % 2)}px; 
                       height: ${1 + (i % 2)}px;
                       animation-delay: ${i * 0.4}s;
                     `">
                </div>
            </div>
        </div>

        <!-- Shimmer effect overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full group-hover:via-violet-300/15 transition-all duration-700 ease-out"></div>

        <!-- Glassmorphism overlay -->
        <div class="absolute inset-0 bg-white/5 backdrop-blur-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        <!-- Enhanced border gradient -->
        <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-slate-600/30 via-slate-500/30 to-slate-600/30 group-hover:from-violet-400/30 group-hover:via-purple-400/30 group-hover:to-cyan-400/30 p-[1px] transition-all duration-300">
            <div class="w-full h-full bg-slate-800/50 backdrop-blur-sm rounded-lg"></div>
        </div>

        <!-- Corner accent elements -->
        <div class="absolute top-1.5 left-1.5 w-2 h-2 opacity-0 group-hover:opacity-50 transition-opacity duration-300">
            <div class="absolute top-0 left-0 w-0.5 h-0.5 bg-slate-400 group-hover:bg-violet-400 rounded-full animate-pulse transition-colors duration-300"></div>
            <div class="absolute top-1 left-1 w-0.5 h-0.5 bg-slate-500 group-hover:bg-cyan-400 rounded-full animate-pulse transition-colors duration-300" style="animation-delay: 0.5s;"></div>
        </div>
        <div class="absolute top-1.5 right-1.5 w-2 h-2 opacity-0 group-hover:opacity-50 transition-opacity duration-300">
            <div class="absolute top-0 right-0 w-0.5 h-0.5 bg-slate-400 group-hover:bg-purple-400 rounded-full animate-pulse transition-colors duration-300" style="animation-delay: 0.25s;"></div>
            <div class="absolute top-1 right-1 w-0.5 h-0.5 bg-slate-500 group-hover:bg-violet-400 rounded-full animate-pulse transition-colors duration-300" style="animation-delay: 0.75s;"></div>
        </div>

        <!-- Text content with enhanced effects -->
        <span class="relative z-10 flex items-center space-x-2 font-bold drop-shadow-sm group-hover:drop-shadow-md transition-all duration-300">
            <!-- Leading accent dot -->
            <div class="w-1 h-1 rounded-full bg-slate-500 group-hover:bg-violet-400 opacity-0 group-hover:opacity-60 group-hover:animate-pulse transition-all duration-300 group-hover:scale-125"></div>
            
            <!-- Main text content -->
            <span class="group-hover:text-shadow-subtle transition-all duration-300">
                <slot />
            </span>
            
            <!-- Trailing accent dot -->
            <div class="w-1 h-1 rounded-full bg-slate-500 group-hover:bg-cyan-400 opacity-0 group-hover:opacity-60 group-hover:animate-pulse transition-all duration-300 group-hover:scale-125" style="animation-delay: 0.3s;"></div>
        </span>

        <!-- Click ripple effect -->
        <div class="absolute inset-0 bg-violet-400/15 rounded-lg scale-0 group-active:scale-100 group-active:opacity-40 opacity-0 transition-all duration-200 ease-out"></div>

        <!-- Subtle outer glow -->
        <div class="absolute -inset-1 bg-gradient-to-r from-slate-600/20 via-slate-500/20 to-slate-600/20 group-hover:from-violet-600/20 group-hover:via-purple-600/20 group-hover:to-cyan-600/20 rounded-lg blur-lg opacity-0 group-hover:opacity-40 transition-all duration-300 -z-10"></div>

        <!-- Disabled state overlay -->
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm rounded-lg opacity-0 disabled:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
    </button>
</template>

<style scoped>
/* Secondary gradient animation */
@keyframes secondary-gradient {
    0%, 100% { 
        background: linear-gradient(90deg, rgba(71, 85, 105, 0.2), rgba(100, 116, 139, 0.2), rgba(71, 85, 105, 0.2));
    }
    50% { 
        background: linear-gradient(90deg, rgba(100, 116, 139, 0.25), rgba(71, 85, 105, 0.25), rgba(100, 116, 139, 0.25));
    }
}

.animate-secondary-gradient {
    animation: secondary-gradient 4s ease-in-out infinite;
}

/* Floating secondary particles */
@keyframes float-secondary-0 {
    0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.2; }
    50% { transform: translateY(-6px) translateX(3px) rotate(180deg); opacity: 0.5; }
}

@keyframes float-secondary-1 {
    0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
    50% { transform: translateY(-5px) translateX(-2px) rotate(-180deg); opacity: 0.6; }
}

@keyframes float-secondary-2 {
    0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.4; }
    50% { transform: translateY(-8px) translateX(1px) rotate(270deg); opacity: 0.4; }
}

.animate-float-secondary-0 { animation: float-secondary-0 5s ease-in-out infinite; }
.animate-float-secondary-1 { animation: float-secondary-1 6s ease-in-out infinite; }
.animate-float-secondary-2 { animation: float-secondary-2 4.5s ease-in-out infinite; }

/* Subtle text shadow effect */
.text-shadow-subtle {
    text-shadow: 0 0 8px rgba(139, 92, 246, 0.3);
}

/* Enhanced hover effects */
.group:hover .animate-float-secondary-0,
.group:hover .animate-float-secondary-1,
.group:hover .animate-float-secondary-2 {
    animation-duration: 3s;
}

.group:hover .animate-secondary-gradient {
    animation-duration: 2.5s;
}

/* Focus and active states */
.group:focus-visible {
    ring-color: rgba(139, 92, 246, 0.5);
    ring-width: 3px;
    ring-offset-width: 3px;
    ring-offset-color: rgb(15, 23, 42);
}

.group:active .animate-secondary-gradient {
    animation-duration: 1s;
}

/* Disabled state enhancements */
.group:disabled {
    opacity: 0.25;
    cursor: not-allowed;
    transform: none !important;
    pointer-events: none;
}

.group:disabled .animate-float-secondary-0,
.group:disabled .animate-float-secondary-1,
.group:disabled .animate-float-secondary-2,
.group:disabled .animate-secondary-gradient {
    animation-play-state: paused;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .group {
        padding: 0.625rem 1.25rem;
        transform: none;
    }
    
    .group:hover {
        transform: scale(1.02);
    }
    
    .group:active {
        transform: scale(0.98);
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .group {
        background: rgb(30, 41, 59) !important;
        border: 2px solid rgb(148, 163, 184) !important;
        color: white !important;
    }
    
    .group:hover {
        background: rgb(51, 65, 85) !important;
        border-color: white !important;
    }
    
    .group:focus {
        ring-color: white !important;
        ring-width: 3px !important;
    }
    
    .text-slate-300 {
        color: white !important;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .group,
    .group *,
    .animate-secondary-gradient,
    .animate-float-secondary-0,
    .animate-float-secondary-1,
    .animate-float-secondary-2 {
        animation: none !important;
        transition-duration: 0.01ms !important;
    }
    
    .group:hover {
        transform: none !important;
    }
}

/* Loading state animation */
@keyframes loading-secondary {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(0.98); }
}

.group.loading {
    animation: loading-secondary 1.5s ease-in-out infinite;
    pointer-events: none;
}

/* Dark theme optimization */
@media (prefers-color-scheme: dark) {
    .group {
        ring-offset-color: rgb(2, 6, 23);
    }
    
    .bg-slate-800\/50 {
        background-color: rgba(30, 41, 59, 0.6);
    }
}

/* Button group spacing */
.group + .group {
    margin-left: 0.5rem;
}

/* Keyboard navigation enhancement */
.group:focus:not(:focus-visible) {
    outline: none;
    ring: 0;
}

/* Loading spinner for async operations */
.group.loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid rgba(139, 92, 246, 0.3);
    border-top-color: rgb(139, 92, 246);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.group.loading slot {
    opacity: 0;
}
</style>