<?php
/**
 * Compile Blade Cache.
 *
 * @package wordpress-blade
 */

namespace Travelopia\Blade;

// Get path to Blade config.
$options              = getopt( 'c:v:', [ 'config-file:', 'vendor-autoload-file:' ] );
$config_file          = $options['c'] ?? $options['config-file'] ?? '';
$vendor_autoload_file = $options['v'] ?? $options['vendor-autoload-file'] ?? __DIR__ . '/../../../../vendor/autoload.php';

// Check if a config path was set.
if ( empty( $config_file ) || ! file_exists( $config_file ) ) {
	echo "\033[31m✗ Path to config file missing!\n";
	exit( 1 );
}

// Check if vendor autoload file was set.
if ( empty( $vendor_autoload_file ) || ! file_exists( $vendor_autoload_file ) ) {
	echo "\033[31m✗ Path to config file missing!\n";
	exit( 1 );
}

// Load files.
require_once $config_file;
require_once $vendor_autoload_file;
require_once __DIR__ . '/../inc/namespace.php';

// Bootstrap Blade.
bootstrap();

// Initialize blade.
$blade_config                  = get_configuration();
$blade                         = new Blade();
$blade->paths_to_views         = $blade_config['paths_to_views'] ?? [];
$blade->path_to_compiled_views = $blade_config['path_to_compiled_views'] ?? '';
$blade->base_path              = $blade_config['base_path'] ?? '';
$blade->initialize();

// Build Blade cache.
echo "Compiling Blade cache...\n";
$blade->build_cache();
echo "\033[32m✓ Blade cache compiled!\n";
exit( 0 );
