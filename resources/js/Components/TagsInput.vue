<template>
  <div class="w-full">
    <input
      ref="input"
      v-model="inputValue"
      @keydown.enter.prevent="addTag"
      @keydown.comma.prevent="addTag"
      @keydown.backspace="handleBackspace"
      type="text"
      :placeholder="placeholder"
      class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
    />
    
    <div v-if="modelValue.length > 0" class="flex flex-wrap gap-2 mt-2">
      <span
        v-for="(tag, index) in modelValue"
        :key="index"
        class="inline-flex items-center px-2 py-1 text-sm bg-blue-100 text-blue-800 rounded-full"
      >
        {{ tag }}
        <button 
          @click="removeTag(index)" 
          class="ml-1 text-blue-600 hover:text-blue-800 focus:outline-none"
          type="button"
        >
          ×
        </button>
      </span>
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
