<div class="page-head">
  <div>
    <p class="crumbs">Content / <?= Html::e($type['name']) ?></p>
    <h1><?= Html::e($type['name']) ?></h1>
    <p><?php if ((int) $type['public']): ?>Archive: <a href="<?= Html::e(Cpt::archiveUrl($type)) ?>" target="_blank"><?= Html::e(Cpt::archiveUrl($type)) ?></a><?php else: ?>Internal data (no public archive)<?php endif; ?></p>
  </div>
  <div class="toolbar">
    <a class="btn-ghost" href="/admin/post-types/<?= (int) $type['id'] ?>/">Edit type</a>
    <a class="btn-ghost" href="/admin/content/<?= Html::e($type['slug']) ?>/<?= !empty($trash) ? '' : '?trash=1' ?>"><?= !empty($trash) ? 'Back' : 'Trash' ?></a>
    <a class="btn" href="/admin/content/<?= Html::e($type['slug']) ?>/new/">Add <?= Html::e($type['singular_name']) ?></a>
  </div>
</div>
<form method="post" action="/admin/content/<?= Html::e($type['slug']) ?>/bulk/" id="bulk-form">
<?= Csrf::field() ?>
<div class="bulk-bar" data-bulk-bar hidden>
  <span><strong data-bulk-count>0</strong> selected</span>
  <select name="bulk_action" required>
    <option value="">Choose an action…</option>
    <?php if (!empty($trash)): ?>
      <option value="restore">Restore as draft</option>
      <option value="delete">Delete permanently</option>
    <?php else: ?>
      <option value="publish">Publish</option>
      <option value="unpublish">Unpublish</option>
      <option value="trash">Move to trash</option>
    <?php endif; ?>
  </select>
  <button class="btn" type="submit" data-bulk-go>Apply</button>
</div>
<div class="table-wrap">
  <table>
    <thead><tr>
      <th class="col-check"><input type="checkbox" data-bulk-all aria-label="Select all"></th>
      <th>Title</th><th>URL</th><th>Status</th><th></th>
    </tr></thead>
    <tbody>
    <?php if (!$rows): ?><tr><td colspan="5" class="empty">No entries yet.</td></tr><?php endif; ?>
    <?php foreach ($rows as $r):
      $live = (int) $type['public'] ? Cpt::permalink($r, $type) : '';
    ?>
      <tr>
        <td class="col-check"><input type="checkbox" name="ids[]" value="<?= (int) $r['id'] ?>" data-bulk-row aria-label="Select <?= Html::e($r['title']) ?>"></td>
        <td><a href="/admin/content/<?= Html::e($type['slug']) ?>/<?= (int) $r['id'] ?>/"><?= Html::e($r['title']) ?></a></td>
        <td><?php if ($live): ?><a href="<?= Html::e($live) ?>" target="_blank" rel="noopener"><code><?= Html::e($live) ?></code></a><?php else: ?><code>—</code><?php endif; ?></td>
        <td><span class="badge <?= $r['status'] === 'published' ? 'badge-ok' : 'badge-warn' ?>"><?= Html::e($r['status']) ?></span></td>
        <td>
          <?php if (!empty($trash)): ?>
            <button class="btn-ghost" type="submit" form="restore-<?= (int) $r['id'] ?>">Restore</button>
          <?php else: ?>
            <div class="toolbar">
              <?php if ($live && $r['status'] === 'published'): ?>
                <a class="btn-ghost" href="<?= Html::e($live) ?>" target="_blank" rel="noopener">View</a>
              <?php endif; ?>
              <a class="btn-ghost" href="/admin/content/<?= Html::e($type['slug']) ?>/<?= (int) $r['id'] ?>/">Edit</a>
            </div>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</form>
<?php if (!empty($trash)): ?>
  <?php foreach ($rows as $r): ?>
    <form id="restore-<?= (int) $r['id'] ?>" method="post" action="/admin/content/<?= Html::e($type['slug']) ?>/restore/" hidden>
      <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
    </form>
  <?php endforeach; ?>
<?php endif; ?>
