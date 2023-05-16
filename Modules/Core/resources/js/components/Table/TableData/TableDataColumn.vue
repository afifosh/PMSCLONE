<template>
  <span v-if="!column.asHtml" v-text="formatted" />
  <span v-else v-html="formatted" />
</template>
<script setup>
import propsDefinition from './props'
defineProps(propsDefinition)
</script>
