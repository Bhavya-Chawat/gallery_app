<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message" class="relative overflow-hidden animate-slide-in-down">
        <!-- Glassmorphism background with gradient border -->
        <div class="relative rounded-lg bg-gradient-to-r from-red-500/10 via-rose-500/10 to-pink-500/10 backdrop-blur-sm border border-red-500/20 p-3 shadow-lg shadow-red-500/10">
            <!-- Animated background mesh -->
            <div class="absolute inset-0 opacity-20 overflow-hidden rounded-lg">
                <div class="absolute inset-0 bg-gradient-to-r from-red-400/20 via-rose-400/20 to-pink-400/20 animate-pulse-glow"></div>
                <div class="absolute top-0 left-0 w-full h-full">
                    <div class="absolute top-1/2 left-0 w-8 h-8 bg-red-400/30 rounded-full blur-sm animate-float-left"></div>
                    <div class="absolute top-1/4 right-2 w-4 h-4 bg-rose-400/40 rounded-full blur-sm animate-float-right"></div>
                    <div class="absolute bottom-1/3 left-1/3 w-6 h-6 bg-pink-400/35 rounded-full blur-sm animate-float-up"></div>
                </div>
            </div>

            <!-- Error icon with pulse effect -->
            <div class="absolute top-2 left-2 flex items-center justify-center">
                <div class="w-4 h-4 rounded-full bg-gradient-to-r from-red-500 to-rose-500 flex items-center justify-center animate-pulse-ring">
                    <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
                </div>
            </div>

            <!-- Shimmer overlay effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full animate-shimmer-error rounded-lg"></div>

            <!-- Enhanced text with gradient and glow -->
            <div class="relative pl-6">
                <p class="text-sm font-medium text-red-200 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-red-300 group-hover:to-rose-300 transition-all duration-300 leading-relaxed">
                    {{ message }}
                </p>
            </div>

            <!-- Bottom border animation -->
            <div class="absolute bottom-0 left-0 h-0.5 w-0 bg-gradient-to-r from-red-500 via-rose-500 to-pink-500 animate-expand-border rounded-full"></div>
            
            <!-- Corner accent dots -->
            <div class="absolute top-1 right-1 w-1 h-1 bg-red-400 rounded-full animate-pulse opacity-60"></div>
            <div class="absolute bottom-1 left-1 w-1 h-1 bg-rose-400 rounded-full animate-pulse opacity-60" style="animation-delay: 0.5s;"></div>
        </div>

        <!-- Subtle outer glow effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-red-500/5 via-rose-500/5 to-pink-500/5 blur-xl rounded-lg -z-10 animate-glow-pulse"></div>
    </div>
</template>

<style scoped>
/* Slide in animation */
@keyframes slide-in-down {
    0% {
        opacity: 0;
        transform: translateY(-10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-in-down {
    animation: slide-in-down 0.3s ease-out forwards;
}

/* Shimmer effect for error messages */
@keyframes shimmer-error {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.animate-shimmer-error {
    animation: shimmer-error 2s ease-in-out infinite;
    animation-delay: 1s;
}

/* Floating particles */
@keyframes float-left {
    0%, 100% { transform: translateX(0px) translateY(0px); opacity: 0.3; }
    50% { transform: translateX(-8px) translateY(-4px); opacity: 0.7; }
}

@keyframes float-right {
    0%, 100% { transform: translateX(0px) translateY(0px); opacity: 0.4; }
    50% { transform: translateX(6px) translateY(-3px); opacity: 0.8; }
}

@keyframes float-up {
    0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.3; }
    50% { transform: translateY(-6px) translateX(2px); opacity: 0.6; }
}

.animate-float-left {
    animation: float-left 3s ease-in-out infinite;
}

.animate-float-right {
    animation: float-right 4s ease-in-out infinite;
    animation-delay: 1s;
}

.animate-float-up {
    animation: float-up 5s ease-in-out infinite;
    animation-delay: 2s;
}

/* Pulse glow for background */
@keyframes pulse-glow {
    0%, 100% { 
        opacity: 0.1;
        transform: scale(1);
    }
    50% { 
        opacity: 0.3;
        transform: scale(1.02);
    }
}

.animate-pulse-glow {
    animation: pulse-glow 2s ease-in-out infinite;
}

/* Ring pulse for error icon */
@keyframes pulse-ring {
    0% {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
    }
    70% {
        box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
    }
}

.animate-pulse-ring {
    animation: pulse-ring 2s ease-out infinite;
}

/* Border expansion animation */
@keyframes expand-border {
    0% { width: 0%; }
    100% { width: 100%; }
}

.animate-expand-border {
    animation: expand-border 0.6s ease-out 0.2s forwards;
}

/* Outer glow pulse */
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
    animation: glow-pulse 3s ease-in-out infinite;
}

/* Enhanced hover states */
.group:hover .animate-shimmer-error {
    animation-duration: 1s;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .animate-slide-in-down {
        animation-duration: 0.4s;
    }
}
</style>
