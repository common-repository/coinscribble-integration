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

$repository = Coinscribble_Integration_Posts_Repository::get_instance();
$service = Coinscribble_Integration_Post_Service::get_instance();
$content_types = Coinscribble_Integration_Categories_Configs::get_content_types();

$page = intval($_GET['paged'] ?? 1);
$limit = 10;

$posts = $repository->get_posts_for_coinscribble_page($page, $limit );
$total = $repository->get_total();
$last_page = intval(ceil($total / $limit));

?>

<div class="wrap">

    <img class="coiscrb_icon" src="<?php echo esc_url(plugin_dir_url (__FILE__ )  . '../img/desktop-logo.png') ?>" alt="icon">
    <h2><?php echo esc_html__('Posts', 'coinscribble-integration'); ?></h2>

    <form method="get">
        <div class="coinscribble-overlay"></div>
        <div class="coinscribble-loader"></div>
        <input type="hidden" name="page" value="coinscribble-overview">
        <div class="tablenav top">

            <div class="tablenav-pages"><span class="displaying-num"><?php echo esc_html($total) . ' '. esc_html__('posts', 'coinscribble-integration') ?></span>
                <span class="pagination-links">
                <?php
                if ($page > 1) :?>
                    <a class="last-page button" href="?page=coinscribble-posts&paged=1"><span class="screen-reader-text"><?php echo esc_html__('First page', 'coinscribble-integration')?></span><span aria-hidden="true">«</span></a>
                    <a class="prev-page button" href="?page=coinscribble-posts&paged=<?php echo intval($page - 1) ?>"><span class="screen-reader-text"><?php echo esc_html__('Prev page', 'coinscribble-integration')?></span><span aria-hidden="true">‹</span></a>
                <?php else:?>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                <?php endif;?>
                <span class="paging-input">
                    <label for="current-page-selector" class="screen-reader-text"><?php echo  esc_html__('Current Page', 'coinscribble-integration') ?></label>
                    <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo esc_html($page) ?>" size="1" aria-describedby="table-paging">
                    <span class="tablenav-paging-text"> <?php echo  esc_html__('of', 'coinscribble-integration') ?> <span class="total-pages"><?php echo esc_html($last_page) ?></span></span>
                </span>
                <?php if ($page < $last_page) :?>
                    <a class="last-page button" href="?page=coinscribble-posts&paged=<?php echo intval($last_page) ?>"><span class="screen-reader-text"><?php echo esc_html__('Last page', 'coinscribble-integration') ?></span><span aria-hidden="true">»</span></a>
                    <a class="next-page button" href="?page=coinscribble-posts&paged=<?php echo intval($page + 1) ?>"><span class="screen-reader-text"><?php echo esc_html__('Next page', 'coinscribble-integration') ?></span><span aria-hidden="true">›</span></a>
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
                    <th><?php echo esc_html__('Title', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Link', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Category', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Publish date', 'coinscribble-integration'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo esc_html($post->post_title) ?></td>
                        <td><a href="<?php echo esc_html($post->guid) ?>" target="_blank"><?php echo esc_html($post->guid) ?></a></td>
                        <td><?php echo esc_html($content_types[$service->getCoinscribbleCategory($post->id)] ?? '') ?></td>
                        <td><?php echo esc_html($post->post_date)?></td>
                    </tr>
                <?php endforeach; ?>
                <!-- Add more rows as needed -->
                </tbody>
                <tfoot>
                <tr>
                    <th><?php echo esc_html__('Title', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Link', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Category', 'coinscribble-integration'); ?></th>
                    <th><?php echo esc_html__('Publish date', 'coinscribble-integration'); ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="tablenav bottom">
            <div class="tablenav-pages"><span class="displaying-num"><?php echo esc_html($total) . ' '. esc_html__('posts', 'coinscribble-integration') ?></span>
                <span class="pagination-links">
                    <?php
                    if ($page > 1) :?>
                        <a class="last-page button" href="?page=coinscribble-posts&paged=1"><span class="screen-reader-text"><?php echo esc_html__('First page', 'coinscribble-integration')?></span><span aria-hidden="true">«</span></a>
                        <a class="prev-page button" href="?page=coinscribble-posts&paged=<?php echo intval($page - 1) ?>"><span class="screen-reader-text"><?php echo esc_html__('Prev page', 'coinscribble-integration')?></span><span aria-hidden="true">‹</span></a>
                    <?php else:?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                    <?php endif;?>
                    <span class="screen-reader-text"><?php echo  esc_html__('Current Page', 'coinscribble-integration') ?></span>
                    <span id="table-paging" class="paging-input">
                        <span class="tablenav-paging-text"><?php echo esc_html($page) ?> <?php echo  esc_html__('of', 'coinscribble-integration') ?>
                            <span class="total-pages"><?php echo esc_html($last_page) ?></span>
                        </span>
                    </span>
                <?php if ($page < $last_page) :?>
                    <a class="last-page button" href="?page=coinscribble-posts&paged=<?php echo intval($last_page) ?>"><span class="screen-reader-text"><?php echo esc_html__('Last page', 'coinscribble-integration') ?></span><span aria-hidden="true">»</span></a>
                    <a class="next-page button" href="?page=coinscribble-posts&paged=<?php echo intval($page + 1) ?>"><span class="screen-reader-text"><?php echo esc_html__('Next page', 'coinscribble-integration') ?></span><span aria-hidden="true">›</span></a>
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
