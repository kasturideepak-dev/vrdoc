<?php
declare(strict_types=1);

/**
 * One-time migration: AI post type, landing template, campuses, and demo content.
 * Run: php database/migrate-ai.php
 */
define('ROOT', dirname(__DIR__));
define('VRDR_INSTALLING', true);
require ROOT . '/app/bootstrap.php';

Templates::ensureStarters();
Cpt::ensureCampuses();
Cpt::ensureAiType();
Cpt::refreshAiLandingContent();
Database::update('post_types', [
    'name' => 'AI Posts',
    'singular_name' => 'AI Post',
    'description' => 'AI landing pages — post title and description show on the banner',
    'supports_excerpt' => 1,
    'supports_featured_image' => 1,
], 'slug = ?', ['ai']);
Settings::ensureBrandAssets();
Cache::flush();

$type = Cpt::type('ai');
$entry = Cpt::entry('ai', 'ai-neet-coaching');
$tpl = Templates::aiLandingId();
$campuses = Cpt::published('campuses');

echo "AI migration complete\n";
echo '  Post type: ' . ($type ? $type['name'] . ' (/' . $type['slug'] . '/)' : 'MISSING') . "\n";
echo '  Template:  ' . ($tpl ? 'ai-landing (id ' . $tpl . ')' : 'MISSING') . "\n";
echo '  Entry:     ' . ($entry ? $entry['title'] . ' [' . $entry['status'] . ']' : 'MISSING') . "\n";
echo '  Campuses:  ' . count($campuses) . "\n";
if ($entry) {
    $n = count(Database::all(
        'SELECT id FROM content_sections WHERE owner_type = "cpt" AND owner_id = ?',
        [(int) $entry['id']]
    ));
    echo '  Sections:  ' . $n . "\n";
    echo '  URL:       ' . path_url('ai/ai-neet-coaching') . "\n";
}
echo '  Logo:      ' . Settings::logoUrl('/assets/') . "\n";
