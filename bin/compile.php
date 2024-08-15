<?php
/**
 * Compile Blade Cache.
 *
 * @package wordpress-blade
 */

namespace Travelopia\Blade;

// Get path to Blade config.
$options     = getopt( 'c:', [ 'config-file:' ] );
$config_file = $options['c'] ?? $options['config-file'] ?? '';

// Check if a path was sent.
if ( empty( $config_file ) || ! file_exists( $config_file ) ) {
	echo "\033[31m✗ Path to config file missing!\n";
	exit( 0 );
}

// Load file.
require_once $config_file;

// Composer autoloader.
if ( file_exists( __DIR__ . '/../vendor/autoload.php' ) ) {
	require_once __DIR__ . '/../vendor/autoload.php';
}

// Bootstrap Blade.
require_once __DIR__ . '/../inc/namespace.php';
bootstrap();

// Initialize blade.
$blade_config                  = get_configuration();
$blade                         = new Blade();
$blade->paths_to_views         = $blade_config['paths_to_views'] ?? [];
$blade->path_to_compiled_views = $blade_config['path_to_compiled_views'] ?? '';
$blade->initialize();

// Build Blade cache.
echo "Compiling Blade cache...\n";
$blade->build_cache();
echo "\033[32m✓ Blade cache compiled!\n";
exit( 0 );
