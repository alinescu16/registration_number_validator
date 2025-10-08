/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.antlers.html',
    './resources/test.html',
  ],
  safelist: [
    'bg-lime-500',
    'bg-rose-500',
    'text-lime-500',
    'text-rose-500',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}