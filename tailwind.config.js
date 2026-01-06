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
            fontFamily: {
                sans: ['Inter', 'DM Sans', ...defaultTheme.fontFamily.sans],
                heading: ['Playfair Display', 'Cormorant Garamond', ...defaultTheme.fontFamily.serif],
                accent: ['Satisfy', 'Dancing Script', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Primary Colors - Flora Theme
                sage: {
                    50: '#f0f4ed',
                    100: '#e0e9db',
                    200: '#c1d3b7',
                    300: '#a2bd93',
                    400: '#87A96B', // Main brand color
                    500: '#6b8e4f',
                    600: '#557153', // Forest Green
                    700: '#445542',
                    800: '#333931',
                    900: '#221d20',
                },
                mint: {
                    50: '#f0faf7',
                    100: '#d4f4e8',
                    200: '#A7D7C5', // Light accent
                    300: '#7abaa2',
                    400: '#4d9d7f',
                    500: '#20805c',
                    600: '#1a6649',
                    700: '#134c36',
                    800: '#0d3323',
                    900: '#061910',
                },
                leaf: {
                    50: '#f2f5e8',
                    100: '#e5ebd1',
                    200: '#cbd7a3',
                    300: '#b1c375',
                    400: '#6B8E23', // Income color
                    500: '#56721c',
                    600: '#455615',
                    700: '#343a0f',
                    800: '#231e08',
                    900: '#120202',
                },
                // Secondary Colors
                coral: {
                    50: '#fff5f5',
                    100: '#ffe3e3',
                    200: '#ffc7c7',
                    300: '#ffabab',
                    400: '#FF6B6B', // Expense color
                    500: '#ff4f4f',
                    600: '#cc3636',
                    700: '#992828',
                    800: '#661b1b',
                    900: '#330d0d',
                },
                cream: {
                    50: '#FFFEF9',
                    100: '#FFF8DC', // Background soft
                    200: '#fff5d0',
                    300: '#fff2c4',
                    400: '#ffefb8',
                    500: '#ffe9a0',
                    600: '#ccba80',
                    700: '#998b60',
                    800: '#665c40',
                    900: '#332e20',
                },
                ivory: {
                    50: '#FEFEF3', // Card background
                    100: '#fefef0',
                    200: '#fefce0',
                    300: '#fdfad0',
                    400: '#fdf8c0',
                    500: '#fcf6b0',
                    600: '#c9c58d',
                    700: '#96946a',
                    800: '#646247',
                    900: '#323123',
                },
                earth: {
                    50: '#f5f3f0',
                    100: '#ebe7e1',
                    200: '#d7cfc3',
                    300: '#c3b7a5',
                    400: '#8B7355', // Text/neutral
                    500: '#6f5c44',
                    600: '#584a36',
                    700: '#423729',
                    800: '#2c251b',
                    900: '#16120e',
                },
                // Accent Colors
                golden: {
                    50: '#fffef0',
                    100: '#fffde0',
                    200: '#fffbc1',
                    300: '#fff9a2',
                    400: '#FFD700', // Highlights
                    500: '#ccac00',
                    600: '#998100',
                    700: '#665600',
                    800: '#332b00',
                    900: '#191500',
                },
                sky: {
                    50: '#f0f9fc',
                    100: '#d4f0f8',
                    200: '#a9e1f1',
                    300: '#87CEEB', // Info messages
                    400: '#6bb8d9',
                    500: '#4fa3c7',
                    600: '#3f829f',
                    700: '#2f6177',
                    800: '#1f414f',
                    900: '#0f2027',
                },
                lavender: {
                    50: '#faf9fe',
                    100: '#f5f3fd',
                    200: '#ebe7fb',
                    300: '#E6E6FA', // Subtle accents
                    400: '#d1cff5',
                    500: '#bcb8f0',
                    600: '#9693c0',
                    700: '#706e90',
                    800: '#4a4960',
                    900: '#252430',
                },
            },
            animation: {
                'leaf-sway': 'leaf-sway 3s ease-in-out infinite',
                'bloom': 'bloom 0.6s ease-out',
                'grow': 'grow 0.8s ease-out',
                'fall': 'fall 1s ease-out',
                'wilt': 'wilt 0.5s ease-out',
                'float': 'float 3s ease-in-out infinite',
                'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                'leaf-sway': {
                    '0%, 100%': { transform: 'rotate(-2deg)' },
                    '50%': { transform: 'rotate(2deg)' },
                },
                'bloom': {
                    '0%': { transform: 'scale(0)', opacity: '0' },
                    '50%': { transform: 'scale(1.1)' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                'grow': {
                    '0%': { transform: 'scaleY(0)', transformOrigin: 'bottom' },
                    '100%': { transform: 'scaleY(1)', transformOrigin: 'bottom' },
                },
                'fall': {
                    '0%': { transform: 'translateY(-20px) rotate(0deg)', opacity: '1' },
                    '100%': { transform: 'translateY(20px) rotate(10deg)', opacity: '0' },
                },
                'wilt': {
                    '0%': { transform: 'rotate(0deg) scale(1)' },
                    '100%': { transform: 'rotate(-10deg) scale(0.9)', opacity: '0.5' },
                },
                'float': {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
            },
            boxShadow: {
                'flora': '0 4px 6px -1px rgba(135, 169, 107, 0.1), 0 2px 4px -1px rgba(135, 169, 107, 0.06)',
                'flora-lg': '0 10px 15px -3px rgba(135, 169, 107, 0.1), 0 4px 6px -2px rgba(135, 169, 107, 0.05)',
            },
            backgroundImage: {
                'flora-gradient': 'linear-gradient(135deg, #87A96B 0%, #A7D7C5 100%)',
                'flora-gradient-dark': 'linear-gradient(135deg, #557153 0%, #87A96B 100%)',
            },
        },
    },

    plugins: [forms],
};
