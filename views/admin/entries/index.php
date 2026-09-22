<?php
$inTrash = !empty($trash);
$base = '/admin/content/' . Html::e($type['slug']) . '/';
// Only offer what this user may do; the routes enforce the same permissions.
$can = [
    'create' => Auth::can('entries.create'),
    'edit' => Auth::can('entries.edit'),
    'publish' => Auth::can('entries.publish'),
    'delete' => Auth::can('entries.delete'),
];
$bulkOptions = $inTrash
    ? array_filter(['restore' => $can['edit'] ? 'Restore as draft' : null, 'delete' => $can['delete'] ? 'Delete permanently' : null])
    : array_filter([
        'publish' => $can['publish'] ? 'Publish' : null,
        'unpublish' => $can['publish'] ? 'Hide (unpublish)' : null,
        'trash' => $can['delete'] ? 'Move to trash' : null,
    ]);
// Entries some menu links to: trashing or hiding them removes that link from the site.
$inMenu = array_flip(array_map('intval', array_column(
    Database::all('SELECT DISTINCT object_id FROM menu_items WHERE link_type = "cpt_entry" AND object_id IS NOT NULL'),
    'object_id'
)));
$name = static fn (array $r): string => trim((string) $r['title']) !== '' ? (string) $r['title'] : 'this ' . strtolower((string) $type['singular_name']);
$cols = $bulkOptions ? 5 : 4;
?>
<div class="page-head">
  <div>
    <p class="crumbs">Content / <?= Html::e($type['name']) ?></p>
    <h1><?= Html::e($type['name']) ?><?= $inTrash ? ' — Trash' : '' ?></h1>
    <p><?php if ((int) $type['public']): ?>Archive: <a href="<?= Html::e(Cpt::archiveUrl($type)) ?>" target="_blank"><?= Html::e(Cpt::archiveUrl($type)) ?></a><?php else: ?>Internal data (no public archive)<?php endif; ?></p>
  </div>
  <div class="toolbar">
    <?php if (Auth::can('post_types.view')): ?>
      <a class="btn-ghost" href="/admin/post-types/<?= (int) $type['id'] ?>/">Edit type</a>
    <?php endif; ?>
    <a class="btn-ghost" href="<?= $base ?><?= $inTrash ? '' : '?trash=1' ?>"><?= $inTrash ? 'Back' : 'Trash' ?></a>
    <?php if ($can['create']): ?>
      <a class="btn" href="<?= $base ?>new/">Add <?= Html::e($type['singular_name']) ?></a>
    <?php endif; ?>
  </div>
</div>
<form method="post" action="<?= $base ?>bulk/" id="bulk-form">
<?= Csrf::field() ?>
<?php if ($inTrash): ?><input type="hidden" name="from" value="trash"><?php endif; ?>
<?php if ($bulkOptions): ?>
<div class="bulk-bar" data-bulk-bar hidden>
  <span><strong data-bulk-count>0</strong> selected</span>
  <select name="bulk_action" required>
    <option value="">Choose an action…</option>
    <?php foreach ($bulkOptions as $v => $l): ?>
      <option value="<?= $v ?>"><?= $l ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn" type="submit" data-bulk-go>Apply</button>
</div>
<?php endif; ?>
<div class="table-wrap">
  <table>
    <thead><tr>
      <?php if ($bulkOptions): ?><th class="col-check"><input type="checkbox" data-bulk-all aria-label="Select all"></th><?php endif; ?>
      <th>Title</th><th>URL</th><th>Status</th><th></th>
    </tr></thead>
    <tbody>
    <?php if (!$rows): ?><tr><td colspan="<?= $cols ?>" class="empty"><?= $inTrash ? 'Trash is empty.' : 'No entries yet.' ?></td></tr><?php endif; ?>
    <?php foreach ($rows as $r):
      $rid = (int) $r['id'];
      $live = (int) $type['public'] ? Cpt::permalink($r, $type) : '';
      $published = $r['status'] === 'published';
      $scheduled = $r['status'] === 'scheduled';
    ?>
      <tr>
        <?php if ($bulkOptions): ?><td class="col-check"><input type="checkbox" name="ids[]" value="<?= $rid ?>" data-bulk-row aria-label="Select <?= Html::e($name($r)) ?>"></td><?php endif; ?>
        <td>
          <?php if ($can['edit'] && !$inTrash): ?><a href="<?= $base ?><?= $rid ?>/"><?= Html::e($name($r)) ?></a><?php else: ?><?= Html::e($name($r)) ?><?php endif; ?>
          <?php if (isset($inMenu[$rid])): ?><span class="badge badge-off" title="A menu links to this entry">In menu</span><?php endif; ?>
        </td>
        <td><?php if ($live): ?><a href="<?= Html::e($live) ?>" target="_blank" rel="noopener"><code><?= Html::e($live) ?></code></a><?php else: ?><code>—</code><?php endif; ?></td>
        <td><span class="badge <?= $published ? 'badge-ok' : 'badge-warn' ?>"><?= Html::e($r['status'] === 'unpublished' ? 'hidden' : $r['status']) ?></span></td>
        <td>
          <div class="row-actions">
            <?php if ($inTrash): ?>
              <?php if ($can['edit']): ?>
                <button class="act" type="submit" form="row-restore-<?= $rid ?>" title="Restore as a draft"><?= admin_icon('restore') ?><span>Restore</span></button>
              <?php endif; ?>
              <?php if ($can['delete']): ?>
                <button class="act act--danger" type="submit" form="row-delete-<?= $rid ?>" title="Delete permanently"><?= admin_icon('delete') ?><span>Delete</span></button>
              <?php endif; ?>
            <?php else: ?>
              <?php if ($live && $published): ?>
                <a class="act" href="<?= Html::e($live) ?>" target="_blank" rel="noopener" title="View on the website"><?= admin_icon('view') ?><span>View</span></a>
              <?php endif; ?>
              <?php if ($can['edit']): ?>
                <a class="act" href="<?= $base ?><?= $rid ?>/" title="Edit"><?= admin_icon('edit') ?><span>Edit</span></a>
              <?php endif; ?>
              <?php if ($can['publish']): ?>
                <?php if ($published): ?>
                  <button class="act" type="submit" form="row-hide-<?= $rid ?>" title="Unpublish — take it off the website, keep it here"><?= admin_icon('hide') ?><span>Hide</span></button>
                <?php else: ?>
                  <button class="act act--ok" type="submit" form="row-show-<?= $rid ?>" title="<?= $scheduled ? 'Publish now instead of at the scheduled time' : 'Publish — show it on the website' ?>"><?= admin_icon('publish') ?><span><?= $scheduled ? 'Publish now' : 'Publish' ?></span></button>
                <?php endif; ?>
              <?php endif; ?>
              <?php if ($can['delete']): ?>
                <button class="act act--danger" type="submit" form="row-trash-<?= $rid ?>" title="Move to trash (can be restored)"><?= admin_icon('trash') ?><span>Trash</span></button>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</form>
<?php
// Row buttons live inside #bulk-form, and forms cannot nest, so each row's
// actions post through these small forms via the button's form="" attribute.
$one = static function (string $id, int $rid, string $action, string $confirm = '') use ($base, $inTrash): void { ?>
  <form id="<?= $id ?>" method="post" action="<?= $base ?>bulk/" hidden<?= $confirm !== '' ? ' data-confirm="' . Html::e($confirm) . '"' : '' ?>>
    <?= Csrf::field() ?>
    <input type="hidden" name="ids[]" value="<?= $rid ?>">
    <input type="hidden" name="bulk_action" value="<?= $action ?>">
    <?php if ($inTrash): ?><input type="hidden" name="from" value="trash"><?php endif; ?>
  </form>
<?php };
foreach ($rows as $r):
  $rid = (int) $r['id'];
  $label = '“' . $name($r) . '”';
  $menuNote = isset($inMenu[$rid]) ? ' A menu links to it — that link will disappear from the site until it is published again.' : '';
  if ($inTrash) {
      if ($can['edit']) {
          $one('row-restore-' . $rid, $rid, 'restore');
      }
      if ($can['delete']) {
          $one('row-delete-' . $rid, $rid, 'delete', 'Permanently delete ' . $label . '? This cannot be undone.');
      }
      continue;
  }
  if ($can['publish']) {
      $one('row-hide-' . $rid, $rid, 'unpublish', $menuNote !== '' ? 'Hide ' . $label . '?' . $menuNote : '');
      $one('row-show-' . $rid, $rid, 'publish');
  }
  if ($can['delete']): ?>
    <form id="row-trash-<?= $rid ?>" method="post" action="<?= $base ?>trash/" hidden data-confirm="<?= Html::e('Move ' . $label . ' to trash? You can restore it from Trash.' . $menuNote) ?>">
      <?= Csrf::field() ?><input type="hidden" name="id" value="<?= $rid ?>">
      <?php if ($menuNote !== ''): ?><input type="hidden" name="confirm_links" value="1"><?php endif; ?>
    </form>
  <?php endif;
endforeach;
?>
