<?php
/** Term archive — entries of a post type filtered to one term. */
require ROOT . '/views/public/_start.php';
$img = static function (string $p) use ($asset): string {
    $p = trim($p);
    if ($p === '') {
        return $asset . 'img/banner/campuses-hero.jpg';
    }
    return (str_starts_with($p, 'http') || str_starts_with($p, '/')) ? $p : $asset . ltrim($p, '/');
};
?>
<section class="page-hero">
  <img class="page-hero__photo" src="<?= Html::e($asset . 'img/banner/campus-life-bg.jpg') ?>" alt="">
  <div class="page-hero__shade"></div>
  <div class="container page-hero__inner">
    <div class="page-hero__copy">
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="/">Home</a><span>/</span>
        <?php if ((int) $type['has_archive']): ?>
          <a href="<?= Html::e(Cpt::archiveUrl($type)) ?>"><?= Html::e($type['name']) ?></a><span>/</span>
        <?php endif; ?>
        <span><?= Html::e($term['name']) ?></span>
      </nav>
      <p class="hero-kicker"><?= Html::e($taxonomy['singular_name'] ?: $taxonomy['name']) ?></p>
      <h1><?= Html::e($term['name']) ?></h1>
      <?php if (!empty($term['description'])): ?>
        <p class="hero-lead"><?= Html::e($term['description']) ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if (!empty($terms) && count($terms) > 1): ?>
  <section class="section section--tight">
    <div class="container">
      <ul class="term-filter">
        <?php foreach ($terms as $t): ?>
          <li>
            <a class="term-filter__chip<?= (int) $t['id'] === (int) $term['id'] ? ' is-on' : '' ?>"
               href="<?= Html::e(Taxonomy::archiveUrl($type, $taxonomy, $t)) ?>">
              <?= Html::e($t['name']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
<?php endif; ?>

<section class="section">
  <div class="container">
    <?php if (!$entries): ?>
      <p class="lede">Nothing published under <?= Html::e($term['name']) ?> yet.</p>
    <?php else: ?>
      <ul class="term-grid">
        <?php foreach ($entries as $e): ?>
          <li>
            <article class="term-card">
              <a class="term-card__shot" href="<?= Html::e(Cpt::permalink($e, $type)) ?>">
                <img src="<?= Html::e($img((string) ($e['featured_image'] ?? ''))) ?>" alt="" loading="lazy">
              </a>
              <div class="term-card__copy">
                <h2><a href="<?= Html::e(Cpt::permalink($e, $type)) ?>"><?= Html::e($e['title']) ?></a></h2>
                <?php if (!empty($e['excerpt'])): ?><p><?= Html::e($e['excerpt']) ?></p><?php endif; ?>
              </div>
            </article>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>
<?php
require ROOT . '/views/public/_end.php';
