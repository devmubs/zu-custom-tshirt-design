<?php
/**
 * Admin Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_Admin
 * Handles admin functionality
 */
class ZU_CTSD_Admin {

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
     * Enqueue admin styles
     */
    public function enqueue_styles(string $hook): void {
        // Only load on plugin pages
        if (strpos($hook, 'zu-tshirt') === false) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name . '-admin',
            ZU_CTSD_PLUGIN_URL . 'assets/css/admin.css',
            [],
            $this->version
        );

        wp_enqueue_style(
            $this->plugin_name . '-admin-components',
            ZU_CTSD_PLUGIN_URL . 'assets/css/admin-components.css',
            [],
            $this->version
        );
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts(string $hook): void {
        // Only load on plugin pages
        if (strpos($hook, 'zu-tshirt') === false) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_script(
            $this->plugin_name . '-admin',
            ZU_CTSD_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'jquery-ui-sortable'],
            $this->version,
            true
        );

        wp_localize_script($this->plugin_name . '-admin', 'zuCtsdAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('zu_ctsd_admin_nonce'),
            'strings' => [
                'saveSuccess' => __('Settings saved successfully!', 'zu-custom-tshirt'),
                'saveError' => __('Error saving settings.', 'zu-custom-tshirt'),
                'confirmDelete' => __('Are you sure you want to delete this item?', 'zu-custom-tshirt'),
                'uploadImage' => __('Upload Image', 'zu-custom-tshirt'),
                'selectImage' => __('Select Image', 'zu-custom-tshirt'),
            ],
        ]);
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu(): void {
        // Main menu
        add_menu_page(
            __('ZU Custom T-Shirt', 'zu-custom-tshirt'),
            __('ZU T-Shirt', 'zu-custom-tshirt'),
            'manage_zu_tshirt',
            'zu-tshirt-dashboard',
            [$this, 'render_dashboard'],
            'dashicons-art',
            30
        );

        // Dashboard submenu
        add_submenu_page(
            'zu-tshirt-dashboard',
            __('Dashboard', 'zu-custom-tshirt'),
            __('Dashboard', 'zu-custom-tshirt'),
            'manage_zu_tshirt',
            'zu-tshirt-dashboard',
            [$this, 'render_dashboard']
        );

        // Design Templates submenu
        add_submenu_page(
            'zu-tshirt-dashboard',
            __('Design Templates', 'zu-custom-tshirt'),
            __('Design Templates', 'zu-custom-tshirt'),
            'manage_zu_tshirt',
            'zu-tshirt-templates',
            [$this, 'render_templates']
        );

        // Pricing Rules submenu
        add_submenu_page(
            'zu-tshirt-dashboard',
            __('Pricing Rules', 'zu-custom-tshirt'),
            __('Pricing Rules', 'zu-custom-tshirt'),
            'manage_zu_tshirt',
            'zu-tshirt-pricing',
            [$this, 'render_pricing']
        );

        // Orders & Custom Designs submenu
        add_submenu_page(
            'zu-tshirt-dashboard',
            __('Orders & Custom Designs', 'zu-custom-tshirt'),
            __('Orders & Designs', 'zu-custom-tshirt'),
            'manage_zu_tshirt',
            'zu-tshirt-orders',
            [$this, 'render_orders']
        );

        // Settings submenu
        add_submenu_page(
            'zu-tshirt-dashboard',
            __('Settings', 'zu-custom-tshirt'),
            __('Settings', 'zu-custom-tshirt'),
            'manage_options',
            'zu-tshirt-settings',
            [$this, 'render_settings']
        );
    }

    /**
     * Register settings
     */
    public function register_settings(): void {
        // General Settings
        register_setting('zu_ctsd_settings', 'zu_ctsd_enabled');
        register_setting('zu_ctsd_settings', 'zu_ctsd_max_file_size');
        register_setting('zu_ctsd_settings', 'zu_ctsd_allowed_file_types');
        register_setting('zu_ctsd_settings', 'zu_ctsd_max_images');
        register_setting('zu_ctsd_settings', 'zu_ctsd_allow_text');
        register_setting('zu_ctsd_settings', 'zu_ctsd_allow_images');
        register_setting('zu_ctsd_settings', 'zu_ctsd_font_family');
        register_setting('zu_ctsd_settings', 'zu_ctsd_default_print_method');
        register_setting('zu_ctsd_settings', 'zu_ctsd_enable_live_price');
        register_setting('zu_ctsd_settings', 'zu_ctsd_enable_share');
        register_setting('zu_ctsd_settings', 'zu_ctsd_enable_export');
        register_setting('zu_ctsd_settings', 'zu_ctsd_admin_approval');
        register_setting('zu_ctsd_settings', 'zu_ctsd_canvas_width');
        register_setting('zu_ctsd_settings', 'zu_ctsd_canvas_height');

        // Add capabilities
        $role = get_role('administrator');
        if ($role) {
            $role->add_cap('manage_zu_tshirt');
        }
    }

    /**
     * Render dashboard page
     */
    public function render_dashboard(): void {
        $stats = ZU_CTSD_Database::get_dashboard_stats();
        include ZU_CTSD_PLUGIN_DIR . 'admin/partials/dashboard.php';
    }

    /**
     * Render templates page
     */
    public function render_templates(): void {
        $action = sanitize_text_field($_GET['action'] ?? 'list');
        $template_id = intval($_GET['template_id'] ?? 0);

        switch ($action) {
            case 'add':
            case 'edit':
                $template = $template_id ? ZU_CTSD_Database::get_template($template_id) : null;
                include ZU_CTSD_PLUGIN_DIR . 'admin/partials/template-edit.php';
                break;
            case 'delete':
                $this->handle_template_delete($template_id);
                break;
            default:
                $templates = ZU_CTSD_Database::get_templates(['is_active' => false]);
                include ZU_CTSD_PLUGIN_DIR . 'admin/partials/templates-list.php';
        }
    }

    /**
     * Render pricing page
     */
    public function render_pricing(): void {
        $pricing_rules = ZU_CTSD_Database::get_pricing_rules();
        include ZU_CTSD_PLUGIN_DIR . 'admin/partials/pricing.php';
    }

    /**
     * Render orders page
     */
    public function render_orders(): void {
        global $wpdb;
        $designs_table = ZU_CTSD_Database::get_table('designs');
        $orders_table = ZU_CTSD_Database::get_table('orders');

        $orders = $wpdb->get_results(
            "SELECT d.*, o.woocommerce_order_id, o.print_method, o.urgency, o.admin_approved, o.production_status 
            FROM {$designs_table} d 
            LEFT JOIN {$orders_table} o ON d.id = o.design_id 
            WHERE d.order_id IS NOT NULL 
            ORDER BY d.created_at DESC"
        );

        // include ZU_CTSD_Design::get_table('orders');
        include ZU_CTSD_PLUGIN_DIR . 'admin/partials/orders.php';
    }

    /**
     * Render settings page
     */
    public function render_settings(): void {
        include ZU_CTSD_PLUGIN_DIR . 'admin/partials/settings.php';
    }

    /**
     * Handle template delete
     */
    private function handle_template_delete(int $template_id): void {
        if (!$template_id) {
            wp_redirect(admin_url('admin.php?page=zu-tshirt-templates'));
            exit;
        }

        check_admin_referer('delete_template_' . $template_id);

        ZU_CTSD_Database::delete_template($template_id);

        wp_redirect(admin_url('admin.php?page=zu-tshirt-templates&deleted=1'));
        exit;
    }

    /**
     * Add order meta boxes
     */
    public function add_order_meta_boxes(): void {
        add_meta_box(
            'zu_ctsd_order_designs',
            __('Custom T-Shirt Designs', 'zu-custom-tshirt'),
            [$this, 'render_order_meta_box'],
            'shop_order',
            'normal',
            'high'
        );
    }

    /**
     * Render order meta box
     */
    public function render_order_meta_box($post): void {
        $order_id = $post->ID;
        
        global $wpdb;
        $designs_table = ZU_CTSD_Database::get_table('designs');
        $orders_table = ZU_CTSD_Database::get_table('orders');

        $designs = $wpdb->get_results($wpdb->prepare(
            "SELECT d.*, o.print_method, o.urgency, o.admin_approved, o.production_status, o.approval_notes 
            FROM {$designs_table} d 
            INNER JOIN {$orders_table} o ON d.id = o.design_id 
            WHERE o.woocommerce_order_id = %d",
            $order_id
        ));

        include ZU_CTSD_PLUGIN_DIR . 'admin/partials/order-meta-box.php';
    }

    /**
     * Display admin notices
     */
    public function display_admin_notices(): void {
        // Activation notice
        if (get_transient('zu_ctsd_activated')) {
            delete_transient('zu_ctsd_activated');
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('ZU Custom T-Shirt Design plugin has been activated successfully!', 'zu-custom-tshirt'); ?></p>
                <p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=zu-tshirt-dashboard')); ?>" class="button button-primary">
                        <?php esc_html_e('Go to Dashboard', 'zu-custom-tshirt'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=zu-tshirt-settings')); ?>" class="button">
                        <?php esc_html_e('Configure Settings', 'zu-custom-tshirt'); ?>
                    </a>
                </p>
            </div>
            <?php
        }

        // Success notices
        if (isset($_GET['saved']) && $_GET['saved'] === '1') {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Settings saved successfully!', 'zu-custom-tshirt'); ?></p>
            </div>
            <?php
        }

        if (isset($_GET['deleted']) && $_GET['deleted'] === '1') {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Item deleted successfully!', 'zu-custom-tshirt'); ?></p>
            </div>
            <?php
        }
    }
}
