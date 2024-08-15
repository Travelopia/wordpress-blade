<?php
/**
 * Finder Class.
 *
 * @package wordpress-blade
 */

namespace Travelopia\Blade;

use Illuminate\View\FileViewFinder;

/**
 * Class Finder.
 */
class Finder extends FileViewFinder {

	/**
	 * Get an array of possible view files.
	 *
	 * @param string $name View name.
	 *
	 * @return array
	 */
	protected function getPossibleViewFiles( $name = '' ): array { // phpcs:ignore
		// Add `/index.blade.php` to the list of possible view files.
		$possibilities   = parent::getPossibleViewFiles( $name );
		$possibilities[] = $name . '/index.blade.php';

		// Return all possibilities.
		return $possibilities;
	}
}
