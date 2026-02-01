<?php
/**
 * Design Handler Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_Design_Handler
 * Handles design-related operations
 */
class ZU_CTSD_Design_Handler {

    /**
     * Save design
     */
    public function save_design(array $data): array {
        // Verify nonce
        if (!isset($data['nonce']) || !ZU_CTSD_Security::verify_nonce($data['nonce'])) {
            return [
                'success' => false,
                'message' => __('Security check failed.', 'zu-custom-tshirt'),
            ];
        }

        // Check rate limit
        if (!ZU_CTSD_Security::check_rate_limit('save_design', 10, 60)) {
            return [
                'success' => false,
                'message' => __('Too many requests. Please try again later.', 'zu-custom-tshirt'),
            ];
        }

        // Sanitize design data
        $design_data = ZU_CTSD_Security::sanitize_design_data($data);

        // Validate required fields
        if (empty($design_data['product_id'])) {
            return [
                'success' => false,
                'message' => __('Product ID is required.', 'zu-custom-tshirt'),
            ];
        }

        // Get user ID
        $user_id = get_current_user_id();

        // Prepare design name
        $design_name = !empty($data['design_name']) 
            ? sanitize_text_field($data['design_name']) 
            : sprintf(__('Design #%s', 'zu-custom-tshirt'), wp_date('Y-m-d H:i:s'));

        // Calculate price
        $product = wc_get_product($design_data['product_id']);
        $base_price = $product ? $product->get_price() : 0;
        
        $pricing_engine = new ZU_CTSD_Pricing();
        $price_data = $pricing_engine->get_live_price($base_price, $design_data);

        // Handle preview image
        $preview_image = '';
        if (!empty($data['preview_image'])) {
            $preview_image = $this->save_preview_image($data['preview_image'], $user_id);
        }

        // Insert design
        $design_id = ZU_CTSD_Database::insert_design([
            'user_id' => $user_id,
            'product_id' => $design_data['product_id'],
            'design_name' => $design_name,
            'design_data' => $design_data,
            'preview_image' => $preview_image,
            'print_side' => $design_data['print_side'],
            'status' => 'draft',
            'total_price' => $price_data['total_price'],
        ]);

        if (!$design_id) {
            return [
                'success' => false,
                'message' => __('Failed to save design.', 'zu-custom-tshirt'),
            ];
        }

        // Save design elements
        if (!empty($design_data['elements'])) {
            foreach ($design_data['elements'] as $index => $element) {
                ZU_CTSD_Database::insert_design_element([
                    'design_id' => $design_id,
                    'element_type' => $element['type'],
                    'element_data' => $element,
                    'position_x' => $element['position_x'],
                    'position_y' => $element['position_y'],
                    'width' => $element['width'],
                    'height' => $element['height'],
                    'rotation' => $element['rotation'],
                    'layer_order' => $index,
                ]);
            }
        }

        return [
            'success' => true,
            'design_id' => $design_id,
            'message' => __('Design saved successfully!', 'zu-custom-tshirt'),
            'price_data' => $price_data,
            'preview_image' => $preview_image,
        ];
    }

    /**
     * Update design
     */
    public function update_design(int $design_id, array $data): array {
        // Verify nonce
        if (!isset($data['nonce']) || !ZU_CTSD_Security::verify_nonce($data['nonce'])) {
            return [
                'success' => false,
                'message' => __('Security check failed.', 'zu-custom-tshirt'),
            ];
        }

        // Get existing design
        $design = ZU_CTSD_Database::get_design($design_id);
        
        if (!$design) {
            return [
                'success' => false,
                'message' => __('Design not found.', 'zu-custom-tshirt'),
            ];
        }

        // Check ownership
        if ($design->user_id != get_current_user_id() && !current_user_can('manage_zu_tshirt')) {
            return [
                'success' => false,
                'message' => __('You do not have permission to edit this design.', 'zu-custom-tshirt'),
            ];
        }

        // Sanitize design data
        $design_data = ZU_CTSD_Security::sanitize_design_data($data);

        // Update design name
        $update_data = [];
        if (!empty($data['design_name'])) {
            $update_data['design_name'] = sanitize_text_field($data['design_name']);
        }

        // Recalculate price
        $product = wc_get_product($design->product_id);
        $base_price = $product ? $product->get_price() : 0;
        
        $pricing_engine = new ZU_CTSD_Pricing();
        $price_data = $pricing_engine->get_live_price($base_price, $design_data);
        $update_data['total_price'] = $price_data['total_price'];

        // Handle preview image
        if (!empty($data['preview_image'])) {
            $update_data['preview_image'] = $this->save_preview_image($data['preview_image'], $design->user_id);
        }

        $update_data['design_data'] = $design_data;
        $update_data['print_side'] = $design_data['print_side'];

        // Update design
        $result = ZU_CTSD_Database::update_design($design_id, $update_data);

        if (!$result) {
            return [
                'success' => false,
                'message' => __('Failed to update design.', 'zu-custom-tshirt'),
            ];
        }

        // Update elements
        ZU_CTSD_Database::delete_design_elements($design_id);
        
        if (!empty($design_data['elements'])) {
            foreach ($design_data['elements'] as $index => $element) {
                ZU_CTSD_Database::insert_design_element([
                    'design_id' => $design_id,
                    'element_type' => $element['type'],
                    'element_data' => $element,
                    'position_x' => $element['position_x'],
                    'position_y' => $element['position_y'],
                    'width' => $element['width'],
                    'height' => $element['height'],
                    'rotation' => $element['rotation'],
                    'layer_order' => $index,
                ]);
            }
        }

        return [
            'success' => true,
            'design_id' => $design_id,
            'message' => __('Design updated successfully!', 'zu-custom-tshirt'),
            'price_data' => $price_data,
        ];
    }

    /**
     * Delete design
     */
    public function delete_design(int $design_id): array {
        // Get existing design
        $design = ZU_CTSD_Database::get_design($design_id);
        
        if (!$design) {
            return [
                'success' => false,
                'message' => __('Design not found.', 'zu-custom-tshirt'),
            ];
        }

        // Check ownership
        if ($design->user_id != get_current_user_id() && !current_user_can('manage_zu_tshirt')) {
            return [
                'success' => false,
                'message' => __('You do not have permission to delete this design.', 'zu-custom-tshirt'),
            ];
        }

        // Delete preview image if exists
        if (!empty($design->preview_image)) {
            $this->delete_preview_image($design->preview_image);
        }

        // Delete design
        $result = ZU_CTSD_Database::delete_design($design_id);

        if (!$result) {
            return [
                'success' => false,
                'message' => __('Failed to delete design.', 'zu-custom-tshirt'),
            ];
        }

        return [
            'success' => true,
            'message' => __('Design deleted successfully!', 'zu-custom-tshirt'),
        ];
    }

    /**
     * Get design
     */
    public function get_design(int $design_id): ?object {
        $design = ZU_CTSD_Database::get_design($design_id);

        if (!$design) {
            return null;
        }

        // Check ownership for private designs
        if ($design->user_id != get_current_user_id() && !current_user_can('manage_zu_tshirt')) {
            return null;
        }

        // Get elements
        $design->elements = ZU_CTSD_Database::get_design_elements($design_id);

        return $design;
    }

    /**
     * Save preview image
     */
    private function save_preview_image(string $base64_image, int $user_id): string {
        // Decode base64 image
        $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64_image));
        
        if (!$image_data) {
            return '';
        }

        // Generate unique filename
        $filename = 'design_' . $user_id . '_' . time() . '_' . wp_rand(1000, 9999) . '.png';
        
        // Get upload directory
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'] . '/zu-tshirt-designs/previews/';
        
        if (!file_exists($upload_path)) {
            wp_mkdir_p($upload_path);
        }

        // Save image
        $file_path = $upload_path . $filename;
        file_put_contents($file_path, $image_data);

        // Return URL
        return $upload_dir['baseurl'] . '/zu-tshirt-designs/previews/' . $filename;
    }

    /**
     * Delete preview image
     */
    private function delete_preview_image(string $image_url): void {
        $upload_dir = wp_upload_dir();
        $image_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $image_url);
        
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    /**
     * Upload image
     */
    public function upload_image(array $file): array {
        // Check rate limit
        if (!ZU_CTSD_Security::check_rate_limit('upload_image', 5, 60)) {
            return [
                'success' => false,
                'message' => __('Too many upload requests. Please try again later.', 'zu-custom-tshirt'),
            ];
        }

        // Validate file
        $validation = ZU_CTSD_Security::validate_file_upload($file);
        
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => implode(' ', $validation['errors']),
            ];
        }

        // Handle upload using WordPress
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $upload_overrides = ['test_form' => false];
        $movefile = wp_handle_upload($file, $upload_overrides);

        if (isset($movefile['error'])) {
            return [
                'success' => false,
                'message' => $movefile['error'],
            ];
        }

        // Move to our custom directory
        $upload_dir = wp_upload_dir();
        $custom_dir = $upload_dir['basedir'] . '/zu-tshirt-designs/designs/';
        
        if (!file_exists($custom_dir)) {
            wp_mkdir_p($custom_dir);
        }

        $new_filename = 'upload_' . get_current_user_id() . '_' . time() . '_' . wp_rand(1000, 9999) . '.' . $validation['extension'];
        $new_path = $custom_dir . $new_filename;

        rename($movefile['file'], $new_path);

        return [
            'success' => true,
            'url' => $upload_dir['baseurl'] . '/zu-tshirt-designs/designs/' . $new_filename,
            'filename' => $new_filename,
        ];
    }

    /**
     * Export design as PNG
     */
    public function export_design(int $design_id): array {
        $design = $this->get_design($design_id);

        if (!$design) {
            return [
                'success' => false,
                'message' => __('Design not found.', 'zu-custom-tshirt'),
            ];
        }

        // Return preview image URL if exists
        if (!empty($design->preview_image)) {
            return [
                'success' => true,
                'url' => $design->preview_image,
                'filename' => 'design_' . $design_id . '.png',
            ];
        }

        return [
            'success' => false,
            'message' => __('No preview image available for this design.', 'zu-custom-tshirt'),
        ];
    }

    /**
     * Share design
     */
    public function share_design(int $design_id, bool $is_public = false): array {
        $design = $this->get_design($design_id);

        if (!$design) {
            return [
                'success' => false,
                'message' => __('Design not found.', 'zu-custom-tshirt'),
            ];
        }

        $share_token = ZU_CTSD_Database::save_design_for_later($design_id, $design->user_id, $is_public);

        $share_url = add_query_arg([
            'zu_ctsd_share' => $share_token,
        ], home_url());

        return [
            'success' => true,
            'share_url' => $share_url,
            'share_token' => $share_token,
            'message' => __('Design shared successfully!', 'zu-custom-tshirt'),
        ];
    }

    /**
     * Reorder design
     */
    public function reorder_design(int $design_id): array {
        $design = $this->get_design($design_id);

        if (!$design) {
            return [
                'success' => false,
                'message' => __('Design not found.', 'zu-custom-tshirt'),
            ];
        }

        // Create a new design based on the existing one
        $new_design_id = ZU_CTSD_Database::insert_design([
            'user_id' => get_current_user_id(),
            'product_id' => $design->product_id,
            'design_name' => $design->design_name . ' (' . __('Copy', 'zu-custom-tshirt') . ')',
            'design_data' => json_decode($design->design_data, true),
            'preview_image' => $design->preview_image,
            'print_side' => $design->print_side,
            'status' => 'draft',
            'total_price' => $design->total_price,
        ]);

        if (!$new_design_id) {
            return [
                'success' => false,
                'message' => __('Failed to create reorder.', 'zu-custom-tshirt'),
            ];
        }

        // Copy elements
        foreach ($design->elements as $element) {
            ZU_CTSD_Database::insert_design_element([
                'design_id' => $new_design_id,
                'element_type' => $element->element_type,
                'element_data' => json_decode($element->element_data, true),
                'position_x' => $element->position_x,
                'position_y' => $element->position_y,
                'width' => $element->width,
                'height' => $element->height,
                'rotation' => $element->rotation,
                'layer_order' => $element->layer_order,
            ]);
        }

        return [
            'success' => true,
            'design_id' => $new_design_id,
            'message' => __('Design ready for reorder!', 'zu-custom-tshirt'),
            'redirect_url' => get_permalink($design->product_id),
        ];
    }
}
