<?php
/**
 * Plugin Name: ZU Custom T-Shirt Design
 * Plugin URI: https://devmubs.com/zu-custom-tshirt-design
 * Description: Advanced WooCommerce plugin for custom T-shirt design with live preview, pricing rules, and order management.
 * Version: 1.0.0
 * Author: DevMUBS
 * Author URI: https://devmubs.com
 * License: MIT License
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: zu-custom-tshirt
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.3
 * WC requires at least: 7.0
 * WC tested up to: 8.5
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ZU_CTSD_VERSION', '1.0.0');
define('ZU_CTSD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ZU_CTSD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ZU_CTSD_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Class ZU_Custom_TShirt_Design
 * Main plugin class
 */
class ZU_Custom_TShirt_Design {

    /**
     * Single instance of the class
     */
    private static ?ZU_Custom_TShirt_Design $instance = null;

    /**
     * Plugin loader instance
     */
    private ?ZU_CTSD_Loader $loader = null;

    /**
     * Get single instance
     */
    public static function get_instance(): ZU_Custom_TShirt_Design {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->check_woocommerce();
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies(): void {
        // Autoloader
        require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-autoloader.php';
        ZU_CTSD_Autoloader::register();

        // Core classes
        require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-loader.php';
        require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-i18n.php';
        require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-activator.php';
        require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-deactivator.php';

        // Database
        require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-database.php';

        // Admin
        require_once ZU_CTSD_PLUGIN_DIR . 'admin/class-zu-ctsd-admin.php';

        // Public
        require_once ZU_CTSD_PLUGIN_DIR . 'public/class-zu-ctsd-public.php';

        // WooCommerce integration
        require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-woocommerce.php';

        // REST API
        require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-rest-api.php';

        // Design handler
        require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-design-handler.php';

        // Pricing engine
        require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-pricing.php';

        // Security
        require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-security.php';

        $this->loader = new ZU_CTSD_Loader();
    }

    /**
     * Set locale for internationalization
     */
    private function set_locale(): void {
        $plugin_i18n = new ZU_CTSD_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Define admin hooks
     */
    private function define_admin_hooks(): void {
        $plugin_admin = new ZU_CTSD_Admin();

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_order_meta_boxes');
        $this->loader->add_action('admin_notices', $plugin_admin, 'display_admin_notices');
    }

    /**
     * Define public hooks
     */
    private function define_public_hooks(): void {
        $plugin_public = new ZU_CTSD_Public();

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('woocommerce_single_product_summary', $plugin_public, 'add_customize_button', 30);
        $this->loader->add_action('woocommerce_account_menu_items', $plugin_public, 'add_my_account_tab');
        $this->loader->add_action('woocommerce_account_my-custom-designs_endpoint', $plugin_public, 'my_account_custom_designs_content');
        $this->loader->add_action('init', $plugin_public, 'add_custom_endpoints');
        $this->loader->add_filter('woocommerce_add_cart_item_data', $plugin_public, 'add_cart_item_data', 10, 3);
        $this->loader->add_filter('woocommerce_get_item_data', $plugin_public, 'display_cart_item_data', 10, 2);
        $this->loader->add_action('woocommerce_checkout_create_order_line_item', $plugin_public, 'add_order_item_meta', 10, 4);
    }

    /**
     * Check WooCommerce dependency
     */
    private function check_woocommerce(): void {
        $woocommerce_check = new ZU_CTSD_WooCommerce();
        $this->loader->add_action('admin_init', $woocommerce_check, 'check_woocommerce_active');
        $this->loader->add_action('admin_notices', $woocommerce_check, 'woocommerce_missing_notice');
    }

    /**
     * Run the loader
     */
    public function run(): void {
        $this->loader->run();
    }

    /**
     * Get plugin version
     */
    public function get_version(): string {
        return ZU_CTSD_VERSION;
    }
}

/**
 * Activation hook
 */
function activate_zu_custom_tshirt_design(): void {
    require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-activator.php';
    ZU_CTSD_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_zu_custom_tshirt_design');

/**
 * Deactivation hook
 */
function deactivate_zu_custom_tshirt_design(): void {
    require_once ZU_CTSD_PLUGIN_DIR . 'includes/class-zu-ctsd-deactivator.php';
    ZU_CTSD_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_zu_custom_tshirt_design');

/**
 * Initialize plugin
 */
function run_zu_custom_tshirt_design(): void {
    $plugin = ZU_Custom_TShirt_Design::get_instance();
    $plugin->run();
}
run_zu_custom_tshirt_design();
