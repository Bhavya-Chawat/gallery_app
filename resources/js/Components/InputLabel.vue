<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-sm font-medium text-gray-700">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
/*<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="group relative block text-sm font-medium text-slate-300 transition-all duration-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-violet-400 hover:via-purple-400 hover:to-cyan-400 cursor-pointer">
        <!-- Animated background elements -->
        <div class="absolute inset-0 -z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <!-- Floating particles -->
            <div class="absolute top-1/2 -left-1 w-1 h-1 bg-violet-400 rounded-full animate-float-particle-1 opacity-60"></div>
            <div class="absolute top-1/4 right-0 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-float-particle-2 opacity-70"></div>
            <div class="absolute bottom-1/3 left-1/2 w-0.5 h-0.5 bg-purple-400 rounded-full animate-float-particle-3 opacity-50"></div>
            
            <!-- Subtle glow background -->
            <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-purple-500/5 to-cyan-500/5 rounded-md blur-sm"></div>
        </div>

        <!-- Shimmer effect overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-out opacity-0 group-hover:opacity-100"></div>

        <!-- Label indicator dot -->
        <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-gradient-to-r from-violet-500 to-cyan-500 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:scale-125 group-hover:shadow-sm group-hover:shadow-violet-400/50"></div>

        <!-- Enhanced text with micro-animations -->
        <span class="relative inline-block group-hover:transform group-hover:translate-x-2 transition-transform duration-300 ease-out">
            <span v-if="value" class="relative">
                {{ value }}
                <!-- Underline animation -->
                <span class="absolute bottom-0 left-0 w-0 h-px bg-gradient-to-r from-violet-500 via-purple-500 to-cyan-500 group-hover:w-full transition-all duration-500 ease-out"></span>
            </span>
            <span v-else class="relative">
                <slot />
                <!-- Underline animation for slot content -->
                <span class="absolute bottom-0 left-0 w-0 h-px bg-gradient-to-r from-violet-500 via-purple-500 to-cyan-500 group-hover:w-full transition-all duration-500 ease-out"></span>
            </span>
        </span>

        <!-- Focus ring enhancement -->
        <div class="absolute inset-0 rounded-md ring-0 ring-violet-500/20 group-hover:ring-2 transition-all duration-300 pointer-events-none"></div>

        <!-- Corner accent elements -->
        <div class="absolute top-0 right-0 w-2 h-2 opacity-0 group-hover:opacity-60 transition-opacity duration-300">
            <div class="absolute top-0 right-0 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-pulse"></div>
            <div class="absolute top-1 right-1 w-0.5 h-0.5 bg-violet-400 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
        </div>

        <!-- Typography enhancement -->
        <style scoped>
            .group:hover span {
                text-shadow: 0 0 10px rgba(139, 92, 246, 0.3);
            }
        </style>
    </label>
</template>

<style scoped>
/* Floating particle animations */
@keyframes float-particle-1 {
    0%, 100% { 
        transform: translateY(0px) translateX(0px); 
        opacity: 0.4; 
    }
    50% { 
        transform: translateY(-6px) translateX(-2px); 
        opacity: 0.8; 
    }
}

@keyframes float-particle-2 {
    0%, 100% { 
        transform: translateY(0px) translateX(0px); 
        opacity: 0.3; 
    }
    50% { 
        transform: translateY(-4px) translateX(3px); 
        opacity: 0.7; 
    }
}

@keyframes float-particle-3 {
    0%, 100% { 
        transform: translateY(0px) translateX(0px); 
        opacity: 0.5; 
    }
    50% { 
        transform: translateY(-5px) translateX(-1px); 
        opacity: 0.6; 
    }
}

.animate-float-particle-1 {
    animation: float-particle-1 3s ease-in-out infinite;
}

.animate-float-particle-2 {
    animation: float-particle-2 4s ease-in-out infinite;
    animation-delay: 1s;
}

.animate-float-particle-3 {
    animation: float-particle-3 5s ease-in-out infinite;
    animation-delay: 2s;
}

/* Enhanced focus and hover states */
.group:hover {
    transform: translateY(-1px);
}

/* Subtle text glow effect */
.group:hover span {
    filter: drop-shadow(0 0 8px rgba(139, 92, 246, 0.4));
}

/* Micro-interaction for the indicator dot */
@keyframes indicator-pulse {
    0%, 100% { 
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.4);
    }
    50% { 
        transform: scale(1.1);
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
    }
}

.group:hover .w-1\.5 {
    animation: indicator-pulse 2s ease-in-out infinite;
}

/* Shimmer enhancement */
.group:focus-within .absolute.bg-gradient-to-r.from-transparent {
    animation: shimmer 1.5s ease-in-out infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateX(100%); opacity: 0; }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .group:hover span {
        transform: translateX(1px);
    }
    
    .group:hover .w-1\.5 {
        transform: translateY(-50%) scale(1.1);
    }
}

/* Accessibility enhancements */
.group:focus-visible {
    outline: 2px solid rgba(139, 92, 246, 0.5);
    outline-offset: 2px;
    border-radius: 4px;
}

/* Dark mode compatibility */
@media (prefers-color-scheme: dark) {
    .group {
        color: rgb(203 213 225); /* slate-300 */
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .group:hover {
        color: white;
        text-shadow: none;
        filter: none;
    }
    
    .group:hover .absolute.bg-gradient-to-r {
        background: white;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .group, .group *, .animate-float-particle-1, .animate-float-particle-2, .animate-float-particle-3 {
        animation: none !important;
        transition-duration: 0.01ms !important;
    }
}
</style>