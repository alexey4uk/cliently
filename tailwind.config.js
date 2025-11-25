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
                primary: {
                    DEFAULT: '#8B5CF6',
                    dark: '#7C3AED'
                },
                secondary: {
                    DEFAULT: '#06B6D4',
                    dark: '#0891B2'
                },
                accent: {
                    DEFAULT: '#10B981',
                    dark: '#059669'
                },
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
        }
    },

    plugins: [forms],
};
