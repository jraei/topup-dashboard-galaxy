
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#6D28D9',
                    50: '#EDE9F6',
                    100: '#DCD4F0',
                    200: '#B9A9E1',
                    300: '#967FD2',
                    400: '#8254DB',
                    500: '#6D28D9',
                    600: '#5821B0',
                    700: '#421986',
                    800: '#2C115C',
                    900: '#170832',
                    950: '#0C041B',
                },
                secondary: {
                    DEFAULT: '#FF5F85',
                    50: '#FFFFFF',
                    100: '#FFFFFF',
                    200: '#FFD8E1',
                    300: '#FFAFBF',
                    400: '#FF879E',
                    500: '#FF5F85',
                    600: '#FF2861',
                    700: '#F0003D',
                    800: '#B80030',
                    900: '#800022',
                    950: '#63001A',
                },
                dark: {
                    DEFAULT: '#1E293B',
                    50: '#8DA2BC',
                    100: '#7D94B2',
                    200: '#5F7A9D',
                    300: '#49607B',
                    400: '#344459',
                    500: '#1E293B',
                    600: '#18222F',
                    700: '#121A24',
                    800: '#0C1118',
                    900: '#06090C',
                    950: '#030406',
                },
            },
            backgroundImage: {
                'glow': 'radial-gradient(ellipse at center, rgba(109, 40, 217, 0.2) 0%, transparent 70%)',
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-card': 'linear-gradient(135deg, rgba(30, 41, 59, 0.5) 0%, rgba(15, 23, 42, 0.5) 100%)',
                'gradient-primary': 'linear-gradient(90deg, #6D28D9 0%, #8254DB 100%)',
                'gradient-secondary': 'linear-gradient(90deg, #FF5F85 0%, #FF2861 100%)',
            },
            boxShadow: {
                'glow-sm': '0 0 5px rgba(109, 40, 217, 0.3)',
                'glow': '0 0 10px rgba(109, 40, 217, 0.5)',
                'glow-lg': '0 0 15px rgba(109, 40, 217, 0.7)',
                'inner-glow': 'inset 0 0 10px rgba(109, 40, 217, 0.5)',
            },
            animation: {
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            dropShadow: {
                'glow': '0 0 8px rgba(109, 40, 217, 0.5)',
            },
        },
    },

    plugins: [forms],
};
