<template>
    <button
        class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700"
    >
        <slot />
    </button>
</template>
/*<template>
    <button
        class="relative inline-flex items-center rounded-xl border border-transparent overflow-hidden group
               bg-gradient-to-r from-red-600 via-red-500 to-orange-500
               px-6 py-3 text-xs font-semibold uppercase tracking-widest text-white 
               transition-all duration-500 ease-out
               hover:from-red-500 hover:via-red-400 hover:to-orange-400 hover:scale-105 hover:shadow-2xl hover:shadow-red-500/50
               focus:outline-none focus:ring-4 focus:ring-red-500/30 focus:ring-offset-0
               active:from-red-700 active:via-red-600 active:to-orange-600 active:scale-95
               shadow-lg backdrop-blur-xl border-red-400/30"
    >
        <!-- Animated Background Glow -->
        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            <div class="absolute inset-0 bg-gradient-to-r from-red-500/30 via-red-400/30 to-orange-500/30 rounded-xl blur-xl animate-pulse-danger"></div>
        </div>

        <!-- Shimmer Effect -->
        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent rounded-xl animate-shimmer-danger"></div>
        </div>

        <!-- Floating Particles -->
        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none overflow-hidden rounded-xl">
            <div class="absolute top-1 left-2 w-1 h-1 bg-orange-300 rounded-full animate-particle-float-1"></div>
            <div class="absolute top-2 right-3 w-0.5 h-0.5 bg-red-300 rounded-full animate-particle-float-2"></div>
            <div class="absolute bottom-2 left-4 w-1.5 h-1.5 bg-yellow-300 rounded-full animate-particle-float-3"></div>
        </div>

        <!-- Warning Pulse Ring -->
        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <div class="absolute inset-0 border-2 border-red-400/40 rounded-xl animate-warning-pulse"></div>
        </div>

        <!-- Enhanced Border Gradient -->
        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-red-400/20 via-red-300/20 to-orange-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        <!-- Button Content -->
        <span class="relative z-10 flex items-center space-x-2 group-hover:drop-shadow-lg transition-all duration-300">
            <!-- Warning Icon -->
            <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 -ml-1 group-hover:animate-bounce-gentle" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
            </svg>
            
            <slot />
            
            <!-- Trailing Glow Dot -->
            <div class="w-2 h-2 bg-orange-300 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-500 group-hover:animate-pulse"></div>
        </span>

        <!-- Ripple Effect on Click -->
        <div class="absolute inset-0 rounded-xl opacity-0 group-active:opacity-100 transition-opacity duration-150">
            <div class="absolute inset-0 bg-white/20 rounded-xl animate-ripple-danger"></div>
        </div>
    </button>
</template>

<style scoped>
@keyframes pulse-danger {
    0%, 100% { 
        opacity: 0.3;
        transform: scale(0.98);
    }
    50% { 
        opacity: 0.6;
        transform: scale(1.02);
    }
}

@keyframes shimmer-danger {
    0% { 
        transform: translateX(-100%) skewX(-45deg);
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% { 
        transform: translateX(200%) skewX(-45deg);
        opacity: 0;
    }
}

@keyframes particle-float-1 {
    0%, 100% { 
        transform: translate(0, 0) scale(1);
        opacity: 0.7;
    }
    25% { 
        transform: translate(10px, -8px) scale(1.2);
        opacity: 1;
    }
    50% { 
        transform: translate(20px, -15px) scale(0.8);
        opacity: 0.5;
    }
    75% { 
        transform: translate(15px, -5px) scale(1.1);
        opacity: 0.8;
    }
}

@keyframes particle-float-2 {
    0%, 100% { 
        transform: translate(0, 0) scale(1);
        opacity: 0.6;
    }
    33% { 
        transform: translate(-12px, 10px) scale(1.3);
        opacity: 0.9;
    }
    66% { 
        transform: translate(-5px, 20px) scale(0.7);
        opacity: 0.4;
    }
}

@keyframes particle-float-3 {
    0%, 100% { 
        transform: translate(0, 0) scale(1);
        opacity: 0.8;
    }
    40% { 
        transform: translate(8px, -12px) scale(1.4);
        opacity: 1;
    }
    80% { 
        transform: translate(-3px, -25px) scale(0.6);
        opacity: 0.3;
    }
}

@keyframes warning-pulse {
    0% {
        transform: scale(1);
        opacity: 0.6;
    }
    50% {
        transform: scale(1.05);
        opacity: 0.3;
    }
    100% {
        transform: scale(1.1);
        opacity: 0;
    }
}

@keyframes bounce-gentle {
    0%, 100% { 
        transform: translateY(0);
    }
    50% { 
        transform: translateY(-3px);
    }
}

@keyframes ripple-danger {
    0% {
        transform: scale(0);
        opacity: 0.8;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

.animate-pulse-danger {
    animation: pulse-danger 2s ease-in-out infinite;
}

.animate-shimmer-danger {
    animation: shimmer-danger 2s ease-in-out infinite;
}

.animate-particle-float-1 {
    animation: particle-float-1 3s ease-in-out infinite;
}

.animate-particle-float-2 {
    animation: particle-float-2 2.5s ease-in-out infinite;
    animation-delay: 0.5s;
}

.animate-particle-float-3 {
    animation: particle-float-3 2.8s ease-in-out infinite;
    animation-delay: 1s;
}

.animate-warning-pulse {
    animation: warning-pulse 1.5s ease-out infinite;
}

.animate-bounce-gentle {
    animation: bounce-gentle 1s ease-in-out infinite;
}

.animate-ripple-danger {
    animation: ripple-danger 0.6s ease-out;
}

/* Enhanced hover states */
.group:hover {
    transform: translateY(-2px);
}

.group:active {
    transform: translateY(0px) scale(0.98);
}
</style>