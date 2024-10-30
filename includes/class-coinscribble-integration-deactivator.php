<?php

/**
 * Fired during plugin deactivation
 *
 * @since      1.0.0
 *
 * @package    Coinscribble_Integration
 * @subpackage Coinscribble_Integration/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Coinscribble_Integration
 * @subpackage Coinscribble_Integration/includes
 */
class Coinscribble_Integration_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		Coinscribble_Integration_Service::get_instance()->logout();
		Coinscribble_Integration_License_Config::set_status(Coinscribble_Integration_License_Statuses::NOT_ACTIVATED);
	}

}
