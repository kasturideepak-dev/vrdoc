<?php
/**
 * AI CPT landing — section builder content with the same site chrome as the homepage.
 */
$asset = $asset ?? '/assets/';
$GLOBALS['vr_extra_stylesheets'] = [
    $asset . 'css/tokens.css',
    // base.css owns `.container` — its max width AND `margin-inline: auto`.
    // Without it every section here renders 1280px wide but flush left.
    $asset . 'css/base.css',
    $asset . 'css/components.css?v=float-right',
    $asset . 'css/sections.css?v=ai-1',
    $asset . 'css/pages.css?v=blog-5',
];
$GLOBALS['vr_extra_scripts'] = [
    $asset . 'js/main.js?v=reels-1',
];

get_header();
?>
<svg xmlns="http://www.w3.org/2000/svg" style="display:none" aria-hidden="true">
  <symbol id="i-play" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></symbol>
</svg>
<main id="content" class="is-ai-landing">
<?php
foreach ($sections ?? [] as $sec) {
    $c = $sec['content'] ?? (json_decode($sec['content_json'] ?? '{}', true) ?: []);
    $type = $sec['type'] ?? '';
    $file = ROOT . '/views/public/sections/' . $type . '.php';
    if (is_file($file)) {
        require $file;
    }
}
?>
</main>
<?php
get_footer();
