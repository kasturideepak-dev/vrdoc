<?php
$first = $sections[0] ?? [];
$firstType = $first['type'] ?? '';
$isCampaign = $firstType === 'campaign_hero';
$isAiLanding = $firstType === 'ai_banner';
$bodyClass = trim(($bodyClass ?? '') . ($isCampaign ? ' is-campaign' : '') . ($isAiLanding ? ' is-ai-landing' : ''));
require ROOT . '/views/public/_start.php';
foreach ($sections ?? [] as $sec) {
    $c = $sec['content'] ?? (json_decode($sec['content_json'] ?? '{}', true) ?: []);
    $type = $sec['type'] ?? '';
    $file = ROOT . '/views/public/sections/' . $type . '.php';
    if (is_file($file)) {
        require $file;
    }
}
require ROOT . '/views/public/_end.php';
