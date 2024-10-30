<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    Coinscribble_Integration
 * @subpackage Coinscribble_Integration/includes
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
 * @package    Coinscribble_Integration
 * @subpackage Coinscribble_Integration/includes
 */
class Coinscribble_Integration {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Coinscribble_Integration_Loader    $loader    Maintains and registers all hooks for the plugin.
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

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

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
		if ( defined( 'COINSCRIBBLE_INTEGRATION_VERSION' ) ) {
			$this->version = COINSCRIBBLE_INTEGRATION_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'coinscribble-integration';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_notice_hooks();
		$this->define_api_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Coinscribble_Integration_Loader. Orchestrates the hooks of the plugin.
	 * - Coinscribble_Integration_i18n. Defines internationalization functionality.
	 * - Coinscribble_Integration_Admin. Defines all hooks for the admin area.
	 * - Coinscribble_Integration_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coinscribble-integration-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/middlewares/class-coinscribble-access-middleware.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coinscribble-integration-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-coinscribble-integration-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-coinscribble-integration-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/configs/class-coinscribble-user-config.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/configs/class-coinscribble-categories-configs.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/configs/class-coinscribble-payment-configs.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/configs/class-coinscribble-license-config.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/enums/class-coinstribble-notice-types.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/enums/class-coinscribble-categories-slugs.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/enums/class-coinscribble-payment-methods.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/enums/class-coinscribble-license-statuses.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/enums/class-coinscribble-nonce-actions.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/enums/class-coinscribble-meta-keys.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/notifications/class-coinscribble-error-notification.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/routes/routes.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/services/class-coinscribble-user-servise.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/services/class-coinscribble-service.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/migrations/class-coinscribble-migration.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/migrations/class-coinscribble-transaction-migration.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/migrations/class-coinscribble-add-note-column-to-transaction-migration.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/repositories/class-coinscribble-transactions-repository.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/repositories/class-coinscribble-posts-repository.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/services/class-coinscribble-post-service.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/controllers/class-coinscribble-post-controller.php';

		$this->loader = new Coinscribble_Integration_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Coinscribble_Integration_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Coinscribble_Integration_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Coinscribble_Integration_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_page_setting_to_menu' );
		$this->loader->add_action( 'wp_ajax_coinscribble_token_saving', $plugin_admin, 'coinscribble_token_saving' );
		$this->loader->add_action( 'wp_ajax_coinscribble_settings_saving', $plugin_admin, 'coinscribble_settings_saving' );
		$this->loader->add_action( 'wp_ajax_coinscribble_update_transactions', $plugin_admin, 'coinscribble_update_transactions' );
		$this->loader->add_action( 'wp_ajax_coinscribble_payment_info_saving', $plugin_admin, 'coinscribble_payment_info_save' );
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'coinscribble_run_migrations' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Coinscribble_Integration_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter('wp_kses_allowed_html', $plugin_public, 'kses_allowed_html');

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
	 * @return    Coinscribble_Integration_Loader    Orchestrates the hooks of the plugin.
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

    private function define_notice_hooks()
    {
        $error_notices = new Coinscribble_Integration_Error_Notification();
        $this->loader->add_action('admin_notices', $error_notices, 'notice');
    }

	private function define_api_hooks() {
		$r = new Coinscribble_Integration_Api_Routes();
		$this->loader->add_action('rest_api_init', $r, 'register_routes');
	}

}
