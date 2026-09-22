<div class="page-head">
  <div><p class="crumbs">Engage</p><h1>Forms</h1></div>
  <a class="btn" href="/admin/forms/new/">New form</a>
</div>
<div class="table-wrap">
  <table>
    <thead><tr><th>Name</th><th>Slug</th><th>Fields</th><th>Leads</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><a href="/admin/forms/<?= (int) $r['id'] ?>/"><?= Html::e($r['name']) ?></a></td>
        <td><code><?= Html::e($r['slug']) ?></code></td>
        <td><?= (int) $r['field_count'] ?></td>
        <td><?= (int) $r['lead_count'] ?></td>
        <td><span class="badge <?= $r['is_active'] ? 'badge-ok' : 'badge-off' ?>"><?= $r['is_active'] ? 'active' : 'off' ?></span></td>
        <td>
          <div class="row-actions">
            <?php if (Auth::can('forms.edit')): ?><a class="act" href="/admin/forms/<?= (int) $r['id'] ?>/" title="Edit fields, recipients and settings"><?= admin_icon('edit') ?><span>Edit</span></a><?php endif; ?>
            <?php if (Auth::can('leads.view')): ?><a class="act" href="/admin/leads/?form=<?= (int) $r['id'] ?>" title="Submissions from this form"><?= admin_icon('inbox') ?><span>Leads</span></a><?php endif; ?>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
