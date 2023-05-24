import { onUnmounted } from 'vue'

export function useGlobalEventListener(eventName, callback) {
  Innoclapps.$on(eventName, callback)

  onUnmounted(() => {
    Innoclapps.$off(eventName, callback)
  })
}
