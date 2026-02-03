import scrollbar from 'tailwind-scrollbar'

/** @type {import('tailwindcss').Config} */
export default {
  theme: {
    extend: {
      colors: {
        // === ОСНОВНАЯ ЦВЕТОВАЯ ПАЛИТРА ===
        midnight: "#00020e",        // Основной темный фон, заголовки
        deepblue: "#181c36",        // Фон сайдбара, темные элементы
        fog: "#cfcfd188",           // Неактивные элементы, hover состояния
        fogactive: "#475981af",     // Активные элементы сайдбара
        activeColor: "#001f3f",     // Активные кнопки и ссылки

        // === ЗАРЕЗЕРВИРОВАННЫЕ ЦВЕТА (для будущего использования) ===
        // midnight: "#34495E",     // Альтернативный midnight
        // deepblue: "#2C3E50",     // Альтернативный deepblue
        // fog: "#3498DB",          // Альтернативный fog
        // fogactive: "#475981af",  // Остается тот же
      },
      backgroundImage: {
        // === ГРАДИЕНТЫ ===
        'my-gradient': 'linear-gradient(to bottom right, #00020ecc, #1e3a8a, #001f3f)',
        'my-gradient-reverse': 'linear-gradient(to bottom right, #001f3f, #1e3a8a, #00020ecc)',
      }
    }
  },
  plugins: [
    scrollbar,
  ]

}
