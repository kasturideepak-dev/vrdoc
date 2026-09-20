<?php
/**
 * Post body (70%) beside a "latest posts" sidebar (30%).
 *
 * The sidebar lists the most recent published entries of a post type — by
 * default whichever type the post being viewed belongs to — and never includes
 * the current post.
 */
$entry = $entry ?? [];
$currentId = (int) ($entry['id'] ?? 0);

$source = trim((string) ($c['source'] ?? ''));
if ($source === '') {
    $source = (string) ($entry['_type']['slug'] ?? ($type['slug'] ?? ''));
}
$limit = (int) ($c['limit'] ?? 0);
$limit = $limit > 0 ? $limit : 5;

$posts = $source !== '' ? Cpt::latest($source, $limit, $currentId) : [];
$sidebarTitle = trim((string) ($c['sidebar_title'] ?? '')) ?: 'Latest posts';
$body = Html::allowedHtml((string) ($c['html'] ?? ''));
?>
<section class="section post-body">
  <div class="container post-body__grid">
    <article class="post-body__main prose">
      <?= $body ?>
    </article>

    <?php if ($posts): ?>
      <aside class="post-body__side" aria-label="<?= Html::e($sidebarTitle) ?>">
        <div class="post-side">
          <h2 class="post-side__title"><?= Html::e($sidebarTitle) ?></h2>
          <ul class="post-side__list">
            <?php foreach ($posts as $p):
              $img = trim((string) ($p['featured_image'] ?? ''));
              if ($img !== '' && !str_starts_with($img, 'http') && !str_starts_with($img, '/')) {
                  $img = $asset . ltrim($img, '/');
              }
              $url = Cpt::permalink($p);
              $when = $p['published_at'] ?? $p['created_at'] ?? '';
            ?>
              <li class="post-side__item">
                <a class="post-side__link" href="<?= Html::e($url) ?>">
                  <?php if ($img !== ''): ?>
                    <span class="post-side__thumb">
                      <img src="<?= Html::e($img) ?>" alt="" loading="lazy" width="120" height="80">
                    </span>
                  <?php endif; ?>
                  <span class="post-side__copy">
                    <span class="post-side__name"><?= Html::e($p['title']) ?></span>
                    <?php if ($when): ?>
                      <time class="post-side__date" datetime="<?= Html::e(date('Y-m-d', strtotime((string) $when))) ?>">
                        <?= Html::e(date('j M Y', strtotime((string) $when))) ?>
                      </time>
                    <?php endif; ?>
                  </span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </aside>
    <?php endif; ?>
  </div>
</section>
