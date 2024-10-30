<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Coinscribble_Integration
 * @subpackage Coinscribble_Integration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Coinscribble_Integration
 * @subpackage Coinscribble_Integration/public
 */
class Coinscribble_Integration_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function kses_allowed_html($allowedposttags) {
		$allowedposttags['iframe'] = array(
			'align'                 => true,
			'width'                 => true,
			'height'                => true,
			'frameborder'           => true,
			'name'                  => true,
			'src'                   => true,
			'id'                    => true,
			'class'                 => true,
			'style'                 => true,
			'scrolling'             => true,
			'marginwidth'           => true,
			'marginheight'          => true,
			'allowfullscreen'       => true,
			'mozallowfullscreen'    => true,
			'webkitallowfullscreen' => true,
		);

		return $allowedposttags;
	}

}
