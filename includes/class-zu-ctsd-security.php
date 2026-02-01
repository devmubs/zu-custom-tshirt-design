<?php
/**
 * Security Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_Security
 * Handles security-related functionality
 */
class ZU_CTSD_Security {

    /**
     * Nonce action for plugin
     */
    const NONCE_ACTION = 'zu_ctsd_nonce';

    /**
     * Nonce name for plugin
     */
    const NONCE_NAME = 'zu_ctsd_nonce_field';

    /**
     * Allowed file types for upload
     */
    private static array $allowed_mime_types = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
    ];

    /**
     * Verify nonce
     */
    public static function verify_nonce(string $nonce = '', string $action = ''): bool {
        $action = $action ?: self::NONCE_ACTION;
        return wp_verify_nonce($nonce, $action) !== false;
    }

    /**
     * Create nonce
     */
    public static function create_nonce(string $action = ''): string {
        $action = $action ?: self::NONCE_ACTION;
        return wp_create_nonce($action);
    }

    /**
     * Check user capability
     */
    public static function check_capability(string $capability = 'manage_zu_tshirt'): bool {
        return current_user_can($capability);
    }

    /**
     * Sanitize design data
     */
    public static function sanitize_design_data(array $data): array {
        $sanitized = [];

        // Sanitize elements
        if (isset($data['elements']) && is_array($data['elements'])) {
            $sanitized['elements'] = array_map([__CLASS__, 'sanitize_element'], $data['elements']);
        }

        // Sanitize other fields
        $sanitized['print_side'] = sanitize_text_field($data['print_side'] ?? 'front');
        $sanitized['print_method'] = sanitize_text_field($data['print_method'] ?? 'dtf');
        $sanitized['material'] = sanitize_text_field($data['material'] ?? 'cotton');
        $sanitized['urgency'] = sanitize_text_field($data['urgency'] ?? 'normal');
        $sanitized['template_id'] = intval($data['template_id'] ?? 0);
        $sanitized['product_id'] = intval($data['product_id'] ?? 0);

        return $sanitized;
    }

    /**
     * Sanitize element data
     */
    private static function sanitize_element(array $element): array {
        $sanitized = [];

        $sanitized['type'] = sanitize_text_field($element['type'] ?? 'text');
        $sanitized['content'] = self::sanitize_element_content($element['content'] ?? '', $sanitized['type']);
        $sanitized['position_x'] = floatval($element['position_x'] ?? 0);
        $sanitized['position_y'] = floatval($element['position_y'] ?? 0);
        $sanitized['width'] = floatval($element['width'] ?? 100);
        $sanitized['height'] = floatval($element['height'] ?? 100);
        $sanitized['rotation'] = floatval($element['rotation'] ?? 0);
        $sanitized['scale_x'] = floatval($element['scale_x'] ?? 1);
        $sanitized['scale_y'] = floatval($element['scale_y'] ?? 1);
        $sanitized['opacity'] = floatval($element['opacity'] ?? 1);

        // Text-specific properties
        if ($sanitized['type'] === 'text') {
            $sanitized['font_family'] = sanitize_text_field($element['font_family'] ?? 'Arial');
            $sanitized['font_size'] = intval($element['font_size'] ?? 24);
            $sanitized['font_color'] = sanitize_hex_color($element['font_color'] ?? '#000000');
            $sanitized['text_align'] = sanitize_text_field($element['text_align'] ?? 'left');
            $sanitized['font_weight'] = sanitize_text_field($element['font_weight'] ?? 'normal');
            $sanitized['font_style'] = sanitize_text_field($element['font_style'] ?? 'normal');
        }

        return $sanitized;
    }

    /**
     * Sanitize element content based on type
     */
    private static function sanitize_element_content(string $content, string $type): string {
        switch ($type) {
            case 'text':
                return sanitize_text_field($content);
            case 'image':
                return esc_url_raw($content);
            default:
                return sanitize_text_field($content);
        }
    }

    /**
     * Validate file upload
     */
    public static function validate_file_upload(array $file): array {
        $errors = [];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = self::get_upload_error_message($file['error']);
            return ['valid' => false, 'errors' => $errors];
        }

        // Check file size
        $max_size = intval(get_option('zu_ctsd_max_file_size', 5)) * 1024 * 1024; // Convert MB to bytes
        if ($file['size'] > $max_size) {
            /* translators: %s: Maximum file size in MB */
            $errors[] = sprintf(__('File size exceeds maximum allowed size of %s MB.', 'zu-custom-tshirt'), get_option('zu_ctsd_max_file_size', 5));
        }

        // Check file type
        $file_info = wp_check_filetype($file['name'], self::$allowed_mime_types);
        if (!$file_info['ext']) {
            $errors[] = __('Invalid file type. Allowed types: JPG, PNG, GIF, SVG.', 'zu-custom-tshirt');
        }

        // Verify MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset(self::$allowed_mime_types[$mime_type])) {
            $errors[] = __('Invalid file type detected.', 'zu-custom-tshirt');
        }

        // Check for PHP code in image
        if (self::contains_php_code($file['tmp_name'])) {
            $errors[] = __('File contains potentially malicious code.', 'zu-custom-tshirt');
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'mime_type' => $mime_type,
            'extension' => $file_info['ext'],
        ];
    }

    /**
     * Check if file contains PHP code
     */
    private static function contains_php_code(string $file_path): bool {
        $content = file_get_contents($file_path);
        
        // Check for PHP tags
        $php_patterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<\?/i',
            '/\?>/i',
        ];

        foreach ($php_patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get upload error message
     */
    private static function get_upload_error_message(int $error_code): string {
        $messages = [
            UPLOAD_ERR_INI_SIZE => __('The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'zu-custom-tshirt'),
            UPLOAD_ERR_FORM_SIZE => __('The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.', 'zu-custom-tshirt'),
            UPLOAD_ERR_PARTIAL => __('The uploaded file was only partially uploaded.', 'zu-custom-tshirt'),
            UPLOAD_ERR_NO_FILE => __('No file was uploaded.', 'zu-custom-tshirt'),
            UPLOAD_ERR_NO_TMP_DIR => __('Missing a temporary folder.', 'zu-custom-tshirt'),
            UPLOAD_ERR_CANT_WRITE => __('Failed to write file to disk.', 'zu-custom-tshirt'),
            UPLOAD_ERR_EXTENSION => __('A PHP extension stopped the file upload.', 'zu-custom-tshirt'),
        ];

        return $messages[$error_code] ?? __('Unknown upload error.', 'zu-custom-tshirt');
    }

    /**
     * Sanitize SQL orderby
     */
    public static function sanitize_orderby(string $orderby, array $allowed_columns): string {
        if (in_array($orderby, $allowed_columns, true)) {
            return $orderby;
        }
        return $allowed_columns[0] ?? 'id';
    }

    /**
     * Sanitize SQL order
     */
    public static function sanitize_order(string $order): string {
        $order = strtoupper($order);
        return in_array($order, ['ASC', 'DESC'], true) ? $order : 'DESC';
    }

    /**
     * Secure file path
     */
    public static function secure_file_path(string $path): string {
        // Remove any null bytes
        $path = str_replace(chr(0), '', $path);
        
        // Remove any path traversal attempts
        $path = preg_replace('/\.\.+/', '', $path);
        
        // Ensure path is within allowed directory
        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'] . '/zu-tshirt-designs';
        
        $real_path = realpath($base_dir . '/' . $path);
        $real_base = realpath($base_dir);
        
        if ($real_path === false || strpos($real_path, $real_base) !== 0) {
            return '';
        }
        
        return $real_path;
    }

    /**
     * Rate limiting check
     */
    public static function check_rate_limit(string $action, int $max_attempts = 10, int $time_window = 60): bool {
        $transient_key = 'zu_ctsd_rate_limit_' . $action . '_' . self::get_client_ip();
        $attempts = get_transient($transient_key);

        if ($attempts === false) {
            set_transient($transient_key, 1, $time_window);
            return true;
        }

        if ($attempts >= $max_attempts) {
            return false;
        }

        set_transient($transient_key, $attempts + 1, $time_window);
        return true;
    }

    /**
     * Get client IP address
     */
    private static function get_client_ip(): string {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = sanitize_text_field(wp_unslash($_SERVER[$key]));
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }

    /**
     * Log security event
     */
    public static function log_security_event(string $event, array $data = []): void {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf(
                '[ZU CTSD Security] %s: %s',
                $event,
                wp_json_encode($data)
            ));
        }

        do_action('zu_ctsd_security_event', $event, $data);
    }
}
