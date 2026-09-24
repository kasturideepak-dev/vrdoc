/** Replaces the Play CDN (cdn.tailwindcss.com), which shipped a ~450 KB
 *  compiler to every visitor. Scans the templates AND the rendered pages,
 *  so classes that live in database content are kept too. */
const R = require('path').resolve(__dirname, '..');
module.exports = {
  content: [
    `${R}/views/**/*.php`,
    `${R}/app/theme/**/*.php`,
    `${R}/app/**/*.php`,
    `${R}/assets/site/**/*.js`,
    // Optionally also scan rendered pages (see build/README.md) for classes
    // that live in database content.
  ],
  theme: { extend: { fontFamily: {
    sans: ['Inter', 'system-ui', 'sans-serif'],
    display: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
  } } },
};
