<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/fagnervalente
 * @since      1.0.0
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 * @author     Fagner Valente <fagner.valente@sambatech.com.br>
 */
class Samba_Videos_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		self::insert_initial_values();

	}

	public static function insert_initial_values(){

		$default_values = array(
			"sv_pid" 		=> null,
			"sv_width" 	=> 640,
			"sv_height"	=> 420
		);
		
	}

}
