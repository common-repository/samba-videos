<?php

/**
 * The settings-screen-specific functionality of the plugin.
 *
 * @link       https://github.com/jefmoura
 * @since      1.0.0
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 */

/**
 * The settings-screen-specific functionality of the plugin.
 *
 * Builds the settings form, sanitizes the information and displays them
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 * @author     Jeferson Moura <jeferson.moura@sambatech.com.br>
 */
class Samba_Videos_Admin_Settings_Screen {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-samba-videos-proxy.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-samba-videos-admin-settings-form.php';
	}

	/**
	 * Player key of each project.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array(string)    $id_playerkey_projects    Project's player hash
	 */
	private $id_playerkey_projects = [];

	/**
	 * Build the Admin Settings screen completely
	 *
	 * @since    1.0.0
	 * @param    string           $plugin_name       The plugin name.
	 */
	public function build_settings_form($plugin_name) {

		$option_group = $plugin_name;

		$sv_admin_proxy = new Samba_Videos_Proxy();

		$json = null;

		try {
	        $json = json_decode($sv_admin_proxy->list_projetcs());
	    } catch ( Exception $ex ) {
	        
	    }
		
		$id_name_projects = array( '---' => 0 );

		unset($id_playerkey_projects);
		$id_playerkey_projects = array();

		if ( !empty($json) && is_array($json) ) {
			if ( !array_key_exists('error', $json) ) {
				foreach ($json as $obj) {
					$id_name_projects[$obj->name] = $obj->id;
					$this->id_playerkey_projects[$obj->id] = $obj->playerKey;
				}
			}
		}

		/**
		 * Register the settings with Validation callback
		 */
		if ( !empty($option_group) ) {
	    	register_setting( $option_group, $plugin_name, array($this, 'sv_sanitize') );
		} else {
			add_settings_error( $option_group, 'invalid-{$option_group}', __('There is an internal error. Contact Samba Videos support!', 'samba-videos') );
		}

	    $sv_settings_form = new Samba_Videos_Admin_Settings_Form();
	    $sv_settings_form->add_settings_section($plugin_name);
	    $sv_settings_form->add_settings_fields($plugin_name, $id_name_projects);
	}

	/**
	 * Sanitize all information that users put on Admin settings screen
	 *
	 * @since    1.0.0
	 * @param    array(mixed)    $input    Information that user chooses and puts.
	 */
	public function sv_sanitize($input){
		$output = array();
		$original_values = SV_Utilities::get_plugin_settings();

		foreach( $input as $key => $value ) {
			$hasError = false;

			switch ($key) {
				case 'sv_pid': 
					if ($value == 0) {
						add_settings_error( $key, 'invalid-{$key}', __('You have to select a Samba Videos project.', 'samba-videos') );
					}
	    		break;
				case 'sv_width':
					$value = absint($value);

					if ( $value == 0 ){
						$value = 640;
					}

					if ($value < 320) {
						$hasError = true;
						add_settings_error( $key, 'invalid-{$key}', __('Width cannot be set at less than 320.', 'samba-videos') );
					}
				break;
				case 'sv_height':
					$value = absint($value);

					if ( $value == 0 ){
						$value = 360;
					}

					if ( $value < 168) {
						$hasError = true;
						add_settings_error( $key, 'invalid-{$key}', __('Height cannot be set at less than 168.', 'samba-videos') );
					}
				break;
				case 'sv_html5':
				case 'sv_resume':
				case 'sv_cast':
				case 'sv_autoStart':
					$value = boolval($value);
				break;
				case 'sv_age':
					if ( !empty($value) ) {
						$value = absint($value);
						if ($value > 100) {
							$hasError = true;
							add_settings_error( $key, 'invalid-{$key}', __('Age cannot exceed 100.', 'samba-videos') );
						}
					}
				break;
				case 'sv_volume':
					$value = absint($value);
					if ($value < 0 || $value > 100) {
						$hasError = true;
						add_settings_error( $key, 'invalid-{$key}', __('The volume must be between 0 and 100.', 'samba-videos') );
					}
				break;
				
				default:
					$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
				break;
			}

			if ( $hasError ) {
				$output[$key] = $original_values[$key];
			} else {
				if ($key == 'sv_pid') {
					if ( is_null(SV_Utilities::get_player_hash()) ) {
						SV_Utilities::add_option('sv_playerKey', $this->id_playerkey_projects[$value]);
					} else {
						SV_Utilities::update_option('sv_playerKey', $this->id_playerkey_projects[$value]);
					}
				}
				$output[$key] = strip_tags( stripslashes( $value ) );
			}
		}

		return apply_filters( array($this, 'sv_sanitize'), $output, $input );
	}

}
