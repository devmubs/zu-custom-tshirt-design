<?php
/**
 * Templates List Template
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap zu-ctsd-admin">
    <h1>
        <?php echo esc_html(get_admin_page_title()); ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=zu-tshirt-templates&action=add')); ?>" class="page-title-action">
            <?php esc_html_e('Add New', 'zu-custom-tshirt'); ?>
        </a>
    </h1>

    <div class="zu-ctsd-templates-list">
        <?php if (!empty($templates)) : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th class="column-thumb"><?php esc_html_e('Preview', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Template Name', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Print Sides', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Max Images', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Features', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Status', 'zu-custom-tshirt'); ?></th>
                        <th><?php esc_html_e('Actions', 'zu-custom-tshirt'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $template) : 
                        $print_sides = [];
                        if ($template->front_image) $print_sides[] = __('Front', 'zu-custom-tshirt');
                        if ($template->back_image) $print_sides[] = __('Back', 'zu-custom-tshirt');
                        if ($template->left_sleeve_image) $print_sides[] = __('Left Sleeve', 'zu-custom-tshirt');
                        if ($template->right_sleeve_image) $print_sides[] = __('Right Sleeve', 'zu-custom-tshirt');
                    ?>
                        <tr>
                            <td class="column-thumb">
                                <?php if ($template->front_image) : ?>
                                    <img src="<?php echo esc_url($template->front_image); ?>" alt="" width="50" height="50" style="object-fit: contain;">
                                <?php else : ?>
                                    <span class="dashicons dashicons-format-image" style="font-size: 40px; color: #ccc;"></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo esc_html($template->template_name); ?></strong>
                                <code><?php echo esc_html($template->template_slug); ?></code>
                            </td>
                            <td><?php echo esc_html(implode(', ', $print_sides)); ?></td>
                            <td><?php echo esc_html($template->max_images); ?></td>
                            <td>
                                <?php if ($template->allow_text) : ?>
                                    <span class="zu-ctsd-feature-tag"><?php esc_html_e('Text', 'zu-custom-tshirt'); ?></span>
                                <?php endif; ?>
                                <?php if ($template->allow_images) : ?>
                                    <span class="zu-ctsd-feature-tag"><?php esc_html_e('Images', 'zu-custom-tshirt'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="zu-ctsd-status zu-ctsd-status--<?php echo $template->is_active ? 'active' : 'inactive'; ?>">
                                    <?php echo $template->is_active ? esc_html__('Active', 'zu-custom-tshirt') : esc_html__('Inactive', 'zu-custom-tshirt'); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=zu-tshirt-templates&action=edit&template_id=' . $template->id)); ?>" class="button button-small">
                                    <?php esc_html_e('Edit', 'zu-custom-tshirt'); ?>
                                </a>
                                <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=zu-tshirt-templates&action=delete&template_id=' . $template->id), 'delete_template_' . $template->id)); ?>" 
                                   class="button button-small button-link-delete" 
                                   onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this template?', 'zu-custom-tshirt'); ?>');">
                                    <?php esc_html_e('Delete', 'zu-custom-tshirt'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="zu-ctsd-empty-state">
                <span class="dashicons dashicons-art"></span>
                <h3><?php esc_html_e('No templates yet', 'zu-custom-tshirt'); ?></h3>
                <p><?php esc_html_e('Create your first T-shirt template to get started.', 'zu-custom-tshirt'); ?></p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=zu-tshirt-templates&action=add')); ?>" class="button button-primary">
                    <?php esc_html_e('Add Template', 'zu-custom-tshirt'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
