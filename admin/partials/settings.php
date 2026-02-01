<?php
/**
 * Settings Template
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$settings = [
    'enabled' => get_option('zu_ctsd_enabled', 'yes'),
    'max_file_size' => get_option('zu_ctsd_max_file_size', '5'),
    'allowed_file_types' => get_option('zu_ctsd_allowed_file_types', 'jpg,jpeg,png,gif,svg'),
    'max_images' => get_option('zu_ctsd_max_images', '5'),
    'allow_text' => get_option('zu_ctsd_allow_text', 'yes'),
    'allow_images' => get_option('zu_ctsd_allow_images', 'yes'),
    'font_family' => get_option('zu_ctsd_font_family', 'Arial,Helvetica,Times New Roman,Verdana,Georgia'),
    'default_print_method' => get_option('zu_ctsd_default_print_method', 'dtf'),
    'enable_live_price' => get_option('zu_ctsd_enable_live_price', 'yes'),
    'enable_share' => get_option('zu_ctsd_enable_share', 'yes'),
    'enable_export' => get_option('zu_ctsd_enable_export', 'yes'),
    'admin_approval' => get_option('zu_ctsd_admin_approval', 'no'),
    'canvas_width' => get_option('zu_ctsd_canvas_width', '500'),
    'canvas_height' => get_option('zu_ctsd_canvas_height', '600'),
];

$print_methods = [
    'dtf' => __('DTF (Direct to Film)', 'zu-custom-tshirt'),
    'screen' => __('Screen Printing', 'zu-custom-tshirt'),
    'digital' => __('Digital Printing', 'zu-custom-tshirt'),
    'vinyl' => __('Vinyl Transfer', 'zu-custom-tshirt'),
    'embroidery' => __('Embroidery', 'zu-custom-tshirt'),
];
?>
<div class="wrap zu-ctsd-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post" action="options.php" class="zu-ctsd-settings-form">
        <?php settings_fields('zu_ctsd_settings'); ?>

        <div class="zu-ctsd-settings-wrapper">
            <!-- General Settings -->
            <div class="postbox">
                <h2><?php esc_html_e('General Settings', 'zu-custom-tshirt'); ?></h2>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_enabled"><?php esc_html_e('Enable Plugin', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="zu_ctsd_enabled" id="zu_ctsd_enabled" value="yes" 
                                           <?php checked($settings['enabled'], 'yes'); ?>>
                                    <?php esc_html_e('Enable custom T-shirt design functionality', 'zu-custom-tshirt'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_default_print_method"><?php esc_html_e('Default Print Method', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <select name="zu_ctsd_default_print_method" id="zu_ctsd_default_print_method">
                                    <?php foreach ($print_methods as $key => $label) : ?>
                                        <option value="<?php echo esc_attr($key); ?>" 
                                                <?php selected($settings['default_print_method'], $key); ?>>
                                            <?php echo esc_html($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_admin_approval"><?php esc_html_e('Admin Approval', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="zu_ctsd_admin_approval" id="zu_ctsd_admin_approval" value="yes" 
                                           <?php checked($settings['admin_approval'], 'yes'); ?>>
                                    <?php esc_html_e('Require admin approval before production', 'zu-custom-tshirt'); ?>
                                </label>
                                <p class="description">
                                    <?php esc_html_e('If enabled, orders with custom designs will require admin approval before production can begin.', 'zu-custom-tshirt'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Canvas Settings -->
            <div class="postbox">
                <h2><?php esc_html_e('Canvas Settings', 'zu-custom-tshirt'); ?></h2>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_canvas_width"><?php esc_html_e('Canvas Width', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <input type="number" name="zu_ctsd_canvas_width" id="zu_ctsd_canvas_width" 
                                       value="<?php echo esc_attr($settings['canvas_width']); ?>" 
                                       class="small-text" min="300" max="1200">
                                <span class="description"><?php esc_html_e('pixels', 'zu-custom-tshirt'); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_canvas_height"><?php esc_html_e('Canvas Height', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <input type="number" name="zu_ctsd_canvas_height" id="zu_ctsd_canvas_height" 
                                       value="<?php echo esc_attr($settings['canvas_height']); ?>" 
                                       class="small-text" min="300" max="1200">
                                <span class="description"><?php esc_html_e('pixels', 'zu-custom-tshirt'); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- File Upload Settings -->
            <div class="postbox">
                <h2><?php esc_html_e('File Upload Settings', 'zu-custom-tshirt'); ?></h2>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_max_file_size"><?php esc_html_e('Max File Size', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <input type="number" name="zu_ctsd_max_file_size" id="zu_ctsd_max_file_size" 
                                       value="<?php echo esc_attr($settings['max_file_size']); ?>" 
                                       class="small-text" min="1" max="50">
                                <span class="description"><?php esc_html_e('MB', 'zu-custom-tshirt'); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_allowed_file_types"><?php esc_html_e('Allowed File Types', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="zu_ctsd_allowed_file_types" id="zu_ctsd_allowed_file_types" 
                                       value="<?php echo esc_attr($settings['allowed_file_types']); ?>" 
                                       class="regular-text">
                                <p class="description">
                                    <?php esc_html_e('Comma-separated list of allowed file extensions (e.g., jpg,jpeg,png,gif,svg)', 'zu-custom-tshirt'); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_max_images"><?php esc_html_e('Max Images per Design', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <input type="number" name="zu_ctsd_max_images" id="zu_ctsd_max_images" 
                                       value="<?php echo esc_attr($settings['max_images']); ?>" 
                                       class="small-text" min="1" max="20">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Design Features -->
            <div class="postbox">
                <h2><?php esc_html_e('Design Features', 'zu-custom-tshirt'); ?></h2>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_allow_text"><?php esc_html_e('Allow Text', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="zu_ctsd_allow_text" id="zu_ctsd_allow_text" value="yes" 
                                           <?php checked($settings['allow_text'], 'yes'); ?>>
                                    <?php esc_html_e('Allow users to add text to designs', 'zu-custom-tshirt'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_allow_images"><?php esc_html_e('Allow Images', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="zu_ctsd_allow_images" id="zu_ctsd_allow_images" value="yes" 
                                           <?php checked($settings['allow_images'], 'yes'); ?>>
                                    <?php esc_html_e('Allow users to upload images', 'zu-custom-tshirt'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_font_family"><?php esc_html_e('Available Fonts', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <textarea name="zu_ctsd_font_family" id="zu_ctsd_font_family" rows="3" class="large-text"><?php echo esc_textarea($settings['font_family']); ?></textarea>
                                <p class="description">
                                    <?php esc_html_e('Comma-separated list of font names available for text elements.', 'zu-custom-tshirt'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Extra Features -->
            <div class="postbox">
                <h2><?php esc_html_e('Extra Features', 'zu-custom-tshirt'); ?></h2>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_enable_live_price"><?php esc_html_e('Live Price Update', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="zu_ctsd_enable_live_price" id="zu_ctsd_enable_live_price" value="yes" 
                                           <?php checked($settings['enable_live_price'], 'yes'); ?>>
                                    <?php esc_html_e('Show live price updates while designing', 'zu-custom-tshirt'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_enable_share"><?php esc_html_e('Share Design', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="zu_ctsd_enable_share" id="zu_ctsd_enable_share" value="yes" 
                                           <?php checked($settings['enable_share'], 'yes'); ?>>
                                    <?php esc_html_e('Allow users to share their designs', 'zu-custom-tshirt'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="zu_ctsd_enable_export"><?php esc_html_e('Export Design', 'zu-custom-tshirt'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="zu_ctsd_enable_export" id="zu_ctsd_enable_export" value="yes" 
                                           <?php checked($settings['enable_export'], 'yes'); ?>>
                                    <?php esc_html_e('Allow users to export designs as PNG', 'zu-custom-tshirt'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <?php submit_button(__('Save Settings', 'zu-custom-tshirt')); ?>
    </form>
</div>
