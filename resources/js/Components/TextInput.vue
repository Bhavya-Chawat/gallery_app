<script setup>
import { onMounted, ref } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
})

const emit = defineEmits(['update:modelValue'])

const input = ref(null)

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus()
    }
})

defineExpose({ focus: () => input.value.focus() })
</script>

<template>
    <div class="relative group">
        <!-- Animated background particles -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1 left-2 w-0.5 h-0.5 bg-violet-400 rounded-full animate-pulse opacity-40"></div>
            <div class="absolute top-3 right-4 w-1 h-1 bg-cyan-400 rounded-full animate-bounce opacity-30" style="animation-delay: 0.5s;"></div>
            <div class="absolute bottom-2 left-6 w-0.5 h-0.5 bg-purple-400 rounded-full animate-ping opacity-50" style="animation-delay: 1s;"></div>
        </div>

        <!-- Input glow effect container -->
        <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 rounded-xl opacity-0 group-hover:opacity-30 group-focus-within:opacity-50 transition-opacity duration-500 blur-sm"></div>
        
        <!-- Enhanced input with glassmorphism -->
        <input
            class="relative w-full px-4 py-3 
                   bg-slate-900/60 backdrop-blur-xl 
                   border border-slate-700/50 rounded-xl 
                   text-slate-100 placeholder-slate-400 
                   shadow-lg shadow-slate-900/20
                   focus:bg-slate-800/60 focus:border-violet-500/50 focus:ring-4 focus:ring-violet-500/20 focus:shadow-xl focus:shadow-violet-500/10
                   hover:border-slate-600/50 hover:bg-slate-800/40 hover:shadow-xl
                   transition-all duration-300 outline-none
                   group-focus-within:scale-[1.02]"
            :value="props.modelValue"
            @input="emit('update:modelValue', $event.target.value)"
            ref="input"
        />
        
        <!-- Input shimmer effect -->
        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-transparent via-white/5 to-transparent -skew-x-12 opacity-0 group-focus-within:opacity-100 group-focus-within:animate-shimmer transition-opacity duration-700 pointer-events-none"></div>

        <!-- Focus ring gradient -->
        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/20 via-cyan-500/20 to-purple-500/20 opacity-0 group-focus-within:opacity-100 transition-opacity duration-300 pointer-events-none -z-10 blur-xl"></div>

        <!-- Floating focus particles -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-0 group-focus-within:opacity-100 transition-opacity duration-500">
            <div class="absolute top-2 left-4 w-1 h-1 bg-violet-400 rounded-full animate-float opacity-60"></div>
            <div class="absolute top-4 right-8 w-1.5 h-1.5 bg-cyan-400 rounded-full animate-float opacity-40" style="animation-delay: 0.8s;"></div>
            <div class="absolute bottom-3 left-8 w-0.5 h-0.5 bg-purple-400 rounded-full animate-float opacity-50" style="animation-delay: 1.5s;"></div>
            <div class="absolute bottom-2 right-4 w-1 h-1 bg-violet-300 rounded-full animate-float opacity-30" style="animation-delay: 2s;"></div>
        </div>

        <!-- Border gradient animation -->
        <div class="absolute inset-0 rounded-xl opacity-0 group-focus-within:opacity-100 transition-opacity duration-500 pointer-events-none">
            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/30 via-cyan-500/30 to-purple-500/30 animate-borderRotate"></div>
        </div>
    </div>
</template>

<style scoped>
@keyframes shimmer {
    0% {
        transform: translateX(-100%) skewX(-12deg);
    }
    100% {
        transform: translateX(200%) skewX(-12deg);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0.6;
    }
    33% {
        transform: translateY(-8px) rotate(120deg);
        opacity: 1;
    }
    66% {
        transform: translateY(4px) rotate(240deg);
        opacity: 0.4;
    }
}

@keyframes borderRotate {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.animate-shimmer {
    animation: shimmer 2s ease-in-out;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-borderRotate {
    animation: borderRotate 8s linear infinite;
}

/* Custom focus styles for better visual hierarchy */
input:focus {
    transform: translateY(-1px);
}

/* Ensure smooth transitions for all states */
input::placeholder {
    transition: color 0.3s ease;
}

input:focus::placeholder {
    color: rgb(148 163 184 / 0.6);
}
</style>