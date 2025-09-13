import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import aspectRatio from '@tailwindcss/aspect-ratio';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Gallery brand colors
                gallery: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                    950: '#082f49',
                },
                // Custom grays
                'warm-gray': {
                    50: '#fafaf9',
                    100: '#f5f5f4',
                    200: '#e7e5e4',
                    300: '#d6d3d1',
                    400: '#a8a29e',
                    500: '#78716c',
                    600: '#57534e',
                    700: '#44403c',
                    800: '#292524',
                    900: '#1c1917',
                },
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
            maxWidth: {
                '8xl': '88rem',
                '9xl': '96rem',
            },
            aspectRatio: {
                '4/3': '4 / 3',
                '3/2': '3 / 2',
                '2/3': '2 / 3',
                '9/16': '9 / 16',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.5s ease-out',
                'scale-in': 'scaleIn 0.3s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.9)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },
            backdropBlur: {
                'xs': '2px',
            },
            gridTemplateColumns: {
                'auto-fit-250': 'repeat(auto-fit, minmax(250px, 1fr))',
                'auto-fit-300': 'repeat(auto-fit, minmax(300px, 1fr))',
                'auto-fill-200': 'repeat(auto-fill, minmax(200px, 1fr))',
            },
            zIndex: {
                '60': '60',
                '70': '70',
                '80': '80',
                '90': '90',
                '100': '100',
            },
        },
    },

    plugins: [
        forms,
        typography,
        aspectRatio,
        
        // Custom plugins
        function({ addUtilities, addComponents, theme }) {
            // Custom utilities
            const newUtilities = {
                '.text-shadow': {
                    textShadow: '0 2px 4px rgba(0,0,0,0.10)',
                },
                '.text-shadow-md': {
                    textShadow: '0 4px 8px rgba(0,0,0,0.12), 0 2px 4px rgba(0,0,0,0.08)',
                },
                '.text-shadow-lg': {
                    textShadow: '0 15px 35px rgba(0,0,0,0.10), 0 5px 15px rgba(0,0,0,0.07)',
                },
                '.text-shadow-none': {
                    textShadow: 'none',
                },
                
                // Image aspect ratio utilities
                '.aspect-square': {
                    aspectRatio: '1 / 1',
                },
                '.aspect-photo': {
                    aspectRatio: '4 / 3',
                },
                '.aspect-video': {
                    aspectRatio: '16 / 9',
                },
                
                // Glassmorphism effect
                '.glass': {
                    backdropFilter: 'blur(16px) saturate(180%)',
                    backgroundColor: 'rgba(255, 255, 255, 0.75)',
                    border: '1px solid rgba(209, 213, 219, 0.3)',
                },
                '.glass-dark': {
                    backdropFilter: 'blur(16px) saturate(180%)',
                    backgroundColor: 'rgba(17, 25, 40, 0.75)',
                    border: '1px solid rgba(255, 255, 255, 0.125)',
                },
            };

            // Custom components
            const newComponents = {
                // Gallery card component
                '.gallery-card': {
                    position: 'relative',
                    backgroundColor: theme('colors.white'),
                    borderRadius: theme('borderRadius.lg'),
                    overflow: 'hidden',
                    boxShadow: theme('boxShadow.sm'),
                    transition: 'all 0.3s ease',
                    '&:hover': {
                        boxShadow: theme('boxShadow.lg'),
                        transform: 'translateY(-2px)',
                    },
                },
                
                // Loading spinner
                '.spinner': {
                    display: 'inline-block',
                    width: '20px',
                    height: '20px',
                    border: '3px solid rgba(255,255,255,.3)',
                    borderRadius: '50%',
                    borderTopColor: '#fff',
                    animation: 'spin 1s ease-in-out infinite',
                },
                
                // Image overlay
                '.image-overlay': {
                    position: 'absolute',
                    inset: '0',
                    backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    opacity: '0',
                    transition: 'opacity 0.3s ease',
                    '&:hover': {
                        opacity: '1',
                    },
                },
            };

            addUtilities(newUtilities);
            addComponents(newComponents);
        },
    ],

    // Dark mode configuration
    darkMode: 'class',
    
    // Safelist for dynamic classes
    safelist: [
        'bg-red-50',
        'bg-yellow-50',
        'bg-green-50',
        'bg-blue-50',
        'text-red-800',
        'text-yellow-800',
        'text-green-800',
        'text-blue-800',
        'border-red-200',
        'border-yellow-200',
        'border-green-200',
        'border-blue-200',
        // Grid column variations
        'grid-cols-1',
        'grid-cols-2',
        'grid-cols-3',
        'grid-cols-4',
        'grid-cols-5',
        'grid-cols-6',
        'sm:grid-cols-2',
        'sm:grid-cols-3',
        'sm:grid-cols-4',
        'md:grid-cols-3',
        'md:grid-cols-4',
        'md:grid-cols-5',
        'lg:grid-cols-4',
        'lg:grid-cols-5',
        'lg:grid-cols-6',
        'xl:grid-cols-5',
        'xl:grid-cols-6',
        'xl:grid-cols-8',
    ],
};
