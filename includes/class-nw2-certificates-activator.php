<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/zarausto
 * @since      1.0.0
 *
 * @package    Nw2_Certificates
 * @subpackage Nw2_Certificates/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Nw2_Certificates
 * @subpackage Nw2_Certificates/includes
 * @author     Fausto Rodrigo Toloi <fausto@nw2web.com.br>
 */
class Nw2_Certificates_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		if (!in_array('contact-form-7/wp-contact-form-7.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			error_log('NW2 Certificates plugin needs Contact Form 7');
			$args = var_export(func_get_args(), true);
			error_log($args);
			wp_die('NW2 Certificates plugin needs Contact Form 7');
		}
	}
}
