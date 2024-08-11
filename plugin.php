<?php
/**
 * Plugin Name: Travelopia WordPress Blade
 * Description: Travelopia WordPress Blade plugin - that enables the use of Laravel Blade templating engine within WordPress themes.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package travelopia-blade
 */

namespace Travelopia\Blade;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
