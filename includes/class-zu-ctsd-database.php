<?php
/**
 * Database Handler Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_Database
 * Handles all database operations
 */
class ZU_CTSD_Database {

    /**
     * Get table name with prefix
     */
    public static function get_table(string $table): string {
        global $wpdb;
        return $wpdb->prefix . 'zu_tshirt_' . $table;
    }

    /**
     * Get a design by ID
     */
    public static function get_design(int $design_id): ?object {
        global $wpdb;
        $table = self::get_table('designs');
        
        $design = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $design_id
        ));

        return $design ?: null;
    }

    /**
     * Get designs by user ID
     */
    public static function get_designs_by_user(int $user_id, array $args = []): array {
        global $wpdb;
        $table = self::get_table('designs');
        
        $defaults = [
            'status' => '',
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
        ];
        $args = wp_parse_args($args, $defaults);

        $where = "WHERE user_id = %d";
        $params = [$user_id];

        if (!empty($args['status'])) {
            $where .= " AND status = %s";
            $params[] = $args['status'];
        }

        $orderby = sanitize_sql_orderby("{$args['orderby']} {$args['order']}");
        $limit = intval($args['limit']);
        $offset = intval($args['offset']);

        $sql = "SELECT * FROM {$table} {$where} ORDER BY {$orderby} LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;

        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    /**
     * Insert a new design
     */
    public static function insert_design(array $data): int {
        global $wpdb;
        $table = self::get_table('designs');

        $wpdb->insert($table, [
            'user_id' => intval($data['user_id'] ?? 0),
            'product_id' => intval($data['product_id'] ?? 0),
            'order_id' => !empty($data['order_id']) ? intval($data['order_id']) : null,
            'design_name' => sanitize_text_field($data['design_name'] ?? ''),
            'design_data' => wp_json_encode($data['design_data'] ?? []),
            'preview_image' => esc_url_raw($data['preview_image'] ?? ''),
            'print_side' => sanitize_text_field($data['print_side'] ?? 'front'),
            'status' => sanitize_text_field($data['status'] ?? 'draft'),
            'total_price' => floatval($data['total_price'] ?? 0),
        ]);

        return intval($wpdb->insert_id);
    }

    /**
     * Update a design
     */
    public static function update_design(int $design_id, array $data): bool {
        global $wpdb;
        $table = self::get_table('designs');

        $update_data = [];
        
        if (isset($data['design_name'])) {
            $update_data['design_name'] = sanitize_text_field($data['design_name']);
        }
        if (isset($data['design_data'])) {
            $update_data['design_data'] = wp_json_encode($data['design_data']);
        }
        if (isset($data['preview_image'])) {
            $update_data['preview_image'] = esc_url_raw($data['preview_image']);
        }
        if (isset($data['print_side'])) {
            $update_data['print_side'] = sanitize_text_field($data['print_side']);
        }
        if (isset($data['status'])) {
            $update_data['status'] = sanitize_text_field($data['status']);
        }
        if (isset($data['order_id'])) {
            $update_data['order_id'] = intval($data['order_id']);
        }
        if (isset($data['total_price'])) {
            $update_data['total_price'] = floatval($data['total_price']);
        }

        if (empty($update_data)) {
            return false;
        }

        $result = $wpdb->update(
            $table,
            $update_data,
            ['id' => $design_id],
            null,
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Delete a design
     */
    public static function delete_design(int $design_id): bool {
        global $wpdb;
        
        // Delete related elements first
        self::delete_design_elements($design_id);
        
        // Delete the design
        $table = self::get_table('designs');
        $result = $wpdb->delete($table, ['id' => $design_id], ['%d']);

        return $result !== false;
    }

    /**
     * Get design elements
     */
    public static function get_design_elements(int $design_id): array {
        global $wpdb;
        $table = self::get_table('elements');

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE design_id = %d ORDER BY layer_order ASC",
            $design_id
        ));
    }

    /**
     * Insert design element
     */
    public static function insert_design_element(array $data): int {
        global $wpdb;
        $table = self::get_table('elements');

        $wpdb->insert($table, [
            'design_id' => intval($data['design_id']),
            'element_type' => sanitize_text_field($data['element_type']),
            'element_data' => wp_json_encode($data['element_data'] ?? []),
            'position_x' => floatval($data['position_x'] ?? 0),
            'position_y' => floatval($data['position_y'] ?? 0),
            'width' => floatval($data['width'] ?? 0),
            'height' => floatval($data['height'] ?? 0),
            'rotation' => floatval($data['rotation'] ?? 0),
            'layer_order' => intval($data['layer_order'] ?? 0),
        ]);

        return intval($wpdb->insert_id);
    }

    /**
     * Delete design elements
     */
    public static function delete_design_elements(int $design_id): bool {
        global $wpdb;
        $table = self::get_table('elements');
        $result = $wpdb->delete($table, ['design_id' => $design_id], ['%d']);
        return $result !== false;
    }

    /**
     * Get pricing rules
     */
    public static function get_pricing_rules(string $rule_type = ''): array {
        global $wpdb;
        $table = self::get_table('pricing');

        if (empty($rule_type)) {
            return $wpdb->get_results("SELECT * FROM {$table} WHERE is_active = 1");
        }

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE rule_type = %s AND is_active = 1",
            $rule_type
        ));
    }

    /**
     * Get single pricing rule
     */
    public static function get_pricing_rule(string $rule_type, string $rule_key): ?object {
        global $wpdb;
        $table = self::get_table('pricing');

        $rule = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE rule_type = %s AND rule_key = %s AND is_active = 1",
            $rule_type,
            $rule_key
        ));

        return $rule ?: null;
    }

    /**
     * Update pricing rule
     */
    public static function update_pricing_rule(int $rule_id, array $data): bool {
        global $wpdb;
        $table = self::get_table('pricing');

        $update_data = [];

        if (isset($data['base_price'])) {
            $update_data['base_price'] = floatval($data['base_price']);
        }
        if (isset($data['extra_cost'])) {
            $update_data['extra_cost'] = floatval($data['extra_cost']);
        }
        if (isset($data['is_active'])) {
            $update_data['is_active'] = intval($data['is_active']);
        }

        if (empty($update_data)) {
            return false;
        }

        $result = $wpdb->update(
            $table,
            $update_data,
            ['id' => $rule_id],
            null,
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Get templates
     */
    public static function get_templates(array $args = []): array {
        global $wpdb;
        $table = self::get_table('templates');

        $defaults = [
            'is_active' => true,
            'limit' => -1,
        ];
        $args = wp_parse_args($args, $defaults);

        $where = '';
        $params = [];

        if ($args['is_active']) {
            $where = 'WHERE is_active = 1';
        }

        $sql = "SELECT * FROM {$table} {$where} ORDER BY template_name ASC";

        if ($args['limit'] > 0) {
            $sql .= ' LIMIT %d';
            $params[] = intval($args['limit']);
        }

        if (!empty($params)) {
            return $wpdb->get_results($wpdb->prepare($sql, $params));
        }

        return $wpdb->get_results($sql);
    }

    /**
     * Get template by ID
     */
    public static function get_template(int $template_id): ?object {
        global $wpdb;
        $table = self::get_table('templates');

        $template = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $template_id
        ));

        return $template ?: null;
    }

    /**
     * Insert template
     */
    public static function insert_template(array $data): int {
        global $wpdb;
        $table = self::get_table('templates');

        $wpdb->insert($table, [
            'template_name' => sanitize_text_field($data['template_name']),
            'template_slug' => sanitize_title($data['template_slug'] ?? $data['template_name']),
            'front_image' => esc_url_raw($data['front_image'] ?? ''),
            'back_image' => esc_url_raw($data['back_image'] ?? ''),
            'left_sleeve_image' => esc_url_raw($data['left_sleeve_image'] ?? ''),
            'right_sleeve_image' => esc_url_raw($data['right_sleeve_image'] ?? ''),
            'printable_area_front' => wp_json_encode($data['printable_area_front'] ?? []),
            'printable_area_back' => wp_json_encode($data['printable_area_back'] ?? []),
            'printable_area_left_sleeve' => wp_json_encode($data['printable_area_left_sleeve'] ?? []),
            'printable_area_right_sleeve' => wp_json_encode($data['printable_area_right_sleeve'] ?? []),
            'max_images' => intval($data['max_images'] ?? 5),
            'allow_text' => intval($data['allow_text'] ?? 1),
            'allow_images' => intval($data['allow_images'] ?? 1),
            'is_active' => intval($data['is_active'] ?? 1),
        ]);

        return intval($wpdb->insert_id);
    }

    /**
     * Update template
     */
    public static function update_template(int $template_id, array $data): bool {
        global $wpdb;
        $table = self::get_table('templates');

        $update_data = [];
        $fields = [
            'template_name', 'template_slug', 'front_image', 'back_image',
            'left_sleeve_image', 'right_sleeve_image', 'max_images',
            'allow_text', 'allow_images', 'is_active'
        ];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                if (in_array($field, ['front_image', 'back_image', 'left_sleeve_image', 'right_sleeve_image'])) {
                    $update_data[$field] = esc_url_raw($data[$field]);
                } elseif (in_array($field, ['max_images', 'allow_text', 'allow_images', 'is_active'])) {
                    $update_data[$field] = intval($data[$field]);
                } else {
                    $update_data[$field] = sanitize_text_field($data[$field]);
                }
            }
        }

        // Handle JSON fields
        $json_fields = ['printable_area_front', 'printable_area_back', 'printable_area_left_sleeve', 'printable_area_right_sleeve'];
        foreach ($json_fields as $field) {
            if (isset($data[$field])) {
                $update_data[$field] = wp_json_encode($data[$field]);
            }
        }

        if (empty($update_data)) {
            return false;
        }

        $result = $wpdb->update(
            $table,
            $update_data,
            ['id' => $template_id],
            null,
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Delete template
     */
    public static function delete_template(int $template_id): bool {
        global $wpdb;
        $table = self::get_table('templates');
        $result = $wpdb->delete($table, ['id' => $template_id], ['%d']);
        return $result !== false;
    }

    /**
     * Get dashboard statistics
     */
    public static function get_dashboard_stats(): array {
        global $wpdb;
        
        $designs_table = self::get_table('designs');
        $orders_table = self::get_table('orders');

        // Total designs
        $total_designs = $wpdb->get_var("SELECT COUNT(*) FROM {$designs_table}");

        // Total orders with custom designs
        $total_orders = $wpdb->get_var("SELECT COUNT(DISTINCT woocommerce_order_id) FROM {$orders_table}");

        // Revenue from custom products
        $revenue = $wpdb->get_var("SELECT SUM(total_price) FROM {$designs_table} WHERE order_id IS NOT NULL");

        // Recent customized orders
        $recent_orders = $wpdb->get_results(
            "SELECT d.*, o.woocommerce_order_id, o.production_status 
            FROM {$designs_table} d 
            LEFT JOIN {$orders_table} o ON d.id = o.design_id 
            WHERE d.order_id IS NOT NULL 
            ORDER BY d.created_at DESC 
            LIMIT 10"
        );

        return [
            'total_designs' => intval($total_designs),
            'total_orders' => intval($total_orders),
            'revenue' => floatval($revenue ?: 0),
            'recent_orders' => $recent_orders,
        ];
    }

    /**
     * Save design for later
     */
    public static function save_design_for_later(int $design_id, int $user_id, bool $is_public = false): string {
        global $wpdb;
        $table = self::get_table('saved_designs');

        // Generate unique share token
        $share_token = wp_generate_password(32, false);

        $wpdb->insert($table, [
            'design_id' => $design_id,
            'user_id' => $user_id,
            'share_token' => $share_token,
            'is_public' => intval($is_public),
        ]);

        return $share_token;
    }

    /**
     * Get saved design by share token
     */
    public static function get_saved_design_by_token(string $token): ?object {
        global $wpdb;
        $table = self::get_table('saved_designs');

        $saved = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE share_token = %s",
            $token
        ));

        if (!$saved) {
            return null;
        }

        // Check if public or belongs to current user
        if (!$saved->is_public && (!is_user_logged_in() || get_current_user_id() !== intval($saved->user_id))) {
            return null;
        }

        return self::get_design(intval($saved->design_id));
    }
}
