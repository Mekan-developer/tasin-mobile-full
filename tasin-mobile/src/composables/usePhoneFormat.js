/**
 * Формат телефона Туркменистана: +993 и 8 цифр, отображение +993 XX XXXXXX
 */

const PREFIX = '+993'

/** Из строки извлечь только цифры после +993 (максимум 8 цифр номера) */
function digitsOnly(str) {
  const s = String(str || '').replace(/\D/g, '')
  if (s.startsWith('993')) return s.slice(3, 11)
  return s.slice(0, 8)
}

/** Формат для отображения в поле: +993 XX XXXXXX */
export function formatPhoneDisplay(phone) {
  const d = digitsOnly(phone)
  if (!d.length) return ''
  if (d.length <= 2) return `${PREFIX} ${d}`
  return `${PREFIX} ${d.slice(0, 2)} ${d.slice(2, 8)}`
}

/** Нормализовать для сохранения: +993 и 8 цифр */
export function normalizePhone(phone) {
  const d = digitsOnly(phone)
  return d.length === 8 ? `${PREFIX}${d}` : ''
}

/** Валидный номер = ровно 8 цифр после +993 */
export function isPhoneValid(phone) {
  return digitsOnly(phone).length === 8
}
