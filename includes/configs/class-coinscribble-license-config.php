<?php

class Coinscribble_Integration_License_Config
{
	const OPTION_NAME = 'coinscribble_licens_data';

	public static function get_status()
	{
		return get_option(self::OPTION_NAME)['status'] ?? Coinscribble_Integration_License_Statuses::NOT_ACTIVATED;
	}

	public static function get_label_status()
	{
		$labels = [Coinscribble_Integration_License_Statuses::NOT_ACTIVATED => __('Not activated', 'coinscribble-integration') , Coinscribble_Integration_License_Statuses::FAILED => __('Failed', 'coinscribble-integration'), Coinscribble_Integration_License_Statuses::ACTIVATED => __('Activated', 'coinscribble-integration')];
		return $labels[self::get_status()];
	}

	public static function set_status(string $status)
	{
		if (in_array($status,  Coinscribble_Integration_License_Statuses::ALL_STATUSES))
		{
			$option = get_option(self::OPTION_NAME);
			$option['status'] = $status;
			return update_option(self::OPTION_NAME, $option);
		}
		return false;
	}

	public static function set_access_token(string $access_token)
	{
			$option = get_option(self::OPTION_NAME);
			$option['access_token'] = $access_token;
			return update_option(self::OPTION_NAME, $option);
	}

	public static function get_access_token()
	{
		return get_option(self::OPTION_NAME)['access_token'] ?? '';
	}

	public static function set_key(string $key)
	{
		$option['key'] = $key;
		return update_option(self::OPTION_NAME, $option);
	}

	public static function get_key()
	{
		return trim(get_option(self::OPTION_NAME)['key'] ?? '');
	}

	public static function clear_config() {
		return delete_option(self::OPTION_NAME);
	}
}
