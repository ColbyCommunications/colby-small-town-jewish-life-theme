module.exports = {
    content: [
        './app/**/*.php',
        './resources/**/*.{php,vue,js}',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                'colby-blue': '#002878',
                'colby-warm-gray': '#e3d7d1',
                'colby-light-blue': '#b7d4f1',
                'colby-dark-green': '#428d50',
                'colby-orange': '#e15630',
                'colby-gold': '#f1bd5b',
                'colby-purple': '#6a3e90',
                'colby-light-green': '#C5E86C',
                'colby-warm-red': '#d95900',
            },
            fontFamily: {
                sans: ['libre-franklin', 'Arial', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
