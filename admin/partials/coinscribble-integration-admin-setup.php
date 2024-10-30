<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$categories = get_categories(array(
    "hide_empty" => 0,
    "type" => "post",
    'orderby' => 'name',
    'order' => 'ASC'
));

$content_types = Coinscribble_Integration_Categories_Configs::get_content_types();
$categories_settings = Coinscribble_Integration_Categories_Configs::get_all_settings();
$payment_methods = Coinscribble_Integration_Payment_Methods::get_all_methods();
$selected_payment_methods = Coinscribble_Integration_Payment_Configs::get_preferred_payment_method();
$status_token = Coinscribble_Integration_License_Config::get_status();
?>
<div class="wrap">
    <img class="coiscrb_icon" src="<?php echo esc_url(plugin_dir_url (__FILE__ )  . '../img/desktop-logo.png') ?>" alt="icon">
    <form id="coinscribbleTokenSettings" method="post" data-ajax_action="coinscribble_token_saving" action="<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>">
        <div class="coinscribble-overlay"></div>
        <div class="coinscribble-loader"></div>
        <h3><?php echo esc_html__('Token status', 'coinscribble-integration') ?></h3>
	    <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce(Coinscribble_Integration_Nonce_Actions::COINSCRIBBLE_TOKEN_NONCE)) ?>">
        <div class="form-wrap">
            <table class="form-table" role="presentation" >
                <tbody>
                    <tr>
                        <th>
                            <fieldset>
                                <span><?php echo esc_html__('Token', 'coinscribble-integration') ?></span>
                                <legend class="screen-reader-text"><span><?php echo esc_html__('Token', 'coinscribble-integration') ?></span>
                                </legend>
                            </fieldset>
                        </th>
                        <td>
                            <input class="regular-text fill-value" name="token" type="text" value="<?php echo esc_attr(Coinscribble_Integration_License_Config::get_key()) ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <fieldset>
                                <span><?php echo esc_html__('Status', 'coinscribble-integration') ?></span>
                                <legend class="screen-reader-text"><span><?php echo esc_html__('Status', 'coinscribble-integration') ?></span>
                                </legend>
                            </fieldset>
                        </th>
                        <td>
                            <span id="status" class="regular-text fill-value status_<?php echo esc_attr($status_token) ?>" ><?php echo esc_attr(Coinscribble_Integration_License_Config::get_label_status()) ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

		<?php
		submit_button();
		?>
    </form>
    <form id="coinscribblePaymentMethods" method="post" data-ajax_action="coinscribble_payment_info_saving" action="<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>" style="display: <?php echo esc_attr($status_token == Coinscribble_Integration_License_Statuses::ACTIVATED ? 'block' : 'none') ?>">
        <div class="coinscribble-overlay"></div>
        <div class="coinscribble-loader"></div>
        <h3><?php echo esc_html__('Preferred payment method', 'coinscribble-integration') ?></h3>
	    <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce(Coinscribble_Integration_Nonce_Actions::COINSCRIBBLE_PAYMENT_NONCE)) ?>">
        <div class="form-wrap">
            <table class="form-table" role="presentation" >
                <tbody>
                    <tr>
                        <th>
                            <fieldset>
                                <span><?php echo esc_html__('Payment method', 'coinscribble-integration') ?></span>
                                <legend class="screen-reader-text"><span><?php echo esc_html__('Payment method', 'coinscribble-integration') ?></span>
                                </legend>
                            </fieldset>
                        </th>
                        <td>
                            <select class="regular-text fill-value" name="payment_method" id="coinscribbleSelectPaymentMethod">
                                <?php foreach ($payment_methods as $id => $label) : ?>
                                    <option data-placeholder="<?php echo esc_attr(Coinscribble_Integration_Payment_Configs::get_additional_info_placeholder($id)) ?>" value="<?php echo esc_attr($id) ?>" <?php echo esc_attr($id == $selected_payment_methods ? 'selected' : '') ?>><?php echo esc_attr($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <span id="coinscribblePaymentAdditionalInfoLabel"><?php echo esc_html(Coinscribble_Integration_Payment_Configs::get_additional_info_placeholder($selected_payment_methods ?? 1)) ?></span>
                        </th>
                        <td>
                            <input id="coinscribblePaymentAdditionalInfo" class="regular-text fill-value" name="method_detail" placeholder="<?php echo esc_attr(Coinscribble_Integration_Payment_Configs::get_additional_info_placeholder($selected_payment_methods ?? 1)) ?>" type="text" value="<?php
	                        echo esc_attr(Coinscribble_Integration_Payment_Configs::get_additional_info()) ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

		<?php
		submit_button();
		?>
    </form>
    <form id="coinscribbleCategorizationSettings" method="post"  data-ajax_action="coinscribble_settings_saving"  action="<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>" style="display: <?php echo esc_attr($status_token == Coinscribble_Integration_License_Statuses::ACTIVATED ? 'block' : 'none') ?>">
        <div class="coinscribble-overlay"></div>
        <div class="coinscribble-loader"></div>
        <h3><?php echo esc_html__('Content Categorization', 'coinscribble-integration') ?></h3>
        <p class="description"><?php echo esc_html__('Please select which category you would like each content type published to.', 'coinscribble-integration') ?></p>
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce(Coinscribble_Integration_Nonce_Actions::COINSCRIBBLE_SETTINGS_NONCE)) ?>">
        <div class="form-wrap">
            <table class="form-table" role="presentation" data-select2-id="select2-data-21-nxgp">
                <tbody>
                <?php foreach ($content_types as $key => $value): ?>
                <tr>
                    <th>
                        <fieldset>
                            <span><?php echo esc_html($value) ?></span>
                            <legend class="screen-reader-text"><span><?php echo esc_html($value) ?></span>
                            </legend>
                        </fieldset>
                    </th>
                    <th>
                        <fieldset>
                            <legend class="screen-reader-text"><span>
	                    <?php echo esc_html($value) ?></span></legend>
                            <label for="allow_posting_<?php echo esc_attr($key) ?>">
                                <input name="allow_posting_<?php echo esc_attr($key) ?>" type="checkbox" id="allow_posting_<?php echo esc_attr($key) ?>" value="1" <?php echo esc_attr($categories_settings[$key]['allow_posting'] == 1 ? 'checked' : '') ?>>
                                <?php echo esc_html__('Allow posting', 'coinscribble-integration'); ?></label>
                        </fieldset>
                    </th>
                    <td>
                        <label for="coinscribble-category_<?php echo esc_attr($key) ?>">
                            <select class="regular-text fill-value" id="coinscribble-category_<?php echo esc_attr($key) ?>" data-value="<?php echo esc_attr($categories_settings[$key]['id'] ?? false) ?>" name="<?php echo esc_attr($key) ?>">
                                <?php
                                foreach ($categories as $category) :?>
                                    <option value='<?php echo esc_attr($category->term_id) ?>'><?php echo esc_html($category->name) ?></option>;
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

		<?php
		submit_button();
		?>
    </form>
    <h3><?php echo esc_html__('Additional info', 'coinscribble-integration') ?></h3>
    <a href="https://coinbound.io/coinscribble-publisher-docs" target="_blank" class="button button-primary"><?php echo esc_html__('View Docs', 'coinscribble-integration') ?></a>
</div>
