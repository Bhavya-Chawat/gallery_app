<template>
  <div class="space-y-1">
    <!-- Label -->
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>

    <!-- Input Container -->
    <div class="relative">
      <!-- Prefix Icon -->
      <div v-if="$slots['prefix-icon']" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <slot name="prefix-icon" />
      </div>

      <!-- Input Element -->
      <component
        :is="inputComponent"
        :id="id"
        v-model="inputValue"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :min="min"
        :max="max"
        :step="step"
        :rows="rows"
        :cols="cols"
        :class="inputClasses"
        :aria-invalid="hasError"
        :aria-describedby="hasError ? `${id}-error` : undefined"
        v-bind="$attrs"
        @blur="handleBlur"
        @focus="handleFocus"
        @input="handleInput"
      />

      <!-- Suffix Icon -->
      <div v-if="$slots['suffix-icon']" class="absolute inset-y-0 right-0 pr-3 flex items-center">
        <slot name="suffix-icon" />
      </div>

      <!-- Clear Button -->
      <button
        v-if="clearable && inputValue && !disabled && !readonly"
        @click="clearInput"
        type="button"
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <!-- Loading Spinner -->
      <div v-if="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
        <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
        </svg>
      </div>
    </div>

    <!-- Help Text -->
    <p v-if="help" class="text-xs text-gray-600 dark:text-gray-400">
      {{ help }}
    </p>

    <!-- Error Message -->
    <p v-if="hasError" :id="`${id}-error`" class="text-xs text-red-600 dark:text-red-400">
      {{ errorMessage }}
    </p>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: [String, Number, Boolean, Array],
  type: {
    type: String,
    default: 'text'
  },
  label: String,
  placeholder: String,
  help: String,
  error: [String, Array, Object],
  id: {
    type: String,
    default: () => `input-${Math.random().toString(36).substr(2, 9)}`
  },
  disabled: Boolean,
  readonly: Boolean,
  required: Boolean,
  loading: Boolean,
  clearable: Boolean,
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'filled', 'flushed'].includes(value)
  },
  // For number inputs
  min: [String, Number],
  max: [String, Number],
  step: [String, Number],
  // For textarea
  rows: {
    type: Number,
    default: 3
  },
  cols: Number
})

const emit = defineEmits(['update:modelValue', 'blur', 'focus', 'input', 'clear'])

const isFocused = ref(false)

const inputComponent = computed(() => {
  return props.type === 'textarea' ? 'textarea' : 'input'
})

const inputValue = computed({
  get() {
    return props.modelValue
  },
  set(value) {
    emit('update:modelValue', value)
  }
})

const hasError = computed(() => {
  if (typeof props.error === 'string') return props.error.length > 0
  if (Array.isArray(props.error)) return props.error.length > 0
  if (typeof props.error === 'object' && props.error !== null) {
    return Object.keys(props.error).length > 0
  }
  return false
})

const errorMessage = computed(() => {
  if (typeof props.error === 'string') return props.error
  if (Array.isArray(props.error)) return props.error[0]
  if (typeof props.error === 'object' && props.error !== null) {
    const firstKey = Object.keys(props.error)[0]
    return props.error[firstKey]
  }
  return ''
})

const inputClasses = computed(() => {
  const classes = ['block w-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
  
  // Size variants
  const sizeClasses = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-4 py-2 text-sm',
    lg: 'px-4 py-3 text-base'
  }
  
  // Variant styles
  const variantClasses = {
    default: 'border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500',
    filled: 'border-0 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-slate-800',
    flushed: 'border-0 border-b-2 border-gray-200 dark:border-gray-600 rounded-none bg-transparent text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-blue-500'
  }
  
  // Icon padding adjustments
  if (props.$slots['prefix-icon']) {
    classes.push('pl-10')
  }
  if (props.$slots['suffix-icon'] || props.clearable || props.loading) {
    classes.push('pr-10')
  }
  
  // State-based styling
  if (hasError.value) {
    classes.push('border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500')
  }
  
  if (props.disabled) {
    classes.push('opacity-50 cursor-not-allowed bg-gray-50 dark:bg-slate-700')
  }
  
  if (props.readonly) {
    classes.push('bg-gray-50 dark:bg-slate-700')
  }
  
  classes.push(sizeClasses[props.size])
  classes.push(variantClasses[props.variant])
  
  return classes.join(' ')
})

const handleBlur = (event) => {
  isFocused.value = false
  emit('blur', event)
}

const handleFocus = (event) => {
  isFocused.value = true
  emit('focus', event)
}

const handleInput = (event) => {
  emit('input', event)
}

const clearInput = () => {
  inputValue.value = ''
  emit('clear')
}
</script>