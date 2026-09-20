<div class="page-head">
  <div>
    <p class="crumbs">Overview</p>
    <h1>Dashboard</h1>
    <p>Production control room for vrdoctors.in</p>
  </div>
</div>
<div class="quick">
  <a href="/admin/post-types/new/"><strong>Add new post type</strong><span>Blog, Events, Notices — fields and URLs included</span></a>
  <a href="/admin/templates/"><strong>Build a template</strong><span>Pick the sections each type starts with</span></a>
  <a href="/admin/leads/"><strong>View leads</strong><span><?= (int) $stats['leads_new'] ?> new <?= (int) $stats['leads_new'] === 1 ? 'enquiry' : 'enquiries' ?></span></a>
  <a href="<?= !empty($homeId) ? '/admin/pages/' . (int) $homeId . '/' : '/admin/pages/' ?>"><strong>Edit homepage</strong><span>Sections, copy and images</span></a>
</div>
<div class="cards">
  <div class="card"><span class="k">Pages</span><strong><?= (int) $stats['pages'] ?></strong><div class="hint"><?= (int) $stats['published'] ?> published</div></div>
  <div class="card"><span class="k">Post types</span><strong><?= (int) $stats['types'] ?></strong><div class="hint"><?= (int) $stats['entries'] ?> entries</div></div>
  <div class="card"><span class="k">Leads this month</span><strong><?= (int) $stats['leads_month'] ?></strong><div class="hint"><?= (int) $stats['leads_new'] ?> still new</div></div>
  <div class="card"><span class="k">Blog posts</span><strong><?= (int) $stats['posts'] ?></strong><div class="hint"><?= (int) $stats['media'] ?> media files</div></div>
</div>
<div class="page-head" style="margin-top:22px">
  <div>
    <h2>Content types</h2>
    <p>What each type publishes, and the template its new entries start from.</p>
  </div>
  <a class="btn-ghost" href="/admin/post-types/">Manage post types</a>
</div>
<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Type</th><th>Archive URL</th><th>Entries</th><th>Default template</th><th>Sections</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (empty($contentMap)): ?>
        <tr><td colspan="6" class="empty">No post types yet. <a href="/admin/post-types/new/">Create one</a> to get started.</td></tr>
      <?php endif; ?>
      <?php foreach ($contentMap as $m):
        $t = $m['type'];
        $tpl = $m['template'];
      ?>
        <tr>
          <td><a href="/admin/content/<?= Html::e($t['slug']) ?>/"><?= Html::e($t['name']) ?></a></td>
          <td>
            <?php if ((int) $t['public'] && (int) $t['has_archive']): ?>
              <code>/<?= Html::e($t['slug']) ?>/</code>
            <?php else: ?>
              <span class="badge badge-off">Not public</span>
            <?php endif; ?>
          </td>
          <td><?= (int) $t['entry_count'] ?> <small style="color:var(--muted)">(<?= (int) $t['published_count'] ?> live)</small></td>
          <td>
            <?php if ($tpl): ?>
              <a href="/admin/templates/pages/<?= (int) $tpl['id'] ?>/"><?= Html::e($tpl['name']) ?></a>
              <?php if (!$m['mapped']): ?>
                <span class="badge badge-warn" title="No template assigned — falling back to a generic layout">Fallback</span>
              <?php endif; ?>
            <?php else: ?>
              <span class="badge badge-off">None</span>
            <?php endif; ?>
          </td>
          <td><?= (int) $m['sections'] ?></td>
          <td>
            <div class="toolbar">
              <a class="btn-ghost" href="/admin/content/<?= Html::e($t['slug']) ?>/new/">Add</a>
              <a class="btn-ghost" href="/admin/post-types/<?= (int) $t['id'] ?>/">Settings</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="row2" style="margin-top:22px">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Recent activity</th><th>When</th></tr></thead>
      <tbody>
        <?php foreach ($activity as $a): ?>
          <tr>
            <td><?= Html::e($a['user_name'] ?: 'System') ?> · <?= Html::e($a['action']) ?></td>
            <td><?= Html::e($a['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$activity): ?><tr><td colspan="2" class="empty">No activity yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
  <div>
    <div class="card" style="margin-bottom:14px">
      <span class="k">Last backup</span>
      <?php if (!empty($lastBackup)): ?>
        <strong style="font-size:1.1rem"><?= Html::e($lastBackup['created_at']) ?></strong>
        <div class="hint"><?= Html::e($lastBackup['filename']) ?> · <?= number_format(((int) $lastBackup['size_bytes']) / 1048576, 2) ?> MB</div>
      <?php else: ?>
        <strong style="font-size:1.1rem">None yet</strong>
        <div class="hint">Create one from Backups.</div>
      <?php endif; ?>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Latest leads</th><th></th></tr></thead>
        <tbody>
          <?php if (!$recentLeads): ?><tr><td colspan="2" class="empty">No enquiries yet.</td></tr><?php endif; ?>
          <?php foreach ($recentLeads as $l): ?>
            <tr>
              <td><?= Html::e($l['form_name']) ?><br><small><?= Html::e($l['created_at']) ?></small></td>
              <td><a href="/admin/leads/<?= (int) $l['id'] ?>/">Open</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php if (!empty($notFound)): ?>
  <div class="table-wrap" style="margin-top:16px">
    <table>
      <thead><tr><th>Recent 404s</th><th>Hits</th></tr></thead>
      <tbody>
        <?php foreach ($notFound as $n): ?>
          <tr><td><?= Html::e($n['path']) ?></td><td><?= (int) $n['hits'] ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
