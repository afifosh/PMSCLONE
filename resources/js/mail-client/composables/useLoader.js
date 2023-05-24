import { ref } from 'vue'

export function useLoader(defaultValue = false) {
  const isLoading = ref(defaultValue)

  function setLoading(value = true) {
    isLoading.value = value
  }

  return { setLoading, isLoading }
}
