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
	 * Name compiled files after their source path instead of a hash.
	 *
	 * `src/components/card/index.blade.php` compiles to
	 * `<compiled path>/src/components/card/index.php`, and files are written
	 * in place with no randomly named temp file. Endpoint security tools can
	 * flag randomly named PHP files that PHP writes and then runs.
	 *
	 * @var bool Mirror source paths.
	 */
	public bool $mirror_source_paths = false;

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

	// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Laravel's camelCase properties.

	/**
	 * Get the path to the compiled version of a view.
	 *
	 * @param string $path Path to view.
	 *
	 * @return string
	 */
	public function getCompiledPath( $path ): string { // phpcs:ignore
		// Default: Laravel's hashed file name.
		if ( ! $this->mirror_source_paths ) {
			return parent::getCompiledPath( $path );
		}

		// Mirror the source path relative to the base path, without `.blade.php`.
		$relative = ltrim( Str::after( strval( $path ), $this->basePath ), '/' );
		$relative = Str::replaceLast( '.blade.php', '', $relative );

		// Keep any suffix Laravel adds to the hashed name (e.g. per echo format)
		// so each variant still gets its own compiled file.
		$suffix = strstr( pathinfo( parent::getCompiledPath( $path ), PATHINFO_FILENAME ), '_' );

		// Add the suffix.
		if ( false !== $suffix ) {
			$relative .= $suffix;
		}

		// Return compiled path.
		return $this->cachePath . '/' . $relative . '.' . $this->compiledExtension;
	}

	/**
	 * Compile the view at the given path.
	 *
	 * @param string|null $path Path to view.
	 *
	 * @return void
	 */
	public function compile( $path = null ): void { // phpcs:ignore
		// Default: Laravel writes through a randomly named temp file.
		if ( ! $this->mirror_source_paths ) {
			parent::compile( $path );

			// Bail.
			return;
		}

		// Set the path.
		if ( $path ) {
			$this->setPath( $path );
		}

		// Nothing to compile.
		if ( is_null( $this->cachePath ) || empty( $this->getPath() ) ) {
			return;
		}

		// Compile contents.
		$contents = $this->appendFilePath( $this->compileString( $this->files->get( $this->getPath() ) ) );

		// Make sure the directory exists.
		$compiled_path = $this->getCompiledPath( $this->getPath() );
		$this->ensureCompiledDirectoryExists( $compiled_path );

		// Write in place, only when the contents changed.
		if ( ! $this->files->exists( $compiled_path ) || $this->files->hash( $compiled_path, 'xxh128' ) !== hash( 'xxh128', $contents ) ) {
			$this->files->put( $compiled_path, $contents, true );
		}
	}

	// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
}
