<template>
  <div class="flex items-center" :class="type">
    <div class="w-14">
      <IFormLabel :label="label" :for="type" />
    </div>
    <div class="recipient grow">
      <ICustomSelect
        :input-id="type"
        :options="options"
        ref="selectRef"
        :filterable="false"
        :clearable="false"
        :taggable="true"
        :multiple="true"
        label="address"
        :filter-by="selectFilterBy"
        :option-key-provider="
          option =>
            String(
              option.address + option.id + option.resourceName + option.name
            )
        "
        :create-option-provider="
          option => ({
            name: '',
            address: option,
          })
        "
        :option-comparator-provider="
          (a, b, defaultComparator) => {
            // For invalid addresses handler
            if (typeof a == 'string' && typeof b === 'object') {
              return a === b.address
            } else if (typeof b == 'string' && typeof a === 'object') {
              return b === a.address
            }

            return defaultComparator(a) === defaultComparator(b)
          }
        "
        :display-new-options-last="true"
        :placeholder="$t('mailclient::inbox.search_recipients')"
        @option:selected="handleRecipientSelected"
        @search="asyncSearch"
        v-model="form[type]"
      >
        <!-- Searched emails -->
        <template #option="option">
          <span class="flex justify-between">
            <span class="mr-2 truncate">
              {{ option.name }} {{ option.address }}
            </span>
            <span>{{ option.resourceSingularName }}</span>
          </span>
        </template>
        <!-- Selected -->
        <template
          #selected-option-container="{ option, disabled, deselect, multiple }"
        >
          <span
            :class="[
              'mr-2 inline-flex rounded-md bg-neutral-100 px-2 dark:bg-neutral-500 dark:text-white',
              {
                'border border-danger-500': !validateAddress(option.address),
              },
            ]"
            v-bind:key="option.index"
          >
            <span v-if="!option.name">
              {{ option.address }}
            </span>
            <span v-else>
              <span v-i-tooltip="option.address">{{ option.name }}</span>
            </span>
            <button
              v-if="multiple"
              type="button"
              :disabled="disabled"
              @click.prevent.stop="removeRecipient(deselect, option)"
              class="ml-1 text-neutral-400 hover:text-neutral-600 dark:text-neutral-200 dark:hover:text-neutral-400"
              title="Remove recipient"
              aria-label="Remove recipient"
            >
              <Icon icon="X" class="h-4 w-4" />
            </button>
          </span>
        </template>
      </ICustomSelect>
      <IFormError v-text="form.getError(type)" />
    </div>
    <slot name="after"></slot>
  </div>
</template>
<script setup>
import { ref, shallowRef, nextTick } from 'vue'
import debounce from 'lodash/debounce'
import validator from 'email-validator'
import { CancelToken } from '~/Core/resources/js/services/HTTP'
import { useResource } from '~/Core/resources/js/composables/useResource'

const emit = defineEmits(['recipient-removed', 'recipient-selected'])

const props = defineProps({
  label: String,
  type: { type: String, required: true },
  form: { required: true },
})

const selectRef = ref(null)
const options = shallowRef([])

const { getSingularName } = useResource()

let cancelToken = null

// Allow non matching addresse to be shown
// based on the searched name (display_name)
function selectFilterBy(option, label, search) {
  return (
    (label || '').toLowerCase().indexOf(search.toLowerCase()) > -1 ||
    (option.name || '').toLowerCase().indexOf(search.toLowerCase()) > -1
  )
}

function handleRecipientSelected(records) {
  emit('recipient-selected', records)
}

function removeRecipient(deselect, option) {
  deselect(option)
  emit('recipient-removed', option)
}

function focus() {
  selectRef.value.focus()
}

/**
 * Perform search
 *
 * @param  {[type]}   q
 * @param  {Function} loading
 * @return {Null|String}
 */
const asyncSearch = debounce(function (q, loading) {
  if (!q) {
    return
  }

  if (cancelToken) {
    cancelToken()
  }

  loading(true)

  Innoclapps.request()
    .get('/search/email-address', getRequestQueryString(q))
    .then(({ data }) => {
      if (data) {
        let opts = []
        data.forEach(result => opts.push(...result.data))
        nextTick(() => {
          options.value = opts.map(opt => {
            opt.resourceSingularName = getSingularName(opt.resourceName)
            return opt
          })
        })
      }
    })
    .finally(() => loading(false))
}, 400)

function getRequestQueryString(q) {
  return {
    params: {
      q: q,
    },
    cancelToken: new CancelToken(token => (cancelToken = token)),
  }
}

function validateAddress(address) {
  return validator.validate(address)
}

defineExpose({ focus })
</script>
<style scoped>
::v-deep(.cs__search::-webkit-search-cancel-button) {
  display: none !important;
}

::v-deep(.cs__search::-webkit-search-decoration),
::v-deep(.cs__search::-webkit-search-results-button),
::v-deep(.cs__search::-webkit-search-results-decoration),
::v-deep(.cs__search::-ms-clear) {
  display: none !important;
}

::v-deep(.cs__search),
::v-deep(.cs__search:focus) {
  appearance: none !important;
}
</style>
