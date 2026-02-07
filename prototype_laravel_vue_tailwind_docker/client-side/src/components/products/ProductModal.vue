<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50"
    @click.self="$emit('update:modelValue', false)"
  >
    <div class="bg-white dark:bg-deepblue rounded-lg shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-midnight">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
          {{ editing ? 'Редактировать' : 'Новый' }} товар
        </h2>
        <button @click="$emit('update:modelValue', false)" class="p-2 hover:bg-gray-100 dark:hover:bg-fogactive rounded-md transition-colors">
          <AppIcon name="X" size="18" class="text-gray-500 dark:text-fog" />
        </button>
      </div>

      <form @submit.prevent="$emit('save')" class="p-5 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Категория *</label>
          <select
            :value="form.category_id"
            @change="$emit('update:form', { ...form, category_id: +$event.target.value })"
            required
            class="w-full px-3 py-2 border border-gray-300 dark:border-midnight rounded-md bg-white dark:bg-deepblue text-gray-900 dark:text-white"
          >
            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Название *</label>
          <input
            :value="form.name"
            @input="$emit('update:form', { ...form, name: $event.target.value })"
            type="text"
            required
            placeholder="Например: iPhone 16"
            class="w-full px-3 py-2 border border-gray-300 dark:border-midnight rounded-md bg-white dark:bg-deepblue text-gray-900 dark:text-white"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Описание</label>
          <textarea
            :value="form.description"
            @input="$emit('update:form', { ...form, description: $event.target.value })"
            rows="2"
            placeholder="Описание товара"
            class="w-full px-3 py-2 border border-gray-300 dark:border-midnight rounded-md bg-white dark:bg-deepblue text-gray-900 dark:text-white"
          />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Цена *</label>
            <input
              :value="form.price"
              @input="$emit('update:form', { ...form, price: +$event.target.value })"
              type="number"
              step="0.01"
              min="0"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-midnight rounded-md bg-white dark:bg-deepblue text-gray-900 dark:text-white"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Валюта *</label>
            <input
              :value="form.currency"
              @input="$emit('update:form', { ...form, currency: $event.target.value })"
              type="text"
              required
              placeholder="TMT"
              class="w-full px-3 py-2 border border-gray-300 dark:border-midnight rounded-md bg-white dark:bg-deepblue text-gray-900 dark:text-white"
            />
          </div>
        </div>

        <div>
          <MultiImagePicker
            :model-value="form.images"
            @update:model-value="$emit('update:form', { ...form, images: $event })"
            label="Изображения товара"
            hint="PNG, JPG до 2MB. Кликните + для добавления."
            :max-count="10"
          />
        </div>

        <div class="flex items-center justify-between pt-2">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0.5">Новинка</label>
          </div>
          <button
            type="button"
            @click="$emit('update:form', { ...form, is_new: !form.is_new })"
            :class="form.is_new ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-midnight'"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
          >
            <span
              :class="form.is_new ? 'translate-x-6' : 'translate-x-1'"
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
            />
          </button>
        </div>

        <div class="flex gap-3 pt-5 border-t border-gray-100 dark:border-midnight">
          <button
            type="button"
            @click="$emit('update:modelValue', false)"
            class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-midnight text-gray-700 dark:text-gray-300 font-medium rounded-md hover:bg-gray-50 dark:hover:bg-fogactive/30 transition-colors"
          >
            Отмена
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="flex-1 px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="flex items-center justify-center gap-2">
              <LoaderSpinner size="sm" />
              Сохранение...
            </span>
            <span v-else>{{ editing ? 'Сохранить' : 'Создать' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import AppIcon from '@/components/icons/AppIcon.vue'
import LoaderSpinner from '@/components/loaders/LoaderSpinner.vue'
import MultiImagePicker from './MultiImagePicker.vue'

/** Модальное окно создания/редактирования товара. */
export default {
  name: 'ProductModal',
  components: { AppIcon, LoaderSpinner, MultiImagePicker },
  props: {
    modelValue: { type: Boolean, default: false },
    form: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
    editing: { type: Boolean, default: false }
  },
  emits: ['update:modelValue', 'update:form', 'save']
}
</script>
