<?php
/**
 * Finder Class.
 *
 * @package travelopia-blade
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
	protected function getPossibleViewFiles( $name = '' ) {
		$original_possibilities   = parent::getPossibleViewFiles( $name );
		$original_possibilities[] = $name . '/index.blade.php';
		return $original_possibilities;
	}
}
