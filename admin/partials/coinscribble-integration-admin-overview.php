<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Coinscribble_Integration
 * @subpackage Coinscribble_Integration/admin/partials
 */

$repository = Coinscribble_Integration_Transactions_Repository::get_instance();

$page = intval($_GET['paged'] ?? 1);
$limit = 10;

$transactions = $repository->get_transactions($page, $limit );
$total = $repository->get_total();
$last_page = intval(ceil($total / $limit));
?>

<div class="wrap">
    <!-- This file should primarily consist of HTML with a little bit of PHP. -->
    <img class="coiscrb_icon" src="<?php echo esc_url(plugin_dir_url (__FILE__ )  . '../img/desktop-logo.png') ?>" alt="icon">
    <h2><?php echo esc_html__('Transactions', 'coinscribble-integration'); ?></h2>
    <form id="coinscribbleUpdateTranactions" data-ajax_action="coinscribble_update_transactions" action="<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>">
        <div class="coinscribble-overlay"></div>
        <div class="coinscribble-loader"></div>
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce(Coinscribble_Integration_Nonce_Actions::COINSCRIBBLE_TRANSACTIONS_UPDATE_NONCE)) ?>">
        <button type="submit" class="button button-primary"><?php echo esc_html__('Refresh transaction list', 'coinscribble-integration') ?></button>
    </form>
    <form method="get">
        <div class="coinscribble-overlay"></div>
        <div class="coinscribble-loader"></div>
        <input type="hidden" name="page" value="coinscribble-overview">
        <div class="tablenav top">

            <div class="tablenav-pages"><span class="displaying-num"><?php echo esc_html($total) . ' '. esc_html__('transactions', 'coinscribble-integration') ?></span>
                <span class="pagination-links">
                <?php if ($page > 1) :?>
                    <a class="last-page button" href="?page=coinscribble-transactions&paged=1"><span class="screen-reader-text"><?php echo esc_html__('First page', 'coinscribble-integration') ?></span><span aria-hidden="true">«</span></a>
                    <a class="prev-page button" href="?page=coinscribble-transactions&paged=<?php echo intval($page - 1) ?>"><span class="screen-reader-text"><?php echo esc_html__('Prev page', 'coinscribble-integration') ?></span><span aria-hidden="true">‹</span></a>
                <?php else: ?>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                <?php endif; ?>
                <span class="paging-input">
                    <label for="current-page-selector" class="screen-reader-text"><?php echo  esc_html__('Current Page', 'coinscribble-integration') ?></label>
                    <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo esc_attr($page) ?>" size="1" aria-describedby="table-paging">
                    <span class="tablenav-paging-text"> <?php echo  esc_html__('of', 'coinscribble-integration') ?> <span class="total-pages"><?php echo esc_html($last_page) ?></span></span>
                </span>
                <?php if ($page < $last_page) :?>
                    <a class="last-page button" href="?page=coinscribble-transactions&paged=<?php echo intval($last_page) ?>"><span class="screen-reader-text"><?php echo esc_html__('Last page', 'coinscribble-integration') ?></span><span aria-hidden="true">»</span></a>
                    <a class="next-page button" href="?page=coinscribble-transactions&paged=<?php echo intval($page + 1) ?>"><span class="screen-reader-text"><?php echo esc_html__('Next page', 'coinscribble-integration') ?></span><span aria-hidden="true">›</span></a>
                <?php else: ?>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                <?php endif; ?>
                </span>
            </div>
            <br class="clear">
        </div>
        <div class="coinscribble_table_wrapper">
            <table class="wp-list-table widefat striped">
                <thead>
                <tr>
                    <th><?php echo esc_html__('Created At', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Payment Method', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Paid', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('To pay', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Receipt Link', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Note', 'coinscribble-integration'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo esc_html($transaction->created_at) ?></td>
                        <td><?php echo esc_html($transaction->payment_method) ?></td>
                        <td><?php echo esc_html($transaction->paid) ?></td>
                        <td><?php echo esc_html($transaction->to_pay) ?></td>
                        <td><a href="<?php echo esc_url($transaction->receipt_link) ?>" target="_blank"><?php echo esc_html($transaction->receipt_link) ?></a></td>
                        <td><?php echo esc_html($transaction->note) ?></td>
                    </tr>
                <?php endforeach; ?>
                <!-- Add more rows as needed -->
                </tbody>
                <tfoot>
                <tr>
                    <th><?php echo esc_html__('Created At', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Payment Method', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Paid', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('To pay', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Receipt Link', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Note', 'coinscribble-integration'); ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="tablenav bottom">
            <div class="tablenav-pages"><span class="displaying-num"><?php echo esc_html($total) . ' '. esc_html__('transactions', 'coinscribble-integration') ?></span>
                <span class="pagination-links">
                <?php if ($page > 1) :?>
                    <a class="last-page button" href="?page=coinscribble-transactions&paged=1"><span class="screen-reader-text"><?php echo esc_html__('First page', 'coinscribble-integration') ?></span><span aria-hidden="true">«</span></a>
                    <a class="prev-page button" href="?page=coinscribble-transactions&paged=<?php echo intval($page - 1) ?>"><span class="screen-reader-text"><?php echo esc_html__('Prev page', 'coinscribble-integration') ?></span><span aria-hidden="true">‹</span></a>
                <?php else: ?>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                <?php endif; ?>
                    <span class="screen-reader-text"><?php echo  esc_html__('Current Page', 'coinscribble-integration') ?></span>
                    <span id="table-paging" class="paging-input">
                        <span class="tablenav-paging-text"><?php echo esc_html($page) ?> <?php echo  esc_html__('of', 'coinscribble-integration') ?>
                            <span class="total-pages"><?php echo esc_html($last_page) ?></span>
                        </span>
                    </span>
                <?php if ($page < $last_page) :?>
                    <a class="last-page button" href="?page=coinscribble-transactions&paged=<?php echo intval($last_page) ?>"><span class="screen-reader-text"><?php echo esc_html__('Last page', 'coinscribble-integration') ?></span><span aria-hidden="true">»</span></a>
                    <a class="next-page button" href="?page=coinscribble-transactions&paged=<?php echo intval($page + 1) ?>"><span class="screen-reader-text"><?php echo esc_html__('Next page', 'coinscribble-integration') ?></span><span aria-hidden="true">›</span></a>
                <?php else: ?>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                <?php endif; ?>
                </span>
            </div>
            <br class="clear">
        </div>
    </form>
</div>
