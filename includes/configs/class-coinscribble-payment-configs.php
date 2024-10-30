<?php
class Coinscribble_Integration_Payment_Configs
{
	const OPTION_NAME = 'coinscribble_preferred_payment_method';
    public static function get_method_title(int $payment_method_num)
    {
		switch ($payment_method_num) {
			case Coinscribble_Integration_Payment_Methods::PAYPAL: return __('PayPal', 'coinscribble-integration');
			case Coinscribble_Integration_Payment_Methods::USDT: return __('USDT (ERC-20)', 'coinscribble-integration');
			case Coinscribble_Integration_Payment_Methods::USDC: return __('USDC (ERC-20)', 'coinscribble-integration');
			case Coinscribble_Integration_Payment_Methods::BANK_TRANSFER: return __('Bank Transfer', 'coinscribble-integration');
			default: return '';
		}
    }

	public static function get_additional_info_placeholder(int $payment_method_num)
	{
		switch ($payment_method_num) {
			case Coinscribble_Integration_Payment_Methods::BANK_TRANSFER:
			case Coinscribble_Integration_Payment_Methods::PAYPAL: return __('Email address', 'coinscribble-integration');
			case Coinscribble_Integration_Payment_Methods::USDC:
			case Coinscribble_Integration_Payment_Methods::USDT: return __('Wallet address', 'coinscribble-integration');
			default: return '';
		}
	}
    public static function get_preferred_payment_method()
    {
        return get_option(self::OPTION_NAME)['method'] ?? null;
    }
    public static function get_additional_info()
    {
        return get_option(self::OPTION_NAME)['additional_info'] ?? '';
    }
    public static function set_preferred_payment_method(int $payment_method, string $additional_info)
    {
		$option = [];
		$option['method'] = $payment_method;
		$option['additional_info'] = $additional_info;
        update_option(self::OPTION_NAME, $option);
    }
	public static function clear_all() {
		delete_option(self::OPTION_NAME);
	}
}
