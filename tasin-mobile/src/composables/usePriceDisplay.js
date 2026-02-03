/**
 * Форматирование цены (число) и отображение с валютой (TMT, USD и т.д.)
 */

/** Число в строку с разделителями (ru-RU) */
export function formatPriceNumber(value) {
  const n = Number(value)
  if (Number.isNaN(n)) return '0'
  return n.toLocaleString('ru-RU')
}

/** Цена + код валюты: "3,5 USD" или "89 990 TMT" */
export function formatPriceWithCurrency(price, currency) {
  const code = currency || 'TMT'
  const num = Number(price)
  if (Number.isNaN(num)) return code
  return `${formatPriceNumber(num)} ${code}`
}
