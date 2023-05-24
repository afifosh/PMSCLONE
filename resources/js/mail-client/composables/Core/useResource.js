import { ref, unref, watchEffect } from 'vue'

export function useResource(resourceName) {
  const singularName = ref(null)

  function getSingularName(name) {
    return Innoclapps.config(`resources.${unref(name || resourceName)}`)
      .singularName
  }

  watchEffect(() => {
    if (unref(resourceName)) {
      singularName.value = getSingularName()
    } else {
      singularName.value = null
    }
  })

  return { singularName, getSingularName }
}
