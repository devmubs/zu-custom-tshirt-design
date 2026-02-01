<?php
/**
 * Customizer Template
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="zu-ctsd-customizer" class="zu-ctsd-customizer">
    <!-- Customizer Header -->
    <div class="zu-ctsd-customizer-header">
        <h3><?php esc_html_e('Customize Your T-Shirt', 'zu-custom-tshirt'); ?></h3>
        <button type="button" class="zu-ctsd-btn zu-ctsd-btn--primary" id="zu-ctsd-start-designing">
            <?php esc_html_e('Start Designing', 'zu-custom-tshirt'); ?>
        </button>
    </div>

    <!-- Customizer Modal -->
    <div id="zu-ctsd-customizer-modal" class="zu-ctsd-modal" style="display: none;">
        <div class="zu-ctsd-modal-overlay"></div>
        <div class="zu-ctsd-modal-content">
            <!-- Modal Header -->
            <div class="zu-ctsd-modal-header">
                <h2><?php esc_html_e('T-Shirt Designer', 'zu-custom-tshirt'); ?></h2>
                <button type="button" class="zu-ctsd-modal-close">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="zu-ctsd-modal-body">
                <!-- Left Sidebar - Tools -->
                <div class="zu-ctsd-sidebar zu-ctsd-sidebar--left">
                    <!-- Print Side Selector -->
                    <div class="zu-ctsd-tool-section">
                        <h4><?php esc_html_e('Print Side', 'zu-custom-tshirt'); ?></h4>
                        <div class="zu-ctsd-print-sides">
                            <button type="button" class="zu-ctsd-side-btn active" data-side="front">
                                <span class="dashicons dashicons-admin-home"></span>
                                <?php esc_html_e('Front', 'zu-custom-tshirt'); ?>
                            </button>
                            <button type="button" class="zu-ctsd-side-btn" data-side="back">
                                <span class="dashicons dashicons-admin-home"></span>
                                <?php esc_html_e('Back', 'zu-custom-tshirt'); ?>
                            </button>
                            <button type="button" class="zu-ctsd-side-btn" data-side="left_sleeve">
                                <span class="dashicons dashicons-arrow-left-alt"></span>
                                <?php esc_html_e('Left Sleeve', 'zu-custom-tshirt'); ?>
                            </button>
                            <button type="button" class="zu-ctsd-side-btn" data-side="right_sleeve">
                                <span class="dashicons dashicons-arrow-right-alt"></span>
                                <?php esc_html_e('Right Sleeve', 'zu-custom-tshirt'); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Add Elements -->
                    <div class="zu-ctsd-tool-section">
                        <h4><?php esc_html_e('Add Elements', 'zu-custom-tshirt'); ?></h4>
                        <div class="zu-ctsd-add-elements">
                            <button type="button" class="zu-ctsd-tool-btn" id="zu-ctsd-add-text">
                                <span class="dashicons dashicons-editor-textcolor"></span>
                                <?php esc_html_e('Add Text', 'zu-custom-tshirt'); ?>
                            </button>
                            <button type="button" class="zu-ctsd-tool-btn" id="zu-ctsd-add-image">
                                <span class="dashicons dashicons-format-image"></span>
                                <?php esc_html_e('Upload Image', 'zu-custom-tshirt'); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Element Properties -->
                    <div class="zu-ctsd-tool-section" id="zu-ctsd-element-properties" style="display: none;">
                        <h4><?php esc_html_e('Properties', 'zu-custom-tshirt'); ?></h4>
                        
                        <!-- Text Properties -->
                        <div id="zu-ctsd-text-properties" style="display: none;">
                            <div class="zu-ctsd-form-group">
                                <label><?php esc_html_e('Font Family', 'zu-custom-tshirt'); ?></label>
                                <select id="zu-ctsd-font-family">
                                    <option value="Arial">Arial</option>
                                    <option value="Helvetica">Helvetica</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Verdana">Verdana</option>
                                    <option value="Georgia">Georgia</option>
                                </select>
                            </div>
                            <div class="zu-ctsd-form-group">
                                <label><?php esc_html_e('Font Size', 'zu-custom-tshirt'); ?></label>
                                <input type="range" id="zu-ctsd-font-size" min="12" max="100" value="24">
                            </div>
                            <div class="zu-ctsd-form-group">
                                <label><?php esc_html_e('Font Color', 'zu-custom-tshirt'); ?></label>
                                <input type="color" id="zu-ctsd-font-color" value="#000000">
                            </div>
                            <div class="zu-ctsd-form-group">
                                <label><?php esc_html_e('Text Align', 'zu-custom-tshirt'); ?></label>
                                <div class="zu-ctsd-text-align">
                                    <button type="button" data-align="left"><span class="dashicons dashicons-editor-alignleft"></span></button>
                                    <button type="button" data-align="center"><span class="dashicons dashicons-editor-aligncenter"></span></button>
                                    <button type="button" data-align="right"><span class="dashicons dashicons-editor-alignright"></span></button>
                                </div>
                            </div>
                        </div>

                        <!-- Common Properties -->
                        <div class="zu-ctsd-form-group">
                            <label><?php esc_html_e('Opacity', 'zu-custom-tshirt'); ?></label>
                            <input type="range" id="zu-ctsd-opacity" min="0" max="100" value="100">
                        </div>

                        <!-- Layer Controls -->
                        <div class="zu-ctsd-form-group">
                            <label><?php esc_html_e('Layer Order', 'zu-custom-tshirt'); ?></label>
                            <div class="zu-ctsd-layer-controls">
                                <button type="button" id="zu-ctsd-bring-front">
                                    <span class="dashicons dashicons-arrow-up-alt"></span>
                                    <?php esc_html_e('Bring Forward', 'zu-custom-tshirt'); ?>
                                </button>
                                <button type="button" id="zu-ctsd-send-back">
                                    <span class="dashicons dashicons-arrow-down-alt"></span>
                                    <?php esc_html_e('Send Backward', 'zu-custom-tshirt'); ?>
                                </button>
                            </div>
                        </div>

                        <button type="button" class="zu-ctsd-btn zu-ctsd-btn--danger" id="zu-ctsd-delete-element">
                            <span class="dashicons dashicons-trash"></span>
                            <?php esc_html_e('Delete', 'zu-custom-tshirt'); ?>
                        </button>
                    </div>
                </div>

                <!-- Canvas Area -->
                <div class="zu-ctsd-canvas-area">
                    <div class="zu-ctsd-canvas-wrapper">
                        <canvas id="zu-ctsd-canvas"></canvas>
                        <div class="zu-ctsd-canvas-loading" style="display: none;">
                            <span class="spinner"></span>
                        </div>
                    </div>
                    <div class="zu-ctsd-canvas-controls">
                        <button type="button" class="zu-ctsd-btn" id="zu-ctsd-zoom-in">
                            <span class="dashicons dashicons-plus"></span>
                        </button>
                        <button type="button" class="zu-ctsd-btn" id="zu-ctsd-zoom-out">
                            <span class="dashicons dashicons-minus"></span>
                        </button>
                        <button type="button" class="zu-ctsd-btn" id="zu-ctsd-reset-zoom">
                            <span class="dashicons dashicons-image-rotate"></span>
                        </button>
                    </div>
                </div>

                <!-- Right Sidebar - Options & Price -->
                <div class="zu-ctsd-sidebar zu-ctsd-sidebar--right">
                    <!-- Print Options -->
                    <div class="zu-ctsd-tool-section">
                        <h4><?php esc_html_e('Print Options', 'zu-custom-tshirt'); ?></h4>
                        
                        <div class="zu-ctsd-form-group">
                            <label><?php esc_html_e('Print Method', 'zu-custom-tshirt'); ?></label>
                            <select id="zu-ctsd-print-method">
                                <option value="dtf"><?php esc_html_e('DTF (Direct to Film)', 'zu-custom-tshirt'); ?></option>
                                <option value="screen"><?php esc_html_e('Screen Printing', 'zu-custom-tshirt'); ?></option>
                                <option value="digital"><?php esc_html_e('Digital Printing', 'zu-custom-tshirt'); ?></option>
                                <option value="vinyl"><?php esc_html_e('Vinyl Transfer', 'zu-custom-tshirt'); ?></option>
                                <option value="embroidery"><?php esc_html_e('Embroidery', 'zu-custom-tshirt'); ?></option>
                            </select>
                        </div>

                        <div class="zu-ctsd-form-group">
                            <label><?php esc_html_e('Material', 'zu-custom-tshirt'); ?></label>
                            <select id="zu-ctsd-material">
                                <option value="cotton"><?php esc_html_e('100% Cotton', 'zu-custom-tshirt'); ?></option>
                                <option value="polyester"><?php esc_html_e('Polyester', 'zu-custom-tshirt'); ?></option>
                                <option value="blend"><?php esc_html_e('Cotton-Polyester Blend', 'zu-custom-tshirt'); ?></option>
                            </select>
                        </div>

                        <div class="zu-ctsd-form-group">
                            <label><?php esc_html_e('Urgency', 'zu-custom-tshirt'); ?></label>
                            <select id="zu-ctsd-urgency">
                                <option value="normal"><?php esc_html_e('Normal (5-7 days)', 'zu-custom-tshirt'); ?></option>
                                <option value="express"><?php esc_html_e('Express (2-3 days)', 'zu-custom-tshirt'); ?></option>
                                <option value="rush"><?php esc_html_e('Rush (24 hours)', 'zu-custom-tshirt'); ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Live Price -->
                    <div class="zu-ctsd-tool-section">
                        <h4><?php esc_html_e('Price Breakdown', 'zu-custom-tshirt'); ?></h4>
                        <div id="zu-ctsd-price-breakdown">
                            <div class="zu-ctsd-price-row">
                                <span><?php esc_html_e('Base Price', 'zu-custom-tshirt'); ?></span>
                                <span id="zu-ctsd-base-price"><?php echo wc_price(0); ?></span>
                            </div>
                            <div id="zu-ctsd-extra-costs"></div>
                            <div class="zu-ctsd-price-row zu-ctsd-price-row--total">
                                <span><?php esc_html_e('Total', 'zu-custom-tshirt'); ?></span>
                                <span id="zu-ctsd-total-price"><?php echo wc_price(0); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="zu-ctsd-modal-footer">
                <div class="zu-ctsd-footer-left">
                    <button type="button" class="zu-ctsd-btn" id="zu-ctsd-save-design">
                        <span class="dashicons dashicons-cloud"></span>
                        <?php esc_html_e('Save for Later', 'zu-custom-tshirt'); ?>
                    </button>
                    <button type="button" class="zu-ctsd-btn" id="zu-ctsd-export-design">
                        <span class="dashicons dashicons-download"></span>
                        <?php esc_html_e('Export', 'zu-custom-tshirt'); ?>
                    </button>
                    <button type="button" class="zu-ctsd-btn" id="zu-ctsd-share-design">
                        <span class="dashicons dashicons-share"></span>
                        <?php esc_html_e('Share', 'zu-custom-tshirt'); ?>
                    </button>
                </div>
                <div class="zu-ctsd-footer-right">
                    <button type="button" class="zu-ctsd-btn" id="zu-ctsd-preview-design">
                        <span class="dashicons dashicons-visibility"></span>
                        <?php esc_html_e('Preview', 'zu-custom-tshirt'); ?>
                    </button>
                    <button type="button" class="zu-ctsd-btn zu-ctsd-btn--primary" id="zu-ctsd-add-to-cart">
                        <span class="dashicons dashicons-cart"></span>
                        <?php esc_html_e('Add to Cart', 'zu-custom-tshirt'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for adding to cart -->
    <form id="zu-ctsd-cart-form" method="post" style="display: none;">
        <?php wp_nonce_field('zu_ctsd_add_to_cart', 'zu_ctsd_cart_nonce'); ?>
        <input type="hidden" name="zu_ctsd_design_id" id="zu-ctsd-design-id" value="">
        <input type="hidden" name="zu_ctsd_print_method" id="zu-ctsd-form-print-method" value="dtf">
        <input type="hidden" name="zu_ctsd_urgency" id="zu-ctsd-form-urgency" value="normal">
        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
    </form>

    <!-- Text Input Modal -->
    <div id="zu-ctsd-text-modal" class="zu-ctsd-mini-modal" style="display: none;">
        <div class="zu-ctsd-mini-modal-content">
            <h4><?php esc_html_e('Add Text', 'zu-custom-tshirt'); ?></h4>
            <textarea id="zu-ctsd-text-input" rows="3" placeholder="<?php esc_attr_e('Enter your text here...', 'zu-custom-tshirt'); ?>"></textarea>
            <div class="zu-ctsd-mini-modal-actions">
                <button type="button" class="zu-ctsd-btn" id="zu-ctsd-text-cancel"><?php esc_html_e('Cancel', 'zu-custom-tshirt'); ?></button>
                <button type="button" class="zu-ctsd-btn zu-ctsd-btn--primary" id="zu-ctsd-text-add"><?php esc_html_e('Add', 'zu-custom-tshirt'); ?></button>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="zu-ctsd-preview-modal" class="zu-ctsd-modal" style="display: none;">
        <div class="zu-ctsd-modal-overlay"></div>
        <div class="zu-ctsd-modal-content zu-ctsd-modal-content--preview">
            <div class="zu-ctsd-modal-header">
                <h2><?php esc_html_e('Design Preview', 'zu-custom-tshirt'); ?></h2>
                <button type="button" class="zu-ctsd-modal-close">&times;</button>
            </div>
            <div class="zu-ctsd-modal-body">
                <img id="zu-ctsd-preview-image" src="" alt="<?php esc_attr_e('Design Preview', 'zu-custom-tshirt'); ?>">
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div id="zu-ctsd-share-modal" class="zu-ctsd-mini-modal" style="display: none;">
        <div class="zu-ctsd-mini-modal-content">
            <h4><?php esc_html_e('Share Your Design', 'zu-custom-tshirt'); ?></h4>
            <input type="text" id="zu-ctsd-share-url" readonly>
            <div class="zu-ctsd-mini-modal-actions">
                <button type="button" class="zu-ctsd-btn" id="zu-ctsd-share-copy">
                    <span class="dashicons dashicons-clipboard"></span>
                    <?php esc_html_e('Copy Link', 'zu-custom-tshirt'); ?>
                </button>
                <button type="button" class="zu-ctsd-btn" id="zu-ctsd-share-close"><?php esc_html_e('Close', 'zu-custom-tshirt'); ?></button>
            </div>
        </div>
    </div>
</div>
