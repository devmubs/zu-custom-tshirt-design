<?php
/**
 * Public Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_Public
 * Handles public-facing functionality
 */
class ZU_CTSD_Public {

    /**
     * Plugin name
     */
    private string $plugin_name = 'zu-custom-tshirt';

    /**
     * Plugin version
     */
    private string $version = ZU_CTSD_VERSION;

    /**
     * Constructor
     */
    public function __construct() {
        // Initialize
    }

    /**
     * Enqueue public styles
     */
    public function enqueue_styles(): void {
        // Only load on product pages or customizer page
        if (!is_product() && !is_account_page()) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name . '-public',
            ZU_CTSD_PLUGIN_URL . 'assets/css/public.css',
            [],
            $this->version
        );

        // Load customizer styles only if needed
        if (is_product() && $this->should_load_customizer()) {
            wp_enqueue_style(
                $this->plugin_name . '-customizer',
                ZU_CTSD_PLUGIN_URL . 'assets/css/customizer.css',
                [],
                $this->version
            );
        }
    }

    /**
     * Enqueue public scripts
     */
    public function enqueue_scripts(): void {
        // Only load on product pages or customizer page
        if (!is_product() && !is_account_page()) {
            return;
        }

        // Fabric.js for canvas manipulation
        wp_enqueue_script(
            'fabric-js',
            'https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js',
            [],
            '5.3.1',
            true
        );

        // Public scripts
        wp_enqueue_script(
            $this->plugin_name . '-public',
            ZU_CTSD_PLUGIN_URL . 'assets/js/public.js',
            ['jquery'],
            $this->version,
            true
        );

        // Load customizer scripts only if needed
        if (is_product() && $this->should_load_customizer()) {
            wp_enqueue_script(
                $this->plugin_name . '-customizer',
                ZU_CTSD_PLUGIN_URL . 'assets/js/customizer.js',
                ['jquery', 'fabric-js'],
                $this->version,
                true
            );

            // Localize script with data
            wp_localize_script($this->plugin_name . '-customizer', 'zuCtsdData', $this->get_customizer_data());
        }

        // My Account scripts
        if (is_account_page()) {
            wp_enqueue_script(
                $this->plugin_name . '-my-account',
                ZU_CTSD_PLUGIN_URL . 'assets/js/my-account.js',
                ['jquery'],
                $this->version,
                true
            );

            wp_localize_script($this->plugin_name . '-my-account', 'zuCtsdMyAccount', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'restUrl' => rest_url('zu-ctsd/v1/'),
                'nonce' => wp_create_nonce('zu_ctsd_nonce'),
                'strings' => [
                    'confirmDelete' => __('Are you sure you want to delete this design?', 'zu-custom-tshirt'),
                    'confirmReorder' => __('Add this design to cart?', 'zu-custom-tshirt'),
                ],
            ]);
        }
    }

    /**
     * Check if customizer should be loaded
     */
    private function should_load_customizer(): bool {
        global $product;
        
        if (!$product) {
            return false;
        }

        return ZU_CTSD_WooCommerce::is_product_customizable($product->get_id());
    }

    /**
     * Get customizer data for localization
     */
    private function get_customizer_data(): array {
        global $product;
        
        if (!$product) {
            return [];
        }

        $product_id = $product->get_id();
        $template = ZU_CTSD_WooCommerce::get_product_template($product_id);
        
        return [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('zu-ctsd/v1/'),
            'nonce' => wp_create_nonce('zu_ctsd_nonce'),
            'productId' => $product_id,
            'productPrice' => $product->get_price(),
            'productName' => $product->get_name(),
            'template' => $template ? [
                'id' => $template->id,
                'name' => $template->template_name,
                'frontImage' => $template->front_image,
                'backImage' => $template->back_image,
                'leftSleeveImage' => $template->left_sleeve_image,
                'rightSleeveImage' => $template->right_sleeve_image,
                'printableAreaFront' => json_decode($template->printable_area_front, true),
                'printableAreaBack' => json_decode($template->printable_area_back, true),
                'printableAreaLeftSleeve' => json_decode($template->printable_area_left_sleeve, true),
                'printableAreaRightSleeve' => json_decode($template->printable_area_right_sleeve, true),
                'maxImages' => intval($template->max_images),
                'allowText' => boolval($template->allow_text),
                'allowImages' => boolval($template->allow_images),
            ] : null,
            'settings' => [
                'canvasWidth' => intval(get_option('zu_ctsd_canvas_width', 500)),
                'canvasHeight' => intval(get_option('zu_ctsd_canvas_height', 600)),
                'maxFileSize' => intval(get_option('zu_ctsd_max_file_size', 5)),
                'allowedFileTypes' => get_option('zu_ctsd_allowed_file_types', 'jpg,jpeg,png,gif,svg'),
                'maxImages' => intval(get_option('zu_ctsd_max_images', 5)),
                'allowText' => get_option('zu_ctsd_allow_text', 'yes') === 'yes',
                'allowImages' => get_option('zu_ctsd_allow_images', 'yes') === 'yes',
                'fontFamily' => explode(',', get_option('zu_ctsd_font_family', 'Arial,Helvetica,Times New Roman,Verdana,Georgia')),
                'defaultPrintMethod' => get_option('zu_ctsd_default_print_method', 'dtf'),
                'enableLivePrice' => get_option('zu_ctsd_enable_live_price', 'yes') === 'yes',
                'enableShare' => get_option('zu_ctsd_enable_share', 'yes') === 'yes',
                'enableExport' => get_option('zu_ctsd_enable_export', 'yes') === 'yes',
            ],
            'strings' => [
                'addText' => __('Add Text', 'zu-custom-tshirt'),
                'uploadImage' => __('Upload Image', 'zu-custom-tshirt'),
                'delete' => __('Delete', 'zu-custom-tshirt'),
                'bringToFront' => __('Bring to Front', 'zu-custom-tshirt'),
                'sendToBack' => __('Send to Back', 'zu-custom-tshirt'),
                'rotate' => __('Rotate', 'zu-custom-tshirt'),
                'scale' => __('Scale', 'zu-custom-tshirt'),
                'saveDesign' => __('Save Design', 'zu-custom-tshirt'),
                'addToCart' => __('Add to Cart', 'zu-custom-tshirt'),
                'preview' => __('Preview', 'zu-custom-tshirt'),
                'export' => __('Export', 'zu-custom-tshirt'),
                'share' => __('Share', 'zu-custom-tshirt'),
                'selectPrintSide' => __('Select Print Side', 'zu-custom-tshirt'),
                'front' => __('Front', 'zu-custom-tshirt'),
                'back' => __('Back', 'zu-custom-tshirt'),
                'leftSleeve' => __('Left Sleeve', 'zu-custom-tshirt'),
                'rightSleeve' => __('Right Sleeve', 'zu-custom-tshirt'),
                'fontFamily' => __('Font Family', 'zu-custom-tshirt'),
                'fontSize' => __('Font Size', 'zu-custom-tshirt'),
                'fontColor' => __('Font Color', 'zu-custom-tshirt'),
                'textAlign' => __('Text Align', 'zu-custom-tshirt'),
                'printMethod' => __('Print Method', 'zu-custom-tshirt'),
                'material' => __('Material', 'zu-custom-tshirt'),
                'urgency' => __('Urgency', 'zu-custom-tshirt'),
                'basePrice' => __('Base Price', 'zu-custom-tshirt'),
                'extraCost' => __('Extra Cost', 'zu-custom-tshirt'),
                'totalPrice' => __('Total Price', 'zu-custom-tshirt'),
                'designSaved' => __('Design saved successfully!', 'zu-custom-tshirt'),
                'designError' => __('Error saving design.', 'zu-custom-tshirt'),
                'uploadError' => __('Error uploading image.', 'zu-custom-tshirt'),
                'maxImagesReached' => __('Maximum number of images reached.', 'zu-custom-tshirt'),
                'fileTooLarge' => __('File is too large.', 'zu-custom-tshirt'),
                'invalidFileType' => __('Invalid file type.', 'zu-custom-tshirt'),
            ],
        ];
    }

    /**
     * Add customize button to product page
     */
    public function add_customize_button(): void {
        global $product;
        
        if (!$product) {
            return;
        }

        if (!ZU_CTSD_WooCommerce::is_product_customizable($product->get_id())) {
            return;
        }

        // Include customizer template
        include ZU_CTSD_PLUGIN_DIR . 'templates/customizer.php';
    }

    /**
     * Add custom endpoint for My Account
     */
    public function add_custom_endpoints(): void {
        add_rewrite_endpoint('my-custom-designs', EP_ROOT | EP_PAGES);
    }

    /**
     * Add My Account tab
     */
    public function add_my_account_tab(array $items): array {
        // Insert before logout
        $logout = $items['customer-logout'];
        unset($items['customer-logout']);
        
        $items['my-custom-designs'] = __('My Custom Designs', 'zu-custom-tshirt');
        $items['customer-logout'] = $logout;

        return $items;
    }

    /**
     * My Account custom designs content
     */
    public function my_account_custom_designs_content(): void {
        $user_id = get_current_user_id();
        $designs = ZU_CTSD_Database::get_designs_by_user($user_id, ['limit' => -1]);
        
        include ZU_CTSD_PLUGIN_DIR . 'templates/my-account-designs.php';
    }

    /**
     * Add cart item data
     */
    public function add_cart_item_data(array $cart_item_data, int $product_id, int $variation_id): array {
        if (isset($_POST['zu_ctsd_design_id'])) {
            $design_id = intval(wp_unslash($_POST['zu_ctsd_design_id']));
            $design = ZU_CTSD_Database::get_design($design_id);
            
            if ($design) {
                $cart_item_data['zu_ctsd_design_id'] = $design_id;
                $cart_item_data['zu_ctsd_design_data'] = $design->design_data;
                $cart_item_data['zu_ctsd_preview_image'] = $design->preview_image;
                $cart_item_data['zu_ctsd_print_side'] = $design->print_side;
                $cart_item_data['zu_ctsd_print_method'] = sanitize_text_field(wp_unslash($_POST['zu_ctsd_print_method'] ?? 'dtf'));
                $cart_item_data['zu_ctsd_urgency'] = sanitize_text_field(wp_unslash($_POST['zu_ctsd_urgency'] ?? 'normal'));
            }
        }

        return $cart_item_data;
    }

    /**
     * Display cart item data
     */
    public function display_cart_item_data(array $item_data, array $cart_item): array {
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

            if (isset($cart_item['zu_ctsd_print_method'])) {
                $print_methods = [
                    'dtf' => __('DTF', 'zu-custom-tshirt'),
                    'screen' => __('Screen', 'zu-custom-tshirt'),
                    'digital' => __('Digital', 'zu-custom-tshirt'),
                    'vinyl' => __('Vinyl', 'zu-custom-tshirt'),
                    'embroidery' => __('Embroidery', 'zu-custom-tshirt'),
                ];
                $item_data[] = [
                    'key' => __('Print Method', 'zu-custom-tshirt'),
                    'value' => $print_methods[$cart_item['zu_ctsd_print_method']] ?? $cart_item['zu_ctsd_print_method'],
                ];
            }
        }

        return $item_data;
    }

    /**
     * Add order item meta
     */
    public function add_order_item_meta($item, $cart_item_key, $values, $order): void {
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
}
