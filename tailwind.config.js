/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    // "./**/*.php",
    "./admin/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        secondary: {
          darkest:      '#2E0AA4',
          dark:         '#4018C1',
          DEFAULT:      '#4B22CC',
          light:        '#552CD4',
          lighter:      '#6A44E6',
          lighest:      '#8B6AF7',
        },
        'secondary-yellow': {
          dark:         '#DAE442',
          DEFAULT:      '#E3ED5D',
          light:        '#EEF678',
        },
        'secondary-black': {
          dark:         '#101010',
          DEFAULT:      '#1A1A1A',
          light:        '#2B2B2B',
        },
        'success': {
          DEFAULT:      '#249907',
          light:        '#E3FFDC',
        },
        'danger': {
          DEFAULT:      '#F5482C',
          light:        '#FEEDEA',
        },
      },
      fontFamily: {
        inter: ['Inter', 'sans-serif']
      }
    },
  },
  plugins: [],
}

