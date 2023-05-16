<template>
  <div class="mt-1 flex w-full items-center overflow-x-auto py-1">
    <a
      v-show="value && !isDisabled"
      href="#"
      @click.prevent="value = null"
      class="link mr-3 border-r-2 border-neutral-200 pr-3 dark:border-neutral-600"
      v-t="'core::app.all'"
    />
    <IIconPicker
      class="min-w-max"
      :icons="typesForIconPicker"
      value-field="id"
      v-i-tooltip="
        typeRuleIsApplied
          ? $t('activities::activity.filters.activity_type_disabled')
          : ''
      "
      :disabled="isDisabled"
      v-model="value"
    />
  </div>
</template>
<script setup>
import { ref, computed, watch } from 'vue'
import { useActivityTypes } from '../composables/useActivityTypes'
import { useQueryBuilder } from '~/Core/resources/js/components/QueryBuilder/useQueryBuilder'

const emit = defineEmits(['update:modelValue'])
const props = defineProps(['modelValue'])

const { hasBuilderRules, rulesAreValid, findRule } =
  useQueryBuilder('activities')

const { typesForIconPicker } = useActivityTypes()
const value = ref(props.modelValue)

const typeRuleIsApplied = computed(() => Boolean(findRule('activity_type_id')))

const isDisabled = computed(
  () => typeRuleIsApplied.value || !rulesAreValid.value
)

watch(value, newVal => {
  emit('update:modelValue', newVal)
})

// Remove selected type when the builder has rules and they are valid
// to prevent errors in the filters
watch(hasBuilderRules, newVal => {
  if (newVal && rulesAreValid.value) {
    value.value = undefined
  }
})

// The same for when rules become valid, when valid and has builder rules
// remove selected type
watch(rulesAreValid, newVal => {
  if (hasBuilderRules.value && newVal) {
    value.value = undefined
  }
})
</script>
