<?php
$can = [
    'create' => Auth::can('pages.create'), 'edit' => Auth::can('pages.edit'),
    'publish' => Auth::can('pages.publish'), 'delete' => Auth::can('pages.delete'),
];
$inMenu = array_flip(array_map('intval', array_column(
    Database::all('SELECT DISTINCT object_id FROM menu_items WHERE link_type = "page" AND object_id IS NOT NULL'),
    'object_id'
)));
$menuNote = ' A menu links to it — that link will disappear from the site until it is published again.';
?>
<div class="page-head">
  <div>
    <p class="crumbs">Content</p>
    <h1><?= !empty($trash) ? 'Pages — Trash' : 'Pages' ?></h1>
  </div>
  <div class="toolbar">
    <a class="btn-ghost" href="/admin/pages/<?= !empty($trash) ? '' : '?trash=1' ?>"><?= !empty($trash) ? 'Back to pages' : 'Trash' ?></a>
    <?php if ($can['create']): ?><a class="btn" href="/admin/pages/new/">New page</a><?php endif; ?>
  </div>
</div>
<form class="toolbar" method="get" style="margin-bottom:14px">
  <input type="search" name="q" value="<?= Html::e($q ?? '') ?>" placeholder="Filter title or slug">
  <select name="status">
    <option value="">All statuses</option>
    <?php foreach (['draft','published','scheduled','unpublished'] as $st): ?>
      <option value="<?= $st ?>"<?= Html::selected($status ?? '', $st) ?>><?= $st ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn-ghost" type="submit">Filter</button>
</form>
<div class="table-wrap">
  <table>
    <thead><tr><th>Title</th><th>URL</th><th>Status</th><th>Updated</th><th></th></tr></thead>
    <tbody>
    <?php if (!$rows): ?><tr><td colspan="5" class="empty">No pages.</td></tr><?php endif; ?>
    <?php foreach ($rows as $p):
      $live = $p['slug'] === '/' ? '/' : '/' . trim($p['slug'], '/') . '/';
    ?>
      <tr>
        <td>
          <?php if ($can['edit'] && empty($trash)): ?><a href="/admin/pages/<?= (int) $p['id'] ?>/"><?= Html::e($p['title']) ?></a><?php else: ?><?= Html::e($p['title']) ?><?php endif; ?>
          <?php if (isset($inMenu[(int) $p['id']])): ?><span class="badge badge-off" title="A menu links to this page">In menu</span><?php endif; ?>
        </td>
        <td><a href="<?= Html::e($live) ?>" target="_blank" rel="noopener"><code><?= Html::e($live) ?></code></a></td>
        <td><span class="badge <?= $p['status'] === 'published' ? 'badge-ok' : 'badge-warn' ?>"><?= Html::e($p['status'] === 'unpublished' ? 'hidden' : $p['status']) ?></span></td>
        <td><?= Html::e($p['updated_at']) ?></td>
        <td>
          <div class="row-actions">
          <?php if (!empty($trash)): ?>
            <?php if ($can['edit']): ?><form method="post" action="/admin/pages/restore/" class="act-form"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>"><button class="act" type="submit" title="Restore as a draft"><?= admin_icon('restore') ?><span>Restore</span></button></form><?php endif; ?>
            <?php if ($can['delete']): ?><form method="post" action="/admin/pages/delete/" class="act-form" data-confirm="Permanently delete “<?= Html::e($p['title']) ?>”? This cannot be undone."><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>"><button class="act act--danger" type="submit" title="Delete permanently"><?= admin_icon('delete') ?><span>Delete</span></button></form><?php endif; ?>
          <?php else: ?>
            <?php $pid = (int) $p['id']; $isHome = $p['slug'] === '/'; $note = isset($inMenu[$pid]) ? $menuNote : ''; ?>
            <?php if ($p['status'] === 'published'): ?>
              <a class="act" href="<?= Html::e($live) ?>" target="_blank" rel="noopener" title="View on the website"><?= admin_icon('view') ?><span>View</span></a>
            <?php endif; ?>
            <?php if ($can['edit']): ?><a class="act" href="/admin/pages/<?= $pid ?>/" title="Edit"><?= admin_icon('edit') ?><span>Edit</span></a><?php endif; ?>
            <?php if (!$can['publish'] || $isHome): /* the homepage stays published */ ?>
            <?php elseif ($p['status'] === 'published'): ?>
              <form method="post" action="/admin/pages/<?= (int) $p['id'] ?>/unpublish/" class="act-form" data-confirm="<?= Html::e('Hide “' . $p['title'] . '”? Visitors will get a 404 until you publish it again.' . $note) ?>"><?= Csrf::field() ?><input type="hidden" name="back" value="list"><button class="act" type="submit" title="Unpublish — take it off the website, keep it here"><?= admin_icon('hide') ?><span>Hide</span></button></form>
            <?php else: ?>
              <form method="post" action="/admin/pages/<?= (int) $p['id'] ?>/publish/" class="act-form"><?= Csrf::field() ?><input type="hidden" name="back" value="list"><button class="act act--ok" type="submit" title="<?= $p['status'] === 'scheduled' ? 'Publish now instead of at the scheduled time' : 'Publish — show it on the website' ?>"><?= admin_icon('publish') ?><span><?= $p['status'] === 'scheduled' ? 'Publish now' : 'Publish' ?></span></button></form>
            <?php endif; ?>
            <?php if ($can['delete'] && !$isHome): ?>
              <form method="post" action="/admin/pages/trash/" class="act-form" data-confirm="<?= Html::e('Move “' . $p['title'] . '” to trash? You can restore it from Trash.' . $note) ?>"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= $pid ?>"><?php if ($note !== ''): ?><input type="hidden" name="confirm_links" value="1"><?php endif; ?><button class="act act--danger" type="submit" title="Move to trash (can be restored)"><?= admin_icon('trash') ?><span>Trash</span></button></form>
            <?php endif; ?>
          <?php endif; ?>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
