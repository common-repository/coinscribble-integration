<?php

class Coinscribble_Integration_Transactions_Repository {
	private static $instance = null;

	private function __construct() {
	}

	public static function get_instance() {
		if (self::$instance == null) {
			self::$instance = new Coinscribble_Integration_Transactions_Repository();
		}
		return self::$instance;
	}

	public function get_last_updated_data() {
		global $wpdb;
		return $wpdb->get_var("SELECT updated_at FROM {$wpdb->prefix}coinscribble_transactions ORDER BY updated_at DESC LIMIT 1") ?? 0;
	}

	public function insert_or_update( int $transaction_id, string $payment_method, float $to_pay, float $paid, string $receipt_link, string $note, string $created_at, string $updated_at ) {
		global $wpdb;
		$format = ['%d', '%s', '%f', '%f', '%s', '%s', '%s'];
		$values = ['transactions_id' => $transaction_id, 'payment_method' => $payment_method, 'to_pay' => $to_pay, 'paid' => $paid, 'receipt_link' => $receipt_link, 'note' => $note, 'created_at' => $created_at, 'updated_at' => $updated_at];
		$id = $this->excists($transaction_id);
		if ($id) {
			$result = $wpdb->update("{$wpdb->prefix}coinscribble_transactions", $values, ['transactions_id' => $transaction_id], $format, ['%d']);
        } else {
			$result = $wpdb->insert("{$wpdb->prefix}coinscribble_transactions", $values, $format);
        }
        if (!$result) {
            error_log($wpdb->last_error);
        }
        return $result;
	}

	public function excists(int $transaction_id) {
		global $wpdb;

		return $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}coinscribble_transactions WHERE transactions_id = %d" , $transaction_id)) ?? false;
	}

	public function get_transactions( int $page, int $limit ) {
		global $wpdb;

		$result = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}coinscribble_transactions ORDER BY created_at DESC LIMIT %d OFFSET %d;", $limit, ($page - 1) * $limit));

		if ($wpdb->last_error) {
			error_log($wpdb->last_error);
		}
		return $result;
	}

	public function get_total() {
		global $wpdb;

		return $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}coinscribble_transactions") ?? 0;
	}
}
