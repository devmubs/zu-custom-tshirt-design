<?php
/**
 * Autoloader Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_Autoloader
 * Handles autoloading of plugin classes
 */
class ZU_CTSD_Autoloader {

    /**
     * Classes map
     */
    private static array $classes_map = [
        'ZU_CTSD_Loader' => 'includes/class-zu-ctsd-loader.php',
        'ZU_CTSD_i18n' => 'includes/class-zu-ctsd-i18n.php',
        'ZU_CTSD_Activator' => 'includes/class-zu-ctsd-activator.php',
        'ZU_CTSD_Deactivator' => 'includes/class-zu-ctsd-deactivator.php',
        'ZU_CTSD_Database' => 'includes/class-zu-ctsd-database.php',
        'ZU_CTSD_Admin' => 'admin/class-zu-ctsd-admin.php',
        'ZU_CTSD_Public' => 'public/class-zu-ctsd-public.php',
        'ZU_CTSD_WooCommerce' => 'includes/class-zu-ctsd-woocommerce.php',
        'ZU_CTSD_REST_API' => 'includes/class-zu-ctsd-rest-api.php',
        'ZU_CTSD_Design_Handler' => 'includes/class-zu-ctsd-design-handler.php',
        'ZU_CTSD_Pricing' => 'includes/class-zu-ctsd-pricing.php',
        'ZU_CTSD_Security' => 'includes/class-zu-ctsd-security.php',
    ];

    /**
     * Register autoloader
     */
    public static function register(): void {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    /**
     * Autoload class
     */
    public static function autoload(string $class): void {
        if (isset(self::$classes_map[$class])) {
            $file = ZU_CTSD_PLUGIN_DIR . self::$classes_map[$class];
            if (file_exists($file)) {
                require $file;
            }
        }
    }
}
