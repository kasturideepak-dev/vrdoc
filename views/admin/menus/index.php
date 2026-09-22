<?php
$tree = $tree ?? [];
$targets = $targets ?? [];
$current = null;
foreach ($menus as $m) {
    if ((int) $m['id'] === (int) $menuId) {
        $current = $m;
    }
}
$typeLabel = [
    'custom' => 'Custom', 'page' => 'Page', 'cpt_archive' => 'Archive',
    'cpt_entry' => 'Entry', 'blog_index' => 'Blog', 'blog_post' => 'Blog post',
];
$targetOf = static function (array $it): string {
    $lt = (string) ($it['link_type'] ?? 'custom');
    if ($lt === 'custom' || $lt === '') {
        return 'custom';
    }
    return $lt . ':' . (int) ($it['object_id'] ?? 0);
};
$topLevel = array_values(array_filter($tree, static fn ($t) => empty($t['_orphan'])));

/** One menu row: collapsed summary, expands to edit. */
$renderRow = static function (array $it, bool $isChild) use ($targets, $typeLabel, $targetOf, $topLevel): void {
    $iid = (int) $it['id'];
    $tgt = $targetOf($it);
    $active = (int) $it['is_active'] === 1;
    $hasChildren = !empty($it['_children']);
    ?>
    <details class="menu-row<?= $isChild ? ' is-child' : '' ?><?= $active ? '' : ' is-off' ?>" data-menu-row>
      <summary class="menu-row__head">
        <span class="menu-row__grip" draggable="true" title="Drag to reorder" aria-hidden="true">⋮⋮</span>
        <span class="menu-row__main">
          <strong data-row-label><?= Html::e($it['label']) ?></strong>
          <small><span class="badge badge-off"><?= Html::e($typeLabel[$it['link_type']] ?? 'Custom') ?></span> <code data-row-url><?= Html::e($it['_url'] ?? $it['url']) ?></code></small>
        </span>
        <span class="menu-row__flags">
          <?php if ($hasChildren): ?><span class="badge badge-off"><?= count($it['_children']) ?> in dropdown</span><?php endif; ?>
          <?php if (!$active): ?><span class="badge badge-warn">Hidden</span><?php elseif (!empty($it['_dead'])): ?><span class="badge badge-err" title="<?= Html::e($it['_dead']) ?>">Not on site</span><?php endif; ?>
          <?php if (!empty($it['_orphan'])): ?><span class="badge badge-err" title="Its parent item was removed">Orphaned</span><?php endif; ?>
        </span>
        <span class="menu-row__chev" aria-hidden="true"></span>
      </summary>

      <div class="menu-row__body">
        <?php if (!empty($it['_dead'])): ?>
          <p class="menu-row__warn">Not shown on the site: <?= Html::e($it['_dead']) ?>.</p>
        <?php endif; ?>
        <input type="hidden" name="item_id[]" value="<?= $iid ?>">
        <div class="field-grid">
          <label class="lab is-half">Label
            <input name="label[]" value="<?= Html::e($it['label']) ?>" data-label-input>
          </label>
          <label class="lab is-half">Links to
            <select name="target[]" data-target-select>
              <?php foreach ($targets as $group => $opts): ?>
                <optgroup label="<?= Html::e($group) ?>">
                  <?php foreach ($opts as $val => $name): ?>
                    <option value="<?= Html::e($val) ?>"<?= Html::selected($tgt, $val) ?>><?= Html::e($name) ?></option>
                  <?php endforeach; ?>
                </optgroup>
              <?php endforeach; ?>
              <?php if ($tgt !== 'custom' && !array_filter($targets, static fn ($o) => isset($o[$tgt]))): ?>
                <?php /* Keep the link to its item while that item is unpublished or its
                   archive is off, so saving the menu does not turn it into a custom URL. */ ?>
                <optgroup label="Current link">
                  <option value="<?= Html::e($tgt) ?>" selected><?= Html::e($it['label']) ?> — not on site right now</option>
                </optgroup>
              <?php endif; ?>
            </select>
          </label>
          <label class="lab is-half" data-custom-url <?= $tgt === 'custom' ? '' : 'hidden' ?>>URL
            <input name="url[]" value="<?= Html::e($it['url']) ?>" placeholder="/about/ or https://…">
          </label>
          <label class="lab is-half">Show in
            <select name="parent_id[]">
              <option value="0">Main menu (top level)</option>
              <?php foreach ($topLevel as $p): if ((int) $p['id'] === $iid) { continue; } ?>
                <option value="<?= (int) $p['id'] ?>"<?= Html::selected((int) ($it['parent_id'] ?? 0), (int) $p['id']) ?> <?= $hasChildren ? 'disabled' : '' ?>>
                  Dropdown under “<?= Html::e($p['label']) ?>”
                </option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>
        <div class="menu-row__foot">
          <label class="menu-row__vis"><input type="checkbox" name="is_active[<?= $iid ?>]" <?= Html::checked($active) ?>> Visible on the site</label>
          <button class="btn-danger" type="submit" form="del-item-<?= $iid ?>">Remove</button>
        </div>
      </div>
    </details>
    <?php
};
?>
<div class="page-head">
  <div>
    <p class="crumbs">Engage</p>
    <h1>Menus</h1>
    <p>Links follow pages and entries automatically — rename a page and the menu keeps pointing at it.</p>
  </div>
</div>

<nav class="menu-tabs" aria-label="Choose a menu">
  <?php foreach ($menus as $m): ?>
    <a class="menu-tabs__tab<?= (int) $m['id'] === (int) $menuId ? ' is-on' : '' ?>" href="/admin/menus/?id=<?= (int) $m['id'] ?>"><?= Html::e($m['name']) ?></a>
  <?php endforeach; ?>
</nav>

<form class="form menu-shell" method="post" id="menu-form">
  <?= Csrf::field() ?>
  <input type="hidden" name="menu_id" value="<?= (int) $menuId ?>">

  <section class="panel">
    <header class="panel-head panel-head--row">
      <div>
        <h2><?= Html::e($current['name'] ?? 'Menu') ?> menu</h2>
        <p><?= (int) ($itemCount ?? 0) ?> link<?= ($itemCount ?? 0) === 1 ? '' : 's' ?> · drag ⋮⋮ to reorder · click a link to edit it<?= ($current['slug'] ?? '') === 'footer' ? ' · the footer shows top-level links only' : '' ?></p>
      </div>
      <button class="btn" type="submit">Save menu</button>
    </header>
    <div class="panel-body">
      <?php if (!$tree): ?>
        <div class="empty">This menu is empty — add the first link below.</div>
      <?php endif; ?>
      <div class="menu-tree" data-menu-list>
        <?php foreach ($tree as $it): ?>
          <div class="menu-group" data-menu-group>
            <?php $renderRow($it, false); ?>
            <?php if (!empty($it['_children'])): ?>
              <div class="menu-children" data-menu-list>
                <?php foreach ($it['_children'] as $ch): ?>
                  <div class="menu-group" data-menu-group><?php $renderRow($ch, true); ?></div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="panel">
    <header class="panel-head">
      <h2>Add a link</h2>
      <p>Pick a page or entry so the link survives renames, or use a custom URL.</p>
    </header>
    <div class="panel-body">
      <div class="field-grid">
        <label class="lab is-third">Label
          <input name="new_label" placeholder="e.g. Admissions">
        </label>
        <label class="lab is-third">Links to
          <select name="new_target" data-target-select>
            <?php foreach ($targets as $group => $opts): ?>
              <optgroup label="<?= Html::e($group) ?>">
                <?php foreach ($opts as $val => $name): ?>
                  <option value="<?= Html::e($val) ?>"><?= Html::e($name) ?></option>
                <?php endforeach; ?>
              </optgroup>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="lab is-third">Show in
          <select name="new_parent">
            <option value="0">Main menu (top level)</option>
            <?php foreach ($topLevel as $p): ?>
              <option value="<?= (int) $p['id'] ?>">Dropdown under “<?= Html::e($p['label']) ?>”</option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="lab is-full" data-custom-url>URL
          <input name="new_url" placeholder="/about/ or https://…">
        </label>
      </div>
      <div class="toolbar"><button class="btn" type="submit">Add link</button></div>
    </div>
  </section>
</form>

<?php foreach ($tree as $it): ?>
  <?php foreach (array_merge([$it], $it['_children'] ?? []) as $row): ?>
    <form id="del-item-<?= (int) $row['id'] ?>" method="post" action="/admin/menus/item-delete/" data-confirm="Remove “<?= Html::e($row['label']) ?>” from the menu?<?= !empty($row['_children']) ? ' Its dropdown links move up to the main menu.' : '' ?>" hidden>
      <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
    </form>
  <?php endforeach; ?>
<?php endforeach; ?>
