<?php
/**
 * Namespace functions.
 *
 * @package travelopia-blade
 */

namespace Travelopia\Blade;

use Illuminate\View\View;

/**
 * Bootstrap plugin.
 */
function bootstrap(): void {
	// Load classes.
	require_once __DIR__ . '/class-app.php';
	require_once __DIR__ . '/class-compiler.php';
	require_once __DIR__ . '/class-finder.php';
	require_once __DIR__ . '/class-blade.php';
}

/**
 * Get Blade instance.
 *
 * @return Blade
 */
function get_blade(): Blade {
	// Check for static.
	static $blade = null;
	if ( null !== $blade ) {
		return $blade;
	}

	// Initialize Blade.
	$config_file = WP_CONTENT_DIR . '/../blade.config.php';
	if ( file_exists( $config_file ) ) {
		require_once $config_file;
	}

	$blade_config                  = $blade_config ?? [];
	$blade                         = new Blade();
	$blade->paths_to_views         = apply_filters( 'travelopia_blade_view_paths', $blade_config['paths_to_views'] ?? [] );
	$blade->path_to_compiled_views = apply_filters( 'travelopia_blade_compiled_path', $blade_config['path_to_compiled_views'] ?? '' );
	$blade->never_expire_cache     = apply_filters( 'travelopia_blade_never_expire_cache', $blade_config['never_expire_cache'] ?? false );

	$blade->view_callback = __NAMESPACE__ . '\\view_callback';

	$blade->initialize();

	// Return initialized Blade object.
	return $blade;
}

/**
 * Load or return a view in Blade.
 *
 * @param string $view View handle.
 * @param array  $data View data.
 * @param bool   $echo Echo or return rendered view.
 *
 * @return string|void
 */
function load_view( string $view = '', array $data = [], bool $echo = true ) { // phpcs:ignore
	$blade = get_blade();
	if ( ! $blade instanceof Blade ) {
		return '';
	}

	$content = $blade->view_factory->make( $view, $data )->render();

	if ( $echo ) {
		echo $content; // phpcs:ignore
	} else {
		return $content;
	}
}

/**
 * Custom callback everytime a view is loaded.
 *
 * @param View $view Blade view.
 *
 * @return void
 */
function view_callback( View $view ): void {
	$name = $view->getName();
	$path = $view->getPath();

	do_action( 'travelopia_blade_view', $name, $path, $view );

	$custom_attributes = apply_filters( 'travelopia_blade_view_custom_attributes', [], $name, $path, $view );
	if ( ! empty( $custom_attributes ) && is_array( $custom_attributes ) ) {
		$view->with( $custom_attributes );
	}
}
