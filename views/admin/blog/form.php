<div class="page-head">
  <div><p class="crumbs">Blog</p><h1><?= $row ? 'Edit post' : 'New post' ?></h1></div>
  <?php if (!empty($row) && ($row['status'] ?? '') === 'published'): ?>
    <div class="toolbar">
      <a class="btn-ghost" href="/blog/<?= Html::e($row['slug']) ?>/" target="_blank" rel="noopener">View post</a>
    </div>
  <?php endif; ?>
</div>
<form class="form wide" method="post" action="/admin/blog/" data-ajax>
  <?= Csrf::field() ?>
  <?php if ($row): ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><?php endif; ?>
  <label class="lab">Title <input name="title" required value="<?= Html::e($row['title'] ?? '') ?>" data-slug-source="[name=slug]"></label>
  <label class="lab">Slug <input name="slug" value="<?= Html::e($row['slug'] ?? '') ?>"></label>
  <div class="row2">
    <label class="lab">Status
      <select name="status">
        <?php foreach (['draft','published','scheduled','unpublished'] as $st): ?>
          <option value="<?= $st ?>"<?= Html::selected($row['status'] ?? 'draft', $st) ?>><?= $st ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label class="lab">Publish date <input type="datetime-local" name="published_at" value="<?= Html::e(!empty($row['published_at']) ? date('Y-m-d\TH:i', strtotime($row['published_at'])) : '') ?>"></label>
  </div>
  <label class="lab">Excerpt <textarea name="excerpt"><?= Html::e($row['excerpt'] ?? '') ?></textarea></label>
  <?php $name = 'body_html'; $val = $row['body_html'] ?? ''; $label = 'Body'; require ROOT . '/views/admin/partials/wysiwyg.php'; ?>
  <label class="lab">Featured image <input name="featured_image" value="<?= Html::e($row['featured_image'] ?? '') ?>">
    <button class="btn-ghost" type="button" data-media-open="[name=featured_image]">Pick</button></label>
  <label class="lab">Categories
    <div>
      <?php foreach ($categories as $c): ?>
        <label style="display:inline-flex;gap:6px;margin-right:12px"><input type="checkbox" name="categories[]" value="<?= (int) $c['id'] ?>" <?= in_array((string) $c['id'], array_map('strval', $selectedCats), true) ? 'checked' : '' ?>> <?= Html::e($c['name']) ?></label>
      <?php endforeach; ?>
    </div>
  </label>
  <label class="lab">Tags (comma separated) <input name="tags" value="<?= Html::e($selectedTagNames ?? '') ?>"></label>
  <?php $faqItems = $faqItems ?? []; ?>
  <section class="panel faq-editor" data-faq-editor>
    <header class="panel-head panel-head--row">
      <div>
        <h2>FAQ section</h2>
        <p>Shown as an accordion at the end of the post, and submitted to Google as FAQ data. Leave empty to hide the section.</p>
      </div>
      <button class="btn-ghost" type="button" data-faq-add>Add question</button>
    </header>
    <div class="panel-body">
      <div data-faq-rows>
        <?php foreach ($faqItems as $i => $f): ?>
          <div class="faq-row" data-faq-row>
            <span class="faq-row__grip" aria-hidden="true">⋮⋮</span>
            <div class="faq-row__fields">
              <label class="lab">Question <input name="faq_q[]" value="<?= Html::e($f['question']) ?>" placeholder="e.g. Who can apply for this programme?"></label>
              <label class="lab">Answer <textarea name="faq_a[]" rows="3" placeholder="Keep it short and direct."><?= Html::e($f['answer']) ?></textarea></label>
            </div>
            <button class="act act--danger" type="button" data-faq-remove title="Remove this question"><?= admin_icon('trash') ?><span>Remove</span></button>
          </div>
        <?php endforeach; ?>
      </div>
      <p class="faq-editor__empty" data-faq-empty <?= $faqItems ? 'hidden' : '' ?>>No questions yet — add the ones people actually ask about this topic.</p>
    </div>
  </section>

  <?php $seo = $seo ?? []; require ROOT . '/views/admin/partials/seo.php'; ?>
  <button class="btn" type="submit">Save</button>
</form>
