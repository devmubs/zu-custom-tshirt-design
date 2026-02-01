<?php
/**
 * Plugin Activator Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_Activator
 * Fired during plugin activation
 */
class ZU_CTSD_Activator {

    /**
     * Activate plugin
     */
    public static function activate(): void {
        self::create_database_tables();
        self::create_upload_directories();
        self::set_default_options();
        self::flush_rewrite_rules();
        self::set_activation_flag();
    }

    /**
     * Create database tables
     */
    private static function create_database_tables(): void {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_prefix = $wpdb->prefix . 'zu_tshirt_';

        // Designs table
        $sql_designs = "CREATE TABLE IF NOT EXISTS {$table_prefix}designs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL DEFAULT 0,
            product_id bigint(20) unsigned NOT NULL DEFAULT 0,
            order_id bigint(20) unsigned DEFAULT NULL,
            design_name varchar(255) NOT NULL,
            design_data longtext NOT NULL,
            preview_image varchar(500) DEFAULT NULL,
            print_side varchar(50) DEFAULT 'front',
            status varchar(50) DEFAULT 'draft',
            total_price decimal(10,2) DEFAULT 0.00,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY product_id (product_id),
            KEY order_id (order_id),
            KEY status (status)
        ) {$charset_collate};";

        // Design elements table
        $sql_elements = "CREATE TABLE IF NOT EXISTS {$table_prefix}elements (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            design_id bigint(20) unsigned NOT NULL,
            element_type varchar(50) NOT NULL,
            element_data longtext NOT NULL,
            position_x decimal(10,2) DEFAULT 0.00,
            position_y decimal(10,2) DEFAULT 0.00,
            width decimal(10,2) DEFAULT 0.00,
            height decimal(10,2) DEFAULT 0.00,
            rotation decimal(5,2) DEFAULT 0.00,
            layer_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY design_id (design_id),
            KEY element_type (element_type)
        ) {$charset_collate};";

        // Pricing rules table
        $sql_pricing = "CREATE TABLE IF NOT EXISTS {$table_prefix}pricing (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            rule_type varchar(100) NOT NULL,
            rule_key varchar(100) NOT NULL,
            rule_value varchar(255) DEFAULT NULL,
            base_price decimal(10,2) DEFAULT 0.00,
            extra_cost decimal(10,2) DEFAULT 0.00,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY rule_unique (rule_type, rule_key),
            KEY rule_type (rule_type),
            KEY is_active (is_active)
        ) {$charset_collate};";

        // Orders table (custom designs linked to orders)
        $sql_orders = "CREATE TABLE IF NOT EXISTS {$table_prefix}orders (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            design_id bigint(20) unsigned NOT NULL,
            woocommerce_order_id bigint(20) unsigned NOT NULL,
            woocommerce_order_item_id bigint(20) unsigned NOT NULL,
            print_method varchar(50) DEFAULT 'dtf',
            urgency varchar(50) DEFAULT 'normal',
            admin_approved tinyint(1) DEFAULT 0,
            approval_notes text,
            production_status varchar(50) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY design_id (design_id),
            KEY woocommerce_order_id (woocommerce_order_id),
            KEY production_status (production_status)
        ) {$charset_collate};";

        // Templates table
        $sql_templates = "CREATE TABLE IF NOT EXISTS {$table_prefix}templates (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            template_name varchar(255) NOT NULL,
            template_slug varchar(255) NOT NULL,
            front_image varchar(500) DEFAULT NULL,
            back_image varchar(500) DEFAULT NULL,
            left_sleeve_image varchar(500) DEFAULT NULL,
            right_sleeve_image varchar(500) DEFAULT NULL,
            printable_area_front longtext DEFAULT NULL,
            printable_area_back longtext DEFAULT NULL,
            printable_area_left_sleeve longtext DEFAULT NULL,
            printable_area_right_sleeve longtext DEFAULT NULL,
            max_images int(11) DEFAULT 5,
            allow_text tinyint(1) DEFAULT 1,
            allow_images tinyint(1) DEFAULT 1,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY template_slug (template_slug),
            KEY is_active (is_active)
        ) {$charset_collate};";

        // Template products relation table
        $sql_template_products = "CREATE TABLE IF NOT EXISTS {$table_prefix}template_products (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            template_id bigint(20) unsigned NOT NULL,
            product_id bigint(20) unsigned NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY template_product (template_id, product_id),
            KEY template_id (template_id),
            KEY product_id (product_id)
        ) {$charset_collate};";

        // Saved designs table (for save for later feature)
        $sql_saved = "CREATE TABLE IF NOT EXISTS {$table_prefix}saved_designs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            design_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            share_token varchar(64) DEFAULT NULL,
            is_public tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY share_token (share_token),
            KEY design_id (design_id),
            KEY user_id (user_id)
        ) {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($sql_designs);
        dbDelta($sql_elements);
        dbDelta($sql_pricing);
        dbDelta($sql_orders);
        dbDelta($sql_templates);
        dbDelta($sql_template_products);
        dbDelta($sql_saved);

        // Store database version
        update_option('zu_ctsd_db_version', ZU_CTSD_VERSION);
    }

    /**
     * Create upload directories
     */
    private static function create_upload_directories(): void {
        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'] . '/zu-tshirt-designs';

        $directories = [
            $base_dir,
            $base_dir . '/designs',
            $base_dir . '/templates',
            $base_dir . '/previews',
            $base_dir . '/exports',
            $base_dir . '/temp',
        ];

        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                wp_mkdir_p($directory);
                // Create .htaccess for security
                $htaccess_file = $directory . '/.htaccess';
                if (!file_exists($htaccess_file)) {
                    $htaccess_content = "Options -Indexes\n";
                    $htaccess_content .= "<FilesMatch '\.(php|php\\.|php3|php4|phtml|pl|py|jsp|asp|sh|cgi)$'>\n";
                    $htaccess_content .= "Order allow,deny\n";
                    $htaccess_content .= "Deny from all\n";
                    $htaccess_content .= "</FilesMatch>\n";
                    file_put_contents($htaccess_file, $htaccess_content);
                }
                // Create index.php for security
                $index_file = $directory . '/index.php';
                if (!file_exists($index_file)) {
                    file_put_contents($index_file, '<?php // Silence is golden');
                }
            }
        }
    }

    /**
     * Set default options
     */
    private static function set_default_options(): void {
        $default_options = [
            'zu_ctsd_enabled' => 'yes',
            'zu_ctsd_max_file_size' => '5',
            'zu_ctsd_allowed_file_types' => 'jpg,jpeg,png,gif,svg',
            'zu_ctsd_max_images' => '5',
            'zu_ctsd_allow_text' => 'yes',
            'zu_ctsd_allow_images' => 'yes',
            'zu_ctsd_font_family' => 'Arial,Helvetica,Times New Roman,Verdana,Georgia',
            'zu_ctsd_default_print_method' => 'dtf',
            'zu_ctsd_enable_live_price' => 'yes',
            'zu_ctsd_enable_share' => 'yes',
            'zu_ctsd_enable_export' => 'yes',
            'zu_ctsd_admin_approval' => 'no',
            'zu_ctsd_canvas_width' => '500',
            'zu_ctsd_canvas_height' => '600',
        ];

        foreach ($default_options as $option_name => $option_value) {
            if (false === get_option($option_name)) {
                add_option($option_name, $option_value);
            }
        }

        // Insert default pricing rules
        self::insert_default_pricing_rules();
    }

    /**
     * Insert default pricing rules
     */
    private static function insert_default_pricing_rules(): void {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zu_tshirt_pricing';

        $default_rules = [
            // Image count pricing
            ['rule_type' => 'image_count', 'rule_key' => '1', 'rule_value' => '1 image', 'base_price' => 0.00, 'extra_cost' => 5.00],
            ['rule_type' => 'image_count', 'rule_key' => '2', 'rule_value' => '2 images', 'base_price' => 0.00, 'extra_cost' => 8.00],
            ['rule_type' => 'image_count', 'rule_key' => '3', 'rule_value' => '3 images', 'base_price' => 0.00, 'extra_cost' => 11.00],
            ['rule_type' => 'image_count', 'rule_key' => '4', 'rule_value' => '4 images', 'base_price' => 0.00, 'extra_cost' => 14.00],
            ['rule_type' => 'image_count', 'rule_key' => '5', 'rule_value' => '5 images', 'base_price' => 0.00, 'extra_cost' => 17.00],

            // Print size pricing
            ['rule_type' => 'print_size', 'rule_key' => 'small', 'rule_value' => 'Small (up to 10x10cm)', 'base_price' => 0.00, 'extra_cost' => 0.00],
            ['rule_type' => 'print_size', 'rule_key' => 'medium', 'rule_value' => 'Medium (up to 20x20cm)', 'base_price' => 0.00, 'extra_cost' => 5.00],
            ['rule_type' => 'print_size', 'rule_key' => 'large', 'rule_value' => 'Large (up to 30x30cm)', 'base_price' => 0.00, 'extra_cost' => 10.00],
            ['rule_type' => 'print_size', 'rule_key' => 'xlarge', 'rule_value' => 'Extra Large (up to 40x40cm)', 'base_price' => 0.00, 'extra_cost' => 15.00],

            // Print method pricing
            ['rule_type' => 'print_method', 'rule_key' => 'dtf', 'rule_value' => 'DTF (Direct to Film)', 'base_price' => 0.00, 'extra_cost' => 0.00],
            ['rule_type' => 'print_method', 'rule_key' => 'screen', 'rule_value' => 'Screen Printing', 'base_price' => 0.00, 'extra_cost' => 3.00],
            ['rule_type' => 'print_method', 'rule_key' => 'digital', 'rule_value' => 'Digital Printing', 'base_price' => 0.00, 'extra_cost' => 5.00],
            ['rule_type' => 'print_method', 'rule_key' => 'vinyl', 'rule_value' => 'Vinyl Transfer', 'base_price' => 0.00, 'extra_cost' => 4.00],
            ['rule_type' => 'print_method', 'rule_key' => 'embroidery', 'rule_value' => 'Embroidery', 'base_price' => 0.00, 'extra_cost' => 15.00],

            // Material pricing
            ['rule_type' => 'material', 'rule_key' => 'cotton', 'rule_value' => '100% Cotton', 'base_price' => 0.00, 'extra_cost' => 0.00],
            ['rule_type' => 'material', 'rule_key' => 'polyester', 'rule_value' => 'Polyester', 'base_price' => 0.00, 'extra_cost' => 2.00],
            ['rule_type' => 'material', 'rule_key' => 'blend', 'rule_value' => 'Cotton-Polyester Blend', 'base_price' => 0.00, 'extra_cost' => 3.00],

            // Urgency pricing
            ['rule_type' => 'urgency', 'rule_key' => 'normal', 'rule_value' => 'Normal (5-7 days)', 'base_price' => 0.00, 'extra_cost' => 0.00],
            ['rule_type' => 'urgency', 'rule_key' => 'express', 'rule_value' => 'Express (2-3 days)', 'base_price' => 0.00, 'extra_cost' => 12.00],
            ['rule_type' => 'urgency', 'rule_key' => 'rush', 'rule_value' => 'Rush (24 hours)', 'base_price' => 0.00, 'extra_cost' => 25.00],
        ];

        foreach ($default_rules as $rule) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$table_name} WHERE rule_type = %s AND rule_key = %s",
                $rule['rule_type'],
                $rule['rule_key']
            ));

            if (!$exists) {
                $wpdb->insert($table_name, $rule);
            }
        }
    }

    /**
     * Flush rewrite rules
     */
    private static function flush_rewrite_rules(): void {
        flush_rewrite_rules();
    }

    /**
     * Set activation flag
     */
    private static function set_activation_flag(): void {
        set_transient('zu_ctsd_activated', true, 30);
    }
}
