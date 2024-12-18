/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
      "./resources/**/*.{js,ts,jsx,tsx,vue}",
  ],
  theme: {
    extend: {
      colors: {

      }
    },
    fontFamily: {
      Roboto: ["Roboto, sans-serif"],
    },
    container: {
      padding: "2rem",
      center: true,
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}

