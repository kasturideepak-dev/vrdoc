<?php
declare(strict_types=1);

/**
 * Seed blog categories and demo posts.
 * Run: php database/migrate-blog.php
 */
define('ROOT', dirname(__DIR__));
define('VRDR_INSTALLING', true);
require ROOT . '/app/bootstrap.php';

Blog::ensureCategories();
Blog::ensurePosts();
Cache::flush();

$posts = Database::all(
    'SELECT title, slug, status, published_at FROM blog_posts WHERE deleted_at IS NULL ORDER BY published_at DESC'
);
$cats = Database::all('SELECT name, slug FROM blog_categories ORDER BY name');

echo "Blog migration complete\n";
echo '  Categories: ' . count($cats) . "\n";
foreach ($cats as $c) {
    echo '    - ' . $c['name'] . ' (' . $c['slug'] . ")\n";
}
echo '  Posts: ' . count($posts) . "\n";
foreach ($posts as $p) {
    echo '    - ' . $p['title'] . ' [' . $p['status'] . '] → ' . path_url('blog/' . $p['slug']) . "\n";
}
