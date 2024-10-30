<?php
class Coinscribble_Integration_Payment_Methods {

	public static function get_all_methods() {
		return [
			self::PAYPAL => Coinscribble_Integration_Payment_Configs::get_method_title(self::PAYPAL),
			self::USDT => Coinscribble_Integration_Payment_Configs::get_method_title(self::USDT),
			self::USDC => Coinscribble_Integration_Payment_Configs::get_method_title(self::USDC),
			self::BANK_TRANSFER => Coinscribble_Integration_Payment_Configs::get_method_title(self::BANK_TRANSFER)];
	}
	const PAYPAL = 1;
	const USDT = 2;
	const USDC = 3;
	const BANK_TRANSFER = 4;
}