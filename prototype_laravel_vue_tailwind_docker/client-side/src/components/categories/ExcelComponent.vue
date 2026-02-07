<template>
  <div class="flex flex-wrap items-center gap-2">
    <input
      ref="excelFileInput"
      type="file"
      accept=".xlsx,.xls"
      class="hidden"
      @change="onExcelFileSelect"
    />
    <button
      @click="$refs.excelFileInput?.click()"
      :disabled="importing"
      class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-deepblue border border-gray-300 dark:border-midnight text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-fogactive/30 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <LoaderSpinner v-if="importing" size="sm" />
      <AppIcon v-else name="Upload" size="18" />
      {{ importing ? 'Импорт...' : 'Загрузить Excel' }}
    </button>
    <button
      @click="downloadExcel"
      :disabled="categories.length === 0 || downloading"
      class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-deepblue border border-gray-300 dark:border-midnight text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-fogactive/30 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <LoaderSpinner v-if="downloading" size="sm" />
      <AppIcon v-else name="Download" size="18" />
      {{ downloading ? 'Скачивание...' : 'Скачать Excel' }}
    </button>
  </div>
</template>

<script>
import * as XLSX from 'xlsx'
import api from '@/api/http'
import store from '@/store'
import AppIcon from '@/components/icons/AppIcon.vue'
import LoaderSpinner from '@/components/loaders/LoaderSpinner.vue'

/** Компонент импорта/экспорта категорий в Excel. */
export default {
  name: 'ExcelComponent',
  components: { AppIcon, LoaderSpinner },
  props: {
    categories: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
    /** Функция загрузки категорий (должна возвращать Promise). */
    loadCategories: { type: Function, required: true }
  },
  emits: ['imported'],
  data() {
    return { importing: false, downloading: false }
  },
  methods: {
    getCategoryProductsCount(categoryId) {
      return this.products.filter((p) => p.category_id === categoryId).length
    },
    onExcelFileSelect(event) {
      const file = event.target.files?.[0]
      if (!file) return
      event.target.value = ''
      this.importFromExcel(file)
    },
    /** Импорт: ID есть и категория существует → update, иначе → create. */
    async importFromExcel(file) {
      this.importing = true
      let created = 0
      let updated = 0
      const errors = []
      try {
        const categories = await this.loadCategories()
        const data = await this.parseExcelFile(file)
        const existingIds = new Set(categories.map((c) => String(c.id)))
        for (let i = 0; i < data.length; i++) {
          const row = data[i]
          const id = row.id != null && row.id !== '' ? Number(row.id) : null
          const name = String(row.name ?? row['Название'] ?? '').trim()
          if (!name) continue
          const order = parseInt(row.order ?? row['Порядок'] ?? i + 1, 10) || i + 1
          const showPriceRaw = row.show_price ?? row['Показывать цену'] ?? ''
          const showPrice = /^(да|yes|1|true)$/i.test(String(showPriceRaw).trim())
          const imageIcon = String(row.image_icon ?? row['Иконка'] ?? '').trim() || null
          const formData = new FormData()
          formData.append('name', name)
          formData.append('order', order)
          formData.append('show_price', showPrice)
          if (imageIcon) formData.append('image_icon', imageIcon)
          try {
            if (id && existingIds.has(String(id))) {
              try {
                formData.append('_method', 'PUT')
                await api.post(`/categories/${id}`, formData, {
                  headers: { 'Content-Type': 'multipart/form-data' }
                })
                updated++
              } catch (putErr) {
                if (putErr.response?.status === 404) {
                  await api.post('/categories', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                  })
                  created++
                } else throw putErr
              }
            } else {
              await api.post('/categories', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
              })
              created++
            }
          } catch (e) {
            errors.push(`Строка ${i + 2}: ${e.response?.data?.message || e.message || 'Ошибка'}`)
          }
        }
        await this.loadCategories()
        this.$emit('imported')
        const msg = `Импорт: создано ${created}, обновлено ${updated}${errors.length ? `. Ошибки: ${errors.join('; ')}` : ''}`
        store.dispatch('notifications/notify', {
          message: msg,
          type: errors.length > 0 ? 'error' : 'success'
        })
      } catch (e) {
        console.error('Import error:', e)
        store.dispatch('notifications/notify', {
          message: 'Ошибка чтения файла: ' + (e.message || 'неизвестная ошибка'),
          type: 'error'
        })
      } finally {
        this.importing = false
      }
    },
    /** Парсит Excel и возвращает массив объектов по колонкам. */
    parseExcelFile(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader()
        reader.onload = (e) => {
          try {
            const wb = XLSX.read(e.target.result, { type: 'array' })
            const ws = wb.Sheets[wb.SheetNames[0]]
            const json = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '' })
            if (json.length < 2) {
              resolve([])
              return
            }
            const headers = json[0].map((h) => String(h ?? '').trim())
            const rows = []
            const keyMap = { ID: 'id', Название: 'name', Порядок: 'order', 'Показывать цену': 'show_price', Иконка: 'image_icon' }
            for (let i = 1; i < json.length; i++) {
              const arr = json[i]
              const row = {}
              headers.forEach((h, j) => {
                row[h] = arr[j]
                if (keyMap[h]) row[keyMap[h]] = arr[j]
              })
              rows.push(row)
            }
            resolve(rows)
          } catch (err) {
            reject(err)
          }
        }
        reader.onerror = () => reject(reader.error)
        reader.readAsArrayBuffer(file)
      })
    },
    /** Экспорт категорий в Excel. Показывает loader во время генерации файла. */
    async downloadExcel() {
      if (this.downloading) return
      this.downloading = true
      await this.$nextTick()
      await new Promise((r) => setTimeout(r, 50))
      try {
        const headers = ['ID', 'Название', 'Порядок', 'Показывать цену', 'Кол-во продуктов', 'Иконка']
        const rows = this.categories.map((c) => [
          c.id,
          c.name,
          c.order ?? '',
          c.show_price ? 'Да' : 'Нет',
          this.getCategoryProductsCount(c.id),
          c.image_icon ?? ''
        ])
        const ws = XLSX.utils.aoa_to_sheet([headers, ...rows])
        const wb = XLSX.utils.book_new()
        XLSX.utils.book_append_sheet(wb, ws, 'Категории')
        XLSX.writeFile(wb, `categories_${new Date().toISOString().slice(0, 10)}.xlsx`)
      } finally {
        this.downloading = false
      }
    }
  }
}
</script>
