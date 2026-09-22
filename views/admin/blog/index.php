<?php $canEdit = Auth::can('blog.edit'); $canDelete = Auth::can('blog.delete'); ?>
<div class="page-head">
  <div><p class="crumbs">Content</p><h1><?= !empty($trash) ? 'Blog — Trash' : 'Blog' ?></h1></div>
  <div class="toolbar">
    <a class="btn-ghost" href="/admin/blog/<?= !empty($trash) ? '' : '?trash=1' ?>"><?= !empty($trash) ? 'Back' : 'Trash' ?></a>
    <?php if (Auth::can('blog.create')): ?><a class="btn" href="/admin/blog/new/">New post</a><?php endif; ?>
  </div>
</div>
<?php if ($canEdit && empty($trash)): ?>
<form class="form" method="post" action="/admin/blog/category/" style="max-width:520px;margin-bottom:16px">
  <?= Csrf::field() ?>
  <div class="row2">
    <label class="lab">New category <input name="name" required></label>
    <label class="lab">Slug <input name="slug"></label>
  </div>
  <button class="btn-ghost" type="submit">Add category</button>
</form>
<?php endif; ?>
<div class="table-wrap">
  <table>
    <thead><tr><th>Title</th><th>URL</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php if (!$rows): ?><tr><td colspan="4" class="empty"><?= !empty($trash) ? 'Trash is empty.' : 'No posts yet. Create your first article.' ?></td></tr><?php endif; ?>
    <?php foreach ($rows as $p):
      $live = '/blog/' . $p['slug'] . '/';
    ?>
      <tr>
        <td><?php if ($canEdit && empty($trash)): ?><a href="/admin/blog/<?= (int) $p['id'] ?>/"><?= Html::e($p['title']) ?></a><?php else: ?><?= Html::e($p['title']) ?><?php endif; ?></td>
        <td><a href="<?= Html::e($live) ?>" target="_blank" rel="noopener"><code><?= Html::e($live) ?></code></a></td>
        <td><span class="badge <?= $p['status'] === 'published' ? 'badge-ok' : 'badge-warn' ?>"><?= Html::e($p['status']) ?></span></td>
        <td>
          <div class="row-actions">
          <?php if (!empty($trash)): ?>
            <?php if ($canEdit): ?><form method="post" action="/admin/blog/restore/" class="act-form"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>"><button class="act" type="submit" title="Restore"><?= admin_icon('restore') ?><span>Restore</span></button></form><?php endif; ?>
            <?php if ($canDelete): ?><form method="post" action="/admin/blog/delete/" class="act-form" data-confirm="Permanently delete “<?= Html::e($p['title']) ?>”? This cannot be undone."><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>"><button class="act act--danger" type="submit" title="Delete permanently"><?= admin_icon('delete') ?><span>Delete</span></button></form><?php endif; ?>
          <?php else: ?>
            <?php if ($p['status'] === 'published'): ?>
              <a class="act" href="<?= Html::e($live) ?>" target="_blank" rel="noopener" title="View on the website"><?= admin_icon('view') ?><span>View</span></a>
            <?php endif; ?>
            <?php if ($canEdit): ?><a class="act" href="/admin/blog/<?= (int) $p['id'] ?>/" title="Edit"><?= admin_icon('edit') ?><span>Edit</span></a><?php endif; ?>
            <?php if ($canDelete): ?><form method="post" action="/admin/blog/trash/" class="act-form" data-confirm="Move “<?= Html::e($p['title']) ?>” to trash? You can restore it from Trash."><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>"><button class="act act--danger" type="submit" title="Move to trash (can be restored)"><?= admin_icon('trash') ?><span>Trash</span></button></form><?php endif; ?>
          <?php endif; ?>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
