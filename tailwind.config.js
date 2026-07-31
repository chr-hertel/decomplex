const colors = require('tailwindcss/colors')

module.exports = {
    content: [
        './templates/**/*.html.twig',
        './assets/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                // v3 renamed blueGray to slate, the palette values are identical
                gray: colors.slate,
            },
            opacity: {
                '85': '0.85',
            },
        }
    }
}
