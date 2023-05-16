<template>
  <IPopover
    flip
    :busy="disabled"
    class="w-72"
    :title="$t('core::app.associate_with_record')"
    :placement="placement"
  >
    <button
      type="button"
      class="link text-sm focus:outline-none"
      v-text="associationsText"
    />

    <template #popper>
      <div class="p-4">
        <IFormGroup class="relative">
          <IFormInput
            @input="search"
            v-model="searchQuery"
            class="pr-8"
            :placeholder="searchInputPlaceholder"
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
            v-text="
              $t('core::app.type_more_to_search', {
                characters: totalCharactersLeftToPerformSearch,
              })
            "
          />
          <div v-for="data in records" :key="data.resource">
            <p
              v-show="data.records.length > 0"
              class="mb-2 mt-3 text-sm font-medium text-neutral-700 dark:text-neutral-100"
              v-text="data.title"
            />
            <div
              v-for="record in data.records"
              :key="data.resource + '-' + record.id"
              class="flex items-center"
            >
              <IFormCheckbox
                class="grow"
                :id="data.resource + '-' + record.id"
                :value="record.id"
                :disabled="
                  resourceName === data.resource &&
                  primaryRecord &&
                  Number(primaryRecord.id) === Number(record.id) &&
                  !associated
                "
                @change="onChange(record, data.resource, data.is_search)"
                v-model:checked="selected[data.resource]"
              >
                {{ record.display_name }}
              </IFormCheckbox>

              <a
                :href="record.path"
                class="mt-0.5 self-start text-neutral-500 dark:text-neutral-400"
              >
                <Icon icon="Eye" class="ml-2 h-4 w-4" />
              </a>
            </div>
          </div>
        </IOverlay>
      </div>
    </template>
  </IPopover>
</template>
<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import findIndex from 'lodash/findIndex'
import debounce from 'lodash/debounce'
import orderBy from 'lodash/orderBy'
import sortBy from 'lodash/sortBy'
import find from 'lodash/find'
import map from 'lodash/map'
import uniq from 'lodash/uniq'
import isObject from 'lodash/isObject'
import cloneDeep from 'lodash/cloneDeep'
import castArray from 'lodash/castArray'
import { CancelToken } from '~/Core/resources/js/services/HTTP'
import { useI18n } from 'vue-i18n'
import { useLoader } from '~/Core/resources/js/composables/useLoader'

const emit = defineEmits(['update:modelValue', 'change'])

const props = defineProps({
  // The actual v-model for the selected associations
  modelValue: {},

  // Indicates whether the popover is disabled
  disabled: { type: Boolean, default: false },

  // The popover placement
  placement: { type: String, default: 'bottom' },

  // Passed only when associatable is needed and
  // will be taken from the resource store
  resourceName: String,

  // The associateable, the record from the passed resourceName, only provide when resourceName is provided
  // The current record which is associtable e.q. when viewing contact the contact is associateable
  associateable: {
    type: Object,
    default() {
      return {}
    },
  },

  // Provide all of the associated record on EDIT, used to fill the associations in the popover
  // as the associateable may be linked to multiple not somehow related
  // resources via the search function, we need all associated records for update
  associated: Object,

  // Custom selected records from outside not somehow related to the associateables
  customSelectedRecords: Object,

  excludedResources: [String, Array],
})

const { t } = useI18n()
const { setLoading, isLoading } = useLoader()

const minimumAsyncCharacters = 2
const totalAsyncSearchCharacters = ref(0)
const minimumAsyncCharactersRequirement = ref(false)
const limitSearchResults = 5
const searchQuery = ref('')
// The selected associations
const selected = ref({})
// Associations selected from search results
const selectedFromSearch = ref({})
const searchResults = ref({})
const cancelTokens = {}

const availableAssociateables = {
  contacts: {
    title: t('contacts::contact.contacts'),
    resource: 'contacts',
  },
  companies: {
    title: t('contacts::company.companies'),
    resource: 'companies',
  },
  deals: {
    title: t('deals::deal.deals'),
    resource: 'deals',
  },
}

if (props.excludedResources) {
  castArray(props.excludedResources).forEach(resourceName => {
    delete availableAssociateables[resourceName]
  })
}

const totalCharactersLeftToPerformSearch = computed(
  () => minimumAsyncCharacters - totalAsyncSearchCharacters.value
)

const searchInputPlaceholder = computed(() => t('core::app.search_records'))

const hasAssociateble = computed(
  () => Object.keys(props.associateable).length > 0
)

/**
 * The available associations resources
 *
 * They are sorted as the primary associtable is always first
 */
const resources = computed(() =>
  sortBy(Object.keys(availableAssociateables), resourceName => {
    return [resourceName !== props.resourceName, resourceName]
  })
)

const hasSearchResults = computed(() => {
  let result = false
  resources.value.every(resource => {
    result = searchResults.value[resource]
      ? searchResults.value[resource].records.length > 0
      : false
    return result ? false : true
  })

  return result
})

const records = computed(() => {
  if (hasSearchResults.value) {
    return searchResults.value
  }

  let data = {}

  let addRecord = (resourceName, record) => {
    if (
      findIndex(data[resourceName].records, ['id', Number(record.id)]) === -1
    ) {
      data[resourceName].records.push(record)
    }
  }

  resources.value.forEach(resource => {
    data[resource] = Object.assign({}, availableAssociateables[resource], {
      records: [],
    })

    if (hasAssociateble.value) {
      // Push the primary associateable
      if (resource === props.resourceName) {
        addRecord(resource, props.associateable)
      }

      getParsedAssociateablesFromInitialData(resource).forEach(record =>
        addRecord(resource, record)
      )
    }

    // Push any associations which are not directly related to the intial associateable
    if (props.associated && props.associated.hasOwnProperty(resource)) {
      props.associated[resource].forEach(record => addRecord(resource, record))
    }

    // Push any custom associations passed
    if (props.customSelectedRecords && props.customSelectedRecords[resource]) {
      props.customSelectedRecords[resource].forEach(record =>
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

  return data
})

const primaryRecord = computed(() => {
  if (!hasAssociateble.value) {
    return null
  }

  let result = null
  resources.value.every(resource => {
    // The current resource is not the actual primary resource
    if (resource != props.resourceName) {
      // Continue every
      return true
    }

    result = find(records.value[resource].records, [
      'id',
      Number(props.associateable.id),
    ])

    return result ? false : true
  })

  return result
})

const isSearching = computed(() => searchQuery.value != '')

const associationsText = computed(() => {
  let totalSelected = 0
  resources.value.forEach(resource => {
    totalSelected += selected.value[resource]
      ? selected.value[resource].length
      : 0
  })

  if (totalSelected === 0) {
    return t('core::app.no_associations')
  }

  return t('core::app.associated_with_total_records', {
    total: totalSelected,
  })
})

/**
 * Get the parsed associtables from the initial data
 * which are intended to be shown as records
 *
 * @param  {String} resource
 *
 * @return {Array}
 */
function getParsedAssociateablesFromInitialData(resource) {
  return orderBy(
    (props.associateable[resource] || []).slice(0, 3),
    'created_at',
    'desc'
  )
}

/**
 * Create search requests for the Promise
 *
 * @param  {String} q
 *
 * @return {Array}
 */
function createResolveableRequests(q) {
  // The order of the promises must be the same
  // like in the order of the availableAssociateables keys data variable
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
      searchResults.value[resource] = Object.assign(
        {},
        availableAssociateables[resource],
        {
          records: map(values[key].data, record => {
            record.from_search = true
            return record
          }),
          is_search: true,
        }
      )
    })

    setLoading(false)
  })
}, 650)

/**
 * Reset selected
 *
 * @return {Void}
 */
function setSelectedRecords() {
  let allSelected = {}
  resources.value.forEach(resource => {
    let resourceSelected = []

    if (props.modelValue && props.modelValue[resource]) {
      resourceSelected = cloneDeep(
        isObject(props.modelValue[resource][0])
          ? map(props.modelValue[resource], record => record.id)
          : props.modelValue[resource]
      )
    }

    if (props.customSelectedRecords && props.customSelectedRecords[resource]) {
      resourceSelected = cloneDeep(
        isObject(props.customSelectedRecords[resource][0])
          ? map(props.customSelectedRecords[resource], record => record.id)
          : props.customSelectedRecords[resource]
      )
    }

    // Is primary resource, has associateable to be handled as
    // primary and is not create view because hasAssociated prop passsed
    if (
      resource === props.resourceName &&
      hasAssociateble.value &&
      !props.associated
    ) {
      resourceSelected.push(props.associateable.id)
    }

    // Set the selected value via the associated
    if (props.associated && props.associated.hasOwnProperty(resource)) {
      resourceSelected = resourceSelected.concat(
        props.associated[resource].map(record => record.id)
      )
    }

    allSelected[resource] = uniq(resourceSelected)
  })

  selected.value = allSelected
}

watch(
  selected,
  newVal => {
    emit('update:modelValue', newVal)
  },
  { deep: true }
)

watch(
  () => props.customSelectedRecords,
  () => {
    setSelectedRecords()
  },
  { deep: true }
)

// Update the selected values when the associated associations are changed
watch(
  () => props.associated,
  () => {
    setSelectedRecords()
  }
)

onMounted(setSelectedRecords)
</script>
