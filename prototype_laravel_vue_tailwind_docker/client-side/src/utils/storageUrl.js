/**
 * Базовый URL для статики Laravel (storage).
 * Выводится из VITE_API_BASE_URL (убираем /api).
 */
const storageBase =
  (import.meta.env.VITE_STORAGE_URL || '').replace(/\/$/, '') ||
  (import.meta.env.VITE_API_BASE_URL || '').replace(/\/api\/?$/, '')

/**
 * Возвращает полный URL изображения категории.
 * @param {string} filename - имя файла
 * @returns {string}
 */
export function categoryImageUrl(filename) {
  if (!filename) return ''
  return `${storageBase}/storage/categories/${filename}`
}

/** URL изображения товара. */
export function productImageUrl(filename) {
  if (!filename) return ''
  return `${storageBase}/storage/products/${filename}`
}
