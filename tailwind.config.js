/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins', 'sans-serif'],
      },
      colors: {
        primary: "#0067B3",
      }
    },
  },
  plugins: [require('daisyui')],
  daisyui: {
    themes: ["light"], // ✅ ini sudah cukup untuk menjadikan "light" sebagai default  
  },
}
