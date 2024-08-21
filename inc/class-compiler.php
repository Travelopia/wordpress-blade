<?php
/**
 * Compiler Class.
 *
 * This class is used to override functionality to support
 * CI and read-only production environments where the paths
 * to files and their modified times may be different.
 *
 * @package wordpress-blade
 */

namespace Travelopia\Blade;

use ErrorException;
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
	 * Determine if the view at the given path is expired.
	 *
	 * @param string $path Path to view.
	 *
	 * @return bool
	 *
	 * @throws ErrorException Exception from the parent.
	 */
	public function isExpired( $path ): bool { // phpcs:ignore
		// If the cache is set to never expire (example on production environments)
		// then set this to false or to "never expire".
		if ( $this->never_expire_cache ) {
			return false;
		}

		// This is not set in the config, set expiry as normal.
		return parent::isExpired( $path );
	}
}
