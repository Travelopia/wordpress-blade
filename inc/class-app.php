<?php
/**
 * App Class.
 *
 * @package wordpress-blade
 */

namespace Travelopia\Blade;

use Illuminate\Container\Container;

/**
 * Class App.
 */
class App extends Container {
	/**
	 * Is down for maintenance.
	 *
	 * @return bool
	 *
	 * @see \Illuminate\Contracts\Foundation\Application::isDownForMaintenance()
	 */
	public function isDownForMaintenance(): bool {
		// Disable this feature.
		return false;
	}

	/**
	 * Environment.
	 *
	 * @param string|string[] ...$environments Environments.
	 *
	 * @return string|bool
	 *
	 * @see \Illuminate\Contracts\Foundation\Application::environment()
	 */
	public function environment( ...$environments ) { // phpcs:ignore
		// Check if we have environments.
		if ( empty( $environments ) ) {
			// We don't add one.
			return 'travelopia';
		}

		// Check if environment exists in the list.
		return in_array(
			'travelopia',
			is_array( $environments[0] ) ? $environments[0] : $environments,
			true
		);
	}

	/**
	 * Get namespace.
	 *
	 * @return string
	 *
	 * @see \Illuminate\Contracts\Foundation\Application::getNamespace()
	 */
	public function getNamespace(): string {
		// The namespace.
		return 'Travelopia\\Blade\\';
	}
}
