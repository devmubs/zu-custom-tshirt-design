<?php
/**
 * Pricing Rules Template
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Group pricing rules by type
$grouped_rules = [];
foreach ($pricing_rules as $rule) {
    $grouped_rules[$rule->rule_type][] = $rule;
}

$rule_type_labels = [
    'image_count' => __('Number of Images', 'zu-custom-tshirt'),
    'print_size' => __('Print Size', 'zu-custom-tshirt'),
    'print_method' => __('Print Method', 'zu-custom-tshirt'),
    'material' => __('Material', 'zu-custom-tshirt'),
    'urgency' => __('Urgency', 'zu-custom-tshirt'),
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zu_ctsd_pricing_nonce'])) {
    if (wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['zu_ctsd_pricing_nonce'])), 'save_zu_ctsd_pricing')) {
        foreach ($_POST['pricing'] as $rule_id => $data) {
            ZU_CTSD_Database::update_pricing_rule(
                intval($rule_id),
                [
                    'base_price' => floatval(wp_unslash($data['base_price'] ?? 0)),
                    'extra_cost' => floatval(wp_unslash($data['extra_cost'] ?? 0)),
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                ]
            );
        }
        
        // Refresh data
        $pricing_rules = ZU_CTSD_Database::get_pricing_rules();
        $grouped_rules = [];
        foreach ($pricing_rules as $rule) {
            $grouped_rules[$rule->rule_type][] = $rule;
        }
        
        $success_message = __('Pricing rules updated successfully!', 'zu-custom-tshirt');
    }
}
?>
<div class="wrap zu-ctsd-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if (isset($success_message)) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html($success_message); ?></p>
        </div>
    <?php endif; ?>

    <div class="zu-ctsd-pricing-rules">
        <p class="description">
            <?php esc_html_e('Configure pricing rules for custom T-shirt designs. These rules will be applied on top of the base product price.', 'zu-custom-tshirt'); ?>
        </p>

        <form method="post">
            <?php wp_nonce_field('save_zu_ctsd_pricing', 'zu_ctsd_pricing_nonce'); ?>

            <?php foreach ($grouped_rules as $rule_type => $rules) : ?>
                <div class="postbox zu-ctsd-pricing-section">
                    <h2>
                        <?php echo esc_html($rule_type_labels[$rule_type] ?? ucfirst(str_replace('_', ' ', $rule_type))); ?>
                    </h2>
                    <div class="inside">
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Option', 'zu-custom-tshirt'); ?></th>
                                    <th><?php esc_html_e('Base Price', 'zu-custom-tshirt'); ?></th>
                                    <th><?php esc_html_e('Extra Cost', 'zu-custom-tshirt'); ?></th>
                                    <th><?php esc_html_e('Active', 'zu-custom-tshirt'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rules as $rule) : ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo esc_html($rule->rule_value); ?></strong>
                                            <code><?php echo esc_html($rule->rule_key); ?></code>
                                        </td>
                                        <td>
                                            <div class="zu-ctsd-price-input">
                                                <span class="currency"><?php echo esc_html(get_woocommerce_currency_symbol()); ?></span>
                                                <input type="number" 
                                                       name="pricing[<?php echo esc_attr($rule->id); ?>][base_price]" 
                                                       value="<?php echo esc_attr(number_format($rule->base_price, 2)); ?>" 
                                                       step="0.01" min="0" class="small-text">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="zu-ctsd-price-input">
                                                <span class="currency"><?php echo esc_html(get_woocommerce_currency_symbol()); ?></span>
                                                <input type="number" 
                                                       name="pricing[<?php echo esc_attr($rule->id); ?>][extra_cost]" 
                                                       value="<?php echo esc_attr(number_format($rule->extra_cost, 2)); ?>" 
                                                       step="0.01" min="0" class="small-text">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="checkbox" 
                                                   name="pricing[<?php echo esc_attr($rule->id); ?>][is_active]" 
                                                   <?php checked($rule->is_active, 1); ?>>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>

            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Save Pricing Rules', 'zu-custom-tshirt'); ?>
                </button>
            </p>
        </form>

        <!-- Pricing Formula Info -->
        <div class="postbox">
            <h2><?php esc_html_e('Pricing Formula', 'zu-custom-tshirt'); ?></h2>
            <div class="inside">
                <p><?php esc_html_e('The final price is calculated as:', 'zu-custom-tshirt'); ?></p>
                <code class="zu-ctsd-formula">
                    <?php esc_html_e('Final Price = Product Base Price + Image Count Cost + Print Size Cost + Print Method Cost + Material Cost + Urgency Cost', 'zu-custom-tshirt'); ?>
                </code>
                
                <h4><?php esc_html_e('Example Calculation:', 'zu-custom-tshirt'); ?></h4>
                <ul class="zu-ctsd-example-calculation">
                    <li><?php esc_html_e('T-Shirt Base Price: $20.00', 'zu-custom-tshirt'); ?></li>
                    <li><?php esc_html_e('2 Images: +$8.00', 'zu-custom-tshirt'); ?></li>
                    <li><?php esc_html_e('Large Print: +$10.00', 'zu-custom-tshirt'); ?></li>
                    <li><?php esc_html_e('Embroidery: +$15.00', 'zu-custom-tshirt'); ?></li>
                    <li><?php esc_html_e('Express Order: +$12.00', 'zu-custom-tshirt'); ?></li>
                    <li><strong><?php esc_html_e('Total: $65.00', 'zu-custom-tshirt'); ?></strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>
