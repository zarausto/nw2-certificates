<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/zarausto
 * @since      1.0.0
 *
 * @package    Nw2_Certificates
 * @subpackage Nw2_Certificates/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Nw2_Certificates
 * @subpackage Nw2_Certificates/includes
 * @author     Fausto Rodrigo Toloi <fausto@nw2web.com.br>
 */
class Nw2_Certificates_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'nw2-certificates',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
