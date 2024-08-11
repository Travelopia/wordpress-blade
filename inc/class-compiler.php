<?php
/**
 * Compiler Class.
 *
 * This class is used to override functionality to support
 * CI and read-only production environments where the paths
 * to files and their modified times may be different.
 *
 * @package travelopia-blade
 */

namespace Travelopia\Blade;

use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;

/**
 * Compiler class.
 */
class Compiler extends BladeCompiler {
	/**
	 * Set this to true on a production environment.
	 *
	 * @var bool Never expire cache.
	 */
	public bool $never_expire_cache = false;

	/**
	 * Get the path to the compiled version of a view.
	 *
	 * @param string $path Path to view.
	 *
	 * @return string
	 */
	public function getCompiledPath( $path ) {
		$path = str_replace( getcwd(), '', $path );

		return $this->cachePath . '/' . sha1( 'v2' . Str::after( $path, $this->basePath ) ) . '.' . $this->compiledExtension; // phpcs:ignore
	}

	/**
	 * Determine if the view at the given path is expired.
	 *
	 * @param string $path Path to view.
	 *
	 * @return bool
	 */
	public function isExpired( $path ) {
		if ( $this->never_expire_cache ) {
			return false;
		}

		return parent::isExpired( $path );
	}
}
