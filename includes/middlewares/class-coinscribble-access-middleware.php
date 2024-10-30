<?php

class Coinscribble_Integration_Access_Middleware {
	public static function run( \WP_REST_Request $request ) {
		$config_key = Coinscribble_Integration_License_Config::get_key();
		$api_key = $request->get_param('api_key');
		if ((empty($api_key) || empty($config_key)) || $api_key !== $config_key) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Access denied!', 'coinscribble-integration'),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}
}