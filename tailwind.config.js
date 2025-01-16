/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./assets/**/*.vue",
  ],
  theme: {
    extend: {},
    colors: {
      primary: 'rgb(var(--color-primary))',
      secondary: 'rgb(var(--color-secondary))',
      black: 'rgba(var(--black))',
      red: 'rgb(var(--red))',
      greyCustom: 'rgba(var(--greyCustom))',
      brown: 'rgba(var(--brown))',
      fillGrey: 'rgba(var(--fillGrey))'
    }
  },
  plugins: [],
}