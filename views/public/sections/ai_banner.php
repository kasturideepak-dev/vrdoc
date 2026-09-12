<?php
/** AI landing — banner with title & description overlaid on the image. */
$entry = $entry ?? [];
$title = trim((string) ($entry['title'] ?? ''));
$desc = trim((string) ($entry['excerpt'] ?? ''));
$banner = trim((string) ($c['image'] ?? ''));
if ($banner === '') {
    $banner = trim((string) ($entry['featured_image'] ?? ''));
}
if ($banner === '') {
    $banner = $asset . 'img/banner/campuses-hero.jpg';
} elseif (!str_starts_with($banner, 'http') && !str_starts_with($banner, '/')) {
    $banner = $asset . ltrim($banner, '/');
}
?>
<section class="ai-hero">
  <div class="ai-hero__banner">
    <img src="<?= Html::e($banner) ?>" alt="<?= Html::e($title) ?>" width="1920" height="720" loading="eager" fetchpriority="high">
    <div class="ai-hero__shade" aria-hidden="true"></div>
    <?php if ($title !== '' || $desc !== '' || !empty($c['kicker'])): ?>
      <div class="container ai-hero__head">
        <?php if (!empty($c['kicker'])): ?>
          <span class="pill pill--light"><?= Html::e($c['kicker']) ?></span>
        <?php endif; ?>
        <?php if ($title !== ''): ?>
          <h1><?= Html::e($title) ?></h1>
        <?php endif; ?>
        <?php if ($desc !== ''): ?>
          <p class="ai-hero__lead"><?= Html::e($desc) ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
