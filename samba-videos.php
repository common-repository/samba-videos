<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/fagnervalente
 * @link              https://github.com/jefmoura
 * @since             1.0.0
 * @package           Samba_Videos
 *
 * @wordpress-plugin
 * Plugin Name:       Samba Videos
 * Plugin URI:        https://github.com/sambatech/sambavideos-wordpress-plugin
 * Description:       Samba Video’s plugin allow you to easily embed videos from Samba Videos in just a few clicks.
 * Version:           1.0.0
 * Author:            Fagner Valente & Jeferson Moura
 * Author URI:        https://github.com/fagnervalente
 * Author URI:        https://github.com/jefmoura
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       samba-videos
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-samba-videos-activator.php
 */
function activate_samba_videos() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-samba-videos-activator.php';
	Samba_Videos_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-samba-videos-deactivator.php
 */
function deactivate_samba_videos() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-samba-videos-deactivator.php';
	Samba_Videos_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_samba_videos' );
register_deactivation_hook( __FILE__, 'deactivate_samba_videos' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-samba-videos.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_samba_videos() {

	$plugin = new Samba_Videos();
	$plugin->run();

}
run_samba_videos();
