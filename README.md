# Custom Colors

Add color settings to the Customizer with CSS output.

## Installation

This component should be installed using Composer, with the command `composer require seothemes/core-custom-colors`.

## Usage

Within your config file (typically found at `config/defaults.php`), use the `CustomColors` as an array key to add a list color settings to add to the Customizer.

For example:

```php
use SEOThemes\Core\CustomColors;

$custom_colors = [
	'background' => [
		'default' => '#ffffff',
		'output'  => [
			[
				'elements'   => [
					'body',
					'.site-container',
				],
				'properties' => [
					'background-color' => '%s',
				],
			],
		],
	],
];

return [
    CustomColors::class => $custom_colors,
];
 ```

## Load the component

Components should be loaded in your theme `functions.php` file, using the `Theme::setup` static method. Code should run on the `after_setup_theme` hook (or `genesis_setup` if you use Genesis Framework).

```php
add_action( 'after_setup_theme', function() {
    $config = include_once __DIR__ . '/config/defaults.php';
    D2\Core\Theme::setup( $config );
} );
```