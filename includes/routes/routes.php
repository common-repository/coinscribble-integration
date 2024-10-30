<?php

class Coinscribble_Integration_Api_Routes {
	public function register_routes(): void {
		register_rest_route( '/coinscribble-integration/post', '/create', array(
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [(new Coinscribble_Integration_Post_Controller()), 'create_item'],
			'permission_callback' => [Coinscribble_Integration_Access_Middleware::class, 'run'],
			'args'                => [
				'title' => [
					'type' => 'string',
					'required'          => true,
					'validate_callback' =>  function($param, $request, $key) {
						return !empty( trim($param) );
					},
				],
				'content' => [
					'type' => 'string',
					'required'          => true,
					'validate_callback' => function($param, $request, $key) {
						return !empty( trim($param) );
					},
				],
				'post_type' => [
					'type' => 'string',
					'required'          => true,
					'validate_callback' => function($param, $request, $key) {
						return !empty( trim($param) );
					},
				],
				'featured_image' => [
					'type' => 'string',
					'required'          => false,
					'validate_callback' => function($param, $request, $key) {
						return (!empty($param) ? wp_http_validate_url( $param) : true );
					},
				],
			]
		) );

		register_rest_route( '/coinscribble-integration/post', '/update', array(
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [(new Coinscribble_Integration_Post_Controller()), 'update_item'],
			'permission_callback' => [Coinscribble_Integration_Access_Middleware::class, 'run'],
			'args'                => [
				'title' => [
					'type' => 'string',
					'required'          => true,
					'validate_callback' =>  function($param, $request, $key) {
						return !empty( trim($param) );
					},
				],
				'content' => [
					'type' => 'string',
					'required'          => true,
					'validate_callback' => function($param, $request, $key) {
						return !empty( trim($param) );
					},
				],
				'featured_image' => [
					'type' => 'string',
					'required'          => false,
					'validate_callback' => function($param, $request, $key) {
						return (!empty($param) ? wp_http_validate_url( $param) : true );
					},
				],
				'post_type' => [
					'type' => 'string',
					'required'          => true,
					'validate_callback' => function($param, $request, $key) {
						return !empty( trim($param) );
					},
				],
				'post_id' => [
					'type' => 'int',
					'required'          => true,
					'validate_callback' => function($param, $request, $key) {
						return !empty($param);
					},
				],
			]
		) );

	}
}
