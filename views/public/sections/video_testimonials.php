<?php
/**
 * Video testimonials — three designs off one content set.
 *
 * Content lines: Name|Role|YouTube URL or ID|Quote
 * The design is chosen per section instance in the admin (_variant).
 */
$variant = SectionRegistry::variant('video_testimonials', $c ?? []);
$items = [];
foreach (Html::lines((string) ($c['items'] ?? '')) as $parts) {
    $id = Html::youtubeId((string) ($parts[2] ?? ''));
    if ($id === '') {
        continue;
    }
    $items[] = [
        'name' => $parts[0] ?? '',
        'role' => $parts[1] ?? '',
        'id' => $id,
        'thumb' => Html::youtubeThumb($id),
        'quote' => $parts[3] ?? '',
    ];
}
if (!$items) {
    return;
}
$lead = array_shift($items);
?>
<section class="section vtest is-<?= Html::e($variant) ?>">
  <div class="container">
    <?php if (!empty($c['kicker']) || !empty($c['heading']) || !empty($c['lede'])): ?>
      <div class="section-head is-center">
        <div>
          <?php if (!empty($c['kicker'])): ?><span class="pill"><?= Html::e($c['kicker']) ?></span><?php endif; ?>
          <?php if (!empty($c['heading'])): ?><h2><?= Html::e($c['heading']) ?></h2><?php endif; ?>
          <?php if (!empty($c['lede'])): ?><p class="lede"><?= Html::e($c['lede']) ?></p><?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($variant === 'spotlight'): ?>
      <div class="vtest__spotlight">
        <figure class="vtest-card vtest-card--lead">
          <a class="vtest-card__frame" href="https://www.youtube.com/watch?v=<?= Html::e($lead['id']) ?>" target="_blank" rel="noopener">
            <img src="<?= Html::e($lead['thumb']) ?>" alt="<?= Html::e($lead['name']) ?>" loading="lazy">
            <span class="vtest-card__play" aria-hidden="true"></span>
          </a>
          <figcaption>
            <?php if ($lead['quote'] !== ''): ?><blockquote><?= Html::e($lead['quote']) ?></blockquote><?php endif; ?>
            <strong><?= Html::e($lead['name']) ?></strong>
            <?php if ($lead['role'] !== ''): ?><small><?= Html::e($lead['role']) ?></small><?php endif; ?>
          </figcaption>
        </figure>
        <?php if ($items): ?>
          <ul class="vtest__rail">
            <?php foreach ($items as $v): ?>
              <li>
                <a class="vtest-card__frame" href="https://www.youtube.com/watch?v=<?= Html::e($v['id']) ?>" target="_blank" rel="noopener">
                  <img src="<?= Html::e($v['thumb']) ?>" alt="<?= Html::e($v['name']) ?>" loading="lazy">
                  <span class="vtest-card__play" aria-hidden="true"></span>
                </a>
                <strong><?= Html::e($v['name']) ?></strong>
                <?php if ($v['role'] !== ''): ?><small><?= Html::e($v['role']) ?></small><?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <?php $all = array_merge([$lead], $items); ?>
      <ul class="vtest__grid">
        <?php foreach ($all as $v): ?>
          <li>
            <figure class="vtest-card">
              <a class="vtest-card__frame" href="https://www.youtube.com/watch?v=<?= Html::e($v['id']) ?>" target="_blank" rel="noopener">
                <img src="<?= Html::e($v['thumb']) ?>" alt="<?= Html::e($v['name']) ?>" loading="lazy">
                <span class="vtest-card__play" aria-hidden="true"></span>
              </a>
              <figcaption>
                <?php if ($v['quote'] !== ''): ?><blockquote><?= Html::e($v['quote']) ?></blockquote><?php endif; ?>
                <strong><?= Html::e($v['name']) ?></strong>
                <?php if ($v['role'] !== ''): ?><small><?= Html::e($v['role']) ?></small><?php endif; ?>
              </figcaption>
            </figure>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>
