<script setup>
import { onMounted, ref } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    },
    rows: {
        type: Number,
        default: 4
    },
    required: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    autofocus: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const textarea = ref(null)

onMounted(() => {
    if (props.autofocus) {
        textarea.value.focus()
    }
})
</script>

<template>
    <div class="flex flex-col">
        <textarea
            ref="textarea"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            :rows="rows"
            :class="[
                'w-full rounded-md shadow-sm dark:bg-gray-800',
                'border-gray-300 dark:border-gray-700',
                'focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-600 dark:focus:ring-blue-600',
                'placeholder-gray-400 dark:placeholder-gray-500',
                'text-gray-900 dark:text-gray-100',
                error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '',
                disabled ? 'bg-gray-50 dark:bg-gray-900 cursor-not-allowed' : ''
            ]"
        ></textarea>

        <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </p>
    </div>
</template>