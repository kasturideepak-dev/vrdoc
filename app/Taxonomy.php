<?php
declare(strict_types=1);

/**
 * Taxonomies and terms for custom post types.
 *
 * The blog shipped with its own categories/tags tables; nothing else could be
 * categorised. This gives any post type the same ability, with archives at
 * /{type}/{taxonomy}/{term}/.
 */
final class Taxonomy
{
    /** Create the tables on installs that pre-date them. */
    public static function ensureSchema(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        try {
            Database::pdo()->exec(
                'CREATE TABLE IF NOT EXISTS taxonomies (
                  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                  post_type_id INT UNSIGNED NOT NULL,
                  name VARCHAR(120) NOT NULL,
                  singular_name VARCHAR(120) NOT NULL,
                  slug VARCHAR(80) NOT NULL,
                  description VARCHAR(255) NULL,
                  hierarchical TINYINT(1) NOT NULL DEFAULT 0,
                  public TINYINT(1) NOT NULL DEFAULT 1,
                  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (id),
                  UNIQUE KEY uq_tax_type_slug (post_type_id, slug),
                  KEY idx_tax_type (post_type_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
            );
            Database::pdo()->exec(
                'CREATE TABLE IF NOT EXISTS terms (
                  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                  taxonomy_id INT UNSIGNED NOT NULL,
                  parent_id INT UNSIGNED NULL,
                  name VARCHAR(190) NOT NULL,
                  slug VARCHAR(190) NOT NULL,
                  description TEXT NULL,
                  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (id),
                  UNIQUE KEY uq_term_tax_slug (taxonomy_id, slug),
                  KEY idx_term_tax (taxonomy_id),
                  KEY idx_term_parent (parent_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
            );
            Database::pdo()->exec(
                'CREATE TABLE IF NOT EXISTS term_entries (
                  term_id INT UNSIGNED NOT NULL,
                  entry_id INT UNSIGNED NOT NULL,
                  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                  PRIMARY KEY (term_id, entry_id),
                  KEY idx_te_entry (entry_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
            );
            Database::pdo()->exec(
                'CREATE TABLE IF NOT EXISTS entry_relations (
                  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                  from_entry_id INT UNSIGNED NOT NULL,
                  to_entry_id INT UNSIGNED NOT NULL,
                  field_name VARCHAR(60) NOT NULL,
                  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                  PRIMARY KEY (id),
                  UNIQUE KEY uq_rel (from_entry_id, to_entry_id, field_name),
                  KEY idx_rel_from (from_entry_id, field_name),
                  KEY idx_rel_to (to_entry_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
            );
            if (!Database::all("SHOW COLUMNS FROM content_sections LIKE 'is_linked'")) {
                Database::pdo()->exec(
                    'ALTER TABLE content_sections ADD COLUMN is_linked TINYINT(1) NOT NULL DEFAULT 0 AFTER section_template_id'
                );
            }
        } catch (Throwable $e) {
            error_log('Taxonomy::ensureSchema: ' . $e->getMessage());
        }
    }

    /** @return list<array<string,mixed>> */
    public static function forType(int $postTypeId): array
    {
        try {
            return Database::all(
                'SELECT * FROM taxonomies WHERE post_type_id = ? ORDER BY sort_order, name',
                [$postTypeId]
            );
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM taxonomies WHERE id = ?', [$id]);
    }

    public static function bySlug(int $postTypeId, string $slug): ?array
    {
        return Database::one(
            'SELECT * FROM taxonomies WHERE post_type_id = ? AND slug = ?',
            [$postTypeId, $slug]
        );
    }

    /** @return list<array<string,mixed>> */
    public static function terms(int $taxonomyId): array
    {
        try {
            return Database::all(
                'SELECT t.*, (SELECT COUNT(*) FROM term_entries te WHERE te.term_id = t.id) AS entry_count
                 FROM terms t WHERE t.taxonomy_id = ? ORDER BY t.sort_order, t.name',
                [$taxonomyId]
            );
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function term(int $taxonomyId, string $slug): ?array
    {
        return Database::one(
            'SELECT * FROM terms WHERE taxonomy_id = ? AND slug = ?',
            [$taxonomyId, $slug]
        );
    }

    /** Term ids currently attached to an entry. @return list<int> */
    public static function entryTermIds(int $entryId): array
    {
        try {
            return array_map(
                static fn ($r) => (int) $r['term_id'],
                Database::all('SELECT term_id FROM term_entries WHERE entry_id = ?', [$entryId])
            );
        } catch (Throwable $e) {
            return [];
        }
    }

    /** Terms attached to an entry, with their taxonomy. */
    public static function entryTerms(int $entryId): array
    {
        try {
            return Database::all(
                'SELECT t.*, x.slug AS taxonomy_slug, x.name AS taxonomy_name
                 FROM term_entries te
                 JOIN terms t ON t.id = te.term_id
                 JOIN taxonomies x ON x.id = t.taxonomy_id
                 WHERE te.entry_id = ? ORDER BY x.sort_order, t.sort_order, t.name',
                [$entryId]
            );
        } catch (Throwable $e) {
            return [];
        }
    }

    /** Replace an entry's term assignments with exactly these ids. */
    public static function setEntryTerms(int $entryId, array $termIds): void
    {
        try {
            Database::delete('term_entries', 'entry_id = ?', [$entryId]);
            $seen = [];
            foreach ($termIds as $i => $tid) {
                $tid = (int) $tid;
                if ($tid <= 0 || isset($seen[$tid])) {
                    continue;
                }
                $seen[$tid] = true;
                Database::insert('term_entries', [
                    'term_id' => $tid,
                    'entry_id' => $entryId,
                    'sort_order' => (int) $i,
                ]);
            }
        } catch (Throwable $e) {
            error_log('Taxonomy::setEntryTerms: ' . $e->getMessage());
        }
    }

    /** Published entries carrying a term, newest first. */
    public static function entriesForTerm(int $termId, int $limit = 100): array
    {
        $limit = max(1, min(200, $limit));
        try {
            $rows = Database::all(
                'SELECT e.* FROM term_entries te
                 JOIN cpt_entries e ON e.id = te.entry_id
                 WHERE te.term_id = ? AND e.status = "published" AND e.deleted_at IS NULL
                 ORDER BY e.sort_order, COALESCE(e.published_at, e.created_at) DESC
                 LIMIT ' . $limit,
                [$termId]
            );
        } catch (Throwable $e) {
            return [];
        }
        foreach ($rows as &$r) {
            $r['_fields'] = json_decode($r['fields_json'] ?: '{}', true) ?: [];
        }
        return $rows;
    }

    public static function archiveUrl(array $type, array $taxonomy, array $term): string
    {
        return path_url($type['slug'] . '/' . $taxonomy['slug'] . '/' . $term['slug']);
    }

    /** Terms posted from an entry form, as ints. @return list<int> */
    public static function termIdsFromRequest(): array
    {
        $raw = $_POST['terms'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $v) {
            if (is_array($v)) {
                foreach ($v as $vv) {
                    $out[] = (int) $vv;
                }
                continue;
            }
            $out[] = (int) $v;
        }
        return array_values(array_filter($out, static fn ($i) => $i > 0));
    }
}
