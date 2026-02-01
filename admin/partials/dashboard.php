<?php
/**
 * Admin Dashboard Template
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap zu-ctsd-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="zu-ctsd-dashboard">
        <!-- Stats Cards -->
        <div class="zu-ctsd-stats-grid">
            <div class="zu-ctsd-stat-card">
                <div class="stat-icon">
                    <span class="dashicons dashicons-art"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($stats['total_designs']); ?></h3>
                    <p><?php esc_html_e('Total Custom Designs', 'zu-custom-tshirt'); ?></p>
                </div>
            </div>

            <div class="zu-ctsd-stat-card">
                <div class="stat-icon orders">
                    <span class="dashicons dashicons-cart"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($stats['total_orders']); ?></h3>
                    <p><?php esc_html_e('Orders with Custom T-Shirts', 'zu-custom-tshirt'); ?></p>
                </div>
            </div>

            <div class="zu-ctsd-stat-card">
                <div class="stat-icon revenue">
                    <span class="dashicons dashicons-money-alt"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo wc_price($stats['revenue']); ?></h3>
                    <p><?php esc_html_e('Revenue from Custom Products', 'zu-custom-tshirt'); ?></p>
                </div>
            </div>

            <div class="zu-ctsd-stat-card">
                <div class="stat-icon pending">
                    <span class="dashicons dashicons-clock"></span>
                </div>
                <div class="stat-content">
                    <h3><?php 
                        $pending = array_filter($stats['recent_orders'], function($o) {
                            return isset($o->production_status) && $o->production_status === 'pending';
                        });
                        echo count($pending);
                    ?></h3>
                    <p><?php esc_html_e('Pending Approval', 'zu-custom-tshirt'); ?></p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="zu-ctsd-quick-actions">
            <h2><?php esc_html_e('Quick Actions', 'zu-custom-tshirt'); ?></h2>
            <div class="action-buttons">
                <a href="<?php echo esc_url(admin_url('admin.php?page=zu-tshirt-templates&action=add')); ?>" class="button button-primary">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php esc_html_e('Add Template', 'zu-custom-tshirt'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=zu-tshirt-pricing')); ?>" class="button">
                    <span class="dashicons dashicons-tag"></span>
                    <?php esc_html_e('Update Pricing', 'zu-custom-tshirt'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=zu-tshirt-orders')); ?>" class="button">
                    <span class="dashicons dashicons-list-view"></span>
                    <?php esc_html_e('View Orders', 'zu-custom-tshirt'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=zu-tshirt-settings')); ?>" class="button">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <?php esc_html_e('Settings', 'zu-custom-tshirt'); ?>
                </a>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="zu-ctsd-recent-orders">
            <h2><?php esc_html_e('Recent Customized Orders', 'zu-custom-tshirt'); ?></h2>
            
            <?php if (!empty($stats['recent_orders'])) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Design', 'zu-custom-tshirt'); ?></th>
                            <th><?php esc_html_e('Order ID', 'zu-custom-tshirt'); ?></th>
                            <th><?php esc_html_e('Customer', 'zu-custom-tshirt'); ?></th>
                            <th><?php esc_html_e('Total', 'zu-custom-tshirt'); ?></th>
                            <th><?php esc_html_e('Status', 'zu-custom-tshirt'); ?></th>
                            <th><?php esc_html_e('Date', 'zu-custom-tshirt'); ?></th>
                            <th><?php esc_html_e('Actions', 'zu-custom-tshirt'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['recent_orders'] as $order) : 
                            $wc_order = wc_get_order($order->woocommerce_order_id);
                            $customer_name = $wc_order ? $wc_order->get_formatted_billing_full_name() : '-';
                        ?>
                            <tr>
                                <td>
                                    <?php if ($order->preview_image) : ?>
                                        <img src="<?php echo esc_url($order->preview_image); ?>" alt="" width="50" height="50" style="object-fit: cover;">
                                    <?php else : ?>
                                        <span class="dashicons dashicons-format-image"></span>
                                    <?php endif; ?>
                                    <?php echo esc_html($order->design_name); ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('post.php?post=' . $order->woocommerce_order_id . '&action=edit')); ?>">
                                        #<?php echo esc_html($order->woocommerce_order_id); ?>
                                    </a>
                                </td>
                                <td><?php echo esc_html($customer_name); ?></td>
                                <td><?php echo wc_price($order->total_price); ?></td>
                                <td>
                                    <span class="zu-ctsd-status zu-ctsd-status--<?php echo esc_attr($order->production_status ?? 'pending'); ?>">
                                        <?php echo esc_html(ucfirst($order->production_status ?? 'pending')); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html(human_time_diff(strtotime($order->created_at), current_time('timestamp')) . ' ' . __('ago', 'zu-custom-tshirt')); ?></td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('post.php?post=' . $order->woocommerce_order_id . '&action=edit')); ?>" class="button button-small">
                                        <?php esc_html_e('View Order', 'zu-custom-tshirt'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="zu-ctsd-empty-state">
                    <span class="dashicons dashicons-cart"></span>
                    <p><?php esc_html_e('No customized orders yet.', 'zu-custom-tshirt'); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- System Status -->
        <div class="zu-ctsd-system-status">
            <h2><?php esc_html_e('System Status', 'zu-custom-tshirt'); ?></h2>
            <table class="widefat">
                <tbody>
                    <tr>
                        <td><?php esc_html_e('WooCommerce', 'zu-custom-tshirt'); ?></td>
                        <td>
                            <?php if (class_exists('WooCommerce')) : ?>
                                <span class="zu-ctsd-status zu-ctsd-status--active"><?php esc_html_e('Active', 'zu-custom-tshirt'); ?></span>
                                <small>(v<?php echo esc_html(WC()->version); ?>)</small>
                            <?php else : ?>
                                <span class="zu-ctsd-status zu-ctsd-status--inactive"><?php esc_html_e('Not Installed', 'zu-custom-tshirt'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Plugin Version', 'zu-custom-tshirt'); ?></td>
                        <td><?php echo esc_html(ZU_CTSD_VERSION); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('PHP Version', 'zu-custom-tshirt'); ?></td>
                        <td>
                            <?php if (version_compare(PHP_VERSION, '8.3.0', '>=')) : ?>
                                <span class="zu-ctsd-status zu-ctsd-status--active"><?php echo esc_html(PHP_VERSION); ?></span>
                            <?php else : ?>
                                <span class="zu-ctsd-status zu-ctsd-status--warning"><?php echo esc_html(PHP_VERSION); ?> <?php esc_html_e('(Recommended: 8.3+)', 'zu-custom-tshirt'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Upload Directory', 'zu-custom-tshirt'); ?></td>
                        <td>
                            <?php 
                            $upload_dir = wp_upload_dir();
                            $designs_dir = $upload_dir['basedir'] . '/zu-tshirt-designs';
                            if (is_writable($designs_dir)) : ?>
                                <span class="zu-ctsd-status zu-ctsd-status--active"><?php esc_html_e('Writable', 'zu-custom-tshirt'); ?></span>
                            <?php else : ?>
                                <span class="zu-ctsd-status zu-ctsd-status--error"><?php esc_html_e('Not Writable', 'zu-custom-tshirt'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
