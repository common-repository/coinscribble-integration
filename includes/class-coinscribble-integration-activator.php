<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    Coinscribble_Integration
 * @subpackage Coinscribble_Integration/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Coinscribble_Integration
 * @subpackage Coinscribble_Integration/includes
 */
class Coinscribble_Integration_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        $key = Coinscribble_Integration_License_Config::get_key();
        if (!empty($key)){
            $result = Coinscribble_Integration_Service::get_instance()->token_activation($key);
            if ($result['success']) {
                Coinscribble_Integration_License_Config::set_status(Coinscribble_Integration_License_Statuses::ACTIVATED);
                Coinscribble_Integration_License_Config::set_access_token($result['access_token']);
            } else {
                Coinscribble_Integration_License_Config::set_status(Coinscribble_Integration_License_Statuses::NOT_ACTIVATED);
            }
        } else {
            Coinscribble_Integration_License_Config::set_status(Coinscribble_Integration_License_Statuses::NOT_ACTIVATED);
        }

        Coinscribble_Integration_Migration::run_create();

	}

}
