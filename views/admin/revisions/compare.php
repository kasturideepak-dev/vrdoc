<?php
$revisions = $revisions ?? [];
$diff = $diff ?? [];
$label = static function (array $r): string {
    return '#' . $r['id'] . ' · ' . $r['created_at']
        . ($r['author'] ? ' · ' . $r['author'] : '')
        . ((int) $r['is_live'] ? ' · LIVE' : '')
        . ($r['note'] ? ' · ' . $r['note'] : '');
};
$trim = static fn (string $s): string => mb_strlen($s) > 160 ? mb_substr($s, 0, 160) . '…' : $s;
?>
<div class="page-head">
  <div>
    <p class="crumbs">Revisions</p>
    <h1>Compare revisions</h1>
    <p>What changed between two saved versions, block by block.</p>
  </div>
  <a class="btn-ghost" href="<?= Html::e($backUrl) ?>">Back to editor</a>
</div>

<form class="panel" method="get">
  <div class="panel-body">
    <div class="field-grid">
      <label class="lab is-half">From (older)
        <select name="from">
          <?php foreach ($revisions as $r): ?>
            <option value="<?= (int) $r['id'] ?>"<?= Html::selected($fromId, (int) $r['id']) ?>><?= Html::e($label($r)) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="lab is-half">To (newer)
        <select name="to">
          <?php foreach ($revisions as $r): ?>
            <option value="<?= (int) $r['id'] ?>"<?= Html::selected($toId, (int) $r['id']) ?>><?= Html::e($label($r)) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div class="toolbar"><button class="btn" type="submit">Compare</button></div>
  </div>
</form>

<div class="set-main" style="margin-top:16px">
  <?php if (!$diff): ?>
    <div class="empty">No differences between these two revisions.</div>
  <?php endif; ?>
  <?php foreach ($diff as $d): ?>
    <section class="panel diff-block is-<?= Html::e($d['status']) ?>">
      <header class="panel-head">
        <h2>
          Block <?= (int) $d['index'] + 1 ?> — <?= Html::e(SectionRegistry::label($d['type'])) ?>
          <span class="badge <?= $d['status'] === 'added' ? 'badge-ok' : ($d['status'] === 'removed' ? 'badge-err' : 'badge-warn') ?>">
            <?= Html::e($d['status']) ?>
          </span>
        </h2>
      </header>
      <?php if ($d['changes']): ?>
        <div class="panel-body" style="padding:0">
          <div class="table-wrap" style="border:0;box-shadow:none;background:none">
            <table>
              <thead><tr><th>Field</th><th>Before</th><th>After</th></tr></thead>
              <tbody>
                <?php foreach ($d['changes'] as $c): ?>
                  <tr>
                    <td><code><?= Html::e($c['key']) ?></code></td>
                    <td class="diff-from"><?= $c['from'] === '' ? '<em>empty</em>' : Html::e($trim($c['from'])) ?></td>
                    <td class="diff-to"><?= $c['to'] === '' ? '<em>empty</em>' : Html::e($trim($c['to'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>
    </section>
  <?php endforeach; ?>
</div>
