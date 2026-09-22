<?php
/**
 * Entry editor — main column for content, sticky side column for publishing,
 * image and categorisation. Field names are unchanged from the flat version.
 */
$row = $row ?? null;
$values = $values ?? [];
$mode = $type['template_mode'] ?? 'both';
// Layouts built around a banner block take the title, excerpt and featured
// image straight from this form, so the labels say where each one shows up.
$banner = !empty($bannerMode);
$base = '/admin/content/' . Html::e($type['slug']);
$eid = (int) ($row['id'] ?? 0);
$useBuilder = in_array($mode, ['builder', 'both'], true);
$status = $row['status'] ?? 'draft';
$statusBadge = ['published' => 'badge-ok', 'scheduled' => 'badge-warn', 'draft' => 'badge-off', 'unpublished' => 'badge-off'][$status] ?? 'badge-off';
$permalink = $row && (int) $type['public'] ? Cpt::permalink($row, $type) : '';
// The site renders an entry's sections when it has any, and falls back to its
// Body text only when it has none. Show Body only where it will actually appear.
$hasSections = $row && !empty($sections);
$showBody = (int) $type['supports_editor'] && !$hasSections;
$strandedBody = $hasSections && trim(strip_tags((string) ($row['body_html'] ?? ''))) !== '';
?>
<div class="page-head">
  <div>
    <p class="crumbs"><a href="<?= $base ?>/"><?= Html::e($type['name']) ?></a></p>
    <h1><?= $row ? Html::e($row['title']) : 'New ' . Html::e($type['singular_name']) ?></h1>
    <?php if ($permalink): ?>
      <p class="entry-url">
        <span class="badge <?= $statusBadge ?>"><?= Html::e($status) ?></span>
        <a href="<?= Html::e($permalink) ?>" target="_blank" rel="noopener"><?= Html::e($permalink) ?></a>
      </p>
    <?php endif; ?>
  </div>
  <?php if ($row): ?>
    <div class="toolbar">
      <?php if ($permalink && $status === 'published'): ?>
        <a class="btn-ghost" href="<?= Html::e($permalink) ?>" target="_blank" rel="noopener">View page</a>
      <?php endif; ?>
      <button class="btn-ghost" type="submit" form="preview-form">Preview</button>
      <a class="btn-ghost" href="/admin/revisions/cpt/<?= $eid ?>/compare/">Revisions</a>
    </div>
  <?php endif; ?>
</div>

<form class="form entry-shell" id="entry-form" method="post" action="<?= $base ?>/" data-ajax>
  <?= Csrf::field() ?>
  <?php if ($row): ?><input type="hidden" name="id" value="<?= $eid ?>"><?php endif; ?>

  <div class="entry-main">

    <?php if (!$row && !empty($pageTemplates) && $useBuilder):
      $defaultTpl = (int) ($defaultTemplateId ?? 0);
    ?>
      <section class="panel">
        <header class="panel-head">
          <h2>Start from a template</h2>
          <p>
            <?php if (!empty($defaultTemplate)): ?>
              <strong><?= Html::e($defaultTemplate['name']) ?></strong> is the default for <?= Html::e($type['name']) ?>. Pick another if this entry needs different sections.
            <?php else: ?>
              No default template for <?= Html::e($type['name']) ?> yet —
              <a href="/admin/post-types/<?= (int) $type['id'] ?>/">set one</a> so every entry starts the same way.
            <?php endif; ?>
          </p>
        </header>
        <div class="panel-body">
          <div class="tpl-grid">
            <label class="tpl-pick">
              <input type="radio" name="template_id" value="" <?= $defaultTpl ? '' : 'checked' ?>>
              <strong>Blank</strong>
              <span>Fields only — add sections later.</span>
            </label>
            <?php foreach ($pageTemplates as $t):
              $n = Templates::sectionCount($t['sections_json'] ?? '[]');
            ?>
              <label class="tpl-pick">
                <input type="radio" name="template_id" value="<?= (int) $t['id'] ?>" <?= $defaultTpl === (int) $t['id'] ? 'checked' : '' ?>>
                <strong><?= Html::e($t['name']) ?><?= $defaultTpl === (int) $t['id'] ? ' — default' : '' ?></strong>
                <span><?= Html::e($t['description'] ?: $t['page_type']) ?></span>
                <small><?= $n ?> section<?= $n === 1 ? '' : 's' ?></small>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <section class="panel">
      <header class="panel-head">
        <h2>Content</h2>
        <?php if ($banner): ?>
          <p>Title and description appear on the banner; the featured image is the banner photo unless the Banner section overrides it.</p>
        <?php endif; ?>
      </header>
      <div class="panel-body">
        <label class="lab entry-title"><?= $banner ? 'Title (banner heading)' : 'Title' ?>
          <input name="title" required value="<?= Html::e($row['title'] ?? '') ?>" data-slug-source="[name=slug]" placeholder="Give it a clear, specific title">
        </label>
        <label class="lab">URL slug
          <span class="slug-field">
            <span class="slug-field__prefix">/<?= Html::e($type['slug']) ?>/</span>
            <input name="slug" value="<?= Html::e($row['slug'] ?? '') ?>" data-slug-check="entry" data-id="<?= $eid ?>" data-type-id="<?= (int) $type['id'] ?>" placeholder="auto-from-title">
          </span>
        </label>
        <?php if ((int) $type['supports_excerpt']): ?>
          <label class="lab"><?= $banner ? 'Description (banner subtitle)' : 'Excerpt' ?>
            <textarea name="excerpt" rows="2" placeholder="One or two sentences — used in listings and search results"><?= Html::e($row['excerpt'] ?? '') ?></textarea>
          </label>
        <?php endif; ?>
        <?php if ($showBody): ?>
          <div data-body-block>
            <?php $name = 'body_html'; $val = (string) ($row['body_html'] ?? ''); $label = 'Body'; require ROOT . '/views/admin/partials/wysiwyg.php'; ?>
            <p class="hint"><?= $row ? 'This is the page’s main text.' : 'Used as the page’s main text when you start from Blank. Templates build the page from sections instead.' ?></p>
          </div>
        <?php elseif ($hasSections): ?>
          <div class="where-note">
            <strong>This page is built from sections.</strong>
            Its text, images and videos are edited in <a href="#builder">Page sections</a> below — each one is a block on the live page, in that order.
          </div>
        <?php endif; ?>
        <?php if ($strandedBody): ?>
          <div class="where-note is-warn">
            <strong>Older body text isn’t showing on the site.</strong>
            Because this page uses sections, its Body text (“<?= Html::e(mb_strimwidth(trim(strip_tags((string) $row['body_html'])), 0, 80, '…')) ?>”) is not displayed.
            <button class="btn-ghost" type="submit" form="body-to-section-form">Move it into a Rich text section</button>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <?php if ($mode !== 'builder' && !empty($fieldDefs)): ?>
      <section class="panel">
        <header class="panel-head">
          <h2>Details</h2>
          <p>Fields specific to <?= Html::e($type['name']) ?>.</p>
        </header>
        <div class="panel-body">
          <?php foreach ($fieldDefs as $f):
            $val = $values[$f['name']] ?? '';
            $opts = json_decode($f['options_json'] ?: '{}', true) ?: [];
            $req = (int) $f['is_required'] ? ' <span class="req">*</span>' : '';
            $help = $f['help_text'] ? '<small class="field-help">' . Html::e($f['help_text']) . '</small>' : '';
          ?>
            <?php if ($f['type'] === 'image'): ?>
              <?php $name = 'f_' . $f['name']; $label = $f['label']; $val = (string) $val; require ROOT . '/views/admin/partials/image-field.php'; ?>

            <?php elseif ($f['type'] === 'richtext'): ?>
              <?php $name = 'f_' . $f['name']; $label = $f['label']; require ROOT . '/views/admin/partials/wysiwyg.php'; ?>

            <?php elseif ($f['type'] === 'checkbox'): ?>
              <label class="switch-row">
                <input type="checkbox" name="f_<?= Html::e($f['name']) ?>" value="1" <?= Html::checked((string) $val === '1') ?>>
                <span><strong><?= Html::e($f['label']) ?></strong><?= $help ?></span>
              </label>

            <?php elseif ($f['type'] === 'relation'):
              $target = trim((string) ($opts['raw'] ?? ''));
              $choices = $target !== '' ? Cpt::relationChoices($target, $eid) : [];
              $linkedIds = $row ? array_flip(Cpt::relationIds($eid, $f['name'])) : [];
            ?>
              <div class="lab"><?= Html::e($f['label']) ?><?= $req ?><?= $help ?>
                <?php if ($target === ''): ?>
                  <p class="hint">Set the target post type slug in this field's Options (e.g. <code>faculty</code>).</p>
                <?php elseif (!$choices): ?>
                  <p class="hint">No entries in <code><?= Html::e($target) ?></code> to link to yet.</p>
                <?php else: ?>
                  <div class="term-picker">
                    <?php foreach ($choices as $ch): ?>
                      <label class="term-chip">
                        <input type="checkbox" name="rel_<?= Html::e($f['name']) ?>[]" value="<?= (int) $ch['id'] ?>" <?= isset($linkedIds[(int) $ch['id']]) ? 'checked' : '' ?>>
                        <span><?= Html::e($ch['title']) ?><?= $ch['status'] !== 'published' ? ' (' . Html::e($ch['status']) . ')' : '' ?></span>
                      </label>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>

            <?php elseif ($f['type'] === 'repeater'):
              $rowsR = is_array($val) && $val ? $val : [[]];
              $subs = $opts['subfields'] ?? [['name' => 'text', 'label' => 'Text', 'type' => 'text']];
            ?>
              <div class="lab"><?= Html::e($f['label']) ?><?= $req ?><?= $help ?>
                <textarea name="f_<?= Html::e($f['name']) ?>_raw" hidden></textarea>
                <div class="repeater" data-repeater>
                  <?php foreach ($rowsR as $ri => $rr): ?>
                    <div class="field-grid repeater__row">
                      <?php foreach ($subs as $sf): ?>
                        <label class="lab is-half"><?= Html::e($sf['label']) ?>
                          <input name="f_<?= Html::e($f['name']) ?>[<?= (int) $ri ?>][<?= Html::e($sf['name']) ?>]" value="<?= Html::e((string) ($rr[$sf['name']] ?? '')) ?>">
                        </label>
                      <?php endforeach; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>

            <?php else: ?>
              <label class="lab"><?= Html::e($f['label']) ?><?= $req ?><?= $help ?>
                <?php if ($f['type'] === 'textarea'): ?>
                  <textarea name="f_<?= Html::e($f['name']) ?>" rows="3"><?= Html::e(is_array($val) ? implode("\n", $val) : (string) $val) ?></textarea>
                <?php elseif ($f['type'] === 'gallery'): ?>
                  <textarea name="f_<?= Html::e($f['name']) ?>" rows="3" placeholder="One image path per line"><?= Html::e(is_array($val) ? implode("\n", $val) : (string) $val) ?></textarea>
                <?php elseif ($f['type'] === 'select'): ?>
                  <select name="f_<?= Html::e($f['name']) ?>">
                    <option value="">Select…</option>
                    <?php foreach ($opts['choices'] ?? [] as $ch): ?>
                      <option value="<?= Html::e($ch) ?>"<?= Html::selected((string) $val, $ch) ?>><?= Html::e($ch) ?></option>
                    <?php endforeach; ?>
                  </select>
                <?php elseif ($f['type'] === 'number'): ?>
                  <input type="number" name="f_<?= Html::e($f['name']) ?>" value="<?= Html::e((string) $val) ?>">
                <?php elseif ($f['type'] === 'date'): ?>
                  <input type="date" name="f_<?= Html::e($f['name']) ?>" value="<?= Html::e((string) $val) ?>">
                <?php elseif ($f['type'] === 'url'): ?>
                  <input type="url" name="f_<?= Html::e($f['name']) ?>" value="<?= Html::e((string) $val) ?>" placeholder="https://">
                <?php elseif ($f['type'] === 'email'): ?>
                  <input type="email" name="f_<?= Html::e($f['name']) ?>" value="<?= Html::e((string) $val) ?>">
                <?php else: ?>
                  <input name="f_<?= Html::e($f['name']) ?>" value="<?= Html::e((string) $val) ?>">
                <?php endif; ?>
              </label>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <?php if ($row && $useBuilder): ?>
      <section class="panel" id="builder">
        <header class="panel-head panel-head--row">
          <div>
            <h2>Page sections</h2>
            <p><?= count($sections) ?> section<?= count($sections) === 1 ? '' : 's' ?> · drag the handle to reorder, click a section to expand.</p>
          </div>
          <button class="btn-ghost" type="button" data-sec-toggle-all>Expand all</button>
        </header>
        <div class="panel-body">
          <?php if (!$sections): ?>
            <div class="empty">No sections yet — add one below.</div>
          <?php endif; ?>
          <div class="sec-list" data-sortable data-sort-item=".sec-card">
            <?php foreach ($sections as $n => $sec):
              $cid = (int) $sec['id'];
              $content = json_decode($sec['content_json'] ?: '{}', true) ?: [];
              $fields = $registry[$sec['type']]['fields'] ?? [];
              $linked = !empty($sec['is_linked']);
              $variant = SectionRegistry::variant($sec['type'], $content);
              $variants = SectionRegistry::variants($sec['type']);
              $summary = trim((string) ($content['heading'] ?? $content['kicker'] ?? ''));
            ?>
              <details class="sec-card<?= $linked ? ' is-linked' : '' ?><?= $sec['is_visible'] ? '' : ' is-hidden' ?>">
                <summary class="sec-card__head">
                  <span class="sec-card__grip" draggable="true" aria-hidden="true" title="Drag to reorder">⋮⋮</span>
                  <span class="sec-card__icon"><?= Html::e(SectionRegistry::icon($sec['type'])) ?></span>
                  <span class="sec-card__title">
                    <strong><?= Html::e($registry[$sec['type']]['label'] ?? $sec['type']) ?></strong>
                    <?php if ($summary !== ''): ?><small><?= Html::e(mb_strimwidth($summary, 0, 70, '…')) ?></small><?php endif; ?>
                  </span>
                  <span class="sec-card__tags">
                    <?php if ($variant !== '' && isset($variants[$variant])): ?>
                      <span class="badge badge-off"><?= Html::e(strtok($variants[$variant], ' —')) ?></span>
                    <?php endif; ?>
                    <?php if ($linked): ?><span class="badge badge-ok" title="Content comes from a global block">Global</span><?php endif; ?>
                    <?php if (!$sec['is_visible']): ?><span class="badge badge-warn">Hidden</span><?php endif; ?>
                  </span>
                </summary>

                <div class="sec-card__body">
                  <input type="hidden" name="section_id[]" value="<?= $cid ?>">
                  <div class="sec-card__bar">
                    <label class="sec-card__vis">
                      <input type="checkbox" name="visible[<?= $cid ?>]" <?= $sec['is_visible'] ? 'checked' : '' ?>> Visible on the page
                    </label>
                    <span class="toolbar">
                      <?php if ($linked): ?>
                        <button class="btn-ghost" form="unlink-<?= $cid ?>" type="submit">Unlink to customise</button>
                      <?php else: ?>
                        <button class="btn-ghost" form="tpl-<?= $cid ?>" type="submit" title="Save this block to the template library">Save as template</button>
                      <?php endif; ?>
                      <button class="btn-danger" form="del-<?= $cid ?>" type="submit">Remove</button>
                    </span>
                  </div>

                  <?php if ($linked): ?>
                    <p class="hint">
                      This block is shared<?= !empty($sec['_linked_name']) ? ' (“' . Html::e($sec['_linked_name']) . '”)' : '' ?>.
                      Edit it once in <a href="/admin/templates/">Templates → Section templates</a> and every page using it updates.
                    </p>
                  <?php else: ?>
                    <div class="field-grid">
                      <?php foreach ($fields as $f):
                        $name = 's' . $cid . '_' . $f['k'];
                        $val = (string) ($content[$f['k']] ?? '');
                        $wide = in_array($f['t'], ['textarea', 'html'], true);
                      ?>
                        <?php if ($f['t'] === 'html'): ?>
                          <div class="is-full"><?php $label = $f['l']; require ROOT . '/views/admin/partials/wysiwyg.php'; ?></div>
                        <?php elseif ($f['t'] === 'image'): ?>
                          <div class="lab is-half"><?php $label = $f['l']; require ROOT . '/views/admin/partials/image-field.php'; ?></div>
                        <?php else: ?>
                          <label class="lab <?= $wide ? 'is-full' : 'is-half' ?>"><?= Html::e($f['l']) ?>
                            <?php if ($f['t'] === 'select'): ?>
                              <select name="<?= Html::e($name) ?>">
                                <?php foreach (($f['opts'] ?? []) as $ov => $ol): ?>
                                  <option value="<?= Html::e((string) $ov) ?>"<?= Html::selected($val, (string) $ov) ?>><?= Html::e($ol) ?></option>
                                <?php endforeach; ?>
                              </select>
                            <?php elseif ($f['t'] === 'textarea'): ?>
                              <textarea name="<?= Html::e($name) ?>" rows="3"><?= Html::e($val) ?></textarea>
                            <?php else: ?>
                              <input name="<?= Html::e($name) ?>" value="<?= Html::e($val) ?>">
                            <?php endif; ?>
                          </label>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </details>
            <?php endforeach; ?>
          </div>

          <div class="add-block">
            <div class="add-block__row">
              <select name="type" form="add-section-form" aria-label="Section type">
                <?php foreach ($registry as $k => $meta): ?>
                  <option value="<?= Html::e($k) ?>"><?= Html::e($meta['label']) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn" type="submit" form="add-section-form">+ Add section</button>
            </div>
            <?php if (!empty($sectionTemplates)): ?>
              <details class="add-block__saved">
                <summary>Insert a saved block (<?= count($sectionTemplates) ?>)</summary>
                <ul class="saved-list">
                  <?php foreach ($sectionTemplates as $st): ?>
                    <li>
                      <span><strong><?= Html::e($st['name']) ?></strong><small><?= Html::e(SectionRegistry::label($st['type'])) ?></small></span>
                      <span class="toolbar">
                        <button class="btn-ghost" type="submit" form="ins-copy-<?= (int) $st['id'] ?>" title="An independent copy you can edit here">Copy</button>
                        <button class="btn-ghost" type="submit" form="ins-link-<?= (int) $st['id'] ?>" title="Stays in sync with the global block">Link</button>
                      </span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </details>
            <?php endif; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <details class="panel panel-collapse">
      <summary class="panel-head"><h2>SEO &amp; sharing</h2><p>Search title, description and social preview. Optional — sensible defaults are used.</p></summary>
      <div class="panel-body">
        <?php $seo = $seo ?? []; require ROOT . '/views/admin/partials/seo.php'; ?>
      </div>
    </details>
  </div>

  <aside class="entry-side">
    <section class="panel side-publish">
      <header class="panel-head"><h2>Publish</h2></header>
      <div class="panel-body">
        <label class="lab">Status
          <select name="status" data-status>
            <?php foreach (['draft' => 'Draft', 'published' => 'Published', 'scheduled' => 'Scheduled', 'unpublished' => 'Unpublished'] as $sv => $sl): ?>
              <option value="<?= $sv ?>"<?= Html::selected($status, $sv) ?>><?= $sl ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="lab" data-schedule-row <?= $status === 'scheduled' ? '' : 'hidden' ?>>Publish at
          <input type="datetime-local" name="scheduled_at" value="<?= Html::e(!empty($row['scheduled_at']) ? date('Y-m-d\TH:i', strtotime($row['scheduled_at'])) : '') ?>">
        </label>
        <label class="lab">Sort order
          <input type="number" name="sort_order" value="<?= Html::e((string) ($row['sort_order'] ?? '0')) ?>">
        </label>
        <?php if ($row && !empty($row['updated_at'])): ?>
          <p class="hint">Last saved <?= Html::e(date('j M Y, H:i', strtotime((string) $row['updated_at']))) ?></p>
        <?php endif; ?>
        <div class="side-publish__actions">
          <button class="btn" type="submit"><?= $row ? 'Save changes' : 'Create ' . Html::e($type['singular_name']) ?></button>
          <?php if ($status !== 'published'): ?>
            <button class="btn-ghost" type="button" data-save-publish>Save &amp; publish</button>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <?php if ((int) $type['supports_featured_image']): ?>
      <section class="panel">
        <header class="panel-head"><h2><?= $banner ? 'Banner image' : 'Featured image' ?></h2></header>
        <div class="panel-body">
          <?php $name = 'featured_image'; $val = (string) ($row['featured_image'] ?? ''); $label = ''; require ROOT . '/views/admin/partials/image-field.php'; ?>
        </div>
      </section>
    <?php endif; ?>

    <?php if (!empty($taxonomies)):
      $picked = array_flip($entryTermIds ?? []);
    ?>
      <?php foreach ($taxonomies as $tx): ?>
        <section class="panel">
          <header class="panel-head"><h2><?= Html::e($tx['name']) ?></h2></header>
          <div class="panel-body">
            <?php if (!$tx['terms']): ?>
              <p class="hint">No terms yet — <a href="/admin/post-types/<?= (int) $type['id'] ?>/taxonomies/">add some</a>.</p>
            <?php else: ?>
              <div class="term-picker">
                <?php foreach ($tx['terms'] as $term): ?>
                  <label class="term-chip">
                    <input type="checkbox" name="terms[]" value="<?= (int) $term['id'] ?>" <?= isset($picked[(int) $term['id']]) ? 'checked' : '' ?>>
                    <span><?= Html::e($term['name']) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </section>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($row): ?>
      <section class="panel panel--quiet">
        <div class="panel-body">
          <button class="btn-danger btn-block" type="submit" form="trash-form">Move to trash</button>
        </div>
      </section>
    <?php endif; ?>
  </aside>
</form>

<?php /* Secondary actions live in their own forms — forms cannot nest. */ ?>
<?php if ($row): ?>
  <form id="preview-form" method="post" action="<?= $base ?>/<?= $eid ?>/preview/" data-ajax="off" hidden><?= Csrf::field() ?></form>
  <form id="trash-form" method="post" action="<?= $base ?>/trash/" data-confirm="Move this entry to trash?" data-ajax="off" hidden>
    <?= Csrf::field() ?><input type="hidden" name="id" value="<?= $eid ?>">
  </form>
<?php endif; ?>
<?php if ($strandedBody): ?>
  <form id="body-to-section-form" method="post" action="<?= $base ?>/<?= $eid ?>/body-to-section/" data-ajax="off" hidden><?= Csrf::field() ?></form>
<?php endif; ?>
<?php if ($row && $useBuilder): ?>
  <form id="add-section-form" method="post" action="<?= $base ?>/<?= $eid ?>/section/" data-ajax="off" hidden><?= Csrf::field() ?></form>
  <?php foreach ($sections as $sec): $cid = (int) $sec['id']; ?>
    <form id="tpl-<?= $cid ?>" method="post" action="<?= $base ?>/<?= $eid ?>/section-template/" data-ajax="off" hidden>
      <?= Csrf::field() ?><input type="hidden" name="section_id" value="<?= $cid ?>">
      <input type="hidden" name="name" value="<?= Html::e($registry[$sec['type']]['label'] ?? $sec['type']) ?>">
    </form>
    <form id="unlink-<?= $cid ?>" method="post" action="<?= $base ?>/<?= $eid ?>/section-unlink/" data-ajax="off" hidden>
      <?= Csrf::field() ?><input type="hidden" name="section_id" value="<?= $cid ?>">
    </form>
    <form id="del-<?= $cid ?>" method="post" action="<?= $base ?>/<?= $eid ?>/section-delete/" data-confirm="Remove this section from the page?" data-ajax="off" hidden>
      <?= Csrf::field() ?><input type="hidden" name="section_id" value="<?= $cid ?>">
    </form>
  <?php endforeach; ?>
  <?php foreach ($sectionTemplates ?? [] as $st): ?>
    <form id="ins-copy-<?= (int) $st['id'] ?>" method="post" action="<?= $base ?>/<?= $eid ?>/section/" data-ajax="off" hidden>
      <?= Csrf::field() ?>
      <input type="hidden" name="type" value="<?= Html::e($st['type']) ?>">
      <input type="hidden" name="section_template_id" value="<?= (int) $st['id'] ?>">
    </form>
    <form id="ins-link-<?= (int) $st['id'] ?>" method="post" action="<?= $base ?>/<?= $eid ?>/section/" data-ajax="off" hidden>
      <?= Csrf::field() ?>
      <input type="hidden" name="type" value="<?= Html::e($st['type']) ?>">
      <input type="hidden" name="section_template_id" value="<?= (int) $st['id'] ?>">
      <input type="hidden" name="linked" value="1">
    </form>
  <?php endforeach; ?>
<?php endif; ?>
