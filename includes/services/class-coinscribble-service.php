<?php

class Coinscribble_Integration_Service {
	private static $instance = null;
    private string $site_url;

    private function __construct() {
		$this->site_url = defined('COINSCRIBBLE_TEST_SERVER') ? COINSCRIBBLE_TEST_SERVER : 'https://portal.coinscribble.com';
	}

	public static function get_instance() {
		if (self::$instance == null) {
			self::$instance = new Coinscribble_Integration_Service();
		}
		return self::$instance;
	}

	public function token_activation(string $token) {
		$response = wp_remote_request(
			$this->site_url. '/api/plugin-login/', [
				'method' => 'POST',
				'headers' => [
					'Accept'  => 'application/json'
				],
				'body' => [
					'api_key' => $token,
				],
			]
		);

		if (is_wp_error($response)) {
			error_log($response->get_error_message());
			return ['success' => false, 'message' => $response->get_error_message()];
		}
		if (200 !== wp_remote_retrieve_response_code( $response )) {
			error_log('Send status! Code response is - '. wp_remote_retrieve_response_code( $response ));
			return ['success' => false, 'message' => __('Something went wrong!', 'coinscribble-integration')];
		}

		$access_token = json_decode(wp_remote_retrieve_body($response))->access_token ?? '';

		return ['success' => true, 'message' => __('Status Of Plugin Successfully Changed', 'coinscribble-integration'), 'access_token' => $access_token];
	}

	public function send_categories_setings(array $category_settings) {
		$response = wp_remote_request(
			$this->site_url. '/api/allow-posting-for-category/', [
				'method' => 'POST',
				'headers' => [
					'Accept'  => 'application/json',
					'Authorization' => 'Bearer ' . Coinscribble_Integration_License_Config::get_access_token(),
				],
				'body' => [
					'token' => Coinscribble_Integration_License_Config::get_key(),
					'allow_publish' => [
						Coinscribble_Integration_Categories_Slugs::PRESS_RELEASE => $category_settings[Coinscribble_Integration_Categories_Slugs::PRESS_RELEASE]['allow_publish'] ?? 0,
						Coinscribble_Integration_Categories_Slugs::ORGANIC_ARTICLE => $category_settings[Coinscribble_Integration_Categories_Slugs::ORGANIC_ARTICLE]['allow_publish'] ?? 0,
						Coinscribble_Integration_Categories_Slugs::SPONSORED_ARTICLE => $category_settings[Coinscribble_Integration_Categories_Slugs::SPONSORED_ARTICLE]['allow_publish'] ?? 0,
					]
				],
			]
		);

		if (is_wp_error($response)) {
			error_log($response->get_error_message());
			return ['success' => false, 'message' => $response->get_error_message()];
		}
		if (200 !== wp_remote_retrieve_response_code( $response )) {
			error_log('Send status! Code response is - '. wp_remote_retrieve_response_code( $response ). 'Message'. $response['message'] ?? '');
			return ['success' => false, 'message' => __('Something went wrong!', 'coinscribble-integration')];
		}

		return ['success' => true, 'message' => __('Settings saved!', 'coinscribble-integration'),];
	}

	public function update_transactions() {
		$token = Coinscribble_Integration_License_Config::get_key();
		if (!empty($token)) {
			$repository = Coinscribble_Integration_Transactions_Repository::get_instance();
			$response = wp_remote_request(
				$this->site_url. '/api/get-transactions/', [
					'method' => 'GET',
					'headers' => [
						'Authorization' => 'Bearer ' . Coinscribble_Integration_License_Config::get_access_token(),
						'Accept'  => 'application/json'
					],
					'body' => [
						'token' => $token,
						'date_from' => strtotime($repository->get_last_updated_data())
					],
				]
			);

			if (is_wp_error($response)) {
				error_log($response->get_error_message());
				return ['success' => false, 'message' => $response->get_error_message()];
			}
			if (200 !== wp_remote_retrieve_response_code( $response )) {
				error_log(__('Send status! Code response is - ', 'coinscribble-integration'). wp_remote_retrieve_response_code( $response ));
				return ['success' => false, 'message' => __('Something went wrong!', 'coinscribble-integration')];
			}

			$transactions_group = json_decode(wp_remote_retrieve_body($response));
			$transactions_group = array_chunk($transactions_group, 10);

			foreach ($transactions_group as $transactions ) {
				foreach ($transactions as $transaction) {
					$repository->insert_or_update($transaction->id, $transaction->payment_method, $transaction->to_pay, $transaction->paid, $transaction->receipt_link, $transaction->note, $transaction->created_at, $transaction->updated_at);
				}
			}

			return ['success' => true, 'message' => __('Successfully updated!', 'coinscribble-integration')];
		}
		error_log('Coinscribble Token is empty!');
		return ['success' => false, 'message' => __('Coinscribble Token is empty!', 'coinscribble-integration')];
	}

	public function logout() {
		$response = wp_remote_request(
			$this->site_url. '/api/plugin-logout/', [
				'method' => 'POST',
				'headers' => [
					'Authorization' => 'Bearer ' . Coinscribble_Integration_License_Config::get_access_token(),
					'Accept'  => 'application/json'
				],
				'body' => [
					'api_key' => Coinscribble_Integration_License_Config::get_key(),
				],
			]
		);

		if (is_wp_error($response)) {
			error_log($response->get_error_message());
			return ['success' => false, 'message' => $response->get_error_message()];
		}
		if (200 !== wp_remote_retrieve_response_code( $response )) {
			error_log('Send status! Code response is - '. wp_remote_retrieve_response_code( $response ));
			return ['success' => false, 'message' => __('Something went wrong!', 'coinscribble-integration')];
		}

		return ['success' => true];

	}

	public function update_preferred_payment_method( int $payment_method, string $method_detail = '' ) {
		$response = wp_remote_request(
			$this->site_url. '/api/preferred-payment-method/save', [
				'method' => 'POST',
				'headers' => [
					'Authorization' => 'Bearer ' . Coinscribble_Integration_License_Config::get_access_token(),
					'Accept'  => 'application/json'
				],
				'body' => [
					'token' => Coinscribble_Integration_License_Config::get_key(),
					'method_detail' => $method_detail,
					'payment_method' => $payment_method,
				],
			]
		);

		if (is_wp_error($response)) {
			error_log($response->get_error_message());
			return ['success' => false, 'message' => $response->get_error_message()];
		}
		if (200 !== wp_remote_retrieve_response_code( $response )) {
			error_log('Send status! Code response is - '. wp_remote_retrieve_response_code( $response ));
			return ['success' => false, 'message' => __('Something went wrong!', 'coinscribble-integration')];
		}

		return ['success' => true];
	}
}
