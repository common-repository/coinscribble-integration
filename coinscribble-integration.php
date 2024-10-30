<?php

/**
 * The plugin bootstrap file
 *
 * @package           Coinscribble_Integration
 *
 * @wordpress-plugin
 * Plugin Name:       Coinscribble Integration
 * Description:       Integrate Your Site With Coinscribble Platform
 * Version:           1.0.3
 * Author:            Coinscribble
 * Author URI:        https://coinscribble.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       coinscribble-integration
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'COINSCRIBBLE_INTEGRATION_VERSION', '1.0.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-coinscribble-integration-activator.php
 */
function coinscribble_integration_plugin_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-coinscribble-integration-activator.php';
	Coinscribble_Integration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-coinscribble-integration-deactivator.php
 */
function coinscribble_integration_plugin_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-coinscribble-integration-deactivator.php';
	Coinscribble_Integration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'coinscribble_integration_plugin_activate' );
register_deactivation_hook( __FILE__, 'coinscribble_integration_plugin_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-coinscribble-integration.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function coinscribble_integration_plugin_run() {

	$plugin = new Coinscribble_Integration();
	$plugin->run();

}
coinscribble_integration_plugin_run();
