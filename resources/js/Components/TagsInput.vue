<template>
  <div class="w-full relative group">
    <!-- Animated background particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-1 left-2 w-0.5 h-0.5 bg-violet-400 rounded-full animate-pulse opacity-40"></div>
      <div class="absolute top-3 right-4 w-1 h-1 bg-cyan-400 rounded-full animate-bounce opacity-30" style="animation-delay: 0.7s;"></div>
      <div class="absolute bottom-2 left-6 w-0.5 h-0.5 bg-purple-400 rounded-full animate-ping opacity-50" style="animation-delay: 1.2s;"></div>
    </div>

    <!-- Enhanced input container -->
    <div class="relative">
      <!-- Input glow effect -->
      <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 rounded-xl opacity-0 group-hover:opacity-30 group-focus-within:opacity-50 transition-opacity duration-500 blur-sm"></div>
      
      <!-- Main input -->
      <input
        ref="input"
        v-model="inputValue"
        @keydown.enter.prevent="addTag"
        @keydown.comma.prevent="addTag"
        @keydown.backspace="handleBackspace"
        type="text"
        :placeholder="placeholder"
        class="relative w-full px-4 py-3 bg-slate-900/60 backdrop-blur-xl border border-slate-700/50 rounded-xl 
               text-slate-100 placeholder-slate-400 
               focus:bg-slate-800/60 focus:border-violet-500/50 focus:ring-4 focus:ring-violet-500/20 
               hover:border-slate-600/50 hover:bg-slate-800/40
               transition-all duration-300 outline-none
               shadow-lg shadow-slate-900/20"
      />
      
      <!-- Input shimmer effect -->
      <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-transparent via-white/5 to-transparent -skew-x-12 opacity-0 group-focus-within:opacity-100 group-focus-within:animate-shimmer transition-opacity duration-700 pointer-events-none"></div>
    </div>
    
    <!-- Enhanced tags container -->
    <div v-if="modelValue.length > 0" class="flex flex-wrap gap-3 mt-4 animate-fadeInUp">
      <span
        v-for="(tag, index) in modelValue"
        :key="index"
        class="group/tag relative inline-flex items-center px-4 py-2 text-sm font-medium
               bg-gradient-to-r from-slate-800/80 via-slate-700/80 to-slate-800/80 
               backdrop-blur-xl border border-slate-600/30
               text-slate-100 rounded-full
               hover:from-violet-600/20 hover:via-purple-600/20 hover:to-cyan-600/20
               hover:border-violet-500/40 hover:shadow-lg hover:shadow-violet-500/20
               hover:scale-105 hover:-translate-y-0.5
               transition-all duration-300 cursor-default
               shadow-md shadow-slate-900/30"
      >
        <!-- Tag background glow -->
        <div class="absolute inset-0 rounded-full bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 opacity-0 group-hover/tag:opacity-20 transition-opacity duration-300"></div>
        
        <!-- Tag text with gradient effect -->
        <span class="relative bg-gradient-to-r from-slate-100 to-slate-300 bg-clip-text text-transparent group-hover/tag:from-white group-hover/tag:to-slate-100 transition-all duration-300">
          {{ tag }}
        </span>
        
        <!-- Enhanced remove button -->
        <button
          @click="removeTag(index)"
          class="relative ml-2 flex items-center justify-center w-5 h-5 
                 text-slate-400 hover:text-red-400 
                 hover:bg-red-500/20 rounded-full
                 focus:outline-none focus:ring-2 focus:ring-red-500/30
                 transition-all duration-200 hover:scale-110
                 group-hover/tag:text-slate-300"
          type="button"
        >
          <!-- Button glow effect -->
          <div class="absolute inset-0 rounded-full bg-red-500/0 hover:bg-red-500/30 transition-colors duration-200"></div>
          
          <!-- X icon with enhanced styling -->
          <span class="relative text-base font-semibold leading-none hover:rotate-90 transition-transform duration-200">
            ×
          </span>
        </button>
        
        <!-- Tag floating particles -->
        <div class="absolute -top-1 -right-1 w-1 h-1 bg-violet-400 rounded-full opacity-0 group-hover/tag:opacity-60 group-hover/tag:animate-ping transition-opacity duration-300"></div>
        <div class="absolute -bottom-1 -left-1 w-0.5 h-0.5 bg-cyan-400 rounded-full opacity-0 group-hover/tag:opacity-40 group-hover/tag:animate-pulse transition-opacity duration-300" style="animation-delay: 0.3s;"></div>
      </span>
    </div>

    <!-- Floating input particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-0 group-focus-within:opacity-100 transition-opacity duration-500">
      <div class="absolute top-2 left-4 w-1 h-1 bg-violet-400 rounded-full animate-float opacity-60"></div>
      <div class="absolute top-4 right-8 w-1.5 h-1.5 bg-cyan-400 rounded-full animate-float opacity-40" style="animation-delay: 0.8s;"></div>
      <div class="absolute bottom-3 left-8 w-0.5 h-0.5 bg-purple-400 rounded-full animate-float opacity-50" style="animation-delay: 1.5s;"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  placeholder: { type: String, default: 'Add tags...' },
})

const emit = defineEmits(['update:modelValue'])

const inputValue = ref('')
const tags = ref([...props.modelValue])

watch(() => props.modelValue, (newTags) => {
  tags.value = [...newTags]
})

watch(tags, (newTags) => {
  emit('update:modelValue', newTags)
}, { deep: true })

const addTag = () => {
  const tag = inputValue.value.trim()
  if (tag && !tags.value.includes(tag)) {
    tags.value.push(tag)
    inputValue.value = ''
  }
}

const removeTag = (index) => {
  tags.value.splice(index, 1)
}

const handleBackspace = () => {
  if (inputValue.value === '' && tags.value.length > 0) {
    tags.value.pop()
  }
}
</script>

<style scoped>
@keyframes shimmer {
  0% {
    transform: translateX(-100%) skewX(-12deg);
  }
  100% {
    transform: translateX(200%) skewX(-12deg);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  33% {
    transform: translateY(-10px) rotate(120deg);
  }
  66% {
    transform: translateY(5px) rotate(240deg);
  }
}

.animate-shimmer {
  animation: shimmer 2s ease-in-out;
}

.animate-fadeInUp {
  animation: fadeInUp 0.5s ease-out;
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}
</style>