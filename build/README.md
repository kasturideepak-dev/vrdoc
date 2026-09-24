# Rebuilding the site CSS

`assets/site/css/tailwind.css` is a prebuilt Tailwind stylesheet. It replaced
`cdn.tailwindcss.com` (the Play CDN), which shipped a ~450 KB compiler to every
visitor, blocked rendering and rebuilt the CSS in the browser on every visit.

Rebuild it after changing classes in any template:

    npm install tailwindcss@3.4.17
    npx tailwindcss -c build/tailwind.config.js -i build/tailwind.in.css \
        -o assets/site/css/tailwind.css --minify

Classes that only exist in database content (page sections saved in the admin)
are not in the templates. To catch those too, save the rendered pages to a
folder and add it to `content` in build/tailwind.config.js before building.

The URL is cache-busted by file modification time, so no version bump is needed.
