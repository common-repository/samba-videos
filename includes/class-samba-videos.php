<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/fagnervalente
 * @since      1.0.0
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 * @author     Fagner Valente <fagner.valente@sambatech.com.br>
 */
class Samba_Videos {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Samba_Videos_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	protected $plugin_js_name;

	/**
	 * The title of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_title    
	 */
	protected $plugin_title;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	protected $domain_name;

	protected $templates;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'samba-videos';
		$this->plugin_js_name = 'SV';
		$this->plugin_title = 'Samba Videos';
		$this->version = '1.0.0';

		//$this->domain_name = preg_replace('/^www\./','',$_SERVER['SERVER_NAME']);

		$this->load_dependencies();

		$this->sv_up_endpoints();

		$this->sv_set_locale();
		$this->sv_define_admin_hooks();
		$this->sv_define_proxy_hooks();
		$this->sv_define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Samba_Videos_Loader. Orchestrates the hooks of the plugin.
	 * - Samba_Videos_i18n. Defines internationalization functionality.
	 * - Samba_Videos_Admin. Defines all hooks for the admin area.
	 * - Samba_Videos_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-samba-videos-templates.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-samba-videos-utilities.php';

		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/samba-videos-short-code.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-samba-videos-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-samba-videos-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-samba-videos-admin.php';

		/**
		 * The class responsible for defining all HTTP request to Samba Videos plataform.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-samba-videos-proxy.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-samba-videos-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-samba-videos-notification.php';


		$this->loader = new Samba_Videos_Loader();

	}


	private function sv_up_endpoints() {
		$plugin_endpoints = new Samba_Videos_App_Listen();
	}


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Samba_Videos_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function sv_set_locale() {

		$plugin_i18n = new Samba_Videos_i18n();

		$this->loader->sv_add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function sv_define_admin_hooks() {

		$plugin_admin = new Samba_Videos_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->sv_add_action( 'admin_enqueue_scripts', $plugin_admin, 'sv_enqueue_styles' );
		$this->loader->sv_add_action( 'admin_enqueue_scripts', $plugin_admin, 'sv_enqueue_scripts' );

		$this->loader->sv_add_action( 'admin_menu', $plugin_admin, 'sv_admin_menu' );
		$this->loader->sv_add_action( 'admin_init', $plugin_admin, 'sv_register_settings' );

		$this->loader->sv_add_filter( 'media_view_strings', $plugin_admin, 'sv_media_menu_strings', 10, 2);
	}

	/**
	 * Register all of the hooks related to the proxy functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function sv_define_proxy_hooks() {

		$plugin_proxy = new Samba_Videos_Proxy();

		$this->loader->sv_add_action( 'wp_ajax_proxy_sv_request', $plugin_proxy, 'ajax_proxy_sv' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function sv_define_public_hooks() {

		$plugin_public = new Samba_Videos_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->sv_add_action( 'wp_enqueue_scripts', $plugin_public, 'sv_enqueue_styles' );
		$this->loader->sv_add_action( 'wp_enqueue_scripts', $plugin_public, 'sv_enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Samba_Videos_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
