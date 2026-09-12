<?php
/**
 * Single blog post — site chrome (vr-navbar, bg-blue-950 footer).
 */
$asset = $asset ?? '/assets/';
$post = $post ?? [];
$related = $related ?? [];
$categories = $categories ?? [];
$allCategories = $allCategories ?? [];
$tags = $tags ?? [];
$GLOBALS['vr_extra_stylesheets'] = [
    $asset . 'css/tokens.css',
    $asset . 'css/pages.css?v=blog-5',
];
$img = $post['featured_image'] ?? '';
if ($img === '') {
    $img = $asset . 'img/gallery/g6.jpg';
}
$pubDate = !empty($post['published_at']) ? date('j F Y', strtotime($post['published_at'])) : '';
$pubIso = !empty($post['published_at']) ? substr($post['published_at'], 0, 10) : '';

get_header();
?>
<main id="content" class="is-blog-post">
  <article>
    <header class="relative bg-gradient-to-br from-[#103058] via-blue-900 to-blue-950 text-white overflow-hidden">
      <?php if ($img): ?>
        <div class="absolute inset-0 opacity-20 bg-cover bg-center" style="background-image:url('<?php echo esc_attr($img); ?>')"></div>
      <?php endif; ?>
      <div class="absolute inset-0 bg-gradient-to-r from-[#103058]/95 via-[#103058]/85 to-blue-900/70"></div>
      <div class="relative max-w-7xl mx-auto px-6 py-14 md:py-20">
        <nav class="text-sm text-white/70 mb-4 flex flex-wrap gap-2 items-center">
          <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-orange-400 transition">Home</a>
          <span>/</span>
          <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="hover:text-orange-400 transition">Blog</a>
          <span>/</span>
          <span class="text-white line-clamp-1"><?php echo esc_html($post['title'] ?? ''); ?></span>
        </nav>
        <p class="text-orange-400 font-semibold tracking-wide uppercase text-sm mb-2">Insights</p>
        <?php if (!empty($categories)): ?>
          <div class="flex flex-wrap gap-2 mb-4">
            <?php foreach ($categories as $c): ?>
              <a href="<?php echo esc_url(home_url('/blog/category/' . $c['slug'] . '/')); ?>" class="px-3 py-1 rounded-full bg-orange-500/20 text-orange-300 text-xs font-semibold uppercase tracking-wide hover:bg-orange-500/30 transition"><?php echo esc_html($c['name']); ?></a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <h1 class="text-3xl md:text-5xl font-bold tracking-tight leading-tight max-w-4xl"><?php echo esc_html($post['title'] ?? ''); ?></h1>
        <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-white/80">
          <?php if ($pubDate): ?>
            <time datetime="<?php echo esc_attr($pubIso); ?>" class="text-orange-300 font-medium"><?php echo esc_html($pubDate); ?></time>
          <?php endif; ?>
          <?php if (!empty($post['author_name'])): ?>
            <span>By <?php echo esc_html($post['author_name']); ?></span>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <section class="py-12 md:py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-6">
        <div class="blog-post-layout">
          <div class="blog-post-main min-w-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
              <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($post['title'] ?? ''); ?>" class="w-full object-cover max-h-[420px]">
              <div class="p-6 md:p-10">
                <div class="blog-prose">
                  <?php echo Html::allowedHtml((string) ($post['body_html'] ?? '')); ?>
                </div>
              </div>
            </div>
          </div>

          <?php get_template_part('template-parts/blog/sidebar', null, [
              'asset' => $asset,
              'allCategories' => $allCategories,
              'related' => $related,
              'tags' => $tags,
              'categories' => $categories,
          ]); ?>
        </div>
      </div>
    </section>
  </article>

  <?php if (!empty($related)): ?>
    <section class="bg-white border-t border-gray-100 py-14">
      <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-2xl font-bold text-[#103058] mb-8">More from the blog</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <?php foreach (array_slice($related, 0, 3) as $r):
            $rImg = $r['featured_image'] ?: $asset . 'img/gallery/g6.jpg';
          ?>
            <a href="<?php echo esc_url(home_url('/blog/' . $r['slug'] . '/')); ?>" class="group bg-white rounded-xl overflow-hidden border border-gray-100 hover:shadow-md hover:border-orange-200 transition">
              <div class="aspect-[16/10] overflow-hidden">
                <img src="<?php echo esc_url($rImg); ?>" alt="<?php echo esc_attr($r['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
              </div>
              <div class="p-5">
                <?php if (!empty($r['published_at'])): ?>
                  <time datetime="<?php echo esc_attr(substr($r['published_at'], 0, 10)); ?>" class="text-sm text-orange-500 font-medium"><?php echo esc_html(date('j M Y', strtotime($r['published_at']))); ?></time>
                <?php endif; ?>
                <h3 class="mt-1 font-bold text-[#103058] group-hover:text-orange-600 transition-colors leading-snug"><?php echo esc_html($r['title']); ?></h3>
                <p class="mt-2 text-sm text-gray-600 line-clamp-2"><?php echo esc_html($r['excerpt']); ?></p>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
        <div class="mt-8 text-center">
          <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-3 rounded-xl transition">View all articles</a>
        </div>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php
get_footer();
