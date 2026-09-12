<?php
?><!DOCTYPE html>
<html lang="en" class="h-full antialiased">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body class="min-h-full flex flex-col">
<?php if (class_exists('Snippets')) { Snippets::emit('body_start', $GLOBALS['snippetCtx'] ?? []); } ?>

<nav class="vr-navbar sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-200/50" data-vr-nav>
	<div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
		<a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center group shrink-0" aria-label="VR Doctors Academy — Home">
			<img src="<?php echo esc_url(class_exists('Settings') ? Settings::logoUrl() : vr_img('logo.webp')); ?>" alt="VR Doctors Academy" width="200" height="102" class="h-11 md:h-14 w-auto object-contain group-hover:opacity-90 transition-opacity" />
		</a>

		<div class="hidden md:flex gap-1 items-center">
			<a href="<?php echo esc_url(home_url('/')); ?>" class="px-4 py-2 text-blue-900 font-medium relative group">Home<span class="absolute bottom-0 left-0 h-0.5 w-0 group-hover:w-full bg-gradient-to-r from-orange-500 to-orange-400 transition-all duration-300"></span></a>
			<a href="<?php echo esc_url(home_url('/bipc-careers/')); ?>" class="px-4 py-2 text-blue-900 font-medium relative group">World of Bipc<span class="absolute bottom-0 left-0 h-0.5 w-0 group-hover:w-full bg-gradient-to-r from-orange-500 to-orange-400 transition-all duration-300"></span></a>
			<a href="<?php echo esc_url(home_url('/about/')); ?>" class="px-4 py-2 text-blue-900 font-medium relative group">About<span class="absolute bottom-0 left-0 h-0.5 w-0 group-hover:w-full bg-gradient-to-r from-orange-500 to-orange-400 transition-all duration-300"></span></a>
			<div class="relative" data-courses-dropdown>
				<a href="<?php echo esc_url(home_url('/courses/')); ?>" class="px-4 py-2 text-blue-900 font-medium relative group flex items-center gap-1">
					Courses <span class="text-xs mt-0.5">▾</span>
					<span class="absolute bottom-0 left-0 h-0.5 w-0 group-hover:w-full bg-gradient-to-r from-orange-500 to-orange-400 transition-all duration-300"></span>
				</a>
				<div class="hidden absolute left-0 top-full mt-1 min-w-[260px] bg-white rounded-lg shadow-lg border border-gray-100 py-2 overflow-hidden z-50" data-courses-menu>
					<a href="<?php echo esc_url(home_url('/best-bipc-college-in-hyderabad-neet-residential/')); ?>" class="block px-4 py-2.5 text-sm text-blue-900 font-medium hover:bg-orange-50 hover:text-orange-600 transition-colors">BiPC + NEET Residential</a>
					<a href="<?php echo esc_url(home_url('/long-term-neet-program-hyderabad/')); ?>" class="block px-4 py-2.5 text-sm text-blue-900 font-medium hover:bg-orange-50 hover:text-orange-600 transition-colors">Long-Term NEET (2-Year)</a>
					<a href="<?php echo esc_url(home_url('/short-term-neet-program-hyderabad/')); ?>" class="block px-4 py-2.5 text-sm text-blue-900 font-medium hover:bg-orange-50 hover:text-orange-600 transition-colors">Short-Term NEET (1-Year)</a>
				</div>
			</div>
			<a href="<?php echo esc_url(home_url('/results/')); ?>" class="px-4 py-2 text-blue-900 font-medium relative group">Results<span class="absolute bottom-0 left-0 h-0.5 w-0 group-hover:w-full bg-gradient-to-r from-orange-500 to-orange-400 transition-all duration-300"></span></a>
			<a href="<?php echo esc_url(home_url('/blog/')); ?>" class="px-4 py-2 text-blue-900 font-medium relative group">Blog<span class="absolute bottom-0 left-0 h-0.5 w-0 group-hover:w-full bg-gradient-to-r from-orange-500 to-orange-400 transition-all duration-300"></span></a>
			<a href="<?php echo esc_url(home_url('/contact/')); ?>" class="px-4 py-2 text-blue-900 font-medium relative group">Contact<span class="absolute bottom-0 left-0 h-0.5 w-0 group-hover:w-full bg-gradient-to-r from-orange-500 to-orange-400 transition-all duration-300"></span></a>
		</div>

		<button type="button" class="md:hidden text-2xl text-blue-900 w-10 h-10 flex items-center justify-center" data-mobile-toggle aria-label="Menu">☰</button>
	</div>

	<div class="hidden md:hidden bg-white border-t border-gray-200" data-mobile-menu>
		<a href="<?php echo esc_url(home_url('/')); ?>" class="block px-6 py-4 text-blue-900 font-medium hover:bg-orange-50">Home</a>
		<a href="<?php echo esc_url(home_url('/bipc-careers/')); ?>" class="block px-6 py-4 text-blue-900 font-medium hover:bg-orange-50">World of Bipc</a>
		<a href="<?php echo esc_url(home_url('/about/')); ?>" class="block px-6 py-4 text-blue-900 font-medium hover:bg-orange-50">About</a>
		<button type="button" class="w-full flex items-center justify-between px-6 py-4 text-blue-900 font-medium hover:bg-orange-50" data-mobile-courses-toggle>
			Courses <span>▾</span>
		</button>
		<div class="hidden bg-orange-50/50" data-mobile-courses>
			<a href="<?php echo esc_url(home_url('/best-bipc-college-in-hyderabad-neet-residential/')); ?>" class="block px-10 py-3 text-sm text-blue-900 hover:bg-orange-100">BiPC + NEET Residential</a>
			<a href="<?php echo esc_url(home_url('/long-term-neet-program-hyderabad/')); ?>" class="block px-10 py-3 text-sm text-blue-900 hover:bg-orange-100">Long-Term NEET (2-Year)</a>
			<a href="<?php echo esc_url(home_url('/short-term-neet-program-hyderabad/')); ?>" class="block px-10 py-3 text-sm text-blue-900 hover:bg-orange-100">Short-Term NEET (1-Year)</a>
		</div>
		<a href="<?php echo esc_url(home_url('/results/')); ?>" class="block px-6 py-4 text-blue-900 font-medium hover:bg-orange-50">Results</a>
		<a href="<?php echo esc_url(home_url('/blog/')); ?>" class="block px-6 py-4 text-blue-900 font-medium hover:bg-orange-50">Blog</a>
		<a href="<?php echo esc_url(home_url('/contact/')); ?>" class="block px-6 py-4 text-blue-900 font-medium hover:bg-orange-50">Contact</a>
	</div>
</nav>
