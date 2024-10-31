<?php

include 'class-samba-videos-admin-display.php';

/**
 * The settings-form-specific functionality of the plugin.
 *
 * @link       https://github.com/jefmoura
 * @since      1.0.0
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 */

/**
 * The settings-form-specific functionality of the plugin.
 *
 * Builds the settings form
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 * @author     Jeferson Moura <jeferson.moura@sambatech.com.br>
 */
class Samba_Videos_Admin_Settings_Form extends Samba_Videos_Admin_Display {

	/**
	 * Add the Admin Settings section
	 *
	 * @since    1.0.0
	 * @param    string           $plugin_name       The plugin name.
	 */
	public function add_settings_section($plugin_name) {
		add_settings_section( $plugin_name, '', array($this, 'sv_display_section'), $plugin_name );
	}

	/**
	 * Add the Admin Settings fields
	 *
	 * @since    1.0.0
	 * @param    string           $plugin_name       The plugin name.
	 * @param    array(string)    $id_name_projects  Name of Samba Videos user's projects
	 */
	public function add_settings_fields($plugin_name, $id_name_projects) {

		$option_group = $plugin_name;

		if ( !empty($option_group) ) {
			add_settings_field( 'sv_pid', __('Project', 'samba-videos'), array($this, 'sv_display_general_input'), $option_group, $plugin_name, array(
				'type'      		=> 'select',
				'id'        		=> 'sv_pid',
				'name'      		=> 'pid',
				'option_group' 	=> $option_group,
				'desc'      		=> __('Project name', 'samba-videos'),
				'label_for' 		=> 'pid',
				'select_values'	=>	$id_name_projects,
				'class'     		=> ''
			) );

			add_settings_field( 'sv_wigth', 'Width', array($this, 'sv_display_general_input'), $option_group, $plugin_name, array(
				'type'      		=> 'text',
				'id'        		=> 'sv_width',
				'name'      		=> 'width',
				'option_group' 	=> $option_group,
				'desc'      		=> __('Video width', 'samba-videos'),
				'label_for' 		=> 'width',
				'class'     		=> ''
			) );

			add_settings_field( 'sv_height', 'Height', array($this, 'sv_display_general_input'), $option_group, $plugin_name, array(
				'type'      		=> 'text',
				'id'        		=> 'sv_height',
				'name'      		=> 'height',
				'option_group' 	=> $option_group,
				'desc'      		=> __('Video height', 'samba-videos'),
				'label_for' 		=> 'height',
				'class'     		=> ''
			) );

			add_settings_field( 'sv_html5', 'html5', array($this, 'sv_display_general_input'), $option_group, $plugin_name, array(
				'type'      		=> 'checkbox',
				'id'        		=> 'sv_html5',
				'name'      		=> 'html5',
				'option_group' 	=> $option_group,
				'desc'      		=> __('Force Player Samba to play in HTML5 in supported browsers and devices.', 'samba-videos'),
				'label_for' 		=> 'html5',
				'checked' 		=> '',
				'class'     		=> ''
			) );

			add_settings_field( 'sv_cast', 'Chrome Cast', array($this, 'sv_display_general_input'), $option_group, $plugin_name, array(
				'type'      		=> 'checkbox',
				'id'        		=> 'sv_cast',
				'name'      		=> 'cast',
				'option_group' 	=> $option_group,
				'desc'      		=> __('Displays the Chromecast option in Google Chrome.', 'samba-videos'),
				'label_for' 		=> 'cast',
				'checked' 		=> 'checked',
				'class'     		=> ''
			) );

			add_settings_field( 'sv_age', 'Age', array($this, 'sv_display_general_input'), $option_group, $plugin_name, array(
				'type'      		=> 'text',
				'id'        		=> 'sv_age',
				'name'      		=> 'age',
				'option_group' 	=> $option_group,
				'desc'      		=> __('Activates the age restriction behavior of the player.', 'samba-videos'),
				'label_for' 		=> 'age',
				'class'     		=> ''
			) );

			add_settings_field( 'sv_resume', 'Resume', array($this, 'sv_display_general_input'), $option_group, $plugin_name, array(
				'type'      		=> 'checkbox',
				'id'        		=> 'sv_resume',
				'name'      		=> 'resume',
				'option_group' 	=> $option_group,
				'desc'      		=> __('Activates the resume behavior of the player.', 'samba-videos'),
				'label_for' 		=> 'resume',
				'checked' 		=> '',
				'class'     		=> ''
			) );

			add_settings_field( 'sv_auto_start', 'Auto Start', array($this, 'sv_display_general_input'), $option_group, $plugin_name, array(
				'type'      		=> 'checkbox',
				'id'        		=> 'sv_autoStart',
				'name'      		=> 'autoStart',
				'option_group' 	=> $option_group,
				'desc'      		=> __('Sets whether the player will automatically start or not after loading.', 'samba-videos'),
				'label_for' 		=> 'autoStart',
				'checked' 		=> '',
				'class'     		=> ''
			) );

			add_settings_field( 'sv_volume', 'volume', array($this, 'sv_display_general_input'), $option_group, $plugin_name, array(
				'type'      		=> 'text',
				'id'        		=> 'sv_volume',
				'name'      		=> 'volume',
				'option_group' 	=> $option_group,
				'desc'      		=> __('Parameter that allows you to assign the default  volume value of the player before the video starts playing.', 'samba-videos'),
				'label_for' 		=> 'volume',
				'max'				=> 100,
				'class'     		=> ''
	    	) );
		} else {
			add_settings_error( $option_group, 'invalid-{$option_group}', __('There is an internal error. Contact Samba Videos support!', 'samba-videos') );
		}
	}
}
