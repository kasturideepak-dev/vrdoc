<?php
$type = $type ?? [];
$taxonomies = $taxonomies ?? [];
$base = '/admin/post-types/' . (int) $type['id'];
?>
<div class="page-head">
  <div>
    <p class="crumbs"><a href="/admin/post-types/">Post types</a> / <a href="<?= $base ?>/"><?= Html::e($type['name']) ?></a></p>
    <h1>Taxonomies</h1>
    <p>Ways to group <?= Html::e($type['name']) ?> — like categories or tags. Public ones get their own archive page.</p>
  </div>
  <a class="btn-ghost" href="<?= $base ?>/">Back to post type</a>
</div>

<div class="set-main">
  <?php foreach ($taxonomies as $t): ?>
    <section class="panel">
      <header class="panel-head">
        <div class="page-head" style="margin:0">
          <div>
            <h2><?= Html::e($t['name']) ?>
              <span class="badge badge-off"><?= (int) $t['hierarchical'] ? 'Hierarchical' : 'Flat' ?></span>
              <?php if (!(int) $t['public']): ?><span class="badge badge-warn">Private</span><?php endif; ?>
            </h2>
            <p>
              <code>/<?= Html::e($type['slug']) ?>/<?= Html::e($t['slug']) ?>/{term}/</code>
              · <?= count($t['terms']) ?> term<?= count($t['terms']) === 1 ? '' : 's' ?>
            </p>
          </div>
          <form method="post" action="<?= $base ?>/taxonomies/delete/" data-confirm="Delete this taxonomy and all its terms?">
            <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
            <button class="btn-danger" type="submit">Delete</button>
          </form>
        </div>
      </header>
      <div class="panel-body">

        <?php if ($t['terms']): ?>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Term</th><th>Slug</th><th>Entries</th><th></th></tr></thead>
              <tbody>
                <?php foreach ($t['terms'] as $term): ?>
                  <tr>
                    <td>
                      <?php if (!empty($term['parent_id'])): ?><span style="opacity:.5">└ </span><?php endif; ?>
                      <?= Html::e($term['name']) ?>
                    </td>
                    <td><code><?= Html::e($term['slug']) ?></code></td>
                    <td><?= (int) $term['entry_count'] ?></td>
                    <td>
                      <form method="post" action="<?= $base ?>/taxonomies/term-delete/" data-confirm="Delete this term?" style="display:inline">
                        <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $term['id'] ?>">
                        <button class="btn-ghost" type="submit">Remove</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty">No terms yet — add the first below.</div>
        <?php endif; ?>

        <form method="post" action="<?= $base ?>/taxonomies/term-save/">
          <?= Csrf::field() ?>
          <input type="hidden" name="taxonomy_id" value="<?= (int) $t['id'] ?>">
          <div class="field-grid">
            <label class="lab is-third">New term <input name="name" placeholder="e.g. NEET" required></label>
            <label class="lab is-third">Slug (optional) <input name="slug" placeholder="neet"></label>
            <?php if ((int) $t['hierarchical']): ?>
              <label class="lab is-third">Parent
                <select name="parent_id">
                  <option value="0">— none —</option>
                  <?php foreach ($t['terms'] as $p): ?>
                    <option value="<?= (int) $p['id'] ?>"><?= Html::e($p['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
            <?php endif; ?>
            <div class="lab is-quarter" style="align-self:end">
              <button class="btn" type="submit">Add term</button>
            </div>
          </div>
        </form>
      </div>
    </section>
  <?php endforeach; ?>

  <section class="panel">
    <header class="panel-head">
      <h2>Add a taxonomy</h2>
      <p>Flat works like tags; hierarchical works like categories with parents.</p>
    </header>
    <div class="panel-body">
      <form method="post" action="<?= $base ?>/taxonomies/">
        <?= Csrf::field() ?>
        <div class="field-grid">
          <label class="lab is-third">Name <input name="name" placeholder="Subjects" required></label>
          <label class="lab is-third">Singular <input name="singular_name" placeholder="Subject"></label>
          <label class="lab is-third">URL slug <input name="slug" placeholder="subject"></label>
          <label class="lab is-full">Description <input name="description" placeholder="Shown on the archive page"></label>
        </div>
        <div class="toolbar">
          <label><input type="checkbox" name="hierarchical"> Hierarchical (parents and children)</label>
          <label><input type="checkbox" name="public" checked> Public archive page</label>
          <button class="btn" type="submit">Create taxonomy</button>
        </div>
      </form>
    </div>
  </section>
</div>
