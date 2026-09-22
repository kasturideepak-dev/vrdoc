<?php
$fields = $fields ?? [];
$content = $content ?? [];
$prefix = $prefix ?? 'f_';
foreach ($fields as $f):
    $name = $prefix . $f['k'];
    $raw = $content[$f['k']] ?? '';
    $val = is_array($raw) ? implode("\n", $raw) : (string) $raw;
?>
  <?php if ($f['t'] === 'html'): ?>
    <?php $label = $f['l']; require ROOT . '/views/admin/partials/wysiwyg.php'; ?>
  <?php else: ?>
  <label class="lab"><?= Html::e($f['l']) ?>
    <?php if ($f['t'] === 'select'): ?>
      <select name="<?= Html::e($name) ?>">
        <?php foreach (($f['opts'] ?? []) as $ov => $ol): ?>
          <option value="<?= Html::e((string) $ov) ?>"<?= Html::selected($val, (string) $ov) ?>><?= Html::e($ol) ?></option>
        <?php endforeach; ?>
      </select>
    <?php elseif ($f['t'] === 'textarea'): ?>
      <textarea name="<?= Html::e($name) ?>"><?= Html::e($val) ?></textarea>
    <?php elseif ($f['t'] === 'image'): ?>
      <input name="<?= Html::e($name) ?>" value="<?= Html::e($val) ?>">
      <button class="btn-ghost" type="button" data-media-open="[name='<?= Html::e($name) ?>']">Pick image</button>
    <?php else: ?>
      <input name="<?= Html::e($name) ?>" value="<?= Html::e($val) ?>">
    <?php endif; ?>
  </label>
  <?php endif; ?>
<?php endforeach; ?>
