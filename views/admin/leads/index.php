<div class="page-head">
  <div><p class="crumbs">Engage</p><h1>Leads</h1></div>
  <a class="btn-ghost" href="/admin/leads/export/">Export CSV</a>
</div>
<?php $fid = Request::int('form'); $fname = $fid > 0 ? (Database::one('SELECT name FROM forms WHERE id = ?', [$fid])['name'] ?? 'a form that no longer exists') : ''; ?>
<?php if ($fid > 0): ?>
  <p class="muted" style="margin:0 0 10px">Showing leads from <strong><?= Html::e($fname) ?></strong> · <a href="/admin/leads/">Show all leads</a></p>
<?php endif; ?>
<form class="toolbar" method="get" style="margin-bottom:14px">
  <?php if (Request::int('form') > 0): ?><input type="hidden" name="form" value="<?= Request::int('form') ?>"><?php endif; ?>
  <input type="search" name="q" value="<?= Html::e($q ?? '') ?>" placeholder="Search payload">
  <select name="status">
    <option value="">All</option>
    <?php foreach (['new','contacted','closed'] as $st): ?>
      <option value="<?= $st ?>"<?= Html::selected($status ?? '', $st) ?>><?= $st ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn-ghost" type="submit">Filter</button>
</form>
<div class="table-wrap">
  <table>
    <thead><tr><th>Form</th><th>Status</th><th>When</th><th></th></tr></thead>
    <tbody>
    <?php if (!$rows): ?><tr><td colspan="4" class="empty">No leads yet.</td></tr><?php endif; ?>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= Html::e($r['form_name']) ?></td>
        <td><span class="badge <?= $r['status'] === 'new' ? 'badge-warn' : 'badge-ok' ?>"><?= Html::e($r['status']) ?></span></td>
        <td><?= Html::e($r['created_at']) ?></td>
        <td><div class="row-actions"><a class="act" href="/admin/leads/<?= (int) $r['id'] ?>/" title="Open this lead"><?= admin_icon('view') ?><span>Open</span></a></div></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
