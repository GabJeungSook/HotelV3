const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')

module.exports = {
  presets: [
    require('./vendor/wireui/wireui/tailwind.config.js'),
  ],
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './vendor/wireui/wireui/src/**/*.php',
    './vendor/wireui/wireui/resources/**/*.blade.php',
    './vendor/wireui/wireui/ts/**/*.ts',
    './vendor/filament/**/*.blade.php',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
        alkatra: ['Alkatra', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        danger: colors.rose,
        primary: colors.gray,
        success: colors.green,
        warning: colors.yellow,
        dlogo: '#009EF5',
        custom: {
          50: 'rgba(var(--c-50), <alpha-value>)',
          100: 'rgba(var(--c-100), <alpha-value>)',
          200: 'rgba(var(--c-200), <alpha-value>)',
          300: 'rgba(var(--c-300), <alpha-value>)',
          400: 'rgba(var(--c-400), <alpha-value>)',
          500: 'rgba(var(--c-500), <alpha-value>)',
          600: 'rgba(var(--c-600), <alpha-value>)',
          700: 'rgba(var(--c-700), <alpha-value>)',
          800: 'rgba(var(--c-800), <alpha-value>)',
          900: 'rgba(var(--c-900), <alpha-value>)',
          950: 'rgba(var(--c-950), <alpha-value>)',
        },
      },
    },
  },

  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
}
