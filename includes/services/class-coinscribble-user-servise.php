<?php

class Coinscribble_Integration_User_Service
{
	private static $instance = null;

	private function __construct() {
	}

	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new Coinscribble_Integration_User_Service();
		}

		return self::$instance;
	}
    public function create_if_not_exists_user()
    {
        Coinscribble_Integration_Error_Notification::clear_error(Coinstribble_Integration_Notice_Types::USER_ERROR);
        if ($id = $this->get_user_id()) {
            return $id;
        }
        $result = wp_create_user(Coinscribble_Integration_User_Config::get_coinscribble_user_name(), wp_generate_password());
        if ($result instanceof WP_Error){
            Coinscribble_Integration_Error_Notification::add_error(Coinstribble_Integration_Notice_Types::USER_ERROR, $result->get_error_message());
            return false;
        }
        return $result;
    }

    public function get_user_id()
    {
        return username_exists(Coinscribble_Integration_User_Config::get_coinscribble_user_name());
    }
}
