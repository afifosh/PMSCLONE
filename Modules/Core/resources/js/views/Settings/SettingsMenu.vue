<template>
  <IVerticalNavigation class="mb-4 sm:sticky sm:top-10 lg:mb-0">
    <IVerticalNavigationItem
      :title="$t('core::settings.general')"
      icon="Cog"
      :link-class="{
        'bg-neutral-50 dark:bg-neutral-600 text-primary-600 dark:text-primary-400':
          $route.name === 'settings-general',
      }"
      :icon-class="{
        'text-primary-600 dark:text-primary-300':
          $route.name === 'settings-general',
      }"
      :to="{ name: 'settings-general' }"
    />
    <IVerticalNavigationItem
      v-for="item in items"
      :key="item.id"
      :title="item.title"
      :to="item.route"
      :href="$router.resolve({ to: item.route }).href"
      :icon="item.icon"
    >
      <template v-if="item.children.length">
        <IVerticalNavigationItem
          v-for="child in item.children"
          :key="item.id + '-' + child.id"
          :to="child.route"
          :href="$router.resolve({ to: child.route }).href"
          :title="child.title"
          :icon="child.icon"
        />
      </template>
    </IVerticalNavigationItem>
  </IVerticalNavigation>
</template>
<script setup>
const items = Innoclapps.config('settings.menu')
</script>
