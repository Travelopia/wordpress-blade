<?php
/**
 * Plugin Name: Travelopia WordPress Blade
 * Description: Use Laravel Blade components in WordPress.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.1.0
 *
 * @package wordpress-blade
 */

namespace Travelopia\Blade;

// Load Composer autoloader.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Load plugin code.
require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
