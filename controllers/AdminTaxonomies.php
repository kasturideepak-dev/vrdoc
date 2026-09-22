<?php
declare(strict_types=1);

/** Taxonomies and their terms, managed per post type. */
final class AdminTaxonomies
{
    public static function index(string $typeId): void
    {
        Auth::requirePerm('post_types.view');
        Cpt::ensureSchema();
        $type = Cpt::typeById((int) $typeId);
        if (!$type) {
            View::flash('error', 'Post type not found.');
            View::redirect('/admin/post-types/');
        }
        $taxes = Taxonomy::forType((int) $type['id']);
        foreach ($taxes as &$t) {
            $t['terms'] = Taxonomy::terms((int) $t['id']);
        }
        unset($t);
        View::admin('taxonomies/index', [
            'title' => $type['name'] . ' — taxonomies',
            'type' => $type,
            'taxonomies' => $taxes,
        ]);
    }

    public static function save(string $typeId): void
    {
        Auth::requirePerm('post_types.edit');
        Cpt::ensureSchema();
        $type = Cpt::typeById((int) $typeId);
        if (!$type) {
            View::redirect('/admin/post-types/');
        }
        $id = Request::int('id');
        $name = Request::str('name');
        if ($name === '') {
            View::flash('error', 'Give the taxonomy a name.');
            View::redirect('/admin/post-types/' . (int) $type['id'] . '/taxonomies/');
        }
        $singular = Request::str('singular_name') ?: $name;
        $slug = Slug::make(Request::str('slug') ?: $name);
        $data = [
            'post_type_id' => (int) $type['id'],
            'name' => $name,
            'singular_name' => $singular,
            'slug' => $slug,
            'description' => Request::str('description'),
            'hierarchical' => Request::bool('hierarchical') ? 1 : 0,
            'public' => Request::bool('public') ? 1 : 0,
            'sort_order' => Request::int('sort_order'),
        ];
        try {
            if ($id) {
                Database::update('taxonomies', $data, 'id = ? AND post_type_id = ?', [$id, (int) $type['id']]);
            } else {
                $id = Database::insert('taxonomies', $data);
            }
        } catch (Throwable $e) {
            View::flash('error', 'A taxonomy with that slug already exists on this post type.');
            View::redirect('/admin/post-types/' . (int) $type['id'] . '/taxonomies/');
        }
        Audit::log('taxonomy.saved', 'taxonomy', $id, ['post_type' => $type['slug']]);
        Cache::flush();
        View::flash('success', 'Taxonomy saved.');
        View::redirect('/admin/post-types/' . (int) $type['id'] . '/taxonomies/');
    }

    public static function delete(string $typeId): void
    {
        Auth::requirePerm('post_types.edit');
        Cpt::ensureSchema();
        $id = Request::int('id');
        $tax = Taxonomy::find($id);
        if ($tax) {
            $terms = Taxonomy::terms($id);
            foreach ($terms as $t) {
                Database::delete('term_entries', 'term_id = ?', [(int) $t['id']]);
            }
            Database::delete('terms', 'taxonomy_id = ?', [$id]);
            Database::delete('taxonomies', 'id = ?', [$id]);
            Audit::log('taxonomy.deleted', 'taxonomy', $id, ['name' => $tax['name'], 'terms' => count($terms)]);
            View::flash('success', 'Deleted “' . $tax['name'] . '” and ' . count($terms) . ' term(s).');
        }
        Cache::flush();
        View::redirect('/admin/post-types/' . (int) $typeId . '/taxonomies/');
    }

    public static function termSave(string $typeId): void
    {
        Auth::requirePerm('post_types.edit');
        Cpt::ensureSchema();
        $taxId = Request::int('taxonomy_id');
        $tax = Taxonomy::find($taxId);
        if (!$tax) {
            View::redirect('/admin/post-types/' . (int) $typeId . '/taxonomies/');
        }
        $id = Request::int('id');
        $name = Request::str('name');
        if ($name === '') {
            View::flash('error', 'Give the term a name.');
            View::redirect('/admin/post-types/' . (int) $typeId . '/taxonomies/');
        }
        $slug = Slug::make(Request::str('slug') ?: $name);
        // Keep the slug unique inside its taxonomy.
        $base = $slug;
        $n = 2;
        while (true) {
            $clash = Database::one(
                'SELECT id FROM terms WHERE taxonomy_id = ? AND slug = ?' . ($id ? ' AND id <> ?' : ''),
                $id ? [$taxId, $slug, $id] : [$taxId, $slug]
            );
            if (!$clash) {
                break;
            }
            $slug = $base . '-' . $n++;
        }
        $parent = Request::int('parent_id');
        $data = [
            'taxonomy_id' => $taxId,
            'parent_id' => ((int) $tax['hierarchical'] === 1 && $parent > 0 && $parent !== $id) ? $parent : null,
            'name' => $name,
            'slug' => $slug,
            'description' => Request::str('description'),
            'sort_order' => Request::int('sort_order'),
        ];
        if ($id) {
            Database::update('terms', $data, 'id = ? AND taxonomy_id = ?', [$id, $taxId]);
        } else {
            $id = Database::insert('terms', $data);
        }
        Audit::log('term.saved', 'term', $id, ['taxonomy' => $tax['slug']]);
        Cache::flush();
        View::flash('success', 'Term saved.');
        View::redirect('/admin/post-types/' . (int) $typeId . '/taxonomies/');
    }

    public static function termDelete(string $typeId): void
    {
        Auth::requirePerm('post_types.edit');
        Cpt::ensureSchema();
        $id = Request::int('id');
        if ($id) {
            Database::query('UPDATE terms SET parent_id = NULL WHERE parent_id = ?', [$id]);
            Database::delete('term_entries', 'term_id = ?', [$id]);
            Database::delete('terms', 'id = ?', [$id]);
            Audit::log('term.deleted', 'term', $id);
            View::flash('success', 'Term deleted.');
        }
        Cache::flush();
        View::redirect('/admin/post-types/' . (int) $typeId . '/taxonomies/');
    }
}
