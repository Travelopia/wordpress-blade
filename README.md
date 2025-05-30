# WordPress Blade Plugin

![maintenance-status](https://img.shields.io/badge/maintenance-actively--developed-brightgreen.svg)

Use Laravel Blade components in WordPress.

🚨 Note: Only Anonymous Components are currently supported: https://laravel.com/docs/10.x/blade#anonymous-components

<table width="100%">
	<tr>
		<td align="left" width="70%">
            <p>Built by the super talented team at <strong><a href="https://www.travelopia.com/work-with-us/">Travelopia</a></strong>.</p>
		</td>
		<td align="center" width="30%">
			<img src="https://www.travelopia.com/wp-content/themes/travelopia/assets/svg/logo-travelopia-circle.svg" width="50" />
		</td>
	</tr>
</table>

## Video Tutorial
▶ ️[tutorial](https://www.youtube.com/watch?v=M7aCzMkL7Lo)

## Installation

### Install via Composer (recommended):

```
$ composer require travelopia/wordpress-blade
```

* Your composer file should include this
```json
{
	"name": "your/packagename",
	"description": "Description",
	"extra": {
		"installer-paths": {
			"wp-content/mu-plugins/{$name}/": ["type:wordpress-muplugin"]
		}
	},
	"require": {
		"travelopia/wordpress-blade": "^1.0.0",
		"composer/installers": "^2.3"
	},
	"config": {
		"allow-plugins": {
			"composer/installers": true
		}
	},
	"scripts": {
		"wordpress-blade": "wordpress-blade"
	}
}
```

* This installs it as an MU Plugin.
* Then load this plugin in your mu plugins loader file e.g. mu-plugins/loader.php
```php
require_once WPMU_PLUGIN_DIR . '/wordpress-blade/plugin.php';
```
* Then require the autoload file from vendor directory by adding the following code in your wp-config.php file.
```php
require_once 'vendor/autoload.php';
```

### Manual Installation (if you know what you are doing):

1. Download this repository as a ZIP file.
2. Run `composer install --no-dev --optimize-autoloader`
3. Use it either as an MU plugin or a normal plugin!

## Building for Production

Compile your Blade components for production as a best practice. Some production environments are read-only, in which case this step is necessary.

Run the following command:

`composer exec wordpress-blade -- --config-file=blade.config.php` - Ensure the path to the Blade config is correct.

## Usage

First, create a `blade.config.php` file at the root of your project, and add the following code in there:

```php
<?php
/**
 * Blade Config.
 *
 * @package wordpress-blade
 */

define(
	'WORDPRESS_BLADE',
	[
		'paths_to_views'         => [
			__DIR__ . '/wp-content/themes/<your-theme>/<path-to-your-components>',
			'parts' => __DIR__ . '/wp-content/themes/<your-theme>/<path-to-your-components>', // With namespace.
			// Any other paths where Blade needs to look for components.
		],
		'path_to_compiled_views' => __DIR__ . '/wp-content/themes/<your-theme>/dist/blade', // Where you want Blade to save compiled files.
		'never_expire_cache'     => false, // Use `true` on production environments.
		'base_path'              => __DIR__, // The base path that is common to the front-end and admin.
	]
);
```

### Bootstrap a layout.

As a best practice, and if applicable, bootstrap an entire layout like so:

```bladehtml
# bootstrap.blade.php
<x-layout>
    <x-hello name="Jane">Hi there!</x-hello>
</x-layout>
```

```bladehtml
# layout.blade.php
@php
    get_header();
@endphp

    <main>
        {{ $slot }}
    </main>

@php
    get_footer();
@endphp
```

```bladehtml
# hello.blade.php
@props( [
    'name' => '',
] )

<div>
    <h1>{{ $slot }}</h1>
    <p>Hello, {{ $name }}</p>
</div>
```

And then load the view in your template:

```php
Travelopia\Blade\load_view( 'bootstrap' );
```

You can also load an individual component like so:

```php
$name       = 'hello';              // The name of the component.
$attributes = [ 'name' => 'Jane' ]; // Properties / attributes to pass into the component.
$echo       = true;                 // Whether to echo the component. `false` returns the component as a string.

Travelopia\Blade\load_view( $name, $attributes, $echo );
```

This is especially useful when you want to load components from Blocks and Full Site Editing.

If you've used namespaces, you can use them like so:

```bladehtml
<x-parts::my-part></x-parts::my-part>
```
