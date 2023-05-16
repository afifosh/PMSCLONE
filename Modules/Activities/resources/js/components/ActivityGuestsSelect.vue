<template>
  <IPopover
    flip
    :busy="disabled"
    class="w-72"
    :title="$t('activities::activity.guests')"
    :placement="placement"
  >
    <button
      type="button"
      class="link text-sm focus:outline-none"
      v-text="totalGuestsText"
    />

    <template #popper>
      <div class="p-4">
        <IFormGroup class="relative">
          <IFormInput
            @input="search"
            v-model="searchQuery"
            :placeholder="searchPlaceholder"
            class="pr-8"
          />
          <a
            href="#"
            @click.prevent="cancelSearch"
            v-show="searchQuery"
            class="absolute right-3 top-2.5 focus:outline-none"
          >
            <Icon icon="X" class="h-5 w-5 text-neutral-400" />
          </a>
        </IFormGroup>
        <IOverlay :show="isLoading">
          <p
            v-show="
              isSearching &&
              !hasSearchResults &&
              !isLoading &&
              !minimumAsyncCharactersRequirement
            "
            class="text-center text-sm text-neutral-600 dark:text-neutral-300"
            v-t="'core::app.no_search_results'"
          />
          <p
            v-show="isSearching && minimumAsyncCharactersRequirement"
            class="text-center text-sm text-neutral-600 dark:text-neutral-300"
            v-t="{
              path: 'core::app.type_more_to_search',
              args: { characters: totalCharactersLeftToPerformSearch },
            }"
          />
          <div class="max-h-96 overflow-y-auto break-all px-1">
            <div v-for="data in guestables" :key="data.resource">
              <p
                v-show="data.records.length > 0"
                class="mb-2 mt-3 text-sm font-medium text-neutral-700 dark:text-neutral-100"
                v-text="data.title"
              />
              <IFormCheckbox
                v-for="record in data.records"
                :key="data.resource + '-' + record.id"
                :id="data.resource + '-' + record.id"
                :value="record.id"
                @change="onChange(record, data.resource, data.is_search)"
                v-model:checked="selected[data.resource]"
              >
                {{ record.guest_display_name }}
                {{ record.guest_email ? `(${record.guest_email})` : '' }}
              </IFormCheckbox>
            </div>
          </div>
        </IOverlay>
      </div>
    </template>
  </IPopover>
</template>
<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import findIndex from 'lodash/findIndex'
import debounce from 'lodash/debounce'
import map from 'lodash/map'
import filter from 'lodash/filter'
import uniq from 'lodash/uniq'
import isObject from 'lodash/isObject'
import sortBy from 'lodash/sortBy'
import { CancelToken } from '~/Core/resources/js/services/HTTP'
import { useI18n } from 'vue-i18n'
import { useLoader } from '~/Core/resources/js/composables/useLoader'
import { useApp } from '~/Core/resources/js/composables/useApp'

const emit = defineEmits(['update:modelValue', 'change'])

const props = defineProps({
  /**
   * The actual v-model for the selected guests
   *
   * @type {Object}
   */
  modelValue: {},

  /**
   * Available contacts for selection
   *
   * @type {Object}
   */
  contacts: {},

  // All guests of the record, use only on EDIT
  // We need all the guests in case there are guests not directly associated with the resource
  guests: {},

  /**
   * Indicates whether the popover is disabled
   *
   * @type {Boolean}
   */
  disabled: {
    type: Boolean,
    default: false,
  },

  /**
   * The popover placement
   *
   * @type {String}
   */
  placement: {
    type: String,
    default: 'bottom',
  },
})

const { t } = useI18n()
const { setLoading, isLoading } = useLoader()

const minimumAsyncCharacters = 2
const totalAsyncSearchCharacters = ref(0)
const minimumAsyncCharactersRequirement = ref(false)
const limitSearchResults = 5
const searchQuery = ref('')
// The selected guests
const selected = ref({})
// Guests selected from search results
const selectedFromSearch = ref({})
const searchResults = ref({})
const cancelTokens = {}

const template = {
  contacts: {
    title: t('contacts::contact.contacts'),
    resource: 'contacts',
  },
  users: {
    title: t('users::user.users'),
    resource: 'users',
  },
}

const { users } = useApp()

watch(
  selected,
  newVal => {
    emit('update:modelValue', newVal)
  },
  { deep: true }
)

watch(
  () => props.guests,
  () => {
    setSelectedGuests()
  }
)

/**
 * The available guestable resources
 */
const resources = computed(() => Object.keys(template))

/**
 * Indicates the total characters left so the request can be performed
 */
const totalCharactersLeftToPerformSearch = computed(
  () => minimumAsyncCharacters - totalAsyncSearchCharacters.value
)

/**
 * The search input placeholder
 */
const searchPlaceholder = computed(() => t('core::app.search_records'))

/**
 * Check whether there are search results
 */
const hasSearchResults = computed(() => {
  let hasSearchResult = false
  resources.value.every(resource => {
    hasSearchResult = searchResults.value[resource]
      ? searchResults.value[resource].records.length > 0
      : false
    return hasSearchResult ? false : true
  })

  return hasSearchResult
})

/**
 * Checks whether the view is search
 */
const isSearching = computed(() => searchQuery.value != '')

/**
 * The guests text
 */
const totalGuestsText = computed(() => {
  let totalSelected = 0
  resources.value.forEach(resource => {
    totalSelected += selected.value[resource]
      ? selected.value[resource].length
      : 0
  })

  return t('activities::activity.count_guests', totalSelected)
})

/**
 * The guestables to be display in the popover
 */
const guestables = computed(() => {
  if (hasSearchResults.value) {
    return searchResults.value
  }

  let guestables = {}

  let addRecord = (resourceName, record) => {
    if (
      findIndex(guestables[resourceName].records, ['id', Number(record.id)]) ===
      -1
    ) {
      guestables[resourceName].records.push(record)
    }
  }

  resources.value.forEach(resource => {
    guestables[resource] = Object.assign({}, template[resource], {
      records: [],
    })

    if (props.guests) {
      filter(props.guests, ['resource_name', resource]).forEach(record =>
        addRecord(resource, record)
      )
    }

    // Check for any selected from search
    if (selectedFromSearch.value[resource]) {
      selectedFromSearch.value[resource].records.forEach(record =>
        addRecord(resource, record)
      )
    }
  })

  users.value.forEach(user => addRecord('users', user))
  props.contacts.forEach(contact => addRecord('contacts', contact))

  return map(guestables, data => {
    data.records = sortBy(data.records, 'guest_display_name')

    return data
  })
})

/**
 * Create search requests for the Promise
 *
 * @param  {String} q
 *
 * @return {Array}
 */
function createResolveableRequests(q) {
  // The order of the promises must be the same
  // like in the order of the template keys data property
  // Create promises array
  let promises = []
  resources.value.forEach(resource => {
    promises.push(
      Innoclapps.request().get(`/${resource}/search`, {
        params: {
          q: q,
          take: limitSearchResults,
        },
        cancelToken: new CancelToken(token => (cancelTokens[resource] = token)),
      })
    )
  })

  return promises
}

/**
 * Cancel any previous requests via the cancel token
 *
 * @return {Void}
 */
function cancelPreviousRequests() {
  Object.keys(cancelTokens).forEach(resource => {
    if (cancelTokens[resource]) {
      cancelTokens[resource]()
    }
  })
}

/**
 * On checkbox change
 *
 * @param  {Object} record
 * @param  {String} resource
 * @param  {Boolean} fromSearch
 *
 * @return {Void}
 */
function onChange(record, resource, fromSearch) {
  if (!selectedFromSearch.value[resource] && fromSearch) {
    selectedFromSearch.value[resource] = {
      records: [],
      is_search: fromSearch,
    }
  }
  nextTick(() => {
    // User checked record selected from search
    if (selected.value[resource].includes(record.id) && fromSearch) {
      selectedFromSearch.value[resource].records.push(record)
    } else if (selectedFromSearch.value[resource]) {
      // Unchecked, now remove it it from the selectedFromSearch
      let selectedIndex = findIndex(
        selectedFromSearch.value[resource].records,
        ['id', Number(record.id)]
      )
      if (selectedIndex != -1) {
        selectedFromSearch.value[resource].records.splice(selectedIndex, 1)
      }
    }

    emit('change', selected.value)
  })
}

/**
 * Cancel the search view
 *
 * @return {Void}
 */
function cancelSearch() {
  searchQuery.value = ''
  search('')
}

/**
 * Search records ASYNC
 *
 * @param  {Array}  q
 *
 * @return {Void}
 */
const search = debounce(function (q) {
  const totalCharacters = q.length

  if (totalCharacters === 0) {
    searchResults.value = {}
    return
  }

  totalAsyncSearchCharacters.value = totalCharacters

  if (totalCharacters < minimumAsyncCharacters) {
    minimumAsyncCharactersRequirement.value = true

    return q
  }

  minimumAsyncCharactersRequirement.value = false
  cancelPreviousRequests()
  setLoading(true)

  Promise.all(createResolveableRequests(q)).then(values => {
    resources.value.forEach((resource, key) => {
      searchResults.value[resource] = Object.assign({}, template[resource], {
        records: map(values[key].data, record => {
          record.from_search = true
          return record
        }),
        is_search: true,
      })
    })

    setLoading(false)
  })
}, 650)

/**
 * Reset selected
 *
 * @return {Void}
 */
function setSelectedGuests() {
  let selectedGuests = {}
  resources.value.forEach(resource => {
    let resourceSelected = []
    if (props.modelValue && props.modelValue[resource]) {
      resourceSelected = isObject(props.modelValue[resource][0])
        ? map(props.modelValue[resource], record => record.id)
        : props.modelValue[resource]
    }

    // Set the selected value via the guests
    if (props.guests) {
      resourceSelected = resourceSelected.concat(
        filter(props.guests, ['resource_name', resource]).map(
          record => record.id
        )
      )
    }
    selectedGuests[resource] = uniq(resourceSelected)
  })

  selected.value = selectedGuests
}

onMounted(() => {
  nextTick(setSelectedGuests)
})

defineExpose({ setSelectedGuests })
</script>
