<?php
$rows = Cpt::published('campuses');
$fallbacks = [
    '/assets/img/campus/hafeezpet.jpg',
    '/assets/img/campus/bowrampet.jpg',
    '/assets/img/campus/hayathnagar.jpg',
    '/assets/img/campus/miyapur-a.jpg',
    '/assets/img/campus/miyapur-b.jpg',
];
$bg = trim((string) ($c['bg'] ?? ''));
if ($bg !== '' && !str_starts_with($bg, 'http') && !str_starts_with($bg, '/')) {
    $bg = $asset . ltrim($bg, '/');
}
if ($bg === '') {
    $bg = $asset . 'img/banner/campus-life-bg.jpg';
}
?>
<section class="section ai-campuses" id="campuses">
  <div class="ai-campuses__bg" style="background-image:url('<?= Html::e($bg) ?>')" aria-hidden="true"></div>
  <div class="ai-campuses__shade" aria-hidden="true"></div>
  <div class="container ai-campuses__inner">
    <div class="section-head is-center">
      <div>
        <?php if (!empty($c['kicker'])): ?>
          <span class="pill"><?= Html::e($c['kicker']) ?></span>
        <?php endif; ?>
        <h2><?= Html::e($c['heading'] ?? 'Our campuses') ?></h2>
        <?php if (!empty($c['note'])): ?>
          <p class="lede"><?= Html::e($c['note']) ?></p>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($rows): ?>
      <div class="campus-grid campus-grid--ai">
        <?php foreach ($rows as $i => $r):
          $img = $r['featured_image'] ?: (string) Cpt::field($r, 'image');
          if ($img === '') {
              $img = $fallbacks[$i % count($fallbacks)];
          }
        ?>
          <article class="campus-card">
            <img src="<?= Html::e($img) ?>" alt="<?= Html::e($r['title']) ?>" loading="lazy">
            <div class="campus-card__body">
              <h3><?= Html::e($r['title']) ?></h3>
              <p><?= Html::e((string) Cpt::field($r, 'address')) ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="campus-note">Campus details will be published shortly.</p>
    <?php endif; ?>
  </div>
</section>
