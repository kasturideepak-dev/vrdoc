<?php
declare(strict_types=1);

final class Html
{
    public static function e(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function json($data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    /**
     * JSON safe to embed inside a <script> tag: "<", ">", "&", quotes are
     * escaped, so text containing "</script>" cannot close the block.
     */
    public static function jsonLd($data): string
    {
        return json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        ) ?: '{}';
    }

    public static function lines(string $text): array
    {
        $out = [];
        foreach (preg_split("/\r\n|\n|\r/", $text) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $out[] = array_map('trim', explode('|', $line));
        }
        return $out;
    }

    public static function allowedHtml(string $html): string
    {
        // Everything the admin editor can produce must be listed here, or it is
        // silently stripped on save (underline and strikethrough used to be).
        $allowed = '<p><br><h2><h3><h4><ul><ol><li><strong><em><b><i><u><s><sub><sup><a><blockquote><pre><code><img><table><thead><tbody><tr><th><td><hr><span><div><iframe>';
        $clean = strip_tags($html, $allowed);
        $clean = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean) ?? $clean;
        $clean = preg_replace('/javascript\s*:/i', '', $clean) ?? $clean;
        return $clean;
    }

    public static function selected($a, $b): string
    {
        return (string) $a === (string) $b ? ' selected' : '';
    }

    public static function checked(bool $on): string
    {
        return $on ? ' checked' : '';
    }

    /** Extract a YouTube video ID from a URL or bare ID string. */
    public static function youtubeId(string $input): string
    {
        $input = trim($input);
        if ($input === '') {
            return '';
        }
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $input)) {
            return $input;
        }
        if (preg_match('/(?:v=|youtu\.be\/|embed\/|shorts\/)([A-Za-z0-9_-]{6,})/', $input, $m)) {
            return $m[1];
        }
        return '';
    }

    /** Best-effort YouTube thumbnail URL (works for regular videos and Shorts). */
    public static function youtubeThumb(string $id): string
    {
        $id = self::youtubeId($id);
        if ($id === '') {
            return '';
        }
        return 'https://i.ytimg.com/vi/' . rawurlencode($id) . '/hqdefault.jpg';
    }
}
