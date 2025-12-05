/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./views/**/*.php",
    "./admin/**/*.php",
    "./*.php",
    "./components/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        primary: '#7B2C2C',
        'primary-dark': '#6B2222'
      }
    },
  },
  plugins: [],
}