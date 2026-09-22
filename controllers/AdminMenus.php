<?php
declare(strict_types=1);

final class AdminMenus
{
    /** link_type => object_type, as stored and as Menu::linksTo()/Cpt::references() expect. */
    private const OBJECT_TYPES = [
        'page' => 'page',
        'cpt_entry' => 'cpt',
        'cpt_archive' => 'post_type',
        'blog_post' => 'blog',
    ];

    public static function index(): void
    {
        Auth::requirePerm('menus.view');
        $menus = Database::all('SELECT * FROM menus ORDER BY id');
        $id = Request::int('id') ?: (int) ($menus[0]['id'] ?? 0);
        $rows = $id ? Database::all('SELECT * FROM menu_items WHERE menu_id = ? ORDER BY sort_order, id', [$id]) : [];

        // Arrange as a tree: top-level items, each followed by its children.
        $byParent = [];
        foreach ($rows as $r) {
            $byParent[(int) ($r['parent_id'] ?? 0)][] = $r;
        }
        $tree = [];
        foreach ($byParent[0] ?? [] as $top) {
            $top['_children'] = $byParent[(int) $top['id']] ?? [];
            $top += self::statusFields($top);
            foreach ($top['_children'] as &$ch) {
                $ch += self::statusFields($ch);
            }
            unset($ch);
            $tree[] = $top;
        }
        // Items whose parent no longer exists would otherwise vanish from the editor.
        $known = array_column($rows, 'id');
        foreach ($rows as $r) {
            $pid = (int) ($r['parent_id'] ?? 0);
            if ($pid && !in_array($pid, array_map('intval', $known), true)) {
                $r['_children'] = [];
                $r += self::statusFields($r);
                $r['_orphan'] = true;
                $tree[] = $r;
            }
        }

        View::admin('menus/index', [
            'title' => 'Menus',
            'menus' => $menus,
            'menuId' => $id,
            'tree' => $tree,
            'itemCount' => count($rows),
            'targets' => self::targets(),
        ]);
    }

    /** Resolved URL plus, when the link will not appear on the site, why. */
    private static function statusFields(array $item): array
    {
        $st = Menu::status($item);
        return ['_url' => $st['url'], '_dead' => $st['live'] ? '' : $st['why']];
    }

    /**
     * Everything a menu item can point at, grouped for the "Links to" picker.
     * Values are "<link_type>:<object_id>" (or "custom" / "blog_index:0").
     *
     * @return array<string, array<string, string>>
     */
    private static function targets(): array
    {
        $out = [
            'General' => ['custom' => 'Custom URL…', 'blog_index:0' => 'Blog (all posts)'],
        ];
        foreach (Database::all('SELECT id, title, slug FROM pages WHERE deleted_at IS NULL AND status = "published" ORDER BY title') as $p) {
            $out['Pages']['page:' . (int) $p['id']] = $p['title'];
        }
        foreach (Cpt::publicTypes() as $t) {
            // Without an archive, /{slug}/ is a 404 — offering it made menu links
            // that looked saved but led nowhere.
            if ((int) $t['has_archive']) {
                $out['Post type archives']['cpt_archive:' . (int) $t['id']] = $t['name'] . ' (all)';
            }
        }
        foreach (Database::all(
            'SELECT e.id, e.title, t.name AS type_name FROM cpt_entries e
             JOIN post_types t ON t.id = e.post_type_id
             WHERE e.deleted_at IS NULL AND e.status = "published" AND t.public = 1 AND t.status = "active"
             ORDER BY t.name, e.title'
        ) as $e) {
            $out[$e['type_name']]['cpt_entry:' . (int) $e['id']] = $e['title'];
        }
        foreach (Database::all('SELECT id, title FROM blog_posts WHERE deleted_at IS NULL AND status = "published" ORDER BY title') as $b) {
            $out['Blog posts']['blog_post:' . (int) $b['id']] = $b['title'];
        }
        return $out;
    }

    /** "page:12" → [link_type, object_type, object_id]. */
    private static function parseTarget(string $target): array
    {
        if ($target === '' || $target === 'custom') {
            return ['custom', null, null];
        }
        [$type, $oid] = array_pad(explode(':', $target, 2), 2, '0');
        $allowed = ['page', 'cpt_entry', 'cpt_archive', 'blog_post', 'blog_index'];
        if (!in_array($type, $allowed, true)) {
            return ['custom', null, null];
        }
        $oid = (int) $oid;
        return [$type, self::OBJECT_TYPES[$type] ?? null, $type === 'blog_index' ? null : ($oid ?: null)];
    }

    public static function save(): void
    {
        Auth::requirePerm('menus.edit');
        $menuId = Request::int('menu_id');
        if (!Database::one('SELECT id FROM menus WHERE id = ?', [$menuId])) {
            View::flash('error', 'Menu not found.');
            View::redirect('/admin/menus/');
        }
        $ids = $_POST['item_id'] ?? [];
        $labels = $_POST['label'] ?? [];
        $targets = $_POST['target'] ?? [];
        $urls = $_POST['url'] ?? [];
        $parents = $_POST['parent_id'] ?? [];
        $actives = $_POST['is_active'] ?? [];
        $owned = array_map('intval', array_column(
            Database::all('SELECT id FROM menu_items WHERE menu_id = ?', [$menuId]),
            'id'
        ));

        // Position in the posted list is the order — the editor is drag-sorted.
        $pos = 0;
        foreach ($ids as $i => $iid) {
            $iid = (int) $iid;
            if (!in_array($iid, $owned, true)) {
                continue;
            }
            [$lt, $ot, $oid] = self::parseTarget((string) ($targets[$i] ?? 'custom'));
            $item = ['link_type' => $lt, 'object_id' => $oid, 'url' => trim((string) ($urls[$i] ?? ''))];
            $parent = (int) ($parents[$i] ?? 0);
            // One level of nesting (the header renders one dropdown level), and
            // never under itself.
            if ($parent === $iid || !in_array($parent, $owned, true)) {
                $parent = 0;
            }
            Database::update('menu_items', [
                'label' => mb_substr(trim((string) ($labels[$i] ?? '')), 0, 120) ?: 'Untitled',
                'link_type' => $lt,
                'object_type' => $ot,
                'object_id' => $oid,
                // Keep the resolved address as a fallback in case the linked
                // page or entry is later removed.
                'url' => mb_substr($lt === 'custom' ? Menu::safeUrl($item['url'] ?: '/') : Menu::resolve($item), 0, 255),
                'parent_id' => $parent ?: null,
                'sort_order' => $pos++,
                'is_active' => isset($actives[$iid]) ? 1 : 0,
            ], 'id = ? AND menu_id = ?', [$iid, $menuId]);
        }

        // A child cannot itself have children — flatten anything deeper.
        Database::query(
            'UPDATE menu_items c JOIN menu_items p ON p.id = c.parent_id
             SET c.parent_id = p.parent_id
             WHERE c.menu_id = ? AND p.parent_id IS NOT NULL',
            [$menuId]
        );

        $newLabel = Request::str('new_label');
        if ($newLabel !== '') {
            [$lt, $ot, $oid] = self::parseTarget(Request::str('new_target'));
            $newParent = Request::int('new_parent');
            $item = ['link_type' => $lt, 'object_id' => $oid, 'url' => Request::str('new_url')];
            $max = Database::one('SELECT MAX(sort_order) m FROM menu_items WHERE menu_id = ?', [$menuId]);
            Database::insert('menu_items', [
                'menu_id' => $menuId,
                'label' => mb_substr($newLabel, 0, 120),
                'link_type' => $lt,
                'object_type' => $ot,
                'object_id' => $oid,
                'url' => mb_substr($lt === 'custom' ? Menu::safeUrl(Request::str('new_url') ?: '/') : Menu::resolve($item), 0, 255),
                'parent_id' => in_array($newParent, $owned, true) ? $newParent : null,
                'sort_order' => (int) ($max['m'] ?? 0) + 1,
                'is_active' => 1,
            ]);
        }

        Cache::flush();
        Audit::log('menu.updated', 'menu', $menuId);
        View::flash('success', 'Menu saved.');
        View::redirect('/admin/menus/?id=' . $menuId);
    }

    public static function itemDelete(): void
    {
        Auth::requirePerm('menus.edit');
        $id = Request::int('id');
        $item = Database::one('SELECT * FROM menu_items WHERE id = ?', [$id]);
        if ($item) {
            // Promote children instead of leaving them pointing at nothing.
            Database::query('UPDATE menu_items SET parent_id = NULL WHERE parent_id = ?', [$id]);
            Database::delete('menu_items', 'id = ?', [$id]);
            Cache::flush();
            Audit::log('menu.item_deleted', 'menu', (int) $item['menu_id'], ['label' => $item['label']]);
            View::flash('success', 'Removed “' . $item['label'] . '”.');
        }
        View::redirect('/admin/menus/?id=' . (int) ($item['menu_id'] ?? 0));
    }
}
