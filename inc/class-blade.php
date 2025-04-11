<?php
/**
 * Blade Class.
 *
 * @package wordpress-blade
 */

namespace Travelopia\Blade;

use Travelopia\Blade\Compiler as BladeCompiler;
use Travelopia\Blade\Finder as FileViewFinder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory as ContractsViewFactory;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade as BladeFacade;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\View;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Blade.
 */
class Blade {
	/**
	 * Set this to true on a production environment.
	 *
	 * @var bool Never expire cache.
	 */
	public bool $never_expire_cache = false;

	/**
	 * Store all paths to views.
	 *
	 * @var array Paths to views.
	 */
	public array $paths_to_views = [];

	/**
	 * Store named paths for AnonymousComponentPaths.
	 *
	 * @var array Named paths for views.
	 */
	public array $named_paths = [];

	/**
	 * Store the path to the directory where all compiled views are cached.
	 *
	 * @var string Path to compiled views.
	 */
	public string $path_to_compiled_views = '';

	/**
	 * Store the base path.
	 *
	 * @var string Base path.
	 */
	public string $base_path = '';

	/**
	 * Store view factory.
	 *
	 * @var ViewFactory View factory.
	 */
	public $view_factory;

	/**
	 * Store view finder.
	 *
	 * @var FileViewFinder View finder.
	 */
	public $view_finder;

	/**
	 * Store blade compiler.
	 *
	 * @var BladeCompiler Blade compiler.
	 */
	public $blade_compiler;

	/**
	 * A callback function for when any view is opened.
	 *
	 * @var callable View callback.
	 */
	public $view_callback;

	/**
	 * Initialize Blade.
	 *
	 * @return void
	 * @see https://github.com/mattstauffer/Torch/blob/master/components/view/index.php
	 */
	public function initialize(): void {
		// phpcs:disable
		// Create a container since Blade needs a namespace.
		$container = App::getInstance();
		$container->instance( Application::class, $container );

		// Dependencies.
		$filesystem       = new Filesystem();
		$event_dispatcher = new Dispatcher( $container );

		// Create View Factory capable of rendering PHP and Blade templates.
		$view_resolver        = new EngineResolver();
		$this->blade_compiler = new BladeCompiler( $filesystem, $this->path_to_compiled_views, $this->base_path );

		$this->blade_compiler->never_expire_cache = $this->never_expire_cache;

		$view_resolver->register( 'blade', function () {
			return new CompilerEngine( $this->blade_compiler );
		} );

		$this->view_finder  = new FileViewFinder( $filesystem, $this->paths_to_views );
		$this->view_factory = new ViewFactory( $view_resolver, $this->view_finder, $event_dispatcher );
		$this->view_factory->setContainer( $container );
		Facade::setFacadeApplication( $container );
		$container->instance( ContractsViewFactory::class, $this->view_factory );
		$container->alias(
			ContractsViewFactory::class,
			( new class extends View {
				public static function getFacadeAccessor() {
					return parent::getFacadeAccessor();
				}
			} )::getFacadeAccessor()
		);
		$container->instance( BladeCompiler::class, $this->blade_compiler );
		$container->alias(
			BladeCompiler::class,
			( new class extends BladeFacade {
				public static function getFacadeAccessor() {
					return parent::getFacadeAccessor();
				}
			} )::getFacadeAccessor()
		);

		// Callback function for every view.
		if ( is_callable( $this->view_callback ) ) {
			$this->view_factory->composer( '*' , function( $view ) {
				call_user_func( $this->view_callback, $view );
			} );
		}
		// phpcs:enable
	}

	/**
	 * Compile all views and store it in cache.
	 *
	 * @note Use this for deployment.
	 * @return void
	 */
	public function build_cache(): void {
		// phpcs:disable
		$paths = collect( $this->view_finder->getPaths() )->merge(
			collect( $this->view_finder->getHints() )->flatten()
		);

		$paths->each( function ( $path ) {
			$blade_files_in = collect(
				Finder::create()
					->in( [ $path ] )
					->exclude( 'vendor' )
					->name( '*.blade.php' )
					->files()
			);

			$blade_files_in->map( function ( SplFileInfo $file ) {
				$this->blade_compiler->compile( $file->getRealPath() );
			} );
		} );
		// phpcs:enable
	}
}
