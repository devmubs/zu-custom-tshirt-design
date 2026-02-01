<?php
/**
 * Plugin Deactivator Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_Deactivator
 * Fired during plugin deactivation
 */
class ZU_CTSD_Deactivator {

    /**
     * Deactivate plugin
     */
    public static function deactivate(): void {
        self::clear_scheduled_events();
        self::flush_rewrite_rules();
        self::set_deactivation_flag();
    }

    /**
     * Clear scheduled events
     */
    private static function clear_scheduled_events(): void {
        wp_clear_scheduled_hook('zu_ctsd_daily_cleanup');
        wp_clear_scheduled_hook('zu_ctsd_process_pending_designs');
    }

    /**
     * Flush rewrite rules
     */
    private static function flush_rewrite_rules(): void {
        flush_rewrite_rules();
    }

    /**
     * Set deactivation flag
     */
    private static function set_deactivation_flag(): void {
        set_transient('zu_ctsd_deactivated', true, 30);
    }
}
