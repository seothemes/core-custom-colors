<?php
/**
 * Add color settings to Customizer.
 *
 * @package   SEOThemes\Core
 * @since     0.1.0
 * @link      https://github.com/seothemes/core-custom-colors
 * @author    SEO Themes
 * @copyright Copyright © 2018 SEO Themes
 * @license   GPL-2.0+
 */

namespace SEOThemes\Core;

use D2\Core\Core;
use SEOThemes\Core\Utilities\MinifyCSS;

/**
 * Add recommended plugins to child theme.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SEOThemes\Core\CustomColors;
 *
 * $custom_colors = [
 *     'background' => [
 *         'default' => '#ffffff',
 *         'output'  => [
 *             [
 *                 'elements'   => [
 *                     'body',
 *                     '.site-container',
 *                 ],
 *                 'properties' => [
 *                     'background-color' => '%s',
 *                 ],
 *             ],
 *         ],
 *     ],
 * ];
 *
 * return [
 *     CustomColors::class => $custom_colors,
 * ];
 * ```
 */
class CustomColors extends Core {

	/**
	 * Initialize class.
	 *
	 * @since  0.1.0
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'customize_register', [ $this, 'add_settings' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'output_css' ], 100 );

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
	public function add_settings( $wp_customize ) {

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
	public function output_css() {

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

			wp_add_inline_style( get_stylesheet(), MinifyCSS::minify( $css ) );

		}

	}

}
