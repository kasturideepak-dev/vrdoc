<?php
/**
 * Blog single post — sidebar widgets.
 */
$asset = $asset ?? '/assets/';
$allCategories = $allCategories ?? [];
$related = $related ?? [];
$tags = $tags ?? [];
$postCategorySlugs = array_column($categories ?? [], 'slug');
$s = class_exists('Settings') ? Settings::all() : [];
$phoneRaw = trim((string) ($s['phone_primary'] ?? '919256925640'));
$phoneTel = preg_replace('/\D+/', '', $phoneRaw) ?: '919256925640';
?>
<aside class="blog-sidebar space-y-6 lg:sticky lg:top-24" aria-label="Blog sidebar">
  <?php if (!empty($allCategories)): ?>
    <div class="blog-sidebar__widget">
      <h2 class="blog-sidebar__title">Categories</h2>
      <ul class="blog-sidebar__categories">
        <li>
          <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="blog-sidebar__pill">All articles</a>
        </li>
        <?php foreach ($allCategories as $c): ?>
          <li>
            <a
              href="<?php echo esc_url(home_url('/blog/category/' . $c['slug'] . '/')); ?>"
              class="blog-sidebar__pill<?php echo in_array($c['slug'], $postCategorySlugs, true) ? ' is-active' : ''; ?>"
            ><?php echo esc_html($c['name']); ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (!empty($related)): ?>
    <div class="blog-sidebar__widget">
      <h2 class="blog-sidebar__title">Recent posts</h2>
      <ul class="blog-sidebar__recent">
        <?php foreach (array_slice($related, 0, 4) as $r):
          $rImg = $r['featured_image'] ?: $asset . 'img/gallery/g6.jpg';
        ?>
          <li>
            <a href="<?php echo esc_url(home_url('/blog/' . $r['slug'] . '/')); ?>" class="blog-sidebar__recent-link group">
              <span class="blog-sidebar__recent-thumb">
                <img src="<?php echo esc_url($rImg); ?>" alt="" loading="lazy" class="w-full h-full object-cover">
              </span>
              <span class="blog-sidebar__recent-body">
                <?php if (!empty($r['published_at'])): ?>
                  <time datetime="<?php echo esc_attr(substr($r['published_at'], 0, 10)); ?>" class="blog-sidebar__recent-date">
                    <?php echo esc_html(date('j M Y', strtotime($r['published_at']))); ?>
                  </time>
                <?php endif; ?>
                <span class="blog-sidebar__recent-title"><?php echo esc_html($r['title']); ?></span>
              </span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (!empty($tags)): ?>
    <div class="blog-sidebar__widget">
      <h2 class="blog-sidebar__title">Tags</h2>
      <div class="flex flex-wrap gap-2">
        <?php foreach ($tags as $t): ?>
          <span class="blog-sidebar__tag"><?php echo esc_html($t['name']); ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="blog-sidebar__cta">
    <p class="text-orange-400 font-semibold uppercase tracking-widest text-xs">Admissions</p>
    <h2 class="mt-2 text-xl font-bold text-white leading-snug">Plan your NEET journey with VR Doctors</h2>
    <p class="mt-2 text-sm text-blue-100 leading-relaxed">Speak to our counsellors about residential programmes, scholarships and campus visits in Hyderabad.</p>
    <div class="mt-5 flex flex-col gap-2">
      <a href="<?php echo esc_url(home_url('/contact/#enquire')); ?>" class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-3 rounded-xl transition text-sm">
        Enquire now
      </a>
      <a href="tel:+<?php echo esc_attr($phoneTel); ?>" class="inline-flex items-center justify-center border border-white/30 hover:bg-white/10 text-white font-semibold px-5 py-3 rounded-xl transition text-sm">
        Call admissions
      </a>
    </div>
  </div>
</aside>
