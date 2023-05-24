<template>
  <div class="mt-1 flex space-x-2">
    <IFormRadio
      v-for="option in options"
      :key="option[rule.valueKey]"
      v-model="value"
      :value="option[rule.valueKey]"
      :disabled="readOnly"
      :id="rule.id + '_' + option[rule.valueKey] + '_' + index"
      :name="rule.id + '_' + option[rule.valueKey]"
      :label="option[rule.labelKey]"
    />
  </div>
</template>
<script>
export default {
  inheritAttrs: false,
}
</script>
<script setup>
import { toRef, computed } from 'vue'
import { useType } from './useType'
import propsDefinition from './props'
import { useElementOptions } from '../../../../composables/Core/useElementOptions'

const props = defineProps(propsDefinition)

const { options, setOptions, getOptions } = useElementOptions()

const value = computed({
  get() {
    return props.query.value
  },
  set(value) {
    updateValue(value)
  },
})

const { updateValue } = useType(
  toRef(props, 'query'),
  toRef(props, 'operator'),
  props.isNullable
)

getOptions(props.rule).then(setOptions)
</script>
