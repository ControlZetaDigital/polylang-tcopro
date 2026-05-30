<?php

namespace PolylangTcoPro;

// Loaded only when the vendor is present (GitHub distribution).
// Excluded from WordPress.org builds via .distignore.

$loader = POLYLANG_TCOPRO_BASEPATH . 'vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';
if ( file_exists( $loader ) ) {
	require_once $loader;

	\YahnisElsts\PluginUpdateChecker\v5p6\PucFactory::buildUpdateChecker(
		'https://github.com/ControlZetaDigital/polylang-tcopro',
		POLYLANG_TCOPRO_BASEPATH . 'polylang-tcopro.php',
		POLYLANG_TCOPRO_NAME
	);
}
