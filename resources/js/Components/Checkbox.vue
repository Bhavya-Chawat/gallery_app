<template>
  <label class="inline-flex items-center cursor-pointer">
    <input
      :id="id"
      type="checkbox"
      :value="value"
      :checked="checked"
      :name="name"
      :disabled="disabled"
      :required="required"
      @change="$emit('update:modelValue', $event.target.checked)"
      class="sr-only"
    />
    
    <div class="relative">
      <!-- Checkbox Visual -->
      <div
        class="flex items-center justify-center w-5 h-5 border-2 rounded-md transition-all duration-200 ease-in-out"
        :class="[
          checked 
            ? 'bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 border-blue-600 shadow-md' 
            : 'bg-white dark:bg-slate-700 border-gray-300 dark:border-slate-600 hover:border-blue-400 dark:hover:border-blue-500',
          disabled 
            ? 'opacity-50 cursor-not-allowed' 
            : 'cursor-pointer',
          indeterminate 
            ? 'bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 border-blue-600' 
            : ''
        ]"
      >
        <!-- Check Mark -->
        <transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 scale-0"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-0"
        >
          <CheckIcon
            v-if="checked && !indeterminate"
            class="w-3 h-3 text-white"
          />
        </transition>
        
        <!-- Indeterminate Mark -->
        <transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 scale-0"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-0"
        >
          <MinusIcon
            v-if="indeterminate"
            class="w-3 h-3 text-white"
          />
        </transition>
      </div>
      
      <!-- Focus Ring -->
      <div
        v-if="!disabled"
        class="absolute inset-0 rounded-md ring-2 ring-blue-500 ring-opacity-0 transition-all duration-200"
        :class="{ 'ring-opacity-50': focused }"
      />
    </div>
    
    <!-- Label -->
    <span
      v-if="label || $slots.default"
      class="ml-3 text-sm font-medium select-none"
      :class="[
        disabled 
          ? 'text-gray-400 dark:text-gray-600' 
          : 'text-gray-900 dark:text-gray-100'
      ]"
    >
      <slot>{{ label }}</slot>
    </span>
    
    <!-- Description -->
    <span
      v-if="description"
      class="ml-3 text-sm text-gray-500 dark:text-gray-400 select-none"
      :class="{ 'opacity-50': disabled }"
    >
      {{ description }}
    </span>
  </label>
</template>

<script setup>
import { ref, computed } from 'vue'
import { CheckIcon, MinusIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  id: {
    type: String,
    default: () => `checkbox-${Math.random().toString(36).substr(2, 9)}`
  },
  modelValue: {
    type: [Boolean, Array],
    default: false
  },
  value: {
    type: [String, Number, Boolean],
    default: true
  },
  name: {
    type: String,
    default: null
  },
  label: {
    type: String,
    default: null
  },
  description: {
    type: String,
    default: null
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  indeterminate: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])

const focused = ref(false)

const checked = computed(() => {
  if (Array.isArray(props.modelValue)) {
    return props.modelValue.includes(props.value)
  }
  return props.modelValue
})
</script>

<style scoped>
/* Additional focus styles for accessibility */
.sr-only:focus + div .absolute {
  @apply ring-opacity-50;
}
</style>