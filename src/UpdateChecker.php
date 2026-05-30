<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Loaded only when the vendor is present (GitHub distribution).
// Excluded from WordPress.org builds via .distignore.

$polylang_tcopro_loader = POLYLANG_TCOPRO_BASEPATH . 'vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( file_exists( $polylang_tcopro_loader ) ) {
	require_once $polylang_tcopro_loader;

	\YahnisElsts\PluginUpdateChecker\v5p6\PucFactory::buildUpdateChecker( // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- update checker excluded from WP.org build via .distignore
		'https://github.com/ControlZetaDigital/polylang-tcopro',
		POLYLANG_TCOPRO_BASEPATH . 'polylang-tcopro.php',
		POLYLANG_TCOPRO_NAME
	);
}
