<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Coinscribble_Integration
 * @subpackage Coinscribble_Integration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Coinscribble_Integration
 * @subpackage Coinscribble_Integration/admin
 */
class Coinscribble_Integration_Admin {

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

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/coinscribble-integration-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coinscribble-integration-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'coinscribbleJsObject', [
			'statuses' => [
				'failed' => __('Failed', 'coinscribble-integration')
			]
		]);
		wp_enqueue_script( $this->plugin_name );

	}

    public function add_page_setting_to_menu()
    {
	    add_menu_page('Coinscribble', 'Coinscribble', 'manage_options', 'coinscribble-setup', array($this, 'display_plugin_setup_page'), 'data:image/svg+xml;base64, ' . base64_encode(file_get_contents(plugin_dir_path (__FILE__ )  . 'img/dashicon.svg')));
	    add_submenu_page('coinscribble-setup', 'Transactions', 'Transactions', 'manage_options', 'coinscribble-transactions' , array($this, 'display_plugin_transactions'));
	    add_submenu_page('coinscribble-setup', 'Posts', 'Posts', 'manage_options', 'coinscribble-posts' , array($this, 'display_plugin_posts'));
    }

    public function display_plugin_setup_page()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/coinscribble-integration-admin-setup.php';
    }

    public function display_plugin_transactions()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/coinscribble-integration-admin-overview.php';
    }

    public function display_plugin_posts()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/coinscribble-integration-admin-posts.php';
    }

	public function coinscribble_token_saving() {
		if (!current_user_can( 'manage_options' )) {
			wp_send_json_error(['error' => __("You do not have enough permissions", 'coinscribble-integration')]);
		} elseif (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), Coinscribble_Integration_Nonce_Actions::COINSCRIBBLE_TOKEN_NONCE)) {
			wp_send_json_error(['error' => __("You do not have enough permissions", 'coinscribble-integration')]);
		} elseif (empty($_POST['token'])) {
			wp_send_json_error(['error' => __("Token is required", 'coinscribble-integration')]);
		}

		Coinscribble_Integration_License_Config::set_key(sanitize_text_field($_POST['token']));
		$response = Coinscribble_Integration_Service::get_instance()->token_activation(sanitize_text_field($_POST['token']));

		if ($response['success']) {
			Coinscribble_Integration_License_Config::set_status(Coinscribble_Integration_License_Statuses::ACTIVATED);
			Coinscribble_Integration_License_Config::set_access_token($response['access_token']);
			if (Coinscribble_Integration_License_Config::get_status() == Coinscribble_Integration_License_Statuses::ACTIVATED) {
				$service = Coinscribble_Integration_Service::get_instance();
				$settings_request_args = [];
				foreach (Coinscribble_Integration_Categories_Configs::get_all_settings() as $key => $category_settings) {
					$settings_request_args[$key]['allow_publish'] = $category_settings['allow_posting'];
				}
				$requestResult = $service->send_categories_setings($settings_request_args);

				if ( !$requestResult['success'] ) {
					Coinscribble_Integration_Categories_Configs::clear_setting('allow_posting');
				}

				$requestResult = $service->update_preferred_payment_method(Coinscribble_Integration_Payment_Configs::get_preferred_payment_method() ?? 0, Coinscribble_Integration_Payment_Configs::get_additional_info());

				if ( !$requestResult['success'] ) {
					Coinscribble_Integration_Payment_Configs::clear_all();
				}
			}
			wp_send_json_success(['message' => $response['message'], 'status' => [Coinscribble_Integration_License_Statuses::ACTIVATED => Coinscribble_Integration_License_Config::get_label_status()]]);
		}

		Coinscribble_Integration_License_Config::set_status(Coinscribble_Integration_License_Statuses::FAILED);
		wp_send_json_error(['error' => $response['message']]);

	}

	public function coinscribble_settings_saving() {
		if (!current_user_can( 'manage_options' )) {
			wp_send_json_error(['error' => __("You do not have enough permissions", 'coinscribble-integration')]);
		} elseif (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), Coinscribble_Integration_Nonce_Actions::COINSCRIBBLE_SETTINGS_NONCE)) {
			wp_send_json_error(['error' => __("You do not have enough permissions", 'coinscribble-integration')]);
		}

		$types = Coinscribble_Integration_Categories_Configs::get_content_types();

		$categories_allow_posting = [];
		foreach ($types as $slug => $label) {
			if(!isset($_POST[$slug])) {
				wp_send_json_error(['error' => $label. __(" is required", 'coinscribble-integration')]);
			} else if (is_wp_error(get_the_category_by_ID(intval($_POST[$slug])))) {
				Coinscribble_Integration_Error_Notification::add_error(Coinstribble_Integration_Notice_Types::CATEGORY_ERROR, __('Category not found, please set up categorization in coinscribble setup page!', 'coinscribble-integration'));
				wp_send_json_error(['error' => __("Category is not exists!", 'coinscribble-integration')]);
			} else{
				Coinscribble_Integration_Categories_Configs::set_settings_for_category($slug, intval($_POST[$slug]), intval($_POST['allow_posting_'. $slug] ?? 0));
				$categories_allow_posting[$slug]['allow_publish'] = intval($_POST['allow_posting_'. $slug] ?? 0);
			}
		}
		Coinscribble_Integration_Error_Notification::clear_error(Coinstribble_Integration_Notice_Types::CATEGORY_ERROR);

		if (Coinscribble_Integration_License_Config::get_status() == Coinscribble_Integration_License_Statuses::ACTIVATED) {
			$service = Coinscribble_Integration_Service::get_instance();

			$requestResult = $service->send_categories_setings($categories_allow_posting);

			if ( !$requestResult['success'] ) {
				Coinscribble_Integration_Categories_Configs::clear_setting('allow_posting');
			}
		}

		wp_send_json_success(['message' => __('Successfully saved!', 'coinscribble-integration')]);

	}

	public function coinscribble_update_transactions() {
		if (!current_user_can( 'manage_options' )) {
			wp_send_json_error(['error' => __("You do not have enough permissions", 'coinscribble-integration')]);
		} elseif (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), Coinscribble_Integration_Nonce_Actions::COINSCRIBBLE_TRANSACTIONS_UPDATE_NONCE)) {
			wp_send_json_error(['error' => __("You do not have enough permissions", 'coinscribble-integration')]);
		}

		$result = Coinscribble_Integration_Service::get_instance()->update_transactions();
		if (!$result['success']) {
			wp_send_json_error(['error' => $result['message']]);
		};

		wp_send_json_success(['message' => __('Successfully saved!', 'coinscribble-integration')]);

	}

	public function coinscribble_payment_info_save() {
		if (!isset($_POST['nonce']) || !current_user_can( 'manage_options' ) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), Coinscribble_Integration_Nonce_Actions::COINSCRIBBLE_PAYMENT_NONCE)) {
			wp_send_json_error(['error' => __("You do not have enough permissions", 'coinscribble-integration')]);
		}

		if ( ! empty( $_POST['method_detail'] ) && ( intval($_POST['payment_method']) == Coinscribble_Integration_Payment_Methods::PAYPAL || intval($_POST['payment_method'] == Coinscribble_Integration_Payment_Methods::BANK_TRANSFER )) ) {
			if(!filter_var($_POST['method_detail'], FILTER_VALIDATE_EMAIL)) {
				wp_send_json_error(['error' => __("Additional input must be eMail!", 'coinscribble-integration')]);
			}
		}

		$result = Coinscribble_Integration_Service::get_instance()->update_preferred_payment_method(intval($_POST['payment_method']), sanitize_text_field($_POST['method_detail']));
		if (!$result['success']) {
			wp_send_json_error(['error' => __('Can`t save preferred payment method, first activate your token!', 'coinscribble-integration')]);
		};

		Coinscribble_Integration_Payment_Configs::set_preferred_payment_method(intval($_POST['payment_method']), sanitize_text_field($_POST['method_detail']));

		wp_send_json_success(['message' => __('Successfully saved!', 'coinscribble-integration')]);

	}

	public function coinscribble_run_migrations() {
        Coinscribble_Integration_Migration::run_create();
	}
}
