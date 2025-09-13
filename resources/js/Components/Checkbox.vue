<script setup>
defineProps({
    checked: Boolean,
    value: {
        type: String,
        default: null,
    },
})

defineEmits(['update:checked'])
</script>

<template>
    <input
        type="checkbox"
        :checked="checked"
        :value="value"
        @change="$emit('update:checked', $event.target.checked)"
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
    />
</template>
/*<script setup>
defineProps({
    checked: Boolean,
    value: {
        type: String,
        default: null,
    },
})

defineEmits(['update:checked'])
</script>

<template>
    <div class="relative inline-block group">
        <!-- Animated Background Glow -->
        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
            <div class="absolute inset-0 bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 rounded-xl blur-lg animate-pulse-glow"></div>
        </div>

        <!-- Floating Ring Animation -->
        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
            <div class="absolute inset-0 border border-violet-400/30 rounded-xl animate-ring-pulse"></div>
        </div>

        <input
            type="checkbox"
            :checked="checked"
            :value="value"
            @change="$emit('update:checked', $event.target.checked)"
            class="relative z-10 w-5 h-5 rounded-lg transition-all duration-300 cursor-pointer
                   bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl
                   border-2 border-slate-600/50 hover:border-violet-400/70
                   text-transparent
                   shadow-lg hover:shadow-xl hover:shadow-violet-500/25
                   focus:ring-4 focus:ring-violet-500/30 focus:ring-offset-0 focus:outline-none
                   checked:bg-gradient-to-br checked:from-violet-500 checked:via-purple-500 checked:to-cyan-500
                   checked:border-violet-400 checked:shadow-2xl checked:shadow-violet-500/50
                   hover:scale-110 active:scale-95
                   before:content-['✓'] before:absolute before:inset-0 before:flex before:items-center before:justify-center
                   before:text-white before:font-bold before:text-xs before:opacity-0 before:scale-50 before:transition-all before:duration-300
                   checked:before:opacity-100 checked:before:scale-100 checked:before:animate-bounce-in"
        />

        <!-- Animated Checkmark Enhancement -->
        <div class="absolute inset-0 pointer-events-none opacity-0 group-has-[:checked]:opacity-100 transition-all duration-500 group-has-[:checked]:animate-checkmark-draw">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="3" 
                    d="M5 13l4 4L19 7"
                    class="animate-checkmark-path"
                    style="stroke-dasharray: 20; stroke-dashoffset: 20;"
                />
            </svg>
        </div>

        <!-- Floating Particles on Check -->
        <div class="absolute inset-0 pointer-events-none opacity-0 group-has-[:checked]:opacity-100">
            <div class="absolute -top-1 -right-1 w-2 h-2 bg-violet-400 rounded-full animate-particle-float-1"></div>
            <div class="absolute -bottom-1 -left-1 w-1.5 h-1.5 bg-cyan-400 rounded-full animate-particle-float-2"></div>
            <div class="absolute top-0 -left-2 w-1 h-1 bg-purple-400 rounded-full animate-particle-float-3"></div>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse-glow {
    0%, 100% { 
        opacity: 0.2;
        transform: scale(0.95);
    }
    50% { 
        opacity: 0.4;
        transform: scale(1.05);
    }
}

@keyframes ring-pulse {
    0% {
        transform: scale(1);
        opacity: 0.5;
    }
    100% {
        transform: scale(1.3);
        opacity: 0;
    }
}

@keyframes bounce-in {
    0% { 
        transform: scale(0.3);
        opacity: 0;
    }
    50% { 
        transform: scale(1.2);
        opacity: 0.8;
    }
    100% { 
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes checkmark-draw {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes checkmark-path {
    from {
        stroke-dashoffset: 20;
    }
    to {
        stroke-dashoffset: 0;
    }
}

@keyframes particle-float-1 {
    0% { 
        transform: translate(0, 0) scale(0);
        opacity: 0;
    }
    50% { 
        transform: translate(-10px, -15px) scale(1);
        opacity: 1;
    }
    100% { 
        transform: translate(-20px, -25px) scale(0);
        opacity: 0;
    }
}

@keyframes particle-float-2 {
    0% { 
        transform: translate(0, 0) scale(0);
        opacity: 0;
    }
    50% { 
        transform: translate(10px, 15px) scale(1);
        opacity: 1;
    }
    100% { 
        transform: translate(20px, 25px) scale(0);
        opacity: 0;
    }
}

@keyframes particle-float-3 {
    0% { 
        transform: translate(0, 0) scale(0);
        opacity: 0;
    }
    50% { 
        transform: translate(-15px, 5px) scale(1);
        opacity: 1;
    }
    100% { 
        transform: translate(-25px, 10px) scale(0);
        opacity: 0;
    }
}

.animate-pulse-glow {
    animation: pulse-glow 2s ease-in-out infinite;
}

.animate-ring-pulse {
    animation: ring-pulse 1.5s ease-out infinite;
}

.animate-bounce-in {
    animation: bounce-in 0.6s ease-out;
}

.animate-checkmark-draw {
    animation: checkmark-draw 0.5s ease-out;
}

.animate-checkmark-path {
    animation: checkmark-path 0.6s ease-out forwards;
}

.animate-particle-float-1 {
    animation: particle-float-1 1s ease-out;
}

.animate-particle-float-2 {
    animation: particle-float-2 1.2s ease-out;
}

.animate-particle-float-3 {
    animation: particle-float-3 0.8s ease-out;
}

/* CSS for better browser support of :has() */
@supports not selector(:has(*)) {
    .group:hover .opacity-0 {
        opacity: 1;
    }
}
</style>