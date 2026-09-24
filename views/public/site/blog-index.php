<?php
/**
 * Blog index — site chrome (vr-navbar, bg-blue-950 footer).
 */
$asset = $asset ?? '/assets/';
$GLOBALS['vr_extra_stylesheets'] = [
    $asset . 'css/tokens.css',
    site_asset($asset, 'css/pages.css'),
];
$cat = $cat ?? null;
$pageNum = $pageNum ?? 1;
$totalPages = $totalPages ?? 1;
$canonicalPath = $canonicalPath ?? '/blog/';
$posts = $posts ?? [];
$categories = $categories ?? [];

get_header();
?>
<main id="content" class="is-blog-index">
  <section class="relative bg-gradient-to-br from-[#103058] via-blue-900 to-blue-950 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-20 bg-[url('/assets/img/gallery/classroom.jpg')] bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-[#103058]/95 via-[#103058]/85 to-blue-900/70"></div>
    <div class="relative max-w-7xl mx-auto px-6 py-16 md:py-20">
      <nav class="text-sm text-white/70 mb-4 flex flex-wrap gap-2 items-center">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-orange-400 transition">Home</a>
        <span>/</span>
        <?php if ($cat): ?>
          <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="hover:text-orange-400 transition">Blog</a>
          <span>/</span>
          <span class="text-white"><?php echo esc_html($cat['name']); ?></span>
        <?php else: ?>
          <span class="text-white">Blog</span>
        <?php endif; ?>
      </nav>
      <p class="text-orange-400 font-semibold tracking-wide uppercase text-sm mb-2">Insights</p>
      <h1 class="text-3xl md:text-5xl font-bold tracking-tight max-w-3xl"><?php echo esc_html($cat['name'] ?? 'Blog'); ?></h1>
      <p class="mt-4 text-lg text-white/85 max-w-2xl leading-relaxed"><?php echo esc_html($cat['description'] ?? 'Education, NEET and IIT-JEE preparation notes from VR Doctors, Hyderabad.'); ?></p>
    </div>
  </section>

  <section class="py-12 md:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
      <?php if (!empty($categories)): ?>
        <div class="flex flex-wrap gap-2 mb-10">
          <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="px-4 py-2 rounded-full text-sm font-semibold transition <?php echo !$cat ? 'bg-[#103058] text-white' : 'bg-white text-blue-900 border border-gray-200 hover:border-orange-400'; ?>">All</a>
          <?php foreach ($categories as $c): ?>
            <a href="<?php echo esc_url(home_url('/blog/category/' . $c['slug'] . '/')); ?>" class="px-4 py-2 rounded-full text-sm font-semibold transition <?php echo ($cat && ($cat['slug'] ?? '') === $c['slug']) ? 'bg-[#103058] text-white' : 'bg-white text-blue-900 border border-gray-200 hover:border-orange-400'; ?>"><?php echo esc_html($c['name']); ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (empty($posts)): ?>
        <p class="text-blue-900/70 text-lg">No articles yet. Check back soon.</p>
      <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php foreach ($posts as $p):
            $img = $p['featured_image'] ?: $asset . 'img/gallery/g6.jpg';
            $href = home_url('/blog/' . $p['slug'] . '/');
          ?>
            <a href="<?php echo esc_url($href); ?>" class="group bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg hover:border-orange-200 transition-all duration-300 flex flex-col">
              <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                <img src="<?php echo esc_url(vr_media_url($img)); ?>" alt="<?php echo esc_attr($p['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
              </div>
              <div class="p-6 flex flex-col flex-1">
                <?php if (!empty($p['published_at'])): ?>
                  <time datetime="<?php echo esc_attr(substr($p['published_at'], 0, 10)); ?>" class="text-sm text-orange-500 font-medium"><?php echo esc_html(date('j M Y', strtotime($p['published_at']))); ?></time>
                <?php endif; ?>
                <h2 class="mt-2 text-xl font-bold text-[#103058] group-hover:text-orange-600 transition-colors leading-snug"><?php echo esc_html($p['title']); ?></h2>
                <p class="mt-3 text-gray-600 text-sm leading-relaxed flex-1"><?php echo esc_html($p['excerpt']); ?></p>
                <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-orange-500 group-hover:gap-2 transition-all">Read article <span aria-hidden="true">→</span></span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($totalPages > 1): ?>
        <nav class="mt-12 flex flex-wrap justify-center gap-2" aria-label="Pagination">
          <?php for ($i = 1; $i <= $totalPages; $i++):
            $href = $i === 1 ? $canonicalPath : rtrim($canonicalPath, '/') . '/page/' . $i . '/';
          ?>
            <?php if ($i === $pageNum): ?>
              <span class="w-10 h-10 flex items-center justify-center rounded-full bg-[#103058] text-white font-semibold"><?php echo $i; ?></span>
            <?php else: ?>
              <a href="<?php echo esc_url($href); ?>" class="w-10 h-10 flex items-center justify-center rounded-full bg-white border border-gray-200 text-blue-900 font-semibold hover:border-orange-400 transition"><?php echo $i; ?></a>
            <?php endif; ?>
          <?php endfor; ?>
        </nav>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php
get_footer();
