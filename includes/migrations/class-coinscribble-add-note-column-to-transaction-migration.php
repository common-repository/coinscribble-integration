<?php

class Coinscribble_Integration_Add_Note_Column_Transaction_Migration
{
    public static function run_create() {
        global $wpdb;
	    $wpdb->query("ALTER TABLE ". $wpdb->prefix ."coinscribble_transactions 
                ADD COLUMN note text null;
			");
		if (!empty($wpdb->last_error)) {
			error_log('CREATE TABLE coinscribble_transactions ' . $wpdb->last_error);

            return ['success' => false, 'message' =>'CREATE TABLE coinscribble_transactions ' . $wpdb->last_error];
        }
        return ['success' => true];
    }

    public static function run_delete() {
        global $wpdb;
	    $wpdb->query("ALTER TABLE ". $wpdb->prefix ."coinscribble_transactions 
                DROP COLUMN note text null;
			");		if (!empty($wpdb->last_error)) {
			error_log('DROP TABLE coinscribble_transactions ' . $wpdb->last_error);
            return ['success' => false, 'message' =>'DROP TABLE coinscribble_transactions ' . $wpdb->last_error];
        }
        return ['success' => true];
    }
}