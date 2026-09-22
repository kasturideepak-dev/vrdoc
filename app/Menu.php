<?php
declare(strict_types=1);

final class Menu
{
    /**
     * Top-level items, each with a `_children` list — the shape the site's
     * header and footer render (one dropdown level).
     *
     * @return list<array<string,mixed>>
     */
    public static function tree(string $slug): array
    {
        $rows = self::items($slug);
        $kids = [];
        foreach ($rows as $r) {
            if (!empty($r['parent_id'])) {
                $kids[(int) $r['parent_id']][] = $r;
            }
        }
        $out = [];
        foreach ($rows as $r) {
            if (empty($r['parent_id'])) {
                $r['_children'] = $kids[(int) $r['id']] ?? [];
                $out[] = $r;
            }
        }
        return $out;
    }

    /**
     * One-time sync before the site/ header and footer switched from
     * hardcoded links to the Menus admin. Their hardcoded lists showed a Blog
     * link that the stored menus never had, so switching over as-is would
     * have silently removed Blog from the live navigation. Adds it once, after
     * Results; a settings flag means deleting it afterwards sticks.
     */
    public static function ensureNavParity(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        try {
            if (Settings::get('menus_nav_parity_v1') === '1') {
                return;
            }
            // Two first visits at once must not both add Blog.
            if (!(int) (Database::one('SELECT GET_LOCK("vr_menu_parity", 3) l')['l'] ?? 0)) {
                return;
            }
            Settings::reload();
            if (Settings::get('menus_nav_parity_v1') === '1') {
                Database::query('SELECT RELEASE_LOCK("vr_menu_parity")');
                return;
            }
            foreach (['header', 'footer'] as $slug) {
                $menu = Database::one('SELECT id FROM menus WHERE slug = ?', [$slug]);
                if (!$menu) {
                    continue;
                }
                $mid = (int) $menu['id'];
                $hasBlog = Database::one(
                    'SELECT id FROM menu_items WHERE menu_id = ? AND (link_type = "blog_index" OR url IN ("/blog/", "/blog"))',
                    [$mid]
                );
                if ($hasBlog) {
                    continue;
                }
                $results = Database::one(
                    'SELECT sort_order FROM menu_items WHERE menu_id = ? AND parent_id IS NULL AND url IN ("/results/", "/results") ORDER BY sort_order LIMIT 1',
                    [$mid]
                );
                $at = $results ? (int) $results['sort_order'] + 1
                    : (int) (Database::one('SELECT MAX(sort_order) m FROM menu_items WHERE menu_id = ?', [$mid])['m'] ?? 0) + 1;
                Database::query('UPDATE menu_items SET sort_order = sort_order + 1 WHERE menu_id = ? AND sort_order >= ?', [$mid, $at]);
                Database::insert('menu_items', [
                    'menu_id' => $mid,
                    'label' => 'Blog',
                    'url' => '/blog/',
                    'link_type' => 'blog_index',
                    'sort_order' => $at,
                    'is_active' => 1,
                ]);
            }
            Settings::set('menus_nav_parity_v1', '1');
            Database::query('SELECT RELEASE_LOCK("vr_menu_parity")');
        } catch (Throwable $e) {
            error_log('Menu::ensureNavParity: ' . $e->getMessage());
        }
    }

    /**
     * Visible items of a menu with resolved URLs, in order. Links whose target
     * is not live (draft or trashed page, archive switched off…) are left out,
     * so the site never shows a link that 404s; a parent that is not live stays
     * only as the label for live dropdown links. The admin shows why an item is
     * missing (see status()).
     */
    public static function items(string $slug, bool $includeDead = false): array
    {
        self::ensureNavParity();
        $menu = Database::one('SELECT id FROM menus WHERE slug = ?', [$slug]);
        if (!$menu) {
            return [];
        }
        $rows = Database::all(
            'SELECT * FROM menu_items WHERE menu_id = ? AND is_active = 1 ORDER BY sort_order, id',
            [(int) $menu['id']]
        );
        $out = [];
        foreach ($rows as $row) {
            $st = self::status($row);
            $row['url'] = $st['url'];
            $row['_live'] = $st['live'];
            $out[(int) $row['id']] = $row;
        }
        if ($includeDead) {
            return array_values($out);
        }
        // Children of a hidden or missing parent have nowhere to render.
        foreach ($out as $id => $row) {
            $pid = (int) ($row['parent_id'] ?? 0);
            if ($pid && !isset($out[$pid])) {
                unset($out[$id]);
            }
        }
        $live = [];
        foreach ($out as $row) {
            if (!empty($row['parent_id']) && $row['_live']) {
                $live[(int) $row['parent_id']] = true;
            }
        }
        foreach ($out as $id => $row) {
            if ($row['_live']) {
                continue;
            }
            if (empty($row['parent_id']) && isset($live[$id])) {
                $out[$id]['url'] = '#';
                continue;
            }
            unset($out[$id]);
        }
        return array_values($out);
    }

    /**
     * Where an item points and whether that target is currently live.
     *
     * @return array{url:string, live:bool, why:string}
     */
    public static function status(array $item): array
    {
        $type = $item['link_type'] ?? 'custom';
        $oid = (int) ($item['object_id'] ?? 0);
        $stored = self::safeUrl((string) ($item['url'] ?? ''));
        switch ($type) {
            case 'page':
                $p = Database::one('SELECT slug, status, deleted_at FROM pages WHERE id = ?', [$oid]);
                if (!$p || $p['deleted_at'] !== null) {
                    return ['url' => $stored, 'live' => false, 'why' => 'The page was deleted'];
                }
                $url = $p['slug'] === '/' ? '/' : path_url($p['slug']);
                return $p['status'] === 'published'
                    ? ['url' => $url, 'live' => true, 'why' => '']
                    : ['url' => $url, 'live' => false, 'why' => 'The page is not published'];
            case 'cpt_archive':
                $t = Database::one('SELECT name, slug, public, has_archive, status FROM post_types WHERE id = ?', [$oid]);
                if (!$t || $t['status'] !== 'active' || !(int) $t['public']) {
                    return ['url' => $stored, 'live' => false, 'why' => 'The post type is removed or not public'];
                }
                $url = path_url($t['slug']);
                return (int) $t['has_archive']
                    ? ['url' => $url, 'live' => true, 'why' => '']
                    : ['url' => $url, 'live' => false, 'why' => '“' . $t['name'] . '” has no archive page — turn on “Archive at /' . $t['slug'] . '/” in Post types'];
            case 'cpt_entry':
                $e = Database::one(
                    'SELECT e.slug, e.status, e.deleted_at, t.slug AS type_slug, t.public, t.status AS type_status
                     FROM cpt_entries e JOIN post_types t ON t.id = e.post_type_id WHERE e.id = ?',
                    [$oid]
                );
                if (!$e || $e['deleted_at'] !== null) {
                    return ['url' => $stored, 'live' => false, 'why' => 'The entry was deleted'];
                }
                if (!(int) $e['public'] || $e['type_status'] !== 'active') {
                    return ['url' => $stored, 'live' => false, 'why' => 'Its post type is not public'];
                }
                $url = path_url($e['type_slug'] . '/' . $e['slug']);
                return $e['status'] === 'published'
                    ? ['url' => $url, 'live' => true, 'why' => '']
                    : ['url' => $url, 'live' => false, 'why' => 'The entry is not published'];
            case 'blog_index':
                return ['url' => path_url('/blog/'), 'live' => true, 'why' => ''];
            case 'blog_post':
                $b = Database::one('SELECT slug, status, deleted_at FROM blog_posts WHERE id = ?', [$oid]);
                if (!$b || $b['deleted_at'] !== null) {
                    return ['url' => $stored, 'live' => false, 'why' => 'The post was deleted'];
                }
                $url = path_url('blog/' . $b['slug']);
                return $b['status'] === 'published'
                    ? ['url' => $url, 'live' => true, 'why' => '']
                    : ['url' => $url, 'live' => false, 'why' => 'The post is not published'];
            default:
                return $stored === '#' && trim((string) ($item['url'] ?? '')) !== '' && trim((string) $item['url']) !== '#'
                    ? ['url' => '#', 'live' => false, 'why' => 'This URL type is not allowed']
                    : ['url' => $stored, 'live' => true, 'why' => ''];
        }
    }

    public static function resolve(array $item): string
    {
        return self::status($item)['url'];
    }

    /**
     * Only web, mail and phone links, or site-relative paths. Blocks
     * javascript:/data: URLs that would otherwise run in visitors' browsers.
     */
    public static function safeUrl(string $url): string
    {
        // Browsers read "\" as "/", so "/\\evil.com" would leave the site;
        // control characters can hide a scheme ("java\tscript:").
        $url = str_replace(['\\', ' '], ['/', '%20'], preg_replace('/[\x00-\x1f\x7f]+/', '', trim($url)));
        if ($url === '') {
            return '#';
        }
        if (preg_match('#^(/|\#|\?)#', $url) && !str_starts_with($url, '//')) {
            return $url;
        }
        if (preg_match('#^(https?:)?//[^/\s]+#i', $url) || preg_match('#^(mailto|tel):#i', $url)) {
            return $url;
        }
        // "about/" or "contact" — treat as site-relative.
        if (!preg_match('#^[a-z][a-z0-9+.-]*:#i', $url)) {
            return '/' . ltrim($url, '/');
        }
        return '#';
    }

    public static function linksTo(string $linkType, string $objectType, int $objectId): array
    {
        return Database::all(
            'SELECT mi.*, m.name AS menu_name FROM menu_items mi
             JOIN menus m ON m.id = mi.menu_id
             WHERE mi.link_type = ? AND (mi.object_type = ? OR mi.object_type IS NULL) AND mi.object_id = ?',
            [$linkType, $objectType, $objectId]
        );
    }
}
