/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
    "./src/**/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        midnight: "#00020e",
        deepblue: "#181c36",
        fog: "#cfcfd1",
      },
    },
  },
  plugins: [],
}
