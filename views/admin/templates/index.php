<?php
$pages = $pages ?? [];
$sections = $sections ?? [];
$postTypes = $postTypes ?? [];
$unmapped = [];
foreach ($postTypes as $pt) {
    if ((int) ($pt['default_template_id'] ?? 0) === 0) {
        $unmapped[] = $pt;
    }
}
?>
<div class="page-head">
  <div>
    <p class="crumbs">System</p>
    <h1>Templates</h1>
    <p>A template is an ordered list of sections. Assign one to a post type and every new entry of that type starts with those sections, ready to fill in.</p>
  </div>
</div>

<?php if ($unmapped): ?>
  <div class="card" style="margin-bottom:16px">
    <span class="k">Unmapped post types</span>
    <p style="margin:6px 0 0">
      <?php foreach ($unmapped as $i => $pt): ?>
        <a href="/admin/post-types/<?= (int) $pt['id'] ?>/"><?= Html::e($pt['name']) ?></a><?= $i < count($unmapped) - 1 ? ', ' : '' ?>
      <?php endforeach; ?>
      <?= count($unmapped) === 1 ? 'has' : 'have' ?> no default template, so new entries fall back to a generic layout.
    </p>
  </div>
<?php endif; ?>

<div class="page-head" style="margin-top:8px">
  <h2>Page templates</h2>
  <a class="btn" href="/admin/templates/pages/new/">Add new template</a>
</div>
<div class="tpl-grid">
  <?php if (!$pages): ?><div class="empty">No page templates yet.</div><?php endif; ?>
  <?php foreach ($pages as $p):
    $usage = $p['usage'] ?? ['post_types' => [], 'pages' => 0];
    $types = Templates::sectionTypesFromJson($p['sections_json'] ?? '[]');
  ?>
    <article class="card tpl-card-admin">
      <span class="k"><?= Html::e($p['page_type']) ?> · <?= (int) $p['section_count'] ?> sections</span>
      <strong><?= Html::e($p['name']) ?></strong>
      <?php if (!empty($p['is_starter'])): ?>
        <span class="badge badge-off" title="Shipped with the site — edit it freely, it is not restored automatically">Starter</span>
      <?php endif; ?>
      <p class="hint"><?= Html::e($p['description'] ?: 'No description') ?></p>
      <?php if ($types): ?>
        <p class="hint" style="margin-top:6px">
          <?= Html::e(implode(' → ', array_map([SectionRegistry::class, 'label'], array_slice($types, 0, 5)))) ?><?= count($types) > 5 ? ' → …' : '' ?>
        </p>
      <?php endif; ?>
      <p style="margin-top:8px">
        <?php if ($usage['post_types']): ?>
          <?php foreach ($usage['post_types'] as $pt): ?>
            <span class="badge badge-ok">Default for <?= Html::e($pt['name']) ?></span>
          <?php endforeach; ?>
        <?php else: ?>
          <span class="badge badge-off">Not assigned to a post type</span>
        <?php endif; ?>
        <?php if ($usage['pages'] > 0): ?>
          <span class="badge badge-off"><?= (int) $usage['pages'] ?> page<?= $usage['pages'] === 1 ? '' : 's' ?></span>
        <?php endif; ?>
      </p>
      <div class="toolbar" style="margin-top:12px">
        <a class="btn-ghost" href="/admin/templates/pages/<?= (int) $p['id'] ?>/">Edit sections</a>
        <form method="post" action="/admin/templates/pages/duplicate/" style="display:inline"><?= Csrf::field() ?>
          <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
          <button class="btn-ghost" type="submit">Duplicate</button>
        </form>
        <?php if (!$usage['post_types']): ?>
          <form method="post" action="/admin/templates/pages/delete/" data-confirm="Delete this page template?" style="display:inline"><?= Csrf::field() ?>
            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <button class="btn-danger" type="submit">Delete</button>
          </form>
        <?php endif; ?>
      </div>
    </article>
  <?php endforeach; ?>
</div>

<div class="page-head" style="margin-top:28px">
  <h2>Section templates</h2>
  <a class="btn" href="/admin/templates/sections/new/">Add new template</a>
</div>
<div class="tpl-grid">
  <?php if (!$sections): ?><div class="empty">No section templates yet. Save any block from a page builder, or add one here.</div><?php endif; ?>
  <?php foreach ($sections as $s): ?>
    <article class="card tpl-card-admin">
      <span class="k"><?= Html::e(SectionRegistry::label($s['type'])) ?></span>
      <strong><?= Html::e($s['name']) ?></strong>
      <p class="hint">Reusable <?= Html::e($s['type']) ?> block — insert it from a page builder or a page template.</p>
      <div class="toolbar" style="margin-top:12px">
        <a class="btn-ghost" href="/admin/templates/sections/<?= (int) $s['id'] ?>/">Edit</a>
        <form method="post" action="/admin/templates/sections/duplicate/" style="display:inline"><?= Csrf::field() ?>
          <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
          <button class="btn-ghost" type="submit">Duplicate</button>
        </form>
        <form method="post" action="/admin/templates/sections/delete/" data-confirm="Delete this section template?" style="display:inline"><?= Csrf::field() ?>
          <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
          <button class="btn-danger" type="submit">Delete</button>
        </form>
      </div>
    </article>
  <?php endforeach; ?>
</div>
