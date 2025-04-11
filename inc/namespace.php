<?php
/**
 * Namespace functions.
 *
 * @package wordpress-blade
 */

namespace Travelopia\Blade;

use Illuminate\View\View;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Load Blade classes.
	require_once __DIR__ . '/class-app.php';
	require_once __DIR__ . '/class-compiler.php';
	require_once __DIR__ . '/class-finder.php';
	require_once __DIR__ . '/class-blade.php';
}

/**
 * Get configuration.
 *
 * @return array{
 *     path_to_views: string,
 *     path_to_compiled_views: string,
 *     never_expire_cache: bool,
 * }
 */
function get_configuration(): array {
	// Fallback config.
	$config = [
		'paths_to_views'         => [],
		'path_to_compiled_views' => '',
		'never_expire_cache'     => false,
	];

	// Initialize Blade.
	if ( defined( 'WP_CONTENT_DIR' ) && ! empty( WP_CONTENT_DIR ) ) {
		// Get config file.
		$config_file = strval( apply_filters( 'wordpress_blade_config_file', WP_CONTENT_DIR . '/../blade.config.php' ) );

		// Load config file.
		if ( file_exists( $config_file ) ) {
			require_once $config_file;
		}
	}

	// Check for user settings.
	if ( defined( 'WORDPRESS_BLADE' ) && is_array( WORDPRESS_BLADE ) ) {
		$config = array_merge( $config, WORDPRESS_BLADE );
	}

	// Return updated config.
	return $config;
}

/**
 * Get Blade instance.
 *
 * @return Blade
 */
function get_blade(): Blade {
	// Initialize static variable.
	static $blade = null;

	// Check for cached static variable.
	if ( null !== $blade ) {
		return $blade;
	}

	// Initialize Blade.
	$blade_config                  = get_configuration();
	$blade                         = new Blade();
	$blade->paths_to_views         = apply_filters( 'wordpress_blade_view_paths', $blade_config['paths_to_views'] );
	$blade->named_paths            = apply_filters( 'wordpress_blade_named_paths', $blade_config['named_paths'] );
	$blade->path_to_compiled_views = apply_filters( 'wordpress_blade_compiled_path', $blade_config['path_to_compiled_views'] );
	$blade->never_expire_cache     = apply_filters( 'wordpress_blade_never_expire_cache', $blade_config['never_expire_cache'] );
	$blade->base_path              = apply_filters( 'wordpress_blade_base_path', $blade_config['base_path'] );
	$blade->view_callback          = __NAMESPACE__ . '\\view_callback';
	$blade->initialize();

	register_component_namespaces( $blade );

	// Return initialized Blade object.
	return $blade;
}


/**
 * Register component namespaces with the Blade compiler.
 *
 * @param Blade $blade The Blade instance.
 *
 * @return void
 */
function register_component_namespaces( Blade $blade ): void {
	// Get the blade compiler
	$compiler = $blade->blade_compiler;

	// Register each named path as an anonymous component path
	foreach ( $blade->named_paths as $prefix => $path ) {
		// Create absolute paths.
		$absolute_path = $blade->base_path . $path;

		// Register the anonymous component path
		$compiler->anonymousComponentPath( $absolute_path, $prefix );
	}
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
	// Get Blade instance.
	$blade = get_blade();

	// Check if we found a valid Blade instance.
	if ( ! $blade instanceof Blade ) {
		// Nope, bail.
		return '';
	}

	// Get compiled component's content.
	$content = $blade->view_factory->make( $view, $data )->render();

	// Check if we need to echo or return content.
	if ( $echo ) {
		// Echo content.
		echo $content; // phpcs:ignore
	} else {
		// Return content.
		return $content;
	}
}

/**
 * Custom callback everytime a view is loaded.
 *
 * @param View|null $view Blade view.
 *
 * @return void
 */
function view_callback( ?View $view = null ): void {
	// Check if we have a valid Blade View instance.
	if ( ! $view instanceof View ) {
		return;
	}

	// Get the view's name and path.
	$name = $view->getName();
	$path = $view->getPath();

	/**
	 * Fire a hook before the Blade component is loaded.
	 *
	 * @param string $name Name of the view.
	 * @param string $path Path to the view.
	 * @param View   $view The Blade View instance.
	 */
	do_action( 'wordpress_blade_before_view', $name, $path, $view );

	/**
	 * Add custom attributes to all components.
	 *
	 * @param array  $attributes Attributes to add to the component.
	 * @param string $name       Name of the view.
	 * @param string $path       Path to the view.
	 * @param View   $view       The Blade View instance.
	 */
	$attributes = (array) apply_filters( 'wordpress_blade_view_custom_attributes', [], $name, $path, $view );

	/**
	 * Add custom attributes to a specific component.
	 *
	 * @param array  $attributes Attributes to add to the component.
	 * @param string $name       Name of the view.
	 * @param string $path       Path to the view.
	 * @param View   $view       The Blade View instance.
	 */
	$attributes = (array) apply_filters( "wordpress_blade_view_custom_attributes_$name", $attributes, $name, $path, $view );

	// Check if we have custom attributes.
	if ( ! empty( $attributes ) ) {
		$view->with( $attributes );
	}
}
