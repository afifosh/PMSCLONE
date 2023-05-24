<template>
  <span
    v-if="row[column.attribute]"
    class="inline-flex max-w-[170px] items-center truncate"
  >
    <IAvatar
      :src="row[column.attribute].avatar_url"
      :alt="row[column.attribute].name"
      size="xs"
    />

    <span class="ml-1" v-text="row[column.attribute].name"></span>
  </span>
</template>
<script setup>
import propsDefinition from './props'
defineProps(propsDefinition)
</script>
