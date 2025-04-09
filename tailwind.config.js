
import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

const dynamicColors = require("./resources/js/tailwind.dynamic.colors.js");

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.fontFamily],
            },
            colors: {
                ...dynamicColors,
            },
            boxShadow: {
                "glow-primary": "0 0 15px rgba(155, 135, 245, 0.5)",
                "glow-secondary": "0 0 15px rgba(51, 195, 240, 0.5)",
                "inner-glow": "inset 0 0 10px rgba(155, 135, 245, 0.3)",
                float: "0 10px 25px -5px rgba(0, 0, 0, 0.3)",
            },
            backgroundImage: {
                "gradient-radial": "radial-gradient(var(--tw-gradient-stops))",
                "gradient-conic":
                    "conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))",
                "gradient-sidebar":
                    "linear-gradient(180deg, #131722 0%, #1A1F2C 100%)",
            },
            animation: {
                "pulse-slow": "pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite",
                "price-pulse": "price-pulse 3s infinite alternate",
                "card-breathing": "card-breathing 4s infinite ease-in-out",
            },
            keyframes: {
                "price-pulse": {
                    "0%": {
                        textShadow: "0 0 5px rgba(155, 135, 245, 0.3)",
                    },
                    "100%": {
                        textShadow: "0 0 12px rgba(155, 135, 245, 0.7)",
                    },
                },
                "card-breathing": {
                    "0%, 100%": { transform: "scale(1)" },
                    "50%": { transform: "scale(1.02)" },
                },
            },
            transitionProperty: {
                'width': 'width',
            },
        },
    },

    plugins: [forms],
};

