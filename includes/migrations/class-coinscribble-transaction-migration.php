<?php

class Coinscribble_Integration_Transaction_Migration
{
    public static function run_create() {
        global $wpdb;
	    $wpdb->query("CREATE TABLE IF NOT EXISTS ". $wpdb->prefix ."coinscribble_transactions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY ,
            transactions_id bigint(20) unsigned NOT NULL,
            payment_method VARCHAR(255) NOT NULL,
            to_pay  DECIMAL(10, 2) NOT NULL,
            paid  DECIMAL(10, 2) NOT NULL,
            receipt_link VARCHAR(255),
            created_at TIMESTAMP,
            updated_at TIMESTAMP,
            INDEX (transactions_id, created_at, updated_at)
        )");
		if (!empty($wpdb->last_error)) {
			error_log('CREATE TABLE coinscribble_transactions ' . $wpdb->last_error);

            return ['success' => false, 'message' =>'CREATE TABLE coinscribble_transactions ' . $wpdb->last_error];
        }
        return ['success' => true];
    }

    public static function run_delete() {
        global $wpdb;
	    $wpdb->query("DROP TABLE ". $wpdb->prefix ."coinscribble_transactions ;");
		if (!empty($wpdb->last_error)) {
			error_log('DROP TABLE coinscribble_transactions ' . $wpdb->last_error);
            return ['success' => false, 'message' =>'DROP TABLE coinscribble_transactions ' . $wpdb->last_error];
        }
        return ['success' => true];
    }
}