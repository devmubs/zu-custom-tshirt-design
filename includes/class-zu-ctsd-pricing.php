<?php
/**
 * Pricing Engine Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_Pricing
 * Handles pricing calculations for custom designs
 */
class ZU_CTSD_Pricing {

    /**
     * Calculate price for a custom design
     */
    public function calculate_price(float $base_price, array $design_data): float {
        $total_price = $base_price;

        // Get pricing rules
        $pricing_rules = $this->get_applicable_rules($design_data);

        // Apply each rule
        foreach ($pricing_rules as $rule) {
            $total_price += floatval($rule->extra_cost);
            $total_price += floatval($rule->base_price);
        }

        return apply_filters('zu_ctsd_calculated_price', $total_price, $base_price, $design_data);
    }

    /**
     * Get applicable pricing rules for a design
     */
    public function get_applicable_rules(array $design_data): array {
        $rules = [];

        // Image count pricing
        $image_count = $this->get_image_count($design_data);
        $image_rule = ZU_CTSD_Database::get_pricing_rule('image_count', (string) $image_count);
        if ($image_rule) {
            $rules[] = $image_rule;
        }

        // Print size pricing
        $print_size = $this->get_print_size($design_data);
        $size_rule = ZU_CTSD_Database::get_pricing_rule('print_size', $print_size);
        if ($size_rule) {
            $rules[] = $size_rule;
        }

        // Print method pricing
        $print_method = sanitize_text_field($design_data['print_method'] ?? 'dtf');
        $method_rule = ZU_CTSD_Database::get_pricing_rule('print_method', $print_method);
        if ($method_rule) {
            $rules[] = $method_rule;
        }

        // Material pricing
        $material = sanitize_text_field($design_data['material'] ?? 'cotton');
        $material_rule = ZU_CTSD_Database::get_pricing_rule('material', $material);
        if ($material_rule) {
            $rules[] = $material_rule;
        }

        // Urgency pricing
        $urgency = sanitize_text_field($design_data['urgency'] ?? 'normal');
        $urgency_rule = ZU_CTSD_Database::get_pricing_rule('urgency', $urgency);
        if ($urgency_rule) {
            $rules[] = $urgency_rule;
        }

        return apply_filters('zu_ctsd_applicable_pricing_rules', $rules, $design_data);
    }

    /**
     * Get image count from design data
     */
    private function get_image_count(array $design_data): int {
        $elements = $design_data['elements'] ?? [];
        $image_count = 0;

        foreach ($elements as $element) {
            if (isset($element['type']) && $element['type'] === 'image') {
                $image_count++;
            }
        }

        return min($image_count, 5); // Cap at 5 for pricing tiers
    }

    /**
     * Get print size from design data
     */
    private function get_print_size(array $design_data): string {
        $elements = $design_data['elements'] ?? [];
        
        $max_width = 0;
        $max_height = 0;

        foreach ($elements as $element) {
            if (isset($element['width'])) {
                $max_width = max($max_width, floatval($element['width']));
            }
            if (isset($element['height'])) {
                $max_height = max($max_height, floatval($element['height']));
            }
        }

        $max_dimension = max($max_width, $max_height);

        // Convert pixels to cm (approximate)
        $size_cm = $max_dimension / 37.8; // 1cm ≈ 37.8px at 96 DPI

        if ($size_cm <= 10) {
            return 'small';
        } elseif ($size_cm <= 20) {
            return 'medium';
        } elseif ($size_cm <= 30) {
            return 'large';
        } else {
            return 'xlarge';
        }
    }

    /**
     * Get live price calculation for AJAX
     */
    public function get_live_price(float $base_price, array $design_data): array {
        $rules = $this->get_applicable_rules($design_data);
        $breakdown = [];
        $total_extra = 0;

        foreach ($rules as $rule) {
            $breakdown[] = [
                'label' => $rule->rule_value,
                'cost' => floatval($rule->extra_cost) + floatval($rule->base_price),
            ];
            $total_extra += floatval($rule->extra_cost) + floatval($rule->base_price);
        }

        return [
            'base_price' => $base_price,
            'extra_cost' => $total_extra,
            'total_price' => $base_price + $total_extra,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Get all pricing options for frontend
     */
    public static function get_pricing_options(): array {
        $options = [];
        
        $rule_types = ['print_method', 'material', 'urgency', 'print_size'];
        
        foreach ($rule_types as $rule_type) {
            $rules = ZU_CTSD_Database::get_pricing_rules($rule_type);
            $options[$rule_type] = array_map(function($rule) {
                return [
                    'key' => $rule->rule_key,
                    'label' => $rule->rule_value,
                    'extra_cost' => floatval($rule->extra_cost),
                ];
            }, $rules);
        }

        return $options;
    }

    /**
     * Format price for display
     */
    public static function format_price(float $price): string {
        return wc_price($price);
    }

    /**
     * Get price breakdown HTML
     */
    public function get_price_breakdown_html(float $base_price, array $design_data): string {
        $live_price = $this->get_live_price($base_price, $design_data);
        
        ob_start();
        ?>
        <div class="zu-ctsd-price-breakdown">
            <div class="price-row base">
                <span class="label"><?php esc_html_e('Base Price', 'zu-custom-tshirt'); ?></span>
                <span class="price"><?php echo wc_price($live_price['base_price']); ?></span>
            </div>
            
            <?php foreach ($live_price['breakdown'] as $item) : ?>
                <div class="price-row extra">
                    <span class="label"><?php echo esc_html($item['label']); ?></span>
                    <span class="price">+<?php echo wc_price($item['cost']); ?></span>
                </div>
            <?php endforeach; ?>
            
            <div class="price-row total">
                <span class="label"><?php esc_html_e('Total', 'zu-custom-tshirt'); ?></span>
                <span class="price"><?php echo wc_price($live_price['total_price']); ?></span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
