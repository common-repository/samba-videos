<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/fagnervalente
 * @since      1.0.0
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 * @author     Fagner Valente <fagner.valente@sambatech.com.br>
 */
class Samba_Videos_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'samba-videos' );
		
		if ( !empty($locale) ) {
			load_textdomain( 'samba-videos', dirname( __DIR__ ) . '/languages/' . 'samba-videos-' . $locale . '.mo' );
		} else {
			load_textdomain( 'samba-videos', dirname( __DIR__ ) . '/languages/' . 'samba-videos-en_US.mo' );
		}
	}
}
