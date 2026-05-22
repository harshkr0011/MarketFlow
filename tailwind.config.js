import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
        './app/Filament/**/*.php',
        './resources/js/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                navy: {
                    950: '#090D16',
                    900: '#0F172A',
                    850: '#172033',
                    800: '#1E293B',
                    750: '#253346',
                    700: '#334155',
                },
                neon: {
                    purple: '#A855F7',
                    cyan: '#22D3EE',
                }
            },
            animation: {
                blob: "blob 7s infinite",
                shimmer: "shimmer 2s infinite",
            },
            keyframes: {
                blob: {
                    "0%": {
                        transform: "translate(0px, 0px) scale(1)",
                    },
                    "33%": {
                        transform: "translate(30px, -50px) scale(1.1)",
                    },
                    "66%": {
                        transform: "translate(-20px, 20px) scale(0.9)",
                    },
                    "100%": {
                        transform: "translate(0px, 0px) scale(1)",
                    },
                },
                shimmer: {
                    "100%": {
                        transform: "translateX(100%)",
                    },
                },
            },
        },
    },

    plugins: [
        forms,
        function({ addUtilities }) {
            const newUtilities = {
                '.animation-delay-2000': {
                    'animation-delay': '2s',
                },
                '.animation-delay-4000': {
                    'animation-delay': '4s',
                },
            };
            addUtilities(newUtilities);
        }
    ],
};
