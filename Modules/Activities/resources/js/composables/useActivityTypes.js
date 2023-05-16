/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */
import { ref, computed } from 'vue'
import orderBy from 'lodash/orderBy'
import { createGlobalState } from '@vueuse/core'
import { useLoader } from '~/Core/resources/js/composables/useLoader'

export const useActivityTypes = createGlobalState(() => {
  const { setLoading, isLoading: typesAreBeingFetched } = useLoader()

  const activityTypes = ref([])

  const typesByName = computed(() => orderBy(activityTypes.value, 'name'))

  const typesForIconPicker = computed(() =>
    formatTypesForIcons(typesByName.value)
  )

  function findTypeById(id) {
    return typesByName.value.find(t => t.id == id)
  }

  function formatTypesForIcons(types) {
    return types.map(type => ({
      id: type.id,
      icon: type.icon,
      tooltip: type.name,
    }))
  }

  function setActivityTypes(types) {
    activityTypes.value = types
  }

  function fetchActivityTypes() {
    setLoading(true)

    Innoclapps.request()
      .get('/activity-types', {
        params: {
          per_page: 100,
        },
      })
      .then(({ data }) => (activityTypes.value = data.data))
      .finally(() => setLoading(false))
  }

  return {
    activityTypes,
    typesByName,
    typesAreBeingFetched,
    typesForIconPicker,

    findTypeById,
    formatTypesForIcons,
    setActivityTypes,

    fetchActivityTypes,
  }
})
