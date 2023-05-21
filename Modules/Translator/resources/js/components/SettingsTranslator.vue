<template>
  <ICard :header="$t('translator::translator.translator')" no-body>
    <template #actions>
      <div class="flex items-center space-x-3">
        <FormDropdownSelect
          :items="locales"
          v-model="locale"
          @change="getTranslations"
          placement="bottom-end"
        />
        <IButton
          v-i-modal="'new-locale'"
          icon="plus"
          size="sm"
          :text="$t('translator::translator.new_locale')"
        />
      </div>
    </template>

    <ul class="divide-y divide-neutral-200 dark:divide-neutral-700">
      <li
        v-for="(groupTranslations, group) in translations.current.groups"
        :key="group"
        v-show="!activeGroup || activeGroup === group"
      >
        <div class="hover:bg-neutral-100 dark:hover:bg-neutral-700/60">
          <div class="flex items-center">
            <div class="grow">
              <a
                href="#"
                @click.prevent="toggleGroup(group)"
                class="block px-7 py-2 font-medium text-neutral-600 focus:outline-none dark:text-neutral-200"
                v-text="strTitle(group.replace('_', ' '))"
              />
            </div>
            <div class="ml-2 py-2 pr-7">
              <IButton
                variant="white"
                size="sm"
                @click="toggleGroup(group)"
                icon="ChevronDown"
              />
            </div>
          </div>
        </div>
        <form
          @submit.prevent="saveGroup(group)"
          novalidate="true"
          v-show="activeGroup === group"
        >
          <ITable :shadow="false">
            <thead>
              <tr>
                <th class="text-left" width="15%">Key</th>
                <th class="text-left" width="30%">Source</th>
                <th class="text-left" width="55%">{{ locale }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(translation, key) in groupTranslations" :key="key">
                <td width="15%" v-text="key" />
                <td
                  width="30%"
                  v-text="translations.source.groups[group][key]"
                />
                <td width="55%">
                  <!-- When disabled, means that the key is array and empty
                    because when the key is empty will the value will be empty array instead of key e.q. lang.key -->
                  <IFormTextarea
                    v-if="
                      !Array.isArray(translations.current.groups[group][key])
                    "
                    v-model="translations.current.groups[group][key]"
                    rows="3"
                  />
                </td>
              </tr>
            </tbody>
          </ITable>
          <div
            class="-mt-px flex items-center justify-end space-x-3 bg-neutral-50 px-6 py-3 dark:bg-neutral-700"
          >
            <IButton
              size="sm"
              @click="deactivateGroup(group, true)"
              :disabled="groupIsBeingSaved"
              :text="$t('core::app.cancel')"
              variant="white"
            />
            <IButton
              type="submit"
              size="sm"
              :text="$t('core::app.save')"
              :disabled="groupIsBeingSaved"
              :loading="groupIsBeingSaved"
            />
          </div>
        </form>
      </li>
    </ul>

    <template
      v-for="(namespaceGroupTranslations, namespace) in translations.current
        .namespaces"
      :key="namespace"
    >
      <p
        class="bg-neutral-100 px-7 py-3 font-semibold text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300"
        v-show="!activeGroup"
      >
        {{ strTitle(namespace) }}
      </p>

      <ul class="divide-y divide-neutral-200 dark:divide-neutral-700">
        <li
          v-for="(groupTranslations, group) in translations.current.namespaces[
            namespace
          ]"
          :key="group"
          v-show="
            !activeGroup ||
            (activeGroup === group && activeNamespace === namespace)
          "
        >
          <div class="hover:bg-neutral-100 dark:hover:bg-neutral-700/60">
            <div class="flex items-center">
              <div class="grow">
                <a
                  href="#"
                  @click.prevent="toggleGroup(group, namespace)"
                  class="block px-7 py-2 font-medium text-neutral-600 focus:outline-none dark:text-neutral-200"
                  v-text="strTitle(group.replace('_', ' '))"
                />
              </div>
              <div class="ml-2 py-2 pr-7">
                <IButton
                  variant="white"
                  size="sm"
                  @click="toggleGroup(group, namespace)"
                  icon="ChevronDown"
                />
              </div>
            </div>
          </div>
          <form
            @submit.prevent="saveGroup(group, namespace)"
            novalidate="true"
            v-show="activeGroup === group && activeNamespace === namespace"
          >
            <ITable :shadow="false">
              <thead>
                <tr>
                  <th class="text-left" width="15%">Key</th>
                  <th class="text-left" width="30%">Source</th>
                  <th class="text-left" width="55%">{{ locale }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(translation, key) in groupTranslations" :key="key">
                  <td width="15%" v-text="key" />
                  <td
                    width="30%"
                    v-text="
                      translations.source.namespaces[namespace][group][key]
                    "
                  />
                  <td width="55%">
                    <!-- When disabled, means that the key is array and empty
                    because when the key is empty will the value will be empty array instead of key e.q. lang.key -->
                    <IFormTextarea
                      v-if="
                        !Array.isArray(
                          translations.current.namespaces[namespace][group][key]
                        )
                      "
                      v-model="
                        translations.current.namespaces[namespace][group][key]
                      "
                      rows="3"
                    />
                  </td>
                </tr>
              </tbody>
            </ITable>
            <div
              class="-mt-px flex items-center justify-end space-x-3 bg-neutral-50 px-6 py-3 dark:bg-neutral-700"
            >
              <IButton
                size="sm"
                @click="deactivateGroup(group, true)"
                :disabled="groupIsBeingSaved"
                :text="$t('core::app.cancel')"
                variant="white"
              />
              <IButton
                type="submit"
                size="sm"
                :text="$t('core::app.save')"
                :disabled="groupIsBeingSaved"
                :loading="groupIsBeingSaved"
              />
            </div>
          </form>
        </li>
      </ul>
    </template>
    <IModal
      size="sm"
      id="new-locale"
      form
      @submit="createLocale"
      @keydown="localeForm.onKeydown($event)"
      @shown="() => $refs.inputNameRef.focus()"
      :ok-title="$t('core::app.create')"
      :cancel-title="$t('core::app.cancel')"
      :title="$t('translator::translator.create_new_locale')"
    >
      <IFormGroup
        label-for="localeName"
        :label="$t('translator::translator.locale_name')"
        required
      >
        <IFormInput
          id="localeName"
          v-model="localeForm.name"
          ref="inputNameRef"
        />
        <IFormError v-text="localeForm.getError('name')" />
      </IFormGroup>
    </IModal>
  </ICard>
</template>
<script setup>
import { ref } from 'vue'
import { useStore } from 'vuex'
import { strTitle } from '@/utils'
import isEqual from 'lodash/isEqual'
import cloneDeep from 'lodash/cloneDeep'
import { onBeforeRouteLeave, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useForm } from '~/Core/resources/js/composables/useForm'
import { useApp } from '~/Core/resources/js/composables/useApp'

const { t } = useI18n()
const store = useStore()
const route = useRoute()
const { locales } = useApp()

const { form: localeForm } = useForm({ name: null })

let originalTranslations = {}

// Active locale
const locale = ref(route.query.locale || Innoclapps.config('locale'))

// Active locale groups translation
const translations = ref({
  source: {
    groups: {},
    namespaces: {},
  },
  current: {
    groups: {},
    namespaces: {},
  },
})

const activeGroup = ref(null)
const activeNamespace = ref(null)
const groupIsBeingSaved = ref(false)

onBeforeRouteLeave((to, from, next) => {
  const unsaved = getUnsavedTranslationGroups()

  if (unsaved.length > 0) {
    Innoclapps.dialog()
      .confirm({
        message: t('translator::translator.changes_not_saved'),
        title: 'Are you sure you want to leave this page?',
        confirmText: t('core::app.discard_changes'),
      })
      .then(() => next())
      .catch(() => next(false))
  } else {
    next()
  }
})

function getUnsavedTranslationGroups() {
  let groups = []
  let originalGroups = {}
  let currentGroups = {}

  if (activeNamespace.value) {
    groups = Object.keys(originalTranslations.namespaces[activeNamespace.value])
    originalGroups = originalTranslations.namespaces[activeNamespace.value]
    currentGroups = translations.value.current.namespaces[activeNamespace.value]
  } else {
    groups = Object.keys(originalTranslations.groups)
    originalGroups = originalTranslations.groups
    currentGroups = translations.value.current.groups
  }

  let unsaved = []

  groups.forEach(group => {
    if (!isEqual(originalGroups[group], currentGroups[group])) {
      unsaved.push(group)
    }
  })

  return unsaved
}

function saveGroup(group, namespace = null) {
  groupIsBeingSaved.value = true

  let payload = null

  if (namespace) {
    payload = translations.value.current.namespaces[namespace][group]
  } else {
    payload = translations.value.current.groups[group]
  }

  Innoclapps.request()
    .put(`/translation/${locale.value}/${group}`, {
      translations: payload,
      namespace: namespace,
    })
    .then(() => {
      window.location.href = `${store.state.url}/settings/translator?locale=${locale.value}`
    })
    .finally(() => setTimeout(() => (groupIsBeingSaved.value = false), 1000))
}

function getTranslations(locale) {
  Innoclapps.request()
    .get(`/translation/${locale}`)
    .then(({ data }) => {
      originalTranslations = cloneDeep(data.current)
      translations.value = data
    })
}

function createLocale() {
  localeForm.post('/translation').then(data => {
    locales.value.push(data.locale)
    locale.value = data.locale
    getTranslations(data.locale)
    Innoclapps.modal().hide('new-locale')
  })
}

function toggleGroup(group, namespace = null) {
  if (activeGroup.value) {
    deactivateGroup(group)
    return
  }

  activateGroup(group, namespace)
}

function activateGroup(group, namespace = null) {
  activeGroup.value = group
  activeNamespace.value = namespace
}

function deactivateGroup(group, skipConfirmation = false) {
  const unsaved = getUnsavedTranslationGroups()
  let namespace = activeNamespace.value
  const groupIsModified = unsaved.indexOf(group) > -1

  if (skipConfirmation || !groupIsModified) {
    activeGroup.value = null
    activeNamespace.value = null
    // Replace only when group group modified
    if (groupIsModified) {
      replaceOriginalTranslations(group, namespace)
    }
    return
  }

  Innoclapps.dialog()
    .confirm({
      message: t('translator::translator.changes_not_saved'),
      title: 'The group has unsaved translations!',
      confirmText: t('core::app.discard_changes'),
    })
    .then(() => {
      activeNamespace.value = null
      activeGroup.value = null
      replaceOriginalTranslations(group, namespace)
    })
}

function replaceOriginalTranslations(group, namespace = null) {
  if (namespace) {
    translations.value.current.namespaces[namespace][group] = cloneDeep(
      originalTranslations.namespaces[namespace][group]
    )
    return
  }

  translations.value.current.groups[group] = cloneDeep(
    originalTranslations.groups[group]
  )
}

getTranslations(locale.value)
</script>
