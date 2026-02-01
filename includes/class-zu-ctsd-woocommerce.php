<?php
/**
 * WooCommerce Integration Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_WooCommerce
 * Handles WooCommerce integration and dependency checking
 */
class ZU_CTSD_WooCommerce {

    /**
     * Check if WooCommerce is active
     */
    public static function is_woocommerce_active(): bool {
        return class_exists('WooCommerce');
    }

    /**
     * Check WooCommerce on admin init
     */
    public function check_woocommerce_active(): void {
        if (!self::is_woocommerce_active()) {
            add_action('admin_notices', [$this, 'woocommerce_missing_notice']);
            
            // Deactivate plugin functionality
            add_filter('zu_ctsd_enabled', '__return_false');
        }
    }

    /**
     * Display WooCommerce missing notice
     */
    public function woocommerce_missing_notice(): void {
        if (!self::is_woocommerce_active()) {
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php esc_html_e('ZU Custom T-Shirt Design', 'zu-custom-tshirt'); ?></strong> 
                    <?php esc_html_e('requires WooCommerce to be installed and activated.', 'zu-custom-tshirt'); ?>
                </p>
                <p>
                    <?php 
                    if (current_user_can('install_plugins')) {
                        printf(
                            /* translators: %s: WooCommerce plugin link */
                            esc_html__('Please %s to use this plugin.', 'zu-custom-tshirt'),
                            '<a href="' . esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')) . '">' . esc_html__('install and activate WooCommerce', 'zu-custom-tshirt') . '</a>'
                        );
                    }
                    ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Get WooCommerce products that support customization
     */
    public static function get_customizable_products(array $args = []): array {
        $defaults = [
            'status' => 'publish',
            'limit' => -1,
            'return' => 'objects',
        ];
        $args = wp_parse_args($args, $defaults);

        // Add meta query for customizable products
        $args['meta_query'] = [
            [
                'key' => '_zu_ctsd_customizable',
                'value' => 'yes',
                'compare' => '=',
            ],
        ];

        return wc_get_products($args);
    }

    /**
     * Check if a product is customizable
     */
    public static function is_product_customizable(int $product_id): bool {
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return false;
        }

        return $product->get_meta('_zu_ctsd_customizable') === 'yes';
    }

    /**
     * Set product as customizable
     */
    public static function set_product_customizable(int $product_id, bool $customizable = true): void {
        $product = wc_get_product($product_id);
        
        if ($product) {
            $product->update_meta_data('_zu_ctsd_customizable', $customizable ? 'yes' : 'no');
            $product->save();
        }
    }

    /**
     * Get product template
     */
    public static function get_product_template(int $product_id): ?object {
        global $wpdb;
        $table = ZU_CTSD_Database::get_table('template_products');
        $templates_table = ZU_CTSD_Database::get_table('templates');

        $template_id = $wpdb->get_var($wpdb->prepare(
            "SELECT template_id FROM {$table} WHERE product_id = %d",
            $product_id
        ));

        if ($template_id) {
            return ZU_CTSD_Database::get_template(intval($template_id));
        }

        return null;
    }

    /**
     * Set product template
     */
    public static function set_product_template(int $product_id, int $template_id): void {
        global $wpdb;
        $table = ZU_CTSD_Database::get_table('template_products');

        // Check if relation exists
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE product_id = %d",
            $product_id
        ));

        if ($exists) {
            $wpdb->update(
                $table,
                ['template_id' => $template_id],
                ['product_id' => $product_id],
                ['%d'],
                ['%d']
            );
        } else {
            $wpdb->insert(
                $table,
                [
                    'template_id' => $template_id,
                    'product_id' => $product_id,
                ],
                ['%d', '%d']
            );
        }
    }

    /**
     * Add custom product tab on product page
     */
    public static function add_customizer_product_tab(array $tabs): array {
        global $product;

        if ($product && self::is_product_customizable($product->get_id())) {
            $tabs['customize'] = [
                'title' => __('Customize', 'zu-custom-tshirt'),
                'priority' => 15,
                'callback' => [__CLASS__, 'customizer_tab_content'],
            ];
        }

        return $tabs;
    }

    /**
     * Customizer tab content
     */
    public static function customizer_tab_content(): void {
        global $product;
        
        if (!$product) {
            return;
        }

        echo '<div class="zu-ctsd-product-tab-content">';
        echo '<p>' . esc_html__('Click the "Customize This T-Shirt" button to start designing your custom T-shirt.', 'zu-custom-tshirt') . '</p>';
        echo '</div>';
    }

    /**
     * Add customizer data to cart item
     */
    public static function add_cart_item_data(array $cart_item_data, int $product_id, int $variation_id): array {
        if (isset($_POST['zu_ctsd_design_id'])) {
            $design_id = intval(wp_unslash($_POST['zu_ctsd_design_id']));
            
            // Verify design exists and belongs to user
            $design = ZU_CTSD_Database::get_design($design_id);
            
            if ($design && ($design->user_id == get_current_user_id() || $design->user_id == 0)) {
                $cart_item_data['zu_ctsd_design_id'] = $design_id;
                $cart_item_data['zu_ctsd_design_data'] = $design->design_data;
                $cart_item_data['zu_ctsd_preview_image'] = $design->preview_image;
                $cart_item_data['zu_ctsd_print_side'] = $design->print_side;
            }
        }

        return $cart_item_data;
    }

    /**
     * Display customizer data in cart
     */
    public static function display_cart_item_data(array $item_data, array $cart_item): array {
        if (isset($cart_item['zu_ctsd_design_id'])) {
            $item_data[] = [
                'key' => __('Custom Design', 'zu-custom-tshirt'),
                'value' => '#' . $cart_item['zu_ctsd_design_id'],
            ];

            if (isset($cart_item['zu_ctsd_print_side'])) {
                $item_data[] = [
                    'key' => __('Print Side', 'zu-custom-tshirt'),
                    'value' => ucfirst($cart_item['zu_ctsd_print_side']),
                ];
            }
        }

        return $item_data;
    }

    /**
     * Add order item meta
     */
    public static function add_order_item_meta($item, $cart_item_key, $values, $order): void {
        if (isset($values['zu_ctsd_design_id'])) {
            $item->add_meta_data('_zu_ctsd_design_id', $values['zu_ctsd_design_id'], true);
            $item->add_meta_data('_zu_ctsd_design_data', $values['zu_ctsd_design_data'], true);
            $item->add_meta_data('_zu_ctsd_preview_image', $values['zu_ctsd_preview_image'], true);
            $item->add_meta_data('_zu_ctsd_print_side', $values['zu_ctsd_print_side'], true);

            // Update design with order ID
            ZU_CTSD_Database::update_design(
                intval($values['zu_ctsd_design_id']),
                ['order_id' => $order->get_id(), 'status' => 'ordered']
            );

            // Create order entry
            global $wpdb;
            $orders_table = ZU_CTSD_Database::get_table('orders');
            
            $wpdb->insert(
                $orders_table,
                [
                    'design_id' => intval($values['zu_ctsd_design_id']),
                    'woocommerce_order_id' => $order->get_id(),
                    'woocommerce_order_item_id' => $item->get_id(),
                    'print_method' => sanitize_text_field($values['zu_ctsd_print_method'] ?? 'dtf'),
                    'urgency' => sanitize_text_field($values['zu_ctsd_urgency'] ?? 'normal'),
                ],
                ['%d', '%d', '%d', '%s', '%s']
            );
        }
    }

    /**
     * Calculate custom design price
     */
    public static function calculate_custom_price(float $price, array $design_data): float {
        $pricing_engine = new ZU_CTSD_Pricing();
        return $pricing_engine->calculate_price($price, $design_data);
    }

    /**
     * Add product customizer meta box
     */
    public static function add_product_meta_box(): void {
        add_meta_box(
            'zu_ctsd_product_customizer',
            __('Custom T-Shirt Design', 'zu-custom-tshirt'),
            [__CLASS__, 'render_product_meta_box'],
            'product',
            'side',
            'default'
        );
    }

    /**
     * Render product meta box
     */
    public static function render_product_meta_box($post): void {
        $product = wc_get_product($post->ID);
        $is_customizable = $product->get_meta('_zu_ctsd_customizable') === 'yes';
        $template_id = $product->get_meta('_zu_ctsd_template_id');
        
        $templates = ZU_CTSD_Database::get_templates();
        
        wp_nonce_field('zu_ctsd_product_meta', 'zu_ctsd_product_nonce');
        ?>
        <p>
            <label>
                <input type="checkbox" name="_zu_ctsd_customizable" value="yes" <?php checked($is_customizable); ?>>
                <?php esc_html_e('Enable customization for this product', 'zu-custom-tshirt'); ?>
            </label>
        </p>
        
        <p>
            <label for="_zu_ctsd_template_id"><?php esc_html_e('Design Template:', 'zu-custom-tshirt'); ?></label>
            <select name="_zu_ctsd_template_id" id="_zu_ctsd_template_id" style="width: 100%;">
                <option value=""><?php esc_html_e('-- Select Template --', 'zu-custom-tshirt'); ?></option>
                <?php foreach ($templates as $template) : ?>
                    <option value="<?php echo esc_attr($template->id); ?>" <?php selected($template_id, $template->id); ?>>
                        <?php echo esc_html($template->template_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        
        <p class="description">
            <?php esc_html_e('Select a template to define the printable areas and design options for this product.', 'zu-custom-tshirt'); ?>
        </p>
        <?php
    }

    /**
     * Save product meta box
     */
    public static function save_product_meta_box(int $post_id): void {
        if (!isset($_POST['zu_ctsd_product_nonce'])) {
            return;
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['zu_ctsd_product_nonce'])), 'zu_ctsd_product_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_product', $post_id)) {
            return;
        }

        $product = wc_get_product($post_id);
        
        if ($product) {
            $is_customizable = isset($_POST['_zu_ctsd_customizable']) && $_POST['_zu_ctsd_customizable'] === 'yes';
            $product->update_meta_data('_zu_ctsd_customizable', $is_customizable ? 'yes' : 'no');
            
            if (isset($_POST['_zu_ctsd_template_id'])) {
                $template_id = intval(wp_unslash($_POST['_zu_ctsd_template_id']));
                $product->update_meta_data('_zu_ctsd_template_id', $template_id);
                
                // Also update the template_products table
                if ($template_id > 0) {
                    self::set_product_template($post_id, $template_id);
                }
            }
            
            $product->save();
        }
    }
}

// Add product meta box hooks
add_action('add_meta_boxes', ['ZU_CTSD_WooCommerce', 'add_product_meta_box']);
add_action('save_post_product', ['ZU_CTSD_WooCommerce', 'save_product_meta_box']);
