<?php
/**
 * My Account Designs Template
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$status_labels = [
    'draft' => __('Draft', 'zu-custom-tshirt'),
    'ordered' => __('Ordered', 'zu-custom-tshirt'),
    'approved' => __('Approved', 'zu-custom-tshirt'),
    'rejected' => __('Rejected', 'zu-custom-tshirt'),
];
?>
<div class="zu-ctsd-my-designs">
    <h2><?php esc_html_e('My Custom Designs', 'zu-custom-tshirt'); ?></h2>

    <?php if (!empty($designs)) : ?>
        <div class="zu-ctsd-designs-grid">
            <?php foreach ($designs as $design) : 
                $product = wc_get_product($design->product_id);
                $product_name = $product ? $product->get_name() : __('Unknown Product', 'zu-custom-tshirt');
                $product_url = $product ? $product->get_permalink() : '#';
            ?>
                <div class="zu-ctsd-design-card" data-design-id="<?php echo esc_attr($design->id); ?>">
                    <div class="design-preview">
                        <?php if ($design->preview_image) : ?>
                            <img src="<?php echo esc_url($design->preview_image); ?>" alt="<?php echo esc_attr($design->design_name); ?>">
                        <?php else : ?>
                            <div class="no-preview">
                                <span class="dashicons dashicons-format-image"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="design-info">
                        <h4><?php echo esc_html($design->design_name); ?></h4>
                        <p class="design-product">
                            <?php esc_html_e('Product:', 'zu-custom-tshirt'); ?> 
                            <a href="<?php echo esc_url($product_url); ?>"><?php echo esc_html($product_name); ?></a>
                        </p>
                        <p class="design-side">
                            <?php esc_html_e('Print Side:', 'zu-custom-tshirt'); ?> 
                            <?php echo esc_html(ucfirst($design->print_side)); ?>
                        </p>
                        <p class="design-price">
                            <?php echo wc_price($design->total_price); ?>
                        </p>
                        <span class="design-status design-status--<?php echo esc_attr($design->status); ?>">
                            <?php echo esc_html($status_labels[$design->status] ?? $design->status); ?>
                        </span>
                        <p class="design-date">
                            <?php echo esc_html(human_time_diff(strtotime($design->created_at), current_time('timestamp')) . ' ' . __('ago', 'zu-custom-tshirt')); ?>
                        </p>
                    </div>
                    
                    <div class="design-actions">
                        <?php if ($design->status === 'draft') : ?>
                            <a href="<?php echo esc_url($product_url); ?>" class="button">
                                <?php esc_html_e('Continue Editing', 'zu-custom-tshirt'); ?>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($design->preview_image) : ?>
                            <a href="<?php echo esc_url($design->preview_image); ?>" download class="button">
                                <span class="dashicons dashicons-download"></span>
                                <?php esc_html_e('Download', 'zu-custom-tshirt'); ?>
                            </a>
                        <?php endif; ?>
                        
                        <button type="button" class="button zu-ctsd-reorder-design" data-design-id="<?php echo esc_attr($design->id); ?>">
                            <span class="dashicons dashicons-cart"></span>
                            <?php esc_html_e('Reorder', 'zu-custom-tshirt'); ?>
                        </button>
                        
                        <button type="button" class="button zu-ctsd-delete-design" data-design-id="<?php echo esc_attr($design->id); ?>">
                            <span class="dashicons dashicons-trash"></span>
                            <?php esc_html_e('Delete', 'zu-custom-tshirt'); ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="zu-ctsd-empty-designs">
            <span class="dashicons dashicons-art"></span>
            <h3><?php esc_html_e('No designs yet', 'zu-custom-tshirt'); ?></h3>
            <p><?php esc_html_e('Start customizing T-shirts to see your designs here.', 'zu-custom-tshirt'); ?></p>
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="button button-primary">
                <?php esc_html_e('Browse Products', 'zu-custom-tshirt'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>
