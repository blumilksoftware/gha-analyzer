import colors from "./resources/data/colors.json"

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    safelist: [
      ...colors
    ],
    theme: {
        extend: {

        },

    },
    plugins: [],
}
