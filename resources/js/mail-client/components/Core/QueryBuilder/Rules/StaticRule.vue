<template>
  <IBadge variant="info" :text="$t('core::filters.static_rule_indicator')" />
</template>
<script>
export default {
  inheritAttrs: false,
}
</script>
<script setup>
import propsDefinition from './props'

const props = defineProps(propsDefinition)
</script>
