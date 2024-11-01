<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.sprucetech.com/
 * @since      1.0.0
 *
 * @package    Spruce_api_extension
 * @subpackage Spruce_api_extension/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Spruce_api_extension
 * @subpackage Spruce_api_extension/includes
 * @author     Jun Huang <jhuang@sprucetech.com>
 */
class Spruce_api_extension_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'spruce_api_extension',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
