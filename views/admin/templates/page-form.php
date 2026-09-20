<?php
$sections = $sections ?? [];
$registry = $registry ?? SectionRegistry::all();
$usage = $usage ?? ['post_types' => [], 'pages' => 0];
$sectionTemplates = $sectionTemplates ?? [];
$last = count($sections) - 1;
?>
<div class="page-head">
  <div>
    <p class="crumbs">Templates / Page</p>
    <h1><?= Html::e($row['name']) ?></h1>
    <p>These sections are cloned into a new page or entry. Editing them here changes what new content starts with — it does not touch content that already exists.</p>
  </div>
  <a class="btn-ghost" href="/admin/templates/">Back to templates</a>
</div>

<div class="card" style="margin-bottom:14px">
  <span class="k">Where this template is used</span>
  <?php if ($usage['post_types']): ?>
    <p style="margin:6px 0 0">
      Default for
      <?php foreach ($usage['post_types'] as $i => $pt): ?>
        <a href="/admin/post-types/<?= (int) $pt['id'] ?>/"><strong><?= Html::e($pt['name']) ?></strong></a><?= $i < count($usage['post_types']) - 1 ? ', ' : '' ?>
      <?php endforeach; ?>
      — every new entry of <?= count($usage['post_types']) === 1 ? 'that type' : 'those types' ?> starts with these sections.
    </p>
  <?php else: ?>
    <p class="hint" style="margin:6px 0 0">
      Not set as the default for any post type yet. Staff can still pick it by hand when creating a page or entry.
      Assign it from <a href="/admin/post-types/">Post types</a>.
    </p>
  <?php endif; ?>
  <?php if ($usage['pages'] > 0): ?>
    <p class="hint" style="margin:4px 0 0"><?= (int) $usage['pages'] ?> page<?= $usage['pages'] === 1 ? ' was' : 's were' ?> created from it.</p>
  <?php endif; ?>
</div>

<form id="tpl-builder" class="form wide" method="post" action="/admin/templates/pages/<?= (int) $row['id'] ?>/" data-dirty>
  <?= Csrf::field() ?>
  <button type="submit" name="action" value="save" hidden>Save</button>
  <div class="builder">
    <aside class="builder-lib">
      <h3>Add section</h3>
      <p class="hint" style="margin:0 0 10px">Same blocks as the Pages builder. New sections land at the bottom — reorder them with the arrows.</p>
      <label class="lab">Type
        <select name="add_type">
          <?php foreach ($registry as $k => $meta): ?>
            <option value="<?= Html::e($k) ?>"><?= Html::e($meta['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button class="btn" type="submit" name="action" value="add_section" style="margin-top:8px">Add</button>

      <?php if ($sectionTemplates): ?>
        <h3 style="margin-top:18px">Saved blocks</h3>
        <p class="hint" style="margin:0 0 10px">Insert a pre-filled block instead of an empty one.</p>
        <label class="lab">Block
          <select name="section_template_id">
            <option value="0">— none —</option>
            <?php foreach ($sectionTemplates as $st): ?>
              <option value="<?= (int) $st['id'] ?>"><?= Html::e($st['name']) ?> (<?= Html::e(SectionRegistry::label($st['type'])) ?>)</option>
            <?php endforeach; ?>
          </select>
        </label>
        <button class="btn-ghost" type="submit" name="action" value="add_block" style="margin-top:8px">Insert block</button>
      <?php endif; ?>
    </aside>

    <div>
      <div class="card" style="margin-bottom:12px">
        <label class="lab">Template name <input name="name" required value="<?= Html::e($row['name']) ?>"></label>
        <label class="lab">Description <input name="description" value="<?= Html::e($row['description'] ?? '') ?>"></label>
        <label class="lab">Use for
          <select name="page_type">
            <?php foreach (['standard' => 'Standard page', 'landing' => 'Landing / CPT entry', 'blog' => 'Blog / notice'] as $k => $l): ?>
              <option value="<?= $k ?>"<?= Html::selected($row['page_type'], $k) ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>

      <?php foreach ($sections as $i => $sec):
        $type = $sec['type'] ?? '';
        $content = $sec['content'] ?? [];
      ?>
        <article class="sec" data-idx="<?= $i ?>">
          <h3>
            <span><?= $i + 1 ?>. <?= Html::e($registry[$type]['label'] ?? $type) ?></span>
            <span class="toolbar">
              <button class="btn-ghost" type="submit" name="action" value="move_up" data-idx="<?= $i ?>" title="Move up" <?= $i === 0 ? 'disabled' : '' ?>>↑</button>
              <button class="btn-ghost" type="submit" name="action" value="move_down" data-idx="<?= $i ?>" title="Move down" <?= $i === $last ? 'disabled' : '' ?>>↓</button>
              <button class="btn-ghost" type="submit" name="action" value="duplicate_section" data-idx="<?= $i ?>">Duplicate</button>
              <button class="btn-danger" type="submit" name="action" value="delete_section" data-idx="<?= $i ?>">Delete</button>
            </span>
          </h3>
          <input type="hidden" name="section_idx[]" value="<?= $i ?>">
          <input type="hidden" name="section_type[]" value="<?= Html::e($type) ?>">
          <?php
          $fields = $registry[$type]['fields'] ?? [];
          $prefix = 't' . $i . '_';
          require ROOT . '/views/admin/partials/block-fields.php';
          ?>
        </article>
      <?php endforeach; ?>
      <?php if (!$sections): ?><div class="empty">No sections yet. Add one from the left — Hero, FAQ, Form Block, Custom HTML, and the rest of the page-builder blocks.</div><?php endif; ?>

      <input type="hidden" name="action_idx" id="action-idx" value="0">
      <div class="toolbar" style="margin-top:12px">
        <button class="btn" type="submit" name="action" value="save">Save template</button>
        <button class="btn-ghost" type="submit" name="action" value="save_close">Save and close</button>
      </div>
    </div>
  </div>
</form>
<script>
(function () {
  document.querySelectorAll('#tpl-builder [data-idx]').forEach(function (el) {
    el.addEventListener('click', function () {
      var idx = el.getAttribute('data-idx');
      if (idx !== null) document.getElementById('action-idx').value = idx;
    });
  });
})();
</script>
