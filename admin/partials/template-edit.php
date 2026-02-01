<?php
/**
 * Template Edit Template
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$is_edit = $template !== null;
$page_title = $is_edit ? __('Edit Template', 'zu-custom-tshirt') : __('Add New Template', 'zu-custom-tshirt');

// Default values
$template_data = $is_edit ? (array) $template : [
    'template_name' => '',
    'template_slug' => '',
    'front_image' => '',
    'back_image' => '',
    'left_sleeve_image' => '',
    'right_sleeve_image' => '',
    'printable_area_front' => [],
    'printable_area_back' => [],
    'printable_area_left_sleeve' => [],
    'printable_area_right_sleeve' => [],
    'max_images' => 5,
    'allow_text' => 1,
    'allow_images' => 1,
    'is_active' => 1,
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zu_ctsd_template_nonce'])) {
    if (wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['zu_ctsd_template_nonce'])), 'save_zu_ctsd_template')) {
        $template_data = [
            'template_name' => sanitize_text_field(wp_unslash($_POST['template_name'] ?? '')),
            'template_slug' => sanitize_title(wp_unslash($_POST['template_slug'] ?? $_POST['template_name'] ?? '')),
            'front_image' => esc_url_raw(wp_unslash($_POST['front_image'] ?? '')),
            'back_image' => esc_url_raw(wp_unslash($_POST['back_image'] ?? '')),
            'left_sleeve_image' => esc_url_raw(wp_unslash($_POST['left_sleeve_image'] ?? '')),
            'right_sleeve_image' => esc_url_raw(wp_unslash($_POST['right_sleeve_image'] ?? '')),
            'printable_area_front' => [
                'x' => floatval(wp_unslash($_POST['printable_area_front_x'] ?? 50)),
                'y' => floatval(wp_unslash($_POST['printable_area_front_y'] ?? 50)),
                'width' => floatval(wp_unslash($_POST['printable_area_front_width'] ?? 400)),
                'height' => floatval(wp_unslash($_POST['printable_area_front_height'] ?? 500)),
            ],
            'printable_area_back' => [
                'x' => floatval(wp_unslash($_POST['printable_area_back_x'] ?? 50)),
                'y' => floatval(wp_unslash($_POST['printable_area_back_y'] ?? 50)),
                'width' => floatval(wp_unslash($_POST['printable_area_back_width'] ?? 400)),
                'height' => floatval(wp_unslash($_POST['printable_area_back_height'] ?? 500)),
            ],
            'printable_area_left_sleeve' => [
                'x' => floatval(wp_unslash($_POST['printable_area_left_sleeve_x'] ?? 10)),
                'y' => floatval(wp_unslash($_POST['printable_area_left_sleeve_y'] ?? 50)),
                'width' => floatval(wp_unslash($_POST['printable_area_left_sleeve_width'] ?? 80)),
                'height' => floatval(wp_unslash($_POST['printable_area_left_sleeve_height'] ?? 150)),
            ],
            'printable_area_right_sleeve' => [
                'x' => floatval(wp_unslash($_POST['printable_area_right_sleeve_x'] ?? 10)),
                'y' => floatval(wp_unslash($_POST['printable_area_right_sleeve_y'] ?? 50)),
                'width' => floatval(wp_unslash($_POST['printable_area_right_sleeve_width'] ?? 80)),
                'height' => floatval(wp_unslash($_POST['printable_area_right_sleeve_height'] ?? 150)),
            ],
            'max_images' => intval(wp_unslash($_POST['max_images'] ?? 5)),
            'allow_text' => isset($_POST['allow_text']) ? 1 : 0,
            'allow_images' => isset($_POST['allow_images']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($is_edit) {
            ZU_CTSD_Database::update_template($template->id, $template_data);
            $success_message = __('Template updated successfully!', 'zu-custom-tshirt');
        } else {
            $new_id = ZU_CTSD_Database::insert_template($template_data);
            $success_message = __('Template created successfully!', 'zu-custom-tshirt');
            // Redirect to edit page
            wp_redirect(admin_url('admin.php?page=zu-tshirt-templates&action=edit&template_id=' . $new_id . '&saved=1'));
            exit;
        }
    }
}
?>
<div class="wrap zu-ctsd-admin">
    <h1><?php echo esc_html($page_title); ?></h1>

    <?php if (isset($success_message)): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html($success_message); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" class="zu-ctsd-template-form">
        <?php wp_nonce_field('save_zu_ctsd_template', 'zu_ctsd_template_nonce'); ?>

        <div class="zu-ctsd-form-wrapper">
            <!-- Main Settings -->
            <div class="zu-ctsd-form-main">
                <div class="postbox">
                    <h2><?php esc_html_e('Template Information', 'zu-custom-tshirt'); ?></h2>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label
                                        for="template_name"><?php esc_html_e('Template Name', 'zu-custom-tshirt'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="template_name" id="template_name"
                                        value="<?php echo esc_attr($template_data['template_name']); ?>"
                                        class="regular-text" required>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="template_slug"><?php esc_html_e('Slug', 'zu-custom-tshirt'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="template_slug" id="template_slug"
                                        value="<?php echo esc_attr($template_data['template_slug']); ?>"
                                        class="regular-text">
                                    <p class="description">
                                        <?php esc_html_e('Leave empty to auto-generate from name.', 'zu-custom-tshirt'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label
                                        for="max_images"><?php esc_html_e('Max Images', 'zu-custom-tshirt'); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="max_images" id="max_images"
                                        value="<?php echo esc_attr($template_data['max_images']); ?>" min="1" max="20"
                                        class="small-text">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Template Images -->
                <div class="postbox">
                    <h2><?php esc_html_e('Template Images', 'zu-custom-tshirt'); ?></h2>
                    <div class="inside">
                        <div class="zu-ctsd-image-uploads">
                            <?php
                            $sides = [
                                'front' => __('Front View', 'zu-custom-tshirt'),
                                'back' => __('Back View', 'zu-custom-tshirt'),
                                'left_sleeve' => __('Left Sleeve', 'zu-custom-tshirt'),
                                'right_sleeve' => __('Right Sleeve', 'zu-custom-tshirt'),
                            ];
                            foreach ($sides as $side => $label):
                                $image_key = $side . '_image';
                                $image_url = $template_data[$image_key] ?? '';
                                ?>
                                <div class="zu-ctsd-image-upload">
                                    <label><?php echo esc_html($label); ?></label>
                                    <div class="image-preview-wrapper">
                                        <?php if ($image_url): ?>
                                            <img src="<?php echo esc_url($image_url); ?>" alt="" class="image-preview">
                                        <?php else: ?>
                                            <div class="image-placeholder">
                                                <span class="dashicons dashicons-format-image"></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <input type="hidden" name="<?php echo esc_attr($image_key); ?>"
                                        value="<?php echo esc_url($image_url); ?>" class="image-url">
                                    <button type="button" class="button upload-image-button">
                                        <?php esc_html_e('Upload Image', 'zu-custom-tshirt'); ?>
                                    </button>
                                    <button type="button" class="button remove-image-button"
                                        style="<?php echo $image_url ? '' : 'display:none;'; ?>">
                                        <?php esc_html_e('Remove', 'zu-custom-tshirt'); ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Printable Areas -->
                <div class="postbox">
                    <h2><?php esc_html_e('Printable Areas', 'zu-custom-tshirt'); ?></h2>
                    <div class="inside">
                        <p class="description">
                            <?php esc_html_e('Define the printable areas for each side (in pixels). These areas define where users can place their designs.', 'zu-custom-tshirt'); ?>
                        </p>

                        <?php foreach ($sides as $side => $label):
                            $area_key = 'printable_area_' . $side;

                            // Get raw value (could be array or JSON string from DB)
                            $raw_area = $template_data[$area_key] ?? [];

                            // Convert JSON string to array if needed
                            if (is_string($raw_area)) {
                                $area_data = json_decode($raw_area, true);
                                if (!is_array($area_data)) {
                                    $area_data = [];
                                }
                            } else {
                                $area_data = is_array($raw_area) ? $raw_area : [];
                            }

                            // Now safely merge with defaults
                            $area = $area_data + ['x' => 50, 'y' => 50, 'width' => 400, 'height' => 500];
                            ?>
                            <div class="printable-area-section">
                                <h4><?php echo esc_html($label); ?></h4>
                                <div class="printable-area-fields">
                                    <label>
                                        <?php esc_html_e('X:', 'zu-custom-tshirt'); ?>
                                        <input type="number" name="<?php echo esc_attr($area_key . '_x'); ?>"
                                            value="<?php echo esc_attr($area['x']); ?>" class="small-text">
                                    </label>
                                    <label>
                                        <?php esc_html_e('Y:', 'zu-custom-tshirt'); ?>
                                        <input type="number" name="<?php echo esc_attr($area_key . '_y'); ?>"
                                            value="<?php echo esc_attr($area['y']); ?>" class="small-text">
                                    </label>
                                    <label>
                                        <?php esc_html_e('Width:', 'zu-custom-tshirt'); ?>
                                        <input type="number" name="<?php echo esc_attr($area_key . '_width'); ?>"
                                            value="<?php echo esc_attr($area['width']); ?>" class="small-text">
                                    </label>
                                    <label>
                                        <?php esc_html_e('Height:', 'zu-custom-tshirt'); ?>
                                        <input type="number" name="<?php echo esc_attr($area_key . '_height'); ?>"
                                            value="<?php echo esc_attr($area['height']); ?>" class="small-text">
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="zu-ctsd-form-sidebar">
                <div class="postbox">
                    <h2><?php esc_html_e('Publish', 'zu-custom-tshirt'); ?></h2>
                    <div class="inside">
                        <div class="zu-ctsd-form-field">
                            <label>
                                <input type="checkbox" name="is_active" <?php checked($template_data['is_active'], 1); ?>>
                                <?php esc_html_e('Active', 'zu-custom-tshirt'); ?>
                            </label>
                        </div>
                        <div class="zu-ctsd-form-field">
                            <label>
                                <input type="checkbox" name="allow_text" <?php checked($template_data['allow_text'], 1); ?>>
                                <?php esc_html_e('Allow Text Editing', 'zu-custom-tshirt'); ?>
                            </label>
                        </div>
                        <div class="zu-ctsd-form-field">
                            <label>
                                <input type="checkbox" name="allow_images" <?php checked($template_data['allow_images'], 1); ?>>
                                <?php esc_html_e('Allow Image Uploads', 'zu-custom-tshirt'); ?>
                            </label>
                        </div>
                        <p class="submit">
                            <button type="submit" class="button button-primary button-large">
                                <?php echo $is_edit ? esc_html__('Update Template', 'zu-custom-tshirt') : esc_html__('Create Template', 'zu-custom-tshirt'); ?>
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>