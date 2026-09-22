<?php
/**
 * Branches — three designs off one content set.
 *
 * Pulls published entries from a post type (default "campuses"), or falls back
 * to manually listed lines: Name|Address|Phone|Image
 */
$variant = SectionRegistry::variant('branches', $c ?? []);
$rows = [];

foreach (Html::lines((string) ($c['items'] ?? '')) as $parts) {
    if (trim((string) ($parts[0] ?? '')) === '') {
        continue;
    }
    $rows[] = [
        'name' => $parts[0],
        'address' => $parts[1] ?? '',
        'phone' => $parts[2] ?? '',
        'image' => $parts[3] ?? '',
        'url' => '',
    ];
}

if (!$rows) {
    $source = trim((string) ($c['source'] ?? '')) ?: 'campuses';
    foreach (Cpt::published($source) as $e) {
        $rows[] = [
            'name' => $e['title'],
            'address' => $e['excerpt'] ?? '',
            'phone' => (string) Cpt::field($e, 'phone', ''),
            'image' => $e['featured_image'] ?? '',
            'url' => Cpt::permalink($e),
        ];
    }
}
if (!$rows) {
    return;
}
$img = static function (string $p) use ($asset): string {
    $p = trim($p);
    if ($p === '') {
        return '';
    }
    return (str_starts_with($p, 'http') || str_starts_with($p, '/')) ? $p : $asset . ltrim($p, '/');
};
$lead = $rows[0];
?>
<section class="section branches is-<?= Html::e($variant) ?>" id="branches">
  <div class="container">
    <?php if (!empty($c['kicker']) || !empty($c['heading']) || !empty($c['note'])): ?>
      <div class="section-head is-center">
        <div>
          <?php if (!empty($c['kicker'])): ?><span class="pill"><?= Html::e($c['kicker']) ?></span><?php endif; ?>
          <?php if (!empty($c['heading'])): ?><h2><?= Html::e($c['heading']) ?></h2><?php endif; ?>
          <?php if (!empty($c['note'])): ?><p class="lede"><?= Html::e($c['note']) ?></p><?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($variant === 'list'): ?>
      <ul class="branches__list">
        <?php foreach ($rows as $b): ?>
          <li class="branch-row">
            <div class="branch-row__main">
              <strong><?= Html::e($b['name']) ?></strong>
              <?php if ($b['address'] !== ''): ?><span><?= Html::e($b['address']) ?></span><?php endif; ?>
            </div>
            <div class="branch-row__side">
              <?php if ($b['phone'] !== ''): ?>
                <a href="tel:<?= Html::e(preg_replace('/\D+/', '', $b['phone'])) ?>"><?= Html::e($b['phone']) ?></a>
              <?php endif; ?>
              <?php if ($b['url'] !== '' && $b['url'] !== '#'): ?>
                <a class="branch-row__link" href="<?= Html::e($b['url']) ?>">Details</a>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>

    <?php elseif ($variant === 'split'): ?>
      <div class="branches__split">
        <article class="branch-feature">
          <?php $li = $img($lead['image']); if ($li !== ''): ?>
            <img src="<?= Html::e($li) ?>" alt="<?= Html::e($lead['name']) ?>" loading="lazy">
          <?php endif; ?>
          <div class="branch-feature__copy">
            <strong><?= Html::e($lead['name']) ?></strong>
            <?php if ($lead['address'] !== ''): ?><p><?= Html::e($lead['address']) ?></p><?php endif; ?>
            <?php if ($lead['phone'] !== ''): ?>
              <a href="tel:<?= Html::e(preg_replace('/\D+/', '', $lead['phone'])) ?>"><?= Html::e($lead['phone']) ?></a>
            <?php endif; ?>
          </div>
        </article>
        <ul class="branches__list">
          <?php foreach (array_slice($rows, 1) as $b): ?>
            <li class="branch-row">
              <div class="branch-row__main">
                <strong><?= Html::e($b['name']) ?></strong>
                <?php if ($b['address'] !== ''): ?><span><?= Html::e($b['address']) ?></span><?php endif; ?>
              </div>
              <?php if ($b['phone'] !== ''): ?>
                <div class="branch-row__side">
                  <a href="tel:<?= Html::e(preg_replace('/\D+/', '', $b['phone'])) ?>"><?= Html::e($b['phone']) ?></a>
                </div>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

    <?php else: ?>
      <ul class="branches__cards">
        <?php foreach ($rows as $b): $bi = $img($b['image']); ?>
          <li>
            <article class="branch-card">
              <?php if ($bi !== ''): ?>
                <span class="branch-card__shot"><img src="<?= Html::e($bi) ?>" alt="<?= Html::e($b['name']) ?>" loading="lazy"></span>
              <?php endif; ?>
              <div class="branch-card__copy">
                <strong><?= Html::e($b['name']) ?></strong>
                <?php if ($b['address'] !== ''): ?><p><?= Html::e($b['address']) ?></p><?php endif; ?>
                <?php if ($b['phone'] !== ''): ?>
                  <a href="tel:<?= Html::e(preg_replace('/\D+/', '', $b['phone'])) ?>"><?= Html::e($b['phone']) ?></a>
                <?php endif; ?>
              </div>
            </article>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>
