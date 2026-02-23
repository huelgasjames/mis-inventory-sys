import { ref, computed, watch } from 'vue'

// Dark mode state
const darkModeState = ref(localStorage.getItem('darkMode') === 'true')

// Toggle dark mode
const toggleDarkMode = () => {
  console.log('Toggle dark mode called, current state:', darkModeState.value)
  darkModeState.value = !darkModeState.value
  localStorage.setItem('darkMode', darkModeState.value)
  console.log('New dark mode state:', darkModeState.value)
  updateDocumentClass()
}

// Update document class for CSS styling
const updateDocumentClass = () => {
  console.log('updateDocumentClass called, darkModeState.value:', darkModeState.value)
  if (darkModeState.value) {
    document.documentElement.classList.add('dark-mode')
    document.body.classList.add('dark-mode')
    console.log('Added dark-mode classes')
  } else {
    document.documentElement.classList.remove('dark-mode')
    document.body.classList.remove('dark-mode')
    console.log('Removed dark-mode classes')
  }
}

// Initialize dark mode on load
const initDarkMode = () => {
  updateDocumentClass()
}

// Watch for changes and update DOM
watch(darkModeState, updateDocumentClass)

// Export as composable
export function useDarkMode() {
  return {
    isDarkMode: computed(() => darkModeState.value),
    toggleDarkMode,
    initDarkMode
  }
}
