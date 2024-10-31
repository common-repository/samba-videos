<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/fagnervalente
 * @link       https://github.com/jefmoura
 * @since      1.0.0
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/admin
 * @author     Fagner Valente <fagner.valente@sambatech.com.br>
 * @author     Jeferson Moura <jeferson.moura@sambatech.com.br>
 */
class Samba_Videos_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->load_dependencies();
		SV_Utilities::set_constants();

		new SV_Admin_Templates();
	}

	private function load_dependencies() {

		require_once dirname( __FILE__ ) . '/class-samba-videos-admin-settings-screen.php';
	}

	/**
	 * It's called in initialization to build the Admin Settings Screen
	 *
	 * @since    1.0.0
	 */
	public function sv_register_settings(){
		$plugin_admin_settings_screen = new Samba_Videos_Admin_Settings_Screen();

		$plugin_admin_settings_screen->build_settings_form($this->plugin_name);
	}

	public function sv_media_menu_strings($strings,  $post){
		$strings['sambaVideosGallery'] = __('Samba Videos Gallery', 'samba-videos');
		$strings['selectMedia'] = __('Select', 'samba-videos');
		$strings['backToGallery'] = __('Back to gallery', 'samba-videos');
		$strings['showMore'] = __('Show more', 'samba-videos');
		return $strings;
	}

	/**
	 * Add the SambaVideos option on Wordpress Settings Menu
	 *
	 * @since    1.0.0
	 */
	public function sv_admin_menu() {
	    add_options_page(
	        $this->plugin_name,
	        'Samba Videos',
	        'manage_options',
	        'samba-videos',
	        array($this, 'sv_display_settings_page')
	    );

	}

	public function sv_display_settings_page(){
		$locale = get_locale();

		if ( !empty($locale) ) {
			$locale = ($locale == 'en_US' || $locale == 'pt_BR') ? $locale : 'en_US';
		} else {
			$locale = 'en_US'; //locale default is en_US
		}

		if ( !defined('SV_ACCESS_TOKEN') ) {
			?>
			<div class="wrap">
				<br/>
				<div id="setting-error-settings_error" class="error settings-error notice is-dismissible"> 
					<p><strong><?php _e('First, you have to set up an app on Samba Videos', 'samba-videos') ?></strong></p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="wrap">
				<h1><img src="<?php  echo(plugins_url('/images/sv-settings-'.$locale.'.png', __FILE__));?>" height="51" width="220"/></h1>
				<form method="post" action="options.php">
					<table>
						<tbody>
							<?php 
								settings_fields($this->plugin_name); 
								do_settings_sections($this->plugin_name);
							?>
						</tbody>
					</table>
					<p class="submit">  
						<input type="submit" class="button-primary" value="<?php _e('Save changes', 'samba-videos') ?>" />  
					</p>  
				</form>
			</div>
			<?php
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function sv_enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/samba-videos-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function sv_enqueue_scripts() {
		$options_data = SV_Utilities::get_option($this->plugin_name);

		$options_data['player_url'] = SAMBA_PLAYER_URL;
		$options_data['sv_playerKey'] = SV_Utilities::get_option('sv_playerKey');

		wp_enqueue_script('jquery');

		//TODO: Remove JS script to build URL
		wp_register_script( $this->plugin_name, plugins_url('js/samba-videos.js', __FILE__), array('jquery'), false, true);

		if( !empty($options_data) && is_array($options_data) ) {
			wp_localize_script( $this->plugin_name, 'CONFIG', $options_data );
		}
		
		wp_enqueue_script( $this->plugin_name );

		wp_register_script( 'samba-videos-media-gallery', plugins_url('js/samba-videos-media-gallery.js', __FILE__), array('media-views'), false, true);
		wp_enqueue_script( 'samba-videos-media-gallery' );
	}

}
