<?php
declare(strict_types=1);

final class AdminEntries
{
    public static function index(string $typeSlug): void
    {
        Auth::requirePerm('entries.view');
        $type = Cpt::type($typeSlug);
        if (!$type) {
            View::admin('errors/404', ['title' => 'Not found']);
            return;
        }
        $trash = Request::str('trash') === '1';
        $sql = 'SELECT * FROM cpt_entries WHERE post_type_id = ? AND ' . ($trash ? 'deleted_at IS NOT NULL' : 'deleted_at IS NULL') . ' ORDER BY sort_order, id DESC';
        View::admin('entries/index', [
            'title' => $type['name'],
            'type' => $type,
            'rows' => Database::all($sql, [(int) $type['id']]),
            'trash' => $trash,
        ]);
    }

    public static function form(string $typeSlug, ?string $id = null): void
    {
        $type = Cpt::type($typeSlug);
        if (!$type) {
            View::admin('errors/404', ['title' => 'Not found']);
            return;
        }
        $row = $id ? Database::one('SELECT * FROM cpt_entries WHERE id = ? AND post_type_id = ?', [(int) $id, (int) $type['id']]) : null;
        Auth::requirePerm($row ? 'entries.edit' : 'entries.create');
        if ($id && !$row) {
            View::admin('errors/404', ['title' => 'Not found']);
            return;
        }
        Templates::ensureStarters();
        Cpt::ensureSchema();
        $fields = Cpt::fields((int) $type['id']);
        $values = $row ? (json_decode($row['fields_json'] ?: '{}', true) ?: []) : [];
        $sections = $row ? Content::sections('cpt', (int) $row['id']) : [];
        $seo = $row ? (Database::one('SELECT * FROM seo_metadata WHERE entity_type="cpt" AND entity_id=?', [(int) $row['id']]) ?: []) : [];
        $defaultTemplateId = Templates::defaultTemplateIdForType($typeSlug);
        // Some layouts put the entry title/excerpt/image straight on a banner
        // block, so the field labels say so. Read that from the real sections
        // rather than hard-coding one post type's slug.
        $sectionTypes = $row
            ? array_column($sections, 'type')
            : Templates::sectionTypes($defaultTemplateId);
        View::admin('entries/form', [
            'title' => $row ? ('Edit ' . $type['singular_name']) : ('New ' . $type['singular_name']),
            'type' => $type,
            'row' => $row,
            'fieldDefs' => $fields,
            'values' => $values,
            'sections' => $sections,
            'seo' => $seo,
            'registry' => SectionRegistry::all(),
            'sectionTemplates' => Database::all('SELECT * FROM section_templates ORDER BY name'),
            'pageTemplates' => Templates::allPages(),
            'defaultTemplateId' => $defaultTemplateId,
            'defaultTemplate' => $defaultTemplateId ? Templates::page($defaultTemplateId) : null,
            'bannerMode' => in_array('ai_banner', $sectionTypes, true),
            'taxonomies' => array_map(static function (array $t): array {
                $t['terms'] = Taxonomy::terms((int) $t['id']);
                return $t;
            }, Taxonomy::forType((int) $type['id'])),
            'entryTermIds' => $row ? Taxonomy::entryTermIds((int) $row['id']) : [],
            'ownerType' => 'cpt',
            'ownerId' => $row ? (int) $row['id'] : 0,
            'publicPath' => $row ? Cpt::permalink($row, $type) : path_url($type['slug'] . '/new'),
        ]);
    }

    public static function save(string $typeSlug): void
    {
        $type = Cpt::type($typeSlug);
        if (!$type) {
            View::redirect('/admin/post-types/');
        }
        $id = Request::int('id');
        Auth::requirePerm($id ? 'entries.edit' : 'entries.create');
        $fieldDefs = Cpt::fields((int) $type['id']);
        $values = Cpt::saveFieldsFromRequest($fieldDefs);
        $errors = Cpt::validateRequired($fieldDefs, $values);
        $title = Request::str('title');
        if ($title === '') {
            $errors[] = 'Title is required.';
        }
        $slugIn = Request::str('slug') ?: $title;
        if ($errors) {
            if (Request::wantsJson()) {
                View::json(['ok' => false, 'error' => implode(' ', $errors)], 422);
            }
            View::flash('error', implode(' ', $errors));
            View::redirect($id ? '/admin/content/' . $typeSlug . '/' . $id . '/' : '/admin/content/' . $typeSlug . '/new/');
        }
        $old = $id ? Database::one('SELECT * FROM cpt_entries WHERE id = ?', [$id]) : null;
        $slug = Slug::uniqueEntry((int) $type['id'], $slugIn, $id ?: null);
        $status = Request::str('status') ?: 'draft';
        if (!in_array($status, ['draft', 'published', 'scheduled', 'unpublished'], true)) {
            $status = 'draft';
        }
        $data = [
            'post_type_id' => (int) $type['id'],
            'title' => $title,
            'slug' => $slug,
            'status' => $status,
            'fields_json' => Html::json($values),
            'sort_order' => Request::int('sort_order'),
            'author_id' => Auth::id(),
            'scheduled_at' => $status === 'scheduled' ? (Request::str('scheduled_at') ?: null) : null,
        ];
        // Excerpt, image and body are only rendered when the post type supports
        // them (and body only when the entry has no sections). Only write what
        // the form actually sent, so hiding a field never blanks stored data.
        foreach (['excerpt', 'featured_image'] as $k) {
            if (array_key_exists($k, $_POST)) {
                $data[$k] = Request::str($k);
            } elseif (!$id) {
                $data[$k] = '';
            }
        }
        if (array_key_exists('body_html', $_POST)) {
            $data['body_html'] = Html::allowedHtml((string) $_POST['body_html']);
        } elseif (!$id) {
            $data['body_html'] = null;
        }
        if ($id) {
            Database::update('cpt_entries', $data, 'id = ?', [$id]);
            if ($old && $old['slug'] !== $slug && $old['status'] === 'published' && (int) $type['public']) {
                Redirects::onSlugChange($type['slug'] . '/' . $old['slug'], $type['slug'] . '/' . $slug, 'Entry slug change');
            }
            Audit::log('entry.updated', 'cpt', $id);
        } else {
            $id = Database::insert('cpt_entries', $data);
            Audit::log('entry.created', 'cpt', $id);
            $tid = Request::int('template_id');
            if ($tid && in_array($type['template_mode'], ['builder', 'both'], true)) {
                Templates::apply('cpt', $id, $tid);
            }
        }
        if ($old && in_array($type['template_mode'], ['builder', 'both'], true)) {
            Content::saveSectionsFromPost('cpt', $id);
        }
        foreach ($fieldDefs as $fd) {
            if (($fd['type'] ?? '') !== 'relation') {
                continue;
            }
            $posted = $_POST['rel_' . $fd['name']] ?? [];
            Cpt::setRelations($id, (string) $fd['name'], is_array($posted) ? $posted : []);
        }
        if (Taxonomy::forType((int) $type['id'])) {
            Taxonomy::setEntryTerms($id, Taxonomy::termIdsFromRequest());
        }
        $seo = Content::seoFromRequest();
        $seo = Content::fillCanonical($seo, $type['slug'] . '/' . $slug);
        Database::upsertSeo('cpt', $id, $seo);
        Content::snapshot('cpt', $id, false, 'Draft saved');
        if ($status === 'published') {
            Content::publish('cpt', $id);
        }
        Cache::flush();
        if (Request::wantsJson()) {
            // Send the edit URL back. Without it an AJAX save of a *new* entry
            // leaves the create form on screen with no id, so a second click
            // silently creates a duplicate instead of updating.
            View::json([
                'ok' => true,
                'id' => $id,
                'slug' => $slug,
                'url' => Cpt::permalink(['slug' => $slug, 'post_type_id' => (int) $type['id']], $type),
                'redirect' => '/admin/content/' . $typeSlug . '/' . $id . '/',
            ]);
        }
        View::flash('success', 'Saved.');
        View::redirect('/admin/content/' . $typeSlug . '/' . $id . '/');
    }

    /** Apply one action to many entries at once. */
    public static function bulk(string $typeSlug): void
    {
        $type = Cpt::type($typeSlug);
        if (!$type) {
            View::redirect('/admin/post-types/');
        }
        $ids = array_values(array_filter(array_map('intval', (array) ($_POST['ids'] ?? [])), static fn ($i) => $i > 0));
        $action = Request::str('bulk_action');
        $back = '/admin/content/' . $typeSlug . '/';
        $fromTrash = Request::str('from') === 'trash';
        if (!$ids || $action === '') {
            View::flash('error', 'Pick at least one entry and an action.');
            View::redirect($back);
        }
        $perm = match ($action) {
            'publish', 'unpublish' => 'entries.publish',
            'trash', 'delete' => 'entries.delete',
            default => 'entries.edit',
        };
        Auth::requirePerm($perm);

        $in = implode(',', array_fill(0, count($ids), '?'));
        $scoped = array_merge($ids, [(int) $type['id']]);
        $n = 0;
        switch ($action) {
            case 'publish':
                foreach ($ids as $id) {
                    $own = Database::one('SELECT id FROM cpt_entries WHERE id = ? AND post_type_id = ?', [$id, (int) $type['id']]);
                    if ($own) {
                        Content::publish('cpt', $id);
                        $n++;
                    }
                }
                break;
            case 'unpublish':
                $n = Database::query(
                    'UPDATE cpt_entries SET status = "unpublished" WHERE id IN (' . $in . ') AND post_type_id = ?',
                    $scoped
                )->rowCount();
                break;
            case 'trash':
                $n = Database::query(
                    'UPDATE cpt_entries SET deleted_at = NOW(), status = "unpublished"
                     WHERE id IN (' . $in . ') AND post_type_id = ?',
                    $scoped
                )->rowCount();
                break;
            case 'restore':
                $n = Database::query(
                    'UPDATE cpt_entries SET deleted_at = NULL, status = "draft"
                     WHERE id IN (' . $in . ') AND post_type_id = ?',
                    $scoped
                )->rowCount();
                break;
            case 'delete':
                // Only entries already in the trash can be deleted for good.
                $ids = array_map('intval', array_column(Database::all(
                    'SELECT id FROM cpt_entries WHERE id IN (' . $in . ') AND post_type_id = ? AND deleted_at IS NOT NULL',
                    $scoped
                ), 'id'));
                if (!$ids) {
                    View::flash('error', 'Move entries to the trash before deleting them permanently.');
                    View::redirect($back . ($fromTrash ? '?trash=1' : ''));
                }
                $in = implode(',', array_fill(0, count($ids), '?'));
                $scoped = array_merge($ids, [(int) $type['id']]);
                Database::query('DELETE FROM content_sections WHERE owner_type = "cpt" AND owner_id IN (' . $in . ')', $ids);
                Database::query('DELETE FROM term_entries WHERE entry_id IN (' . $in . ')', $ids);
                Database::query('DELETE FROM entry_relations WHERE from_entry_id IN (' . $in . ') OR to_entry_id IN (' . $in . ')', array_merge($ids, $ids));
                $n = Database::query(
                    'DELETE FROM cpt_entries WHERE id IN (' . $in . ') AND post_type_id = ?',
                    $scoped
                )->rowCount();
                break;
            default:
                View::flash('error', 'Unknown action.');
                View::redirect($back);
        }
        Audit::log('entry.bulk', 'cpt', 0, ['action' => $action, 'count' => $n, 'type' => $typeSlug]);
        Cache::flush();
        $done = [
            'publish' => 'published', 'unpublish' => 'hidden (unpublished)', 'trash' => 'moved to trash',
            'restore' => 'restored as draft', 'delete' => 'deleted permanently',
        ][$action];
        View::flash('success', $n . ' ' . ($n === 1 ? 'entry' : 'entries') . ' ' . $done . '.');
        // Stay in the trash while working through it, unless it is now empty.
        $stayInTrash = $fromTrash && Database::one(
            'SELECT id FROM cpt_entries WHERE post_type_id = ? AND deleted_at IS NOT NULL LIMIT 1',
            [(int) $type['id']]
        );
        View::redirect($back . ($stayInTrash ? '?trash=1' : ''));
    }

    public static function addSection(string $typeSlug, string $id): void
    {
        Auth::requirePerm('entries.edit');
        Content::addSection(
            'cpt',
            (int) $id,
            Request::str('type'),
            Request::int('section_template_id') ?: null,
            Request::bool('linked')
        );
        View::redirect('/admin/content/' . $typeSlug . '/' . $id . '/#builder');
    }

    /**
     * Turn an entry's Body text into a Rich text section.
     *
     * Once a page has sections the site renders only those, so body text left
     * over from before is invisible. This moves it where it will show.
     */
    public static function bodyToSection(string $typeSlug, string $id): void
    {
        Auth::requirePerm('entries.edit');
        $type = Cpt::type($typeSlug);
        $row = $type ? Database::one(
            'SELECT * FROM cpt_entries WHERE id = ? AND post_type_id = ?',
            [(int) $id, (int) $type['id']]
        ) : null;
        $back = '/admin/content/' . $typeSlug . '/' . (int) $id . '/#builder';
        if (!$row || trim(strip_tags((string) $row['body_html'])) === '') {
            View::flash('error', 'There is no body text to move.');
            View::redirect($back);
        }
        $sid = Content::addSection('cpt', (int) $id, 'rich_text');
        Database::update('content_sections', [
            'content_json' => Html::json(['html' => Html::allowedHtml((string) $row['body_html'])]),
        ], 'id = ?', [$sid]);
        Database::update('cpt_entries', ['body_html' => null], 'id = ?', [(int) $id]);
        Content::snapshot('cpt', (int) $id, false, 'Body moved into a Rich text section');
        Audit::log('entry.body_to_section', 'cpt', (int) $id, ['section' => $sid]);
        View::flash('success', 'Body text moved into a new Rich text section at the end of the page. Publish to make it live.');
        View::redirect($back);
    }

    /** Break a global block's link so this page can customise its copy. */
    public static function unlinkSection(string $typeSlug, string $id): void
    {
        Auth::requirePerm('entries.edit');
        $sid = Request::int('section_id');
        $sec = Database::one(
            'SELECT * FROM content_sections WHERE id = ? AND owner_type="cpt" AND owner_id = ?',
            [$sid, (int) $id]
        );
        if ($sec && !empty($sec['is_linked']) && !empty($sec['section_template_id'])) {
            $tpl = Database::one('SELECT * FROM section_templates WHERE id = ?', [(int) $sec['section_template_id']]);
            Database::update('content_sections', [
                'is_linked' => 0,
                'content_json' => $tpl['content_json'] ?? $sec['content_json'],
            ], 'id = ?', [$sid]);
            View::flash('success', 'Unlinked — this copy is now independent of the global block.');
        }
        View::redirect('/admin/content/' . $typeSlug . '/' . $id . '/#builder');
    }

    public static function saveSectionTemplate(string $typeSlug, string $id): void
    {
        Auth::requirePerm('templates.create');
        $sec = Database::one(
            'SELECT * FROM content_sections WHERE id = ? AND owner_type="cpt" AND owner_id = ?',
            [Request::int('section_id'), (int) $id]
        );
        if ($sec) {
            Templates::saveSectionAsTemplate($sec, Request::str('name'));
            View::flash('success', 'Saved as a section template. Find it under Templates.');
        }
        View::redirect('/admin/content/' . $typeSlug . '/' . $id . '/#builder');
    }

    public static function deleteSection(string $typeSlug, string $id): void
    {
        Auth::requirePerm('entries.edit');
        Database::delete('content_sections', 'id = ? AND owner_type="cpt" AND owner_id = ?', [Request::int('section_id'), (int) $id]);
        View::redirect('/admin/content/' . $typeSlug . '/' . $id . '/#builder');
    }

    public static function publish(string $typeSlug, string $id): void
    {
        Auth::requirePerm('entries.publish');
        Content::publish('cpt', (int) $id);
        Audit::log('entry.published', 'cpt', (int) $id);
        View::flash('success', 'Published.');
        View::redirect('/admin/content/' . $typeSlug . '/' . $id . '/');
    }

    public static function preview(string $typeSlug, string $id): void
    {
        Auth::requirePerm('entries.view');
        $type = Cpt::type($typeSlug);
        $row = Database::one('SELECT slug FROM cpt_entries WHERE id = ?', [(int) $id]);
        if (!$type || !$row) {
            http_response_code(404);
            View::admin('errors/404', ['title' => 'Not found']);
            return;
        }
        View::redirect(Content::previewUrl('cpt', (int) $id, $type['slug'] . '/' . $row['slug']));
    }

    public static function trash(string $typeSlug): void
    {
        Auth::requirePerm('entries.delete');
        $id = Request::int('id');
        $links = Menu::linksTo('cpt_entry', 'cpt', $id);
        if ($links && !Request::bool('confirm_links')) {
            View::flash('error', 'This entry is linked from a menu. Confirm to trash it.');
            View::redirect('/admin/content/' . $typeSlug . '/' . $id . '/');
        }
        Database::update('cpt_entries', ['deleted_at' => date('Y-m-d H:i:s'), 'status' => 'unpublished'], 'id = ?', [$id]);
        Cache::flush();
        Audit::log('entry.trashed', 'cpt', $id);
        View::flash('success', 'Moved to trash.');
        View::redirect('/admin/content/' . $typeSlug . '/');
    }

    public static function restore(string $typeSlug): void
    {
        Auth::requirePerm('entries.edit');
        $id = Request::int('id');
        Database::update('cpt_entries', ['deleted_at' => null, 'status' => 'draft'], 'id = ?', [$id]);
        View::flash('success', 'Restored as a draft.');
        View::redirect('/admin/content/' . $typeSlug . '/');
    }

    public static function destroy(string $typeSlug): void
    {
        Auth::requirePerm('entries.delete');
        $type = Cpt::type($typeSlug);
        $id = Request::int('id');
        $row = $type ? Database::one(
            'SELECT id FROM cpt_entries WHERE id = ? AND post_type_id = ? AND deleted_at IS NOT NULL',
            [$id, (int) $type['id']]
        ) : null;
        if (!$row) {
            View::flash('error', 'Move the entry to the trash before deleting it permanently.');
            View::redirect('/admin/content/' . $typeSlug . '/?trash=1');
        }
        Database::query('DELETE FROM content_sections WHERE owner_type = "cpt" AND owner_id = ?', [$id]);
        Database::query('DELETE FROM term_entries WHERE entry_id = ?', [$id]);
        Database::query('DELETE FROM entry_relations WHERE from_entry_id = ? OR to_entry_id = ?', [$id, $id]);
        Database::delete('cpt_entries', 'id = ?', [$id]);
        Cache::flush();
        Audit::log('entry.deleted', 'cpt', $id);
        View::flash('success', 'Deleted permanently.');
        View::redirect('/admin/content/' . $typeSlug . '/?trash=1');
    }
}
