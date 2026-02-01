<?php
/**
 * Order Meta Box Template
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$print_methods = [
    'dtf' => __('DTF (Direct to Film)', 'zu-custom-tshirt'),
    'screen' => __('Screen Printing', 'zu-custom-tshirt'),
    'digital' => __('Digital Printing', 'zu-custom-tshirt'),
    'vinyl' => __('Vinyl Transfer', 'zu-custom-tshirt'),
    'embroidery' => __('Embroidery', 'zu-custom-tshirt'),
];

$urgency_labels = [
    'normal' => __('Normal (5-7 days)', 'zu-custom-tshirt'),
    'express' => __('Express (2-3 days)', 'zu-custom-tshirt'),
    'rush' => __('Rush (24 hours)', 'zu-custom-tshirt'),
];

$status_labels = [
    'pending' => __('Pending', 'zu-custom-tshirt'),
    'approved' => __('Approved', 'zu-custom-tshirt'),
    'rejected' => __('Rejected', 'zu-custom-tshirt'),
    'in_production' => __('In Production', 'zu-custom-tshirt'),
    'completed' => __('Completed', 'zu-custom-tshirt'),
];
?>
<div class="zu-ctsd-order-designs">
    <?php if (!empty($designs)) : ?>
        <?php foreach ($designs as $design) : ?>
            <div class="zu-ctsd-design-card">
                <div class="design-preview">
                    <?php if ($design->preview_image) : ?>
                        <img src="<?php echo esc_url($design->preview_image); ?>" alt="<?php echo esc_attr($design->design_name); ?>">
                    <?php else : ?>
                        <div class="no-preview">
                            <span class="dashicons dashicons-format-image"></span>
                            <p><?php esc_html_e('No preview available', 'zu-custom-tshirt'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="design-details">
                    <h4><?php echo esc_html($design->design_name); ?></h4>
                    
                    <table class="design-info">
                        <tr>
                            <th><?php esc_html_e('Print Side:', 'zu-custom-tshirt'); ?></th>
                            <td><?php echo esc_html(ucfirst($design->print_side)); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Print Method:', 'zu-custom-tshirt'); ?></th>
                            <td><?php echo esc_html($print_methods[$design->print_method] ?? $design->print_method); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Urgency:', 'zu-custom-tshirt'); ?></th>
                            <td><?php echo esc_html($urgency_labels[$design->urgency] ?? $design->urgency); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Design Price:', 'zu-custom-tshirt'); ?></th>
                            <td><?php echo wc_price($design->total_price); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Status:', 'zu-custom-tshirt'); ?></th>
                            <td>
                                <span class="zu-ctsd-status zu-ctsd-status--<?php echo esc_attr($design->production_status); ?>">
                                    <?php echo esc_html($status_labels[$design->production_status] ?? $design->production_status); ?>
                                </span>
                            </td>
                        </tr>
                    </table>

                    <?php if ($design->admin_approved) : ?>
                        <div class="approval-info approved">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <?php esc_html_e('Approved for production', 'zu-custom-tshirt'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($design->approval_notes)) : ?>
                        <div class="approval-notes">
                            <strong><?php esc_html_e('Notes:', 'zu-custom-tshirt'); ?></strong>
                            <p><?php echo esc_html($design->approval_notes); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="design-actions">
                        <?php if ($design->preview_image) : ?>
                            <a href="<?php echo esc_url($design->preview_image); ?>" download class="button">
                                <span class="dashicons dashicons-download"></span>
                                <?php esc_html_e('Download Design', 'zu-custom-tshirt'); ?>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($design->production_status === 'pending') : ?>
                            <button type="button" class="button button-primary zu-ctsd-approve-design" data-design-id="<?php echo esc_attr($design->id); ?>">
                                <span class="dashicons dashicons-yes"></span>
                                <?php esc_html_e('Approve', 'zu-custom-tshirt'); ?>
                            </button>
                            <button type="button" class="button zu-ctsd-reject-design" data-design-id="<?php echo esc_attr($design->id); ?>">
                                <span class="dashicons dashicons-no"></span>
                                <?php esc_html_e('Reject', 'zu-custom-tshirt'); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="zu-ctsd-empty-state">
            <span class="dashicons dashicons-art"></span>
            <p><?php esc_html_e('No custom designs in this order.', 'zu-custom-tshirt'); ?></p>
        </div>
    <?php endif; ?>
</div>
