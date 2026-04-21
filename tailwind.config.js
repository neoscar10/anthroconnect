import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
              stone: {
                50: "#fafaf9",
                100: "#f5f5f4",
                200: "#e7e5e4",
                300: "#d6d3d1",
                400: "#a8a29e",
                500: "#78716c",
                600: "#57534e",
                700: "#44403c",
                800: "#292524",
                900: "#1c1917",
                950: "#0c0a09",
              },
              orange: {
                50: "#fffaf0",
                100: "#ffedd5",
                200: "#fed7aa",
                300: "#fdba74",
                700: "#c2410c",
                800: "#9a3412",
                900: "#7c2d12",
              }
            },
            fontFamily: {
              headline: ["Lora", "serif"],
              body: ["Public Sans", "sans-serif"],
              sans: ["Public Sans", ...defaultTheme.fontFamily.sans],
              serif: ["Lora", ...defaultTheme.fontFamily.serif],
            },
            keyframes: {
                'progress-fast': {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(100%)' },
                }
            },
            animation: {
                'progress-fast': 'progress-fast 1s infinite linear',
            },
        },
    },

    plugins: [forms],
};
