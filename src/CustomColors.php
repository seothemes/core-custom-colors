<?php
/**
 * Define child theme constants.
 *
 * @package   SEOThemes\Core
 * @since     0.1.0
 * @link      https://github.com/seothemes/core-plugin-activator
 * @author    SEO Themes
 * @copyright Copyright © 2018 SEO Themes
 * @license   GPL-2.0+
 */

namespace SEOThemes\Core;

use D2\Core\Core;

/**
 * Add recommended plugins to child theme.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SEOThemes\Core\PluginActivator;
 *
 * $plugins = [
 *     PluginActivator::REGISTER => [
 *         'Genesis eNews Extended',
 *         'Genesis Simple FAQ',
 *         'Genesis Testimonial Slider',
 *         'Genesis Widget Column Classes',
 *         'Google Map',
 *         'Icon Widget',
 *         'One Click Demo Import',
 *         'Simple Social Icons',
 *         'WP Featherlight',
 *     ],
 * ];
 *
 * return [
 *     PluginActivator::class => $plugins,
 * ];
 * ```
 */
class CustomColors extends Core {

	const REGISTER = 'register';

	public $plugins = [];

	/**
	 * Initialize class.
	 *
	 * @since  0.1.0
	 *
	 * @return void
	 */
	public function init() {

		if ( array_key_exists( self::REGISTER, $this->config ) ) {

			add_action( 'customize_register', [ $this, 'settings' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'output' ], 100 );

		}

	}

	/**
	 * Sets up the theme customizer sections, controls, and settings.
	 *
	 * @since  1.2.0
	 *
	 * @param  object $wp_customize Global customizer object.
	 *
	 * @return void
	 */
	public function settings( $wp_customize ) {

		$wp_customize->remove_control( 'background_color' );
		$wp_customize->remove_control( 'header_textcolor' );

		foreach ( $this->config as $color => $settings ) {

			$setting = "child_theme_{$color}_color";
			$label   = ucwords( str_replace( '_', ' ', $color ) ) . __( ' Color', 'child-theme-library' );

			$wp_customize->add_setting(
				$setting,
				array(
					'default'           => $settings['default'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					$setting,
					array(
						'section'  => 'colors',
						'label'    => $label,
						'settings' => $setting,
					)
				)
			);
		}
	}

	/**
	 * Logic to output customizer styles.
	 *
	 * @since  1.2.0
	 *
	 * @return void
	 */
	public function output() {

		$css = '';

		foreach ( $this->config as $color => $settings ) {

			$custom_color = get_theme_mod(
				"child_theme_{$color}_color",
				$settings['default']
			);

			if ( $settings['default'] !== $custom_color ) {

				foreach ( $settings['output'] as $rule ) {

					$counter = 0;

					foreach ( $rule['elements'] as $element ) {

						$comma = ( 0 === $counter ++ ? '' : ',' );
						$css  .= $comma . $element;

					}

					$css .= '{';

					foreach ( $rule['properties'] as $property => $pattern ) {

						$css .= $property . ':' . sprintf( $pattern, $custom_color ) . ';';

					}

					$css .= '}';

				}
			}
		}

		if ( ! empty( $css ) ) {

			wp_add_inline_style( sanitize_title_with_dashes( 'child-theme' ), $this->theme->utilities->minify_css( $css ) );

		}

	}

}
