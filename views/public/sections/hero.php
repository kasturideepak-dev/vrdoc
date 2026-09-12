<?php
$stats = Html::lines($c['stats'] ?? '');
$icons = ['i-people','i-medal','i-learn','i-star'];
?>
<section class="hero" id="top">
  <img class="hero__photo" src="<?= Html::e($c['image'] ?? $asset . 'img/banner/hero-class.jpg') ?>" alt="" width="1600" height="900">
  <div class="hero__shade"></div>
  <div class="container hero__inner">
    <div class="hero-stage">
      <div class="hero-copy">
        <?php if (!empty($c['kicker'])): ?><p class="hero-kicker"><?= Html::e($c['kicker']) ?></p><?php endif; ?>
        <h1><?= Html::e($c['heading'] ?? '') ?></h1>
        <p class="hero-lead"><?= Html::e($c['lead'] ?? '') ?></p>
        <div class="hero-actions">
          <a class="apply-chip apply-chip--hero" href="<?= Html::e($c['cta_url'] ?? '/contact-us/#enquire') ?>"><?= Html::e($c['cta_label'] ?? 'Apply now') ?> <span class="apply-chip__mark">↗</span></a>
          <?php if (!empty($c['video_url'])): ?><a class="hero-play" href="<?= Html::e($c['video_url']) ?>" rel="noopener" aria-label="Watch film"><span></span></a><?php endif; ?>
        </div>
      </div>
      <?php if (!empty($c['figure'])): ?>
        <div class="hero-figure"><img src="<?= Html::e($c['figure']) ?>" alt="" width="800" height="1000" fetchpriority="high"></div>
      <?php endif; ?>
    </div>
    <?php if ($stats): ?>
    <aside class="hero-stats">
      <?php foreach ($stats as $i => $st): ?>
        <div class="hero-stats__row">
          <strong><?= Html::e($st[0] ?? '') ?></strong>
          <span><?= Html::e($st[1] ?? '') ?></span>
          <i class="hero-stats__ico" aria-hidden="true"><svg><use href="#<?= $icons[$i] ?? 'i-star' ?>"></use></svg></i>
        </div>
      <?php endforeach; ?>
    </aside>
    <?php endif; ?>
  </div>
</section>
