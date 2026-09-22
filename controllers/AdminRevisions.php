<?php
declare(strict_types=1);

/**
 * Side-by-side revision comparison.
 *
 * Revisions were already stored and restorable, but there was no way to see
 * what actually changed before restoring — you had to restore and look.
 */
final class AdminRevisions
{
    public static function compare(string $ownerType, string $ownerId): void
    {
        Auth::requirePerm($ownerType === 'page' ? 'pages.view' : 'entries.view');
        $ownerType = $ownerType === 'page' ? 'page' : 'cpt';
        $ownerId = (int) $ownerId;

        $revisions = Database::all(
            'SELECT r.id, r.is_live, r.note, r.created_at, u.name AS author
             FROM content_revisions r LEFT JOIN users u ON u.id = r.created_by
             WHERE r.owner_type = ? AND r.owner_id = ?
             ORDER BY r.id DESC LIMIT 40',
            [$ownerType, $ownerId]
        );
        if (!$revisions) {
            View::flash('error', 'No revisions recorded yet.');
            View::redirect(self::backUrl($ownerType, $ownerId));
        }

        $toId = Request::int('to') ?: (int) $revisions[0]['id'];
        $fromId = Request::int('from') ?: (int) ($revisions[1]['id'] ?? $revisions[0]['id']);
        $from = self::snapshot($ownerType, $ownerId, $fromId);
        $to = self::snapshot($ownerType, $ownerId, $toId);

        View::admin('revisions/compare', [
            'title' => 'Compare revisions',
            'ownerType' => $ownerType,
            'ownerId' => $ownerId,
            'revisions' => $revisions,
            'fromId' => $fromId,
            'toId' => $toId,
            'diff' => self::diff($from, $to),
            'backUrl' => self::backUrl($ownerType, $ownerId),
        ]);
    }

    private static function backUrl(string $ownerType, int $ownerId): string
    {
        if ($ownerType === 'page') {
            return '/admin/pages/' . $ownerId . '/';
        }
        $e = Database::one('SELECT post_type_id FROM cpt_entries WHERE id = ?', [$ownerId]);
        $t = $e ? Cpt::typeById((int) $e['post_type_id']) : null;
        return $t ? '/admin/content/' . $t['slug'] . '/' . $ownerId . '/' : '/admin/';
    }

    private static function snapshot(string $ownerType, int $ownerId, int $revId): array
    {
        $rev = Database::one(
            'SELECT snapshot_json FROM content_revisions WHERE id = ? AND owner_type = ? AND owner_id = ?',
            [$revId, $ownerType, $ownerId]
        );
        return $rev ? (json_decode($rev['snapshot_json'], true) ?: []) : [];
    }

    /**
     * Compare two snapshots block by block.
     *
     * @return list<array{status:string,type:string,index:int,changes:list<array{key:string,from:string,to:string}>}>
     */
    private static function diff(array $from, array $to): array
    {
        $a = $from['sections'] ?? [];
        $b = $to['sections'] ?? [];
        $out = [];
        $max = max(count($a), count($b));
        for ($i = 0; $i < $max; $i++) {
            $left = $a[$i] ?? null;
            $right = $b[$i] ?? null;
            if ($left === null) {
                $out[] = ['status' => 'added', 'type' => (string) ($right['type'] ?? '?'), 'index' => $i, 'changes' => []];
                continue;
            }
            if ($right === null) {
                $out[] = ['status' => 'removed', 'type' => (string) ($left['type'] ?? '?'), 'index' => $i, 'changes' => []];
                continue;
            }
            if (($left['type'] ?? '') !== ($right['type'] ?? '')) {
                $out[] = [
                    'status' => 'replaced',
                    'type' => ($left['type'] ?? '?') . ' → ' . ($right['type'] ?? '?'),
                    'index' => $i,
                    'changes' => [],
                ];
                continue;
            }
            $lc = $left['content'] ?? [];
            $rc = $right['content'] ?? [];
            $keys = array_unique(array_merge(array_keys($lc), array_keys($rc)));
            sort($keys);
            $changes = [];
            foreach ($keys as $k) {
                $lv = self::flat($lc[$k] ?? '');
                $rv = self::flat($rc[$k] ?? '');
                if ($lv !== $rv) {
                    $changes[] = ['key' => (string) $k, 'from' => $lv, 'to' => $rv];
                }
            }
            if ((int) ($left['is_visible'] ?? 1) !== (int) ($right['is_visible'] ?? 1)) {
                $changes[] = [
                    'key' => 'visible',
                    'from' => ((int) ($left['is_visible'] ?? 1)) ? 'yes' : 'no',
                    'to' => ((int) ($right['is_visible'] ?? 1)) ? 'yes' : 'no',
                ];
            }
            if ($changes) {
                $out[] = ['status' => 'changed', 'type' => (string) ($left['type'] ?? '?'), 'index' => $i, 'changes' => $changes];
            }
        }
        return $out;
    }

    private static function flat(mixed $v): string
    {
        if (is_array($v)) {
            return trim(implode(' | ', array_map(static fn ($x) => is_array($x) ? implode(' ', $x) : (string) $x, $v)));
        }
        return trim((string) $v);
    }
}
