<?php
/** Student testimonial reels — 8 YouTube shorts in a 4×2 grid with poster thumbnails. */
$rows = Html::lines($c['videos'] ?? '');
$videos = [];
$seen = [];
foreach ($rows as $row) {
    $title = $row[0] ?? '';
    $raw = $row[1] ?? ($row[0] ?? '');
    $id = Html::youtubeId($raw);
    if ($id === '' || isset($seen[$id])) {
        continue;
    }
    $seen[$id] = true;
    $videos[] = [
        'title' => $title !== $raw ? $title : '',
        'id' => $id,
        'thumb' => Html::youtubeThumb($id),
    ];
}
$videos = array_slice($videos, 0, 8);
$reelCount = count($videos);
?>
<section class="section ai-reels" id="student-reels">
  <div class="container">
    <div class="section-head is-center">
      <div>
        <?php if (!empty($c['kicker'])): ?>
          <span class="pill"><?= Html::e($c['kicker']) ?></span>
        <?php endif; ?>
        <h2><?= Html::e($c['heading'] ?? 'Student testimonials') ?></h2>
        <?php if (!empty($c['lede'])): ?>
          <p class="lede"><?= Html::e($c['lede']) ?></p>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($videos): ?>
      <div class="reels-grid<?= $reelCount <= 4 ? ' reels-grid--' . $reelCount : '' ?>">
        <?php foreach ($videos as $v): ?>
          <article class="reel-card">
            <div class="reel-card__frame" data-reel data-yt-id="<?= Html::e($v['id']) ?>">
              <button type="button" class="reel-card__poster" aria-label="Play <?= Html::e($v['title'] ?: 'student testimonial') ?>">
                <img
                  src="<?= Html::e($v['thumb']) ?>"
                  alt="<?= Html::e($v['title'] ?: 'Student testimonial') ?>"
                  loading="lazy"
                  width="480"
                  height="854"
                  onerror="if(!this.dataset.fbk){this.dataset.fbk='1';this.src='https://i.ytimg.com/vi/<?= Html::e($v['id']) ?>/mqdefault.jpg'}"
                >
                <span class="reel-card__play" aria-hidden="true"><svg><use href="#i-play"></use></svg></span>
              </button>
            </div>
            <?php if ($v['title']): ?>
              <h3 class="reel-card__title"><?= Html::e($v['title']) ?></h3>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
