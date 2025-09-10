<template>
  <div class="tag-input">
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
    </label>
    
    <!-- Selected Tags -->
    <div v-if="selectedTags.length" class="flex flex-wrap gap-2 mb-3">
      <span
        v-for="tag in selectedTags"
        :key="tag.id || tag.name"
        :class="[
          'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
          tag.color ? `bg-${getTagColorClass(tag.color)}-100 text-${getTagColorClass(tag.color)}-800` : 'bg-blue-100 text-blue-800'
        ]"
      >
        {{ tag.name }}
        <button
          @click="removeTag(tag)"
          class="ml-2 inline-flex items-center justify-center w-4 h-4 rounded-full hover:bg-black hover:bg-opacity-10"
        >
          <XMarkIcon class="w-3 h-3" />
        </button>
      </span>
    </div>

    <!-- Input Field -->
    <div class="relative">
      <input
        ref="input"
        v-model="inputValue"
        @input="handleInput"
        @keydown="handleKeydown"
        @focus="showSuggestions = true"
        @blur="handleBlur"
        type="text"
        :placeholder="placeholder"
        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
      />

      <!-- Suggestions Dropdown -->
      <div
        v-if="showSuggestions && (filteredSuggestions.length || inputValue.trim())"
        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
      >
        <!-- Existing tag suggestions -->
        <button
          v-for="suggestion in filteredSuggestions"
          :key="suggestion.id"
          @mousedown.prevent="selectTag(suggestion)"
          class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center justify-between"
        >
          <span>{{ suggestion.name }}</span>
          <span class="text-xs text-gray-500">{{ suggestion.usage_count || 0 }} uses</span>
        </button>

        <!-- Create new tag option -->
        <button
          v-if="inputValue.trim() && !tagExists(inputValue.trim())"
          @mousedown.prevent="createNewTag"
          class="w-full px-4 py-2 text-left hover:bg-gray-100 border-t border-gray-200 text-blue-600"
        >
          <span class="flex items-center">
            <PlusIcon class="w-4 h-4 mr-2" />
            Create "{{ inputValue.trim() }}"
          </span>
        </button>

        <!-- No results -->
        <div
          v-if="!filteredSuggestions.length && !inputValue.trim()"
          class="px-4 py-2 text-gray-500 text-sm"
        >
          Start typing to search tags...
        </div>
      </div>
    </div>

    <!-- Help Text -->
    <p v-if="helpText" class="mt-2 text-sm text-gray-500">
      {{ helpText }}
    </p>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { XMarkIcon, PlusIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  suggestions: {
    type: Array,
    default: () => []
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Type to search or create tags...'
  },
  helpText: {
    type: String,
    default: 'Press Enter or comma to add tags'
  },
  maxTags: {
    type: Number,
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'tag-created'])

const input = ref(null)
const inputValue = ref('')
const showSuggestions = ref(false)
const selectedTags = ref([...props.modelValue])

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  selectedTags.value = [...newValue]
}, { deep: true })

// Emit changes when selectedTags changes
watch(selectedTags, (newTags) => {
  emit('update:modelValue', newTags)
}, { deep: true })

const filteredSuggestions = computed(() => {
  if (!inputValue.value.trim()) return []
  
  const query = inputValue.value.toLowerCase().trim()
  const selectedTagNames = selectedTags.value.map(tag => tag.name.toLowerCase())
  
  return props.suggestions
    .filter(suggestion => 
      suggestion.name.toLowerCase().includes(query) &&
      !selectedTagNames.includes(suggestion.name.toLowerCase())
    )
    .slice(0, 10) // Limit to 10 suggestions
})

const handleInput = () => {
  showSuggestions.value = true
}

const handleKeydown = (e) => {
  if (e.key === 'Enter' || e.key === ',') {
    e.preventDefault()
    if (inputValue.value.trim()) {
      createNewTag()
    }
  } else if (e.key === 'Backspace' && !inputValue.value && selectedTags.value.length) {
    // Remove last tag when backspace is pressed on empty input
    removeTag(selectedTags.value[selectedTags.value.length - 1])
  } else if (e.key === 'Escape') {
    showSuggestions.value = false
    input.value.blur()
  }
}

const handleBlur = () => {
  // Delay hiding suggestions to allow for click events
  setTimeout(() => {
    showSuggestions.value = false
  }, 150)
}

const selectTag = (tag) => {
  if (props.maxTags && selectedTags.value.length >= props.maxTags) {
    return
  }
  
  const exists = selectedTags.value.some(selected => 
    selected.id === tag.id || selected.name.toLowerCase() === tag.name.toLowerCase()
  )
  
  if (!exists) {
    selectedTags.value.push(tag)
    inputValue.value = ''
    showSuggestions.value = false
  }
}

const createNewTag = () => {
  const tagName = inputValue.value.trim()
  if (!tagName) return
  
  if (props.maxTags && selectedTags.value.length >= props.maxTags) {
    return
  }
  
  if (tagExists(tagName)) return
  
  const newTag = {
    id: `new-${Date.now()}`,
    name: tagName,
    color: getRandomColor(),
    is_new: true
  }
  
  selectedTags.value.push(newTag)
  inputValue.value = ''
  showSuggestions.value = false
  
  emit('tag-created', newTag)
}

const removeTag = (tagToRemove) => {
  selectedTags.value = selectedTags.value.filter(tag => 
    tag.id !== tagToRemove.id && tag.name !== tagToRemove.name
  )
}

const tagExists = (tagName) => {
  const lowerName = tagName.toLowerCase()
  return selectedTags.value.some(tag => tag.name.toLowerCase() === lowerName) ||
         props.suggestions.some(tag => tag.name.toLowerCase() === lowerName)
}

const getTagColorClass = (color) => {
  // Remove # if present and map to Tailwind color
  const cleanColor = color?.replace('#', '')
  const colorMap = {
    'red': 'red',
    'blue': 'blue',
    'green': 'green',
    'yellow': 'yellow',
    'purple': 'purple',
    'pink': 'pink',
    'indigo': 'indigo'
  }
  return colorMap[cleanColor] || 'blue'
}

const getRandomColor = () => {
  const colors = ['red', 'blue', 'green', 'yellow', 'purple', 'pink', 'indigo']
  return colors[Math.floor(Math.random() * colors.length)]
}
</script>
