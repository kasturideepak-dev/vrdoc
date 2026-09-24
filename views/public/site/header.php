<?php
// Links come from Admin → Menus (the "header" menu); one dropdown level.
$vrNav = class_exists('Menu') ? Menu::tree('header') : [];
$vrUl = 'absolute bottom-0 left-0 h-0.5 w-0 group-hover:w-full bg-gradient-to-r from-orange-500 to-orange-400 transition-all duration-300';
// Absolute links to another site open in a new tab (www. and ports ignored,
// so a full link to this site stays in the same tab).
$vrHost = static fn (?string $h): string => preg_replace('/^www\./i', '', strtolower(explode(':', (string) $h)[0]));
$vrExt = static function (array $i) use ($vrHost): string {
    $host = parse_url((string) $i['url'], PHP_URL_HOST);
    return $host && $vrHost($host) !== $vrHost($_SERVER['HTTP_HOST'] ?? '') ? ' target="_blank" rel="noopener noreferrer"' : '';
};
?><!DOCTYPE html>
<html lang="en" class="h-full antialiased">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<style>
		/* Set by main.js when the menu links are too wide for the bar. */
		.vr-nav--compact [data-desktop-nav] { display: none !important; }
		.vr-nav--compact [data-mobile-toggle] { display: flex !important; }
		.vr-nav--compact [data-mobile-menu]:not(.hidden) { display: block !important; }
	</style>
</head>
<body class="min-h-full flex flex-col">
<?php if (class_exists('Snippets')) { Snippets::emit('body_start', $GLOBALS['snippetCtx'] ?? []); } ?>

<nav class="vr-navbar sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-200/50" data-vr-nav>
	<div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
		<a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center group shrink-0" aria-label="VR Doctors Academy — Home">
			<img src="<?php echo esc_url(vr_media_url(class_exists('Settings') ? Settings::logoUrl() : vr_img('logo.webp'))); ?>" alt="VR Doctors Academy" width="200" height="102" class="h-11 md:h-14 w-auto object-contain group-hover:opacity-90 transition-opacity" decoding="async" />
		</a>

		<div class="hidden lg:flex gap-1 items-center" data-desktop-nav>
			<?php foreach ($vrNav as $it): ?>
				<?php if (!empty($it['_children'])): ?>
					<div class="relative" data-courses-dropdown>
						<a href="<?php echo esc_url($it['url']); ?>" class="px-4 py-2 text-blue-900 font-medium relative group flex items-center gap-1 whitespace-nowrap"<?php echo $vrExt($it); ?>>
							<?php echo esc_html($it['label']); ?> <span class="text-xs mt-0.5">▾</span>
							<span class="<?php echo $vrUl; ?>"></span>
						</a>
						<div class="hidden absolute left-0 top-full mt-1 min-w-[260px] bg-white rounded-lg shadow-lg border border-gray-100 py-2 overflow-hidden z-50" data-courses-menu>
							<?php foreach ($it['_children'] as $ch): ?>
								<a href="<?php echo esc_url($ch['url']); ?>" class="block px-4 py-2.5 text-sm text-blue-900 font-medium hover:bg-orange-50 hover:text-orange-600 transition-colors"<?php echo $vrExt($ch); ?>><?php echo esc_html($ch['label']); ?></a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php else: ?>
					<a href="<?php echo esc_url($it['url']); ?>" class="px-4 py-2 text-blue-900 font-medium relative group whitespace-nowrap"<?php echo $vrExt($it); ?>><?php echo esc_html($it['label']); ?><span class="<?php echo $vrUl; ?>"></span></a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<button type="button" class="lg:hidden text-2xl text-blue-900 w-10 h-10 flex items-center justify-center" data-mobile-toggle aria-label="Menu">☰</button>
	</div>

	<div class="hidden lg:hidden bg-white border-t border-gray-200" data-mobile-menu>
		<?php foreach ($vrNav as $it): ?>
			<?php if (!empty($it['_children'])): ?>
				<button type="button" class="w-full flex items-center justify-between px-6 py-4 text-blue-900 font-medium hover:bg-orange-50" data-mobile-courses-toggle>
					<?php echo esc_html($it['label']); ?> <span>▾</span>
				</button>
				<div class="hidden bg-orange-50/50" data-mobile-courses>
					<?php foreach ($it['_children'] as $ch): ?>
						<a href="<?php echo esc_url($ch['url']); ?>" class="block px-10 py-3 text-sm text-blue-900 hover:bg-orange-100"<?php echo $vrExt($ch); ?>><?php echo esc_html($ch['label']); ?></a>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<a href="<?php echo esc_url($it['url']); ?>" class="block px-6 py-4 text-blue-900 font-medium hover:bg-orange-50"<?php echo $vrExt($it); ?>><?php echo esc_html($it['label']); ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</nav>
