<?php
class Coinscribble_Integration_Categories_Configs
{
	const OPTION_NAME = 'coinscribble_content_types_cat';
    public static function get_content_types()
    {
        return [
            Coinscribble_Integration_Categories_Slugs::PRESS_RELEASE => __('Press Release', 'coinscribble-integration'),
            Coinscribble_Integration_Categories_Slugs::SPONSORED_ARTICLE => __('Sponsored Article', 'coinscribble-integration'),
            Coinscribble_Integration_Categories_Slugs::ORGANIC_ARTICLE => __('Organic Article', 'coinscribble-integration'),
        ];
    }
    public static function get_settings_for_category($type)
    {
        return get_option(self::OPTION_NAME)[$type] ?? false;
    }
    public static function get_all_settings()
    {
        return get_option(self::OPTION_NAME);
    }
    public static function set_settings_for_category($type, int $id, int $allow_posting)
    {
		$option = get_option(self::OPTION_NAME);
		$option[$type]['id'] = $id;
		$option[$type]['allow_posting'] = $allow_posting;
        update_option(self::OPTION_NAME, $option);
    }
	public static function clear_all() {
		delete_option(self::OPTION_NAME);
	}

	public static function clear_setting( string $string ) {
		$option = get_option(self::OPTION_NAME);
		foreach (self::get_content_types() as $slug => $label) {
			unset($option[$slug][$string]);
		}
		update_option(self::OPTION_NAME, $option);
	}
}
