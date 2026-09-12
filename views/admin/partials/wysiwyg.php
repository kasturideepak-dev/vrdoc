<?php
/** Rich text / WYSIWYG field — Quill editor synced to a hidden textarea. */
$name = $name ?? '';
$val = $val ?? '';
$label = $label ?? 'Content';
$id = $id ?? ('wysiwyg_' . preg_replace('/[^a-z0-9_]/i', '_', $name));
?>
<label class="lab wysiwyg-field">
  <?= Html::e($label) ?>
  <div class="wysiwyg-mount" data-wysiwyg-for="<?= Html::e($id) ?>"><?= Html::allowedHtml((string) $val) ?></div>
  <textarea id="<?= Html::e($id) ?>" name="<?= Html::e($name) ?>" hidden><?= Html::e((string) $val) ?></textarea>
</label>
