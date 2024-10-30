<?php

class Coinscribble_Integration_Posts_Repository {
	private static $instance = null;
	private function __construct() {
	}

	public static function get_instance() {
		if (self::$instance == null) {
			self::$instance = new Coinscribble_Integration_Posts_Repository();
		}
		return self::$instance;
	}

	public function get_total() {
		global $wpdb;

		return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->postmeta where meta_key = %s and meta_value = 1", Coinscribble_Integration_Meta_Keys::IS_Coinscribble_POST)) ?? 0;
	}

	public function get_posts_for_coinscribble_page(int $page, int $limit ) {
        global $wpdb;

        $result = $wpdb->get_results($wpdb->prepare(
            "SELECT id, post_title, guid, post_date
                    FROM $wpdb->posts
                    inner join $wpdb->postmeta on $wpdb->posts.id = $wpdb->postmeta.post_id
                        and $wpdb->postmeta.meta_key = %s
                        and meta_value = 1
                    ORDER BY post_date DESC
                    LIMIT %d
                    OFFSET %d;",
            Coinscribble_Integration_Meta_Keys::IS_Coinscribble_POST,
            $limit,
            ($page - 1) * $limit));

        if ($wpdb->last_error) {
            error_log($wpdb->last_error);
        }
        return $result;
	}
}
