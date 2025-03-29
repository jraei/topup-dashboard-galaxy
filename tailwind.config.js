
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
                // Primary colors
                primary: {
                    DEFAULT: '#9b87f5',
                    hover: '#a897f6',
                    dark: '#7E69AB',
                    light: '#c2b5fa',
                },
                // Secondary colors
                secondary: {
                    DEFAULT: '#33C3F0',
                    hover: '#5ed4f5',
                    dark: '#2497ba',
                    light: '#7ee1ff',
                },
                // Background colors
                dark: {
                    DEFAULT: '#1A1F2C',
                    lighter: '#222838',
                    card: '#252c3e', 
                    sidebar: '#131722',
                    deepest: '#0e1018',
                },
                accent: {
                    purple: '#7E69AB',
                    blue: '#33C3F0',
                    green: '#4CAF50',
                    red: '#f56565',
                    yellow: '#FFC107',
                },
            },
            boxShadow: {
                'glow-primary': '0 0 15px rgba(155, 135, 245, 0.5)',
                'glow-secondary': '0 0 15px rgba(51, 195, 240, 0.5)',
                'inner-glow': 'inset 0 0 10px rgba(155, 135, 245, 0.3)',
                'float': '0 10px 25px -5px rgba(0, 0, 0, 0.3)',
                'card-glow': '0 0 20px 0 rgba(155, 135, 245, 0.15)',
                'card-glow-hover': '0 0 30px 0 rgba(155, 135, 245, 0.25)',
                'neon-primary': '0 0 5px rgba(155, 135, 245, 0.5), 0 0 20px rgba(155, 135, 245, 0.3)',
                'neon-secondary': '0 0 5px rgba(51, 195, 240, 0.5), 0 0 20px rgba(51, 195, 240, 0.3)',
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                'gradient-sidebar': 'linear-gradient(180deg, #131722 0%, #1A1F2C 100%)',
                'gradient-card': 'linear-gradient(145deg, rgba(37, 44, 62, 0.8) 0%, rgba(26, 31, 44, 0.8) 100%)',
                'gradient-primary': 'linear-gradient(90deg, #9b87f5, #7E69AB)',
                'gradient-secondary': 'linear-gradient(90deg, #33C3F0, #2497ba)',
                'gradient-dark': 'linear-gradient(90deg, #1A1F2C, #131722)',
            },
            animation: {
                'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'float': 'float 6s ease-in-out infinite',
                'glow': 'glow 1.5s ease-in-out infinite alternate',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                glow: {
                    '0%': { boxShadow: '0 0 5px rgba(155, 135, 245, 0.5), 0 0 10px rgba(155, 135, 245, 0.3)' },
                    '100%': { boxShadow: '0 0 10px rgba(155, 135, 245, 0.7), 0 0 20px rgba(155, 135, 245, 0.5)' },
                },
            },
        },
    },

    plugins: [forms],
};
