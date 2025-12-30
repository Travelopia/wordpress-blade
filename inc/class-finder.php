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
		$index = $name . '/index.blade.php';

		// Only add index blade if not already in the list.
		if ( ! in_array( $index, $possibilities, true ) ) {
			$possibilities[] = $index;
		}

		// Allow filtering of possibilities.
		$possibilities = apply_filters(
			'wordpress_blade_view_possibilities',
			$possibilities,
			$name
		);

		// Return all possibilities.
		return $possibilities;
	}
}
