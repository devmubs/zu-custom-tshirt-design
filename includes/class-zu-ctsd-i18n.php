<?php
/**
 * Internationalization Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_i18n
 * Handles internationalization
 */
class ZU_CTSD_i18n {

    /**
     * Load plugin text domain
     */
    public function load_plugin_textdomain(): void {
        load_plugin_textdomain(
            'zu-custom-tshirt',
            false,
            dirname(ZU_CTSD_PLUGIN_BASENAME) . '/languages/'
        );
    }
}
