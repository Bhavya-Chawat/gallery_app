<template>
  <div class="relative">
    <!-- Prefix Icon -->
    <div v-if="prefixIcon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <component :is="prefixIcon" class="h-5 w-5 text-gray-400" />
    </div>

    <!-- Input Field -->
    <input
      :id="id"
      :name="name"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :readonly="readonly"
      :required="required"
      :autocomplete="autocomplete"
      :class="inputClasses"
      @input="$emit('update:modelValue', $event.target.value)"
      @focus="focused = true"
      @blur="focused = false"
      v-bind="$attrs"
      ref="input"
    />

    <!-- Suffix Icon -->
    <div v-if="suffixIcon || type === 'password'" class="absolute inset-y-0 right-0 flex items-center">
      <!-- Password Toggle -->
      <button
        v-if="type === 'password'"
        type="button"
        @click="togglePassword"
        class="pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none"
      >
        <EyeIcon v-if="showPassword" class="h-5 w-5" />
        <EyeSlashIcon v-else class="h-5 w-5" />
      </button>
      
      <!-- Custom Suffix Icon -->
      <div v-else-if="suffixIcon" class="pr-3 pointer-events-none">
        <component :is="suffixIcon" class="h-5 w-5 text-gray-400" />
      </div>
    </div>

    <!-- Loading Spinner -->
    <div v-if="loading" class="absolute inset-y-0 right-0 flex items-center pr-3">
      <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  id: {
    type: String,
    default: () => `input-${Math.random().toString(36).substr(2, 9)}`
  },
  name: {
    type: String,
    default: null
  },
  type: {
    type: String,
    default: 'text'
  },
  modelValue: {
    type: [String, Number],
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  autocomplete: {
    type: String,
    default: null
  },
  prefixIcon: {
    type: [String, Object],
    default: null
  },
  suffixIcon: {
    type: [String, Object],
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: Boolean,
    default: false
  },
  success: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  }
})

defineEmits(['update:modelValue'])

const input = ref()
const focused = ref(false)
const showPassword = ref(false)

const inputClasses = computed(() => {
  const baseClasses = [
    'block w-full rounded-lg border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1',
    'bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100',
    'placeholder-gray-500 dark:placeholder-gray-400'
  ]

  // Size classes
  const sizeClasses = {
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-2.5 text-sm',
    lg: 'px-4 py-3 text-base'
  }

  // Prefix/Suffix padding
  const paddingClasses = []
  if (props.prefixIcon) {
    paddingClasses.push(props.size === 'sm' ? 'pl-10' : 'pl-11')
  }
  if (props.suffixIcon || props.type === 'password' || props.loading) {
    paddingClasses.push(props.size === 'sm' ? 'pr-10' : 'pr-11')
  }

  // State classes
  let stateClasses = []
  if (props.error) {
    stateClasses = [
      'border-red-300 dark:border-red-600',
      'focus:ring-red-500 focus:border-red-500'
    ]
  } else if (props.success) {
    stateClasses = [
      'border-emerald-300 dark:border-emerald-600',
      'focus:ring-emerald-500 focus:border-emerald-500'
    ]
  } else {
    stateClasses = [
      'border-gray-300 dark:border-slate-600',
      'focus:ring-blue-500 focus:border-blue-500',
      'hover:border-gray-400 dark:hover:border-slate-500'
    ]
  }

  // Disabled state
  if (props.disabled) {
    stateClasses.push('opacity-50 cursor-not-allowed bg-gray-50 dark:bg-slate-800')
  }

  return [
    ...baseClasses,
    sizeClasses[props.size],
    ...paddingClasses,
    ...stateClasses
  ].join(' ')
})

const togglePassword = () => {
  showPassword.value = !showPassword.value
  input.value.type = showPassword.value ? 'text' : 'password'
}

// Expose focus method
const focus = () => {
  input.value.focus()
}

defineExpose({ focus })
</script>