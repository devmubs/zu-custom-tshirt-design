<?php
/**
 * Orders & Custom Designs Template
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle bulk actions
if (isset($_POST['bulk_action']) && isset($_POST['order_ids'])) {
    check_admin_referer('zu_ctsd_bulk_actions', 'zu_ctsd_bulk_nonce');
    
    $action = sanitize_text_field(wp_unslash($_POST['bulk_action']));
    $order_ids = array_map('intval', wp_unslash($_POST['order_ids']));
    
    global $wpdb;
    $orders_table = ZU_CTSD_Database::get_table('orders');
    
    foreach ($order_ids as $order_id) {
        switch ($action) {
            case 'approve':
                $wpdb->update(
                    $orders_table,
                    ['admin_approved' => 1, 'production_status' => 'approved'],
                    ['id' => $order_id],
                    ['%d', '%s'],
                    ['%d']
                );
                break;
            case 'reject':
                $wpdb->update(
                    $orders_table,
                    ['admin_approved' => 0, 'production_status' => 'rejected'],
                    ['id' => $order_id],
                    ['%d', '%s'],
                    ['%d']
                );
                break;
            case 'in_production':
                $wpdb->update(
                    $orders_table,
                    ['production_status' => 'in_production'],
                    ['id' => $order_id],
                    ['%s'],
                    ['%d']
                );
                break;
            case 'completed':
                $wpdb->update(
                    $orders_table,
                    ['production_status' => 'completed'],
                    ['id' => $order_id],
                    ['%s'],
                    ['%d']
                );
                break;
        }
    }
    
    // Refresh orders
    $designs_table = ZU_CTSD_Database::get_table('designs');
    $orders = $wpdb->get_results(
        "SELECT d.*, o.id as order_entry_id, o.woocommerce_order_id, o.print_method, o.urgency, o.admin_approved, o.production_status 
        FROM {$designs_table} d 
        LEFT JOIN {$orders_table} o ON d.id = o.design_id 
        WHERE d.order_id IS NOT NULL 
        ORDER BY d.created_at DESC"
    );
}

// Status labels and colors
$status_labels = [
    'pending' => __('Pending', 'zu-custom-tshirt'),
    'approved' => __('Approved', 'zu-custom-tshirt'),
    'rejected' => __('Rejected', 'zu-custom-tshirt'),
    'in_production' => __('In Production', 'zu-custom-tshirt'),
    'completed' => __('Completed', 'zu-custom-tshirt'),
];

$print_methods = [
    'dtf' => __('DTF', 'zu-custom-tshirt'),
    'screen' => __('Screen', 'zu-custom-tshirt'),
    'digital' => __('Digital', 'zu-custom-tshirt'),
    'vinyl' => __('Vinyl', 'zu-custom-tshirt'),
    'embroidery' => __('Embroidery', 'zu-custom-tshirt'),
];

$urgency_labels = [
    'normal' => __('Normal', 'zu-custom-tshirt'),
    'express' => __('Express', 'zu-custom-tshirt'),
    'rush' => __('Rush', 'zu-custom-tshirt'),
];
?>
<div class="wrap zu-ctsd-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="zu-ctsd-orders">
        <!-- Filter Tabs -->
        <ul class="subsubsub">
            <li class="all">
                <a href="<?php echo esc_url(admin_url('admin.php?page=zu-tshirt-orders')); ?>" class="current">
                    <?php esc_html_e('All', 'zu-custom-tshirt'); ?> 
                    <span class="count">(<?php echo count($orders); ?>)</span>
                </a>
            </li>
            <?php foreach ($status_labels as $status => $label) : 
                $count = count(array_filter($orders, function($o) use ($status) {
                    return $o->production_status === $status;
                }));
                if ($count > 0) :
            ?>
                <li class="<?php echo esc_attr($status); ?>">
                    | <a href="<?php echo esc_url(admin_url('admin.php?page=zu-tshirt-orders&status=' . $status)); ?>">
                        <?php echo esc_html($label); ?> 
                        <span class="count">(<?php echo $count; ?>)</span>
                    </a>
                </li>
            <?php endif; endforeach; ?>
        </ul>

        <form method="post">
            <?php wp_nonce_field('zu_ctsd_bulk_actions', 'zu_ctsd_bulk_nonce'); ?>

            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">
                        <?php esc_html_e('Select bulk action', 'zu-custom-tshirt'); ?>
                    </label>
                    <select name="bulk_action" id="bulk-action-selector-top">
                        <option value=""><?php esc_html_e('Bulk Actions', 'zu-custom-tshirt'); ?></option>
                        <option value="approve"><?php esc_html_e('Approve', 'zu-custom-tshirt'); ?></option>
                        <option value="reject"><?php esc_html_e('Reject', 'zu-custom-tshirt'); ?></option>
                        <option value="in_production"><?php esc_html_e('Mark In Production', 'zu-custom-tshirt'); ?></option>
                        <option value="completed"><?php esc_html_e('Mark Completed', 'zu-custom-tshirt'); ?></option>
                    </select>
                    <button type="submit" class="button action"><?php esc_html_e('Apply', 'zu-custom-tshirt'); ?></button>
                </div>
            </div>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <input type="checkbox" id="cb-select-all-1">
                        </td>
                        <th><?php esc_html_e('Preview', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Design', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Order', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Customer', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Print Details', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Price', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Status', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Date', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Actions', 'zu-custom-tshirt'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)) : ?>
                        <?php foreach ($orders as $order) : 
                            $wc_order = wc_get_order($order->woocommerce_order_id);
                            $customer_name = $wc_order ? $wc_order->get_formatted_billing_full_name() : '-';
                            $customer_email = $wc_order ? $wc_order->get_billing_email() : '-';
                        ?>
                            <tr>
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="order_ids[]" value="<?php echo esc_attr($order->order_entry_id); ?>">
                                </th>
                                <td>
                                    <?php if ($order->preview_image) : ?>
                                        <a href="<?php echo esc_url($order->preview_image); ?>" target="_blank">
                                            <img src="<?php echo esc_url($order->preview_image); ?>" alt="" width="60" height="60" style="object-fit: cover; border-radius: 4px;">
                                        </a>
                                    <?php else : ?>
                                        <span class="dashicons dashicons-format-image" style="font-size: 40px; color: #ccc;"></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo esc_html($order->design_name); ?></strong>
                                    <br>
                                    <small><?php esc_html_e('Side:', 'zu-custom-tshirt'); ?> <?php echo esc_html(ucfirst($order->print_side)); ?></small>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('post.php?post=' . $order->woocommerce_order_id . '&action=edit')); ?>" target="_blank">
                                        #<?php echo esc_html($order->woocommerce_order_id); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo esc_html($customer_name); ?>
                                    <br>
                                    <small><?php echo esc_html($customer_email); ?></small>
                                </td>
                                <td>
                                    <span class="zu-ctsd-badge zu-ctsd-badge--<?php echo esc_attr($order->print_method); ?>">
                                        <?php echo esc_html($print_methods[$order->print_method] ?? $order->print_method); ?>
                                    </span>
                                    <br>
                                    <small><?php echo esc_html($urgency_labels[$order->urgency] ?? $order->urgency); ?></small>
                                </td>
                                <td><?php echo wc_price($order->total_price); ?></td>
                                <td>
                                    <span class="zu-ctsd-status zu-ctsd-status--<?php echo esc_attr($order->production_status ?? 'pending'); ?>">
                                        <?php echo esc_html($status_labels[$order->production_status ?? 'pending']); ?>
                                    </span>
                                    <?php if ($order->admin_approved) : ?>
                                        <br><small class="zu-ctsd-approved"><?php esc_html_e('Approved', 'zu-custom-tshirt'); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo esc_html(human_time_diff(strtotime($order->created_at), current_time('timestamp')) . ' ' . __('ago', 'zu-custom-tshirt')); ?>
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <a href="<?php echo esc_url(admin_url('post.php?post=' . $order->woocommerce_order_id . '&action=edit')); ?>" class="button button-small">
                                            <?php esc_html_e('View Order', 'zu-custom-tshirt'); ?>
                                        </a>
                                        <?php if ($order->preview_image) : ?>
                                            <a href="<?php echo esc_url($order->preview_image); ?>" download class="button button-small">
                                                <?php esc_html_e('Download', 'zu-custom-tshirt'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="10" class="zu-ctsd-empty-cell">
                                <div class="zu-ctsd-empty-state">
                                    <span class="dashicons dashicons-cart"></span>
                                    <p><?php esc_html_e('No custom design orders found.', 'zu-custom-tshirt'); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
</div>
